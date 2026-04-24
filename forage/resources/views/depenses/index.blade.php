@extends('layouts.app')

@section('title', 'Dépenses')
@section('page-title', 'Gestion des dépenses')

@push('styles')
<style>
    .page-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .btn { padding: 10px 18px; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
    .btn-sm { padding: 6px 12px; font-size: 0.8rem; }
    .btn-primary { background: #1a3a5c; color: #fff; }
    .btn-primary:hover { background: #0d2d4a; }
    .btn-outline { background: transparent; border: 1.5px solid #1a3a5c; color: #1a3a5c; }
    .btn-outline:hover { background: #1a3a5c; color: #fff; }
    .btn-danger { background: #e53935; color: #fff; }
    .btn-danger:hover { background: #b71c1c; }

    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: #fff; border-radius: 10px; padding: 18px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; align-items: center; gap: 14px; }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .icon-red    { background: #ffebee; color: #c62828; }
    .icon-orange { background: #fff3e0; color: #e65100; }
    .icon-purple { background: #f3e5f5; color: #6a1b9a; }
    .stat-card p { font-size: 0.78rem; color: #888; margin-bottom: 3px; }
    .stat-card h3 { font-size: 1.15rem; font-weight: 700; color: #1a3a5c; }

    .filters { background: #fff; border-radius: 10px; padding: 14px 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .filters form { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; }
    .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .filter-group label { font-size: 0.78rem; font-weight: 600; color: #555; }
    .filter-group input { padding: 8px 12px; border: 1.5px solid #e0e0e0; border-radius: 7px; font-size: 0.875rem; outline: none; }
    .filter-group input:focus { border-color: #1a3a5c; }

    .table-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #f8f9fa; padding: 12px 16px; text-align: left; font-size: 0.78rem; font-weight: 700; color: #555; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #eee; }
    tbody td { padding: 13px 16px; font-size: 0.875rem; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #fafafa; }

    .montant-depense { font-weight: 700; color: #c62828; font-size: 1rem; }
    .motif-cell { max-width: 260px; }
    .motif-text { font-weight: 600; color: #333; }
    .actions { display: flex; gap: 6px; }
    .empty-state { text-align: center; padding: 60px 20px; color: #aaa; }
    .empty-state i { font-size: 3rem; display: block; margin-bottom: 12px; color: #ddd; }
    .pagination-wrapper { padding: 16px 20px; border-top: 1px solid #eee; }
    .total-row td { background: #fff8e1; font-weight: 700; border-top: 2px solid #ffe082; }
</style>
@endpush

@section('content')
<div class="page-actions">
    <div></div>
    <a href="{{ route('depenses.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouvelle dépense
    </a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon icon-red"><i class="fas fa-calendar-day"></i></div>
        <div>
            <p>Aujourd'hui</p>
            <h3>{{ number_format($stats['aujourd_hui'], 0, ',', ' ') }} FCFA</h3>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-orange"><i class="fas fa-calendar-alt"></i></div>
        <div>
            <p>Ce mois ({{ now()->translatedFormat('F') }})</p>
            <h3>{{ number_format($stats['ce_mois'], 0, ',', ' ') }} FCFA</h3>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-purple"><i class="fas fa-chart-bar"></i></div>
        <div>
            <p>Cette année ({{ now()->year }})</p>
            <h3>{{ number_format($stats['cette_annee'], 0, ',', ' ') }} FCFA</h3>
        </div>
    </div>
</div>

<div class="filters">
    <form method="GET" action="{{ route('depenses.index') }}">
        <div class="filter-group">
            <label>Recherche motif</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ex: carburant, salaire...">
        </div>
        <div class="filter-group">
            <label>Date début</label>
            <input type="date" name="date_debut" value="{{ request('date_debut') }}">
        </div>
        <div class="filter-group">
            <label>Date fin</label>
            <input type="date" name="date_fin" value="{{ request('date_fin') }}">
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Filtrer</button>
        <a href="{{ route('depenses.index') }}" class="btn btn-outline btn-sm"><i class="fas fa-times"></i> Réinitialiser</a>
    </form>
</div>

<div class="table-card">
    <table>
        <thead>
            <tr>
                <th class="hide-mobile">#</th>
                <th>Date</th>
                <th>Motif</th>
                <th>Montant</th>
                <th class="hide-mobile">Enregistré par</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($depenses as $depense)
            <tr>
                <td class="hide-mobile" style="color:#aaa; font-size:0.8rem;">#{{ $depense->id }}</td>
                <td>{{ $depense->date_depense->format('d/m/Y') }}</td>
                <td class="motif-cell">
                    <div class="motif-text">{{ $depense->motif }}</div>
                </td>
                <td class="montant-depense">{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</td>
                <td class="hide-mobile" style="color:#777; font-size:0.82rem;">{{ $depense->user->name }}</td>
                <td>
                    <div class="actions">
                        @if(auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('depenses.destroy', $depense) }}"
                              onsubmit="return confirm('Supprimer cette dépense ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Aucune dépense trouvée.</p>
                    </div>
                </td>
            </tr>
            @endforelse
            @if($depenses->count())
            <tr class="total-row">
                <td colspan="3" style="text-align:right; color:#888;">Total affiché</td>
                <td>{{ number_format($depenses->sum('montant'), 0, ',', ' ') }} FCFA</td>
                <td colspan="2"></td>
            </tr>
            @endif
        </tbody>
    </table>
    @if($depenses->hasPages())
    <div class="pagination-wrapper">{{ $depenses->links() }}</div>
    @endif
</div>
@endsection
