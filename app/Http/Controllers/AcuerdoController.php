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
        return view('acuerdos.index');
    }

    public function historico()
    {
        return view('acuerdos.historico');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('acuerdos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
        return view('acuerdos.edit', compact('acuerdo'));
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
                    'fecha_compromiso' => 'Solo los Super Administradores autorizados (soporte / v.arochi) pueden modificar la fecha compromiso.'
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
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\AcuerdosExport, 'acuerdos.xlsx');
    }
}
