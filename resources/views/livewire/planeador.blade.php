<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fas fa-calendar-alt" style="color: var(--primary);"></i>
                Planeador de Actividades
            </h2>
            <p style="color: var(--secondary); margin-top: 0.25rem;">Gestión estratégica y planificación de compromisos
            </p>
        </div>
        <div
            style="background: white; padding: 0.5rem 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div style="font-weight: 700; color: var(--primary);">{{ ucfirst(now()->translatedFormat('F Y')) }}</div>
            <div style="display: flex; gap: 0.5rem;">
                <button class="btn" style="padding: 0.35rem 0.6rem; background: #f1f5f9;"><i
                        class="fas fa-chevron-left"></i></button>
                <button class="btn" style="padding: 0.35rem 0.6rem; background: #f1f5f9;"><i
                        class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <div
            style="display: grid; grid-template-columns: repeat(7, 1fr); background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
            @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $day)
                <div
                    style="padding: 1rem; text-align: center; font-size: 0.8rem; font-weight: 700; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em;">
                    {{ $day }}
                </div>
            @endforeach
        </div>

        <div style="display: grid; grid-template-columns: repeat(7, 1fr); min-height: 600px;">
            @php
                $startOfMonth = now()->startOfMonth();
                $endOfMonth = now()->endOfMonth();
                $daysInMonth = $startOfMonth->daysInMonth;
                $startDayOfWeek = $startOfMonth->dayOfWeekIso; // 1 (Mon) to 7 (Sun)
            @endphp

            @for ($i = 1; $i < $startDayOfWeek; $i++)
                <div style="border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; background: #fafafa;"></div>
            @endfor

            @for ($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $isToday = now()->day == $day;
                @endphp
                <div style="padding: 1rem; border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; min-height: 120px; position: relative; transition: background 0.2s; cursor: pointer;"
                    onmouseover="this.style.background='#fcfcfc'" onmouseout="this.style.background='white'">
                    <span
                        style="font-size: 1rem; font-weight: 700; color: {{ $isToday ? 'var(--primary)' : '#64748b' }}; 
                              {{ $isToday ? 'background: rgba(79, 70, 229, 0.1); width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%;' : '' }}">
                        {{ $day }}
                    </span>

                    <div style="margin-top: 0.75rem; display: flex; flex-direction: column; gap: 0.4rem;">
                        <!-- Placeholder for activities -->
                        @if($day % 7 == 2)
                            <div
                                style="background: rgba(79, 70, 229, 0.1); color: var(--primary); font-size: 0.7rem; padding: 0.25rem 0.5rem; border-radius: 4px; border-left: 3px solid var(--primary); font-weight: 600;">
                                Revisión KPI Ventas
                            </div>
                        @endif
                        @if($day % 10 == 0)
                            <div
                                style="background: rgba(16, 185, 129, 0.1); color: var(--success); font-size: 0.7rem; padding: 0.25rem 0.5rem; border-radius: 4px; border-left: 3px solid var(--success); font-weight: 600;">
                                Entrega de Reportes
                            </div>
                        @endif
                    </div>
                </div>
            @endfor

            @php
                $remainingCells = (7 - (($daysInMonth + $startDayOfWeek - 1) % 7)) % 7;
            @endphp
            @for ($i = 0; $i < $remainingCells; $i++)
                <div style="border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; background: #fafafa;"></div>
            @endfor
        </div>
    </div>
</div>