<div wire:poll.600s style="max-width: 1200px; margin: 0 auto; padding-bottom: 5rem;">
    <!-- Pyramid Peak: Main Title -->
    <div style="text-align: center; margin-bottom: 3.5rem; animation: fadeInDown 0.8s ease-out;">
        <h1
            style="font-size: 3rem; font-weight: 900; color: var(--dark); text-transform: uppercase; letter-spacing: 4px; margin: 0; line-height: 1;">
            SEGUIMIENTO POR ÁREA
        </h1>
        <div
            style="width: 150px; height: 6px; background: linear-gradient(90deg, var(--primary), #818cf8); margin: 1rem auto; border-radius: 10px;">
        </div>
        <p
            style="color: var(--secondary); font-size: 1.2rem; font-weight: 500; text-transform: uppercase; letter-spacing: 1px;">
            Inteligencia Operativa y Cumplimiento</p>
    </div>

    <!-- Pyramid Step 1: Strategic Filter -->
    <div
        style="display: flex; justify-content: center; margin-bottom: 4rem; animation: fadeInUp 0.8s ease-out 0.2s; animation-fill-mode: both;">
        <div
            style="background: white; padding: 1.5rem 3rem; border-radius: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 2rem; transform: translateY(0); transition: all 0.3s hover; border-top: 4px solid var(--primary);">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div
                    style="width: 45px; height: 45px; background: #eef2ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.2rem;">
                    <i class="fas fa-filter"></i>
                </div>
                <div>
                    <span
                        style="display: block; font-size: 0.75rem; color: var(--secondary); font-weight: 700; text-transform: uppercase;">Selección
                        de Vista</span>
                    <label style="font-weight: 800; color: #1e293b; font-size: 1.1rem;">Área de Negocio</label>
                </div>
            </div>
            <select wire:model.live="selectedArea"
                style="padding: 0.85rem 1.5rem; border-radius: 1rem; border: 2px solid #e2e8f0; outline: none; font-family: inherit; background: #f8fafc; min-width: 350px; cursor: pointer; font-weight: 700; color: var(--primary); font-size: 1.1rem; transition: border-color 0.2s;">
                <option value="">Vista Corporativa (Todas las Áreas)</option>
                @foreach($areas as $area)
                    <option value="{{ $area }}">{{ $area }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if($sin_actualizar->count() > 0)
        <div
            style="background: #fff5f5; border-left: 6px solid #f56565; border-radius: 1rem; padding: 1.25rem 2rem; margin-bottom: 4rem; display: flex; align-items: flex-start; gap: 1.5rem; box-shadow: 0 10px 15px -3px rgba(245, 101, 101, 0.1); animation: shake 0.5s ease-in-out;">
            <div
                style="background: #f56565; color: white; width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; margin-top: 5px;">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div style="flex: 1;">
                <h4
                    style="color: #c53030; margin: 0; font-size: 1rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                    Protocolo de Actualización Incumplido Hoy</h4>
                <p style="color: #9b2c2c; margin: 0.35rem 0 0.75rem 0; font-size: 0.95rem;">
                    Las siguientes áreas no han registrado su seguimiento diario en el monitor:
                </p>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    @foreach($sin_actualizar as $item)
                        <span
                            style="background: rgba(245, 101, 101, 0.1); color: #c53030; padding: 0.4rem 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 700; border: 1px solid rgba(245, 101, 101, 0.2); display: flex; align-items: center; gap: 0.5rem;">
                            <span style="width: 7px; height: 7px; background: #f56565; border-radius: 50%;"></span>
                            {{ $item->area }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Pyramid Step 2: Key Indicators Grid -->
    <div
        style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem; margin-bottom: 4rem; animation: fadeInUp 0.8s ease-out 0.4s; animation-fill-mode: both;">
        <div
            style="background: white; padding: 2rem; border-radius: 1.5rem; text-align: center; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.03); border-top: 5px solid var(--primary);">
            <i class="fas fa-folder-open"
                style="color: var(--primary); font-size: 1.5rem; margin-bottom: 1rem; opacity: 0.5;"></i>
            <span
                style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--secondary); text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 1px;">Acuerdos
                Totales</span>
            <span style="font-size: 2.5rem; font-weight: 900; color: var(--dark);">{{ $total_acuerdos }}</span>
        </div>
        <div
            style="background: white; padding: 2rem; border-radius: 1.5rem; text-align: center; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.03); border-top: 5px solid var(--warning);">
            <i class="fas fa-clock"
                style="color: var(--warning); font-size: 1.5rem; margin-bottom: 1rem; opacity: 0.5;"></i>
            <span
                style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--secondary); text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 1px;">En
                Ejecución</span>
            <span style="font-size: 2.5rem; font-weight: 900; color: var(--warning);">{{ $pendientes }}</span>
        </div>
        <div
            style="background: white; padding: 2rem; border-radius: 1.5rem; text-align: center; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.03); border-top: 5px solid var(--danger);">
            <i class="fas fa-hourglass-end"
                style="color: var(--danger); font-size: 1.5rem; margin-bottom: 1rem; opacity: 0.5;"></i>
            <span
                style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--secondary); text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 1px;">Vencidos</span>
            <span style="font-size: 2.5rem; font-weight: 900; color: var(--danger);">{{ $vencidos }}</span>
        </div>
        <div
            style="background: white; padding: 2rem; border-radius: 1.5rem; text-align: center; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.03); border-top: 5px solid var(--success);">
            <i class="fas fa-check-double"
                style="color: var(--success); font-size: 1.5rem; margin-bottom: 1rem; opacity: 0.5;"></i>
            <span
                style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--secondary); text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 1px;">Eficiencia
                ICO</span>
            <span
                style="font-size: 2.5rem; font-weight: 900; color: var(--success);">{{ number_format($cumplimiento, 0) }}%</span>
        </div>
    </div>

    <!-- Pyramid Step 3: Analysis & Leadership -->
    <div
        style="display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem; margin-bottom: 4rem; animation: fadeInUp 0.8s ease-out 0.6s; animation-fill-mode: both;">
        <div
            style="background: white; padding: 2.5rem; border-radius: 2rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                <div
                    style="width: 40px; height: 40px; background: #fff1f2; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--danger);">
                    <i class="fas fa-th-large"></i>
                </div>
                <h3 style="margin: 0; font-size: 1.3rem; font-weight: 800; color: #1e293b;">Análisis por Cuadrante</h3>
            </div>
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-weight: 700; color: #334155; font-size: 1rem;">Cuadrante 1: Crítico y
                            Urgente</span>
                        <span
                            style="background: #fee2e2; color: #991b1b; padding: 0.2rem 0.8rem; border-radius: 0.5rem; font-weight: 800;">{{ $c1_count }}</span>
                    </div>
                    <div
                        style="width: 100%; height: 16px; background: #f1f5f9; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <div
                            style="width: {{ $total_acuerdos > 0 ? ($c1_count / $total_acuerdos) * 100 : 0 }}%; height: 100%; background: linear-gradient(90deg, #ef4444, #f87171); border-radius: 8px;">
                        </div>
                    </div>
                </div>
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-weight: 700; color: #334155; font-size: 1rem;">Cuadrante 2: Planificación
                            Estratégica</span>
                        <span
                            style="background: #e0f2fe; color: #075985; padding: 0.2rem 0.8rem; border-radius: 0.5rem; font-weight: 800;">{{ $c2_count }}</span>
                    </div>
                    <div
                        style="width: 100%; height: 16px; background: #f1f5f9; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <div
                            style="width: {{ $total_acuerdos > 0 ? ($c2_count / $total_acuerdos) * 100 : 0 }}%; height: 100%; background: linear-gradient(90deg, #3b82f6, #60a5fa); border-radius: 8px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            style="background: white; padding: 2.5rem; border-radius: 2rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                <div
                    style="width: 40px; height: 40px; background: #f0fdf4; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--success);">
                    <i class="fas fa-crown"></i>
                </div>
                <h3 style="margin: 0; font-size: 1.3rem; font-weight: 800; color: #1e293b;">Mayores Compromisos</h3>
            </div>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($por_responsable as $resp)
                    <div
                        style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: #f8fafc; border-radius: 1rem; border: 1px solid #f1f5f9; transition: transform 0.2s hover;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div
                                style="width: 35px; height: 35px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; color: var(--primary); border: 1px solid #e2e8f0;">
                                {{ substr($resp->responsable, 0, 1) }}
                            </div>
                            <span style="font-weight: 700; color: #334155;">{{ $resp->responsable }}</span>
                        </div>
                        <span
                            style="background: var(--primary); color: white; padding: 0.3rem 1rem; border-radius: 2rem; font-size: 0.85rem; font-weight: 800; box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);">{{ $resp->total }}
                            Acuerdos</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Pyramid Base: Detailed Strategic Matrix -->
    <div
        style="background: white; padding: 3rem; border-radius: 2.5rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.08); animation: fadeInUp 0.8s ease-out 0.8s; animation-fill-mode: both; border: 1px solid #f1f5f9;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div
                    style="width: 50px; height: 50px; background: var(--dark); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white;">
                    <i class="fas fa-table-list"></i>
                </div>
                <div>
                    <h3
                        style="margin: 0; font-size: 1.5rem; font-weight: 900; color: var(--dark); text-transform: uppercase; letter-spacing: 1px;">
                        Matriz de Seguimiento</h3>
                    <p style="margin: 0.2rem 0 0 0; color: var(--secondary); font-weight: 600;">Detalle de acuerdos e
                        hitos próximos</p>
                </div>
            </div>
            <a href="{{ route('acuerdos.index') }}"
                style="background: var(--light); color: var(--primary); padding: 0.75rem 1.5rem; border-radius: 1rem; font-weight: 800; text-decoration: none; border: 2px solid var(--primary); transition: all 0.3s hover; display: flex; align-items: center; gap: 0.75rem;">
                VER LISTADO MAESTRO <i class="fas fa-chevron-right"></i>
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0 0.75rem;">
                <thead>
                    <tr style="text-align: left;">
                        <th
                            style="padding: 1.5rem; color: #94a3b8; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">
                            Acuerdo Estratégico</th>
                        <th
                            style="padding: 1.5rem; color: #94a3b8; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">
                            Líder de Acción</th>
                        <th
                            style="padding: 1.5rem; color: #94a3b8; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">
                            Fecha Compromiso</th>
                        <th
                            style="padding: 1.5rem; color: #94a3b8; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">
                            Nivel de Avance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($proximos as $acuerdo)
                        <tr style="background: white; transition: all 0.3s; transform: translateY(0);">
                            <td
                                style="padding: 1.75rem 1.5rem; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; border-left: 1px solid #f1f5f9; border-radius: 1.5rem 0 0 1.5rem;">
                                <div style="font-weight: 800; color: #1e293b; font-size: 1.1rem; margin-bottom: 0.35rem;">
                                    {{ $acuerdo->acuerdo }}
                                </div>
                                <div
                                    style="font-size: 0.85rem; color: #64748b; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-bullseye" style="color: var(--primary); font-size: 0.7rem;"></i>
                                    {{ $acuerdo->actividad }}
                                </div>
                            </td>
                            <td
                                style="padding: 1.75rem 1.5rem; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; color: #475569; font-weight: 700;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div
                                        style="width: 32px; height: 32px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; color: var(--primary); border: 1px solid #e2e8f0;">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                    {{ $acuerdo->responsable }}
                                </div>
                            </td>
                            <td
                                style="padding: 1.75rem 1.5rem; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">
                                <span
                                    style="font-family: 'Courier New', Courier, monospace; font-size: 1rem; font-weight: 700; color: {{ $acuerdo->fecha_compromiso->isPast() ? 'var(--danger)' : '#475569' }}; background: {{ $acuerdo->fecha_compromiso->isPast() ? '#fee2e2' : '#f8fafc' }}; padding: 0.5rem 1rem; border-radius: 0.75rem;">
                                    <i class="far fa-calendar-alt"
                                        style="margin-right: 0.5rem;"></i>{{ $acuerdo->fecha_compromiso->format('d/m/Y') }}
                                </span>
                            </td>
                            <td
                                style="padding: 1.75rem 1.5rem; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; border-radius: 0 1.5rem 1.5rem 0;">
                                @php
                                    $sem = $acuerdo->semaforo;
                                @endphp
                                @if($acuerdo->isAtrasoCritico())
                                    <span style="background: linear-gradient(135deg, #dc2626, #991b1b); color: white; padding: 0.4rem 0.85rem; border-radius: 8px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 0.4rem; animation: pulse-critical 1.5s ease-in-out infinite; white-space: nowrap;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        ATRASO CRÍTICO
                                    </span>
                                @else
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <span style="width: 10px; height: 10px; border-radius: 50%; background: {{ $sem['color'] }}; display: inline-block; flex-shrink: 0;" title="{{ $sem['label'] }}"></span>
                                        <div
                                            style="flex: 1; height: 12px; background: #f1f5f9; border-radius: 6px; overflow: hidden; border: 1px solid #e2e8f0;">
                                            <div
                                                style="width: {{ $acuerdo->porcentaje_avance }}%; height: 100%; background: {{ $sem['color'] }}; border-radius: 6px; transition: width 0.3s ease;">
                                            </div>
                                        </div>
                                        <span
                                            style="font-weight: 900; color: {{ $sem['color'] }}; font-size: 1.1rem; width: 50px;">{{ $acuerdo->porcentaje_avance }}%</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <style>
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        tr:hover {
            transform: scale(1.01);
            z-index: 10;
        }
    </style>
</div>