<?php

namespace App\Http\Controllers;

use App\Models\ArdoiseForage;
use App\Models\Depense;
use App\Models\Vente;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();
        $mois  = now()->month;
        $annee = now()->year;

        // Données communes
        $nbVentesJour  = Vente::journalier($today)->count();
        $nbCredits     = Vente::credits()->count();
        $ventesRecentes = Vente::with(['client', 'user'])->latest('date_vente')->limit(5)->get();

        if (auth()->user()->isAdmin()) {
            // Ventes encaissées (terme unifié, plus "revenus")
            $ventesJour   = Vente::journalier($today)->where('statut_paiement', 'paye')->sum('montant');
            $ventesMois   = Vente::mensuel($mois, $annee)->where('statut_paiement', 'paye')->sum('montant');
            $ventesAnnee  = Vente::annuel($annee)->where('statut_paiement', 'paye')->sum('montant');

            // Dépenses
            $depensesMois = Depense::mensuel($mois, $annee)->sum('montant');

            // Net ce mois (cohérent avec le bilan)
            $netMois = $ventesMois - $depensesMois;

            // Dettes : crédits + ardoises (vision complète)
            $totalCredits       = Vente::credits()->sum('montant');
            $totalArdoises      = ArdoiseForage::enCours()->sum('montant');
            $nbArdoisesEnCours  = ArdoiseForage::enCours()->count();

            $ventesParType = Vente::mensuel($mois, $annee)
                ->where('statut_paiement', 'paye')
                ->select('type_vente', DB::raw('SUM(montant) as total'))
                ->groupBy('type_vente')
                ->pluck('total', 'type_vente');

            $evolution = collect(range(5, 0))->map(function ($i) {
                $date = now()->subMonths($i);
                return [
                    'mois'     => $date->translatedFormat('M Y'),
                    'ventes'   => Vente::mensuel($date->month, $date->year)->where('statut_paiement', 'paye')->sum('montant'),
                    'depenses' => Depense::mensuel($date->month, $date->year)->sum('montant'),
                ];
            });

            return view('dashboard', compact(
                'ventesJour', 'ventesMois', 'ventesAnnee',
                'depensesMois', 'netMois',
                'totalCredits', 'totalArdoises', 'nbArdoisesEnCours',
                'ventesParType', 'evolution',
                'nbVentesJour', 'nbCredits', 'ventesRecentes'
            ));
        }

        // Vue employé (sans totaux financiers)
        return view('dashboard_employe', compact('nbVentesJour', 'nbCredits', 'ventesRecentes'));
    }
}
