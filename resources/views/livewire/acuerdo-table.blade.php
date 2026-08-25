<div wire:poll.600s>
    <style>
        .table-container th {
            font-weight: 800 !important; 
            color: #1e293b !important;
        }
    </style>
    <div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; align-items: center;">
        <select wire:model.live="status"
            style="padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; outline: none; font-family: inherit; background: white; cursor: pointer;">
            <option value="">Todos los estatus</option>
            <option value="pendiente">Pendiente</option>
            <option value="en_proceso">En Proceso</option>
            <option value="detenido">Detenido</option>
        </select>

        @if(auth()->user()->hasRole('Administrador') || auth()->user()->email === 'v.arochi@mapetzin.com' || auth()->user()->email === 'gerencia_serv_gobierno@lesli.com.mx')
            <select wire:model.live="selectedArea"
                style="padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; outline: none; font-family: inherit; background: white; cursor: pointer;">
                <option value="">Todas las áreas</option>
                @foreach ($areas as $area)
                    <option value="{{ $area }}">{{ $area }}</option>
                @endforeach
            </select>
        @endif

        <select wire:model.live="cuadrante"
            style="padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; outline: none; font-family: inherit; background: white; cursor: pointer;">
            <option value="">Todos los cuadrantes</option>
            <option value="1">Cuadrante 1</option>
            <option value="2">Cuadrante 2</option>
        </select>

    </div>

    @if($sin_actualizar->count() > 0)
        <div
            style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 1rem; padding: 1rem 1.5rem; margin-bottom: 2rem; display: flex; align-items: flex-start; gap: 1rem; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.1);">
            <div
                style="background: #f59e0b; color: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; margin-top: 2px;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div style="flex: 1;">
                @if(auth()->user()->hasRole('Administrador') || auth()->user()->email === 'v.arochi@mapetzin.com' || auth()->user()->email === 'gerencia_serv_gobierno@lesli.com.mx')
                    <p style="color: #92400e; margin: 0; font-size: 0.95rem; font-weight: 500;">
                        <strong>Atención:</strong> Las siguientes áreas aún no registran avances en sus acuerdos para el día de
                        hoy:
                    </p>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.75rem;">
                        @foreach($sin_actualizar as $item)
                            <span
                                style="background: rgba(245, 158, 11, 0.1); color: #92400e; padding: 0.35rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 700; border: 1px solid rgba(245, 158, 11, 0.2); display: flex; align-items: center; gap: 0.4rem;">
                                <span style="width: 6px; height: 6px; background: #f59e0b; border-radius: 50%;"></span>
                                {{ $item->area }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p style="color: #92400e; margin: 0; font-size: 1rem; font-weight: 700; padding-top: 5px;">
                        No has registrado avances en tus acuerdos para el día de hoy
                    </p>
                @endif
            </div>
        </div>
    @endif

    <div class="table-container">
        <table>
            <thead>
                <tr style="text-transform: uppercase; font-weight: bold;">
                    <th style="cursor: pointer;" wire:click="sortBy('id')">ID
                        @if($sortField === 'id') <i
                        class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                    </th>
                    <th style="cursor: pointer; text-transform: uppercase;" wire:click="sortBy('area')">Área y Nombre<br>de responsable
                        @if($sortField === 'area') <i
                        class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                    </th>
                    <th>Cuadrante</th>
                    <th style="cursor: pointer;" wire:click="sortBy('actividad')">KPI Y METRICA<br>ALCANZADA
                        @if ($sortField === 'actividad')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th style="cursor: pointer;" wire:click="sortBy('acuerdo')">Acuerdo<br>(ESTADO REAL DEL INDICADOR)
                        @if ($sortField === 'acuerdo')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th style="white-space: nowrap;">COMPROMISO<br>DÍA LUNES</th>
                    <th style="white-space: nowrap;">CIERRE<br>DÍA VIERNES</th>
                    <th style="cursor: pointer; white-space: nowrap;" wire:click="sortBy('fecha_compromiso')">FECHA<br>COMPROMISO
                        @if($sortField === 'fecha_compromiso') <i
                        class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                    </th>
                    <th>Estatus</th>
                    <th>Avance</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($acuerdos as $acuerdo)
                    @php
                        $today = \Carbon\Carbon::today();
                        $fechaCompromiso = $acuerdo->fecha_compromiso;
                        $diffInDays = $today->diffInDays($fechaCompromiso, false);

                        $rowStyle = '';
                        if ($acuerdo->estatus === 'pendiente') {
                            $rowStyle = 'background-color: rgba(245, 158, 11, 0.15);';
                        } elseif ($acuerdo->estatus !== 'finalizado') {
                            if ($diffInDays < 0) {
                                // Overdue (Red)
                                $rowStyle = 'background-color: rgba(239, 68, 68, 0.15);';
                            } elseif ($diffInDays <= 2) {
                                // Near deadline (Yellow)
                                $rowStyle = 'background-color: rgba(245, 158, 11, 0.15);';
                            } elseif ($acuerdo->estatus === 'en_proceso') {
                                // On time and in process (Verde Agua) -> #0d9488 with opacity
                                $rowStyle = 'background-color: rgba(13, 148, 136, 0.15);';
                            }
                        }
                    @endphp
                    <tr style="{{ $rowStyle }}">
                        <td>#{{ $acuerdo->id }}</td>
                        <td style="text-transform: uppercase;">{{ $acuerdo->area }} - {{ $acuerdo->responsable }}</td>
                        <td><span
                                class="badge {{ $acuerdo->tipo_cuadrante == 1 ? 'badge-blue' : 'badge-yellow' }}">C{{ $acuerdo->tipo_cuadrante }}</span>
                        </td>
                        <td>
                            <div style="font-weight: 500;">{{ $acuerdo->actividad }}</div>
                        </td>
                        <td>
                            <div>{{ $acuerdo->acuerdo }}</div>
                            <div style="font-size: 0.75rem; color: var(--secondary); margin-bottom: 0.25rem;">
                                {{ Str::limit($acuerdo->descripcion, 50) }}
                            </div>
                            @if($acuerdo->latestComment)
                                <div style="font-size: 0.75rem; color: #6366f1; font-weight: 500; display: flex; align-items: center; gap: 0.3rem;">
                                    <i class="fas fa-comment-dots" style="font-size: 0.65rem;"></i>
                                    {{ Str::limit($acuerdo->latestComment->comentario, 60) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div>{{ Str::limit($acuerdo->compromiso_lunes, 150) ?? '-' }}</div>
                        </td>
                        <td>
                            <div>{{ Str::limit($acuerdo->cierre_viernes, 150) ?? '-' }}</div>
                        </td>
                        <td>
                            <span
                                style="color: {{ ($diffInDays < 0) && $acuerdo->estatus !== 'finalizado' ? 'var(--danger)' : 'inherit' }}; font-weight: {{ ($diffInDays < 0) && $acuerdo->estatus !== 'finalizado' ? '600' : 'normal' }}">
                                {{ $acuerdo->fecha_compromiso->format('d/m/Y') }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusClass = match ($acuerdo->estatus) {
                                    'pendiente' => 'badge-yellow',
                                    'en_proceso' => 'badge-blue',
                                    'detenido' => 'badge-red',
                                    'finalizado' => 'badge-green',
                                    default => ''
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ str_replace('_', ' ', $acuerdo->estatus) }}</span>
                        </td>
                        <td>
                            @php
                                $sem = $acuerdo->semaforo;
                            @endphp
                            @if($acuerdo->isAtrasoCritico())
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="background: linear-gradient(135deg, #dc2626, #991b1b); color: white; padding: 0.35rem 0.75rem; border-radius: 6px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 0.4rem; animation: pulse-critical 1.5s ease-in-out infinite; white-space: nowrap;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        ATRASO CRÍTICO
                                    </span>
                                </div>
                            @else
                                <div style="display: flex; align-items: center; gap: 0.5rem; white-space: nowrap;">
                                    <span style="width: 9px; height: 9px; border-radius: 50%; background: {{ $sem['color'] }}; display: inline-block;" title="{{ $sem['label'] }}"></span>
                                    <div class="progress-bar" style="width: 60px;">
                                        <div class="progress-fill"
                                            style="width: {{ $acuerdo->porcentaje_avance }}%; background: {{ $sem['color'] }};">
                                        </div>
                                    </div>
                                    <span style="font-size: 0.8rem; font-weight: 700; color: {{ $sem['color'] }};">{{ $acuerdo->porcentaje_avance }}%</span>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('acuerdos.show', $acuerdo) }}" class="btn"
                                    style="padding: 0.4rem; background: #f1f5f9; color: var(--secondary);"
                                    title="Ver Detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('acuerdos.edit', $acuerdo) }}" class="btn"
                                    style="padding: 0.4rem; background: #f1f5f9; color: var(--info);" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 3rem; color: var(--secondary);">
                            <i class="fas fa-folder-open" style="font-size: 2rem; display: block; margin-bottom: 1rem;"></i>
                            No se encontraron acuerdos con los filtros aplicados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 2rem;">
        {{ $acuerdos->links() }}
    </div>
</div>