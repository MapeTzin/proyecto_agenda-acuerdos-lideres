<div>
    <style>
        .table-container th {
            font-weight: 800 !important; 
            color: #1e293b !important;
        }
    </style>
    <div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; align-items: center;">
        <input type="text" wire:model.live="search" placeholder="Buscar en historial..."
            style="padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; outline: none; flex: 1; min-width: 250px;">

        @if(auth()->user()->hasRole('Administrador') || auth()->user()->email === 'v.arochi@mapetzin.com' || auth()->user()->email === 'gerencia_serv_gobierno@lesli.com.mx')
            <select wire:model.live="selectedArea"
                style="padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; outline: none; font-family: inherit; background: white; cursor: pointer;">
                <option value="">Todas las áreas</option>
                @foreach ($areas as $area)
                    <option value="{{ $area }}">{{ $area }}</option>
                @endforeach
            </select>
        @endif
    </div>

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
                    <th style="cursor: pointer;" wire:click="sortBy('acuerdo')">Acuerdo (ESTADO REAL DEL INDICADOR)<br>/ KPI Y METRICA ALCANZADA
                        @if ($sortField === 'acuerdo') <i
                        class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                    </th>
                    <th style="white-space: nowrap;">COMPROMISO<br>DÍA LUNES</th>
                    <th style="white-space: nowrap;">CIERRE<br>DÍA VIERNES</th>
                    <th style="cursor: pointer; white-space: nowrap;" wire:click="sortBy('fecha_cierre')">FECHA<br>CIERRE
                        @if($sortField === 'fecha_cierre') <i
                        class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                    </th>
                    <th>ICO Final</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($acuerdos as $acuerdo)
                    <tr>
                        <td>#{{ $acuerdo->id }}</td>
                        <td style="text-transform: uppercase;">{{ $acuerdo->area }} - {{ $acuerdo->responsable }}</td>
                        <td>
                            <div>{{ $acuerdo->acuerdo }}</div>
                            <div style="font-size: 0.8rem; color: var(--secondary);">{{ $acuerdo->actividad }}</div>
                        </td>
                        <td>
                            <div>{{ Str::limit($acuerdo->compromiso_lunes, 150) ?? '-' }}</div>
                        </td>
                        <td>
                            <div>{{ Str::limit($acuerdo->cierre_viernes, 150) ?? '-' }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--success);">
                                {{ $acuerdo->fecha_cierre ? $acuerdo->fecha_cierre->format('d/m/Y') : 'N/A' }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--secondary);">
                                Meta: {{ $acuerdo->fecha_compromiso->format('d/m/Y') }}
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-green">100% Finalizado</span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('acuerdos.show', $acuerdo) }}" class="btn"
                                    style="padding: 0.4rem; background: #f1f5f9; color: var(--secondary);"
                                    title="Ver Detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 3rem; color: var(--secondary);">
                            <i class="fas fa-history" style="font-size: 2rem; display: block; margin-bottom: 1rem;"></i>
                            No hay acuerdos finalizados en el histórico todavía.
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