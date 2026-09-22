<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Acuerdo;

class AcuerdoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless(auth()->user()->can('acuerdos.view'), 403, 'No tiene permiso para ver el listado de acuerdos.');
        return view('acuerdos.index');
    }

    public function historico()
    {
        abort_unless(auth()->user()->can('acuerdos.historico.view'), 403, 'No tiene permiso para ver el histórico de acuerdos.');
        return view('acuerdos.historico');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Acuerdo::class);
        $kpisByArea = $this->getKpisGroupedByArea();
        return view('acuerdos.create', compact('kpisByArea'));
    }

    private function getKpisGroupedByArea()
    {
        try {
            $db = \Illuminate\Support\Facades\DB::connection('sistema_tickets');
            $kpis = $db->table('kpis')->get(['id', 'name', 'category']);
        } catch (\Exception $e) {
            $kpis = collect();
        }

        $areas = \App\Models\Area::pluck('name')->toArray();
        $result = [];

        foreach ($areas as $area) {
            $areaUpper = strtoupper($area);
            $matched = $kpis->filter(function($kpi) use ($areaUpper) {
                $cat = strtoupper($kpi->category ?? '');
                if ($cat === $areaUpper) return true;
                if (str_contains($areaUpper, 'CONTABILIDAD') || str_contains($areaUpper, 'CXC') || str_contains($areaUpper, 'COBRAR')) {
                    if ($cat === 'CONTABILIDAD' || $cat === 'CUENTAS POR COBRAR') return true;
                }
                if (str_contains($areaUpper, 'SISTEMAS') || str_contains($areaUpper, 'TI')) {
                    if ($cat === 'SISTEMAS Y TI' || $cat === 'SISTEMAS' || $cat === 'TI') return true;
                }
                if (str_contains($areaUpper, 'CAPITAL') || str_contains($areaUpper, 'HUMANO') || str_contains($areaUpper, 'RECURSOS')) {
                    if ($cat === 'CAPITAL HUMANO') return true;
                }
                if (str_contains($areaUpper, 'STAFF')) {
                    if ($cat === 'JEFATURA DE STAFF') return true;
                }
                if (str_contains($areaUpper, 'CALIDAD')) {
                    if ($cat === 'ASEGURAMIENTO DE CALIDAD') return true;
                }
                if (str_contains($areaUpper, 'ADQUISICION')) {
                    if ($cat === 'ADQUISICIONES') return true;
                }
                if (str_contains($areaUpper, 'VENTAS')) {
                    if ($cat === 'VENTAS') return true;
                }
                if (str_contains($areaUpper, 'COMERCIAL')) {
                    if ($cat === 'COMERCIAL') return true;
                }
                if (str_contains($areaUpper, 'CULTURA')) {
                    if ($cat === 'CULTURA ORGANIZACIONAL') return true;
                }
                if (str_contains($areaUpper, 'ALMACEN') || str_contains($areaUpper, 'EMBARQUE')) {
                    if ($cat === 'ALMACEN') return true;
                }
                if (str_contains($areaUpper, 'ASIS') || str_contains($areaUpper, 'ADM')) {
                    if ($cat === 'ASIS ADM') return true;
                }
                return false;
            })->pluck('name')->unique()->values()->toArray();

            $result[$area] = $matched;
        }

        return $result;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\Acuerdo::class);

        $validated = $request->validate([
            'area' => 'required|string|max:255',
            'tipo_cuadrante' => 'required|in:1,2',
            'actividad' => 'required|string|max:255',
            'acuerdo' => 'required|string',
            'descripcion' => 'nullable|string',
            'responsable' => 'required|string|max:255',
            'compromiso_lunes' => 'nullable|string',
            'cierre_viernes' => 'nullable|string',
            'apoyo' => 'nullable|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_compromiso' => 'required|date',
            'prioridad' => 'required|in:alta,media',
            'porcentaje_avance' => 'nullable|numeric|min:0|max:100',
        ]);

        $acuerdo = \App\Models\Acuerdo::create($validated);
        if (isset($validated['porcentaje_avance'])) {
            $acuerdo->porcentaje_avance = (float)$validated['porcentaje_avance'];
            $acuerdo->saveQuietly();
        } else {
            $acuerdo->calculateAutomaticProgress();
        }

        // Record initial progress
        \App\Models\AvanceDiario::updateOrCreate(
            ['acuerdo_id' => $acuerdo->id, 'fecha' => today()],
            ['porcentaje_avance' => $acuerdo->porcentaje_avance ?? 0]
        );

        return redirect()->route('acuerdos.index')->with('success', 'Acuerdo creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Acuerdo $acuerdo)
    {
        $this->authorize('view', $acuerdo);
        return view('acuerdos.show', compact('acuerdo'));
    }

    public function edit(Acuerdo $acuerdo)
    {
        $this->authorize('update', $acuerdo);
        $kpisByArea = $this->getKpisGroupedByArea();
        return view('acuerdos.edit', compact('acuerdo', 'kpisByArea'));
    }

    public function update(Request $request, Acuerdo $acuerdo)
    {
        $this->authorize('update', $acuerdo);

        $validated = $request->validate([
            'area' => 'required|string|max:255',
            'tipo_cuadrante' => 'required|in:1,2',
            'actividad' => 'required|string|max:255',
            'acuerdo' => 'required|string',
            'descripcion' => 'nullable|string',
            'responsable' => 'required|string|max:255',
            'compromiso_lunes' => 'nullable|string',
            'cierre_viernes' => 'nullable|string',
            'apoyo' => 'nullable|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_compromiso' => 'required|date',
            'fecha_cierre' => 'nullable|date',
            'estatus' => 'required|in:pendiente,en_proceso,detenido,finalizado',
            'prioridad' => 'required|in:alta,media',
            'porcentaje_avance' => 'nullable|numeric|min:0|max:100',
        ]);

        // Validar si se intenta cambiar la fecha compromiso
        $fechaCompromisoOriginal = $acuerdo->fecha_compromiso ? $acuerdo->fecha_compromiso->format('Y-m-d') : null;
        $fechaCompromisoNueva = $validated['fecha_compromiso'];

        if ($fechaCompromisoOriginal && $fechaCompromisoOriginal !== $fechaCompromisoNueva) {
            if (!$acuerdo->canChangeFechaCompromiso()) {
                return back()->withErrors([
                    'fecha_compromiso' => 'Solo los Administradores autorizados pueden modificar la fecha compromiso.'
                ])->withInput();
            }

            $motivo = $request->input('motivo_cambio_fecha');
            if (empty($motivo)) {
                return back()->withErrors([
                    'motivo_cambio_fecha' => 'Debe indicar el motivo por el cual se cambia la fecha compromiso.'
                ])->withInput();
            }

            // Registrar el motivo como un comentario oficial
            $acuerdo->comentarios_extra()->create([
                'comentario' => "🔄 CAMBIO DE FECHA COMPROMISO: de " . \Carbon\Carbon::parse($fechaCompromisoOriginal)->format('d/m/Y') . " a " . \Carbon\Carbon::parse($fechaCompromisoNueva)->format('d/m/Y') . ".\nMOTIVO: " . $motivo,
                'user_id' => auth()->id(),
                'usuario' => auth()->user()->name
            ]);
        }

        if ($validated['estatus'] === 'finalizado' && empty($validated['fecha_cierre'])) {
            $validated['fecha_cierre'] = now()->format('Y-m-d');
        }

        // History tracking
        foreach ($validated as $key => $value) {
            $oldValue = $acuerdo->$key;
            $newValue = $value;

            // Normalize dates for comparison
            if (in_array($key, ['fecha_inicio', 'fecha_compromiso', 'fecha_cierre'])) {
                $oldValue = $oldValue ? $oldValue->format('Y-m-d') : null;
            }

            if ($oldValue != $newValue) {
                \App\Models\Bitacora::create([
                    'acuerdo_id' => $acuerdo->id,
                    'user_id' => auth()->id(),
                    'campo' => $key,
                    'valor_anterior' => (string) $oldValue,
                    'valor_nuevo' => (string) $newValue,
                    'usuario' => auth()->user()->name,
                ]);
            }
        }

        $hasManualPct = array_key_exists('porcentaje_avance', $validated) && $validated['porcentaje_avance'] !== null && $validated['porcentaje_avance'] !== '';

        $acuerdo->update($validated);
        
        if ($hasManualPct) {
            $acuerdo->porcentaje_avance = (float)$validated['porcentaje_avance'];
            $acuerdo->saveQuietly();
        } else {
            $acuerdo->calculateAutomaticProgress();
        }

        // Record daily progress
        \App\Models\AvanceDiario::updateOrCreate(
            ['acuerdo_id' => $acuerdo->id, 'fecha' => today()],
            ['porcentaje_avance' => $acuerdo->porcentaje_avance]
        );

        return redirect()->route('acuerdos.index')->with('success', 'Acuerdo actualizado.');
    }

    public function destroy(Acuerdo $acuerdo)
    {
        $this->authorize('delete', $acuerdo);
        $acuerdo->delete();
        return redirect()->route('acuerdos.index')->with('success', 'Acuerdo eliminado.');
    }

    public function comment(Request $request, Acuerdo $acuerdo)
    {
        $this->authorize('update', $acuerdo);
        $request->validate(['comentario' => 'required|string']);

        $acuerdo->comentarios_extra()->create([
            'comentario' => $request->comentario,
            'user_id' => auth()->id(),
            'usuario' => auth()->user()->name
        ]);

        $acuerdo->calculateAutomaticProgress(); // Detona el cálculo automático al comentar

        // Record daily progress
        \App\Models\AvanceDiario::updateOrCreate(
            ['acuerdo_id' => $acuerdo->id, 'fecha' => today()],
            ['porcentaje_avance' => $acuerdo->porcentaje_avance]
        );

        return back()->with('success', 'Comentario agregado.');

    }

    public function export()
    {
        abort_unless(auth()->user()->can('acuerdos.export'), 403, 'No tiene permiso para exportar acuerdos.');
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\AcuerdosExport, 'acuerdos.xlsx');
    }
}
