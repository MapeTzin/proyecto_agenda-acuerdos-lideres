<div wire:poll.600s>
    <div class="bg-white dark:bg-[#1e293b] border border-slate-200/80 dark:border-slate-400/15 rounded-2xl shadow-sm px-5 py-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div
                class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 text-white"
                style="background: linear-gradient(135deg, #460046 0%, #CC3D77 50%, #FF558A 100%); box-shadow: 0 4px 14px rgba(204, 61, 119, 0.30);"
            >
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
            </div>
            <div>
                <h2 class="text-lg sm:text-xl font-black tracking-tight text-slate-900 dark:text-white uppercase leading-tight">
                    Dashboard General
                </h2>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-0.5">
                    Monitoreo integral de acuerdos, seguimiento por área y cumplimiento operativo.
                </p>
            </div>
        </div>
        <div class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium sm:text-right shrink-0">
            Bienvenido, <span class="font-black text-slate-900 dark:text-white">{{ Auth::user()?->name ?? 'Usuario' }}</span>
        </div>
    </div>

    @if($sin_actualizar->count() > 0)
        <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-500/30 border-l-4 border-l-rose-500 rounded-xl p-5 mb-6 flex items-start gap-4 shadow-sm">
            <div class="bg-rose-500 text-white w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 mt-0.5">
                <i class="fas fa-history"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-rose-700 dark:text-rose-300 m-0 text-sm font-extrabold uppercase tracking-wide">
                    Alerta: Áreas sin Seguimiento Ayer
                </h4>
                <p class="text-rose-800/90 dark:text-rose-200/80 mt-1 mb-3 text-xs sm:text-sm">
                    Las siguientes áreas no registraron avances en sus acuerdos durante el día de ayer:
                </p>
                <div class="flex flex-wrap gap-2">
                    @foreach($sin_actualizar as $item)
                        <span class="bg-rose-500/10 dark:bg-rose-500/15 text-rose-700 dark:text-rose-300 px-3 py-1 rounded-lg text-xs font-bold border border-rose-500/20 dark:border-rose-400/30 inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 bg-rose-500 rounded-full"></span>
                            {{ $item->area }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-value">{{ $total_acuerdos }}</span>
            <span class="stat-label">Total de Acuerdos</span>
        </div>
        <div class="stat-card pendientes">
            <span class="stat-value">{{ $pendientes }}</span>
            <span class="stat-label">Pendientes / En Proceso</span>
        </div>
        <div class="stat-card vencidos">
            <span class="stat-value">{{ $vencidos }}</span>
            <span class="stat-label">Vencidos Globales</span>
        </div>
        <div class="stat-card success">
            <span class="stat-value">{{ number_format($cumplimiento, 1) }}%</span>
            <span class="stat-label">ICO Global</span>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1.1fr 1fr 1fr 0.9fr; gap: 1.5rem;">
        <div class="card">
            <h3>Distribución por Cuadrantes</h3>
            <div style="margin-top: 1.5rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Cuadrante 1</span>
                    <span style="font-weight: 600;">{{ $c1_count }}</span>
                </div>
                <div class="progress-bar" style="width: 100%; height: 12px; margin-bottom: 2rem;">
                    <div class="progress-fill"
                        style="width: {{ $total_acuerdos > 0 ? ($c1_count / $total_acuerdos) * 100 : 0 }}%; background: var(--danger);">
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Cuadrante 2</span>
                    <span style="font-weight: 600;">{{ $c2_count }}</span>
                </div>
                <div class="progress-bar" style="width: 100%; height: 12px;">
                    <div class="progress-fill"
                        style="width: {{ $total_acuerdos > 0 ? ($c2_count / $total_acuerdos) * 100 : 0 }}%; background: var(--info);">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>Cumplimiento Diario por Área</h3>
            <div style="margin-top: 1rem; max-height: 250px; overflow-y: auto;">
                @foreach($cumplimiento_areas as $ca)
                    <div style="margin-bottom: 1rem;">
                        <div
                            style="display: flex; justify-content: space-between; margin-bottom: 0.25rem; font-size: 0.85rem;">
                            <span style="font-weight: 600;">{{ $ca['nombre'] }}</span>
                            <span
                                style="color: {{ $ca['porcentaje'] == 100 ? 'var(--success)' : ($ca['porcentaje'] > 0 ? 'var(--warning)' : 'var(--danger)') }}; font-weight: 700;">
                                {{ $ca['actualizados'] }}/{{ $ca['total'] }} ({{ number_format($ca['porcentaje'], 0) }}%)
                            </span>
                        </div>
                        <div class="progress-bar" style="width: 100%; height: 8px;">
                            <div class="progress-fill"
                                style="width: {{ $ca['porcentaje'] }}%; background: {{ $ca['porcentaje'] == 100 ? 'var(--success)' : ($ca['porcentaje'] > 50 ? 'var(--warning)' : 'var(--danger)') }};">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card">
            <h3>Cierre a Tiempo (Meta)</h3>
            <div style="margin-top: 1rem; max-height: 250px; overflow-y: auto;">
                @forelse($cumplimiento_fechas as $cf)
                    <div style="margin-bottom: 1rem;">
                        <div
                            style="display: flex; justify-content: space-between; margin-bottom: 0.25rem; font-size: 0.85rem;">
                            <span style="font-weight: 600;">{{ $cf['responsable'] }}</span>
                            <span style="color: var(--primary); font-weight: 700;">
                                {{ $cf['atendido'] }}/{{ $cf['total_finalizados'] }}
                                ({{ number_format($cf['porcentaje'], 0) }}%)
                            </span>
                        </div>
                        <div class="progress-bar" style="width: 100%; height: 8px;">
                            <div class="progress-fill"
                                style="width: {{ $cf['porcentaje'] }}%; background: var(--primary); opacity: {{ 0.4 + ($cf['porcentaje'] / 100) * 0.6 }};">
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="text-align: center; color: var(--secondary); margin-top: 2rem;">Sin acuerdos finalizados aún
                    </p>
                @endforelse
            </div>
        </div>

        <div class="card">
            <h3>Top Responsables (Volumen)</h3>
            <ul style="list-style: none; padding: 0; margin-top: 1rem;">
                @foreach($por_responsable as $resp)
                    <li
                        style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                        <span style="font-size: 0.9rem;">{{ $resp->responsable }}</span>
                        <span class="badge badge-blue">{{ $resp->total }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="card" style="margin-top: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3>Próximos Vencimientos Estratégicos</h3>
            <a href="{{ route('acuerdos.index') }}"
                style="font-size: 0.8rem; color: var(--primary); font-weight: 600;">Ver todo</a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Acuerdo / Actividad</th>
                        <th>Responsable</th>
                        <th>Vencimiento</th>
                        <th>Avance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($proximos as $acuerdo)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $acuerdo->acuerdo }}</div>
                                <div style="font-size: 0.75rem; color: var(--secondary);">{{ $acuerdo->actividad }}</div>
                            </td>
                            <td>{{ $acuerdo->responsable }}</td>
                            <td>
                                <span
                                    style="color: {{ $acuerdo->fecha_compromiso->isPast() ? 'var(--danger)' : 'inherit' }}; font-weight: {{ $acuerdo->fecha_compromiso->isPast() ? '700' : 'normal' }}">
                                    {{ $acuerdo->fecha_compromiso->format('d/m/Y') }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $sem = $acuerdo->semaforo;
                                @endphp
                                @if($acuerdo->isAtrasoCritico())
                                    <span style="background: linear-gradient(135deg, #dc2626, #991b1b); color: white; padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 0.3rem; animation: pulse-critical 1.5s ease-in-out infinite; white-space: nowrap;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        ATRASO CRÍTICO
                                    </span>
                                @else
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $sem['color'] }}; display: inline-block;"></span>
                                        <div class="progress-bar" style="width: 60px;">
                                            <div class="progress-fill" style="width: {{ $acuerdo->porcentaje_avance }}%; background: {{ $sem['color'] }};"></div>
                                        </div>
                                        <span style="font-size: 0.8rem; font-weight: 700; color: {{ $sem['color'] }};">{{ $acuerdo->porcentaje_avance }}%</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>