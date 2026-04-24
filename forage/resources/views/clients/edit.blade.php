@extends('layouts.app')

@section('title', 'Modifier ' . $client->nom)
@section('page-title', 'Modifier le client')

@push('styles')
<style>
    .back-link { display: inline-flex; align-items: center; gap: 6px; color: #1a3a5c; text-decoration: none; font-size: 0.875rem; margin-bottom: 20px; }
    .back-link:hover { text-decoration: underline; }
    .form-card { background: #fff; border-radius: 12px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); max-width: 560px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px; }
    .form-group label { font-size: 0.85rem; font-weight: 600; color: #444; }
    .form-group label span.req { color: #e53935; }
    .form-group input, .form-group textarea {
        padding: 11px 14px; border: 1.5px solid #e0e0e0; border-radius: 8px;
        font-size: 0.9rem; outline: none; transition: border-color 0.2s;
    }
    .form-group input:focus, .form-group textarea:focus { border-color: #1a3a5c; }
    .form-group input.is-invalid { border-color: #e53935; }
    .error-msg { color: #e53935; font-size: 0.8rem; }
    .form-group textarea { resize: vertical; min-height: 80px; }
    .form-actions { display: flex; gap: 12px; margin-top: 8px; }
    .btn { padding: 11px 22px; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
    .btn-primary { background: #1a3a5c; color: #fff; }
    .btn-primary:hover { background: #0d2d4a; }
    .btn-outline { background: transparent; border: 1.5px solid #ccc; color: #555; }
    .btn-outline:hover { border-color: #1a3a5c; color: #1a3a5c; }
    .input-icon { position: relative; }
    .input-icon i { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #aaa; font-size: 0.9rem; }
    .input-icon input { padding-left: 38px; }
    .info-box { background: #e3f2fd; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; color: #1a3a5c; margin-bottom: 24px; display: flex; align-items: center; gap: 8px; }
</style>
@endpush

@section('content')
<a href="{{ route('clients.show', $client) }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Retour à la fiche client
</a>

<div class="info-box">
    <i class="fas fa-user"></i>
    Modification du client <strong>{{ $client->nom }}</strong>
</div>

<div class="form-card">
    <form method="POST" action="{{ route('clients.update', $client) }}">
        @csrf @method('PUT')

        <div class="form-group">
            <label for="nom">Nom du client <span class="req">*</span></label>
            <div class="input-icon">
                <i class="fas fa-user"></i>
                <input type="text" name="nom" id="nom"
                    value="{{ old('nom', $client->nom) }}"
                    class="{{ $errors->has('nom') ? 'is-invalid' : '' }}"
                    autofocus>
            </div>
            @error('nom') <span class="error-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="contact">Contact / Téléphone</label>
            <div class="input-icon">
                <i class="fas fa-phone"></i>
                <input type="text" name="contact" id="contact"
                    value="{{ old('contact', $client->contact) }}">
            </div>
        </div>

        <div class="form-group">
            <label for="adresse">Adresse / Localisation</label>
            <textarea name="adresse" id="adresse">{{ old('adresse', $client->adresse) }}</textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer
            </button>
            <a href="{{ route('clients.show', $client) }}" class="btn btn-outline">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>
@endsection
