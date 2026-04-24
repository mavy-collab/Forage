@extends('layouts.app')

@section('title', 'Vente #' . $vente->id)
@section('page-title', 'Détail de la vente')

@push('styles')
<style>
    .back-link { display: inline-flex; align-items: center; gap: 6px; color: #1a3a5c; text-decoration: none; font-size: 0.875rem; margin-bottom: 20px; }
    .back-link:hover { text-decoration: underline; }
    .detail-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
    .card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .card h3 { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #888; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
    .detail-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f5f5f5; font-size: 0.9rem; }
    .detail-row:last-child { border-bottom: none; }
    .detail-row .label { color: #888; }
    .detail-row .value { font-weight: 600; color: #333; }
    .montant-big { font-size: 2rem; font-weight: 800; color: #1a3a5c; margin: 8px 0; }
    .badge { display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .badge-forage { background: #e3f2fd; color: #1565c0; }
    .badge-livraison { background: #e8f5e9; color: #2e7d32; }
    .badge-exterieure { background: #fff3e0; color: #e65100; }
    .badge-paye { background: #e8f5e9; color: #2e7d32; }
    .badge-non-paye { background: #ffebee; color: #c62828; }
    .actions { display: flex; gap: 10px; margin-top: 20px; flex-wrap: wrap; }
    .btn { padding: 10px 18px; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
    .btn-primary { background: #1a3a5c; color: #fff; }
    .btn-primary:hover { background: #0d2d4a; }
    .btn-outline { background: transparent; border: 1.5px solid #ccc; color: #555; }
    .btn-outline:hover { border-color: #1a3a5c; color: #1a3a5c; }
    .btn-danger { background: #e53935; color: #fff; }
    .btn-danger:hover { background: #b71c1c; }
    .notes-box { background: #f8f9fa; border-radius: 8px; padding: 12px; font-size: 0.875rem; color: #555; margin-top: 8px; }
</style>
@endpush

@section('content')
<a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('ventes.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Retour
</a>

<div class="detail-grid">
    <div>
        <div class="card" style="margin-bottom: 20px;">
            <h3>Informations de la vente</h3>
            <div class="detail-row">
                <span class="label">Référence</span>
                <span class="value">#{{ $vente->id }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Type</span>
                <span class="badge badge-{{ $vente->type_vente }}">
                    @if($vente->type_vente === 'forage') <i class="fas fa-faucet"></i> Forage
                    @elseif($vente->type_vente === 'livraison') <x-icon-tuyau /> Livraison
                    @else <i class="fas fa-box"></i> Extérieure
                    @endif
                </span>
            </div>
            <div class="detail-row">
                <span class="label">Date</span>
                <span class="value">{{ $vente->date_vente->format('d/m/Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Client</span>
                <span class="value">{{ $vente->client?->nom ?? '—' }}</span>
            </div>
            @if($vente->type_vente !== 'forage')
            <div class="detail-row">
                <span class="label">Statut paiement</span>
                <span class="badge {{ $vente->statut_paiement === 'paye' ? 'badge-paye' : 'badge-non-paye' }}">
                    @if($vente->statut_paiement === 'paye')
                        <i class="fas fa-check-circle"></i> Payé
                    @else
                        <i class="fas fa-clock"></i> Crédit (non payé)
                    @endif
                </span>
            </div>
            @endif
            <div class="detail-row">
                <span class="label">Enregistré par</span>
                <span class="value">{{ $vente->user->name }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Date enregistrement</span>
                <span class="value">{{ $vente->created_at->format('d/m/Y H:i') }}</span>
            </div>
            @if($vente->notes)
            <div style="margin-top: 12px;">
                <span class="label" style="font-size:0.82rem;">Notes</span>
                <div class="notes-box">{{ $vente->notes }}</div>
            </div>
            @endif
        </div>

        <div class="actions">
            @if(auth()->user()->isAdmin())
            <form method="POST" action="{{ route('ventes.destroy', $vente) }}"
                  onsubmit="return confirm('Supprimer définitivement cette vente ?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </form>
            @endif
            <a href="{{ route('ventes.index') }}" class="btn btn-outline">
                <i class="fas fa-list"></i> Toutes les ventes
            </a>
        </div>
    </div>

    <div>
        <div class="card">
            <h3>Montant</h3>
            <div class="montant-big">{{ number_format($vente->montant, 0, ',', ' ') }}</div>
            <div style="color:#888; font-size:0.85rem;">FCFA</div>
        </div>

        @if($vente->client)
        <div class="card" style="margin-top: 20px;">
            <h3>Client</h3>
            <div class="detail-row">
                <span class="label">Nom</span>
                <span class="value">{{ $vente->client->nom }}</span>
            </div>
            @if($vente->client->adresse)
            <div class="detail-row">
                <span class="label">Adresse</span>
                <span class="value">{{ $vente->client->adresse }}</span>
            </div>
            @endif
            @if($vente->client->contact)
            <div class="detail-row">
                <span class="label">Contact</span>
                <span class="value">{{ $vente->client->contact }}</span>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
