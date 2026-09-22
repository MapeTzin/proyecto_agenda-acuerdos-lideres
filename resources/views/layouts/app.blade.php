<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Agenda de Acuerdos')</title>
    <script>
        // Check localStorage to apply collapsed state before rendering body
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            document.documentElement.classList.add('sidebar-collapsed-init');
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --dark: #0f172a;
            --light: #f8fafc;
            --glass: rgba(255, 255, 255, 0.8);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f1f5f9;
            color: var(--dark);
            margin: 0;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, var(--dark) 0%, #1e293b 100%);
            color: white;
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 100;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 2.5rem;
            padding: 0 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand i {
            color: var(--primary);
        }

        .nav-item {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            color: #94a3b8;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s;
        }

        .nav-item:hover,
        .nav-item.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-item.active {
            background: var(--primary);
            color: white;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 2.5rem 3.5rem;
            max-width: calc(100% - 260px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Collapsed Sidebar Styles */
        html.sidebar-collapsed-init .sidebar,
        body.sidebar-collapsed .sidebar {
            width: 70px;
            padding: 2rem 0.5rem;
        }

        html.sidebar-collapsed-init .main-content,
        body.sidebar-collapsed .main-content {
            margin-left: 70px;
            max-width: calc(100% - 70px);
        }

        html.sidebar-collapsed-init .sidebar-brand-text,
        body.sidebar-collapsed .sidebar-brand-text {
            display: none !important;
        }

        html.sidebar-collapsed-init .nav-item span,
        body.sidebar-collapsed .nav-item span {
            display: none !important;
        }

        html.sidebar-collapsed-init .nav-item,
        body.sidebar-collapsed .nav-item {
            justify-content: center;
            padding: 0.75rem;
            gap: 0;
        }

        html.sidebar-collapsed-init .sidebar-submenu,
        body.sidebar-collapsed .sidebar-submenu {
            display: none !important;
        }

        html.sidebar-collapsed-init .sidebar-toggle-btn i,
        body.sidebar-collapsed .sidebar-toggle-btn i {
            transform: rotate(180deg);
        }

        .sidebar-toggle-btn {
            position: absolute;
            top: 1.75rem;
            right: -12px;
            width: 24px;
            height: 24px;
            background: var(--primary);
            border: 2px solid var(--dark);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 110;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
            transition: all 0.3s;
        }

        .sidebar-toggle-btn:hover {
            background: var(--primary-dark);
            transform: scale(1.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .premium-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
        }

        h1,
        h2,
        h3 {
            margin: 0;
            color: var(--dark);
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        /* Dashboard specific */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            border-left: 4px solid var(--primary);
        }

        .stat-card.vencidos {
            border-left-color: var(--danger);
        }

        .stat-card.pendientes {
            border-left-color: var(--warning);
        }

        .stat-card.success {
            border-left-color: var(--success);
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            display: block;
        }

        .stat-label {
            color: var(--secondary);
            font-size: 0.9rem;
        }

        /* Custom Table Styles */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 1rem;
            background: #f8fafc;
            color: var(--secondary);
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-red {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-blue {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-yellow {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-green {
            background: #d1fae5;
            color: #065f46;
        }

        .progress-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            width: 100px;
        }

        .progress-fill {
            height: 100%;
            background: var(--primary);
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                padding: 0;
                overflow: hidden;
            }

            .main-content {
                margin-left: 0;
                max-width: 100%;
            }
        }
    </style>
    <style>
        /* Pagination Styles */
        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            gap: 0.25rem;
            align-items: center;
        }

        .page-item .page-link {
            padding: 0.5rem 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            background: white;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .page-item.active .page-link {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .page-item.disabled .page-link {
            color: var(--secondary);
            cursor: not-allowed;
            opacity: 0.6;
        }

        .page-item:not(.active):not(.disabled) .page-link:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        /* Atraso Crítico Animation */
        @keyframes pulse-critical {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.85;
                transform: scale(1.03);
            }
        }

        /* Validation Error Styles */
        .alert-validation-error {
            background: #fff5f5;
            border: 1px solid #feb2b2;
            border-left: 5px solid #f56565;
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            color: #c53030;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Disabled field style */
        .field-locked {
            background: #f1f5f9 !important;
            color: #94a3b8 !important;
            cursor: not-allowed !important;
            border-color: #cbd5e1 !important;
        }
    </style>
    @livewireStyles
</head>

<body>
    <div class="sidebar">
        <!-- Floating Collapse Toggle Button -->
        <button id="sidebarToggle" class="sidebar-toggle-btn" title="Ocultar/Mostrar Panel">
            <i class="fas fa-chevron-left"></i>
        </button>

        <div class="sidebar-brand" style="margin-bottom: 3rem; padding-top: 1rem;">
            <div style="display: flex; flex-direction: column; align-items: center; width: 100%;">
                <div
                    style="border: 2px solid #fff; padding: 10px 15px; display: flex; align-items: center; gap: 8px; border-radius: 4px; position: relative;">
                    <span style="font-size: 1.8rem; font-weight: 800; color: #fff;">M</span>
                    <i class="fas fa-sparkles" style="font-size: 0.8rem; color: var(--primary);"></i>
                    <span style="font-size: 1.8rem; font-weight: 800; color: #fff;">T</span>
                    <span
                        style="position: absolute; top: -10px; right: -10px; font-size: 0.6rem; border: 1px solid #fff; border-radius: 50%; width: 15px; height: 15px; display: flex; align-items: center; justify-content: center;">R</span>
                </div>
                <div class="sidebar-brand-text"
                    style="margin-top: 10px; font-size: 0.9rem; font-weight: 600; color: #fff; letter-spacing: 1px; text-align: center;">
                    MAPE+TZIN<br>
                    <span style="font-size: 0.7rem; opacity: 0.8; font-weight: 400;">GOBIERNO</span>
                </div>
            </div>
        </div>
        @if(auth()->user()->hasRole('Administrador') || auth()->user()->email === 'v.arochi@mapetzin.com' || auth()->user()->email === 'gerencia_serv_gobierno@lesli.com.mx')
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard Corporativo</span>
            </a>
            <a href="{{ route('seguimiento.area') }}"
                class="nav-item {{ request()->routeIs('seguimiento.area') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i>
                <span>Seguimiento Global</span>
            </a>
        @endif
        <a href="{{ route('kpis.areas') }}"
            class="nav-item {{ request()->routeIs('kpis.areas') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i>
            <span>KPI´s AREAS</span>
        </a>
        <a href="{{ route('acuerdos.index') }}"
            class="nav-item {{ (request()->routeIs('acuerdos.index') && !request()->has('area')) ? 'active' : '' }}">
            <i class="fas fa-list-check"></i>
            <span>Mi Listado de Acuerdos</span>
        </a>
        @if(count(auth()->user()->areas) > 1)
            <div class="sidebar-submenu" style="margin-left: 1.5rem; margin-top: -0.25rem; margin-bottom: 0.75rem; display: flex; flex-direction: column; gap: 0.25rem;">
                @foreach(auth()->user()->areas as $area)
                    <a href="{{ route('acuerdos.index', ['area' => $area]) }}"
                        class="nav-item {{ (request()->routeIs('acuerdos.index') && request()->get('area') === $area) ? 'active' : '' }}"
                        style="padding: 0.5rem 0.75rem; font-size: 0.8rem; margin-bottom: 0; gap: 0.5rem;">
                        <i class="fas fa-folder" style="font-size: 0.75rem; color: #818cf8;"></i>
                        <span style="font-weight: 500;">{{ $area }}</span>
                    </a>
                @endforeach
            </div>
        @endif
        <a href="{{ route('acuerdos.historico') }}"
            class="nav-item {{ (request()->routeIs('acuerdos.historico') && !request()->has('area')) ? 'active' : '' }}">
            <i class="fas fa-history"></i>
            <span>Histórico de Acuerdos</span>
        </a>
        @if(count(auth()->user()->areas) > 1)
            <div class="sidebar-submenu" style="margin-left: 1.5rem; margin-top: -0.25rem; margin-bottom: 0.75rem; display: flex; flex-direction: column; gap: 0.25rem;">
                @foreach(auth()->user()->areas as $area)
                    <a href="{{ route('acuerdos.historico', ['area' => $area]) }}"
                        class="nav-item {{ (request()->routeIs('acuerdos.historico') && request()->get('area') === $area) ? 'active' : '' }}"
                        style="padding: 0.5rem 0.75rem; font-size: 0.8rem; margin-bottom: 0; gap: 0.5rem;">
                        <i class="fas fa-folder" style="font-size: 0.75rem; color: #f43f5e;"></i>
                        <span style="font-weight: 500;">{{ $area }}</span>
                    </a>
                @endforeach
            </div>
        @endif
        <a href="{{ route('planeador') }}" class="nav-item {{ request()->routeIs('planeador') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Planeador</span>
        </a>
        @if(auth()->user()->email === 'soporte@mapetzin.com' || auth()->user()->hasRole('Administrador') || auth()->user()->can('roles.manage') || auth()->user()->can('users.manage'))
            <a href="{{ route('roles-permisos.index') }}" class="nav-item {{ request()->routeIs('roles-permisos.*') || request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i>
                <span>Roles y Accesos</span>
            </a>
        @endif
        <div style="margin-top: auto; padding: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1);">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-item"
                    style="width: 100%; background: none; border: none; cursor: pointer; color: #94a3b8; display: flex; align-items: center; gap: 1rem; padding: 0.75rem 1rem; border-radius: 0.5rem; font-family: inherit;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <div>
                <h1>@yield('header_title', 'Bienvenido')</h1>
                <p style="color: var(--secondary); margin-top: 0.25rem;">
                    {{ ucfirst(now()->translatedFormat('l, d \d\e F \d\e\l Y')) }}
                </p>
            </div>
            <div class="user-profile" style="display: flex; align-items: center; gap: 1rem;">
                <div style="text-align: right;">
                    <span style="font-weight: 600; display: block;">{{ Auth::user()->name }}</span>
                    <span
                        style="font-size: 0.8rem; color: var(--secondary);">{{ implode(' y ', Auth::user()->areas) }} | {{ Auth::user()->position }}</span>
                </div>
                <div
                    style="width: 40px; height: 40px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="card"
                style="background: #d1fae5; border-left: 4px solid #10b981; color: #065f46; margin-bottom: 1rem; padding: 1rem;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </div>

    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Apply init class to body once DOM is ready if it was set
            if (document.documentElement.classList.contains('sidebar-collapsed-init')) {
                document.body.classList.add('sidebar-collapsed');
                document.documentElement.classList.remove('sidebar-collapsed-init');
            }

            const toggleBtn = document.getElementById('sidebarToggle');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    const isCollapsed = document.body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('sidebar-collapsed', isCollapsed ? 'true' : 'false');
                });
            }
        });
    </script>
</body>

</html>