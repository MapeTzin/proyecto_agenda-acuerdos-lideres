<div wire:poll.600s>
    <div style="margin-bottom: 2rem;">
        <h2 style="margin: 0;">Dashboard General</h2>
        <p style="color: var(--secondary); margin-top: 0.25rem;">Estado global de todos los acuerdos operativa</p>
    </div>

    @if($sin_actualizar->count() > 0)
        <div
            style="background: #fff5f5; border: 1px solid #feb2b2; border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 2rem; display: flex; align-items: flex-start; gap: 1.25rem; border-left: 6px solid #f56565; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <div
                style="background: #f56565; color: white; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; margin-top: 2px;">
                <i class="fas fa-history"></i>
            </div>
            <div style="flex: 1;">
                <h4
                    style="color: #c53030; margin: 0; font-size: 1rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.025em;">
                    Alerta: Áreas sin Seguimiento Ayer
                </h4>
                <p style="color: #9b2c2c; margin: 0.35rem 0 0.75rem 0; font-size: 0.9rem; opacity: 0.9;">
                    Las siguientes áreas no registraron avances en sus acuerdos durante el día de ayer:
                </p>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    @foreach($sin_actualizar as $item)
                        <span
                            style="background: rgba(245, 101, 101, 0.1); color: #c53030; padding: 0.35rem 0.85rem; border-radius: 6px; font-size: 0.8rem; font-weight: 700; border: 1px solid rgba(245, 101, 101, 0.2); display: inline-flex; align-items: center; gap: 0.4rem;">
                            <span style="width: 6px; height: 6px; background: #f56565; border-radius: 50%;"></span>
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