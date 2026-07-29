<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    use HasFactory;

    protected $table = 'kpis';

    protected $fillable = [
        'area',
        'nombre',
        'descripcion',
        'meta_green',
        'meta_yellow',
        'orden'
    ];

    public function valores()
    {
        return $this->hasMany(KpiValor::class, 'kpi_id');
    }
}
