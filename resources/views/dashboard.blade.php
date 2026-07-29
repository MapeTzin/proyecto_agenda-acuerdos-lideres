@extends('layouts.app')

@section('title', 'Dashboard - Agenda de Acuerdos')
@section('header_title', 'Dashboard Operativo')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-value">{{ $total_acuerdos }}</span>
            <span class="stat-label">Total de Acuerdos</span>
        </div>
        <div class="stat-card pendientes">
            <span class="stat-value">{{ $pendientes }}</span>
            <span class="stat-label">Pendientes / En Proceso</span>
        </div>
        <div class="stat-card vencidos">
            <span class="stat-value">{{ $vencidos }}</span>
            <span class="stat-label">Vencidos</span>
        </div>
        <div class="stat-card success">
            <span class="stat-value">{{ number_format($cumplimiento, 1) }}%</span>
            <span class="stat-label">Cumplimiento (ICO)</span>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <div class="card">
            <h3>Estatus por Cuadrante</h3>
            <div style="margin-top: 1.5rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Cuadrante 1 (Urgente/Importante)</span>
                    <span>{{ $c1_count }}</span>
                </div>
                <div class="progress-bar" style="width: 100%; height: 12px; margin-bottom: 1.5rem;">
                    <div class="progress-fill"
                        style="width: {{ $total_acuerdos > 0 ? ($c1_count / $total_acuerdos) * 100 : 0 }}%; background: var(--danger);">
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Cuadrante 2 (No Urgente/Importante)</span>
                    <span>{{ $c2_count }}</span>
                </div>
                <div class="progress-bar" style="width: 100%; height: 12px;">
                    <div class="progress-fill"
                        style="width: {{ $total_acuerdos > 0 ? ($c2_count / $total_acuerdos) * 100 : 0 }}%; background: var(--info);">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>Top Responsables</h3>
            <ul style="list-style: none; padding: 0; margin-top: 1rem;">
                @foreach($por_responsable as $resp)
                    <li
                        style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9;">
                        <span>{{ $resp->responsable }}</span>
                        <span class="badge badge-blue">{{ $resp->total }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="card" style="margin-top: 1.5rem;">
        <h3>Próximos Vencimientos</h3>
        <div class="table-container" style="margin-top: 1rem;">
            <table>
                <thead>
                    <tr>
                        <th>Acuerdo</th>
                        <th>Responsable</th>
                        <th>Fecha Compromiso</th>
                        <th>Avance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($proximos as $acuerdo)
                        <tr>
                            <td>{{ $acuerdo->acuerdo }}</td>
                            <td>{{ $acuerdo->responsable }}</td>
                            <td>
                                <span style="color: {{ $acuerdo->fecha_compromiso->isPast() ? 'var(--danger)' : 'inherit' }}">
                                    {{ $acuerdo->fecha_compromiso->format('d/m/Y') }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div class="progress-bar" style="width: 60px;">
                                        <div class="progress-fill" style="width: {{ $acuerdo->porcentaje_avance }}%;"></div>
                                    </div>
                                    <span style="font-size: 0.8rem;">{{ $acuerdo->porcentaje_avance }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection