@extends('layouts.app')

@section('title', 'Utilisateurs')
@section('page-title', 'Gestion des utilisateurs')

@push('styles')
<style>
    .page-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
    .btn { padding: 10px 18px; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
    .btn-sm { padding: 6px 12px; font-size: 0.8rem; }
    .btn-primary { background: #1a3a5c; color: #fff; }
    .btn-primary:hover { background: #0d2d4a; }
    .btn-outline { background: transparent; border: 1.5px solid #1a3a5c; color: #1a3a5c; }
    .btn-outline:hover { background: #1a3a5c; color: #fff; }
    .btn-danger { background: #e53935; color: #fff; }
    .btn-danger:hover { background: #b71c1c; }
    .btn-warning { background: #f57f17; color: #fff; }
    .btn-warning:hover { background: #e65100; }

    /* Sélecteur de mois */
    .month-picker {
        background: #fff; border-radius: 10px; padding: 14px 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 24px;
        display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
    }
    .month-picker label { font-size: 0.85rem; font-weight: 600; color: #444; white-space: nowrap; }
    .month-picker select, .month-picker input {
        padding: 8px 12px; border: 1.5px solid #e0e0e0; border-radius: 7px;
        font-size: 0.875rem; outline: none; background: #fff;
    }
    .month-picker select:focus, .month-picker input:focus { border-color: #1a3a5c; }
    .month-badge {
        background: #e3f2fd; color: #1a3a5c; padding: 5px 14px;
        border-radius: 20px; font-size: 0.8rem; font-weight: 700;
    }

    .users-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 20px; }

    .user-card { background: #fff; border-radius: 14px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); overflow: hidden; transition: box-shadow 0.2s; }
    .user-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.12); }

    .user-card-header { padding: 20px 24px; display: flex; align-items: center; gap: 16px; border-bottom: 1px solid #f0f0f0; }
    .user-avatar { width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; font-weight: 700; color: #fff; flex-shrink: 0; }
    .avatar-admin   { background: linear-gradient(135deg, #1a3a5c, #0d6efd); }
    .avatar-employe { background: linear-gradient(135deg, #2e7d32, #43a047); }

    .user-info h3 { font-size: 1rem; font-weight: 700; color: #1a3a5c; margin-bottom: 4px; }
    .user-info .email { font-size: 0.8rem; color: #888; }
    .user-info .role-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 0.73rem; font-weight: 700; margin-top: 5px; }
    .role-admin   { background: #e3f2fd; color: #1a3a5c; }
    .role-employe { background: #e8f5e9; color: #2e7d32; }
    .you-badge { background: #fff3e0; color: #e65100; font-size: 0.7rem; font-weight: 700; padding: 2px 8px; border-radius: 12px; margin-left: 6px; }

    /* Stats globales */
    .stats-global { padding: 14px 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; border-bottom: 1px solid #f0f0f0; }
    .stat-mini { text-align: center; background: #f8f9fa; border-radius: 8px; padding: 8px; }
    .stat-mini p { font-size: 0.7rem; color: #aaa; margin-bottom: 2px; }
    .stat-mini h4 { font-size: 1rem; font-weight: 700; color: #555; }

    /* Stats mensuelles */
    .stats-mois {
        padding: 14px 24px;
        background: linear-gradient(135deg, #f8f9ff, #f0f4ff);
        border-bottom: 1px solid #e8eaf6;
    }
    .stats-mois-title {
        font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.5px; color: #7986cb; margin-bottom: 10px;
        display: flex; align-items: center; gap: 6px;
    }
    .stats-mois-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .stat-mois-card { border-radius: 8px; padding: 10px 12px; }
    .stat-mois-card.ventes  { background: #e8f5e9; }
    .stat-mois-card.depenses { background: #ffebee; }
    .stat-mois-card p { font-size: 0.72rem; color: #888; margin-bottom: 3px; }
    .stat-mois-card h4 { font-size: 1rem; font-weight: 700; }
    .stat-mois-card.ventes h4  { color: #2e7d32; }
    .stat-mois-card.depenses h4 { color: #c62828; }
    .stat-mois-card .sub { font-size: 0.7rem; color: #aaa; margin-top: 2px; }
    .zero-activity { text-align:center; padding: 8px; font-size:0.8rem; color:#bbb; font-style:italic; }

    .user-meta { padding: 6px 24px 10px; font-size: 0.76rem; color: #bbb; display: flex; align-items: center; gap: 6px; }

    .user-card-footer { padding: 12px 24px; border-top: 1px solid #f0f0f0; display: flex; gap: 8px; flex-wrap: wrap; }

    /* Modal reset password */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 200; align-items: center; justify-content: center; }
    .modal-overlay.open { display: flex; }
    .modal { background: #fff; border-radius: 14px; padding: 28px; width: 100%; max-width: 420px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
    .modal h3 { font-size: 1.1rem; color: #1a3a5c; margin-bottom: 6px; }
    .modal p { font-size: 0.85rem; color: #888; margin-bottom: 20px; }
    .modal .form-group { margin-bottom: 14px; }
    .modal .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #444; margin-bottom: 6px; }
    .modal .form-group input { width: 100%; padding: 10px 14px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 0.9rem; outline: none; }
    .modal .form-group input:focus { border-color: #1a3a5c; }
    .error-msg { color: #e53935; font-size: 0.8rem; margin-top: 4px; }
    .modal-actions { display: flex; gap: 10px; margin-top: 20px; }
    .modal-actions .btn { flex: 1; justify-content: center; }
    .btn-cancel { background: #f5f5f5; color: #555; }
    .btn-cancel:hover { background: #e0e0e0; }
</style>
@endpush

@section('content')
<div class="page-actions">
    <p style="color:#888; font-size:0.875rem;">{{ $users->count() }} utilisateur(s) enregistré(s)</p>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Nouvel utilisateur
    </a>
</div>

{{-- Sélecteur de mois --}}
<div class="month-picker">
    <i class="fas fa-calendar-alt" style="color:#7986cb;"></i>
    <label>Statistiques pour :</label>
    <form method="GET" action="{{ route('users.index') }}" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <select name="mois" onchange="this.form.submit()">
            @foreach(range(1,12) as $m)
                <option value="{{ $m }}" {{ $m == $mois ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                </option>
            @endforeach
        </select>
        <select name="annee" onchange="this.form.submit()">
            @foreach(range(now()->year, now()->year - 2) as $a)
                <option value="{{ $a }}" {{ $a == $annee ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
        </select>
    </form>
    <span class="month-badge"><i class="fas fa-chart-bar"></i> {{ $moisLabel }}</span>
</div>

<div class="users-grid">
    @foreach($users as $user)
    <div class="user-card">
        <div class="user-card-header">
            <div class="user-avatar {{ $user->role === 'admin' ? 'avatar-admin' : 'avatar-employe' }}">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="user-info">
                <h3>
                    {{ $user->name }}
                    @if($user->id === auth()->id())
                        <span class="you-badge">Vous</span>
                    @endif
                </h3>
                <div class="email"><i class="fas fa-envelope" style="font-size:0.72rem;"></i> {{ $user->email }}</div>
                <div class="role-badge {{ $user->role === 'admin' ? 'role-admin' : 'role-employe' }}">
                    <i class="fas {{ $user->role === 'admin' ? 'fa-shield-alt' : 'fa-user' }}"></i>
                    {{ $user->role === 'admin' ? 'Administrateur' : 'Employé' }}
                </div>
            </div>
        </div>

        {{-- Stats globales (total depuis le début) --}}
        <div class="stats-global">
            <div class="stat-mini">
                <p>Ventes (total)</p>
                <h4>{{ $user->ventes_count }}</h4>
            </div>
            <div class="stat-mini">
                <p>Dépenses (total)</p>
                <h4>{{ $user->depenses_count }}</h4>
            </div>
        </div>

        {{-- Stats mensuelles --}}
        <div class="stats-mois">
            <div class="stats-mois-title">
                <i class="fas fa-calendar-alt"></i> {{ $moisLabel }}
            </div>
            @if(($user->ventes_mois_count ?? 0) == 0 && ($user->depenses_mois_count ?? 0) == 0)
                <div class="zero-activity">Aucune activité ce mois</div>
            @else
            <div class="stats-mois-grid">
                <div class="stat-mois-card ventes">
                    <p>Ventes encaissées</p>
                    <h4>{{ number_format($user->ventes_mois_montant ?? 0, 0, ',', ' ') }} FCFA</h4>
                    <div class="sub">{{ $user->ventes_mois_count ?? 0 }} vente(s)</div>
                </div>
                <div class="stat-mois-card depenses">
                    <p>Dépenses enregistrées</p>
                    <h4>{{ number_format($user->depenses_mois_montant ?? 0, 0, ',', ' ') }} FCFA</h4>
                    <div class="sub">{{ $user->depenses_mois_count ?? 0 }} dépense(s)</div>
                </div>
            </div>
            @endif
        </div>

        <div class="user-meta">
            <i class="fas fa-calendar-plus"></i>
            Créé le {{ $user->created_at->format('d/m/Y') }}
        </div>

        <div class="user-card-footer">
            <a href="{{ route('users.edit', $user) }}" class="btn btn-outline btn-sm">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <button class="btn btn-warning btn-sm"
                onclick="openReset({{ $user->id }}, '{{ addslashes($user->name) }}')">
                <i class="fas fa-key"></i> Mot de passe
            </button>
            @if($user->id !== auth()->id())
            <form method="POST" action="{{ route('users.destroy', $user) }}"
                  onsubmit="return confirm('Supprimer « {{ $user->name }} » ? Cette action est irréversible.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>

{{-- Modal reset mot de passe --}}
<div class="modal-overlay" id="modalReset">
    <div class="modal">
        <h3><i class="fas fa-key"></i> Réinitialiser le mot de passe</h3>
        <p id="resetSubtitle"></p>
        <form method="POST" id="formReset">
            @csrf
            <div class="form-group">
                <label>Nouveau mot de passe *</label>
                <input type="password" name="password" id="resetPassword"
                       placeholder="••••••••" required minlength="8">
                @error('password') <div class="error-msg">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Confirmer le mot de passe *</label>
                <input type="password" name="password_confirmation"
                       placeholder="••••••••" required minlength="8">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="closeModal()">Annuler</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Valider</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openReset(id, name) {
    document.getElementById('resetSubtitle').textContent = 'Utilisateur : ' + name;
    document.getElementById('formReset').action = '{{ url('users') }}/' + id + '/reset-password';
    document.getElementById('resetPassword').value = '';
    document.getElementById('modalReset').classList.add('open');
}
function closeModal() {
    document.getElementById('modalReset').classList.remove('open');
}
document.getElementById('modalReset').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endpush
