<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'color'];

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_areas', 'area_id', 'user_id');
    }
}
