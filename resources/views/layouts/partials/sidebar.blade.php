@php
    $user = auth()->user();
    $userAreas = $user?->areas ?? [];
    $hasMultipleAreas = count($userAreas) > 1;

    $appendAreaItems = static function (array &$items, string $routeName, string $prefix = '') use ($hasMultipleAreas, $userAreas): void {
        if (! $hasMultipleAreas) {
            return;
        }
        foreach ($userAreas as $area) {
            $items[] = [
                'name' => $prefix !== '' ? "{$prefix} · {$area}" : $area,
                'url' => route($routeName, ['area' => $area]),
                'icon' => 'folder',
                'active' => request()->routeIs($routeName) && request()->get('area') === $area,
                'is_subitem' => true,
            ];
        }
    };

    $principalItems = [];
    if ($user?->can('dashboard.view')) {
        $principalItems[] = [
            'name' => 'Dashboard Corporativo',
            'url' => route('dashboard'),
            'icon' => 'chart-line',
            'active' => request()->routeIs('dashboard'),
        ];
    }
    if ($user?->can('seguimiento.view')) {
        $principalItems[] = [
            'name' => 'Seguimiento Global',
            'url' => route('seguimiento.area'),
            'icon' => 'layer-group',
            'active' => request()->routeIs('seguimiento.area'),
        ];
    }
    if ($user?->can('kpis.view')) {
        $principalItems[] = [
            'name' => "KPI's Áreas",
            'url' => route('kpis.areas'),
            'icon' => 'chart-pie',
            'active' => request()->routeIs('kpis.areas'),
        ];
    }

    $acuerdosItems = [];
    if ($user?->can('acuerdos.view')) {
        $acuerdosItems[] = [
            'name' => 'Mi Listado de Acuerdos',
            'url' => route('acuerdos.index'),
            'icon' => 'list-check',
            'active' => request()->routeIs('acuerdos.index') && !request()->has('area'),
        ];
        $appendAreaItems($acuerdosItems, 'acuerdos.index');
    }
    if ($user?->can('acuerdos.historico.view')) {
        $acuerdosItems[] = [
            'name' => 'Histórico de Acuerdos',
            'url' => route('acuerdos.historico'),
            'icon' => 'history',
            'active' => request()->routeIs('acuerdos.historico') && !request()->has('area'),
        ];
        $appendAreaItems($acuerdosItems, 'acuerdos.historico', 'Histórico');
    }
    if ($user?->can('planeador.view')) {
        $acuerdosItems[] = [
            'name' => 'Planeador',
            'url' => route('planeador'),
            'icon' => 'calendar',
            'active' => request()->routeIs('planeador'),
        ];
    }

    $adminItems = [];
    if ($user && ($user->email === 'soporte@mapetzin.com' || $user->can('roles.manage') || $user->can('users.manage') || $user->can('users.view'))) {
        $adminItems[] = [
            'name' => 'Roles y Accesos',
            'url' => route('roles-permisos.index'),
            'icon' => 'shield-check',
            'active' => request()->routeIs('roles-permisos.*') || request()->routeIs('users.*'),
        ];
    }

    $menuGroups = array_filter([
        'principal' => !empty($principalItems) ? ['title' => 'Principal', 'items' => $principalItems] : null,
        'acuerdos' => !empty($acuerdosItems) ? ['title' => 'Acuerdos y Agenda', 'items' => $acuerdosItems] : null,
        'admin' => !empty($adminItems) ? ['title' => 'Administración', 'items' => $adminItems] : null,
    ]);
@endphp

<!-- Desktop & Mobile Sidebar Container (Soft UI Dashboard Style — Estilo AuthCenter) -->
<aside
    id="main-sidebar"
    x-data="{
        showTooltip(el, name, group) {
            const isSidebarOpen = (this.$store && this.$store.sidebar) ? this.$store.sidebar.open : this.sidebarOpen;
            if (isSidebarOpen) return;
            const rect = el.getBoundingClientRect();
            window.dispatchEvent(new CustomEvent('sidebar-tooltip:show', {
                detail: {
                    name: name,
                    group: group,
                    top: rect.top + (rect.height / 2)
                }
            }));
        },
        hideTooltip() {
            window.dispatchEvent(new CustomEvent('sidebar-tooltip:hide'));
        }
    }"
    class="fixed top-16 bottom-0 left-0 z-40 bg-[#F8F9FA] dark:bg-slate-900 border-r border-[#E9ECEF] dark:border-slate-800 flex flex-col shadow-xs overflow-x-hidden select-none"
