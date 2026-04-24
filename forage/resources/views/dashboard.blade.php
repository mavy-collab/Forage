@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@push('styles')
<style>
    .period-title {
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1px; color: #aaa; margin: 0 0 12px;
        padding-bottom: 8px; border-bottom: 1px solid #eee;
    }
    .stats-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px; margin-bottom: 8px;
    }
    .stat-card {
        background: #fff; border-radius: 12px; padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        display: flex; align-items: center; gap: 14px;
    }
    .stat-icon {
        width: 48px; height: 48px; border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; flex-shrink: 0;
    }
    .stat-icon.blue    { background: #e3f2fd; color: #1a3a5c; }
    .stat-icon.green   { background: #e8f5e9; color: #2e7d32; }
    .stat-icon.orange  { background: #fff3e0; color: #e65100; }
    .stat-icon.red     { background: #ffebee; color: #c62828; }
    .stat-icon.purple  { background: #f3e5f5; color: #6a1b9a; }
    .stat-icon.teal    { background: #e0f2f1; color: #00695c; }
    .stat-icon.net-pos { background: #e8f5e9; color: #1b5e20; }
    .stat-icon.net-neg { background: #ffebee; color: #b71c1c; }
    .stat-info p  { font-size: 0.78rem; color: #888; margin-bottom: 3px; }
    .stat-info h2 { font-size: 1.35rem; font-weight: 700; color: #1a3a5c; }
    .stat-info h2.pos { color: #2e7d32; }
    .stat-info h2.neg { color: #c62828; }
    .stat-info .scope { font-size: 0.7rem; color: #bbb; margin-top: 2px; }

    .section-block { margin-bottom: 28px; }

    .charts-grid {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 20px; margin-bottom: 28px;
    }
    .chart-card {
        background: #fff; border-radius: 12px; padding: 22px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .chart-card h3 { font-size: 0.9rem; color: #1a3a5c; margin-bottom: 16px; font-weight: 600; }

    @media(max-width: 900px) { .charts-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

{{-- BLOC 1 : Aujourd'hui --}}
<div class="section-block">
    <p class="period-title"><i class="fas fa-calendar-day"></i> Aujourd'hui — {{ now()->translatedFormat('d F Y') }}</p>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-faucet"></i></div>
            <div class="stat-info">
                <p>Ventes encaissées aujourd'hui</p>
                <h2>{{ number_format($ventesJour, 0, ',', ' ') }} FCFA</h2>
                <div class="scope">{{ $nbVentesJour }} vente(s) enregistrée(s)</div>
            </div>
        </div>
    </div>
</div>

{{-- BLOC 2 : Ce mois --}}
<div class="section-block">
    <p class="period-title"><i class="fas fa-calendar-alt"></i> Ce mois — {{ now()->translatedFormat('F Y') }}</p>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-info">
                <p>Ventes encaissées</p>
                <h2>{{ number_format($ventesMois, 0, ',', ' ') }} FCFA</h2>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-arrow-down"></i></div>
            <div class="stat-info">
                <p>Dépenses</p>
                <h2>{{ number_format($depensesMois, 0, ',', ' ') }} FCFA</h2>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon {{ $netMois >= 0 ? 'net-pos' : 'net-neg' }}">
                <i class="fas fa-balance-scale"></i>
            </div>
            <div class="stat-info">
                <p>Net ce mois (ventes − dépenses)</p>
                <h2 class="{{ $netMois >= 0 ? 'pos' : 'neg' }}">
                    {{ $netMois >= 0 ? '+' : '' }}{{ number_format($netMois, 0, ',', ' ') }} FCFA
                </h2>
            </div>
        </div>
    </div>
</div>

{{-- BLOC 3 : Cette année --}}
<div class="section-block">
    <p class="period-title"><i class="fas fa-chart-line"></i> Cette année — {{ now()->year }}</p>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-coins"></i></div>
            <div class="stat-info">
                <p>Ventes encaissées</p>
                <h2>{{ number_format($ventesAnnee, 0, ',', ' ') }} FCFA</h2>
            </div>
        </div>
    </div>
</div>

{{-- BLOC 4 : Dettes en cours --}}
<div class="section-block">
    <p class="period-title"><i class="fas fa-clock"></i> Dettes en cours (toutes périodes)</p>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-credit-card"></i></div>
            <div class="stat-info">
                <p>Crédits impayés livraison/ext. ({{ $nbCredits }})</p>
                <h2>{{ number_format($totalCredits, 0, ',', ' ') }} FCFA</h2>
                <div class="scope">Ventes en crédit non soldées</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon teal"><i class="fas fa-pencil-alt"></i></div>
            <div class="stat-info">
                <p>Ardoises forage en cours ({{ $nbArdoisesEnCours }})</p>
                <h2>{{ number_format($totalArdoises, 0, ',', ' ') }} FCFA</h2>
                <div class="scope">Petites dettes au forage</div>
            </div>
        </div>
    </div>
</div>

{{-- Graphiques --}}
<div class="charts-grid">
    <div class="chart-card">
        <h3><i class="fas fa-chart-bar"></i> Ventes encaissées vs Dépenses (6 derniers mois)</h3>
        <canvas id="evolutionChart" height="130"></canvas>
    </div>
    <div class="chart-card">
        <h3><i class="fas fa-chart-pie"></i> Répartition des ventes par type — {{ now()->translatedFormat('F Y') }}</h3>
        <canvas id="repartitionChart" height="130"></canvas>
    </div>
</div>

@endsection

@push('scripts')
<script>
const evolutionData = @json($evolution);
const ventesParType = @json($ventesParType);

new Chart(document.getElementById('evolutionChart'), {
    type: 'bar',
    data: {
        labels: evolutionData.map(d => d.mois),
        datasets: [
            {
                label: 'Ventes encaissées',
                data: evolutionData.map(d => d.ventes),
                backgroundColor: '#1a3a5c',
                borderRadius: 5,
            },
            {
                label: 'Dépenses',
                data: evolutionData.map(d => d.depenses),
                backgroundColor: '#ef9a9a',
                borderRadius: 5,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.dataset.label + ' : ' + ctx.parsed.y.toLocaleString('fr-FR') + ' FCFA'
                }
            }
        },
        scales: { y: { beginAtZero: true } }
    }
});

const typeLabels = { forage: 'Forage', livraison: 'Livraison', exterieure: 'Extérieure' };
const types = Object.keys(ventesParType);
new Chart(document.getElementById('repartitionChart'), {
    type: 'doughnut',
    data: {
        labels: types.map(t => typeLabels[t] || t),
        datasets: [{
            data: types.map(t => ventesParType[t]),
            backgroundColor: ['#1a3a5c', '#4fc3f7', '#ff8f00'],
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.label + ' : ' + ctx.parsed.toLocaleString('fr-FR') + ' FCFA'
                }
            }
        }
    }
});
</script>
@endpush
