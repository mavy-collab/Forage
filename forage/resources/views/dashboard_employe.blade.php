@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@push('styles')
<style>
    .emp-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px; margin-bottom: 28px;
    }
    .emp-card {
        background: #fff; border-radius: 12px; padding: 22px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        display: flex; align-items: center; gap: 16px;
    }
    .emp-icon {
        width: 52px; height: 52px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; flex-shrink: 0;
    }
    .emp-icon.blue   { background: #e3f2fd; color: #1a3a5c; }
    .emp-icon.orange { background: #fff3e0; color: #e65100; }
    .emp-info p  { font-size: 0.8rem; color: #888; margin-bottom: 4px; }
    .emp-info h2 { font-size: 1.8rem; font-weight: 700; color: #1a3a5c; }

    .quick-actions {
        background: #fff; border-radius: 12px; padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 28px;
    }
    .quick-actions h3 {
        font-size: 0.95rem; font-weight: 700; color: #1a3a5c;
        margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid #eee;
        display: flex; align-items: center; gap: 8px;
    }
    .actions-row { display: flex; gap: 12px; flex-wrap: wrap; }
    .action-btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 12px 20px; border-radius: 10px; font-size: 0.9rem;
        font-weight: 600; text-decoration: none; transition: all 0.2s;
    }
    .action-btn.primary { background: #1a3a5c; color: #fff; }
    .action-btn.primary:hover { background: #0d2d4a; }
    .action-btn.secondary { background: #e8f5e9; color: #2e7d32; }
    .action-btn.secondary:hover { background: #c8e6c9; }
    .action-btn.tertiary { background: #fff3e0; color: #e65100; }
    .action-btn.tertiary:hover { background: #ffe0b2; }
    .action-btn.credit { background: #fce4ec; color: #c62828; }
    .action-btn.credit:hover { background: #f8bbd0; }

    .recent-card {
        background: #fff; border-radius: 12px; padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .recent-card h3 {
        font-size: 0.95rem; font-weight: 700; color: #1a3a5c;
        margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid #eee;
        display: flex; align-items: center; gap: 8px;
    }
    .recent-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
    .recent-table th { text-align: left; padding: 8px 12px; color: #888; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; }
    .recent-table td { padding: 10px 12px; border-top: 1px solid #f0f0f0; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .badge-forage    { background: #e3f2fd; color: #1a3a5c; }
    .badge-livraison { background: #e8f5e9; color: #2e7d32; }
    .badge-exterieure{ background: #fff3e0; color: #e65100; }
    .badge-paye      { background: #e8f5e9; color: #2e7d32; }
    .badge-credit    { background: #fff3e0; color: #e65100; }
    .empty-state { text-align: center; padding: 32px; color: #aaa; }
</style>
@endpush

@section('content')

<div class="emp-grid">
    <div class="emp-card">
        <div class="emp-icon blue"><i class="fas fa-calendar-day"></i></div>
        <div class="emp-info">
            <p>Ventes enregistrées aujourd'hui</p>
            <h2>{{ $nbVentesJour }}</h2>
        </div>
    </div>
    <div class="emp-card">
        <div class="emp-icon orange"><i class="fas fa-clock"></i></div>
        <div class="emp-info">
            <p>Crédits en attente</p>
            <h2>{{ $nbCredits }}</h2>
        </div>
    </div>
</div>

<div class="quick-actions">
    <h3><i class="fas fa-bolt"></i> Actions rapides</h3>
    <div class="actions-row">
        <a href="{{ route('ventes.create') }}" class="action-btn primary">
            <i class="fas fa-plus"></i> Nouvelle vente
        </a>
        <a href="{{ route('ventes.create', ['type' => 'forage']) }}" class="action-btn secondary">
            <i class="fas fa-faucet"></i> Vente forage
        </a>
        <a href="{{ route('ventes.create', ['type' => 'livraison']) }}" class="action-btn tertiary">
            <i class="fas fa-truck"></i> Livraison
        </a>
        <a href="{{ route('depenses.create') }}" class="action-btn credit">
            <i class="fas fa-wallet"></i> Dépense
        </a>
        <a href="{{ route('credits.index') }}" class="action-btn credit">
            <i class="fas fa-credit-card"></i> Voir crédits
        </a>
    </div>
</div>

<div class="recent-card">
    <h3><i class="fas fa-history"></i> Dernières ventes enregistrées</h3>
    @if($ventesRecentes->isEmpty())
        <div class="empty-state"><i class="fas fa-inbox fa-2x" style="margin-bottom:8px;"></i><br>Aucune vente enregistrée.</div>
    @else
        <table class="recent-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Client</th>
                    <th>Enregistré par</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventesRecentes as $vente)
                <tr>
                    <td>{{ $vente->date_vente->format('d/m/Y') }}</td>
                    <td><span class="badge badge-{{ $vente->type_vente }}">{{ ucfirst($vente->type_vente) }}</span></td>
                    <td>{{ $vente->client?->nom ?? '—' }}</td>
                    <td>{{ $vente->user?->name ?? '—' }}</td>
                    <td>
                        @if($vente->statut_paiement === 'paye')
                            <span class="badge badge-paye">Payé</span>
                        @else
                            <span class="badge badge-credit">Crédit</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('ventes.show', $vente) }}" style="color:#1a3a5c; font-size:0.8rem;">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:14px;">
            <a href="{{ route('ventes.index') }}" style="font-size:0.85rem; color:#1a3a5c; text-decoration:none; font-weight:600;">
                Voir toutes les ventes <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    @endif
</div>
@endsection
