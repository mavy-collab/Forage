@extends('layouts.app')

@section('title', 'Crédits')
@section('page-title', 'Gestion des crédits')

@push('styles')
<style>
    .btn { padding: 10px 18px; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
    .btn-sm { padding: 6px 12px; font-size: 0.8rem; }
    .btn-primary { background: #1a3a5c; color: #fff; }
    .btn-primary:hover { background: #0d2d4a; }
    .btn-success { background: #2e7d32; color: #fff; }
    .btn-success:hover { background: #1b5e20; }
    .btn-outline { background: transparent; border: 1.5px solid #1a3a5c; color: #1a3a5c; }
    .btn-outline:hover { background: #1a3a5c; color: #fff; }

    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: #fff; border-radius: 10px; padding: 18px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; align-items: center; gap: 14px; }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
    .icon-red { background: #ffebee; color: #c62828; }
    .icon-orange { background: #fff3e0; color: #e65100; }
    .icon-blue { background: #e3f2fd; color: #1a3a5c; }
    .stat-card p { font-size: 0.78rem; color: #888; margin-bottom: 3px; }
    .stat-card h3 { font-size: 1.2rem; font-weight: 700; color: #1a3a5c; }

    .filters { background: #fff; border-radius: 10px; padding: 14px 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .filters form { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; }
    .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .filter-group label { font-size: 0.78rem; font-weight: 600; color: #555; }
    .filter-group select, .filter-group input { padding: 8px 12px; border: 1.5px solid #e0e0e0; border-radius: 7px; font-size: 0.875rem; outline: none; background: #fff; }
    .filter-group select:focus, .filter-group input:focus { border-color: #1a3a5c; }

    .table-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #fff8e1; padding: 12px 16px; text-align: left; font-size: 0.78rem; font-weight: 700; color: #e65100; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #ffe082; }
    tbody td { padding: 14px 16px; font-size: 0.875rem; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #fffde7; }

    .client-cell { display: flex; align-items: center; gap: 10px; }
    .avatar { width: 34px; height: 34px; border-radius: 50%; background: #e65100; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700; flex-shrink: 0; }
    .client-name { font-weight: 600; color: #1a3a5c; }

    .badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .badge-livraison { background: #e8f5e9; color: #2e7d32; }
    .badge-exterieure { background: #fff3e0; color: #e65100; }
    .badge-forage { background: #e3f2fd; color: #1565c0; }

    .montant-dette { font-weight: 700; color: #c62828; font-size: 1rem; }
    .montant-reste { font-weight: 700; color: #e65100; }
    .montant-paye-partiel { font-size: 0.8rem; color: #888; }

    .progress-bar { height: 6px; background: #ffcdd2; border-radius: 3px; margin-top: 5px; overflow: hidden; }
    .progress-fill { height: 100%; background: #4caf50; border-radius: 3px; transition: width 0.3s; }

    .jours-badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 0.73rem; font-weight: 700; }
    .jours-urgent { background: #ffebee; color: #c62828; }
    .jours-warning { background: #fff3e0; color: #e65100; }
    .jours-ok { background: #f3e5f5; color: #6a1b9a; }

    .actions { display: flex; gap: 6px; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
    .empty-state i { font-size: 3rem; display: block; margin-bottom: 12px; color: #ddd; }
    .pagination-wrapper { padding: 16px 20px; border-top: 1px solid #eee; }

    /* Modal paiement rapide */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 200; align-items: center; justify-content: center; }
    .modal-overlay.open { display: flex; }
    .modal { background: #fff; border-radius: 14px; padding: 28px; width: 100%; max-width: 440px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
    .modal h3 { font-size: 1.1rem; color: #1a3a5c; margin-bottom: 6px; }
    .modal .subtitle { font-size: 0.85rem; color: #888; margin-bottom: 20px; }
    .modal .form-group { margin-bottom: 16px; }
    .modal .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #444; margin-bottom: 6px; }
    .modal .form-group input { width: 100%; padding: 10px 14px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 0.9rem; outline: none; }
    .modal .form-group input:focus { border-color: #1a3a5c; }
    .modal-actions { display: flex; gap: 10px; margin-top: 20px; }
    .modal-actions .btn { flex: 1; justify-content: center; }
    .btn-cancel { background: #f5f5f5; color: #555; border: none; }
    .btn-cancel:hover { background: #e0e0e0; }
    .reste-info { background: #fff8e1; border-radius: 8px; padding: 10px 14px; font-size: 0.85rem; color: #e65100; margin-bottom: 16px; display: flex; justify-content: space-between; }
    .reste-info strong { font-size: 1rem; }
</style>
@endpush

@section('content')

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon icon-red"><i class="fas fa-exclamation-triangle"></i></div>
        <div>
            <p>Crédits en cours</p>
            <h3>{{ $stats['total_credits'] }}</h3>
        </div>
    </div>
    @if(auth()->user()->isAdmin())
    <div class="stat-card">
        <div class="stat-icon icon-orange"><i class="fas fa-money-bill-wave"></i></div>
        <div>
            <p>Montant total impayé</p>
            <h3>{{ number_format($stats['total_montant'], 0, ',', ' ') }} FCFA</h3>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
        <div>
            <p>Clients endettés</p>
            <h3>{{ $stats['clients_endettes'] }}</h3>
        </div>
    </div>
    @endif
</div>

<div class="filters">
    <form method="GET" action="{{ route('credits.index') }}">
        <div class="filter-group">
            <label>Client</label>
            <select name="client_id">
                <option value="">Tous les clients</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->nom }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label>Type de vente</label>
            <select name="type">
                <option value="">Tous</option>
                <option value="forage" {{ request('type') === 'forage' ? 'selected' : '' }}>Forage</option>
                <option value="livraison" {{ request('type') === 'livraison' ? 'selected' : '' }}>Livraison</option>
                <option value="exterieure" {{ request('type') === 'exterieure' ? 'selected' : '' }}>Extérieure</option>
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
        <a href="{{ route('credits.index') }}" class="btn btn-outline btn-sm"><i class="fas fa-times"></i> Réinitialiser</a>
    </form>
</div>

<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Type</th>
                <th>Date</th>
                <th>Ancienneté</th>
                <th>Montant</th>
                <th>Payé</th>
                <th>Reste à payer</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($credits as $credit)
            @php
                $dejaPaye = $credit->paiements->sum('montant');
                $reste = $credit->montant - $dejaPaye;
                $pct = $credit->montant > 0 ? round($dejaPaye / $credit->montant * 100) : 0;
                $jours = $credit->date_vente->diffInDays(today());
            @endphp
            <tr>
                <td>
                    <div class="client-cell">
                        <div class="avatar">{{ strtoupper(substr($credit->client?->nom ?? 'A', 0, 1)) }}</div>
                        <div>
                            <div class="client-name">{{ $credit->client?->nom ?? 'Client anonyme' }}</div>
                            <div style="font-size:0.78rem; color:#aaa;">{{ $credit->user->name }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge badge-{{ $credit->type_vente }}">
                        @if($credit->type_vente === 'forage') <i class="fas fa-faucet"></i> Forage
                        @elseif($credit->type_vente === 'livraison') <x-icon-tuyau /> Livraison
                        @else <i class="fas fa-box"></i> Extérieure
                        @endif
                    </span>
                </td>
                <td>{{ $credit->date_vente->format('d/m/Y') }}</td>
                <td>
                    <span class="jours-badge {{ $jours > 30 ? 'jours-urgent' : ($jours > 15 ? 'jours-warning' : 'jours-ok') }}">
                        {{ $jours }}j
                    </span>
                </td>
                <td class="montant-dette">{{ number_format($credit->montant, 0, ',', ' ') }} FCFA</td>
                <td>
                    @if($dejaPaye > 0)
                        <div style="font-size:0.85rem; color:#2e7d32; font-weight:600;">{{ number_format($dejaPaye, 0, ',', ' ') }}</div>
                        <div class="progress-bar"><div class="progress-fill" style="width:{{ $pct }}%"></div></div>
                        <div style="font-size:0.72rem; color:#aaa;">{{ $pct }}%</div>
                    @else
                        <span style="color:#ccc; font-size:0.82rem;">—</span>
                    @endif
                </td>
                <td class="montant-reste">{{ number_format($reste, 0, ',', ' ') }} FCFA</td>
                <td>
                    <div class="actions">
                        <button class="btn btn-success btn-sm"
                            onclick="openPaiement({{ $credit->id }}, '{{ addslashes($credit->client?->nom ?? 'Anonyme') }}', {{ $reste }})">
                            <i class="fas fa-hand-holding-usd"></i> Encaisser
                        </button>
                        <a href="{{ route('credits.show', $credit) }}" class="btn btn-outline btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8">
                    <div class="empty-state">
                        <i class="fas fa-check-circle" style="color:#4caf50;"></i>
                        <p style="color:#4caf50; font-weight:600;">Aucun crédit en cours. Tous les paiements sont à jour !</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($credits->hasPages())
    <div class="pagination-wrapper">{{ $credits->links() }}</div>
    @endif
</div>

{{-- Modal paiement rapide --}}
<div class="modal-overlay" id="modalPaiement">
    <div class="modal">
        <h3><i class="fas fa-hand-holding-usd"></i> Encaisser un paiement</h3>
        <p class="subtitle" id="modalSubtitle"></p>

        <div class="reste-info">
            <span>Reste à payer</span>
            <strong id="modalReste"></strong>
        </div>

        <form method="POST" id="formPaiement">
            @csrf
            <div class="form-group">
                <label>Montant encaissé (FCFA) <span style="color:#e53935">*</span></label>
                <input type="number" name="montant" id="inputMontant" min="1" step="1" placeholder="0" required>
            </div>
            <div class="form-group">
                <label>Date du paiement <span style="color:#e53935">*</span></label>
                <input type="date" name="date_paiement" value="{{ today()->toDateString() }}" required>
            </div>
            <div class="form-group">
                <label>Notes <small style="font-weight:400; color:#aaa;">(optionnel)</small></label>
                <input type="text" name="notes" placeholder="Remarques...">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="closeModal()">Annuler</button>
                <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Valider</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openPaiement(id, client, reste) {
    document.getElementById('modalSubtitle').textContent = 'Client : ' + client;
    document.getElementById('modalReste').textContent = new Intl.NumberFormat('fr').format(reste) + ' FCFA';
    document.getElementById('inputMontant').max = reste;
    document.getElementById('inputMontant').value = reste;
    document.getElementById('formPaiement').action = '{{ url('credits') }}/' + id + '/payer';
    document.getElementById('modalPaiement').classList.add('open');
}

function closeModal() {
    document.getElementById('modalPaiement').classList.remove('open');
}

document.getElementById('modalPaiement').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endpush
