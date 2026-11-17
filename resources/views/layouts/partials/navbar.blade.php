<header
    class="siamt-header fixed top-0 left-0 right-0 z-50 h-16 w-full bg-[#460046]/95 backdrop-blur-md border-b border-[#641064] shadow-md text-white transition-colors duration-300"
>
    <div class="h-full px-4 sm:px-6 flex items-center justify-between gap-4 relative">

        <!-- Left Side: Mobile Hamburger Toggle (< 768px) + System Brand -->
        <div class="flex items-center gap-3.5 relative z-10">
            <!-- Botón Hamburguesa Móvil (En desktop vive dentro de la propia Sidebar) -->
            <button
                type="button"
                @click="if (window.Alpine && window.Alpine.store('sidebar')) { window.Alpine.store('sidebar').toggle(); } else { $dispatch('toggle-sidebar'); }"
                class="flex md:hidden items-center justify-center p-2 rounded-2xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200/80 dark:border-slate-700 shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer select-none group"
                title="Alternar Menú Lateral"
                aria-label="Alternar Menú Lateral"
            >
                <svg class="w-5 h-5 text-slate-700 dark:text-slate-200 group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Brand Icon & Name (Tipografía Righteous) -->
            <a
                href="{{ auth()->user()?->can('dashboard.view') ? route('dashboard') : route('acuerdos.index') }}"
                class="flex items-center gap-2.5 group text-left"
            >
                <x-icons.agenda-logo class="w-9 h-9 shrink-0 group-hover:scale-105 transition-transform duration-200" />
                <div class="flex items-center gap-2">
                    <span class="text-base font-normal tracking-wide text-white whitespace-nowrap" style="font-family: 'Righteous', sans-serif;">Agenda de Acuerdos</span>
                </div>
            </a>
        </div>

        <!-- Right Side: SIAMt Home, Theme Switcher & User Profile Dropdown -->
        <div class="flex items-center gap-3 relative z-10 shrink-0">

            <!-- Botón de Regreso al Home de SIAMt (Solo Ícono) -->
            <a
                href="{{ rtrim(config('services.auth_center.base_url', 'https://siamt.mt.local/'), '/') }}/"
                data-no-spa
                class="flex items-center justify-center p-2 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 shadow-sm hover:shadow-md transition-all duration-200 active:scale-95 shrink-0"
                title="Ir al Portal SIAMt"
                aria-label="Ir al Portal SIAMt"
            >
                <svg class="w-4 h-4" viewBox="0 0 2297 2296" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <g transform="translate(-1052 -90)">
                        <path d="M2200 318.818 3118.68 1237.5 2200 2156.18 1281.32 1237.5Z" stroke="#336699" stroke-width="68.75" stroke-miterlimit="8" fill="none" fill-rule="evenodd"/>
                        <path d="M1086.5 1238C1086.5 1130.58 1173.8 1043.5 1281.5 1043.5 1389.2 1043.5 1476.5 1130.58 1476.5 1238 1476.5 1345.42 1389.2 1432.5 1281.5 1432.5 1173.8 1432.5 1086.5 1345.42 1086.5 1238Z" stroke="#61CBF4" stroke-width="68.75" stroke-miterlimit="8" fill="#3B536D" fill-rule="evenodd"/>
                        <path d="M2924.5 1238C2924.5 1130.58 3011.8 1043.5 3119.5 1043.5 3227.2 1043.5 3314.5 1130.58 3314.5 1238 3314.5 1345.42 3227.2 1432.5 3119.5 1432.5 3011.8 1432.5 2924.5 1345.42 2924.5 1238Z" stroke="#61CBF4" stroke-width="68.75" stroke-miterlimit="8" fill="#3B536D" fill-rule="evenodd"/>
                        <path d="M2005.5 2156.5C2005.5 2048.8 2092.8 1961.5 2200.5 1961.5 2308.2 1961.5 2395.5 2048.8 2395.5 2156.5 2395.5 2264.2 2308.2 2351.5 2200.5 2351.5 2092.8 2351.5 2005.5 2264.2 2005.5 2156.5Z" stroke="#61CBF4" stroke-width="68.75" stroke-miterlimit="8" fill="#3B536D" fill-rule="evenodd"/>
                        <path d="M2005.5 319.5C2005.5 211.804 2092.8 124.5 2200.5 124.5 2308.2 124.5 2395.5 211.804 2395.5 319.5 2395.5 427.196 2308.2 514.5 2200.5 514.5 2092.8 514.5 2005.5 427.196 2005.5 319.5Z" stroke="#61CBF4" stroke-width="68.75" stroke-miterlimit="8" fill="#3B536D" fill-rule="evenodd"/>
                        <path d="M1875 1237.5C1875 1058.28 2020.51 913 2200 913 2379.49 913 2525 1058.28 2525 1237.5 2525 1416.72 2379.49 1562 2200 1562 2020.51 1562 1875 1416.72 1875 1237.5Z" stroke="#61CBF4" stroke-width="206.25" stroke-miterlimit="8" fill="#CAEEFB" fill-rule="evenodd"/>
                        <path d="M1476 1238 1875 1238" stroke="#61CBF4" stroke-width="91.6667" stroke-miterlimit="8" fill="none" fill-rule="evenodd"/>
                        <path d="M2525 1223 2924 1223" stroke="#61CBF4" stroke-width="91.6667" stroke-miterlimit="8" fill="none" fill-rule="evenodd"/>
                        <path d="M2200 514 2200 912.997" stroke="#61CBF4" stroke-width="91.6667" stroke-miterlimit="8" fill="none" fill-rule="evenodd"/>
                        <path d="M2200 1562 2200 1961" stroke="#61CBF4" stroke-width="91.6667" stroke-miterlimit="8" fill="none" fill-rule="evenodd"/>
                    </g>
                </svg>
            </a>

            <!-- Theme Switcher Button (Claro / Nocturno) -->
            <button
                type="button"
                @click="cycleTheme()"
                class="flex items-center justify-center p-2 rounded-2xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200/80 dark:border-slate-700 shadow-sm hover:shadow-md transition-all duration-200 active:scale-95 cursor-pointer select-none relative group shrink-0"
                :title="currentTheme === 'light' ? 'Tema: Claro Institucional (Clic para Modo Nocturno)' : 'Tema: Nocturno Institucional (Clic para Modo Claro)'"
                aria-label="Alternar tema del sistema"
            >
                <!-- Ícono Modo Claro: Luna -->
                <svg x-show="currentTheme === 'light'" class="w-4 h-4 text-slate-700 dark:text-slate-200 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>

                <!-- Ícono Modo Nocturno: Sol -->
                <svg x-show="currentTheme === 'dark'" x-cloak class="w-4 h-4 text-amber-500 transition-transform duration-300 group-hover:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </button>

            <!-- User Profile Dropdown Pill -->
            <div class="relative shrink-0" x-data="{ userMenuOpen: false }">
                <button
                    type="button"
                    @click="userMenuOpen = !userMenuOpen"
                    @click.outside="userMenuOpen = false"
                    class="flex items-center gap-2.5 pl-1.5 pr-3 py-1.5 rounded-2xl bg-white dark:bg-slate-800 text-slate-800 dark:text-white border border-slate-200/80 dark:border-slate-700 shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer select-none group shrink-0"
                >
                    <!-- Avatar Squircle con Degradado del Icono (#460046 -> #CC3D77 -> #FF558A) -->
                    <div
                        class="w-7.5 h-7.5 sm:w-8 sm:h-8 rounded-xl flex items-center justify-center shrink-0 shadow-xs group-hover:scale-105 transition-all duration-200"
                        style="background: linear-gradient(135deg, #460046 0%, #CC3D77 50%, #FF558A 100%); box-shadow: 0 2px 8px rgba(204, 61, 119, 0.35);"
                    >
                        <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="text-left hidden sm:block whitespace-nowrap">
                        <div class="text-xs font-bold text-slate-900 dark:text-white leading-tight">
                            {{ Auth::user()?->name ?? 'Usuario' }}
                        </div>
                        <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium leading-tight">
                            {{ Auth::user()?->position ?: (implode(' · ', Auth::user()?->areas ?? []) ?: 'Colaborador') }}
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 ml-0.5 shrink-0 transition-transform duration-200" :class="userMenuOpen ? 'rotate-180 text-slate-600 dark:text-slate-200' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Profile Dropdown Menu -->
                <div
                    x-show="userMenuOpen"
                    x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-full min-w-[200px] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl py-2 z-50 text-slate-700 dark:text-slate-200"
                >
                    @can('dashboard.view')
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-[#ffe4ed] hover:text-[#460046] dark:hover:bg-slate-800 dark:hover:text-white transition rounded-xl mx-1.5">
                            <svg class="w-4 h-4 text-slate-500 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Dashboard Corporativo</span>
                        </a>
                        <div class="border-t border-slate-200/80 dark:border-slate-700/80 my-1.5 mx-2"></div>
                    @endcan

                    <!-- Cerrar Sesión -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-rose-500 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/40 dark:hover:text-rose-400 transition cursor-pointer rounded-xl mx-1.5">
                            <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Cerrar Sesión</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
