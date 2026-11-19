<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $fillable = ['acuerdo_id', 'user_id', 'comentario'];

    protected $with = ['user'];

    protected $appends = ['usuario'];

    public function acuerdo()
    {
        return $this->belongsTo(Acuerdo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUsuarioAttribute(): string
    {
        return $this->user?->name ?? 'Administrador';
    }
}