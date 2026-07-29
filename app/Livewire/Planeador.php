<?php

namespace App\Livewire;

use Livewire\Component;

class Planeador extends Component
{
    public function render()
    {
        return view('livewire.planeador')->extends('layouts.app')->section('content');
    }
}
