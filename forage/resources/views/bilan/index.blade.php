@extends('layouts.app')

@section('title', 'Bilan Total')
@section('page-title', 'Bilan Total')

@push('styles')
<style>
    .bilan-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px; margin-bottom: 28px;
    }
    .bilan-card {
        background: #fff; border-radius: 12px; padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        display: flex; align-items: center; gap: 16px;
    }
    .bilan-icon {
        width: 54px; height: 54px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; flex-shrink: 0;
    }
    .bilan-icon.blue   { background: #e3f2fd; color: #1a3a5c; }
    .bilan-icon.green  { background: #e8f5e9; color: #2e7d32; }
    .bilan-icon.red    { background: #ffebee; color: #c62828; }
    .bilan-icon.orange { background: #fff3e0; color: #e65100; }
    .bilan-icon.teal   { background: #e0f2f1; color: #00695c; }
    .bilan-icon.net-pos { background: #e8f5e9; color: #1b5e20; }
    .bilan-icon.net-neg { background: #ffebee; color: #b71c1c; }
    .bilan-info p  { font-size: 0.8rem; color: #888; margin-bottom: 4px; }
    .bilan-info h2 { font-size: 1.45rem; font-weight: 700; color: #1a3a5c; }
    .bilan-info h2.pos { color: #2e7d32; }
    .bilan-info h2.neg { color: #c62828; }

    .section-card {
        background: #fff; border-radius: 12px; padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 24px;
    }
    .section-card h3 {
        font-size: 1rem; color: #1a3a5c; font-weight: 700;
        margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee;
        display: flex; align-items: center; gap: 8px;
    }

    .type-breakdown { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
    .type-item {
        text-align: center; padding: 16px; border-radius: 10px; background: #f8f9fa;
    }
    .type-item .type-icon { font-size: 1.6rem; margin-bottom: 8px; }
    .type-item .type-label { font-size: 0.8rem; color: #666; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .type-item .type-amount { font-size: 1.2rem; font-weight: 700; color: #1a3a5c; margin-top: 4px; }

    .mois-chart-wrap { height: 200px; }

    @media(max-width: 768px) {
        .type-breakdown { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

{{-- Résumé global --}}
<div class="bilan-grid">
    <div class="bilan-card">
        <div class="bilan-icon blue"><i class="fas fa-coins"></i></div>
        <div class="bilan-info">
            <p>Total ventes encaissées</p>
            <h2>{{ number_format($totalVentes, 0, ',', ' ') }} FCFA</h2>
        </div>
    </div>
    <div class="bilan-card">
        <div class="bilan-icon red"><i class="fas fa-wallet"></i></div>
        <div class="bilan-info">
            <p>Total dépenses</p>
            <h2>{{ number_format($totalDepenses, 0, ',', ' ') }} FCFA</h2>
        </div>
    </div>
    <div class="bilan-card">
        <div class="bilan-icon {{ $bilanNet >= 0 ? 'net-pos' : 'net-neg' }}">
            <i class="fas fa-balance-scale"></i>
        </div>
        <div class="bilan-info">
            <p>Bilan net (ventes − dépenses)</p>
            <h2 class="{{ $bilanNet >= 0 ? 'pos' : 'neg' }}">
                {{ $bilanNet >= 0 ? '+' : '' }}{{ number_format($bilanNet, 0, ',', ' ') }} FCFA
            </h2>
        </div>
    </div>
    <div class="bilan-card">
        <div class="bilan-icon orange"><i class="fas fa-clock"></i></div>
        <div class="bilan-info">
            <p>Crédits livraison/ext. en attente ({{ $nbCredits }})</p>
            <h2>{{ number_format($totalCredits, 0, ',', ' ') }} FCFA</h2>
        </div>
    </div>
    <div class="bilan-card">
        <div class="bilan-icon teal"><i class="fas fa-pencil-alt"></i></div>
        <div class="bilan-info">
            <p>Ardoises forage en cours ({{ $nbArdoiseEnCours }})</p>
            <h2>{{ number_format($ardoiseEnCours, 0, ',', ' ') }} FCFA</h2>
        </div>
    </div>
    <div class="bilan-card">
        <div class="bilan-icon green"><i class="fas fa-pencil-alt"></i></div>
        <div class="bilan-info">
            <p>Ardoises forage remboursées</p>
            <h2>{{ number_format($ardoiseRemboursee, 0, ',', ' ') }} FCFA</h2>
        </div>
    </div>
</div>

{{-- Ce mois --}}
<div class="section-card">
    <h3><i class="fas fa-calendar-alt"></i> Ce mois ({{ now()->translatedFormat('F Y') }})</h3>
    <div class="bilan-grid" style="margin-bottom:0;">
        <div class="bilan-card" style="box-shadow:none; background:#f8f9fa; border-radius:10px;">
            <div class="bilan-icon green"><i class="fas fa-arrow-up"></i></div>
            <div class="bilan-info">
                <p>Ventes encaissées</p>
                <h2>{{ number_format($ventesCeMois, 0, ',', ' ') }} FCFA</h2>
            </div>
        </div>
        <div class="bilan-card" style="box-shadow:none; background:#f8f9fa; border-radius:10px;">
            <div class="bilan-icon red"><i class="fas fa-arrow-down"></i></div>
            <div class="bilan-info">
                <p>Dépenses</p>
                <h2>{{ number_format($depensesCeMois, 0, ',', ' ') }} FCFA</h2>
            </div>
        </div>
        @php $netMois = $ventesCeMois - $depensesCeMois; @endphp
        <div class="bilan-card" style="box-shadow:none; background:#f8f9fa; border-radius:10px;">
            <div class="bilan-icon {{ $netMois >= 0 ? 'net-pos' : 'net-neg' }}"><i class="fas fa-balance-scale"></i></div>
            <div class="bilan-info">
                <p>Net ce mois</p>
                <h2 class="{{ $netMois >= 0 ? 'pos' : 'neg' }}">
                    {{ $netMois >= 0 ? '+' : '' }}{{ number_format($netMois, 0, ',', ' ') }} FCFA
                </h2>
            </div>
        </div>
    </div>
</div>

{{-- Répartition par type --}}
<div class="section-card">
    <h3><i class="fas fa-chart-pie"></i> Ventes encaissées par type (total)</h3>
    <div class="type-breakdown">
        <div class="type-item">
            <div class="type-icon" style="color:#1a3a5c;"><i class="fas fa-faucet"></i></div>
            <div class="type-label">Forage</div>
            <div class="type-amount">{{ number_format($ventesTotales['forage'], 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="type-item">
            <div class="type-icon" style="color:#2e7d32;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><rect x="0" y="8" width="4" height="8" rx="1"/><rect x="4" y="10" width="16" height="4"/><rect x="20" y="8" width="4" height="8" rx="1"/></svg>
            </div>
            <div class="type-label">Livraison</div>
            <div class="type-amount">{{ number_format($ventesTotales['livraison'], 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="type-item">
            <div class="type-icon" style="color:#e65100;"><i class="fas fa-box"></i></div>
            <div class="type-label">Extérieure</div>
            <div class="type-amount">{{ number_format($ventesTotales['exterieure'], 0, ',', ' ') }} FCFA</div>
        </div>
    </div>
</div>

{{-- Évolution mensuelle --}}
<div class="section-card">
    <h3><i class="fas fa-chart-bar"></i> Évolution mensuelle {{ $annee }}</h3>
    <div class="mois-chart-wrap">
        <canvas id="bilanChart"></canvas>
    </div>
</div>

@endsection

@push('scripts')
<script>
const parMois = @json($parMois);

new Chart(document.getElementById('bilanChart'), {
    type: 'bar',
    data: {
        labels: parMois.map(m => m.label),
        datasets: [
            {
                label: 'Ventes',
                data: parMois.map(m => m.ventes),
                backgroundColor: '#1a3a5c',
                borderRadius: 4,
            },
            {
                label: 'Dépenses',
                data: parMois.map(m => m.depenses),
                backgroundColor: '#ef9a9a',
                borderRadius: 4,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.dataset.label + ': ' + ctx.parsed.y.toLocaleString('fr-FR') + ' FCFA'
                }
            }
        },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
@endpush
