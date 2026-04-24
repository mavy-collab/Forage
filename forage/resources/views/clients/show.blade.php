@extends('layouts.app')

@section('title', $client->nom)
@section('page-title', 'Fiche client')

@push('styles')
<style>
    .back-link { display: inline-flex; align-items: center; gap: 6px; color: #1a3a5c; text-decoration: none; font-size: 0.875rem; margin-bottom: 20px; }
    .back-link:hover { text-decoration: underline; }
    .page-grid { display: grid; grid-template-columns: 300px 1fr; gap: 20px; }
    .card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .card h3 { font-size: 0.82rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #888; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid #eee; }

    /* Profil */
    .client-avatar-lg { width: 70px; height: 70px; border-radius: 50%; background: #1a3a5c; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 700; margin: 0 auto 12px; }
    .client-name-lg { text-align: center; font-size: 1.2rem; font-weight: 700; color: #1a3a5c; margin-bottom: 4px; }
    .client-since { text-align: center; font-size: 0.8rem; color: #aaa; margin-bottom: 20px; }

    .info-row { display: flex; align-items: flex-start; gap: 10px; padding: 10px 0; border-bottom: 1px solid #f5f5f5; font-size: 0.875rem; }
    .info-row:last-child { border-bottom: none; }
    .info-row i { color: #1a3a5c; width: 18px; margin-top: 2px; flex-shrink: 0; }
    .info-row .info-label { color: #888; font-size: 0.78rem; }
    .info-row .info-value { font-weight: 600; color: #333; }

    /* Stats */
    .stats-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; }
    .stat-mini { background: #f8f9fa; border-radius: 8px; padding: 14px; text-align: center; }
    .stat-mini p { font-size: 0.75rem; color: #888; margin-bottom: 4px; }
    .stat-mini h4 { font-size: 1.1rem; font-weight: 700; }
    .color-green { color: #2e7d32; }
    .color-red { color: #c62828; }
    .color-blue { color: #1a3a5c; }

    /* Dette alerte */
    .dette-alert { background: #ffebee; border: 1px solid #ef9a9a; border-radius: 8px; padding: 14px 16px; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
    .dette-alert i { color: #c62828; font-size: 1.1rem; }
    .dette-alert .amount { font-size: 1.1rem; font-weight: 700; color: #c62828; }
    .dette-alert small { color: #888; font-size: 0.8rem; }

    .btn { padding: 9px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 7px; transition: all 0.2s; width: 100%; justify-content: center; margin-bottom: 8px; }
    .btn-primary { background: #1a3a5c; color: #fff; }
    .btn-primary:hover { background: #0d2d4a; }
    .btn-outline { background: transparent; border: 1.5px solid #ccc; color: #555; }
    .btn-outline:hover { border-color: #1a3a5c; color: #1a3a5c; }
    .btn-danger { background: #ffebee; color: #c62828; border: 1px solid #ef9a9a; }
    .btn-danger:hover { background: #e53935; color: #fff; }
    .btn-success { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
    .btn-success:hover { background: #2e7d32; color: #fff; }

    /* Historique table */
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #f8f9fa; padding: 10px 14px; text-align: left; font-size: 0.77rem; font-weight: 700; color: #555; text-transform: uppercase; border-bottom: 1px solid #eee; }
    tbody td { padding: 12px 14px; font-size: 0.875rem; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #fafafa; }

    .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 20px; font-size: 0.73rem; font-weight: 600; }
    .badge-livraison { background: #e8f5e9; color: #2e7d32; }
    .badge-exterieure { background: #fff3e0; color: #e65100; }
    .badge-paye { background: #e8f5e9; color: #2e7d32; }
    .badge-non-paye { background: #ffebee; color: #c62828; }
    .montant { font-weight: 700; color: #1a3a5c; }
    .empty-state { text-align: center; padding: 40px; color: #aaa; }
    .empty-state i { font-size: 2.5rem; display: block; margin-bottom: 10px; color: #ddd; }
    .pagination-wrapper { padding: 14px; border-top: 1px solid #eee; }

    @media(max-width: 900px) { .page-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<a href="{{ route('clients.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Retour aux clients
</a>

<div class="page-grid">
    {{-- Colonne gauche : profil --}}
    <div>
        <div class="card" style="margin-bottom: 16px;">
            <div class="client-avatar-lg">{{ strtoupper(substr($client->nom, 0, 1)) }}</div>
            <div class="client-name-lg">{{ $client->nom }}</div>
            <div class="client-since">Client depuis le {{ $client->created_at->format('d/m/Y') }}</div>

            @if($client->contact)
            <div class="info-row">
                <i class="fas fa-phone"></i>
                <div>
                    <div class="info-label">Contact</div>
                    <div class="info-value">{{ $client->contact }}</div>
                </div>
            </div>
            @endif

            @if($client->adresse)
            <div class="info-row">
                <i class="fas fa-map-marker-alt"></i>
                <div>
                    <div class="info-label">Adresse</div>
                    <div class="info-value">{{ $client->adresse }}</div>
                </div>
            </div>
            @endif

            @if($stats['derniere_vente'])
            <div class="info-row">
                <i class="fas fa-calendar-check"></i>
                <div>
                    <div class="info-label">Dernière vente</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($stats['derniere_vente'])->format('d/m/Y') }}</div>
                </div>
            </div>
            @endif
        </div>

        <div class="card" style="margin-bottom: 16px;">
            <h3>Résumé financier</h3>

            @if($stats['total_dette'] > 0)
            <div class="dette-alert">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <div class="amount">{{ number_format($stats['total_dette'], 0, ',', ' ') }} FCFA</div>
                    <small>de crédit en attente</small>
                </div>
            </div>
            @endif

            <div class="stats-row">
                <div class="stat-mini">
                    <p>Ventes totales</p>
                    <h4 class="color-blue">{{ $stats['total_ventes'] }}</h4>
                </div>
                <div class="stat-mini">
                    <p>Montant payé</p>
                    <h4 class="color-green">{{ number_format($stats['total_paye'], 0, ',', ' ') }}</h4>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>Actions</h3>
            <a href="{{ route('clients.edit', $client) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier le client
            </a>
            <a href="{{ route('ventes.create', ['client_id' => $client->id]) }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Nouvelle vente
            </a>
            @if(auth()->user()->isAdmin())
            <form method="POST" action="{{ route('clients.destroy', $client) }}"
                  onsubmit="return confirm('Supprimer ce client ?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Colonne droite : historique --}}
    <div class="card">
        <h3>Historique des ventes</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>

                    <th>Montant</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventes as $vente)
                <tr>
                    <td>{{ $vente->date_vente->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge badge-{{ $vente->type_vente }}">
                            @if($vente->type_vente === 'livraison') <x-icon-tuyau /> Livraison
                            @else <i class="fas fa-box"></i> Extérieure
                            @endif
                        </span>
                    </td>
                    <td class="montant">{{ number_format($vente->montant, 0, ',', ' ') }} FCFA</td>
                    <td>
                        <span class="badge {{ $vente->statut_paiement === 'paye' ? 'badge-paye' : 'badge-non-paye' }}">
                            @if($vente->statut_paiement === 'paye')
                                <i class="fas fa-check"></i> Payé
                            @else
                                <i class="fas fa-clock"></i> Crédit
                            @endif
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('ventes.show', $vente) }}" style="color:#1a3a5c; font-size:0.82rem;">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>Aucune vente enregistrée.</p>
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
</div>
@endsection
