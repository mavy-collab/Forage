@extends('layouts.app')

@section('title', 'Nouvelle ardoise')
@section('page-title', 'Enregistrer une ardoise')

@push('styles')
<style>
    .form-card { background: #fff; border-radius: 12px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); max-width: 560px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group.full { grid-column: 1 / -1; }
    .form-group label { font-size: 0.85rem; font-weight: 600; color: #444; }
    .form-group label span.req { color: #e53935; }
    .form-group input, .form-group textarea {
        padding: 10px 14px; border: 1.5px solid #e0e0e0; border-radius: 8px;
        font-size: 0.9rem; outline: none; transition: border-color 0.2s; background: #fff;
    }
    .form-group input:focus, .form-group textarea:focus { border-color: #1a3a5c; }
    .form-group input.is-invalid { border-color: #e53935; }
    .error-msg { color: #e53935; font-size: 0.8rem; }
    .form-group textarea { resize: vertical; min-height: 80px; }
    .form-actions { display: flex; gap: 12px; margin-top: 28px; }
    .btn { padding: 11px 22px; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
    .btn-primary { background: #1a3a5c; color: #fff; }
    .btn-primary:hover { background: #0d2d4a; }
    .btn-outline { background: transparent; border: 1.5px solid #ccc; color: #555; }
    .btn-outline:hover { border-color: #1a3a5c; color: #1a3a5c; }
    .info-banner { background: #fff8e1; border: 1px solid #ffe082; border-radius: 8px; padding: 12px 16px; margin-bottom: 24px; font-size: 0.85rem; color: #795548; display: flex; align-items: center; gap: 10px; }
</style>
@endpush

@section('content')
<div class="form-card">
    <div class="info-banner">
        <i class="fas fa-info-circle" style="color:#f9a825;"></i>
        Enregistrez une personne qui a pris de l'eau au forage sans payer. Vous pourrez marquer la dette comme remboursée plus tard.
    </div>

    <form method="POST" action="{{ route('ardoise.store') }}">
        @csrf
        <div class="form-grid">
            <div class="form-group full">
                <label for="nom">Nom de la personne <span class="req">*</span></label>
                <input type="text" name="nom" id="nom"
                    value="{{ old('nom') }}" placeholder="Ex : Amadou, Tante Mariama..."
                    class="{{ $errors->has('nom') ? 'is-invalid' : '' }}" autofocus>
                @error('nom') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="montant">Montant dû (FCFA) <span class="req">*</span></label>
                <input type="number" name="montant" id="montant" step="1" min="1"
                    value="{{ old('montant') }}" placeholder="0"
                    class="{{ $errors->has('montant') ? 'is-invalid' : '' }}">
                @error('montant') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="date_ardoise">Date <span class="req">*</span></label>
                <input type="date" name="date_ardoise" id="date_ardoise"
                    value="{{ old('date_ardoise', today()->toDateString()) }}"
                    class="{{ $errors->has('date_ardoise') ? 'is-invalid' : '' }}">
                @error('date_ardoise') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="form-group full">
                <label for="notes">Notes <small style="font-weight:400; color:#888;">(optionnel)</small></label>
                <textarea name="notes" id="notes" placeholder="Ex : a pris 2 bidons, voisin du quartier...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer l'ardoise
            </button>
            <a href="{{ route('ardoise.index') }}" class="btn btn-outline">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>
@endsection
