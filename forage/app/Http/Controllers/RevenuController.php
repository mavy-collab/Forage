<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use App\Models\Vente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenuController extends Controller
{
    public function index(Request $request)
    {
        $annee = $request->input('annee', now()->year);
        $mois  = $request->input('mois', now()->month);

        // Revenus journaliers du mois sélectionné
        $revenusJournaliers = Vente::where('statut_paiement', 'paye')
            ->whereMonth('date_vente', $mois)
            ->whereYear('date_vente', $annee)
            ->select(
                DB::raw('DATE(date_vente) as jour'),
                DB::raw('SUM(montant) as total'),
                DB::raw('SUM(CASE WHEN type_vente="forage" THEN montant ELSE 0 END) as forage'),
                DB::raw('SUM(CASE WHEN type_vente="livraison" THEN montant ELSE 0 END) as livraison'),
                DB::raw('SUM(CASE WHEN type_vente="exterieure" THEN montant ELSE 0 END) as exterieure')
            )
            ->groupBy('jour')
            ->orderBy('jour')
            ->get();

        // Revenus mensuels de l'année sélectionnée
        $revenusMensuels = Vente::where('statut_paiement', 'paye')
            ->whereYear('date_vente', $annee)
            ->select(
                DB::raw('MONTH(date_vente) as mois_num'),
                DB::raw('SUM(montant) as total'),
                DB::raw('SUM(CASE WHEN type_vente="forage" THEN montant ELSE 0 END) as forage'),
                DB::raw('SUM(CASE WHEN type_vente="livraison" THEN montant ELSE 0 END) as livraison'),
                DB::raw('SUM(CASE WHEN type_vente="exterieure" THEN montant ELSE 0 END) as exterieure')
            )
            ->groupBy('mois_num')
            ->orderBy('mois_num')
            ->get()
            ->keyBy('mois_num');

        // Totaux de la période sélectionnée
        $totauxMois = [
            'forage'     => Vente::forage()->where('statut_paiement', 'paye')->mensuel($mois, $annee)->sum('montant'),
            'livraison'  => Vente::livraison()->where('statut_paiement', 'paye')->mensuel($mois, $annee)->sum('montant'),
            'exterieure' => Vente::exterieure()->where('statut_paiement', 'paye')->mensuel($mois, $annee)->sum('montant'),
            'total'      => Vente::where('statut_paiement', 'paye')->mensuel($mois, $annee)->sum('montant'),
            'depenses'   => Depense::mensuel($mois, $annee)->sum('montant'),
        ];
        $totauxMois['net'] = $totauxMois['total'] - $totauxMois['depenses'];

        // Totaux annuels
        $totauxAnnee = [
            'total'    => Vente::where('statut_paiement', 'paye')->annuel($annee)->sum('montant'),
            'depenses' => Depense::annuel($annee)->sum('montant'),
        ];
        $totauxAnnee['net'] = $totauxAnnee['total'] - $totauxAnnee['depenses'];

        // Comparaison mois précédent
        $moisPrec = $mois == 1 ? 12 : $mois - 1;
        $anneePrec = $mois == 1 ? $annee - 1 : $annee;
        $totalMoisPrec = Vente::where('statut_paiement', 'paye')->mensuel($moisPrec, $anneePrec)->sum('montant');
        $evolution = $totalMoisPrec > 0
            ? round(($totauxMois['total'] - $totalMoisPrec) / $totalMoisPrec * 100, 1)
            : null;

        $annees = Vente::selectRaw('YEAR(date_vente) as annee')
            ->groupBy('annee')->orderByDesc('annee')->pluck('annee');

        $moisNoms = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                     'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

        return view('revenus.index', compact(
            'revenusJournaliers', 'revenusMensuels', 'totauxMois', 'totauxAnnee',
            'annee', 'mois', 'evolution', 'totalMoisPrec', 'annees', 'moisNoms'
        ));
    }
}
