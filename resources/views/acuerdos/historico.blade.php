@extends('layouts.app')

@section('title', 'Histórico de Acuerdos')
@section('header_title', 'Histórico de Acuerdos Finalizados')

@section('content')
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h3>Acuerdos Cerrados</h3>
            <div style="display: flex; gap: 0.5rem;">
                <button class="btn" style="background: #10b981; color: white;"
                    onclick="window.location.href='{{ route('acuerdos.export') }}'">
                    <i class="fas fa-file-excel"></i> Exportar Historial
                </button>
            </div>
        </div>

        <livewire:acuerdo-historico-table />
    </div>
@endsection