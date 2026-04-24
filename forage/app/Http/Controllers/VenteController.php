<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\JournalActivite;
use App\Models\Vente;
use Illuminate\Http\Request;

class VenteController extends Controller
{
    public function index(Request $request)
    {
        $query = Vente::with(['client', 'user'])->latest('date_vente');

        if ($request->filled('type')) {
            $query->where('type_vente', $request->type);
        }
        if ($request->filled('statut')) {
            $query->where('statut_paiement', $request->statut);
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('date_vente', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_vente', '<=', $request->date_fin);
        }

        $ventes = $query->paginate(20)->withQueryString();

        $typeActif = $request->input('type');

        if ($typeActif) {
            // Stats spécifiques à la section active
            $statsSection = [
                'total_paye'   => Vente::where('type_vente', $typeActif)->where('statut_paiement', 'paye')->sum('montant'),
                'total_credit' => Vente::where('type_vente', $typeActif)->where('statut_paiement', 'non_paye')->sum('montant'),
                'nb_ventes'    => Vente::where('type_vente', $typeActif)->count(),
                'nb_credits'   => Vente::where('type_vente', $typeActif)->where('statut_paiement', 'non_paye')->count(),
            ];
            $totaux = null;
        } else {
            $statsSection = null;
            $totaux = [
                'forage'     => Vente::forage()->where('statut_paiement', 'paye')->sum('montant'),
                'livraison'  => Vente::livraison()->where('statut_paiement', 'paye')->sum('montant'),
                'exterieure' => Vente::exterieure()->where('statut_paiement', 'paye')->sum('montant'),
            ];
        }

        return view('ventes.index', compact('ventes', 'totaux', 'statsSection', 'typeActif'));
    }

    public function create()
    {
        $clients = Client::orderBy('nom')->get();
        $forageExisteAujourdhui = Vente::forage()->journalier()->exists();
        return view('ventes.create', compact('clients', 'forageExisteAujourdhui'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type_vente'       => ['required', 'in:forage,livraison,exterieure'],
            'client_id'        => ['nullable', 'exists:clients,id'],
            'montant'          => ['required', 'numeric', 'min:0'],
            'statut_paiement'  => ['required', 'in:paye,non_paye'],
            'date_vente'       => ['required', 'date'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ], [
            'type_vente.required' => 'Le type de vente est obligatoire.',
            'montant.required'    => 'Le montant est obligatoire.',
            'date_vente.required' => 'La date est obligatoire.',
        ]);

        $data['user_id'] = auth()->id();

        // Forage = toujours payé sur place, une seule entrée par jour
        if ($data['type_vente'] === 'forage') {
            $data['statut_paiement'] = 'paye';
            $data['client_id'] = null;

            $dejaExiste = Vente::forage()->whereDate('date_vente', $data['date_vente'])->exists();
            if ($dejaExiste) {
                return back()->withInput()->withErrors([
                    'date_vente' => 'Une vente forage existe déjà pour le ' . \Carbon\Carbon::parse($data['date_vente'])->translatedFormat('d F Y') . '.',
                ]);
            }
        }

        $vente = Vente::create($data);

        JournalActivite::enregistrer('vente', "Nouvelle vente {$vente->type_vente} — {$vente->montant} FCFA", [
            'vente_id' => $vente->id,
            'type'     => $vente->type_vente,
            'montant'  => $vente->montant,
        ]);

        return redirect()->route('ventes.index')
            ->with('success', 'Vente enregistrée avec succès.');
    }

    public function show(Vente $vente)
    {
        $vente->load(['client', 'user', 'paiements.user']);
        return view('ventes.show', compact('vente'));
    }

    public function destroy(Vente $vente)
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Suppression réservée à l\'administrateur.');

        JournalActivite::enregistrer('vente', "Suppression vente #{$vente->id} — {$vente->montant} FCFA", [
            'vente_id' => $vente->id,
        ]);

        $vente->delete();

        return redirect()->route('ventes.index')
            ->with('success', 'Vente supprimée.');
    }
}
