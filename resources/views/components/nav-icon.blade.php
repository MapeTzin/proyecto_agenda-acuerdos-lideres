@props(['name' => 'circle'])

@switch($name)
    @case('chart-line')
        <svg {{ $attributes->merge(['fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
        </svg>
        @break

    @case('layer-group')
        <svg {{ $attributes->merge(['fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0l4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0l-5.571 3-5.571-3" />
        </svg>
        @break

    @case('chart-pie')
        <svg {{ $attributes->merge(['fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
        </svg>
        @break

    @case('list-check')
        <svg {{ $attributes->merge(['fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        @break

    @case('folder')
        <svg {{ $attributes->merge(['fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
        </svg>
        @break

    @case('history')
        <svg {{ $attributes->merge(['fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        @break

    @case('calendar')
        <svg {{ $attributes->merge(['fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
        </svg>
        @break

    @case('shield-check')
        <svg {{ $attributes->merge(['fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
        </svg>
        @break

    @default
        <svg {{ $attributes->merge(['fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}>
            <circle cx="12" cy="12" r="9" stroke-width="1.8" />
        </svg>
@endswitch
