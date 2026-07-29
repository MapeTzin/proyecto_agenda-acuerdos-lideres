<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvanceDiario extends Model
{
    use HasFactory;

    protected $table = 'avances_diarios';

    protected $fillable = [
        'acuerdo_id',
        'fecha',
        'porcentaje_avance'
    ];

    protected $casts = [
        'fecha' => 'date'
    ];

    public function acuerdo()
    {
        return $this->belongsTo(Acuerdo::class);
    }
}
