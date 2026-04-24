<?php

namespace App\Http\Controllers;

use App\Models\ArdoiseForage;
use App\Models\Depense;
use App\Models\Vente;

class BilanController extends Controller
{
    public function index()
    {
        $annee = now()->year;
        $mois  = now()->month;

        $ventesTotales = [
            'forage'     => Vente::forage()->where('statut_paiement', 'paye')->sum('montant'),
            'livraison'  => Vente::livraison()->where('statut_paiement', 'paye')->sum('montant'),
            'exterieure' => Vente::exterieure()->where('statut_paiement', 'paye')->sum('montant'),
        ];
        $totalVentes   = array_sum($ventesTotales);
        $totalDepenses = Depense::sum('montant');
        $bilanNet      = $totalVentes - $totalDepenses;
        $totalCredits      = Vente::credits()->sum('montant');
        $nbCredits         = Vente::credits()->count();
        $ardoiseEnCours    = ArdoiseForage::enCours()->sum('montant');
        $ardoiseRemboursee = ArdoiseForage::remboursees()->sum('montant');
        $nbArdoiseEnCours  = ArdoiseForage::enCours()->count();

        $ventesCeMois   = Vente::mensuel($mois, $annee)->where('statut_paiement', 'paye')->sum('montant');
        $depensesCeMois = Depense::mensuel($mois, $annee)->sum('montant');

        $parMois = collect(range(1, 12))->map(function ($m) use ($annee) {
            return [
                'label'    => \Carbon\Carbon::create($annee, $m, 1)->translatedFormat('M'),
                'ventes'   => Vente::mensuel($m, $annee)->where('statut_paiement', 'paye')->sum('montant'),
                'depenses' => Depense::mensuel($m, $annee)->sum('montant'),
            ];
        });

        return view('bilan.index', compact(
            'ventesTotales', 'totalVentes', 'totalDepenses', 'bilanNet',
            'totalCredits', 'nbCredits', 'ventesCeMois', 'depensesCeMois',
            'ardoiseEnCours', 'ardoiseRemboursee', 'nbArdoiseEnCours',
            'parMois', 'annee'
        ));
    }
}
