@extends('layouts.app')

@php
$sections = [
    'forage'     => ['label' => 'Forage',             'icon' => '<i class="fas fa-faucet"></i>',  'color' => '#1a3a5c', 'bg' => '#e3f2fd'],
    'livraison'  => ['label' => 'Livraisons',          'icon' => null,                             'color' => '#2e7d32', 'bg' => '#e8f5e9'],
    'exterieure' => ['label' => 'Ventes extérieures', 'icon' => '<i class="fas fa-box"></i>',     'color' => '#e65100', 'bg' => '#fff3e0'],
];
$section = $typeActif ? $sections[$typeActif] : null;
$pageTitle = $section ? $section['label'] : 'Toutes les ventes';
@endphp

@section('title', $pageTitle)
@section('page-title', $pageTitle)

@push('styles')
<style>
    .page-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
    .btn { padding: 10px 18px; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
    .btn-primary { background: #1a3a5c; color: #fff; }
    .btn-primary:hover { background: #0d2d4a; }
    .btn-sm { padding: 6px 12px; font-size: 0.8rem; }
    .btn-outline { background: transparent; border: 1.5px solid #1a3a5c; color: #1a3a5c; }
    .btn-outline:hover { background: #1a3a5c; color: #fff; }
    .btn-danger { background: #e53935; color: #fff; }
    .btn-danger:hover { background: #b71c1c; }

    /* Bannière de section */
    .section-banner {
        border-radius: 12px; padding: 18px 24px; margin-bottom: 20px;
        display: flex; align-items: center; gap: 14px;
        border-left: 5px solid;
    }
    .section-banner .banner-icon { font-size: 1.6rem; }
    .section-banner h2 { font-size: 1.1rem; font-weight: 700; margin-bottom: 2px; }
    .section-banner p  { font-size: 0.82rem; opacity: 0.75; }

    /* Stats section */
    .stats-section { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
    .stat-card { background: #fff; border-radius: 10px; padding: 16px 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .stat-card p { font-size: 0.77rem; color: #888; margin-bottom: 4px; }
    .stat-card h3 { font-size: 1.1rem; font-weight: 700; color: #1a3a5c; }
    .stat-card h3.red   { color: #c62828; }
    .stat-card h3.green { color: #2e7d32; }

    /* Totaux globaux (vue "Toutes") */
    .totaux-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    .total-card { background: #fff; border-radius: 10px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; align-items: center; gap: 14px; cursor: pointer; transition: box-shadow 0.2s; text-decoration: none; }
    .total-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.12); }
    .total-card .icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
    .icon-forage    { background: #e3f2fd; color: #1a3a5c; }
    .icon-livraison { background: #e8f5e9; color: #2e7d32; }
    .icon-exterieure{ background: #fff3e0; color: #e65100; }
    .total-card p { font-size: 0.78rem; color: #888; margin-bottom: 2px; }
    .total-card h3 { font-size: 1.05rem; font-weight: 700; color: #1a3a5c; }

    /* Filtres */
    .filters { background: #fff; border-radius: 10px; padding: 14px 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .filters form { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; }
    .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .filter-group label { font-size: 0.78rem; font-weight: 600; color: #555; }
    .filter-group select, .filter-group input { padding: 8px 12px; border: 1.5px solid #e0e0e0; border-radius: 7px; font-size: 0.875rem; outline: none; background: #fff; }
    .filter-group select:focus, .filter-group input:focus { border-color: #1a3a5c; }

    /* Table */
    .table-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #f8f9fa; padding: 12px 16px; text-align: left; font-size: 0.78rem; font-weight: 700; color: #555; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #eee; }
    tbody td { padding: 13px 16px; font-size: 0.875rem; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #fafafa; }

    .badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .badge-forage    { background: #e3f2fd; color: #1565c0; }
    .badge-livraison { background: #e8f5e9; color: #2e7d32; }
    .badge-exterieure{ background: #fff3e0; color: #e65100; }
    .badge-paye      { background: #e8f5e9; color: #2e7d32; }
    .badge-non-paye  { background: #ffebee; color: #c62828; }

    .montant { font-weight: 700; color: #1a3a5c; }
    .actions { display: flex; gap: 6px; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
    .empty-state i { font-size: 3rem; margin-bottom: 12px; display: block; color: #ddd; }
    .pagination-wrapper { padding: 16px 20px; border-top: 1px solid #eee; }
</style>
@endpush

@section('content')

{{-- Bouton nouvelle vente --}}
<div class="page-actions">
    <div style="font-size:0.875rem; color:#888;">{{ now()->translatedFormat('l d F Y') }}</div>
    <a href="{{ route('ventes.create', $typeActif ? ['type' => $typeActif] : []) }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Nouvelle {{ $typeActif === 'forage' ? 'vente forage' : ($typeActif === 'livraison' ? 'livraison' : ($typeActif === 'exterieure' ? 'vente extérieure' : 'vente')) }}
    </a>
</div>

@if($section)
{{-- ====== VUE SECTION SPÉCIFIQUE ====== --}}

{{-- Bannière de section --}}
<div class="section-banner" style="background:{{ $section['bg'] }}; border-color:{{ $section['color'] }}; color:{{ $section['color'] }};">
    <div class="banner-icon">
        @if($typeActif === 'forage') <i class="fas fa-faucet"></i>
        @elseif($typeActif === 'livraison') <x-icon-tuyau style="font-size:1.6rem;" />
        @else <i class="fas fa-box"></i>
        @endif
    </div>
    <div>
        <h2>{{ $section['label'] }}</h2>
        <p>
            @if($typeActif === 'forage') Ventes sur place — paiement immédiat
            @elseif($typeActif === 'livraison') Livraisons vers les bases d'eau
            @else Livraisons extérieures — bidons et fûts
            @endif
        </p>
    </div>
</div>

{{-- Stats de la section --}}
<div class="stats-section">
    <div class="stat-card">
        <p>Nb total de ventes</p>
        <h3>{{ $statsSection['nb_ventes'] }}</h3>
    </div>
    @if(auth()->user()->isAdmin())
    <div class="stat-card">
        <p>Total encaissé</p>
        <h3 class="green">{{ number_format($statsSection['total_paye'], 0, ',', ' ') }} FCFA</h3>
    </div>
    @if($typeActif !== 'forage')
    <div class="stat-card">
        <p>Total crédits impayés</p>
        <h3 class="red">{{ number_format($statsSection['total_credit'], 0, ',', ' ') }} FCFA</h3>
    </div>
    <div class="stat-card">
        <p>Nb crédits en cours</p>
        <h3 class="red">{{ $statsSection['nb_credits'] }}</h3>
    </div>
    @else
    <div class="stat-card">
        <p>Ce mois</p>
        <h3>{{ number_format(\App\Models\Vente::forage()->where('statut_paiement','paye')->mensuel()->sum('montant'), 0, ',', ' ') }} FCFA</h3>
    </div>
    <div class="stat-card">
        <p>Aujourd'hui</p>
        <h3>{{ number_format(\App\Models\Vente::forage()->where('statut_paiement','paye')->journalier()->sum('montant'), 0, ',', ' ') }} FCFA</h3>
    </div>
    @endif
    @endif
</div>

@else
{{-- ====== VUE GLOBALE "TOUTES LES VENTES" ====== --}}

@if(auth()->user()->isAdmin())
<div class="totaux-grid">
    <a href="{{ route('ventes.index', ['type' => 'forage']) }}" class="total-card">
        <div class="icon icon-forage"><i class="fas fa-faucet"></i></div>
        <div>
            <p>Forage (encaissé)</p>
            <h3>{{ number_format($totaux['forage'], 0, ',', ' ') }} FCFA</h3>
        </div>
    </a>
    <a href="{{ route('ventes.index', ['type' => 'livraison']) }}" class="total-card">
        <div class="icon icon-livraison"><x-icon-tuyau /></div>
        <div>
            <p>Livraisons (encaissé)</p>
            <h3>{{ number_format($totaux['livraison'], 0, ',', ' ') }} FCFA</h3>
        </div>
    </a>
    <a href="{{ route('ventes.index', ['type' => 'exterieure']) }}" class="total-card">
        <div class="icon icon-exterieure"><i class="fas fa-box"></i></div>
        <div>
            <p>Extérieures (encaissé)</p>
            <h3>{{ number_format($totaux['exterieure'], 0, ',', ' ') }} FCFA</h3>
        </div>
    </a>
</div>
@else
<div style="display:flex; gap:12px; margin-bottom:20px; flex-wrap:wrap;">
    <a href="{{ route('ventes.index', ['type' => 'forage']) }}" class="total-card" style="flex:1; min-width:140px; cursor:pointer;">
        <div class="icon icon-forage"><i class="fas fa-faucet"></i></div>
        <div><p>Forage</p><h3 style="font-size:0.9rem; color:#555;">Voir →</h3></div>
    </a>
    <a href="{{ route('ventes.index', ['type' => 'livraison']) }}" class="total-card" style="flex:1; min-width:140px;">
        <div class="icon icon-livraison"><x-icon-tuyau /></div>
        <div><p>Livraisons</p><h3 style="font-size:0.9rem; color:#555;">Voir →</h3></div>
    </a>
    <a href="{{ route('ventes.index', ['type' => 'exterieure']) }}" class="total-card" style="flex:1; min-width:140px;">
        <div class="icon icon-exterieure"><i class="fas fa-box"></i></div>
        <div><p>Extérieures</p><h3 style="font-size:0.9rem; color:#555;">Voir →</h3></div>
    </a>
</div>
@endif
@endif

{{-- Filtres --}}
<div class="filters">
    <form method="GET" action="{{ route('ventes.index') }}">
        @if($typeActif)
            <input type="hidden" name="type" value="{{ $typeActif }}">
        @else
        <div class="filter-group">
            <label>Type</label>
            <select name="type">
                <option value="">Tous</option>
                <option value="forage"     {{ request('type') === 'forage'     ? 'selected' : '' }}>Forage</option>
                <option value="livraison"  {{ request('type') === 'livraison'  ? 'selected' : '' }}>Livraison</option>
                <option value="exterieure" {{ request('type') === 'exterieure' ? 'selected' : '' }}>Extérieure</option>
            </select>
        </div>
        @endif
        @if($typeActif !== 'forage')
        <div class="filter-group">
            <label>Statut</label>
            <select name="statut">
                <option value="">Tous</option>
                <option value="paye"     {{ request('statut') === 'paye'     ? 'selected' : '' }}>Payé</option>
                <option value="non_paye" {{ request('statut') === 'non_paye' ? 'selected' : '' }}>Non payé (crédit)</option>
            </select>
        </div>
        @endif
        <div class="filter-group">
            <label>Date début</label>
            <input type="date" name="date_debut" value="{{ request('date_debut') }}">
        </div>
        <div class="filter-group">
            <label>Date fin</label>
            <input type="date" name="date_fin" value="{{ request('date_fin') }}">
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Filtrer</button>
        <a href="{{ route('ventes.index', $typeActif ? ['type' => $typeActif] : []) }}"
           class="btn btn-outline btn-sm"><i class="fas fa-times"></i> Réinitialiser</a>
    </form>
</div>

{{-- Table --}}
<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                @if(!$typeActif)<th>Type</th>@endif
                @if($typeActif !== 'forage')<th>Client</th>@endif
                <th>Montant</th>
                @if($typeActif !== 'forage')<th>Statut</th>@endif
                <th>Enregistré par</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventes as $vente)
            <tr>
                <td style="color:#999; font-size:0.8rem;">#{{ $vente->id }}</td>
                <td>{{ $vente->date_vente->format('d/m/Y') }}</td>
                @if(!$typeActif)
                <td>
                    <span class="badge badge-{{ $vente->type_vente }}">
                        @if($vente->type_vente === 'forage') <i class="fas fa-faucet"></i> Forage
                        @elseif($vente->type_vente === 'livraison') <x-icon-tuyau /> Livraison
                        @else <i class="fas fa-box"></i> Extérieure
                        @endif
                    </span>
                </td>
                @endif
                @if($typeActif !== 'forage')
                <td>{{ $vente->client?->nom ?? '—' }}</td>
                @endif
                <td class="montant">{{ number_format($vente->montant, 0, ',', ' ') }} FCFA</td>
                @if($typeActif !== 'forage')
                <td>
                    @if($vente->type_vente === 'forage')
                        <span style="color:#aaa; font-size:0.82rem;">—</span>
                    @else
                        <span class="badge {{ $vente->statut_paiement === 'paye' ? 'badge-paye' : 'badge-non-paye' }}">
                            @if($vente->statut_paiement === 'paye')
                                <i class="fas fa-check-circle"></i> Payé
                            @else
                                <i class="fas fa-clock"></i> Crédit
                            @endif
                        </span>
                    @endif
                </td>
                @endif
                <td style="color:#777; font-size:0.82rem;">{{ $vente->user->name }}</td>
                <td>
                    <div class="actions">
                        <a href="{{ route('ventes.show', $vente) }}" class="btn btn-outline btn-sm" title="Voir">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if(auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('ventes.destroy', $vente) }}"
                              onsubmit="return confirm('Supprimer cette vente ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8">
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Aucune vente trouvée.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($ventes->hasPages())
    <div class="pagination-wrapper">{{ $ventes->links() }}</div>
    @endif
</div>
@endsection
