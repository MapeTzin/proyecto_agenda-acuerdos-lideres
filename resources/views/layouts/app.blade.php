<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{
        currentTheme: (function() {
            const t = localStorage.getItem('theme');
            return (t === 'dark') ? 'dark' : 'light';
        })(),
        get darkMode() {
            return this.currentTheme === 'dark';
        },
        themeToastVisible: false,
        themeToastText: '',
        themeToastTimer: null,

        cycleTheme() {
            const next = this.currentTheme === 'light' ? 'dark' : 'light';
            this.setTheme(next);
        },

        setTheme(t, notify = true) {
            this.currentTheme = t;
            localStorage.setItem('theme', t);
            const root = document.documentElement;
            root.classList.remove('dark', 'theme-dark');
            if (t === 'dark') {
                root.classList.add('dark', 'theme-dark');
                root.style.colorScheme = 'dark';
            } else {
                root.style.colorScheme = 'light';
            }

            if (notify) {
                const labels = {
                    'light': 'Tema: Claro Institucional',
                    'dark': 'Tema: Nocturno Institucional'
                };
                this.showThemeToast(labels[t] || t);
            }
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: t } }));
        },

        showThemeToast(label) {
            this.themeToastText = label;
            this.themeToastVisible = true;
            if (this.themeToastTimer) clearTimeout(this.themeToastTimer);
            this.themeToastTimer = setTimeout(() => {
                this.themeToastVisible = false;
            }, 2200);
        },

        sidebarOpen: localStorage.getItem('sidebar_open') === 'true',
        toggleSidebar() {
            if (window.Alpine && window.Alpine.store('sidebar')) {
                window.Alpine.store('sidebar').toggle();
            } else {
                this.sidebarOpen = !this.sidebarOpen;
                localStorage.setItem('sidebar_open', this.sidebarOpen ? 'true' : 'false');
                document.documentElement.classList.toggle('sidebar-collapsed', !this.sidebarOpen);
            }
        }
    }"
    x-init="
        $watch('sidebarOpen', val => {
            localStorage.setItem('sidebar_open', val ? 'true' : 'false');
            document.documentElement.classList.toggle('sidebar-collapsed', !val);
            if (window.Alpine && window.Alpine.store('sidebar') && window.Alpine.store('sidebar').open !== val) {
                window.Alpine.store('sidebar').open = val;
            }
            window.dispatchEvent(new CustomEvent('sidebar-state-changed', { detail: { open: val } }));
        });
        window.addEventListener('toggle-sidebar', () => {
            if (window.Alpine && window.Alpine.store('sidebar')) {
                window.Alpine.store('sidebar').toggle();
            } else {
                sidebarOpen = !sidebarOpen;
            }
        });
        window.addEventListener('sidebar-state-changed', (e) => {
            if (e.detail && typeof e.detail.open === 'boolean' && sidebarOpen !== e.detail.open) {
                sidebarOpen = e.detail.open;
            }
        });
    "
