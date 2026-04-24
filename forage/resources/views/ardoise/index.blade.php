@extends('layouts.app')

@section('title', 'Ardoise Forage')
@section('page-title', 'Ardoise Forage')

@push('styles')
<style>
    .page-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
    .btn { padding: 10px 18px; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
    .btn-primary { background: #1a3a5c; color: #fff; }
    .btn-primary:hover { background: #0d2d4a; }
    .btn-sm { padding: 6px 12px; font-size: 0.8rem; }
    .btn-success { background: #2e7d32; color: #fff; }
    .btn-success:hover { background: #1b5e20; }
    .btn-danger { background: #e53935; color: #fff; }
    .btn-danger:hover { background: #b71c1c; }

    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: #fff; border-radius: 10px; padding: 18px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; align-items: center; gap: 14px; }
    .stat-icon { width: 46px; height: 46px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
    .stat-icon.red    { background: #ffebee; color: #c62828; }
    .stat-icon.green  { background: #e8f5e9; color: #2e7d32; }
    .stat-info p  { font-size: 0.78rem; color: #888; margin-bottom: 3px; }
    .stat-info h3 { font-size: 1.05rem; font-weight: 700; color: #1a3a5c; }

    .tabs { display: flex; gap: 4px; background: #fff; border-radius: 10px; padding: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; width: fit-content; }
    .tab { padding: 8px 18px; border-radius: 7px; font-size: 0.875rem; font-weight: 600; text-decoration: none; color: #666; transition: all 0.2s; }
    .tab.active { background: #1a3a5c; color: #fff; }
    .tab:hover:not(.active) { background: #f0f2f5; color: #333; }

    .table-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #f8f9fa; padding: 12px 16px; text-align: left; font-size: 0.78rem; font-weight: 700; color: #555; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #eee; }
    tbody td { padding: 13px 16px; font-size: 0.875rem; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #fafafa; }
    .montant { font-weight: 700; color: #c62828; }
    .montant.ok { color: #2e7d32; }
    .nom-cell { font-weight: 600; color: #1a3a5c; }
    .actions { display: flex; gap: 6px; align-items: center; }
    .badge-rembourse { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; background: #e8f5e9; color: #2e7d32; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
    .empty-state i { font-size: 3rem; margin-bottom: 12px; display: block; color: #ddd; }
    .pagination-wrapper { padding: 16px 20px; border-top: 1px solid #eee; }
    .notes-text { font-size: 0.8rem; color: #888; font-style: italic; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>
@endpush

@section('content')

<div class="page-actions">
    <div style="font-size:0.875rem; color:#888;">{{ now()->translatedFormat('l d F Y') }}</div>
    <a href="{{ route('ardoise.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouvelle ardoise
    </a>
</div>

{{-- Stats (totaux visibles par l'admin uniquement) --}}
@if(auth()->user()->isAdmin())
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <p>En cours ({{ $stats['nb_en_cours'] }})</p>
            <h3>{{ number_format($stats['total_en_cours'], 0, ',', ' ') }} FCFA</h3>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <p>Remboursées ({{ $stats['nb_rembourse'] }})</p>
            <h3>{{ number_format($stats['total_rembourse'], 0, ',', ' ') }} FCFA</h3>
        </div>
    </div>
</div>
@else
<div style="background:#fff8e1; border:1px solid #ffe082; border-radius:8px; padding:10px 16px; margin-bottom:20px; font-size:0.85rem; color:#795548; display:flex; align-items:center; gap:8px;">
    <i class="fas fa-info-circle" style="color:#f9a825;"></i>
    {{ $stats['nb_en_cours'] }} ardoise(s) en cours — {{ $stats['nb_rembourse'] }} remboursée(s)
</div>
@endif

{{-- Onglets --}}
<div class="tabs">
    <a href="{{ route('ardoise.index', ['filtre' => 'en_cours']) }}"
       class="tab {{ $filtre === 'en_cours' ? 'active' : '' }}">
        <i class="fas fa-clock"></i> En cours
    </a>
    <a href="{{ route('ardoise.index', ['filtre' => 'remboursees']) }}"
       class="tab {{ $filtre === 'remboursees' ? 'active' : '' }}">
        <i class="fas fa-check-circle"></i> Remboursées
    </a>
    <a href="{{ route('ardoise.index', ['filtre' => 'toutes']) }}"
       class="tab {{ $filtre === 'toutes' ? 'active' : '' }}">
        <i class="fas fa-list"></i> Toutes
    </a>
</div>

{{-- Table --}}
<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Montant dû</th>
                <th>Date</th>
                @if($filtre === 'remboursees')<th class="hide-mobile">Remboursé le</th>@endif
                <th class="hide-mobile">Notes</th>
                <th class="hide-mobile">Enregistré par</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ardoises as $ardoise)
            <tr>
                <td class="nom-cell"><i class="fas fa-user" style="color:#aaa; margin-right:6px;"></i>{{ $ardoise->nom }}</td>
                <td class="montant {{ $ardoise->rembourse ? 'ok' : '' }}">
                    {{ number_format($ardoise->montant, 0, ',', ' ') }} FCFA
                    @if($ardoise->rembourse)
                        <span class="badge-rembourse" style="margin-left:6px;"><i class="fas fa-check"></i> Remboursé</span>
                    @endif
                </td>
                <td>{{ $ardoise->date_ardoise->format('d/m/Y') }}</td>
                @if($filtre === 'remboursees')
                <td class="hide-mobile">{{ $ardoise->date_remboursement?->format('d/m/Y') ?? '—' }}</td>
                @endif
                <td class="hide-mobile">
                    @if($ardoise->notes)
                        <span class="notes-text" title="{{ $ardoise->notes }}">{{ $ardoise->notes }}</span>
                    @else
                        <span style="color:#ccc;">—</span>
                    @endif
                </td>
                <td class="hide-mobile" style="color:#777; font-size:0.82rem;">{{ $ardoise->user->name }}</td>
                <td>
                    <div class="actions">
                        @if(!$ardoise->rembourse)
                        <form method="POST" action="{{ route('ardoise.rembourser', $ardoise) }}"
                              onsubmit="return confirm('Marquer cette ardoise comme remboursée ?')">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" title="Marquer remboursé">
                                <i class="fas fa-check"></i> Remboursé
                            </button>
                        </form>
                        @endif
                        @if(auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('ardoise.destroy', $ardoise) }}"
                              onsubmit="return confirm('Supprimer cette ardoise ?')">
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
                <td colspan="7">
                    <div class="empty-state">
                        <i class="fas fa-check-double"></i>
                        @if($filtre === 'en_cours')
                            <p>Aucune ardoise en cours. Tout est à jour !</p>
                        @elseif($filtre === 'remboursees')
                            <p>Aucune ardoise remboursée.</p>
                        @else
                            <p>Aucune ardoise enregistrée.</p>
                        @endif
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($ardoises->hasPages())
    <div class="pagination-wrapper">{{ $ardoises->links() }}</div>
    @endif
</div>

@endsection
