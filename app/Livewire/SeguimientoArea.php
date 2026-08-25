<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Acuerdo;
use Illuminate\Support\Facades\DB;

class SeguimientoArea extends Component
{
    public $selectedArea = '';

    public function mount()
    {
        if (!auth()->user()->hasRole('Administrador') && auth()->user()->email !== 'v.arochi@mapetzin.com' && auth()->user()->email !== 'gerencia_serv_gobierno@lesli.com.mx') {
            return redirect()->route('acuerdos.index');
        }
    }

    public function render()
    {
        $query = Acuerdo::query();

        if ($this->selectedArea) {
            $query->where('area', $this->selectedArea);
        }

        $total_acuerdos = (clone $query)->count();
        $pendientes = (clone $query)->whereIn('estatus', ['pendiente', 'en_proceso', 'detenido'])->count();
        $vencidos = (clone $query)->where('estatus', '!=', 'finalizado')
            ->where('fecha_compromiso', '<', now())
            ->count();

        $cumplidos = (clone $query)->where('estatus', 'finalizado')->count();
        $cumplimiento = $total_acuerdos > 0 ? ($cumplidos / $total_acuerdos) * 100 : 0;

        $c1_count = (clone $query)->where('tipo_cuadrante', 1)->count();
        $c2_count = (clone $query)->where('tipo_cuadrante', 2)->count();

        $por_responsable = (clone $query)->select('responsable', DB::raw('count(*) as total'))
            ->groupBy('responsable')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $proximos = (clone $query)->where('estatus', '!=', 'finalizado')
            ->orderBy('fecha_compromiso', 'asc')
            ->take(5)
            ->get();

        // Check for missing daily updates (Only after 4 PM)
        $now = now();
        $sin_actualizar = collect();

        if (!$now->isWeekend() && $now->hour >= 16) {
            $areasConAvanceHoy = Acuerdo::where('estatus', '!=', 'finalizado')
                ->whereHas('avancesDiarios', function ($q) {
                    $q->whereDate('fecha', now()->format('Y-m-d'));
                })
                ->pluck('area')
                ->toArray();

            $sin_actualizar = Acuerdo::where('estatus', '!=', 'finalizado')
                ->whereNotIn('area', $areasConAvanceHoy)
                ->when($this->selectedArea, function ($q) {
                    $q->where('area', $this->selectedArea);
                })
                ->select('area', DB::raw('count(*) as pendientes'))
                ->groupBy('area')
                ->get();
        }

        $areas = Acuerdo::select('area')->distinct()->pluck('area');

        return view('livewire.seguimiento-area', [
            'total_acuerdos' => $total_acuerdos,
            'pendientes' => $pendientes,
            'vencidos' => $vencidos,
            'cumplimiento' => $cumplimiento,
            'c1_count' => $c1_count,
            'c2_count' => $c2_count,
            'por_responsable' => $por_responsable,
            'proximos' => $proximos,
            'sin_actualizar' => $sin_actualizar,
            'areas' => $areas
        ])->extends('layouts.app')->section('content');
    }
}