>
    <!-- Cabecera fija de la Sidebar con Botón Toggle Interno -->
    <!-- Modo Contraído: Tarjeta squircle centrada + divisor horizontal -->
    <div class="sidebar-collapsed-item pt-3 pb-1 flex flex-col items-center shrink-0">
        <button
            type="button"
            @click="if (window.Alpine && window.Alpine.store('sidebar')) { window.Alpine.store('sidebar').toggle(); } else { $dispatch('toggle-sidebar'); }"
            @mouseenter="showTooltip($el, 'Expandir menú', 'Navegación')"
            @mouseleave="hideTooltip()"
            title="Expandir menú"
            aria-label="Expandir menú"
            class="sidebar-toggle-btn sidebar-collapsed-card flex items-center justify-center cursor-pointer"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <div class="w-8 h-px bg-slate-200/80 dark:bg-slate-700/80 mt-2.5"></div>
    </div>

    <!-- Modo Expandido: Etiqueta Menú + Botón flecha izquierda para contraer -->
    <div class="sidebar-expanded-item px-3 pt-3 pb-2 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between shrink-0">
        <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">Menú</span>
        <button
            type="button"
            @click="if (window.Alpine && window.Alpine.store('sidebar')) { window.Alpine.store('sidebar').toggle(); } else { $dispatch('toggle-sidebar'); }"
            title="Contraer menú"
            aria-label="Contraer menú"
            class="w-8 h-8 rounded-lg flex items-center justify-center bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700 shadow-xs hover:shadow-sm transition-all cursor-pointer"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </button>
    </div>

    <!-- Navigation Menu Wrapper -->
    <nav @scroll="hideTooltip()" class="flex-1 overflow-y-auto overflow-x-hidden pb-3 text-left transition-all duration-300" :class="sidebarOpen ? 'px-3 pt-1 space-y-1.5' : 'px-2.5 pt-2 space-y-2.5'">
        @foreach ($menuGroups as $groupKey => $group)
            <div x-data="{ open: true }" class="space-y-1 text-left">

                <!-- Group Header (Expanded Mode) -->
                <button
                    x-show="sidebarOpen"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    @click="open = !open"
                    type="button"
                    class="sidebar-expanded-item w-full flex items-center justify-between px-2.5 pt-1 pb-0.5 text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 transition-colors select-none cursor-pointer group"
                >
                    <span class="truncate">{{ $group['title'] }}</span>
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 dark:text-slate-500 dark:group-hover:text-slate-300 transition-transform duration-200" :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Group Divider (Collapsed/Slim Mode — únicamente entre grupos) -->
                @if (!$loop->first)
                    <div
                        x-show="!sidebarOpen"
                        @mouseenter="showTooltip($el, '{{ addslashes($group['title']) }}', 'Módulos')"
                        @mouseleave="hideTooltip()"
                        class="sidebar-collapsed-item py-0.5 flex flex-col items-center justify-center cursor-default"
                    >
                        <div class="w-6 h-px bg-slate-200/75 dark:bg-slate-700/70"></div>
                    </div>
                @endif

                <!-- Sub-Items List (Expanded Mode) -->
                <div x-show="sidebarOpen && open" class="sidebar-expanded-item space-y-1 text-left">
                    @foreach ($group['items'] as $item)
                        <a
                            href="{{ $item['url'] }}"
                            class="sidebar-item-link flex items-center justify-between px-2.5 py-1 rounded-xl transition-all duration-200 group {{ !empty($item['is_subitem']) ? 'pl-5' : '' }} {{ $item['active'] ? 'is-active-item bg-white dark:bg-slate-800 shadow-sm border border-slate-100/90 dark:border-slate-700/60' : 'hover:bg-white/80 dark:hover:bg-slate-800/60 text-slate-600 dark:text-slate-400' }}"
                        >
                            <div class="flex items-center gap-2.5 text-left min-w-0">
                                <div class="sidebar-icon-box w-7 h-7 rounded-lg flex items-center justify-center shrink-0 transition-all duration-200 {{ $item['active'] ? 'active-gradient-box text-white' : 'bg-white dark:bg-slate-800 shadow-xs border border-slate-200/70 dark:border-slate-700/70 text-[#1A365D] dark:text-slate-300 group-hover:shadow-sm group-hover:scale-105' }}">
                                    <x-nav-icon :name="$item['icon']" class="w-4 h-4 shrink-0 transition-colors {{ $item['active'] ? 'text-white' : '' }}" />
                                </div>
                                <span class="text-sm font-semibold truncate {{ $item['active'] ? 'text-slate-800 dark:text-white font-bold' : 'text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white' }}">{{ $item['name'] }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Sub-Items List (Slim / Collapsed Mode: Tarjetas Squircle Directas) -->
                <div x-show="!sidebarOpen" class="sidebar-collapsed-item space-y-2.5 flex flex-col items-center">
                    @foreach ($group['items'] as $item)
                        <div class="relative w-full flex items-center justify-center">
                            <a
                                href="{{ $item['url'] }}"
                                @mouseenter="showTooltip($el, '{{ addslashes($item['name']) }}', '{{ addslashes($group['title']) }}')"
                                @mouseleave="hideTooltip()"
                                class="sidebar-item-link sidebar-collapsed-card flex items-center justify-center transition-all duration-200 {{ $item['active'] ? 'is-active-item text-white' : '' }}"
                            >
                                <x-nav-icon :name="$item['icon']" class="w-5 h-5 shrink-0" />
                            </a>
                        </div>
                    @endforeach
                </div>

            </div>
        @endforeach
    </nav>
</aside>

<!-- Floating Tooltip in Collapsed Mode (Positioned outside <aside> to avoid overflow clipping) -->
<div
    x-cloak
    x-data="{
        show: false,
        name: '',
        group: '',
        top: 0,
        init() {
            window.addEventListener('sidebar-tooltip:show', (e) => {
                this.show = true;
                this.name = e.detail.name;
                this.group = e.detail.group;
                this.top = e.detail.top;
            });
            window.addEventListener('sidebar-tooltip:hide', () => {
                this.show = false;
            });
            window.addEventListener('scroll', () => {
                this.show = false;
            }, true);
            window.addEventListener('resize', () => {
                this.show = false;
            });
        }
    }"
    x-show="!sidebarOpen && show"
    x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0 translate-x-1"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-1"
    :style="`top: ${top}px;`"
    class="fixed left-[80px] -translate-y-1/2 z-[70] pointer-events-none drop-shadow-xl"
