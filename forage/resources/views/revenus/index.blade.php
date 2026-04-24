@extends('layouts.app')

@section('title', 'Revenus')
@section('page-title', 'Rapport des revenus')

@push('styles')
<style>
    .periode-form { background: #fff; border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap; }
    .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .filter-group label { font-size: 0.78rem; font-weight: 600; color: #555; }
    .filter-group select { padding: 9px 12px; border: 1.5px solid #e0e0e0; border-radius: 7px; font-size: 0.875rem; outline: none; background: #fff; min-width: 130px; }
    .filter-group select:focus { border-color: #1a3a5c; }
    .btn { padding: 9px 18px; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 7px; transition: all 0.2s; }
    .btn-primary { background: #1a3a5c; color: #fff; }
    .btn-primary:hover { background: #0d2d4a; }

    /* KPI cards */
    .kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    .kpi-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .kpi-card .kpi-label { font-size: 0.78rem; color: #888; margin-bottom: 6px; }
    .kpi-card .kpi-value { font-size: 1.5rem; font-weight: 800; margin-bottom: 4px; }
    .kpi-card .kpi-sub { font-size: 0.78rem; color: #aaa; }
    .kpi-card .kpi-evol { display: inline-flex; align-items: center; gap: 4px; font-size: 0.8rem; font-weight: 600; padding: 2px 8px; border-radius: 12px; margin-top: 4px; }
    .evol-up   { background: #e8f5e9; color: #2e7d32; }
    .evol-down { background: #ffebee; color: #c62828; }
    .color-green  { color: #2e7d32; }
    .color-red    { color: #c62828; }
    .color-blue   { color: #1a3a5c; }
    .color-orange { color: #e65100; }
    .color-purple { color: #6a1b9a; }

    /* Répartition par type */
    .types-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 24px; }
    .type-card { background: #fff; border-radius: 10px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; align-items: center; gap: 14px; }
    .type-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
    .icon-forage    { background: #e3f2fd; color: #1a3a5c; }
    .icon-livraison { background: #e8f5e9; color: #2e7d32; }
    .icon-ext       { background: #fff3e0; color: #e65100; }
    .type-card p { font-size: 0.78rem; color: #888; margin-bottom: 3px; }
    .type-card h4 { font-size: 1.05rem; font-weight: 700; }
    .type-pct { font-size: 0.75rem; color: #aaa; }

    /* Charts */
    .charts-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px; }
    .chart-card { background: #fff; border-radius: 12px; padding: 22px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .chart-card h3 { font-size: 0.9rem; font-weight: 700; color: #1a3a5c; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    @media(max-width:900px) { .charts-grid { grid-template-columns: 1fr; } .kpi-grid, .types-grid { grid-template-columns: 1fr 1fr; } }

    /* Table journalière */
    .table-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    .table-header { padding: 16px 20px; border-bottom: 1px solid #eee; font-size: 0.9rem; font-weight: 700; color: #1a3a5c; display: flex; align-items: center; gap: 8px; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #f8f9fa; padding: 11px 16px; text-align: left; font-size: 0.77rem; font-weight: 700; color: #555; text-transform: uppercase; border-bottom: 1px solid #eee; }
    thead th:not(:first-child) { text-align: right; }
    tbody td { padding: 12px 16px; font-size: 0.875rem; border-bottom: 1px solid #f5f5f5; }
    tbody td:not(:first-child) { text-align: right; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #fafafa; }
    .total-row td { background: #e3f2fd; font-weight: 700; border-top: 2px solid #90caf9; }
    .empty-state { text-align: center; padding: 40px; color: #aaa; }
    .section-title { font-size: 0.9rem; font-weight: 700; color: #1a3a5c; margin: 24px 0 14px; display: flex; align-items: center; gap: 8px; }
</style>
@endpush

@section('content')

{{-- Sélecteur de période --}}
<form class="periode-form" method="GET" action="{{ route('revenus.index') }}">
    <div class="filter-group">
        <label>Mois</label>
        <select name="mois">
            @foreach($moisNoms as $num => $nom)
                @if($num > 0)
                <option value="{{ $num }}" {{ $mois == $num ? 'selected' : '' }}>{{ $nom }}</option>
                @endif
            @endforeach
        </select>
    </div>
    <div class="filter-group">
        <label>Année</label>
        <select name="annee">
            @foreach($annees as $a)
                <option value="{{ $a }}" {{ $annee == $a ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
            @if($annees->isEmpty())
                <option value="{{ now()->year }}" selected>{{ now()->year }}</option>
            @endif
        </select>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-sync"></i> Actualiser</button>
</form>

{{-- KPI --}}
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-label">Revenus bruts — {{ $moisNoms[$mois] }} {{ $annee }}</div>
        <div class="kpi-value color-blue">{{ number_format($totauxMois['total'], 0, ',', ' ') }} FCFA</div>
        @if($evolution !== null)
            <span class="kpi-evol {{ $evolution >= 0 ? 'evol-up' : 'evol-down' }}">
                <i class="fas fa-arrow-{{ $evolution >= 0 ? 'up' : 'down' }}"></i>
                {{ abs($evolution) }}% vs mois précédent
            </span>
        @endif
        <div class="kpi-sub">Mois précédent : {{ number_format($totalMoisPrec, 0, ',', ' ') }} FCFA</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Dépenses — {{ $moisNoms[$mois] }} {{ $annee }}</div>
        <div class="kpi-value color-red">{{ number_format($totauxMois['depenses'], 0, ',', ' ') }} FCFA</div>
        <div class="kpi-sub">Sorties d'argent enregistrées</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Bénéfice net — {{ $moisNoms[$mois] }} {{ $annee }}</div>
        <div class="kpi-value {{ $totauxMois['net'] >= 0 ? 'color-green' : 'color-red' }}">
            {{ number_format($totauxMois['net'], 0, ',', ' ') }} FCFA
        </div>
        <div class="kpi-sub">Revenus − Dépenses</div>
    </div>
</div>

{{-- Répartition par type --}}
<div class="types-grid">
    @php $totalMois = $totauxMois['total'] ?: 1; @endphp
    <div class="type-card">
        <div class="type-icon icon-forage"><i class="fas fa-faucet"></i></div>
        <div>
            <p>Forage</p>
            <h4 class="color-blue">{{ number_format($totauxMois['forage'], 0, ',', ' ') }} FCFA</h4>
            <div class="type-pct">{{ round($totauxMois['forage'] / $totalMois * 100) }}% du total</div>
        </div>
    </div>
    <div class="type-card">
        <div class="type-icon icon-livraison"><i class="fas fa-truck"></i></div>
        <div>
            <p>Livraisons</p>
            <h4 class="color-green">{{ number_format($totauxMois['livraison'], 0, ',', ' ') }} FCFA</h4>
            <div class="type-pct">{{ round($totauxMois['livraison'] / $totalMois * 100) }}% du total</div>
        </div>
    </div>
    <div class="type-card">
        <div class="type-icon icon-ext"><i class="fas fa-box"></i></div>
        <div>
            <p>Extérieures</p>
            <h4 class="color-orange">{{ number_format($totauxMois['exterieure'], 0, ',', ' ') }} FCFA</h4>
            <div class="type-pct">{{ round($totauxMois['exterieure'] / $totalMois * 100) }}% du total</div>
        </div>
    </div>
</div>

{{-- Graphiques --}}
<div class="charts-grid">
    <div class="chart-card">
        <h3><i class="fas fa-chart-bar"></i> Revenus journaliers — {{ $moisNoms[$mois] }} {{ $annee }}</h3>
        <canvas id="chartJour" height="100"></canvas>
    </div>
    <div class="chart-card">
        <h3><i class="fas fa-chart-pie"></i> Répartition types</h3>
        <canvas id="chartTypes" height="160"></canvas>
    </div>
</div>

{{-- Table journalière --}}
<div class="table-card" style="margin-bottom: 24px;">
    <div class="table-header"><i class="fas fa-calendar-day"></i> Détail journalier — {{ $moisNoms[$mois] }} {{ $annee }}</div>
    <table>
        <thead>
            <tr>
                <th>Jour</th>
                <th>Forage</th>
                <th>Livraison</th>
                <th>Extérieure</th>
                <th>Total jour</th>
            </tr>
        </thead>
        <tbody>
            @forelse($revenusJournaliers as $row)
            <tr>
                <td>{{ \Carbon\Carbon::parse($row->jour)->format('d/m/Y') }}</td>
                <td>{{ $row->forage > 0 ? number_format($row->forage, 0, ',', ' ') . ' FCFA' : '—' }}</td>
                <td>{{ $row->livraison > 0 ? number_format($row->livraison, 0, ',', ' ') . ' FCFA' : '—' }}</td>
                <td>{{ $row->exterieure > 0 ? number_format($row->exterieure, 0, ',', ' ') . ' FCFA' : '—' }}</td>
                <td style="font-weight:700; color:#1a3a5c;">{{ number_format($row->total, 0, ',', ' ') }} FCFA</td>
            </tr>
            @empty
            <tr><td colspan="5"><div class="empty-state">Aucune vente ce mois.</div></td></tr>
            @endforelse
            @if($revenusJournaliers->count())
            <tr class="total-row">
                <td>TOTAL</td>
                <td>{{ number_format($totauxMois['forage'], 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($totauxMois['livraison'], 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($totauxMois['exterieure'], 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($totauxMois['total'], 0, ',', ' ') }} FCFA</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

{{-- Table mensuelle de l'année --}}
<div class="table-card">
    <div class="table-header"><i class="fas fa-calendar-alt"></i> Récapitulatif annuel — {{ $annee }}</div>
    <table>
        <thead>
            <tr>
                <th>Mois</th>
                <th>Forage</th>
                <th>Livraison</th>
                <th>Extérieure</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach(range(1, 12) as $m)
            @php $row = $revenusMensuels[$m] ?? null; @endphp
            <tr>
                <td style="{{ $m == $mois ? 'font-weight:700; color:#1a3a5c;' : '' }}">
                    {{ $moisNoms[$m] }}
                    @if($m == $mois) <span style="font-size:0.75rem; color:#4fc3f7;"> ◀ actuel</span> @endif
                </td>
                <td>{{ $row ? number_format($row->forage, 0, ',', ' ') . ' FCFA' : '—' }}</td>
                <td>{{ $row ? number_format($row->livraison, 0, ',', ' ') . ' FCFA' : '—' }}</td>
                <td>{{ $row ? number_format($row->exterieure, 0, ',', ' ') . ' FCFA' : '—' }}</td>
                <td style="font-weight:600; color:#1a3a5c;">
                    {{ $row ? number_format($row->total, 0, ',', ' ') . ' FCFA' : '—' }}
                </td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td>TOTAL {{ $annee }}</td>
                <td colspan="3"></td>
                <td>{{ number_format($totauxAnnee['total'], 0, ',', ' ') }} FCFA</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
// Graphique journalier
const joursData = @json($revenusJournaliers);
new Chart(document.getElementById('chartJour'), {
    type: 'bar',
    data: {
        labels: joursData.map(d => {
            const date = new Date(d.jour);
            return date.getDate() + '/' + (date.getMonth()+1);
        }),
        datasets: [
            { label: 'Forage', data: joursData.map(d => d.forage), backgroundColor: '#1a3a5c', borderRadius: 4 },
            { label: 'Livraison', data: joursData.map(d => d.livraison), backgroundColor: '#4caf50', borderRadius: 4 },
            { label: 'Extérieure', data: joursData.map(d => d.exterieure), backgroundColor: '#ff8f00', borderRadius: 4 },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }
    }
});

// Graphique répartition
const totaux = @json($totauxMois);
new Chart(document.getElementById('chartTypes'), {
    type: 'doughnut',
    data: {
        labels: ['Forage', 'Livraison', 'Extérieure'],
        datasets: [{
            data: [totaux.forage, totaux.livraison, totaux.exterieure],
            backgroundColor: ['#1a3a5c', '#4caf50', '#ff8f00'],
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
@endpush
