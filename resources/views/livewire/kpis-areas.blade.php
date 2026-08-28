<div wire:poll.600s style="width: 100%; max-width: 100%; margin: 0 auto; padding-bottom: 5rem;">

    <style>
        /* KPI Tooltips & Interactive Popovers */
        .kpi-tooltip-wrap {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .kpi-popover {
            position: absolute;
            bottom: calc(100% + 14px);
            left: 50%;
            transform: translateX(-50%) translateY(8px);
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 20px 35px -8px rgba(15, 23, 42, 0.22), 0 8px 16px -4px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(226, 232, 240, 0.95);
            padding: 0;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.2s cubic-bezier(0.16, 1, 0.3, 1), transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.2s;
            z-index: 9999;
            min-width: 320px;
            max-width: 400px;
            width: max-content;
            text-align: left;
            cursor: default;
            font-family: inherit;
            white-space: normal;
        }

        .kpi-tooltip-wrap:hover {
            z-index: 100;
        }

        .kpi-tooltip-wrap:hover .kpi-popover {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
            pointer-events: auto;
        }

        /* Popover Arrow */
        .kpi-popover::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 8px;
            border-style: solid;
            border-color: #ffffff transparent transparent transparent;
            filter: drop-shadow(0 2px 2px rgba(0,0,0,0.06));
        }

        /* Right-aligned popover for rightmost table columns */
        .kpi-popover-right {
            left: auto;
            right: 0;
            transform: translateY(8px);
        }
        .kpi-tooltip-wrap:hover .kpi-popover-right {
            transform: translateY(0);
        }
        .kpi-popover-right::after {
            left: auto;
            right: 32px;
            transform: none;
        }

        /* Left-aligned popover */
        .kpi-popover-left {
            left: 0;
            right: auto;
            transform: translateY(8px);
        }
        .kpi-tooltip-wrap:hover .kpi-popover-left {
            transform: translateY(0);
        }
        .kpi-popover-left::after {
            left: 32px;
            right: auto;
            transform: none;
        }

        /* Subtle scrollbar for tooltip lists */
        .kpi-popover-list::-webkit-scrollbar {
            width: 4px;
        }
        .kpi-popover-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
    </style>

    <!-- Top Pyramid Peak Title & Strategic View Filter -->
    <div style="text-align: center; margin-bottom: 2.5rem; animation: fadeInDown 0.8s ease-out;">
        <h1 style="font-size: 2.75rem; font-weight: 900; color: #0f172a; text-transform: uppercase; letter-spacing: 3px; margin: 0; line-height: 1;">
            {{ empty($selectedArea) ? 'MONITOR GLOBAL DE KPI´S POR ÁREA' : $selectedArea }}
        </h1>
        <div style="width: 140px; height: 5px; background: linear-gradient(90deg, #6366f1, #818cf8); margin: 0.85rem auto; border-radius: 10px;"></div>
        <p style="color: #64748b; font-size: 1.1rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin: 0;">
            {{ empty($selectedArea) ? 'Inteligencia Operativa y Cumplimiento de Indicadores' : 'Indicadores de Desempeño y Métricas' }}
        </p>
    </div>

    <!-- View Selector (Vista Corporativa for Admins vs Locked Area for Regular Users) + Month Filter -->
    <div style="display: flex; justify-content: center; margin-bottom: 3rem;">
        <div style="background: white; padding: 1.25rem 2.5rem; border-radius: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 2rem; border-top: 4px solid #6366f1; flex-wrap: wrap;">
            
            <!-- Area Selector Portion -->
            <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 42px; height: 42px; background: #eef2ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #6366f1; font-size: 1.1rem;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <span style="display: block; font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Área de Trabajo</span>
                        <label style="font-weight: 800; color: #1e293b; font-size: 1.05rem;">
                            {{ $isAdminUser ? 'Área de Negocio' : $selectedArea }}
                        </label>
                    </div>
                </div>

                @if($isAdminUser)
                    <select wire:model.live="selectedArea"
                        style="padding: 0.8rem 1.5rem; border-radius: 1rem; border: 2px solid #e2e8f0; outline: none; font-family: inherit; background: #f8fafc; min-width: 320px; cursor: pointer; font-weight: 700; color: #6366f1; font-size: 1.05rem;">
                        <option value="">Vista Corporativa (Todas las Áreas)</option>
                        @foreach($officialAreas as $areaOption)
                            <option value="{{ $areaOption }}">{{ $areaOption }}</option>
                        @endforeach
                    </select>
                @else
                    <div style="background: #eef2ff; color: #6366f1; padding: 0.6rem 1.5rem; border-radius: 1rem; font-weight: 800; font-size: 1rem; border: 1px solid #c7d2fe;">
                        <i class="fas fa-lock" style="margin-right: 0.4rem; font-size: 0.85rem;"></i> {{ $selectedArea }}
                    </div>
                @endif
            </div>

            <!-- Month Filter Portion -->
            <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap; border-left: 2px solid #f1f5f9; padding-left: 2rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 42px; height: 42px; background: #ecfdf5; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 1.1rem;">
                        <i class="fas fa-calendar-days"></i>
                    </div>
                    <div>
                        <span style="display: block; font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Mes de Evaluación</span>
                        <label style="font-weight: 800; color: #1e293b; font-size: 1.05rem;">Seleccionar Mes</label>
                    </div>
                </div>

                <select wire:model.live="selectedMonth"
                    style="padding: 0.8rem 1.5rem; border-radius: 1rem; border: 2px solid #e2e8f0; outline: none; font-family: inherit; background: #f8fafc; min-width: 180px; cursor: pointer; font-weight: 700; color: #10b981; font-size: 1.05rem;">
                    <option value="1">Enero</option>
                    <option value="2">Febrero</option>
                    <option value="3">Marzo</option>
                    <option value="4">Abril</option>
                    <option value="5">Mayo</option>
                    <option value="6">Junio</option>
                    <option value="7">Julio</option>
                    <option value="8">Agosto</option>
                    <option value="9">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                </select>
            </div>

        </div>
    </div>

    @if(session()->has('success'))
        <div style="background: #d1fae5; border-left: 4px solid #10b981; color: #065f46; margin-bottom: 2rem; padding: 1rem 1.5rem; border-radius: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <i class="fas fa-check-circle" style="font-size: 1.2rem;"></i> {{ session('success') }}
        </div>
    @endif

    <!-- MODE 1: MONITOR GLOBAL DE KPIS (For Admins only when selectedArea is empty "") -->
    @if(empty($selectedArea) && $isAdminUser)
        <!-- Summary Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
            <!-- Card 1 -->
            <div style="background: white; padding: 1.5rem; border-radius: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.04); display: flex; align-items: center; gap: 1.25rem; border-left: 5px solid #6366f1;">
                <div style="width: 50px; height: 50px; background: #eef2ff; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #6366f1; font-size: 1.4rem;">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div>
                    <span style="font-size: 2rem; font-weight: 900; color: #0f172a; display: block; line-height: 1;">{{ $totalKpisGlobal }}</span>
                    <span style="font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase; margin-top: 0.35rem; display: block;">Total KPI´s Registrados</span>
                </div>
            </div>

            <!-- Card 2 -->
            <div style="background: white; padding: 1.5rem; border-radius: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.04); display: flex; align-items: center; gap: 1.25rem; border-left: 5px solid #3b82f6;">
                <div style="width: 50px; height: 50px; background: #eff6ff; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 1.4rem;">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <span style="font-size: 2rem; font-weight: 900; color: #0f172a; display: block; line-height: 1;">{{ $areasCount }}</span>
                    <span style="font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase; margin-top: 0.35rem; display: block;">Áreas Monitoreadas</span>
                </div>
            </div>

            <!-- Card 3 (With Interactive Popover on Hover) -->
            <div class="kpi-tooltip-wrap" style="width: 100%; display: block;">
                <div style="background: white; padding: 1.5rem; border-radius: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.04); display: flex; align-items: center; gap: 1.25rem; border-left: 5px solid #10b981; cursor: help; height: 100%; box-sizing: border-box;">
                    <div style="width: 50px; height: 50px; background: #ecfdf5; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 1.4rem; flex-shrink: 0;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <span style="font-size: 2rem; font-weight: 900; color: #0f172a; line-height: 1;">{{ number_format($cumplimientoJunioAvg, 1) }}%</span>
                            <span style="background: {{ $globalDiff >= 0 ? '#d1fae5' : '#fee2e2' }}; color: {{ $globalDiff >= 0 ? '#065f46' : '#991b1b' }}; padding: 2px 8px; border-radius: 9999px; font-weight: 800; font-size: 0.75rem;">
                                {{ $globalDiff >= 0 ? '+' : '' }}{{ $globalDiff }}%
                            </span>
                        </div>
                        <span style="font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase; margin-top: 0.35rem; display: block;">Cumplimiento Promedio ({{ $latestMonthName }})</span>
                    </div>
                </div>

                <!-- Popover Card for Global Average -->
                <div class="kpi-popover">
                    <div style="height: 4px; background: #10b981; border-radius: 1rem 1rem 0 0;"></div>
                    <div style="padding: 1.1rem 1.25rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.4rem;">
                            <span style="background: #ecfdf5; color: #065f46; font-size: 0.68rem; font-weight: 800; padding: 2px 8px; border-radius: 6px; text-transform: uppercase;">
                                <i class="fas fa-chart-pie" style="margin-right: 3px;"></i> Desempeño Corporativo
                            </span>
                            <span style="font-size: 0.72rem; color: #64748b; font-weight: 700;">
                                {{ $latestMonthName }} 2026
                            </span>
                        </div>
                        <div style="font-weight: 900; color: #0f172a; font-size: 1.05rem; margin-bottom: 0.5rem;">
                            Cumplimiento Promedio Global
                        </div>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 0.65rem 0.85rem; margin-bottom: 0.75rem; display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <span style="display: block; font-size: 0.68rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Promedio {{ $latestMonthName }}</span>
                                <span style="font-size: 1.4rem; font-weight: 900; color: #10b981; line-height: 1.1;">
                                    {{ number_format($cumplimientoJunioAvg, 1) }}%
                                </span>
                            </div>
                            <span style="background: {{ $globalDiff >= 0 ? '#d1fae5' : '#fee2e2' }}; color: {{ $globalDiff >= 0 ? '#065f46' : '#991b1b' }}; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 9999px;">
                                {{ $globalDiff >= 0 ? '+' : '' }}{{ $globalDiff }}% vs {{ $prevMonthName }}
                            </span>
                        </div>
                        <div style="display: flex; gap: 4px; margin-bottom: 0.6rem;">
                            <span style="background: #d1fae5; color: #065f46; font-size: 0.68rem; font-weight: 800; padding: 3px 6px; border-radius: 6px; flex: 1; text-align: center;">
                                🟢 {{ $areasEnMetaCount }} En Meta
                            </span>
                            <span style="background: #fef3c7; color: #92400e; font-size: 0.68rem; font-weight: 800; padding: 3px 6px; border-radius: 6px; flex: 1; text-align: center;">
                                🟡 {{ $areasEnPrevencionCount }} Prevención
                            </span>
                            <span style="background: #fee2e2; color: #991b1b; font-size: 0.68rem; font-weight: 800; padding: 3px 6px; border-radius: 6px; flex: 1; text-align: center;">
                                🔴 {{ $areasEnAtencionCount }} Atención
                            </span>
                        </div>
                        <div style="font-size: 0.72rem; color: #475569; background: #f1f5f9; padding: 6px 8px; border-radius: 6px; line-height: 1.35;">
                            <i class="fas fa-calculator" style="color: #6366f1; margin-right: 3px;"></i> Calculado a partir de la media aritmética de las {{ $areasCount }} áreas oficiales evaluadas.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4 (With Interactive Popover on Hover) -->
            <div class="kpi-tooltip-wrap" style="width: 100%; display: block;">
                <div style="background: white; padding: 1.5rem; border-radius: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.04); display: flex; align-items: center; gap: 1.25rem; border-left: 5px solid #f59e0b; cursor: help; height: 100%; box-sizing: border-box;">
                    <div style="width: 50px; height: 50px; background: #fffbeb; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #f59e0b; font-size: 1.4rem; flex-shrink: 0;">
                        <i class="fas fa-arrow-right-arrow-left"></i>
                    </div>
                    <div>
                        <span style="font-size: 1.15rem; font-weight: 900; color: #0f172a; display: block; line-height: 1.2;">
                            {{ strtoupper($prevMonthName) }}: <span style="color: #6366f1;">{{ number_format($cumplimientoMayoAvg, 1) }}%</span> ➔ {{ strtoupper($latestMonthName) }}: <span style="color: #10b981;">{{ number_format($cumplimientoJunioAvg, 1) }}%</span>
                        </span>
                        <span style="font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase; margin-top: 0.35rem; display: block;">Comparativo Global Mensual</span>
                    </div>
                </div>

                <!-- Popover Card for Monthly Comparison -->
                <div class="kpi-popover kpi-popover-right">
                    <div style="height: 4px; background: #f59e0b; border-radius: 1rem 1rem 0 0;"></div>
                    <div style="padding: 1.1rem 1.25rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.4rem;">
                            <span style="background: #fffbeb; color: #92400e; font-size: 0.68rem; font-weight: 800; padding: 2px 8px; border-radius: 6px; text-transform: uppercase;">
                                <i class="fas fa-chart-simple" style="margin-right: 3px;"></i> Comparativo Intermensual
                            </span>
                            <span style="font-size: 0.72rem; color: #64748b; font-weight: 700;">
                                {{ $prevMonthName }} vs {{ $latestMonthName }}
                            </span>
                        </div>
                        <div style="font-weight: 900; color: #0f172a; font-size: 1.05rem; margin-bottom: 0.5rem;">
                            Evolución Global de Desempeño
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-bottom: 0.75rem;">
                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px; text-align: center;">
                                <span style="font-size: 0.65rem; color: #64748b; font-weight: 700; text-transform: uppercase; display: block;">{{ $prevMonthName }}</span>
                                <span style="font-size: 1.15rem; font-weight: 900; color: #6366f1;">{{ number_format($cumplimientoMayoAvg, 1) }}%</span>
                            </div>
                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px; text-align: center;">
                                <span style="font-size: 0.65rem; color: #64748b; font-weight: 700; text-transform: uppercase; display: block;">{{ $latestMonthName }}</span>
                                <span style="font-size: 1.15rem; font-weight: 900; color: #10b981;">{{ number_format($cumplimientoJunioAvg, 1) }}%</span>
                            </div>
                        </div>
                        <div style="background: {{ $globalDiff >= 0 ? '#d1fae5' : '#fee2e2' }}; color: {{ $globalDiff >= 0 ? '#065f46' : '#991b1b' }}; padding: 6px 9px; border-radius: 6px; font-size: 0.74rem; font-weight: 700; text-align: center;">
                            <i class="fas {{ $globalDiff >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                            Variación General: {{ $globalDiff >= 0 ? '+' : '' }}{{ $globalDiff }}% de cumplimiento corporativo
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Global Monitor Table -->
        <div style="background: white; border-radius: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); padding: 1.5rem; width: 100%; box-sizing: border-box;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 0.85rem; border-bottom: 1px solid #f1f5f9;">
                <div>
                    <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #0f172a;">
                        <i class="fas fa-table-columns" style="color: #6366f1; margin-right: 0.5rem;"></i> Tabla de Monitoreo de KPI´s por Área
                    </h3>
                    <span style="font-size: 0.85rem; color: #64748b; font-weight: 500;">Consolidado de desempeño por departamento ({{ $latestMonthName }} 2026) &bull; <span style="color: #6366f1; font-weight: 600;"><i class="fas fa-mouse-pointer"></i> Pasa el cursor sobre los porcentajes para ver el detalle</span></span>
                </div>
            </div>

            <table style="width: 100%; border-collapse: collapse; font-family: inherit;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 1rem 0.8rem; text-align: left; font-size: 0.8rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.4px; white-space: nowrap;">
                            Área
                        </th>
                        <th style="padding: 1rem 0.8rem; text-align: center; font-size: 0.8rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.4px; white-space: nowrap;">
                            Total KPI´s
                        </th>
                        <th style="padding: 1rem 0.8rem; text-align: left; font-size: 0.8rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.4px; white-space: nowrap;">
                            SEMÁFORO {{ $latestMonthName }}
                        </th>
                        <th style="padding: 1rem 0.8rem; text-align: center; font-size: 0.8rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.4px; white-space: nowrap;">
                            CUMPLIMIENTO TOTAL ({{ $latestMonthName }})
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monitorData as $item)
                        <tr wire:key="monitor-row-{{ $item['area'] }}-{{ $selectedYear }}-{{ $selectedMonth }}" style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s; cursor: pointer;" wire:click="selectArea('{{ $item['area'] }}')" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            <!-- Area Name -->
                            <td style="padding: 1rem 0.8rem; white-space: nowrap;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 36px; height: 36px; background: #eef2ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #6366f1; font-size: 0.95rem; font-weight: 700;">
                                        <i class="fas fa-folder"></i>
                                    </div>
                                    <span style="font-weight: 800; color: #1e293b; font-size: 0.92rem;">{{ $item['area'] }}</span>
                                </div>
                            </td>

                            <!-- Total KPIs -->
                            <td style="padding: 1rem 0.8rem; text-align: center; white-space: nowrap;">
                                <span style="background: #f1f5f9; color: #334155; padding: 0.35rem 0.75rem; border-radius: 9999px; font-weight: 800; font-size: 0.8rem; border: 1px solid #cbd5e1; display: inline-block;">
                                    {{ $item['total_kpis'] }} KPI´s
                                </span>
                            </td>

                            <!-- Semáforo Individual por KPI con Popovers Explicativos Detallados -->
                            <td style="padding: 1rem 0.8rem; text-align: left;">
                                <div style="display: flex; align-items: center; justify-content: flex-start; gap: 0.45rem; flex-wrap: nowrap; white-space: nowrap;">
                                    @foreach($item['kpi_dots_junio'] as $dot)
                                        <div class="kpi-tooltip-wrap" onclick="event.stopPropagation();">
                                            <!-- The Badge Trigger -->
                                            <div style="display: flex; flex-direction: column; align-items: center; gap: 3px; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 5px 8px; min-width: 36px; flex-shrink: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); transition: all 0.15s ease; cursor: help;"
                                                 onmouseover="this.style.transform='scale(1.15)'; this.style.borderColor='{{ $dot['color'] }}';"
                                                 onmouseout="this.style.transform='scale(1)'; this.style.borderColor='#cbd5e1';">
                                                <span style="font-size: 0.76rem; font-weight: 800; color: #1e293b;">{{ $dot['val'] }}</span>
                                                <span style="width: 9px; height: 9px; border-radius: 50%; background: {{ $dot['color'] }}; display: inline-block;"></span>
                                            </div>

                                            <!-- The Rich Popover Body -->
                                            <div class="kpi-popover">
                                                <!-- Top Accent Bar -->
                                                <div style="height: 4px; background: {{ $dot['color'] }}; border-radius: 1rem 1rem 0 0;"></div>
                                                
                                                <div style="padding: 1.1rem 1.25rem;">
                                                    <!-- Header: Area Tag & Meta -->
                                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.4rem;">
                                                        <span style="background: #eef2ff; color: #6366f1; font-size: 0.68rem; font-weight: 800; padding: 2px 8px; border-radius: 6px; text-transform: uppercase;">
                                                            <i class="fas fa-folder" style="margin-right: 3px;"></i> {{ $item['area'] }}
                                                        </span>
                                                        <span style="font-size: 0.72rem; color: #64748b; font-weight: 700;">
                                                            Meta: &ge; 95%
                                                        </span>
                                                    </div>

                                                    <!-- KPI Name -->
                                                    <div style="font-weight: 800; color: #0f172a; font-size: 0.95rem; line-height: 1.25; margin-bottom: 0.3rem;">
                                                        {{ $dot['name'] }}
                                                    </div>

                                                    @if(!empty($dot['description']) && $dot['description'] !== $dot['name'])
                                                        <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.75rem; line-height: 1.3;">
                                                            {{ $dot['description'] }}
                                                        </div>
                                                    @endif

                                                    <!-- Current Score & Status Pill -->
                                                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 0.65rem 0.85rem; margin: 0.65rem 0; display: flex; align-items: center; justify-content: space-between;">
                                                        <div>
                                                            <span style="display: block; font-size: 0.68rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Cumplimiento {{ $latestMonthName }}</span>
                                                            <span style="font-size: 1.35rem; font-weight: 900; color: {{ $dot['color'] }}; line-height: 1.1;">
                                                                {{ $dot['val'] }}
                                                            </span>
                                                        </div>
                                                        <span style="background: {{ $dot['status_bg'] }}; color: {{ $dot['status_color'] }}; font-size: 0.74rem; font-weight: 800; padding: 4px 10px; border-radius: 9999px; display: inline-flex; align-items: center; gap: 5px;">
                                                            <span style="width: 7px; height: 7px; border-radius: 50%; background: {{ $dot['color'] }};"></span>
                                                            {{ $dot['status_text'] }}
                                                        </span>
                                                    </div>

                                                    <!-- "Por qué este porcentaje" Section -->
                                                    <div style="border-top: 1px solid #f1f5f9; padding-top: 0.65rem; margin-top: 0.5rem;">
                                                        <div style="display: flex; align-items: center; gap: 5px; font-size: 0.75rem; font-weight: 800; color: #334155; margin-bottom: 0.45rem;">
                                                            <i class="fas fa-calculator" style="color: #6366f1;"></i> Desglose y Origen del Cálculo
                                                        </div>

                                                        @if($dot['weeks_count'] > 0)
                                                            <!-- Mini weekly cards -->
                                                            <div style="display: flex; gap: 4px; margin-bottom: 0.5rem; flex-wrap: wrap;">
                                                                @foreach($dot['weeks'] as $wk)
                                                                    <div style="flex: 1; min-width: 52px; background: #ffffff; border: 1px solid {{ $wk['numeric_val'] !== null ? '#cbd5e1' : '#f1f5f9' }}; border-radius: 6px; padding: 4px 2px; text-align: center;">
                                                                        <span style="font-size: 0.62rem; color: #64748b; font-weight: 700; display: block; line-height: 1.1;">{{ $wk['label'] }}</span>
                                                                        <span style="font-size: 0.76rem; font-weight: 800; color: {{ $wk['color'] }}; display: block; margin-top: 2px;">
                                                                            {{ $wk['val'] }}
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif

                                                        <div style="font-size: 0.74rem; color: #475569; background: #f1f5f9; padding: 6px 9px; border-radius: 6px; line-height: 1.35; font-weight: 500;">
                                                            <i class="fas fa-circle-info" style="color: #6366f1; margin-right: 3px;"></i> {{ $dot['calc_explanation'] }}
                                                        </div>
                                                    </div>

                                                    <!-- Observaciones / Cierre Notas (if any) -->
                                                    @if(!empty($dot['cierre_notas']))
                                                        <div style="margin-top: 0.55rem; background: #fefce8; border-left: 3px solid #f59e0b; padding: 6px 9px; border-radius: 0 6px 6px 0;">
                                                            <div style="font-size: 0.68rem; font-weight: 800; color: #92400e; margin-bottom: 2px; text-transform: uppercase;">
                                                                <i class="fas fa-comment-dots" style="margin-right: 3px;"></i> Cierre / Observaciones:
                                                            </div>
                                                            <div style="font-size: 0.74rem; color: #78350f; font-style: italic; line-height: 1.3;">
                                                                "{{ $dot['cierre_notas'] }}"
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Compromisos / Inicio Notas (if any) -->
                                                    @if(!empty($dot['inicio_notas']))
                                                        <div style="margin-top: 0.45rem; background: #eef2ff; border-left: 3px solid #6366f1; padding: 6px 9px; border-radius: 0 6px 6px 0;">
                                                            <div style="font-size: 0.68rem; font-weight: 800; color: #4338ca; margin-bottom: 2px; text-transform: uppercase;">
                                                                <i class="fas fa-flag" style="margin-right: 3px;"></i> Compromisos de Inicio:
                                                            </div>
                                                            <div style="font-size: 0.74rem; color: #312e81; font-style: italic; line-height: 1.3;">
                                                                "{{ $dot['inicio_notas'] }}"
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Cumplimiento Total por Área con Popover de Desglose Completo -->
                            <td style="padding: 1rem 0.8rem; text-align: center; white-space: nowrap;">
                                <div class="kpi-tooltip-wrap" onclick="event.stopPropagation();" style="width: 140px; margin: 0 auto; display: flex; flex-direction: column; align-items: center; cursor: help;">
                                    <span style="font-weight: 900; font-size: 1.1rem; color: {{ $item['color'] }};">
                                        {{ number_format($item['cumplimiento_junio'], 1) }}%
                                    </span>
                                    <div style="width: 100%; height: 7px; background: #e2e8f0; border-radius: 9999px; overflow: hidden; margin-top: 0.35rem;">
                                        <div style="width: {{ min(100, max(0, $item['cumplimiento_junio'])) }}%; height: 100%; background: {{ $item['color'] }}; border-radius: 9999px; transition: width 0.4s ease;"></div>
                                    </div>

                                    <!-- Popover Body for Area Total -->
                                    <div class="kpi-popover kpi-popover-right">
                                        <!-- Top Accent Bar -->
                                        <div style="height: 4px; background: {{ $item['color'] }}; border-radius: 1rem 1rem 0 0;"></div>

                                        <div style="padding: 1.1rem 1.25rem;">
                                            <!-- Header -->
                                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.4rem;">
                                                <span style="background: #eef2ff; color: #6366f1; font-size: 0.68rem; font-weight: 800; padding: 2px 8px; border-radius: 6px; text-transform: uppercase;">
                                                    <i class="fas fa-layer-group" style="margin-right: 3px;"></i> Consolidado de Área
                                                </span>
                                                <span style="font-size: 0.72rem; color: #64748b; font-weight: 700;">
                                                    {{ $latestMonthName }} 2026
                                                </span>
                                            </div>

                                            <div style="font-weight: 900; color: #0f172a; font-size: 1.05rem; line-height: 1.2; margin-bottom: 0.5rem;">
                                                {{ $item['area'] }}
                                            </div>

                                            <!-- Total Score & Status -->
                                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 0.65rem 0.85rem; margin-bottom: 0.75rem; display: flex; align-items: center; justify-content: space-between;">
                                                <div>
                                                    <span style="display: block; font-size: 0.68rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Cumplimiento Global</span>
                                                    <span style="font-size: 1.4rem; font-weight: 900; color: {{ $item['color'] }}; line-height: 1.1;">
                                                        {{ number_format($item['cumplimiento_junio'], 1) }}%
                                                    </span>
                                                </div>
                                                <span style="background: {{ $item['badgeBg'] }}; color: {{ $item['color'] }}; font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 9999px;">
                                                    {{ $item['label'] }}
                                                </span>
                                            </div>

                                            <!-- KPI Status Counters Pills -->
                                            <div style="display: flex; gap: 4px; margin-bottom: 0.75rem; flex-wrap: wrap;">
                                                <span style="background: #d1fae5; color: #065f46; font-size: 0.68rem; font-weight: 800; padding: 3px 7px; border-radius: 6px; flex: 1; text-align: center;">
                                                    🟢 {{ $item['kpis_en_meta_count'] }} En Meta
                                                </span>
                                                <span style="background: #fef3c7; color: #92400e; font-size: 0.68rem; font-weight: 800; padding: 3px 7px; border-radius: 6px; flex: 1; text-align: center;">
                                                    🟡 {{ $item['kpis_en_prevencion_count'] }} Prevención
                                                </span>
                                                <span style="background: #fee2e2; color: #991b1b; font-size: 0.68rem; font-weight: 800; padding: 3px 7px; border-radius: 6px; flex: 1; text-align: center;">
                                                    🔴 {{ $item['kpis_en_atencion_count'] }} Atención
                                                </span>
                                            </div>

                                            <!-- Desglose de KPIs que integran el porcentaje -->
                                            <div style="border-top: 1px solid #f1f5f9; padding-top: 0.6rem;">
                                                <div style="font-size: 0.75rem; font-weight: 800; color: #334155; margin-bottom: 0.4rem; display: flex; align-items: center; justify-content: space-between;">
                                                    <span><i class="fas fa-list-check" style="color: #6366f1; margin-right: 3px;"></i> Indicadores que componen el promedio:</span>
                                                    <span style="font-size: 0.7rem; color: #64748b; font-weight: 700;">{{ $item['total_kpis'] }} KPIs</span>
                                                </div>

                                                <div class="kpi-popover-list" style="max-height: 140px; overflow-y: auto; display: flex; flex-direction: column; gap: 4px; margin-bottom: 0.6rem; padding-right: 2px;">
                                                    @foreach($item['kpi_dots_junio'] as $idx => $kpiDot)
                                                        <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px 8px; font-size: 0.72rem;">
                                                            <span style="font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 210px;" title="{{ $kpiDot['name'] }}">
                                                                {{ $idx + 1 }}. {{ $kpiDot['name'] }}
                                                            </span>
                                                            <span style="font-weight: 800; color: {{ $kpiDot['color'] }}; margin-left: 6px; flex-shrink: 0;">
                                                                {{ $kpiDot['val'] }}
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <!-- Comparativa vs mes anterior -->
                                                <div style="background: #f1f5f9; border-radius: 6px; padding: 6px 9px; font-size: 0.72rem; color: #475569; display: flex; align-items: center; justify-content: space-between;">
                                                    <span><i class="fas fa-arrow-right-arrow-left" style="color: #64748b; margin-right: 3px;"></i> Mes anterior ({{ $prevMonthName }}): <strong>{{ number_format($item['cumplimiento_mayo'], 1) }}%</strong></span>
                                                    <span style="color: {{ $item['trendColor'] }}; font-weight: 800;">
                                                        <i class="fas {{ $item['trendIcon'] }}"></i> {{ $item['trendText'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    <!-- MODE 2: DETAILED KPI TABLE FOR A SPECIFIC AREA -->
    @else
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            @if($isAdminUser)
                <button wire:click="selectArea('')" style="background: white; border: 1.5px solid #cbd5e1; color: #475569; padding: 0.5rem 1rem; border-radius: 0.6rem; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                    <i class="fas fa-arrow-left"></i> Volver a Vista Corporativa
                </button>
            @else
                <div></div>
            @endif

            <button wire:click="openCreateModal" style="background: white; border: 1.5px solid #3b82f6; color: #2563eb; padding: 0.55rem 1.25rem; border-radius: 9999px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <i class="fas fa-plus" style="font-size: 0.75rem;"></i> Nuevo KPI
            </button>
        </div>

        <!-- Main KPIs Table Card for Area -->
        <div style="background: white; border-radius: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); padding: 1.5rem; overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0 1rem; font-family: inherit;">
                <thead>
                    <tr>
                        <th style="text-align: left; padding: 0.75rem 1rem; color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f1f5f9; min-width: 240px;">
                            NOMBRE DE KPI
                        </th>
                        <th style="text-align: center; padding: 0.75rem 1rem; color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f1f5f9; min-width: 130px;">
                            MEDIA (META)
                        </th>
                        @foreach($weeksList as $wNum => $week)
                            <th style="text-align: center; padding: 0.75rem 0.5rem; color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f1f5f9; min-width: 80px;">
                                {{ $week['label'] }}<br>
                                <span style="font-size: 0.65rem; color: #94a3b8; font-weight: 500; text-transform: none;">({{ $week['range'] }})</span>
                            </th>
                        @endforeach
                        <th style="text-align: center; padding: 0.75rem 0.5rem; color: #475569; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f1f5f9; min-width: 110px; background: #f8fafc;">
                            TOTAL MENSUAL
                        </th>
                        <th style="text-align: left; padding: 0.75rem 1rem; color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f1f5f9; min-width: 220px;">
                            INICIO DE SEMANA
                        </th>
                        <th style="text-align: left; padding: 0.75rem 1rem; color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f1f5f9; min-width: 220px;">
                            CIERRE DE SEMANA
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kpis as $kpi)
                        <tr wire:key="kpi-row-{{ $kpi->id }}-{{ $selectedYear }}-{{ $selectedMonth }}" style="border-bottom: 1px solid #f8fafc;">
                            <!-- KPI Name & Description + Actions -->
                            <td style="padding: 1rem; vertical-align: top;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
                                    <div>
                                        <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem; margin-bottom: 0.25rem;">
                                            {{ $kpi->name }}
                                        </div>
                                        @if($kpi->description)
                                            <div style="font-size: 0.78rem; color: #64748b; font-weight: 400; line-height: 1.3;">
                                                {{ $kpi->description }}
                                            </div>
                                        @endif
                                    </div>
                                    <div style="display: flex; gap: 0.35rem; flex-shrink: 0;">
                                        <button wire:click="editKpi({{ $kpi->id }})" title="Editar KPI" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #2563eb; width: 28px; height: 28px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; transition: background 0.2s;" onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                                            <i class="fas fa-pencil"></i>
                                        </button>
                                        <button wire:click="deleteKpi({{ $kpi->id }})" wire:confirm="¿Estás seguro de eliminar este KPI?" title="Eliminar KPI" style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; width: 28px; height: 28px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; transition: background 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>

                            <!-- Meta Legend Card -->
                            <td wire:key="kpi-meta-{{ $kpi->id }}-{{ $selectedYear }}-{{ $selectedMonth }}" style="padding: 1rem; vertical-align: top; text-align: center;">
                                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 10px; font-size: 0.7rem; font-weight: 600; display: inline-flex; flex-direction: column; gap: 4px; text-align: left;">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                                        <span style="color: #334155;">&ge; 95%</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span>
                                        <span style="color: #334155;">80% - 94%</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                                        <span style="color: #334155;">&lt; 80%</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Weekly Values Columns -->
                            @foreach($weeksList as $wNum => $week)
                                @php
                                    $wInfo = $kpiValues[$kpi->id][$wNum] ?? ['val' => '-', 'date' => ''];
                                    $val = $wInfo['val'];
                                    $dateStr = $wInfo['date'];

                                    $dotColor = '#94a3b8'; // default grey
                                    if ($val !== '-' && $val !== '' && is_numeric($val)) {
                                        $num = (float)$val;
                                        if ($num >= 95.00) {
                                            $dotColor = '#10b981'; // green
                                        } elseif ($num >= 80.00) {
                                            $dotColor = '#f59e0b'; // yellow
                                        } else {
                                            $dotColor = '#ef4444'; // red
                                        }
                                    }
                                @endphp
                                <td wire:key="kpi-week-cell-{{ $kpi->id }}-{{ $wNum }}-{{ $selectedYear }}-{{ $selectedMonth }}" style="padding: 0.75rem 0.25rem; vertical-align: top; text-align: center;">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 6px;">
                                        <input type="text"
                                            wire:model.defer="kpiValues.{{ $kpi->id }}.{{ $wNum }}.val"
                                            value="{{ $val }}"
                                            data-target="95"
                                            oninput="updateKpiDot(this)"
                                            style="width: 52px; height: 34px; border: 1.5px solid #cbd5e1; border-radius: 8px; text-align: center; font-weight: 700; font-size: 0.85rem; color: #1e293b; background: white; outline: none; transition: border-color 0.2s;" />
                                        
                                        <span class="kpi-dot-indicator" style="width: 10px; height: 10px; border-radius: 50%; background: {{ $dotColor }}; display: inline-block; transition: background 0.3s;"></span>

                                        <span style="font-size: 0.65rem; color: #94a3b8; font-weight: 500; min-height: 14px; display: block;">
                                            {{ $dateStr }}
                                        </span>
                                    </div>
                                </td>
                            @endforeach

                            <!-- Monthly Acumulado Column with Interactive Popover -->
                            @php
                                $mVal = $kpiMonthlyValues[$kpi->id] ?? '-';
                                $mDotColor = '#94a3b8'; // default grey
                                $mStatusText = 'Sin Registro';
                                $mStatusBg = '#f1f5f9';
                                $mStatusColor = '#64748b';

                                if ($mVal !== '-' && $mVal !== '' && is_numeric($mVal)) {
                                    $num = (float)$mVal;
                                    if ($num >= 95.00) {
                                        $mDotColor = '#10b981'; // green
                                        $mStatusText = 'En Meta (≥ 95%)';
                                        $mStatusBg = '#d1fae5';
                                        $mStatusColor = '#065f46';
                                    } elseif ($num >= 80.00) {
                                        $mDotColor = '#f59e0b'; // yellow
                                        $mStatusText = 'Prevención (80% - 94%)';
                                        $mStatusBg = '#fef3c7';
                                        $mStatusColor = '#92400e';
                                    } else {
                                        $mDotColor = '#ef4444'; // red
                                        $mStatusText = 'Atención Requerida (< 80%)';
                                        $mStatusBg = '#fee2e2';
                                        $mStatusColor = '#991b1b';
                                    }
                                }
                            @endphp
                            <td wire:key="kpi-monthly-cell-{{ $kpi->id }}-{{ $selectedYear }}-{{ $selectedMonth }}" style="padding: 0.75rem 0.25rem; vertical-align: top; text-align: center; background: #f8fafc; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0;">
                                <div class="kpi-tooltip-wrap" style="cursor: help;">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 6px; padding: 4px 8px; border-radius: 8px; transition: background 0.15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                        <span style="font-weight: 800; font-size: 0.95rem; color: #1e293b;">
                                            {{ $mVal !== '-' ? $mVal . '%' : '-' }}
                                        </span>
                                        <span style="width: 10px; height: 10px; border-radius: 50%; background: {{ $mDotColor }}; display: inline-block;"></span>
                                    </div>

                                    <!-- Popover for Monthly Total -->
                                    <div class="kpi-popover">
                                        <div style="height: 4px; background: {{ $mDotColor }}; border-radius: 1rem 1rem 0 0;"></div>
                                        <div style="padding: 1rem 1.15rem;">
                                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.3rem;">
                                                <span style="background: #eef2ff; color: #6366f1; font-size: 0.68rem; font-weight: 800; padding: 2px 8px; border-radius: 6px; text-transform: uppercase;">
                                                    Total Mensual
                                                </span>
                                                <span style="font-size: 0.72rem; color: #64748b; font-weight: 700;">
                                                    Meta: &ge; 95%
                                                </span>
                                            </div>
                                            <div style="font-weight: 800; color: #0f172a; font-size: 0.9rem; margin-bottom: 0.5rem;">
                                                {{ $kpi->name }}
                                            </div>
                                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.6rem; padding: 0.55rem 0.75rem; margin-bottom: 0.5rem; display: flex; align-items: center; justify-content: space-between;">
                                                <span style="font-size: 1.25rem; font-weight: 900; color: {{ $mDotColor }};">
                                                    {{ $mVal !== '-' ? $mVal . '%' : '-' }}
                                                </span>
                                                <span style="background: {{ $mStatusBg }}; color: {{ $mStatusColor }}; font-size: 0.72rem; font-weight: 800; padding: 3px 8px; border-radius: 9999px;">
                                                    {{ $mStatusText }}
                                                </span>
                                            </div>
                                            <div style="font-size: 0.72rem; color: #475569; background: #f1f5f9; padding: 6px 8px; border-radius: 6px; line-height: 1.35;">
                                                <i class="fas fa-calculator" style="color: #6366f1; margin-right: 3px;"></i> Promedio resultante de las semanas capturadas en este mes.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Inicio de Semana Field -->
                            <td wire:key="kpi-startnote-cell-{{ $kpi->id }}-{{ $selectedYear }}-{{ $selectedMonth }}" style="padding: 1rem; vertical-align: top;">
                                <textarea
                                    wire:model.defer="kpiStartNotes.{{ $kpi->id }}"
                                    placeholder="Inicio de semana..."
                                    rows="3"
                                    style="width: 100%; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 8px 10px; font-size: 0.78rem; font-family: inherit; color: #334155; outline: none; resize: vertical; min-height: 52px; line-height: 1.35; background: white; transition: border-color 0.2s;"></textarea>
                            </td>

                            <!-- Cierre de Semana Field -->
                            <td wire:key="kpi-note-cell-{{ $kpi->id }}-{{ $selectedYear }}-{{ $selectedMonth }}" style="padding: 1rem; vertical-align: top;">
                                <textarea
                                    wire:model.defer="kpiNotes.{{ $kpi->id }}"
                                    placeholder="Cierre de semana..."
                                    rows="3"
                                    style="width: 100%; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 8px 10px; font-size: 0.78rem; font-family: inherit; color: #334155; outline: none; resize: vertical; min-height: 52px; line-height: 1.35; background: white; transition: border-color 0.2s;"></textarea>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 3rem; color: #64748b;">
                                <i class="fas fa-chart-pie" style="font-size: 2rem; margin-bottom: 0.5rem; display: block; opacity: 0.4;"></i>
                                No hay KPI´s registrados para el área de <strong>{{ $selectedArea }}</strong>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Bottom Save Button -->
            <div style="display: flex; justify-content: flex-end; margin-top: 1.75rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                <button wire:click="saveChanges" style="background: #059669; color: white; border: none; padding: 0.75rem 1.75rem; border-radius: 0.6rem; font-weight: 700; font-size: 0.95rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.6rem; transition: all 0.2s; box-shadow: 0 4px 10px rgba(5, 150, 105, 0.25);">
                    <i class="fas fa-floppy-disk"></i> Guardar Cambios
                </button>
            </div>
        </div>

        <!-- Global Average Footer Section with Popovers -->
        <div style="background: white; border-radius: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); padding: 1.25rem 1.75rem; margin-top: 2rem; display: flex; flex-direction: column; gap: 1rem;">
            <div style="text-align: center; font-weight: 800; color: #1e293b; font-size: 1.05rem; letter-spacing: -0.2px;">
                Promedio Global de KPIs por Semana ({{ $latestMonthName }} - {{ $selectedArea }})
            </div>
            <div style="display: flex; justify-content: center; gap: 1.25rem; flex-wrap: wrap;">
                @foreach($weeksList as $wNum => $week)
                    @php
                        $avgData = $weeklyAverages[$wNum] ?? ['avg' => '-', 'count' => 0, 'total' => 0, 'kpis' => []];
                        $avg = is_array($avgData) ? ($avgData['avg'] ?? '-') : $avgData;
                        $countCapturados = is_array($avgData) ? ($avgData['count'] ?? 0) : 0;
                        $kpisListSem = is_array($avgData) ? ($avgData['kpis'] ?? []) : [];

                        $badgeBg = '#f1f5f9';
                        $badgeColor = '#64748b';

                        if ($avg !== '-' && is_numeric($avg)) {
                            if ($avg >= 95) {
                                $badgeBg = '#d1fae5';
                                $badgeColor = '#065f46';
                            } elseif ($avg >= 80) {
                                $badgeBg = '#fef3c7';
                                $badgeColor = '#92400e';
                            } else {
                                $badgeBg = '#fee2e2';
                                $badgeColor = '#991b1b';
                            }
                        }
                    @endphp
                    <div class="kpi-tooltip-wrap" style="cursor: help;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px; background: #f8fafc; padding: 0.75rem 1.25rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; min-width: 90px; transition: border-color 0.2s;" onmouseover="this.style.borderColor='#6366f1'" onmouseout="this.style.borderColor='#e2e8f0'">
                            <span style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">{{ $week['label'] }}</span>
                            <span style="font-size: 1.1rem; font-weight: 800; background: {{ $badgeBg }}; color: {{ $badgeColor }}; padding: 2px 10px; border-radius: 9999px; display: inline-block;">
                                {{ $avg !== '-' ? $avg . '%' : '-' }}
                            </span>
                        </div>

                        <!-- Popover for Weekly Average -->
                        <div class="kpi-popover">
                            <div style="height: 4px; background: {{ $badgeColor }}; border-radius: 1rem 1rem 0 0;"></div>
                            <div style="padding: 1rem 1.15rem;">
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.3rem;">
                                    <span style="background: #eef2ff; color: #6366f1; font-size: 0.68rem; font-weight: 800; padding: 2px 8px; border-radius: 6px; text-transform: uppercase;">
                                        {{ $week['label'] }} ({{ $week['range'] }})
                                    </span>
                                    <span style="font-size: 0.72rem; color: #64748b; font-weight: 700;">
                                        {{ $selectedArea }}
                                    </span>
                                </div>
                                <div style="font-weight: 900; color: #0f172a; font-size: 0.95rem; margin-bottom: 0.5rem;">
                                    Promedio de la Semana: <span style="color: {{ $badgeColor }};">{{ $avg !== '-' ? $avg . '%' : 'Sin evaluaciones' }}</span>
                                </div>

                                @if(!empty($kpisListSem))
                                    <div class="kpi-popover-list" style="max-height: 120px; overflow-y: auto; display: flex; flex-direction: column; gap: 3px; margin-bottom: 0.5rem;">
                                        @foreach($kpisListSem as $kItem)
                                            <div style="display: flex; justify-content: space-between; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 5px; padding: 3px 6px; font-size: 0.7rem;">
                                                <span style="font-weight: 600; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 200px;">{{ $kItem['name'] }}</span>
                                                <span style="font-weight: 800; color: #6366f1;">{{ $kItem['val'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div style="font-size: 0.72rem; color: #475569; background: #f1f5f9; padding: 6px 8px; border-radius: 6px; line-height: 1.35;">
                                    <i class="fas fa-calculator" style="color: #6366f1; margin-right: 3px;"></i> Promedio de {{ $countCapturados }} indicador(es) evaluados en esta semana.
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Modal for Creating New KPI -->
    @if($showModal)
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 999; animation: fadeIn 0.2s ease-out;">
            <div style="background: white; border-radius: 1rem; width: 100%; max-width: 500px; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #0f172a;">
                        <i class="fas {{ $editingKpiId ? 'fa-pen-to-square' : 'fa-plus-circle' }}" style="color: #3b82f6; margin-right: 0.4rem;"></i> {{ $editingKpiId ? 'Editar KPI' : 'Agregar Nuevo KPI' }}
                    </h3>
                    <button wire:click="closeModal" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #64748b;">
                        &times;
                    </button>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 0.4rem;">
                        Área de Negocio
                    </label>
                    <input type="text" value="{{ !empty($selectedArea) ? $selectedArea : 'CONTABILIDAD' }}" readonly style="width: 100%; padding: 0.65rem 0.85rem; border: 1.5px solid #cbd5e1; border-radius: 0.5rem; background: #f8fafc; font-weight: 700; color: #475569;" />
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 0.4rem;">
                        Nombre del KPI *
                    </label>
                    <input type="text" wire:model.defer="newKpiName" placeholder="Ej. Cumplimiento de entregas" style="width: 100%; padding: 0.65rem 0.85rem; border: 1.5px solid #cbd5e1; border-radius: 0.5rem; font-weight: 600; color: #0f172a; outline: none;" />
                    @error('newKpiName')
                        <span style="color: #ef4444; font-size: 0.75rem; font-weight: 600;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 0.4rem;">
                        Descripción
                    </label>
                    <textarea wire:model.defer="newKpiDescription" placeholder="Breve descripción del indicador..." rows="3" style="width: 100%; padding: 0.65rem 0.85rem; border: 1.5px solid #cbd5e1; border-radius: 0.5rem; font-family: inherit; font-size: 0.85rem; color: #0f172a; outline: none;"></textarea>
                </div>

                <div style="margin-bottom: 1.75rem;">
                    <label style="display: block; font-weight: 700; font-size: 0.85rem; color: #334155; margin-bottom: 0.4rem;">
                        Meta (%)
                    </label>
                    <input type="number" wire:model.defer="newKpiTarget" min="0" max="100" style="width: 100%; padding: 0.65rem 0.85rem; border: 1.5px solid #cbd5e1; border-radius: 0.5rem; font-weight: 700; color: #0f172a; outline: none;" />
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button wire:click="closeModal" style="background: #f1f5f9; color: #475569; border: none; padding: 0.6rem 1.2rem; border-radius: 0.5rem; font-weight: 600; cursor: pointer;">
                        Cancelar
                    </button>
                    <button wire:click="saveNewKpi" style="background: #2563eb; color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 0.5rem; font-weight: 700; cursor: pointer;">
                        {{ $editingKpiId ? 'Guardar Cambios' : 'Guardar KPI' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <script>
        function updateKpiDot(el) {
            if (!el) return;
            const val = el.value.trim();
            const container = el.parentElement;
            if (!container) return;
            const dot = container.querySelector('.kpi-dot-indicator');
            if (!dot) return;

            const targetVal = parseFloat(el.getAttribute('data-target') || '95');

            if (val === '' || val === '-' || isNaN(val) || parseFloat(val) < 0) {
                dot.style.backgroundColor = '#94a3b8'; // Grey
            } else {
                const num = parseFloat(val);
                if (num >= targetVal) {
                    dot.style.backgroundColor = '#10b981'; // Green
                } else if (num >= 80) {
                    dot.style.backgroundColor = '#f59e0b'; // Yellow
                } else {
                    dot.style.backgroundColor = '#ef4444'; // Red
                }
            }
        }
    </script>
</div>
