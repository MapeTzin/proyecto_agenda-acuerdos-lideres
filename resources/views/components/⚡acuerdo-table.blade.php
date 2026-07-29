<?php

use Livewire\Volt\Component;
use App\Models\Acuerdo;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $area = '';
    public $estatus = '';
    public $prioridad = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingArea()
    {
        $this->resetPage();
    }
    public function updatingEstatus()
    {
        $this->resetPage();
    }
    public function updatingPrioridad()
    {
        $this->resetPage();
    }

    public function with()
    {
        $query = Acuerdo::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('acuerdo', 'like', '%' . $this->search . '%')
                    ->orWhere('responsable', 'like', '%' . $this->search . '%')
                    ->orWhere('descripcion', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->area) {
            $query->where('area', $this->area);
        }

        if ($this->estatus) {
            $query->where('estatus', $this->estatus);
        }

        if ($this->prioridad) {
            $query->where('prioridad', $this->prioridad);
        }

        return [
            'acuerdos' => $query->orderBy('fecha_compromiso', 'asc')->paginate(10),
            'areas' => Acuerdo::select('area')->distinct()->pluck('area'),
        ];
    }
};
?>

<div>
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <input type="text" wire:model.live="search" placeholder="Buscar acuerdo o responsable..."
            style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">

        <select wire:model.live="area" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
            <option value="">Todas las Áreas</option>
            @foreach($areas as $a)
                <option value="{{ $a }}">{{ $a }}</option>
            @endforeach
        </select>

        <select wire:model.live="estatus" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
            <option value="">Todos los Estatus</option>
            <option value="pendiente">Pendiente</option>
            <option value="en_proceso">En Proceso</option>
            <option value="detenido">Detenido</option>
            <option value="finalizado">Finalizado</option>
        </select>

        <select wire:model.live="prioridad" style="padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
            <option value="">Todas las Prioridades</option>
            <option value="alta">Alta</option>
            <option value="media">Media</option>
        </select>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Área / C</th>
                    <th>Acuerdo</th>
                    <th>Responsable</th>
                    <th>F. Compromiso</th>
                    <th>Estatus</th>
                    <th>Avance</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($acuerdos as $acuerdo)
                    <tr
                        style="border-left: 4px solid {{ $acuerdo->tipo_cuadrante == 1 ? 'var(--danger)' : ($acuerdo->prioridad == 'alta' ? 'var(--warning)' : 'var(--info)') }};">
                        <td>#{{ $acuerdo->id }}</td>
                        <td>
                            <span class="badge {{ $acuerdo->tipo_cuadrante == 1 ? 'badge-red' : 'badge-blue' }}">
                                {{ $acuerdo->area }} / C{{ $acuerdo->tipo_cuadrante }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 600;">{{ $acuerdo->acuerdo }}</div>
                            <div style="font-size: 0.8rem; color: var(--secondary);">
                                {{ Str::limit($acuerdo->descripcion, 50) }}</div>
                        </td>
                        <td>{{ $acuerdo->responsable }}</td>
                        <td>
                            <span
                                style="color: {{ $acuerdo->fecha_compromiso->isPast() && $acuerdo->estatus != 'finalizado' ? 'var(--danger)' : 'inherit' }}">
                                {{ $acuerdo->fecha_compromiso->format('d/m/Y') }}
                            </span>
                        </td>
                        <td>
                            @php
                                $status_class = match ($acuerdo->estatus) {
                                    'pendiente' => 'badge-yellow',
                                    'en_proceso' => 'badge-blue',
                                    'detenido' => 'badge-red',
                                    'finalizado' => 'badge-green',
                                };
                            @endphp
                            <span class="badge {{ $status_class }}">
                                {{ str_replace('_', ' ', $acuerdo->estatus) }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div class="progress-bar" title="{{ $acuerdo->porcentaje_avance }}%">
                                    <div class="progress-fill" style="width: {{ $acuerdo->porcentaje_avance }}%;"></div>
                                </div>
                                <span style="font-size: 0.8rem;">{{ $acuerdo->porcentaje_avance }}%</span>
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('acuerdos.show', $acuerdo) }}" style="color: var(--info);"><i
                                        class="fas fa-eye"></i></a>
                                <a href="{{ route('acuerdos.edit', $acuerdo) }}" style="color: var(--secondary);"><i
                                        class="fas fa-edit"></i></a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem; color: var(--secondary);">No se
                            encontraron acuerdos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $acuerdos->links() }}
    </div>
</div>