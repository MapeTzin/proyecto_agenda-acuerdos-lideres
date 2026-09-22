@extends('layouts.app')

@section('title', 'Detalle de Acuerdo - Agenda de Acuerdos')
@section('header_title', 'Detalle del Acuerdo #' . $acuerdo->id)

@section('content')
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                <div>
                    <span
                        style="display: block; color: var(--secondary); font-size: 0.9rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">{{ $acuerdo->actividad }}</span>
                    <h2 style="font-size: 1.5rem; margin: 0;">{{ $acuerdo->acuerdo }}</h2>
                </div>
                <span class="badge {{ $acuerdo->tipo_cuadrante == 1 ? 'badge-red' : 'badge-blue' }}"
                    style="padding: 0.5rem 1rem;">
                    {{ $acuerdo->area }} - Cuadrante {{ $acuerdo->tipo_cuadrante }}
                </span>
            </div>

            <div style="margin-bottom: 2rem;">
                <h4 style="color: var(--secondary); margin-bottom: 0.5rem;">Descripción</h4>
                <p style="font-size: 1.1rem; line-height: 1.6;">{{ $acuerdo->descripcion ?: 'Sin descripción adicional.' }}
                </p>
            </div>

            <div
                style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem; background: #f8fafc; padding: 1.5rem; border-radius: 0.5rem;">
                <div>
                    <span style="display: block; font-size: 0.8rem; color: var(--secondary);">Responsable</span>
                    <span style="font-weight: 600; font-size: 1.1rem;">{{ $acuerdo->responsable }}</span>
                </div>
                <div>
                    <span style="display: block; font-size: 0.8rem; color: var(--secondary);">Apoyo</span>
                    <span style="font-weight: 600;">{{ $acuerdo->apoyo ?: 'N/A' }}</span>
                </div>
                <div>
                    <span style="display: block; font-size: 0.8rem; color: var(--secondary);">Fecha Compromiso</span>
                    <span style="font-weight: 600;">{{ $acuerdo->fecha_compromiso->format('d/m/Y') }}</span>
                </div>
                <div>
                    <span style="display: block; font-size: 0.8rem; color: var(--secondary);">Fecha Cierre</span>
                    <span
                        style="font-weight: 600;">{{ $acuerdo->fecha_cierre ? $acuerdo->fecha_cierre->format('d/m/Y') : '-' }}</span>
                </div>
            </div>

            @php
                $sem = $acuerdo->semaforo;
            @endphp
            <div style="margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <h4 style="color: var(--secondary); margin: 0;">Avance Operativo Actual</h4>
                    @if(!$acuerdo->isAtrasoCritico())
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="background: {{ $sem['bg'] }}; color: {{ $sem['text_color'] }}; padding: 0.3rem 0.75rem; border-radius: 9999px; font-weight: 800; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $sem['color'] }};"></span>
                                {{ $sem['label'] }}
                            </span>
                            <span style="font-weight: 800; font-size: 1.1rem; color: {{ $sem['color'] }};">{{ $acuerdo->porcentaje_avance }}%</span>
                        </div>
                    @endif
                </div>
                @if($acuerdo->isAtrasoCritico())
                    <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.75rem;">
                        <span style="background: linear-gradient(135deg, #dc2626, #991b1b); color: white; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 0.5rem; animation: pulse-critical 1.5s ease-in-out infinite;">
                            <i class="fas fa-exclamation-triangle"></i>
                            ATRASO CRÍTICO
                        </span>
                        <span style="font-size: 0.85rem; color: #991b1b; font-weight: 600;">
                            La fecha compromiso ({{ $acuerdo->fecha_compromiso->format('d/m/Y') }}) ha sido superada sin completar la actividad al 100%.
                        </span>
                    </div>
                @else
                    <div class="progress-bar" style="width: 100%; height: 20px; border-radius: 10px; background: #edf2f7; overflow: hidden;">
                        <div class="progress-fill" style="width: {{ $acuerdo->porcentaje_avance }}%; height: 100%; background: {{ $sem['color'] }}; transition: width 0.3s ease;"></div>
                    </div>
                @endif
            </div>

            <div style="margin-bottom: 2rem;">
                <h4 style="color: var(--secondary); margin-bottom: 1rem;">Historial de Avance Diario</h4>
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 0.5rem; overflow: hidden;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                        <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <tr>
                                <th style="padding: 0.75rem; text-align: left;">Fecha</th>
                                <th style="padding: 0.75rem; text-align: left;">% Avance</th>
                                <th style="padding: 0.75rem; text-align: left;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($acuerdo->avancesDiarios()->orderBy('fecha', 'desc')->get() as $avance)
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 0.75rem;">{{ $avance->fecha->format('d/m/Y') }}</td>
                                    <td style="padding: 0.75rem; font-weight: 600;">{{ $avance->porcentaje_avance }}%</td>
                                    <td style="padding: 0.75rem;">
                                        <div class="progress-bar" style="width: 100px; height: 8px;">
                                            <div class="progress-fill" style="width: {{ $avance->porcentaje_avance }}%;"></div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="padding: 1rem; text-align: center; color: var(--secondary);">No hay
                                        registros de avance aún.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="display: flex; gap: 1rem;">
                @can('update', $acuerdo)
                <button class="btn btn-primary" onclick="window.location.href='{{ route('acuerdos.edit', $acuerdo) }}'">
                    <i class="fas fa-edit"></i> Editar Acuerdo
                </button>
                @endcan
                <button class="btn" style="background: var(--light); border: 1px solid #e2e8f0;"
                    onclick="window.location.href='{{ route('acuerdos.index') }}'">
                    Volver al Listado
                </button>
            </div>
        </div>

        <div>
            <div class="card" style="padding: 0; overflow: hidden; border: 1px solid #e2e8f0; margin-bottom: 1.5rem;">
                {{-- Header con gradiente --}}
                <div style="background: linear-gradient(135deg, #4f46e5, #6366f1); padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 0.8rem;">
                    <div style="width: 32px; height: 32px; background: rgba(255,255,255,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.9rem;">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <h3 style="color: white; font-size: 1.1rem; font-weight: 700; margin: 0;">Registro de Avances</h3>
                </div>

                <div style="padding: 1.5rem;">
                    @can('update', $acuerdo)
                    {{-- Formulario para nuevo avance --}}
                    <form action="{{ route('acuerdos.comment', $acuerdo) }}" method="POST">
                        @csrf
                        <textarea name="comentario" required rows="3"
                            placeholder="Describe el avance o comentario aquí..."
                            style="width: 100%; padding: 0.75rem; border: 2px solid #f1f5f9; border-radius: 0.5rem; font-family: inherit; font-size: 0.9rem; resize: vertical; outline: none; transition: border-color 0.2s;"
                            onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#f1f5f9'"></textarea>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.75rem; justify-content: center; border-radius: 0.5rem;">
                            <i class="fas fa-plus"></i> Registrar Avance
                        </button>
                    </form>
                    @endcan

                    {{-- Historial de avances --}}
                    <div style="margin-top: 1.5rem; max-height: 450px; overflow-y: auto; padding-right: 0.25rem;">
                        @php
                            $comentarios = $acuerdo->comentarios_extra()->latest()->get();
                        @endphp
                        
                        @forelse($comentarios as $comment)
                            <div style="display: flex; gap: 0.75rem; margin-bottom: 1.25rem; position: relative;">
                                @if(!$loop->last)
                                    <div style="position: absolute; left: 16px; top: 32px; bottom: -20px; width: 2px; background: #f1f5f9;"></div>
                                @endif
                                
                                <div style="width: 32px; height: 32px; background: {{ $loop->first ? 'var(--primary)' : '#e2e8f0' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: {{ $loop->first ? 'white' : '#94a3b8' }}; font-weight: 700; font-size: 0.7rem; flex-shrink: 0; z-index: 1;">
                                    {{ strtoupper(substr($comment->usuario, 0, 1)) }}
                                </div>
                                
                                <div style="flex: 1;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                                        <span style="font-weight: 700; font-size: 0.85rem; color: #1e293b;">{{ $comment->usuario }}</span>
                                        <span style="font-size: 0.65rem; color: #94a3b8;">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div style="background: #f8fafc; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #f1f5f9; font-size: 0.85rem; color: #475569; line-height: 1.4;">
                                        {{ $comment->comentario }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; padding: 2rem 0; color: #cbd5e1;">
                                <i class="fas fa-comments" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                                <p style="font-size: 0.85rem; margin: 0;">Sin avances registrados aún</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card" style="margin-top: 1.5rem;">
                <h3>Historial de Cambios</h3>
                <div style="margin-top: 1rem; font-size: 0.85rem;">
                    @foreach($acuerdo->bitacoras()->latest()->get() as $log)
                        <div style="margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 1px dashed #e2e8f0;">
                            <span style="color: var(--secondary);">{{ $log->created_at->format('d/m/Y H:i') }}</span><br>
                            <strong>{{ ucfirst($log->campo) }}</strong> cambió de <span
                                style="color: var(--danger);">"{{ $log->valor_anterior }}"</span> a <span
                                style="color: var(--success);">"{{ $log->valor_nuevo }}"</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection