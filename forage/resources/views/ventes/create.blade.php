@extends('layouts.app')

@section('title', 'Nouvelle vente')
@section('page-title', 'Enregistrer une vente')

@push('styles')
<style>
    .form-card { background: #fff; border-radius: 12px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); max-width: 700px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group.full { grid-column: 1 / -1; }
    .form-group label { font-size: 0.85rem; font-weight: 600; color: #444; }
    .form-group label span.req { color: #e53935; }
    .form-group input, .form-group select, .form-group textarea {
        padding: 10px 14px; border: 1.5px solid #e0e0e0; border-radius: 8px;
        font-size: 0.9rem; outline: none; transition: border-color 0.2s; background: #fff;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #1a3a5c; }
    .form-group input.is-invalid, .form-group select.is-invalid { border-color: #e53935; }
    .error-msg { color: #e53935; font-size: 0.8rem; }
    .form-group textarea { resize: vertical; min-height: 80px; }

    .type-selector { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
    .type-option { position: relative; }
    .type-option input[type="radio"] { position: absolute; opacity: 0; }
    .type-option label {
        display: flex; flex-direction: column; align-items: center; gap: 8px;
        padding: 16px; border: 2px solid #e0e0e0; border-radius: 10px;
        cursor: pointer; transition: all 0.2s; text-align: center;
    }
    .type-option label i { font-size: 1.5rem; }
    .type-option label span { font-size: 0.85rem; font-weight: 600; }
    .type-option input[type="radio"]:checked + label { border-color: #1a3a5c; background: #e3f2fd; color: #1a3a5c; }

    .section-title { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #888; margin: 24px 0 12px; padding-bottom: 8px; border-bottom: 1px solid #eee; }
    .form-actions { display: flex; gap: 12px; margin-top: 28px; }
    .btn { padding: 11px 22px; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
    .btn-primary { background: #1a3a5c; color: #fff; }
    .btn-primary:hover { background: #0d2d4a; }
    .btn-outline { background: transparent; border: 1.5px solid #ccc; color: #555; }
    .btn-outline:hover { border-color: #1a3a5c; color: #1a3a5c; }

    #client-section { display: none; }
</style>
@endpush

@section('content')
<div class="form-card">
    <form method="POST" action="{{ route('ventes.store') }}">
        @csrf

        @if($forageExisteAujourdhui)
        <div id="forage-warning" style="display:none; background:#fff8e1; border:1px solid #ffe082; border-radius:8px; padding:11px 15px; margin-bottom:16px; font-size:0.85rem; color:#795548;">
            <i class="fas fa-exclamation-triangle" style="color:#f9a825;"></i>
            Une vente forage a déjà été enregistrée aujourd'hui. Vous ne pouvez pas en créer une deuxième pour la même date.
        </div>
        @endif

        <p class="section-title">Type de vente</p>
        <div class="type-selector">
            <div class="type-option">
                <input type="radio" name="type_vente" id="type-forage" value="forage"
                    {{ old('type_vente', request('type', 'forage')) === 'forage' ? 'checked' : '' }}>
                <label for="type-forage">
                    <i class="fas fa-faucet" style="color:#1a3a5c;"></i>
                    <span>Forage</span>
                    <small style="color:#888; font-size:0.75rem;">Sur place / immédiat</small>
                </label>
            </div>
            <div class="type-option">
                <input type="radio" name="type_vente" id="type-livraison" value="livraison"
                    {{ old('type_vente', request('type')) === 'livraison' ? 'checked' : '' }}>
                <label for="type-livraison">
                    <x-icon-tuyau style="color:#2e7d32; font-size:1.5rem;" />
                    <span>Livraison</span>
                    <small style="color:#888; font-size:0.75rem;">Bases d'eau</small>
                </label>
            </div>
            <div class="type-option">
                <input type="radio" name="type_vente" id="type-exterieure" value="exterieure"
                    {{ old('type_vente', request('type')) === 'exterieure' ? 'checked' : '' }}>
                <label for="type-exterieure">
                    <i class="fas fa-box" style="color:#e65100;"></i>
                    <span>Extérieure</span>
                    <small style="color:#888; font-size:0.75rem;">Bidons / Fûts</small>
                </label>
            </div>
        </div>
        @error('type_vente')
            <p class="error-msg" style="margin-top:6px;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
        @enderror

        {{-- Client (caché pour forage) --}}
        <div id="client-section">
            <p class="section-title">Client</p>
            <div class="form-group">
                <label for="client_id">Client <small style="font-weight:400; color:#888;">(optionnel)</small></label>
                <select name="client_id" id="client_id">
                    <option value="">— Sélectionner un client —</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <p class="section-title">Détails de la vente</p>
        <div class="form-grid">
            <div class="form-group">
                <label for="date_vente">Date <span class="req">*</span></label>
                <input type="date" name="date_vente" id="date_vente"
                    value="{{ old('date_vente', today()->toDateString()) }}"
                    class="{{ $errors->has('date_vente') ? 'is-invalid' : '' }}">
                @error('date_vente') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="montant">Montant (FCFA) <span class="req">*</span></label>
                <input type="number" name="montant" id="montant" step="1" min="0"
                    value="{{ old('montant') }}" placeholder="0"
                    class="{{ $errors->has('montant') ? 'is-invalid' : '' }}">
                @error('montant') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            {{-- Statut paiement (caché pour forage) --}}
            <div class="form-group" id="statut-section">
                <label for="statut_paiement">Statut paiement <span class="req">*</span></label>
                <select name="statut_paiement" id="statut_paiement">
                    <option value="paye" {{ old('statut_paiement', 'paye') === 'paye' ? 'selected' : '' }}>
                        ✅ Payé immédiatement
                    </option>
                    <option value="non_paye" {{ old('statut_paiement') === 'non_paye' ? 'selected' : '' }}>
                        ⏳ Crédit (non payé)
                    </option>
                </select>
            </div>

            <div class="form-group full">
                <label for="notes">Notes / Observations</label>
                <textarea name="notes" id="notes" placeholder="Remarques éventuelles...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer la vente
            </button>
            <a href="{{ route('ventes.index') }}" class="btn btn-outline">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const clientSection  = document.getElementById('client-section');
const statutSection  = document.getElementById('statut-section');
const forageWarning  = document.getElementById('forage-warning');
const submitBtn      = document.querySelector('button[type="submit"]');
const radioButtons   = document.querySelectorAll('input[name="type_vente"]');
const forageExists   = {{ $forageExisteAujourdhui ? 'true' : 'false' }};

function updateForm(type) {
    clientSection.style.display = (type === 'livraison' || type === 'exterieure') ? 'block' : 'none';
    statutSection.style.display = type === 'forage' ? 'none' : 'block';

    if (forageWarning) {
        const blocked = type === 'forage' && forageExists;
        forageWarning.style.display = blocked ? 'block' : 'none';
        submitBtn.disabled = blocked;
        submitBtn.style.opacity = blocked ? '0.5' : '1';
    }
}

radioButtons.forEach(rb => rb.addEventListener('change', e => updateForm(e.target.value)));

const checked = document.querySelector('input[name="type_vente"]:checked');
if (checked) updateForm(checked.value);
</script>
@endpush
