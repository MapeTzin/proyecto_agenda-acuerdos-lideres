@extends('layouts.app')

@section('title', 'Editar Acuerdo - Agenda de Acuerdos')
@section('header_title', 'Editar Acuerdo #' . $acuerdo->id)

@section('content')
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <div class="card">
            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="alert-validation-error" style="margin-bottom: 1.5rem;">
                    <i class="fas fa-exclamation-circle" style="font-size: 1.2rem;"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('acuerdos.update', $acuerdo) }}" method="POST">
                @csrf
                @method('PUT')

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    {{-- Row 1: Area and Responsable --}}
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">ÁREA</label>
                        @if(count(auth()->user()->areas) > 1)
                            <select name="area" id="area_field" onchange="updateKpisForArea(this.value)" style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                                @foreach(auth()->user()->areas as $area)
                                    <option value="{{ $area }}" {{ old('area', $acuerdo->area) == $area ? 'selected' : '' }}>{{ $area }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" id="area_field" value="{{ old('area', $acuerdo->area) }}" readonly
                                style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; background-color: #f8fafc;">
                            <input type="hidden" name="area" value="{{ old('area', $acuerdo->area) }}">
                        @endif
                    </div>

                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">RESPONSABLE</label>
                        <input type="text" value="{{ old('responsable', $acuerdo->responsable) }}" readonly
                            style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; background-color: #f8fafc;">
                        <input type="hidden" name="responsable" value="{{ old('responsable', $acuerdo->responsable) }}">
                    </div>


                    {{-- Row 2: Cuadrante and Prioridad --}}
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Cuadrante</label>
                        <select name="tipo_cuadrante" required
                            style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                            <option value="1" {{ old('tipo_cuadrante', $acuerdo->tipo_cuadrante) == 1 ? 'selected' : '' }}>Cuadrante 1 (Urgente / Impacto Alto)</option>
                            <option value="2" {{ old('tipo_cuadrante', $acuerdo->tipo_cuadrante) == 2 ? 'selected' : '' }}>Cuadrante 2 (Importante / Seguimiento)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Prioridad</label>
                        <select name="prioridad"
                            style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                            <option value="alta" {{ old('prioridad', $acuerdo->prioridad) == 'alta' ? 'selected' : '' }}>Alta</option>
                            <option value="media" {{ old('prioridad', $acuerdo->prioridad) == 'media' ? 'selected' : '' }}>Media</option>
                        </select>
                    </div>

                    {{-- Row 3: Estatus and Apoyo --}}
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Estatus</label>
                        <select name="estatus"
                            style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                            <option value="pendiente" {{ old('estatus', $acuerdo->estatus) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="en_proceso" {{ old('estatus', $acuerdo->estatus) == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="detenido" {{ old('estatus', $acuerdo->estatus) == 'detenido' ? 'selected' : '' }}>Detenido</option>
                            <option value="finalizado" {{ old('estatus', $acuerdo->estatus) == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Apoyo</label>
                        <input type="text" name="apoyo" value="{{ old('apoyo', $acuerdo->apoyo) }}"
                            style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                    </div>

                    {{-- Row 4-6: KPI, Acuerdo, Descripcion --}}
                    <div class="form-group" style="grid-column: span 2;">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">NOMBRE DE KPI Y METRICA ALCANZADA</label>
                        <div id="kpi_container">
                            <select id="kpi_select" name="actividad" required onchange="handleKpiChange(this.value)"
                                style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; background-color: #ffffff;">
                                <option value="">-- Seleccionar KPI --</option>
                            </select>
                            <input type="text" id="kpi_custom_input" placeholder="Escriba el nombre del KPI..."
                                style="display: none; width: 100%; margin-top: 0.5rem; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                        </div>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">ACUERDO (ESTADO REAL DEL INDICADOR)</label>
                        <input type="text" name="acuerdo" value="{{ old('acuerdo', $acuerdo->acuerdo) }}" required
                            style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Descripción Detallada</label>
                        <textarea name="descripcion" rows="3"
                            style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">{{ old('descripcion', $acuerdo->descripcion) }}</textarea>
                    </div>

                    {{-- Row 7: Compromiso Lunes and Cierre Viernes --}}
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">COMPROMISO DÍA LUNES</label>
                        <input type="text" name="compromiso_lunes" value="{{ old('compromiso_lunes', $acuerdo->compromiso_lunes) }}"
                            style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                    </div>
                    
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">CIERRE DÍA VIERNES</label>
                        <input type="text" name="cierre_viernes" value="{{ old('cierre_viernes', $acuerdo->cierre_viernes) }}"
                            style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                    </div>

                    {{-- Row 8: Fechas --}}
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio"
                            value="{{ old('fecha_inicio', $acuerdo->fecha_inicio ? $acuerdo->fecha_inicio->format('Y-m-d') : $acuerdo->created_at->format('Y-m-d')) }}" required
                            style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                    </div>

                    <div class="form-group" style="position: relative;">
                        <label style="display: flex; justify-content: space-between; align-items: center; font-weight: 600; margin-bottom: 0.5rem;">
                            <span>
                                Fecha Compromiso
                                @if(!$acuerdo->canChangeFechaCompromiso())
                                    <i class="fas fa-lock" style="color: #dc2626; margin-left: 0.3rem;" title="Bloqueado - Solo Super Administrador"></i>
                                @endif
                            </span>
                            @if($acuerdo->canChangeFechaCompromiso())
                                <button type="button" id="btn_modificar_fecha" class="btn btn-sm" style="background:#f1f5f9; color:#475569; font-size:0.75rem; padding:0.3rem 0.6rem; border-radius:0.4rem; border:1px solid #cbd5e1; cursor:pointer;">
                                    <i class="fas fa-edit"></i> Modificar Fecha
                                </button>
                            @endif
                        </label>
                        @if($acuerdo->canChangeFechaCompromiso())
                            <input type="date" name="fecha_compromiso" id="fecha_compromiso"
                                value="{{ old('fecha_compromiso', $acuerdo->fecha_compromiso->format('Y-m-d')) }}" required
                                readonly
                                data-original-date="{{ $acuerdo->fecha_compromiso->format('Y-m-d') }}"
                                style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; background-color: #f1f5f9;">
                                
                            {{-- Contenedor para el motivo (oculto por defecto) --}}
                            <div id="motivo_container" style="display: none; margin-top: 1rem; background: #fffbeb; padding: 1rem; border-radius: 0.5rem; border: 1px solid #fde68a;">
                                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #92400e;">
                                    Motivo del Cambio de Fecha *
                                </label>
                                <textarea name="motivo_cambio_fecha" id="motivo_cambio_fecha" rows="2"
                                    placeholder="Indique la razón por la cual se extiende o modifica la fecha compromiso..."
                                    style="width: 100%; padding: 0.6rem; border: 1px solid #fcd34d; border-radius: 0.5rem;">{{ old('motivo_cambio_fecha') }}</textarea>
                                <p style="font-size: 0.75rem; color: #b45309; margin-top: 0.4rem;">
                                    Este motivo quedará registrado permanentemente en el historial y en los comentarios del acuerdo.
                                </p>
                            </div>
                            
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const fechaInput = document.getElementById('fecha_compromiso');
                                    const motivoContainer = document.getElementById('motivo_container');
                                    const motivoInput = document.getElementById('motivo_cambio_fecha');
                                    const btnModificar = document.getElementById('btn_modificar_fecha');
                                    
                                    if (btnModificar) {
                                        btnModificar.addEventListener('click', function() {
                                            // 1. Habilitar el campo de fecha
                                            fechaInput.removeAttribute('readonly');
                                            fechaInput.style.backgroundColor = '#ffffff';
                                            fechaInput.focus();
                                            
                                            // 2. Ocultar el botón
                                            this.style.display = 'none'; 
                                            
                                            // 3. Mostrar INMEDIATAMENTE el campo de motivo al dar clic
                                            motivoContainer.style.display = 'block';
                                            motivoInput.setAttribute('required', 'required');
                                        });
                                    }
                                    
                                    // Check on initial load (in case of validation errors)
                                    if (motivoInput.value.trim() !== '' && btnModificar) {
                                        btnModificar.click();
                                    }
                                });
                            </script>
                        @else
                            {{-- Campo bloqueado visualmente + campo hidden para enviar el valor original --}}
                            <input type="date" disabled
                                value="{{ $acuerdo->fecha_compromiso->format('Y-m-d') }}"
                                class="field-locked"
                                style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                            <input type="hidden" name="fecha_compromiso" value="{{ $acuerdo->fecha_compromiso->format('Y-m-d') }}">
                            <p style="font-size: 0.75rem; color: #dc2626; margin-top: 0.4rem; font-weight: 600; display: flex; align-items: center; gap: 0.4rem;">
                                <i class="fas fa-info-circle"></i>
                                Solo los administradores pueden modificar la fecha compromiso.
                            </p>
                        @endif
                    </div>

                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Fecha Cierre</label>
                        <input type="date" name="fecha_cierre"
                            value="{{ old('fecha_cierre', $acuerdo->fecha_cierre ? $acuerdo->fecha_cierre->format('Y-m-d') : '') }}"
                            style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <label style="font-weight: 600;">Progreso Operativo Actual (<span id="pct_label">{{ $acuerdo->porcentaje_avance }}</span>%)</label>
                            <span style="font-size: 0.8rem; color: var(--secondary);">
                                <i class="fas fa-traffic-light"></i> Semáforo de Desempeño
                            </span>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem;">
                            <input type="number" name="porcentaje_avance" id="porcentaje_avance_input" 
                                value="{{ old('porcentaje_avance', $acuerdo->porcentaje_avance) }}" 
                                min="0" max="100" step="1"
                                oninput="updateFormSemaforo(this.value)"
                                style="width: 100px; padding: 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 0.5rem; font-weight: 800; font-size: 1.05rem; text-align: center; outline: none; background: white;" />
                            <span style="font-weight: 700; color: #475569; font-size: 1rem;">%</span>
                        </div>

                        @php
                            $semaforo = $acuerdo->semaforo;
                        @endphp

                        <div style="display: flex; align-items: center; gap: 1.5rem; background: #f8fafc; padding: 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; margin-bottom: 1rem;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px; background: white; border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 6px 14px; min-width: 60px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                                <span id="semaforo_val_display" style="font-size: 1.05rem; font-weight: 800; color: #1e293b;">{{ $acuerdo->porcentaje_avance }}%</span>
                                <span id="semaforo_dot_display" style="width: 12px; height: 12px; border-radius: 50%; background: {{ $semaforo['color'] }}; display: inline-block; transition: background 0.3s;"></span>
                            </div>
                            <div>
                                <span id="semaforo_badge" style="background: {{ $semaforo['bg'] }}; color: {{ $semaforo['text_color'] }}; padding: 0.35rem 0.85rem; border-radius: 9999px; font-weight: 800; font-size: 0.85rem; display: inline-block; transition: all 0.3s;">
                                    {{ $semaforo['label'] }}
                                </span>
                                <span style="display: block; font-size: 0.75rem; color: #64748b; margin-top: 0.35rem;">
                                    Cálculo de Semáforo: ≥ 95% (Verde) | 90% - 94% (Amarillo) | &lt; 90% (Rojo)
                                </span>
                            </div>
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
                            <div style="width: 100%; height: 12px; background: #edf2f7; border-radius: 10px; overflow: hidden;">
                                <div id="progress_bar_fill" style="width: {{ $acuerdo->porcentaje_avance }}%; height: 100%; background: {{ $semaforo['color'] }}; transition: width 0.3s ease, background-color 0.3s ease;"></div>
                            </div>
                        @endif
                    </div>
                </div>

                <script>
                    function updateFormSemaforo(valStr) {
                        const valDisplay = document.getElementById('semaforo_val_display');
                        const dotDisplay = document.getElementById('semaforo_dot_display');
                        const badge = document.getElementById('semaforo_badge');
                        const pctLabel = document.getElementById('pct_label');
                        const progressBarFill = document.getElementById('progress_bar_fill');

                        if (pctLabel) pctLabel.innerText = valStr !== '' ? valStr : '0';
                        if (valDisplay) valDisplay.innerText = (valStr !== '' && !isNaN(valStr) ? valStr : '0') + '%';

                        let color = '#94a3b8';
                        let bg = '#f1f5f9';
                        let textColor = '#475569';
                        let label = 'Sin Registro';

                        if (valStr !== '' && !isNaN(valStr) && parseFloat(valStr) >= 0) {
                            const num = parseFloat(valStr);
                            if (num >= 95) {
                                color = '#10b981';
                                bg = '#d1fae5';
                                textColor = '#065f46';
                                label = 'En Meta (≥ 95%)';
                            } else if (num >= 90) {
                                color = '#f59e0b';
                                bg = '#fef3c7';
                                textColor = '#92400e';
                                label = 'Prevención (90% - 94%)';
                            } else {
                                color = '#ef4444';
                                bg = '#fee2e2';
                                textColor = '#991b1b';
                                label = 'Atención Requerida (< 90%)';
                            }
                        }

                        if (dotDisplay) dotDisplay.style.backgroundColor = color;
                        if (badge) {
                            badge.style.backgroundColor = bg;
                            badge.style.color = textColor;
                            badge.innerText = label;
                        }
                        if (progressBarFill) {
                            progressBarFill.style.width = Math.min(100, Math.max(0, parseFloat(valStr) || 0)) + '%';
                            progressBarFill.style.backgroundColor = color;
                        }
                    }
                </script>

                <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
                    <button type="button" onclick="window.history.back()" class="btn"
                        style="background: #e2e8f0;">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Acuerdo</button>
                </div>
            </form>
        </div>

        <div>
            <div class="card" style="padding: 0; overflow: hidden; border: 1px solid #e2e8f0; margin-bottom: 1.5rem;">
                {{-- Header con gradiente --}}
                <div style="background: linear-gradient(135deg, #4f46e5, #6366f1); padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 0.8rem;">
                    <div style="width: 32px; height: 32px; background: rgba(255,255,255,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.9rem;">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <h3 style="color: white; font-size: 1.1rem; font-weight: 700; margin: 0;">Capturar Avance</h3>
                </div>

                <div style="padding: 1.5rem;">
                    {{-- Formulario para nuevo avance --}}
                    <form action="{{ route('acuerdos.comment', $acuerdo) }}" method="POST">
                        @csrf
                        <textarea name="comentario" required rows="3"
                            placeholder="Describe el avance realizado hoy..."
                            style="width: 100%; padding: 0.75rem; border: 2px solid #f1f5f9; border-radius: 0.5rem; font-family: inherit; font-size: 0.9rem; resize: vertical; outline: none; transition: border-color 0.2s;"
                            onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#f1f5f9'"></textarea>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.75rem; justify-content: center; border-radius: 0.5rem;">
                            <i class="fas fa-plus"></i> Registrar Avance
                        </button>
                    </form>

                    {{-- Historial de avances --}}
                    <div style="margin-top: 1.5rem; max-height: 480px; overflow-y: auto; padding-right: 0.25rem;">
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
                <h4 style="margin-bottom: 1rem; color: var(--secondary); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Historial de Cambios</h4>
                <div style="font-size: 0.85rem; max-height: 300px; overflow-y: auto;">
                    @foreach($acuerdo->bitacoras()->latest()->get() as $log)
                        <div style="margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 1px dashed #e2e8f0;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.2rem;">
                                <span style="font-weight: 600; color: #4b5563;">{{ ucfirst($log->campo) }}</span>
                                <span style="color: #9ca3af; font-size: 0.75rem;">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div style="color: #6b7280; font-size: 0.8rem;">
                                de <span style="text-decoration: line-through; opacity: 0.6;">"{{ $log->valor_anterior }}"</span> 
                                a <span style="color: var(--primary); font-weight: 600;">"{{ $log->valor_nuevo }}"</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        const kpisByArea = @json($kpisByArea ?? []);
        const initialOldActividad = @json(old('actividad', $acuerdo->actividad));

        function updateKpisForArea(areaName) {
            const select = document.getElementById('kpi_select');
            const customInput = document.getElementById('kpi_custom_input');
            if (!select) return;

            select.innerHTML = '<option value="">-- Seleccionar KPI --</option>';

            const kpis = kpisByArea[areaName] || [];
            let isMatched = false;

            kpis.forEach(kpi => {
                const opt = document.createElement('option');
                opt.value = kpi;
                opt.textContent = kpi;
                if (initialOldActividad && initialOldActividad === kpi) {
                    opt.selected = true;
                    isMatched = true;
                }
                select.appendChild(opt);
            });

            const otherOpt = document.createElement('option');
            otherOpt.value = '__custom__';
            otherOpt.textContent = 'Otro / Ingresar manualmente...';
            select.appendChild(otherOpt);

            if (initialOldActividad && !isMatched) {
                otherOpt.selected = true;
                if (customInput) {
                    customInput.style.display = 'block';
                    customInput.value = initialOldActividad;
                    customInput.name = 'actividad';
                    select.name = '';
                }
            } else {
                if (customInput) {
                    customInput.style.display = 'none';
                    customInput.name = '';
                    select.name = 'actividad';
                }
            }
        }

        function handleKpiChange(val) {
            const select = document.getElementById('kpi_select');
            const customInput = document.getElementById('kpi_custom_input');
            if (val === '__custom__') {
                if (customInput) {
                    customInput.style.display = 'block';
                    customInput.required = true;
                    customInput.name = 'actividad';
                    select.name = '';
                    customInput.focus();
                }
            } else {
                if (customInput) {
                    customInput.style.display = 'none';
                    customInput.required = false;
                    customInput.name = '';
                    select.name = 'actividad';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const areaField = document.getElementById('area_field');
            const initialArea = areaField ? (areaField.value || areaField.getAttribute('value')) : '';
            updateKpisForArea(initialArea);
        });
    </script>
@endsection
