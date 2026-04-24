@extends('layouts.app')

@section('title', 'Crédit #' . $credit->id)
@section('page-title', 'Détail du crédit')

@push('styles')
<style>
    .back-link { display: inline-flex; align-items: center; gap: 6px; color: #1a3a5c; text-decoration: none; font-size: 0.875rem; margin-bottom: 20px; }
    .back-link:hover { text-decoration: underline; }
    .page-grid { display: grid; grid-template-columns: 1fr 360px; gap: 20px; }
    .card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 16px; }
    .card h3 { font-size: 0.82rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #888; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid #eee; }

    .detail-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f5f5f5; font-size: 0.9rem; }
    .detail-row:last-child { border-bottom: none; }
    .detail-row .label { color: #888; }
    .detail-row .value { font-weight: 600; color: #333; }

    .montant-header { text-align: center; padding: 20px; background: linear-gradient(135deg, #c62828, #e53935); border-radius: 10px; color: #fff; margin-bottom: 16px; }
    .montant-header .label { font-size: 0.85rem; opacity: 0.8; margin-bottom: 4px; }
    .montant-header .amount { font-size: 2rem; font-weight: 800; }

    .progress-section { margin: 16px 0; }
    .progress-labels { display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 6px; }
    .progress-bar { height: 10px; background: #ffcdd2; border-radius: 5px; overflow: hidden; }
    .progress-fill { height: 100%; background: linear-gradient(90deg, #4caf50, #2e7d32); border-radius: 5px; transition: width 0.5s; }

    .amount-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f5f5f5; }
    .amount-row:last-child { border-bottom: none; }
    .amount-row .alabel { font-size: 0.85rem; color: #888; }
    .amount-row .aval { font-size: 1rem; font-weight: 700; }
    .color-red { color: #c62828; }
    .color-green { color: #2e7d32; }
    .color-orange { color: #e65100; }

    /* Formulaire paiement */
    .paiement-form { background: #f8fff8; border: 1.5px solid #a5d6a7; border-radius: 10px; padding: 20px; }
    .paiement-form h4 { font-size: 0.9rem; font-weight: 700; color: #2e7d32; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .form-group { margin-bottom: 14px; }
    .form-group label { display: block; font-size: 0.82rem; font-weight: 600; color: #444; margin-bottom: 5px; }
    .form-group input { width: 100%; padding: 10px 12px; border: 1.5px solid #e0e0e0; border-radius: 7px; font-size: 0.9rem; outline: none; }
    .form-group input:focus { border-color: #2e7d32; }
    .btn { padding: 10px 18px; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
    .btn-success { background: #2e7d32; color: #fff; width: 100%; justify-content: center; }
    .btn-success:hover { background: #1b5e20; }
    .btn-outline { background: transparent; border: 1.5px solid #ccc; color: #555; }
    .btn-outline:hover { border-color: #1a3a5c; color: #1a3a5c; }

    .solde-badge { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; border-radius: 8px; padding: 12px 16px; text-align: center; font-weight: 600; font-size: 0.9rem; }

    /* Historique paiements */
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #f8f9fa; padding: 10px 14px; text-align: left; font-size: 0.77rem; font-weight: 700; color: #555; text-transform: uppercase; border-bottom: 1px solid #eee; }
    tbody td { padding: 12px 14px; font-size: 0.875rem; border-bottom: 1px solid #f5f5f5; }
    tbody tr:last-child td { border-bottom: none; }
    .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 20px; font-size: 0.73rem; font-weight: 600; }
    .badge-partiel { background: #fff3e0; color: #e65100; }
    .badge-solde { background: #e8f5e9; color: #2e7d32; }
    .empty-mini { text-align: center; padding: 24px; color: #aaa; font-size: 0.85rem; }
</style>
@endpush

@section('content')
<a href="{{ route('credits.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Retour aux crédits
</a>

<div class="page-grid">
    {{-- Colonne gauche --}}
    <div>
        <div class="card">
            <h3>Informations de la vente</h3>
            <div class="detail-row">
                <span class="label">Référence</span>
                <span class="value">#{{ $credit->id }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Client</span>
                <span class="value">{{ $credit->client?->nom ?? '—' }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Type de vente</span>
                <span class="value">
                    @if($credit->type_vente === 'livraison') <x-icon-tuyau /> Livraison
                    @elseif($credit->type_vente === 'exterieure') <i class="fas fa-box"></i> Extérieure
                    @else {{ ucfirst($credit->type_vente) }} @endif
                </span>
            </div>
            <div class="detail-row">
                <span class="label">Date de vente</span>
                <span class="value">{{ $credit->date_vente->format('d/m/Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Ancienneté</span>
                <span class="value" style="color:#e65100;">{{ $credit->date_vente->diffInDays(today()) }} jours</span>
            </div>
            <div class="detail-row">
                <span class="label">Enregistré par</span>
                <span class="value">{{ $credit->user->name }}</span>
            </div>
            @if($credit->notes)
            <div class="detail-row">
                <span class="label">Notes</span>
                <span class="value">{{ $credit->notes }}</span>
            </div>
            @endif
        </div>

        <div class="card">
            <h3>Historique des paiements</h3>
            @if($credit->paiements->count())
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Montant</th>
                        <th>Type</th>
                        <th>Par</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($credit->paiements as $i => $paiement)
                    <tr>
                        <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                        <td style="font-weight:700; color:#2e7d32;">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                        <td>
                            @if($i === $credit->paiements->count() - 1 && $resteAPayer <= 0)
                                <span class="badge badge-solde"><i class="fas fa-check"></i> Soldé</span>
                            @else
                                <span class="badge badge-partiel"><i class="fas fa-adjust"></i> Partiel</span>
                            @endif
                        </td>
                        <td style="color:#777; font-size:0.82rem;">{{ $paiement->user->name }}</td>
                        <td style="color:#999; font-size:0.82rem;">{{ $paiement->notes ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-mini"><i class="fas fa-info-circle"></i> Aucun paiement enregistré.</div>
            @endif
        </div>
    </div>

    {{-- Colonne droite --}}
    <div>
        <div class="montant-header">
            <div class="label">Montant de la vente</div>
            <div class="amount">{{ number_format($credit->montant, 0, ',', ' ') }} FCFA</div>
        </div>

        <div class="card">
            <h3>Suivi du règlement</h3>

            <div class="progress-section">
                <div class="progress-labels">
                    <span style="color:#2e7d32;">Payé</span>
                    <span style="color:#e65100;">{{ $credit->montant > 0 ? round($montantDejaPaye / $credit->montant * 100) : 0 }}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width:{{ $credit->montant > 0 ? round($montantDejaPaye / $credit->montant * 100) : 0 }}%"></div>
                </div>
            </div>

            <div class="amount-row">
                <span class="alabel">Montant total</span>
                <span class="aval">{{ number_format($credit->montant, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="amount-row">
                <span class="alabel">Déjà payé</span>
                <span class="aval color-green">{{ number_format($montantDejaPaye, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="amount-row">
                <span class="alabel">Reste à payer</span>
                <span class="aval color-red">{{ number_format($resteAPayer, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        @if($resteAPayer > 0)
        <div class="card">
            <form class="paiement-form" method="POST" action="{{ route('credits.payer', $credit) }}">
                @csrf
                <h4><i class="fas fa-hand-holding-usd"></i> Enregistrer un paiement</h4>

                <div class="form-group">
                    <label>Montant (FCFA) *</label>
                    <input type="number" name="montant" min="1" max="{{ $resteAPayer }}"
                        value="{{ old('montant', $resteAPayer) }}" required>
                    @error('montant') <span style="color:#e53935; font-size:0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Date du paiement *</label>
                    <input type="date" name="date_paiement" value="{{ old('date_paiement', today()->toDateString()) }}" required>
                    @error('date_paiement') <span style="color:#e53935; font-size:0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Notes <small style="font-weight:400; color:#aaa;">(optionnel)</small></label>
                    <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Remarques...">
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check-circle"></i> Valider le paiement
                </button>
            </form>
        </div>
        @else
        <div class="solde-badge">
            <i class="fas fa-check-circle"></i> Ce crédit est soldé
        </div>
        @endif

        <div style="margin-top: 12px;">
            <a href="{{ route('ventes.show', $credit) }}" class="btn btn-outline" style="width:100%; justify-content:center;">
                <i class="fas fa-file-invoice"></i> Voir la vente complète
            </a>
        </div>
    </div>
</div>
@endsection
