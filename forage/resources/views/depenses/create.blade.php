@extends('layouts.app')

@section('title', 'Nouvelle dépense')
@section('page-title', 'Enregistrer une dépense')

@push('styles')
<style>
    .back-link { display: inline-flex; align-items: center; gap: 6px; color: #1a3a5c; text-decoration: none; font-size: 0.875rem; margin-bottom: 20px; }
    .back-link:hover { text-decoration: underline; }
    .form-card { background: #fff; border-radius: 12px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); max-width: 520px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px; }
    .form-group label { font-size: 0.85rem; font-weight: 600; color: #444; }
    .form-group label span.req { color: #e53935; }
    .form-group input, .form-group select {
        padding: 11px 14px; border: 1.5px solid #e0e0e0; border-radius: 8px;
        font-size: 0.9rem; outline: none; transition: border-color 0.2s;
    }
    .form-group input:focus, .form-group select:focus { border-color: #1a3a5c; }
    .form-group input.is-invalid { border-color: #e53935; }
    .error-msg { color: #e53935; font-size: 0.8rem; }
    .form-actions { display: flex; gap: 12px; margin-top: 8px; }
    .btn { padding: 11px 22px; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
    .btn-primary { background: #1a3a5c; color: #fff; }
    .btn-primary:hover { background: #0d2d4a; }
    .btn-outline { background: transparent; border: 1.5px solid #ccc; color: #555; }
    .btn-outline:hover { border-color: #1a3a5c; color: #1a3a5c; }
    .suggestions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 6px; }
    .suggestion { background: #f0f2f5; color: #555; border: none; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; cursor: pointer; transition: all 0.2s; }
    .suggestion:hover { background: #1a3a5c; color: #fff; }
</style>
@endpush

@section('content')
<a href="{{ route('depenses.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Retour aux dépenses
</a>

<div class="form-card">
    <form method="POST" action="{{ route('depenses.store') }}">
        @csrf

        <div class="form-group">
            <label for="date_depense">Date <span class="req">*</span></label>
            <input type="date" name="date_depense" id="date_depense"
                value="{{ old('date_depense', today()->toDateString()) }}"
                class="{{ $errors->has('date_depense') ? 'is-invalid' : '' }}">
            @error('date_depense') <span class="error-msg">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="motif">Motif / Description <span class="req">*</span></label>
            <input type="text" name="motif" id="motif"
                value="{{ old('motif') }}" placeholder="Ex: Carburant groupe électrogène"
                class="{{ $errors->has('motif') ? 'is-invalid' : '' }}"
                autofocus>
            @error('motif') <span class="error-msg">{{ $message }}</span> @enderror
            <div class="suggestions">
                <span class="suggestion" onclick="setMotif('Carburant')">Carburant</span>
                <span class="suggestion" onclick="setMotif('Salaire employé')">Salaire</span>
                <span class="suggestion" onclick="setMotif('Maintenance pompe')">Maintenance pompe</span>
                <span class="suggestion" onclick="setMotif('Pièces de rechange')">Pièces de rechange</span>
                <span class="suggestion" onclick="setMotif('Électricité')">Électricité</span>
                <span class="suggestion" onclick="setMotif('Retrait caisse')">Retrait caisse</span>
            </div>
        </div>

        <div class="form-group">
            <label for="montant">Montant (FCFA) <span class="req">*</span></label>
            <input type="number" name="montant" id="montant" min="1" step="1"
                value="{{ old('montant') }}" placeholder="0"
                class="{{ $errors->has('montant') ? 'is-invalid' : '' }}">
            @error('montant') <span class="error-msg">{{ $message }}</span> @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer
            </button>
            <a href="{{ route('depenses.index') }}" class="btn btn-outline">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function setMotif(val) {
    document.getElementById('motif').value = val;
    document.getElementById('motif').focus();
}
</script>
@endpush
