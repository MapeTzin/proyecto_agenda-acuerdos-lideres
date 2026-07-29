<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Acuerdo;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public function mount()
    {
        if (!auth()->user()->hasRole('Administrador') && auth()->user()->email !== 'v.arochi@mapetzin.com' && auth()->user()->email !== 'gerencia_serv_gobierno@lesli.com.mx') {
            return redirect()->route('acuerdos.index');
        }
    }

    public function render()
    {
        $total_acuerdos = Acuerdo::count();
        $pendientes = Acuerdo::whereIn('estatus', ['pendiente', 'en_proceso', 'detenido'])->count();
        $vencidos = Acuerdo::where('estatus', '!=', 'finalizado')
            ->where('fecha_compromiso', '<', now())
            ->count();

        $cumplidos = Acuerdo::where('estatus', 'finalizado')->count();
        $cumplimiento = $total_acuerdos > 0 ? ($cumplidos / $total_acuerdos) * 100 : 0;

        $c1_count = Acuerdo::where('tipo_cuadrante', 1)->count();
        $c2_count = Acuerdo::where('tipo_cuadrante', 2)->count();

        $por_responsable = Acuerdo::select('responsable', DB::raw('count(*) as total'))
            ->groupBy('responsable')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $proximos = Acuerdo::where('estatus', '!=', 'finalizado')
            ->orderBy('fecha_compromiso', 'asc')
            ->take(5)
            ->get();

        // Check for missing daily updates (Yesterday/Last Working Day) - ALWAYS VISIBLE ONLY ON DASHBOARD
        $lastWorkingDay = now()->subDay();
        while ($lastWorkingDay->isWeekend()) {
            $lastWorkingDay->subDay();
        }

        $sin_actualizar = Acuerdo::where('estatus', '!=', 'finalizado')
            ->whereDoesntHave('avancesDiarios', function ($q) use ($lastWorkingDay) {
                $q->whereDate('fecha', $lastWorkingDay->format('Y-m-d'));
            })
            ->select('area', DB::raw('count(*) as pendientes'))
            ->groupBy('area')
            ->get();

        // Calculate compliance by area (Based on last working day for constant visibility)
        $areas_list = collect([
            'ALMACEN',
            'VENTAS',
            'COMERCIAL',
            'CAPITAL HUMANO',
            'ASEGURAMIENTO DE CALIDAD',
            'JEFATURA DE STAFF',
            'ASIS ADM',
            'ADQUISICIONES',
            'SISTEMAS Y TI',
            'CONTABILIDAD Y CXC',
            'CULTURA ORGANIZACIONAL'
        ]);
        $cumplimiento_areas = [];

        foreach ($areas_list as $area) {
            $total_activos = Acuerdo::where('area', $area)
                ->where('estatus', '!=', 'finalizado')
                ->count();

            if ($total_activos > 0) {
                $actualizados = Acuerdo::where('area', $area)
                    ->where('estatus', '!=', 'finalizado')
                    ->whereHas('avancesDiarios', function ($q) use ($lastWorkingDay) {
                        $q->whereDate('fecha', $lastWorkingDay->format('Y-m-d'));
                    })
                    ->count();

                $porcentaje = ($actualizados / $total_activos) * 100;
                $cumplimiento_areas[] = [
                    'nombre' => $area,
                    'porcentaje' => $porcentaje,
                    'total' => $total_activos,
                    'actualizados' => $actualizados
                ];
            }
        }

        // Sort by percentage descending
        usort($cumplimiento_areas, function ($a, $b) {
            return $b['porcentaje'] <=> $a['porcentaje'];
        });

        // Calculate on-time completion by responsible
        $responsables_stats = Acuerdo::select('responsable')
            ->distinct()
            ->pluck('responsable');

        $cumplimiento_fechas = [];
        foreach ($responsables_stats as $resp) {
            $finalizados = Acuerdo::where('responsable', $resp)
                ->where('estatus', 'finalizado')
                ->count();

            if ($finalizados > 0) {
                $a_tiempo = Acuerdo::where('responsable', $resp)
                    ->where('estatus', 'finalizado')
                    ->whereColumn('fecha_cierre', '<=', 'fecha_compromiso')
                    ->count();

                $porcentaje = ($a_tiempo / $finalizados) * 100;
                $cumplimiento_fechas[] = [
                    'responsable' => $resp,
                    'porcentaje' => $porcentaje,
                    'atendido' => $a_tiempo,
                    'total_finalizados' => $finalizados
                ];
            }
        }

        // Sort by percentage descending
        usort($cumplimiento_fechas, function ($a, $b) {
            return $b['porcentaje'] <=> $a['porcentaje'];
        });

        return view('livewire.dashboard', [
            'total_acuerdos' => $total_acuerdos,
            'pendientes' => $pendientes,
            'vencidos' => $vencidos,
            'cumplimiento' => $cumplimiento,
            'c1_count' => $c1_count,
            'c2_count' => $c2_count,
            'por_responsable' => $por_responsable,
            'proximos' => $proximos,
            'sin_actualizar' => $sin_actualizar,
            'cumplimiento_areas' => $cumplimiento_areas,
            'cumplimiento_fechas' => $cumplimiento_fechas,
        ])->extends('layouts.app')->section('content');
    }
}
