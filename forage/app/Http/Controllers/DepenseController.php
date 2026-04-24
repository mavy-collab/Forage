<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use App\Models\JournalActivite;
use Illuminate\Http\Request;

class DepenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Depense::with('user')->latest('date_depense');

        if ($request->filled('date_debut')) {
            $query->whereDate('date_depense', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_depense', '<=', $request->date_fin);
        }
        if ($request->filled('search')) {
            $query->where('motif', 'like', '%' . $request->search . '%');
        }

        $depenses = $query->paginate(20)->withQueryString();

        $stats = [
            'aujourd_hui' => Depense::whereDate('date_depense', today())->sum('montant'),
            'ce_mois'     => Depense::mensuel()->sum('montant'),
            'cette_annee' => Depense::annuel()->sum('montant'),
        ];

        return view('depenses.index', compact('depenses', 'stats'));
    }

    public function create()
    {
        return view('depenses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'montant'      => ['required', 'numeric', 'min:1'],
            'motif'        => ['required', 'string', 'max:200'],
            'date_depense' => ['required', 'date'],
        ], [
            'montant.required'      => 'Le montant est obligatoire.',
            'motif.required'        => 'Le motif est obligatoire.',
            'date_depense.required' => 'La date est obligatoire.',
        ]);

        $data['user_id'] = auth()->id();

        $depense = Depense::create($data);

        JournalActivite::enregistrer('depense', "Dépense enregistrée : {$depense->motif} — {$depense->montant} FCFA", [
            'depense_id' => $depense->id,
            'montant'    => $depense->montant,
        ]);

        return redirect()->route('depenses.index')
            ->with('success', 'Dépense enregistrée avec succès.');
    }

    public function destroy(Depense $depense)
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Suppression réservée à l\'administrateur.');

        JournalActivite::enregistrer('depense', "Dépense supprimée : {$depense->motif} — {$depense->montant} FCFA");
        $depense->delete();

        return redirect()->route('depenses.index')
            ->with('success', 'Dépense supprimée.');
    }
}
