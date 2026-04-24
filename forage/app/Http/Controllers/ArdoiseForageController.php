<?php

namespace App\Http\Controllers;

use App\Models\ArdoiseForage;
use App\Models\JournalActivite;
use Illuminate\Http\Request;

class ArdoiseForageController extends Controller
{
    public function index(Request $request)
    {
        $filtre = in_array($request->input('filtre'), ['en_cours', 'remboursees', 'toutes'])
            ? $request->input('filtre')
            : 'en_cours';

        $query = ArdoiseForage::with('user')->orderBy('date_ardoise', 'desc');

        if ($filtre === 'en_cours') {
            $query->enCours();
        } elseif ($filtre === 'remboursees') {
            $query->remboursees();
        }
        // 'toutes' : pas de filtre supplémentaire

        $ardoises = $query->paginate(20)->withQueryString();

        $stats = [
            'total_en_cours'    => ArdoiseForage::enCours()->sum('montant'),
            'nb_en_cours'       => ArdoiseForage::enCours()->count(),
            'total_rembourse'   => ArdoiseForage::remboursees()->sum('montant'),
            'nb_rembourse'      => ArdoiseForage::remboursees()->count(),
        ];

        return view('ardoise.index', compact('ardoises', 'stats', 'filtre'));
    }

    public function create()
    {
        return view('ardoise.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'          => ['required', 'string', 'max:150'],
            'montant'      => ['required', 'numeric', 'min:1'],
            'date_ardoise' => ['required', 'date'],
            'notes'        => ['nullable', 'string', 'max:300'],
        ], [
            'nom.required'          => 'Le nom est obligatoire.',
            'montant.required'      => 'Le montant est obligatoire.',
            'date_ardoise.required' => 'La date est obligatoire.',
        ]);

        $data['user_id'] = auth()->id();

        $ardoise = ArdoiseForage::create($data);

        JournalActivite::enregistrer('ardoise', "Ardoise forage — {$ardoise->nom} : {$ardoise->montant} FCFA", [
            'ardoise_id' => $ardoise->id,
            'montant'    => $ardoise->montant,
        ]);

        return redirect()->route('ardoise.index')
            ->with('success', "Ardoise enregistrée pour {$ardoise->nom}.");
    }

    public function rembourser(ArdoiseForage $ardoise)
    {
        if ($ardoise->rembourse) {
            return back()->with('error', 'Cette ardoise est déjà remboursée.');
        }

        $ardoise->update([
            'rembourse'          => true,
            'date_remboursement' => today(),
        ]);

        JournalActivite::enregistrer('ardoise', "Remboursement ardoise — {$ardoise->nom} : {$ardoise->montant} FCFA", [
            'ardoise_id' => $ardoise->id,
        ]);

        return back()->with('success', "Ardoise de {$ardoise->nom} marquée comme remboursée.");
    }

    public function destroy(ArdoiseForage $ardoise)
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Suppression réservée à l\'administrateur.');

        JournalActivite::enregistrer('ardoise', "Suppression ardoise — {$ardoise->nom} : {$ardoise->montant} FCFA", [
            'ardoise_id' => $ardoise->id,
        ]);

        $ardoise->delete();

        return back()->with('success', 'Ardoise supprimée.');
    }
}
