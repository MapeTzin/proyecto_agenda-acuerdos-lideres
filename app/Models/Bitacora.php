<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    use HasFactory;

    protected $fillable = ['acuerdo_id', 'user_id', 'campo', 'valor_anterior', 'valor_nuevo', 'usuario'];

    public function acuerdo()
    {
        return $this->belongsTo(Acuerdo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
