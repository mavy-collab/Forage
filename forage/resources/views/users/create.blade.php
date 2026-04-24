@extends('layouts.app')

@section('title', 'Nouvel utilisateur')
@section('page-title', 'Créer un utilisateur')

@push('styles')
<style>
    .back-link { display: inline-flex; align-items: center; gap: 6px; color: #1a3a5c; text-decoration: none; font-size: 0.875rem; margin-bottom: 20px; }
    .back-link:hover { text-decoration: underline; }
    .form-card { background: #fff; border-radius: 12px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); max-width: 540px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px; }
    .form-group label { font-size: 0.85rem; font-weight: 600; color: #444; }
    .form-group label span.req { color: #e53935; }
    .form-group input, .form-group select {
        padding: 11px 14px; border: 1.5px solid #e0e0e0; border-radius: 8px;
        font-size: 0.9rem; outline: none; transition: border-color 0.2s; background: #fff;
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
    .section-title { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #888; margin: 0 0 16px; padding-bottom: 8px; border-bottom: 1px solid #eee; }
    .role-options { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .role-option { position: relative; }
    .role-option input[type="radio"] { position: absolute; opacity: 0; }
    .role-option label { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 16px; border: 2px solid #e0e0e0; border-radius: 10px; cursor: pointer; transition: all 0.2s; text-align: center; }
    .role-option label i { font-size: 1.4rem; }
    .role-option label span { font-size: 0.85rem; font-weight: 700; }
    .role-option label small { font-size: 0.75rem; color: #999; }
    .role-option input:checked + label { border-color: #1a3a5c; background: #e3f2fd; color: #1a3a5c; }
    .password-hint { font-size: 0.78rem; color: #aaa; margin-top: 2px; }
</style>
@endpush

@section('content')
<a href="{{ route('users.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Retour aux utilisateurs
</a>

<div class="form-card">
    <form method="POST" action="{{ route('users.store') }}">
        @csrf

        <p class="section-title">Informations personnelles</p>

        <div class="form-group">
            <label for="name">Nom complet <span class="req">*</span></label>
            <input type="text" name="name" id="name"
                value="{{ old('name') }}" placeholder="Ex : Mamadou Diallo"
                class="{{ $errors->has('name') ? 'is-invalid' : '' }}" autofocus>
            @error('name') <span class="error-msg">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="email">Adresse email <span class="req">*</span></label>
            <input type="email" name="email" id="email"
                value="{{ old('email') }}" placeholder="email@exemple.com"
                class="{{ $errors->has('email') ? 'is-invalid' : '' }}">
            @error('email') <span class="error-msg">{{ $message }}</span> @enderror
        </div>

        <p class="section-title" style="margin-top: 24px;">Rôle</p>
        <div class="role-options" style="margin-bottom: 20px;">
            <div class="role-option">
                <input type="radio" name="role" id="role-employe" value="employe"
                    {{ old('role', 'employe') === 'employe' ? 'checked' : '' }}>
                <label for="role-employe">
                    <i class="fas fa-user" style="color:#2e7d32;"></i>
                    <span>Employé</span>
                    <small>Saisie des ventes et dépenses</small>
                </label>
            </div>
            <div class="role-option">
                <input type="radio" name="role" id="role-admin" value="admin"
                    {{ old('role') === 'admin' ? 'checked' : '' }}>
                <label for="role-admin">
                    <i class="fas fa-shield-alt" style="color:#1a3a5c;"></i>
                    <span>Administrateur</span>
                    <small>Accès complet + gestion</small>
                </label>
            </div>
        </div>
        @error('role') <span class="error-msg" style="margin-bottom:16px; display:block;">{{ $message }}</span> @enderror

        <p class="section-title">Mot de passe</p>

        <div class="form-group">
            <label for="password">Mot de passe <span class="req">*</span></label>
            <input type="password" name="password" id="password"
                placeholder="••••••••"
                class="{{ $errors->has('password') ? 'is-invalid' : '' }}">
            <span class="password-hint">Minimum 6 caractères</span>
            @error('password') <span class="error-msg">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirmer le mot de passe <span class="req">*</span></label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                placeholder="••••••••">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Créer l'utilisateur
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-outline">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>
@endsection