>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Agenda de Acuerdos | SIAMt')</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg viewBox='0 0 2325 2290' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3ClinearGradient x1='1175.85' y1='16.8703' x2='3225.15' y2='2459.13' gradientUnits='userSpaceOnUse' id='f'%3E%3Cstop offset='0' stop-color='%23460046'/%3E%3Cstop offset='0.5' stop-color='%23CC3D77'/%3E%3Cstop offset='1' stop-color='%23FF558A'/%3E%3C/linearGradient%3E%3C/defs%3E%3Cg transform='translate(-1038 -93)'%3E%3Cpath d='M1059.5 391.488C1059.5 238.511 1183.5 114.5 1336.46 114.5L3064.54 114.5C3217.5 114.5 3341.5 238.511 3341.5 391.488L3341.5 2084.51C3341.5 2237.49 3217.5 2361.5 3064.54 2361.5L1336.46 2361.5C1183.5 2361.5 1059.5 2237.49 1059.5 2084.51Z' stroke='%23FFFFFF' stroke-width='42.8365' fill='url(%23f)'/%3E%3Cpath d='M1648.77 701.295 1825.48 712.005 1820.9 787.491C1819.07 817.811 1842.16 843.88 1872.48 845.718 1902.8 847.555 1928.87 824.466 1930.7 794.146L1935.28 718.659 2525.45 754.428 2520.87 829.915C2519.03 860.235 2542.12 886.304 2572.44 888.141 2602.76 889.979 2628.83 866.889 2630.67 836.569L2635.24 761.083 2811.95 771.793C2876.38 775.698 2925.45 831.094 2921.54 895.524L2867.06 1794.5C2863.15 1858.93 2807.75 1907.99 2743.32 1904.09L1580.15 1833.59C1515.72 1829.69 1466.65 1774.29 1470.56 1709.86L1525.04 810.885C1528.95 746.455 1584.34 697.39 1648.77 701.295Z' stroke='%23FFFFFF' stroke-width='45.8333' fill='%23FFFFFF' fill-opacity='0.2'/%3E%3Cpath d='M1615.47 1179.69C1616.57 1161.68 1632.04 1147.98 1650.04 1149.07L2756.61 1216.13C2774.61 1217.22 2788.32 1232.7 2787.23 1250.7L2757.28 1744.8C2756.19 1762.8 2740.71 1776.51 2722.71 1775.42L1616.15 1708.35C1598.15 1707.26 1584.44 1691.78 1585.53 1673.78Z' stroke='%23FFFFFF' stroke-width='45.8333' fill='%23000000' fill-opacity='0.25'/%3E%3C/g%3E%3C/svg%3E">

    <!-- Prevención instantánea de parpadeo (Anti-FOUC) para tema y sidebar -->
    <script>
        (function() {
            try {
                const t = localStorage.getItem('theme') || 'light';
                const root = document.documentElement;
                root.classList.remove('dark', 'theme-dark');
                if (t === 'dark') {
                    root.classList.add('dark', 'theme-dark');
                    root.style.colorScheme = 'dark';
                } else {
                    root.style.colorScheme = 'light';
                }

                const sidebarOpen = localStorage.getItem('sidebar_open') === 'true';
                if (!sidebarOpen) {
                    root.classList.add('sidebar-collapsed');
                } else {
                    root.classList.remove('sidebar-collapsed');
                }
            } catch (e) {}
        })();
    </script>

    <!-- Tipografías Institucionales (Inter + Righteous) y FontAwesome (compatibilidad con vistas hijas) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Righteous&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS v4 -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @custom-variant dark (&:where(.dark, .dark *));

        @theme {
            --font-sans: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-mono: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
    </style>

    <!-- Registro de Alpine Store para Sidebar Centralizado -->
    <script>
        document.addEventListener('alpine:init', () => {
            const initOpen = localStorage.getItem('sidebar_open') === 'true';

            if (window.Alpine) {
                window.Alpine.store('sidebar', {
                    open: initOpen,
                    toggle() {
                        this.open = !this.open;
                        localStorage.setItem('sidebar_open', this.open ? 'true' : 'false');
                        document.documentElement.classList.toggle('sidebar-collapsed', !this.open);
                        window.dispatchEvent(new CustomEvent('sidebar-state-changed', { detail: { open: this.open } }));
                    },
                    close() {
                        this.open = false;
                        localStorage.setItem('sidebar_open', 'false');
                        document.documentElement.classList.add('sidebar-collapsed');
                        window.dispatchEvent(new CustomEvent('sidebar-state-changed', { detail: { open: false } }));
                    },
                    openSidebar() {
                        this.open = true;
                        localStorage.setItem('sidebar_open', 'true');
                        document.documentElement.classList.remove('sidebar-collapsed');
                        window.dispatchEvent(new CustomEvent('sidebar-state-changed', { detail: { open: true } }));
                    }
                });
            }
        });
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        :root {
            --primary: #002045;
            --primary-dark: #1A365D;
            --secondary: #545f72;
            --success: #16a34a;
            --danger: #dc2626;
            --warning: #ca8a04;
            --info: #0284c7;
            --dark: #181C1E;
            --light: #F7FAFC;

            --radius-xs: 10px !important;
            --radius-sm: 10px !important;
            --radius-md: 10px !important;
            --radius-lg: 10px !important;
            --radius-xl: 10px !important;
            --radius-2xl: 10px !important;
            --radius-3xl: 10px !important;
            --radius-4xl: 10px !important;
        }

        html, body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
            background-color: #F7FAFC !important;
            color: #181C1E !important;
            min-height: 100vh;
            margin: 0;
        }

        html.dark, html.dark body {
            background-color: #0b1326 !important;
            color: #dae2fd !important;
        }

        .siamt-header {
            background-color: #460046;
            backdrop-filter: blur(12px);
        }
        html.dark .siamt-header {
            background-color: rgba(11, 19, 38, 0.95) !important;
            border-bottom: 1px solid rgba(148, 163, 184, 0.12) !important;
        }

        /* Números tabulares */
        .tabular-nums, table td, table th, [data-numeric] {
            font-variant-numeric: tabular-nums !important;
            font-feature-settings: "tnum" 1, "cv05" 1 !important;
        }

        html.dark h1, html.dark h2, html.dark h3, html.dark h4 {
            color: #f8fafc;
        }

        /* Inputs y controles de formulario en Modo Nocturno (Dark_Theme.md) */
        html.dark input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]),
        html.dark select,
        html.dark textarea,
        html.dark .form-control {
            background-color: #0b132b !important;
            color: #f8fafc !important;
            border-color: rgba(148, 163, 184, 0.2) !important;
        }
        html.dark input::placeholder,
        html.dark textarea::placeholder {
            color: #64748b !important;
        }
        html.dark input:not([type="checkbox"]):not([type="radio"]):focus,
        html.dark select:focus,
        html.dark textarea:focus,
        html.dark .form-control:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
            outline: none;
        }
        html.dark input[type="checkbox"],
        html.dark input[type="radio"] {
            background-color: #0b132b;
            border-color: rgba(148, 163, 184, 0.4);
            accent-color: #3b82f6;
        }

        /* =========================================================================
           Sidebar y Layout Principal Institucional (CLS = 0)
           ========================================================================= */
        #main-sidebar {
            width: 280px;
            transform: translateX(0);
            display: flex !important;
            z-index: 40;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #main-content-wrapper {
            padding-left: 280px;
            transition: padding-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        html.sidebar-collapsed #main-sidebar {
            width: 72px !important;
            transform: translateX(0) !important;
        }
        html.sidebar-collapsed #main-content-wrapper {
            padding-left: 72px !important;
        }

        @media (max-width: 767px) {
            #main-sidebar {
                width: 280px !important;
                transform: translateX(0);
            }
            html.sidebar-collapsed #main-sidebar {
                transform: translateX(-100%) !important;
            }
            #main-content-wrapper,
            html.sidebar-collapsed #main-content-wrapper {
                padding-left: 0 !important;
            }
        }

        html.sidebar-collapsed #main-sidebar .sidebar-expanded-item {
            display: none !important;
        }
        html:not(.sidebar-collapsed) #main-sidebar .sidebar-collapsed-item {
            display: none !important;
        }

        /* Estandarización de Border Radius (10px) */
        .rounded,
        .rounded-xs,
        .rounded-sm,
        .rounded-md,
        .rounded-lg,
        .rounded-xl,
        .rounded-2xl,
        .rounded-3xl,
        .rounded-4xl,
        button,
        input,
        select,
        textarea,
        .badge-pill {
            border-radius: 10px !important;
        }
        .rounded-none,
        .excel-table-wrapper {
            border-radius: 0px !important;
        }

        /* =========================================================================
           Compatibilidad con vistas existentes de Agenda de Acuerdos + Modo Nocturno
           ========================================================================= */
        .card {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e2e8f0;
        }
        html.dark .card {
            background: #1e293b !important;
            border-color: rgba(148, 163, 184, 0.12) !important;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.45) !important;
            color: #dae2fd !important;
        }

        .premium-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border: 1px solid #e2e8f0;
        }
        html.dark .premium-card {
            background: rgba(30, 41, 59, 0.92) !important;
            border-color: rgba(148, 163, 184, 0.12) !important;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.45) !important;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #460046 0%, #CC3D77 50%, #FF558A 100%);
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(204, 61, 119, 0.28);
        }
        .btn-primary:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(204, 61, 119, 0.38);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #ffffff;
            padding: 1.5rem;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #002045;
        }
        html.dark .stat-card {
            background: #1e293b !important;
            border-color: rgba(148, 163, 184, 0.12) !important;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.45) !important;
            border-left-color: #adc6ff !important;
        }
        .stat-card.vencidos { border-left-color: var(--danger); }
        .stat-card.pendientes { border-left-color: var(--warning); }
        .stat-card.success { border-left-color: var(--success); }
        html.dark .stat-card.vencidos { border-left-color: #f87171 !important; }
        html.dark .stat-card.pendientes { border-left-color: #facc15 !important; }
        html.dark .stat-card.success { border-left-color: #34d399 !important; }
        .stat-value { font-size: 1.8rem; font-weight: 700; display: block; }
        html.dark .stat-value { color: #f8fafc; }
        .stat-label { color: #64748b; font-size: 0.9rem; }
        html.dark .stat-label { color: #94a3b8; }

        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left;
            padding: 0.875rem 1rem;
            background: #f8fafc;
            color: #64748b;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 1px solid #e2e8f0;
        }
        td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        html.dark th {
            background: #0f172a !important;
            color: #94a3b8 !important;
            border-bottom-color: rgba(148, 163, 184, 0.16) !important;
        }
        html.dark tbody tr {
            background-color: #1e293b;
            transition: background-color 0.15s ease;
        }
        html.dark tbody tr:hover {
            background-color: #253349 !important;
        }
        html.dark td {
            color: #dae2fd;
            border-bottom-color: rgba(148, 163, 184, 0.08) !important;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
        }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-yellow { background: #fef3c7; color: #92400e; }
        .badge-green { background: #d1fae5; color: #065f46; }
        html.dark .badge-red { background: rgba(248, 113, 113, 0.15); color: #f87171; border: 1px solid rgba(248, 113, 113, 0.3); }
        html.dark .badge-blue { background: rgba(129, 140, 248, 0.15); color: #818cf8; border: 1px solid rgba(129, 140, 248, 0.3); }
        html.dark .badge-yellow { background: rgba(250, 204, 21, 0.15); color: #facc15; border: 1px solid rgba(250, 204, 21, 0.3); }
        html.dark .badge-green { background: rgba(52, 211, 153, 0.15); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.3); }

        .progress-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            width: 100px;
        }
        html.dark .progress-bar { background: #2d3449; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #460046 0%, #CC3D77 50%, #FF558A 100%); }

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
            border-radius: 10px;
            background: white;
            color: #460046;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }
        html.dark .page-item .page-link {
            background: #1e293b;
            border-color: rgba(148, 163, 184, 0.2);
            color: #f8fafc;
        }
        html.dark .page-item:not(.active):not(.disabled) .page-link:hover {
            background: #253349;
        }
        .page-item.active .page-link {
            background: linear-gradient(135deg, #460046 0%, #CC3D77 50%, #FF558A 100%);
            color: white;
            border-color: #CC3D77;
        }
        .page-item.disabled .page-link {
            color: #64748b;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .alert-validation-error {
            background: #fff5f5;
            border: 1px solid #feb2b2;
            border-left: 5px solid #f56565;
            border-radius: 10px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            color: #c53030;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        html.dark .alert-validation-error {
            background: rgba(147, 0, 10, 0.25);
            border-color: rgba(255, 180, 171, 0.35);
            border-left-color: #f87171;
            color: #ffdad6;
        }

        .field-locked {
            background: #f1f5f9 !important;
            color: #94a3b8 !important;
            cursor: not-allowed !important;
            border-color: #cbd5e1 !important;
        }
        html.dark .field-locked {
            background: #171f33 !important;
            color: #64748b !important;
            border-color: rgba(148, 163, 184, 0.16) !important;
        }

        @keyframes pulse-critical {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.85; transform: scale(1.03); }
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>

<body class="min-h-screen font-sans antialiased overflow-x-hidden transition-colors duration-200">

    <!-- Navbar Superior (100% ancho, h-16, z-50) -->
    @include('layouts.partials.navbar')

    <div class="min-h-screen flex bg-transparent pt-16">
        <!-- Sidebar Lateral (top-16, z-40) -->
        @include('layouts.partials.sidebar')

        <!-- Mobile Backdrop Overlay -->
        <div
            x-show="sidebarOpen"
            @click="if (window.Alpine && window.Alpine.store('sidebar')) { window.Alpine.store('sidebar').close(); } else { sidebarOpen = false; }"
            x-cloak
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-[35] md:hidden"
        ></div>

        <!-- Main Content Wrapper (CLS = 0) -->
        <div id="main-content-wrapper" class="flex-1 flex flex-col min-w-0">
            <main class="flex-1 py-6 px-1 sm:px-2 w-[95%] max-w-[95%] mx-auto">

                @hasSection('header_title')
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                        <div>
                            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                                @yield('header_title')
                            </h1>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ ucfirst(now()->translatedFormat('l, d \d\e F \d\e\l Y')) }}
                            </p>
                        </div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-semibold">
                        <i class="fas fa-check-circle shrink-0"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm font-semibold">
                        <i class="fas fa-exclamation-circle shrink-0"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
                {{ $slot ?? '' }}
            </main>
        </div>
    </div>

    <!-- Toast Flotante de Cambio de Tema -->
    <div
        x-cloak
        x-show="themeToastVisible"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="fixed bottom-6 right-6 z-[99999] pointer-events-none flex items-center gap-3 px-4 py-2.5 rounded-2xl bg-slate-900/95 dark:bg-slate-800/95 text-white text-xs font-bold shadow-2xl backdrop-blur-xl border border-white/15 select-none"
    >
        <span class="w-2.5 h-2.5 rounded-full animate-pulse" :class="{
            'bg-amber-400 shadow-[0_0_10px_#f59e0b]': currentTheme === 'light',
            'bg-blue-400 shadow-[0_0_10px_#60a5fa]': currentTheme === 'dark'
        }"></span>
        <span x-text="themeToastText" class="tracking-wide"></span>
    </div>

    <!-- Detector Anti-bfcache -->
    <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>

    @livewireScripts
    @stack('scripts')
    @yield('scripts')
</body>

</html>
