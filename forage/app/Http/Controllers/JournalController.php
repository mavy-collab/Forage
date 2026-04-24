<?php

namespace App\Http\Controllers;

use App\Models\JournalActivite;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $query = JournalActivite::with('user')->latest('created_at');

        if ($request->filled('type')) {
            $query->where('type_action', $request->type);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $activites = $query->paginate(30)->withQueryString();

        $stats = [
            'aujourd_hui' => JournalActivite::whereDate('created_at', today())->count(),
            'ce_mois'     => JournalActivite::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)->count(),
            'total'       => JournalActivite::count(),
        ];

        $utilisateurs = \App\Models\User::orderBy('name')->get();

        $types = [
            'vente'     => ['label' => 'Vente',      'icon' => 'fa-faucet',          'color' => '#1a3a5c', 'bg' => '#e3f2fd'],
            'paiement'  => ['label' => 'Paiement',   'icon' => 'fa-hand-holding-usd','color' => '#2e7d32', 'bg' => '#e8f5e9'],
            'credit'    => ['label' => 'Crédit',      'icon' => 'fa-credit-card',     'color' => '#c62828', 'bg' => '#ffebee'],
            'depense'   => ['label' => 'Dépense',    'icon' => 'fa-wallet',           'color' => '#e65100', 'bg' => '#fff3e0'],
            'client'    => ['label' => 'Client',     'icon' => 'fa-user',             'color' => '#6a1b9a', 'bg' => '#f3e5f5'],
            'connexion' => ['label' => 'Connexion',  'icon' => 'fa-sign-in-alt',      'color' => '#0277bd', 'bg' => '#e1f5fe'],
            'autre'     => ['label' => 'Autre',      'icon' => 'fa-info-circle',      'color' => '#555',    'bg' => '#f5f5f5'],
        ];

        return view('journal.index', compact('activites', 'stats', 'utilisateurs', 'types'));
    }
}
