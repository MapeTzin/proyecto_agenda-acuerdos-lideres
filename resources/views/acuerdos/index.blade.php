@extends('layouts.app')

@section('title', 'Seguimiento de Acuerdos')
@section('header_title', 'Registro Operativo de Acuerdos')

@section('content')
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3>Listado de Acuerdos</h3>
            <div style="display: flex; gap: 0.5rem;">
                <button class="btn btn-primary" onclick="window.location.href='{{ route('acuerdos.create') }}'">
                    <i class="fas fa-plus"></i> Nuevo Acuerdo
                </button>
                <button class="btn" style="background: #10b981; color: white;"
                    onclick="window.location.href='{{ route('acuerdos.export') }}'">
                    <i class="fas fa-file-excel"></i> Exportar
                </button>
            </div>
        </div>

        <livewire:acuerdo-table />
    </div>
@endsection