>
    <div class="relative bg-slate-900/95 dark:bg-slate-800/95 backdrop-blur-md text-white py-1.5 px-3 rounded-xl border border-slate-700/80 whitespace-nowrap shadow-2xl">
        <div class="absolute top-1/2 -left-1.5 -translate-y-1/2 w-3 h-3 bg-slate-900/95 dark:bg-slate-800/95 border-l border-b border-slate-700/80 transform rotate-45 pointer-events-none"></div>
        <span class="text-[11px] font-black uppercase tracking-wider text-slate-400 block" x-text="group"></span>
        <span class="text-sm font-bold text-white block" x-text="name"></span>
    </div>
</div>

<style>
    /* =========================================================================
       Soft UI Dashboard Sidebar Style (Suite SIAMt)
       ========================================================================= */
    #main-sidebar {
        background-color: #F8F9FA !important;
        border-right: 1px solid #E9ECEF !important;
    }
    html.dark #main-sidebar {
        background-color: #131b2e !important;
        border-right: 1px solid rgba(148, 163, 184, 0.12) !important;
        color: #dae2fd !important;
    }

    #main-sidebar nav > div {
        padding-top: 1px;
        padding-bottom: 1px;
        margin-top: 0;
        margin-bottom: 0;
    }
    #main-sidebar nav > div + div {
        margin-top: 0.45rem;
    }
    #main-sidebar button.sidebar-expanded-item {
        padding-top: 0.2rem;
        padding-bottom: 0.15rem;
        margin-top: 0;
        margin-bottom: 2px;
    }
    #main-sidebar div.sidebar-expanded-item > a + a {
        margin-top: 0.25rem;
    }
    #main-sidebar div.sidebar-expanded-item a.sidebar-item-link {
        padding-top: 0.275rem;
        padding-bottom: 0.275rem;
        margin-top: 0;
        margin-bottom: 0;
    }
    #main-sidebar .sidebar-icon-box {
        width: 1.7rem;
        height: 1.7rem;
        border-radius: 9px;
    }

    /* Tarjetas squircle directas en modo contraído (2.625rem x 2.625rem = 42px x 42px) */
    #main-sidebar .sidebar-collapsed-card {
        width: 2.625rem;
        height: 2.625rem;
        padding: 0;
        border-radius: 10px !important;
        background-color: #FFFFFF;
        border: 1px solid rgba(226, 232, 240, 0.85);
        box-shadow: 0 2px 6px rgba(0, 32, 69, 0.06);
        color: #1A365D;
        transition: all 0.2s ease;
    }
    #main-sidebar .sidebar-collapsed-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 32, 69, 0.1);
    }
    html.dark #main-sidebar .sidebar-collapsed-card {
        background-color: #1E293B;
        border-color: rgba(148, 163, 184, 0.16);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.22);
        color: #CBD5E1;
    }

    /* Ítem Activo en Modo Contraído: Tarjeta completa con degradado del icono */
    #main-sidebar a.sidebar-collapsed-card.is-active-item {
        background: var(--sidebar-active-gradient, linear-gradient(135deg, #460046 0%, #CC3D77 50%, #FF558A 100%)) !important;
        box-shadow: var(--sidebar-active-shadow, 0 6px 16px rgba(204, 61, 119, 0.35)) !important;
        border-color: transparent !important;
        color: #FFFFFF !important;
    }
    #main-sidebar a.sidebar-collapsed-card.is-active-item svg {
        color: #FFFFFF !important;
        stroke: #FFFFFF !important;
    }

    /* Caja de icono con degradado del icono (Modo Expandido) */
    #main-sidebar .active-gradient-box,
    #main-sidebar div.sidebar-expanded-item a.is-active-item .sidebar-icon-box {
        background: var(--sidebar-active-gradient, linear-gradient(135deg, #460046 0%, #CC3D77 50%, #FF558A 100%)) !important;
        box-shadow: var(--sidebar-active-shadow, 0 6px 16px rgba(204, 61, 119, 0.35)) !important;
        color: #FFFFFF !important;
    }
    #main-sidebar .active-gradient-box svg,
    #main-sidebar div.sidebar-expanded-item a.is-active-item .sidebar-icon-box svg {
        color: #FFFFFF !important;
    }
    #main-sidebar .active-gradient-box svg[fill="none"],
    #main-sidebar div.sidebar-expanded-item a.is-active-item .sidebar-icon-box svg[fill="none"] {
        fill: none !important;
        stroke: #FFFFFF !important;
    }

    /* Ítem Activo Expandido: Tarjeta Blanca Soft UI */
    #main-sidebar div.sidebar-expanded-item a.is-active-item {
        background-color: #FFFFFF !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05) !important;
    }
    html.dark #main-sidebar div.sidebar-expanded-item a.is-active-item {
        background-color: #1E293B !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25) !important;
    }

    /* Ítems Inactivos Expandidos */
    #main-sidebar div.sidebar-expanded-item a:not(.is-active-item) .sidebar-icon-box {
        background-color: #FFFFFF !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
    }
    html.dark #main-sidebar div.sidebar-expanded-item a:not(.is-active-item) .sidebar-icon-box {
        background-color: #1E293B !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) !important;
    }
    #main-sidebar div.sidebar-expanded-item a:not(.is-active-item) .sidebar-icon-box svg {
        color: #1A365D !important;
    }
    html.dark #main-sidebar div.sidebar-expanded-item a:not(.is-active-item) .sidebar-icon-box svg {
        color: #CBD5E1 !important;
    }
    #main-sidebar div.sidebar-expanded-item a:not(.is-active-item) .sidebar-icon-box svg[fill="none"] {
        stroke: currentColor !important;
        fill: none !important;
    }

    /* Scrollbar suave en sidebar */
    #main-sidebar nav::-webkit-scrollbar {
        width: 4px;
    }
    #main-sidebar nav::-webkit-scrollbar-thumb {
        background: rgba(0, 32, 69, 0.15);
        border-radius: 9999px;
    }
    html.dark #main-sidebar nav::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.15);
    }
</style>
