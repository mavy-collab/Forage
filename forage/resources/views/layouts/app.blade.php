<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1a3a5c">
    <title>@yield('title', 'Forage') — Gestion des ventes</title>
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
          integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw=="
          crossorigin="anonymous" referrerpolicy="no-referrer">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"
            integrity="sha256-oVuSxbGMLMH2RDpN4g9ysMCLNDC2GVkxZi/4VTZ4OKA="
            crossorigin="anonymous"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { overflow-x: hidden; max-width: 100vw; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; color: #333; display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: 250px; background: #1a3a5c; color: #fff;
            display: flex; flex-direction: column; position: fixed;
            top: 0; left: 0; height: 100vh; z-index: 100;
        }
        .sidebar-brand {
            padding: 24px 20px; font-size: 1.2rem; font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-brand i { color: #4fc3f7; font-size: 1.4rem; }
        .sidebar-menu { flex: 1; padding: 16px 0; overflow-y: auto; }
        .sidebar-menu a {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 20px; color: rgba(255,255,255,0.75);
            text-decoration: none; font-size: 0.9rem; transition: all 0.2s;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(255,255,255,0.1); color: #fff;
            border-left: 3px solid #4fc3f7;
        }
        .sidebar-menu a i { width: 20px; text-align: center; }
        .sidebar-menu .menu-section {
            padding: 12px 20px 4px; font-size: 0.7rem;
            text-transform: uppercase; letter-spacing: 1px;
            color: rgba(255,255,255,0.4);
        }
        .sidebar-footer {
            padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-footer .user-info { font-size: 0.85rem; margin-bottom: 8px; }
        .sidebar-footer .user-info span { color: rgba(255,255,255,0.6); }
        .sidebar-footer form button {
            width: 100%; padding: 8px; background: rgba(255,255,255,0.1);
            color: #fff; border: none; border-radius: 6px;
            cursor: pointer; font-size: 0.85rem; transition: background 0.2s;
        }
        .sidebar-footer form button:hover { background: #e53935; }

        /* Main content */
        .main-content { margin-left: 250px; flex: 1; display: flex; flex-direction: column; }
        .topbar {
            background: #fff; padding: 16px 28px;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 50;
        }
        .topbar h1 { font-size: 1.2rem; color: #1a3a5c; font-weight: 600; }
        .topbar .badge-role {
            background: #e3f2fd; color: #1a3a5c;
            padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;
        }
        .content { padding: 28px; flex: 1; }

        /* Alerts */
        .alert {
            padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px; font-size: 0.9rem;
        }
        .alert-success { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
        .alert-error { background: #ffebee; color: #c62828; border: 1px solid #ef9a9a; }
        .alert-warning { background: #fff8e1; color: #f57f17; border: 1px solid #ffe082; }

        /* Tableaux responsive */
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }

        @media (max-width: 768px) {
            /* Bloquer tout défilement horizontal */
            .main-content, .content { overflow-x: hidden; max-width: 100%; }

            /* Grilles en colonne unique */
            .stats-grid, .users-grid, .charts-grid, .stats-grid-3,
            .totaux-grid, .stats-section, .stats-mois-grid {
                grid-template-columns: 1fr !important;
            }

            /* Tableaux : pas de scroll, on masque les colonnes */
            .table-card { overflow: hidden; }
            .table-card table { width: 100%; table-layout: fixed; }
            .table-card td, .table-card th { word-break: break-word; }

            /* Colonnes à masquer sur mobile */
            .hide-mobile { display: none !important; }

            /* Filtres en colonne */
            .filters form { flex-direction: column; }
            .filter-group { width: 100%; }
            .filter-group select, .filter-group input { width: 100%; }

            /* Page actions */
            .page-actions { flex-direction: column; align-items: flex-start; }

            /* Formulaires */
            .form-card { padding: 16px; }

            /* Modals pleine largeur */
            .modal { padding: 20px 16px; margin: 0 12px; max-width: calc(100vw - 24px); }

            /* Montants compacts */
            .montant-depense, .montant { font-size: 0.9rem; }
        }

        /* Hamburger */
        .hamburger {
            display: none; background: none; border: none;
            cursor: pointer; padding: 6px; color: #1a3a5c;
            font-size: 1.4rem; line-height: 1;
        }

        /* Overlay mobile */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 99;
        }
        .sidebar-overlay.open { display: block; }

        /* ── Responsive mobile ── */
        @media (max-width: 768px) {
            .hamburger { display: flex; align-items: center; }

            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.28s ease;
                z-index: 200;
            }
            .sidebar.open { transform: translateX(0); box-shadow: 4px 0 20px rgba(0,0,0,0.3); }

            .main-content { margin-left: 0 !important; }

            .topbar { padding: 12px 16px; gap: 8px; }
            .topbar h1 { font-size: 1rem; }

            .content { padding: 14px; }
        }

        @media (max-width: 480px) {
            .topbar h1 { font-size: 0.9rem; }
            .conn-badge { font-size: 0.72rem; padding: 3px 8px; }
            .badge-role { font-size: 0.72rem; padding: 3px 8px; }
        }

        /* Indicateur connexion */
        .conn-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600;
        }
        .conn-online  { background: #e8f5e9; color: #2e7d32; }
        .conn-offline { background: #ffebee; color: #c62828; }
        .sync-count {
            display: none; background: #e65100; color: #fff;
            border-radius: 50%; width: 18px; height: 18px;
            font-size: 0.7rem; font-weight: 700;
            align-items: center; justify-content: center; margin-left: 4px;
        }

        /* Toast offline */
        .offline-toast {
            position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(80px);
            background: #1a3a5c; color: #fff;
            padding: 12px 22px; border-radius: 10px;
            font-size: 0.88rem; font-weight: 500;
            box-shadow: 0 6px 24px rgba(0,0,0,0.25);
            z-index: 9999; opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
            max-width: 90vw; text-align: center;
        }
        .offline-toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
        .offline-toast.toast-success  { background: #2e7d32; }
        .offline-toast.toast-error    { background: #c62828; }
        .offline-toast.toast-sync     { background: #1565c0; }
        .offline-toast.toast-offline  { background: #37474f; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-tint"></i>
            Forage
        </div>
        <nav class="sidebar-menu">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Tableau de bord
            </a>

            <div class="menu-section">Ventes</div>
            <a href="{{ route('ventes.index') }}" class="{{ request()->routeIs('ventes.*') ? 'active' : '' }}">
                <i class="fas fa-list"></i> Toutes les ventes
            </a>
            <a href="{{ route('ventes.index', ['type' => 'forage']) }}" class="{{ request('type') === 'forage' ? 'active' : '' }}">
                <i class="fas fa-faucet"></i> Forage
            </a>
            <a href="{{ route('ventes.index', ['type' => 'livraison']) }}" class="{{ request('type') === 'livraison' ? 'active' : '' }}">
                <x-icon-tuyau /> Livraisons
            </a>
            <a href="{{ route('ventes.index', ['type' => 'exterieure']) }}" class="{{ request('type') === 'exterieure' ? 'active' : '' }}">
                <i class="fas fa-box"></i> Extérieures
            </a>

            <a href="{{ route('ardoise.index') }}" class="{{ request()->routeIs('ardoise.*') ? 'active' : '' }}">
                <i class="fas fa-pencil-alt" style="color:#f9a825;"></i> Ardoise forage
            </a>

            <div class="menu-section">Gestion</div>
            <a href="{{ route('clients.index') }}" class="{{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Clients
            </a>
            <a href="{{ route('credits.index') }}" class="{{ request()->routeIs('credits.*') ? 'active' : '' }}">
                <i class="fas fa-credit-card"></i> Crédits
            </a>
            <a href="{{ route('depenses.index') }}" class="{{ request()->routeIs('depenses.*') ? 'active' : '' }}">
                <i class="fas fa-wallet"></i> Dépenses
            </a>

            @if(auth()->user()->isAdmin())
            <div class="menu-section">Rapports</div>
            <a href="{{ route('revenus.index') }}" class="{{ request()->routeIs('revenus.*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Revenus
            </a>
            <a href="{{ route('journal.index') }}" class="{{ request()->routeIs('journal.*') ? 'active' : '' }}">
                <i class="fas fa-history"></i> Journal
            </a>
            <a href="{{ route('bilan.index') }}" class="{{ request()->routeIs('bilan.*') ? 'active' : '' }}">
                <i class="fas fa-balance-scale"></i> Bilan Total
            </a>

            <div class="menu-section">Administration</div>
            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fas fa-user-cog"></i> Utilisateurs
            </a>
            @endif
        </nav>
        <div class="sidebar-footer">
            <div class="user-info">
                <strong>{{ auth()->user()->name }}</strong><br>
                <span>{{ auth()->user()->role === 'admin' ? 'Administrateur' : 'Employé' }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
            </form>
        </div>
    </aside>

    <div class="main-content">
        <div class="topbar">
            <div style="display:flex; align-items:center; gap:10px;">
                <button class="hamburger" onclick="toggleSidebar()" aria-label="Menu">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>@yield('page-title', 'Tableau de bord')</h1>
            </div>
            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                <span id="conn-status" class="conn-badge conn-online">
                    <i class="fas fa-wifi"></i> En ligne
                </span>
                <span id="sync-count" class="sync-count"></span>
                <span class="badge-role">
                    <i class="fas fa-user"></i>
                    {{ auth()->user()->role === 'admin' ? 'Admin' : 'Employé' }}
                </span>
            </div>
        </div>
        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <div id="offline-toast" class="offline-toast"></div>

    <script src="/js/offline.js"></script>
    <script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').classList.toggle('open');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('open');
    }
    // Fermer la sidebar quand on clique sur un lien (mobile)
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) closeSidebar();
        });
    });
    </script>
    @stack('scripts')
</body>
</html>
