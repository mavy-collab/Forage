<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\JournalActivite;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::withCount('ventes')
            ->withSum(['ventes as total_achats' => fn($q) => $q->where('statut_paiement', 'paye')], 'montant')
            ->withSum(['ventes as total_dette' => fn($q) => $q->where('statut_paiement', 'non_paye')], 'montant');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('contact', 'like', '%' . $request->search . '%')
                  ->orWhere('adresse', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('filtre') && $request->filtre === 'avec_dette') {
            $query->whereHas('ventesNonPayees');
        }

        $clients = $query->orderBy('nom')->paginate(20)->withQueryString();

        $stats = [
            'total'      => Client::count(),
            'avec_dette' => Client::whereHas('ventesNonPayees')->count(),
            'total_dette' => \App\Models\Vente::credits()->sum('montant'),
        ];

        return view('clients.index', compact('clients', 'stats'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'     => ['required', 'string', 'max:100'],
            'adresse' => ['nullable', 'string', 'max:200'],
            'contact' => ['nullable', 'string', 'max:50'],
        ], [
            'nom.required' => 'Le nom du client est obligatoire.',
        ]);

        $client = Client::create($data);

        JournalActivite::enregistrer('client', "Nouveau client ajouté : {$client->nom}", ['client_id' => $client->id]);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Client ajouté avec succès.');
    }

    public function show(Client $client)
    {
        $ventes = $client->ventes()->with('user')->latest('date_vente')->paginate(15);

        $stats = [
            'total_ventes'   => $client->ventes()->count(),
            'total_paye'     => $client->ventes()->where('statut_paiement', 'paye')->sum('montant'),
            'total_dette'    => $client->ventes()->where('statut_paiement', 'non_paye')->sum('montant'),
            'derniere_vente' => $client->ventes()->latest('date_vente')->value('date_vente'),
        ];

        return view('clients.show', compact('client', 'ventes', 'stats'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'nom'     => ['required', 'string', 'max:100'],
            'adresse' => ['nullable', 'string', 'max:200'],
            'contact' => ['nullable', 'string', 'max:50'],
        ]);

        $client->update($data);

        JournalActivite::enregistrer('client', "Client modifié : {$client->nom}", ['client_id' => $client->id]);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Client modifié avec succès.');
    }

    public function destroy(Client $client)
    {
        $nom = $client->nom;
        $client->delete();

        JournalActivite::enregistrer('client', "Client supprimé : {$nom}");

        return redirect()->route('clients.index')
            ->with('success', "Client « {$nom} » supprimé.");
    }
}
