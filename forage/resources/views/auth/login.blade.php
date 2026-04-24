<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Forage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a3a5c 0%, #0d6efd 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .login-container {
            background: #fff; border-radius: 16px; padding: 48px 40px;
            width: 100%; max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        }
        .login-header { text-align: center; margin-bottom: 36px; }
        .login-header .icon {
            width: 70px; height: 70px; background: #e3f2fd;
            border-radius: 50%; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 16px;
        }
        .login-header .icon i { font-size: 2rem; color: #1a3a5c; }
        .login-header h1 { font-size: 1.6rem; color: #1a3a5c; font-weight: 700; }
        .login-header p { color: #666; margin-top: 6px; font-size: 0.9rem; }

        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block; margin-bottom: 8px;
            font-size: 0.875rem; font-weight: 600; color: #444;
        }
        .input-wrapper { position: relative; }
        .input-wrapper i {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%); color: #999; font-size: 0.9rem;
        }
        .form-group input {
            width: 100%; padding: 12px 14px 12px 40px;
            border: 1.5px solid #ddd; border-radius: 8px;
            font-size: 0.95rem; transition: border-color 0.2s;
            outline: none;
        }
        .form-group input:focus { border-color: #1a3a5c; }
        .form-group input.is-invalid { border-color: #e53935; }

        .error-msg {
            color: #e53935; font-size: 0.82rem;
            margin-top: 5px; display: flex; align-items: center; gap: 4px;
        }

        .form-remember {
            display: flex; align-items: center; gap: 8px;
            font-size: 0.875rem; color: #555; margin-bottom: 24px;
        }
        .form-remember input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; }

        .btn-login {
            width: 100%; padding: 13px;
            background: #1a3a5c; color: #fff;
            border: none; border-radius: 8px; font-size: 1rem;
            font-weight: 600; cursor: pointer; transition: background 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-login:hover { background: #0d2d4a; }

        .alert-error {
            background: #ffebee; color: #c62828;
            border: 1px solid #ef9a9a; border-radius: 8px;
            padding: 12px 14px; margin-bottom: 20px;
            font-size: 0.875rem; display: flex; align-items: center; gap: 8px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="icon"><i class="fas fa-tint"></i></div>
            <h1>Gestion Forage</h1>
            <p>Connectez-vous à votre espace</p>
        </div>

        @if(session('error'))
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        @error('email')
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ $message }}
            </div>
        @enderror

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="form-group">
                <label for="email">Adresse email</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope"></i>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="admin@forage.com"
                        class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                        autofocus
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                    >
                </div>
                @error('password')
                    <p class="error-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                @enderror
            </div>

            <div class="form-remember">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Se souvenir de moi</label>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </button>
        </form>
    </div>
</body>
</html>
