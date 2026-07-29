<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Acuerdo;

class DashboardController extends Controller
{
    public function index()
    {
        $total_acuerdos = Acuerdo::count();
        $pendientes = Acuerdo::whereIn('estatus', ['pendiente', 'en_proceso', 'detenido'])->count();
        $vencidos = Acuerdo::where('estatus', '!=', 'finalizado')
            ->where('fecha_compromiso', '<', now())
            ->count();

        $cumplidos = Acuerdo::where('estatus', 'finalizado')->count();
        $comprometidos = Acuerdo::count();
        $cumplimiento = $comprometidos > 0 ? ($cumplidos / $comprometidos) * 100 : 0;

        $c1_count = Acuerdo::where('tipo_cuadrante', 1)->count();
        $c2_count = Acuerdo::where('tipo_cuadrante', 2)->count();

        $por_responsable = Acuerdo::select('responsable', \DB::raw('count(*) as total'))
            ->groupBy('responsable')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $proximos = Acuerdo::where('estatus', '!=', 'finalizado')
            ->orderBy('fecha_compromiso', 'asc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'total_acuerdos',
            'pendientes',
            'vencidos',
            'cumplimiento',
            'c1_count',
            'c2_count',
            'por_responsable',
            'proximos'
        ));
    }
}
