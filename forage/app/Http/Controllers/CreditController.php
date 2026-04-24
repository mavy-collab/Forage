<?php

namespace App\Http\Controllers;

use App\Models\JournalActivite;
use App\Models\Paiement;
use App\Models\Vente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditController extends Controller
{
    public function index(Request $request)
    {
        $query = Vente::credits()
            ->with(['client', 'user', 'paiements'])
            ->latest('date_vente');

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('type')) {
            $query->where('type_vente', $request->type);
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('date_vente', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_vente', '<=', $request->date_fin);
        }

        $credits = $query->paginate(20)->withQueryString();

        $stats = [
            'total_credits'  => Vente::credits()->count(),
            'total_montant'  => Vente::credits()->sum('montant'),
            'clients_endettes' => Vente::credits()->distinct('client_id')->count('client_id'),
        ];

        $clients = \App\Models\Client::whereHas('ventesNonPayees')->orderBy('nom')->get();

        return view('credits.index', compact('credits', 'stats', 'clients'));
    }

    public function show(Vente $credit)
    {
        abort_if($credit->statut_paiement === 'paye', 404);
        $credit->load(['client', 'user', 'paiements.user']);
        $montantDejaPaye = $credit->paiements->sum('montant');
        $resteAPayer = $credit->montant - $montantDejaPaye;

        return view('credits.show', compact('credit', 'montantDejaPaye', 'resteAPayer'));
    }

    public function payer(Request $request, Vente $credit)
    {
        abort_if($credit->statut_paiement === 'paye', 403);

        $montantDejaPaye = $credit->paiements()->sum('montant');
        $resteAPayer = $credit->montant - $montantDejaPaye;

        $data = $request->validate([
            'montant'       => ['required', 'numeric', 'min:1', 'max:' . $resteAPayer],
            'date_paiement' => ['required', 'date'],
            'notes'         => ['nullable', 'string', 'max:300'],
        ], [
            'montant.required'       => 'Le montant est obligatoire.',
            'montant.max'            => "Le montant ne peut pas dépasser le reste à payer ({$resteAPayer} FCFA).",
            'date_paiement.required' => 'La date est obligatoire.',
        ]);

        DB::transaction(function () use ($data, $credit, $resteAPayer) {
            Paiement::create([
                'vente_id'      => $credit->id,
                'montant'       => $data['montant'],
                'date_paiement' => $data['date_paiement'],
                'notes'         => $data['notes'] ?? null,
                'user_id'       => auth()->id(),
            ]);

            // Si le paiement solde la dette, marquer comme payé
            if ((float) $data['montant'] >= (float) $resteAPayer) {
                $credit->update(['statut_paiement' => 'paye']);
            }

            JournalActivite::enregistrer('paiement',
                "Paiement de {$data['montant']} FCFA sur crédit #{$credit->id} (client : {$credit->client?->nom})",
                ['vente_id' => $credit->id, 'montant' => $data['montant']]
            );
        });

        $msg = (float) $data['montant'] >= (float) $resteAPayer
            ? 'Crédit soldé ! La vente est maintenant marquée comme payée.'
            : 'Paiement partiel enregistré.';

        return redirect()->route('credits.index')->with('success', $msg);
    }
}
