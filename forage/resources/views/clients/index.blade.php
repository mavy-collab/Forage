@extends('layouts.app')

@section('title', 'Clients')
@section('page-title', 'Gestion des clients')

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

    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: #fff; border-radius: 10px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; align-items: center; gap: 14px; }
    .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
    .icon-blue { background: #e3f2fd; color: #1a3a5c; }
    .icon-red { background: #ffebee; color: #c62828; }
    .icon-orange { background: #fff3e0; color: #e65100; }
    .stat-card p { font-size: 0.78rem; color: #888; margin-bottom: 2px; }
    .stat-card h3 { font-size: 1.1rem; font-weight: 700; color: #1a3a5c; }

    .filters { background: #fff; border-radius: 10px; padding: 14px 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .filters form { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; }
    .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .filter-group label { font-size: 0.78rem; font-weight: 600; color: #555; }
    .filter-group input, .filter-group select { padding: 8px 12px; border: 1.5px solid #e0e0e0; border-radius: 7px; font-size: 0.875rem; outline: none; background: #fff; }
    .filter-group input:focus, .filter-group select:focus { border-color: #1a3a5c; }

    .table-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #f8f9fa; padding: 12px 16px; text-align: left; font-size: 0.78rem; font-weight: 700; color: #555; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #eee; }
    tbody td { padding: 13px 16px; font-size: 0.875rem; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #fafafa; }

    .client-avatar { width: 38px; height: 38px; border-radius: 50%; background: #1a3a5c; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; flex-shrink: 0; }
    .client-info { display: flex; align-items: center; gap: 12px; }
    .client-name { font-weight: 600; color: #1a3a5c; }
    .client-contact { font-size: 0.8rem; color: #888; }

    .badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .badge-dette { background: #ffebee; color: #c62828; }
    .badge-ok { background: #e8f5e9; color: #2e7d32; }

    .montant { font-weight: 700; }
    .montant-dette { color: #c62828; }
    .montant-paye { color: #2e7d32; }

    .actions { display: flex; gap: 6px; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
    .empty-state i { font-size: 3rem; margin-bottom: 12px; color: #ddd; display: block; }
    .pagination-wrapper { padding: 16px 20px; border-top: 1px solid #eee; }
</style>
@endpush

@section('content')
<div class="page-actions">
    <div></div>
    <a href="{{ route('clients.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Nouveau client
    </a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
        <div>
            <p>Total clients</p>
            <h3>{{ $stats['total'] }}</h3>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-red"><i class="fas fa-exclamation-triangle"></i></div>
        <div>
            <p>Clients avec dettes</p>
            <h3>{{ $stats['avec_dette'] }}</h3>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-orange"><i class="fas fa-credit-card"></i></div>
        <div>
            <p>Total dettes clients</p>
            <h3>{{ number_format($stats['total_dette'], 0, ',', ' ') }} FCFA</h3>
        </div>
    </div>
</div>

<div class="filters">
    <form method="GET" action="{{ route('clients.index') }}">
        <div class="filter-group">
            <label>Rechercher</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, contact, adresse...">
        </div>
        <div class="filter-group">
            <label>Filtre</label>
            <select name="filtre">
                <option value="">Tous les clients</option>
                <option value="avec_dette" {{ request('filtre') === 'avec_dette' ? 'selected' : '' }}>Avec dettes uniquement</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Rechercher</button>
        <a href="{{ route('clients.index') }}" class="btn btn-outline btn-sm"><i class="fas fa-times"></i> Réinitialiser</a>
    </form>
</div>

<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Adresse</th>
                <th>Nb ventes</th>
                <th>Total payé</th>
                <th>Dette</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
            <tr>
                <td>
                    <div class="client-info">
                        <div class="client-avatar">{{ strtoupper(substr($client->nom, 0, 1)) }}</div>
                        <div>
                            <div class="client-name">{{ $client->nom }}</div>
                            @if($client->contact)
                            <div class="client-contact"><i class="fas fa-phone"></i> {{ $client->contact }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td style="color:#666;">{{ $client->adresse ?? '—' }}</td>
                <td style="text-align:center; font-weight:600;">{{ $client->ventes_count }}</td>
                <td class="montant montant-paye">{{ number_format($client->total_achats ?? 0, 0, ',', ' ') }} FCFA</td>
                <td class="montant {{ ($client->total_dette ?? 0) > 0 ? 'montant-dette' : '' }}">
                    {{ number_format($client->total_dette ?? 0, 0, ',', ' ') }} FCFA
                </td>
                <td>
                    @if(($client->total_dette ?? 0) > 0)
                        <span class="badge badge-dette"><i class="fas fa-exclamation-circle"></i> Dette</span>
                    @else
                        <span class="badge badge-ok"><i class="fas fa-check-circle"></i> À jour</span>
                    @endif
                </td>
                <td>
                    <div class="actions">
                        <a href="{{ route('clients.show', $client) }}" class="btn btn-outline btn-sm" title="Voir">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline btn-sm" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if(auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('clients.destroy', $client) }}"
                              onsubmit="return confirm('Supprimer ce client ? Ses ventes ne seront pas supprimées.')">
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
                        <i class="fas fa-users"></i>
                        <p>Aucun client trouvé.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($clients->hasPages())
    <div class="pagination-wrapper">{{ $clients->links() }}</div>
    @endif
</div>
@endsection
