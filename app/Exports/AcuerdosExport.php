<?php

namespace App\Exports;

use App\Models\Acuerdo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AcuerdosExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $query = Acuerdo::query();

        if (!auth()->user()->hasRole(['Administrador', 'Director General'])) {
            $query->where(function($q) {
                $q->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(area)'), array_map('strtolower', auth()->user()->areas))
                  ->orWhereRaw('LOWER(responsable) = ?', [strtolower(auth()->user()->name)]);
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Área',
            'Cuadrante',
            'Actividad',
            'Acuerdo',
            'Descripción',
            'Responsable',
            'Apoyo',
            'F. Compromiso',
            'F. Cierre',
            'Estatus',
            'Prioridad',
            '% Avance',
        ];
    }

    public function map($acuerdo): array
    {
        return [
            $acuerdo->id,
            $acuerdo->area,
            'C' . $acuerdo->tipo_cuadrante,
            $acuerdo->actividad,
            $acuerdo->acuerdo,
            $acuerdo->descripcion,
            $acuerdo->responsable,
            $acuerdo->apoyo,
            $acuerdo->fecha_compromiso->format('d/m/Y'),
            $acuerdo->fecha_cierre ? $acuerdo->fecha_cierre->format('d/m/Y') : '',
            $acuerdo->estatus,
            $acuerdo->prioridad,
            $acuerdo->porcentaje_avance . '%',
        ];
    }
}
