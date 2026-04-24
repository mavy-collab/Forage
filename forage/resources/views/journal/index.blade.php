@extends('layouts.app')

@section('title', 'Journal d\'activités')
@section('page-title', 'Journal d\'activités')

@push('styles')
<style>
    .btn { padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 7px; transition: all 0.2s; }
    .btn-sm { padding: 6px 12px; font-size: 0.8rem; }
    .btn-primary { background: #1a3a5c; color: #fff; }
    .btn-primary:hover { background: #0d2d4a; }
    .btn-outline { background: transparent; border: 1.5px solid #1a3a5c; color: #1a3a5c; }
    .btn-outline:hover { background: #1a3a5c; color: #fff; }

    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: #fff; border-radius: 10px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; align-items: center; gap: 14px; }
    .stat-icon { width: 46px; height: 46px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
    .icon-blue   { background: #e3f2fd; color: #1a3a5c; }
    .icon-green  { background: #e8f5e9; color: #2e7d32; }
    .icon-purple { background: #f3e5f5; color: #6a1b9a; }
    .stat-card p { font-size: 0.78rem; color: #888; margin-bottom: 3px; }
    .stat-card h3 { font-size: 1.2rem; font-weight: 700; color: #1a3a5c; }

    /* Filtres */
    .filters { background: #fff; border-radius: 10px; padding: 16px 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .filters form { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; }
    .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .filter-group label { font-size: 0.78rem; font-weight: 600; color: #555; }
    .filter-group input, .filter-group select { padding: 8px 12px; border: 1.5px solid #e0e0e0; border-radius: 7px; font-size: 0.85rem; outline: none; background: #fff; }
    .filter-group input:focus, .filter-group select:focus { border-color: #1a3a5c; }
    .filter-group input[name="search"] { min-width: 200px; }

    /* Filtres rapides par type */
    .type-filters { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
    .type-pill { padding: 5px 14px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; cursor: pointer; border: 1.5px solid transparent; text-decoration: none; transition: all 0.2s; display: inline-flex; align-items: center; gap: 5px; }
    .type-pill:hover { opacity: 0.8; }
    .type-pill.active { border-color: currentColor; }

    /* Timeline */
    .timeline-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    .timeline-header { padding: 16px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
    .timeline-header span { font-size: 0.85rem; color: #888; }

    .date-group { padding: 0 20px; }
    .date-separator { padding: 14px 0 8px; display: flex; align-items: center; gap: 12px; }
    .date-separator .date-label { font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #888; white-space: nowrap; }
    .date-separator::after { content: ''; flex: 1; height: 1px; background: #eee; }

    .activity-item { display: flex; gap: 14px; padding: 12px 0; border-bottom: 1px solid #f8f8f8; align-items: flex-start; }
    .activity-item:last-child { border-bottom: none; }

    .activity-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; flex-shrink: 0; margin-top: 2px; }

    .activity-body { flex: 1; min-width: 0; }
    .activity-desc { font-size: 0.875rem; color: #333; font-weight: 500; line-height: 1.4; }
    .activity-meta { display: flex; align-items: center; gap: 12px; margin-top: 4px; flex-wrap: wrap; }
    .activity-time { font-size: 0.78rem; color: #aaa; display: flex; align-items: center; gap: 4px; }
    .activity-user { font-size: 0.78rem; color: #888; background: #f5f5f5; padding: 2px 8px; border-radius: 12px; display: flex; align-items: center; gap: 4px; }
    .activity-type-badge { font-size: 0.72rem; font-weight: 700; padding: 2px 8px; border-radius: 12px; }

    .activity-details { margin-top: 6px; background: #f8f9fa; border-radius: 6px; padding: 8px 10px; font-size: 0.78rem; color: #666; font-family: monospace; }

    .empty-state { text-align: center; padding: 60px 20px; color: #aaa; }
    .empty-state i { font-size: 3rem; display: block; margin-bottom: 12px; color: #ddd; }
    .pagination-wrapper { padding: 16px 20px; border-top: 1px solid #eee; }
</style>
@endpush

@section('content')

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-calendar-day"></i></div>
        <div>
            <p>Actions aujourd'hui</p>
            <h3>{{ $stats['aujourd_hui'] }}</h3>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-calendar-alt"></i></div>
        <div>
            <p>Actions ce mois</p>
            <h3>{{ $stats['ce_mois'] }}</h3>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-purple"><i class="fas fa-history"></i></div>
        <div>
            <p>Total enregistrements</p>
            <h3>{{ number_format($stats['total'], 0, ',', ' ') }}</h3>
        </div>
    </div>
</div>

{{-- Filtres rapides par type --}}
<div class="type-filters">
    <a href="{{ route('journal.index', array_merge(request()->except('type', 'page'), [])) }}"
       class="type-pill {{ !request('type') ? 'active' : '' }}"
       style="background:#f5f5f5; color:#555;">
        <i class="fas fa-list"></i> Tous
    </a>
    @foreach($types as $key => $type)
    <a href="{{ route('journal.index', array_merge(request()->except('type', 'page'), ['type' => $key])) }}"
       class="type-pill {{ request('type') === $key ? 'active' : '' }}"
       style="background:{{ $type['bg'] }}; color:{{ $type['color'] }};">
        <i class="fas {{ $type['icon'] }}"></i> {{ $type['label'] }}
    </a>
    @endforeach
</div>

{{-- Filtres avancés --}}
<div class="filters">
    <form method="GET" action="{{ route('journal.index') }}">
        @if(request('type'))
            <input type="hidden" name="type" value="{{ request('type') }}">
        @endif
        <div class="filter-group">
            <label>Rechercher</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher dans les descriptions...">
        </div>
        <div class="filter-group">
            <label>Utilisateur</label>
            <select name="user_id">
                <option value="">Tous</option>
                @foreach($utilisateurs as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
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
        <a href="{{ route('journal.index') }}" class="btn btn-outline btn-sm"><i class="fas fa-times"></i> Réinitialiser</a>
    </form>
</div>

{{-- Timeline --}}
<div class="timeline-card">
    <div class="timeline-header">
        <strong style="color:#1a3a5c;"><i class="fas fa-stream"></i> Activités</strong>
        <span>{{ $activites->total() }} entrée(s) trouvée(s)</span>
    </div>

    @forelse($activites->groupBy(fn($a) => $a->created_at->toDateString()) as $date => $groupe)
    <div class="date-group">
        <div class="date-separator">
            <span class="date-label">
                @php
                    $d = \Carbon\Carbon::parse($date);
                    if ($d->isToday()) echo "Aujourd'hui — " . $d->format('d/m/Y');
                    elseif ($d->isYesterday()) echo "Hier — " . $d->format('d/m/Y');
                    else echo $d->translatedFormat('l d F Y');
                @endphp
            </span>
        </div>

        @foreach($groupe as $activite)
        @php $type = $types[$activite->type_action] ?? $types['autre']; @endphp
        <div class="activity-item">
            <div class="activity-icon" style="background:{{ $type['bg'] }}; color:{{ $type['color'] }};">
                <i class="fas {{ $type['icon'] }}"></i>
            </div>
            <div class="activity-body">
                <div class="activity-desc">{{ $activite->description }}</div>
                <div class="activity-meta">
                    <span class="activity-time">
                        <i class="fas fa-clock"></i>
                        {{ $activite->created_at->format('H:i:s') }}
                    </span>
                    <span class="activity-user">
                        <i class="fas fa-user"></i>
                        {{ $activite->user?->name ?? 'Système' }}
                    </span>
                    <span class="activity-type-badge"
                          style="background:{{ $type['bg'] }}; color:{{ $type['color'] }};">
                        {{ $type['label'] }}
                    </span>
                </div>
                @if($activite->donnees)
                <div class="activity-details">
                    @foreach($activite->donnees as $k => $v)
                        <span style="color:#888;">{{ $k }}</span>: <span>{{ is_array($v) ? json_encode($v) : $v }}</span>
                        @if(!$loop->last) &nbsp;·&nbsp; @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-history"></i>
        <p>Aucune activité trouvée.</p>
    </div>
    @endforelse

    @if($activites->hasPages())
    <div class="pagination-wrapper">{{ $activites->links() }}</div>
    @endif
</div>
@endsection
