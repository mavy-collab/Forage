@extends('layouts.app')

@section('title', 'Modifier ' . $user->name)
@section('page-title', 'Modifier un utilisateur')

@push('styles')
<style>
    .back-link { display: inline-flex; align-items: center; gap: 6px; color: #1a3a5c; text-decoration: none; font-size: 0.875rem; margin-bottom: 20px; }
    .back-link:hover { text-decoration: underline; }
    .form-card { background: #fff; border-radius: 12px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); max-width: 540px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px; }
    .form-group label { font-size: 0.85rem; font-weight: 600; color: #444; }
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
    .role-options { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; }
    .role-option { position: relative; }
    .role-option input[type="radio"] { position: absolute; opacity: 0; }
    .role-option label { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 16px; border: 2px solid #e0e0e0; border-radius: 10px; cursor: pointer; transition: all 0.2s; text-align: center; }
    .role-option label i { font-size: 1.4rem; }
    .role-option label span { font-size: 0.85rem; font-weight: 700; }
    .role-option label small { font-size: 0.75rem; color: #999; }
    .role-option input:checked + label { border-color: #1a3a5c; background: #e3f2fd; color: #1a3a5c; }
    .info-box { background: #e3f2fd; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; color: #1a3a5c; margin-bottom: 24px; display: flex; align-items: center; gap: 8px; }
    .you-notice { background: #fff8e1; border-radius: 8px; padding: 10px 14px; font-size: 0.82rem; color: #e65100; margin-top: -10px; margin-bottom: 16px; display: flex; align-items: center; gap: 7px; }
</style>
@endpush

@section('content')
<a href="{{ route('users.index') }}" class="back-link">
    <i class="fas fa-arrow-left"></i> Retour aux utilisateurs
</a>

<div class="info-box">
    <i class="fas fa-user-edit"></i>
    Modification de <strong>{{ $user->name }}</strong>
    @if($user->id === auth()->id()) — <em>C'est votre compte</em> @endif
</div>

<div class="form-card">
    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf @method('PUT')

        <p class="section-title">Informations personnelles</p>

        <div class="form-group">
            <label for="name">Nom complet *</label>
            <input type="text" name="name" id="name"
                value="{{ old('name', $user->name) }}"
                class="{{ $errors->has('name') ? 'is-invalid' : '' }}">
            @error('name') <span class="error-msg">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="email">Adresse email *</label>
            <input type="email" name="email" id="email"
                value="{{ old('email', $user->email) }}"
                class="{{ $errors->has('email') ? 'is-invalid' : '' }}">
            @error('email') <span class="error-msg">{{ $message }}</span> @enderror
        </div>

        <p class="section-title" style="margin-top: 24px;">Rôle</p>

        @if($user->id === auth()->id())
        <div class="you-notice">
            <i class="fas fa-info-circle"></i>
            Vous ne pouvez pas retirer votre propre rôle administrateur.
        </div>
        @endif

        <div class="role-options">
            <div class="role-option">
                <input type="radio" name="role" id="role-employe" value="employe"
                    {{ old('role', $user->role) === 'employe' ? 'checked' : '' }}
                    {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                <label for="role-employe">
                    <i class="fas fa-user" style="color:#2e7d32;"></i>
                    <span>Employé</span>
                    <small>Saisie des ventes et dépenses</small>
                </label>
            </div>
            <div class="role-option">
                <input type="radio" name="role" id="role-admin" value="admin"
                    {{ old('role', $user->role) === 'admin' ? 'checked' : '' }}>
                <label for="role-admin">
                    <i class="fas fa-shield-alt" style="color:#1a3a5c;"></i>
                    <span>Administrateur</span>
                    <small>Accès complet + gestion</small>
                </label>
            </div>
        </div>
        @error('role') <span class="error-msg" style="margin-bottom:16px; display:block;">{{ $message }}</span> @enderror

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-outline">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>
@endsection
