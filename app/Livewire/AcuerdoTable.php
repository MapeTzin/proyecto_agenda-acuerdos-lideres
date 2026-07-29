<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Acuerdo;

class AcuerdoTable extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $cuadrante = '';
    public $selectedArea = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'selectedArea' => ['except' => '', 'as' => 'area']
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingCuadrante()
    {
        $this->resetPage();
    }

    public function updatingSelectedArea()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $query = Acuerdo::query()
            ->with('latestComment')
            ->where('estatus', '!=', 'finalizado')
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('actividad', 'like', '%' . $this->search . '%')
                        ->orWhere('acuerdo', 'like', '%' . $this->search . '%')
                        ->orWhere('responsable', 'like', '%' . $this->search . '%')
                        ->orWhere('area', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function ($q) {
                $q->where('estatus', $this->status);
            })
            ->when($this->cuadrante, function ($q) {
                $q->where('tipo_cuadrante', $this->cuadrante);
            })
            ->when($this->selectedArea, function ($q) {
                $q->where('area', $this->selectedArea);
            })
            ->when(!auth()->user()->hasRole('Administrador') && auth()->user()->email !== 'v.arochi@mapetzin.com' && auth()->user()->email !== 'gerencia_serv_gobierno@lesli.com.mx', function ($q) {
                $q->where(function($query) {
                    $query->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(area)'), array_map('strtolower', auth()->user()->areas))
                          ->orWhereRaw('LOWER(responsable) = ?', [strtolower(auth()->user()->name)]);
                });
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $customOrder = [
            'ALMACEN',
            'VENTAS',
            'COMERCIAL',
            'ASEGURAMIENTO DE CALIDAD',
            'CAPITAL HUMANO',
            'JEFATURA DE STAFF',
            'ASIS ADM',
            'ADQUISICIONES',
            'SISTEMAS Y TI',
            'CONTABILIDAD Y CXC',
            'CULTURA ORGANIZACIONAL'
        ];

        $areas = \App\Models\Area::pluck('name')->sortBy(function ($area) use ($customOrder) {
            $pos = array_search($area, $customOrder);
            return $pos === false ? 999 : $pos;
        })->values();


        // Check for missing daily updates (Only after 4 PM)
        $now = now();
        $sin_actualizar = collect();

        if (!$now->isWeekend() && $now->hour >= 16) {
            $sin_actualizar = Acuerdo::where('estatus', '!=', 'finalizado')
                ->whereDoesntHave('avancesDiarios', function ($q) {
                    $q->whereDate('fecha', now()->format('Y-m-d'));
                })
                ->when(!auth()->user()->hasRole('Administrador') && auth()->user()->email !== 'v.arochi@mapetzin.com' && auth()->user()->email !== 'gerencia_serv_gobierno@lesli.com.mx', function ($q) {
                    $q->where(function($query) {
                        $query->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(area)'), array_map('strtolower', auth()->user()->areas))
                              ->orWhereRaw('LOWER(responsable) = ?', [strtolower(auth()->user()->name)]);
                    });
                })
                ->select('area', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                ->groupBy('area')
                ->get();
        }

        return view('livewire.acuerdo-table', [
            'acuerdos' => $query->paginate(10),
            'areas' => $areas,
            'sin_actualizar' => $sin_actualizar
        ]);
    }
}
