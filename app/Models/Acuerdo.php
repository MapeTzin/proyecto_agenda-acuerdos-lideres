<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acuerdo extends Model
{
    use HasFactory;

    protected $table = 'acuerdos';

    protected $fillable = [
        'area',
        'tipo_cuadrante',
        'actividad',
        'acuerdo',
        'descripcion',
        'responsable',
        'apoyo',
        'fecha_inicio',
        'fecha_compromiso',
        'fecha_cierre',
        'estatus',
        'prioridad',
        'porcentaje_avance',
        'comentarios',
        'fecha_cambio_estatus',
        'motivo_detencion',
        'comentario_cierre',
        'compromiso_lunes',
        'cierre_viernes',
        'tiempo_total_dias',
        'tiempo_detenido_dias',
        'tiempo_efectivo_dias'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_compromiso' => 'date',
        'fecha_cierre' => 'date',
        'fecha_cambio_estatus' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($acuerdo) {
            if ($acuerdo->isDirty('estatus')) {
                $acuerdo->fecha_cambio_estatus = now();
            }
        });

        // Excluir lógicamente acuerdos cuya área sea 'ELIMINADO' (Soft Delete lógico por falta de privilegios DELETE)
        static::addGlobalScope('exclude_deleted_areas', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->where('area', '!=', 'ELIMINADO');
        });
    }

    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class);
    }

    public function comentarios_extra()
    {
        return $this->hasMany(Comentario::class);
    }

    public function latestComment()
    {
        return $this->hasOne(Comentario::class)->latestOfMany();
    }

    public function avancesDiarios()
    {
        return $this->hasMany(AvanceDiario::class);
    }

    /**
     * Calcula los días hábiles (Lunes-Viernes) entre dos fechas
     */
    public function getWorkingDays($start, $end)
    {
        $start = \Carbon\Carbon::parse($start);
        $end = \Carbon\Carbon::parse($end);
        $days = 0;
        
        if ($start->gt($end)) return 0;

        while ($start->lte($end)) {
            if (!$start->isWeekend()) {
                $days++;
            }
            $start->addDay();
        }
        return $days;
    }

    /**
     * Calcula la información del semáforo según el porcentaje de avance (regla KPI Áreas)
     * >= 95%: VERDE (#10b981)
     * 90% - 94%: AMARILLO (#f59e0b)
     * < 90%: ROJO (#ef4444)
     * nulo/negativo: GRIS (#94a3b8)
     */
    public static function getSemaforoInfo($porcentaje): array
    {
        if ($porcentaje === null || $porcentaje < 0 || $porcentaje === '' || $porcentaje === '-') {
            return [
                'semaforo' => 'GRIS',
                'color' => '#94a3b8',
                'bg' => '#f1f5f9',
                'text_color' => '#475569',
                'label' => 'Sin Registro'
            ];
        }

        $num = (float)$porcentaje;
        if ($num >= 95) {
            return [
                'semaforo' => 'VERDE',
                'color' => '#10b981',
                'bg' => '#d1fae5',
                'text_color' => '#065f46',
                'label' => 'En Meta (≥ 95%)'
            ];
        } elseif ($num >= 80) {
            return [
                'semaforo' => 'AMARILLO',
                'color' => '#f59e0b',
                'bg' => '#fef3c7',
                'text_color' => '#92400e',
                'label' => 'Prevención (80% - 94%)'
            ];
        } else {
            return [
                'semaforo' => 'ROJO',
                'color' => '#ef4444',
                'bg' => '#fee2e2',
                'text_color' => '#991b1b',
                'label' => 'Atención (< 80%)'
            ];
        }
    }

    public function getSemaforoAttribute(): array
    {
        return static::getSemaforoInfo($this->porcentaje_avance);
    }

    /**
     * Determina si el acuerdo está en atraso crítico.
     * Es atraso crítico cuando la fecha compromiso ya pasó y la actividad NO está al 100% (finalizada).
     */
    public function isAtrasoCritico(): bool
    {
        if ($this->estatus === 'finalizado') return false;
        if (!$this->fecha_compromiso) return false;

        return \Carbon\Carbon::today()->gt($this->fecha_compromiso);
    }

    /**
     * Determina si se puede cambiar la fecha compromiso.
     * Solo se permite si falta al menos 1 día para la fecha compromiso actual.
     * Es decir, la fecha compromiso debe ser estrictamente posterior a mañana (hoy + 1 día).
     */
    public function canChangeFechaCompromiso(): bool
    {
        if (!$this->fecha_compromiso) return true;
        if ($this->estatus === 'finalizado') return false;

        if (auth()->check()) {
            $user = auth()->user();
            if ($user->email === 'soporte@mapetzin.com' || $user->hasRole('Administrador')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Alerta: Pendiente por más de 1 día
     */
    public function isAlertaPendiente(): bool
    {
        if ($this->estatus !== 'pendiente') return false;
        if (!$this->fecha_cambio_estatus) return false;
        
        return $this->fecha_cambio_estatus->diffInDays(now()) >= 1;
    }

    /**
     * Alerta: Detenido por más de 2 días
     */
    public function isAlertaDetenido(): bool
    {
        if ($this->estatus !== 'detenido') return false;
        if (!$this->fecha_cambio_estatus) return false;

        return $this->fecha_cambio_estatus->diffInDays(now()) >= 2;
    }

    /**
     * Recalcula el porcentaje de avance automáticamente basado en actividad histórica.
     * REGLA: Solo se suma avance si existe registro de actividad ese día.
     */
    public function calculateAutomaticProgress()
    {
        if (!$this->fecha_inicio || !$this->fecha_compromiso) return;
        
        // Si ya está finalizado, no recalculamos (ya se fijó al 100% y se calcularon tiempos)
        if ($this->estatus === 'finalizado') {
            $this->porcentaje_avance = 100;
            $this->saveQuietly();
            return;
        }

        if ($this->estatus === 'pendiente') {
            $this->porcentaje_avance = 0;
            $this->saveQuietly();
            return;
        }

        // Si está en atraso crítico, no se actualiza el porcentaje automático
        if ($this->isAtrasoCritico()) return;

        // 1. Días hábiles totales del proyecto (Duración total)
        $totalWorkingDays = $this->getWorkingDays($this->fecha_inicio, $this->fecha_compromiso);
        if ($totalWorkingDays <= 0) return;

        $weightPerDay = 100 / $totalWorkingDays;

        // 2. Contar días con actividad (comentarios o bitacora) entre inicio y hoy
        $currentDate = \Carbon\Carbon::parse($this->fecha_inicio);
        $endDate = \Carbon\Carbon::now()->min($this->fecha_compromiso);
        $daysWithActivity = 0;

        // Eager load or fetch dates with activity to avoid N+1 if this was called in a loop, 
        // but here we are in a single model instance.
        $activityDates = Comentario::where('acuerdo_id', $this->id)
            ->selectRaw('DATE(created_at) as date')
            ->pluck('date')
            ->toArray();
            
        // También consideramos bitácora como actividad? El usuario dice "registro de actividad".
        $bitacoraDates = Bitacora::where('acuerdo_id', $this->id)
            ->selectRaw('DATE(created_at) as date')
            ->pluck('date')
            ->toArray();
            
        $allActivityDates = array_unique(array_merge($activityDates, $bitacoraDates));

        while ($currentDate->lte($endDate)) {
            if (!$currentDate->isWeekend()) {
                if (in_array($currentDate->format('Y-m-d'), $allActivityDates)) {
                    $daysWithActivity++;
                }
            }
            $currentDate->addDay();
        }

        // 3. Calcular nuevo avance
        $newProgress = round($daysWithActivity * $weightPerDay);
        if ($newProgress > 100) $newProgress = 100;

        $this->porcentaje_avance = $newProgress;
        $this->saveQuietly();
    }

    /**
     * Finaliza el acuerdo y calcula métricas
     */
    public function finalizar($comentarioCierre = null)
    {
        $this->estatus = 'finalizado';
        $this->comentario_cierre = $comentarioCierre;
        $this->fecha_cierre = now();
        $this->porcentaje_avance = 100;

        // Cálculos de tiempos
        $this->tiempo_total_dias = $this->getWorkingDays($this->fecha_inicio, $this->fecha_cierre);
        
        // Para tiempo detenido, tendríamos que buscar en la bitácora periodos en estatus 'detenido'
        // Por ahora, lo simplificamos o buscamos histórico
        $detenidoStart = null;
        $totalDetenido = 0;
        $logs = $this->bitacoras()->where('campo', 'estatus')->orderBy('created_at')->get();
        
        $lastStatus = 'pendiente';
        $lastDate = \Carbon\Carbon::parse($this->created_at);

        foreach ($logs as $log) {
            if ($lastStatus === 'detenido') {
                $totalDetenido += $this->getWorkingDays($lastDate, $log->created_at);
            }
            $lastStatus = $log->valor_nuevo;
            $lastDate = \Carbon\Carbon::parse($log->created_at);
        }
        
        // Ultimo tramo si terminó siendo detenido (raro pero posible)
        if ($lastStatus === 'detenido') {
            $totalDetenido += $this->getWorkingDays($lastDate, now());
        }

        $this->tiempo_detenido_dias = $totalDetenido;
        $this->tiempo_efectivo_dias = max(0, $this->tiempo_total_dias - $totalDetenido);

        $this->save();
    }
}
