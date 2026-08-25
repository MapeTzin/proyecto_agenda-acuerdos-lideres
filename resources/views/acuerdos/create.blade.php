@extends('layouts.app')

@section('title', 'Nuevo Acuerdo - Agenda de Acuerdos')
@section('header_title', 'Crear Nuevo Acuerdo')

@section('content')
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <form action="{{ route('acuerdos.store') }}" method="POST">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                {{-- Row 1: Area and Responsable --}}
                <div class="form-group">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">ÁREA</label>
                    @if(count(auth()->user()->areas) > 1)
                        <select name="area" id="area_field" onchange="updateKpisForArea(this.value)" style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                            @foreach(auth()->user()->areas as $area)
                                <option value="{{ $area }}" {{ old('area') == $area ? 'selected' : '' }}>{{ $area }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" id="area_field" value="{{ old('area', auth()->user()->area) }}" readonly
                            style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; background-color: #f8fafc;">
                        <input type="hidden" name="area" value="{{ old('area', auth()->user()->area) }}">
                    @endif
                    @error('area') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">RESPONSABLE</label>
                    <input type="text" value="{{ old('responsable', auth()->user()->name) }}" readonly
                        style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; background-color: #f8fafc;">
                    <input type="hidden" name="responsable" value="{{ old('responsable', auth()->user()->name) }}">
                    @error('responsable') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>


                {{-- Row 2: Cuadrante and Prioridad --}}
                <div class="form-group">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Cuadrante</label>
                    <select name="tipo_cuadrante" required
                        style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                        <option value="1" {{ old('tipo_cuadrante') == '1' ? 'selected' : '' }}>Cuadrante 1 (Urgente / Impacto Alto)</option>
                        <option value="2" {{ old('tipo_cuadrante', '2') == '2' ? 'selected' : '' }}>Cuadrante 2 (Importante / Seguimiento)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Prioridad</label>
                    <select name="prioridad"
                        style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                        <option value="alta" {{ old('prioridad') == 'alta' ? 'selected' : '' }}>Alta</option>
                        <option value="media" {{ old('prioridad', 'media') == 'media' ? 'selected' : '' }}>Media</option>
                    </select>
                </div>

                {{-- Row 3-5: Spanned KPI, Acuerdo, Descripcion --}}
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
                    @error('actividad') <span style="color: var(--danger); font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">ACUERDO (ESTADO REAL DEL INDICADOR)</label>
                    <input type="text" name="acuerdo" value="{{ old('acuerdo') }}" required
                        style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                    @error('acuerdo') <span style="color: var(--danger); font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Descripción Detallada</label>
                    <textarea name="descripcion" rows="3"
                        style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">{{ old('descripcion') }}</textarea>
                </div>

                {{-- Row 6: Compromiso Lunes and Apoyo --}}
                <div class="form-group">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">COMPROMISO DÍA LUNES</label>
                    <input type="text" name="compromiso_lunes" value="{{ old('compromiso_lunes') }}"
                        style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                </div>

                <div class="form-group">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Apoyo</label>
                    <input type="text" name="apoyo" value="{{ old('apoyo') }}"
                        style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                </div>

                {{-- Row 7: Fechas --}}
                <div class="form-group">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', date('Y-m-d')) }}" required
                        style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                </div>

                <div class="form-group">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Fecha Compromiso</label>
                    <input type="date" name="fecha_compromiso" value="{{ old('fecha_compromiso') }}" required
                        style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                </div>

                {{-- Row 8: Porcentaje Inicial --}}
                <div class="form-group" style="grid-column: span 2;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Porcentaje de Avance Inicial (%)</label>
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem;">
                        <input type="number" name="porcentaje_avance" id="porcentaje_avance_create"
                            value="{{ old('porcentaje_avance', 0) }}"
                            min="0" max="100" step="1"
                            oninput="updateCreateSemaforo(this.value)"
                            style="width: 100px; padding: 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 0.5rem; font-weight: 800; font-size: 1.05rem; text-align: center; outline: none; background: white;" />
                        <span style="font-weight: 700; color: #475569; font-size: 1rem;">%</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 1.5rem; background: #f8fafc; padding: 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px; background: white; border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 6px 14px; min-width: 60px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                            <span id="create_val_display" style="font-size: 1.05rem; font-weight: 800; color: #1e293b;">0%</span>
                            <span id="create_dot_display" style="width: 12px; height: 12px; border-radius: 50%; background: #ef4444; display: inline-block; transition: background 0.3s;"></span>
                        </div>
                        <div>
                            <span id="create_badge" style="background: #fee2e2; color: #991b1b; padding: 0.35rem 0.85rem; border-radius: 9999px; font-weight: 800; font-size: 0.85rem; display: inline-block; transition: all 0.3s;">
                                Atención Requerida (< 90%)
                            </span>
                            <span style="display: block; font-size: 0.75rem; color: #64748b; margin-top: 0.35rem;">
                                Cálculo de Semáforo: ≥ 95% (Verde) | 90% - 94% (Amarillo) | &lt; 90% (Rojo)
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function updateCreateSemaforo(valStr) {
                    const valDisplay = document.getElementById('create_val_display');
                    const dotDisplay = document.getElementById('create_dot_display');
                    const badge = document.getElementById('create_badge');

                    if (valDisplay) valDisplay.innerText = (valStr !== '' && !isNaN(valStr) ? valStr : '0') + '%';

                    let color = '#ef4444';
                    let bg = '#fee2e2';
                    let textColor = '#991b1b';
                    let label = 'Atención Requerida (< 90%)';

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
                        }
                    }

                    if (dotDisplay) dotDisplay.style.backgroundColor = color;
                    if (badge) {
                        badge.style.backgroundColor = bg;
                        badge.style.color = textColor;
                        badge.innerText = label;
                    }
                }

                const kpisByArea = @json($kpisByArea ?? []);
                const initialOldActividad = @json(old('actividad', ''));

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

            <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
                <button type="button" onclick="window.location.href='{{ route('acuerdos.index') }}'" class="btn"
                    style="background: #e2e8f0;">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Acuerdo</button>
            </div>
        </form>
    </div>
@endsection