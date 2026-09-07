<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class KpisAreas extends Component
{
    public $selectedArea = ''; // Empty string = Monitor Global (Vista Corporativa)
    public $selectedYear = 2026;
    public $selectedMonth = 8; // Default to August (month 8)
    public $isAdminUser = false;
    public $userAreaName = 'CONTABILIDAD';
    
    // KPI input data binding
    public $kpiValues = [];
    public $kpiNotes = [];
    public $kpiStartNotes = []; // Bindings for Inicio de Semana comments
    public $kpiMonthlyValues = []; // Monthly totals mapping

    public $weeksList = [];

    public function getWeeksOfMonth($year, $month)
    {
        $weeks = [];
        $firstDay = \Carbon\Carbon::createFromDate($year, $month, 1);
        $lastDay = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        $currentDay = $firstDay->copy();
        $weekNum = 1;
        $weekStart = null;
        $weekEnd = null;
        
        while ($currentDay->lte($lastDay)) {
            if ($currentDay->isWeekend()) {
                if ($weekStart !== null) {
                    $weeks[$weekNum] = [
                        'label' => "Semana $weekNum",
                        'range' => $weekStart->day === $weekEnd->day ? "{$weekStart->day}" : "{$weekStart->day} al {$weekEnd->day}"
                    ];
                    $weekNum++;
                    $weekStart = null;
                    $weekEnd = null;
                }
                $currentDay->addDay();
                continue;
            }
            
            if ($weekStart === null) {
                $weekStart = $currentDay->copy();
            }
            $weekEnd = $currentDay->copy();
            
            if ($currentDay->isFriday() || $currentDay->copy()->addDay()->month !== (int)$month) {
                $weeks[$weekNum] = [
                    'label' => "Semana $weekNum",
                    'range' => $weekStart->day === $weekEnd->day ? "{$weekStart->day}" : "{$weekStart->day} al {$weekEnd->day}"
                ];
                $weekNum++;
                $weekStart = null;
                $weekEnd = null;
            }
            
            $currentDay->addDay();
        }
        
        if ($weekStart !== null) {
            $weeks[$weekNum] = [
                'label' => "Semana $weekNum",
                'range' => $weekStart->day === $weekEnd->day ? "{$weekStart->day}" : "{$weekStart->day} al {$weekEnd->day}"
            ];
        }
        
        return $weeks;
    }

    // Modal fields for Create/Edit KPI
    public $showModal = false;
    public $editingKpiId = null;
    public $newKpiName = '';
    public $newKpiDescription = '';
    public $newKpiTarget = 95;

    public function checkIsAdmin(): bool
    {
        $u = auth()->user();
        if (!$u) return false;

        $email = strtolower($u->email ?? '');
        if (in_array($email, [
            'soporte@mapetzin.com',
            'direccion@mapetzin.com',
            'v.arochi@mapetzin.com',
            'v.arochi@gmail.com',
            'g.gonzalez@mapetzin.com'
        ])) {
            return true;
        }

        $pos = strtolower($u->position ?? $u->area ?? '');
        if (str_contains($pos, 'staff') || str_contains($pos, 'jefa de staff') || str_contains($pos, 'jefatura de staff')) {
            return true;
        }

        if (isset($u->role_id) && $u->role_id == 3) {
            return true;
        }

        $rol = strtolower($u->attributes['rol'] ?? '');
        if ($rol === 'admin' || $rol === 'administrador') {
            return true;
        }

        try {
            if ($u->hasRole('administrador') || $u->hasRole('admin')) {
                return true;
            }
        } catch (\Throwable $e) {}

        return false;
    }

    public function getNormalizedUserArea(): string
    {
        $user = auth()->user();
        if ($user && $user->email === 'l.diaz@mapetzin.com') {
            return 'CUENTAS POR COBRAR';
        }

        $userArea = strtoupper($user->area ?? 'CONTABILIDAD');

        if (str_contains($userArea, 'CONTABILIDAD')) return 'CONTABILIDAD';
        if (str_contains($userArea, 'CXC') || str_contains($userArea, 'COBRAR')) return 'CUENTAS POR COBRAR';
        if (str_contains($userArea, 'SISTEMAS') || str_contains($userArea, 'TI')) return 'SISTEMAS Y TI';
        if (str_contains($userArea, 'ALMACEN') || str_contains($userArea, 'EMBARQUE')) return 'ALMACEN';
        if (str_contains($userArea, 'CALIDAD')) return 'ASEGURAMIENTO DE CALIDAD';
        if (str_contains($userArea, 'CAPITAL') || str_contains($userArea, 'HUMANO') || str_contains($userArea, 'RECURSOS')) return 'CAPITAL HUMANO';
        if (str_contains($userArea, 'ADQUISICION')) return 'ADQUISICIONES';
        if (str_contains($userArea, 'VENTAS') || str_contains($userArea, 'LICITACION')) return 'VENTAS';
        if (str_contains($userArea, 'COMERCIAL')) return 'COMERCIAL';
        if (str_contains($userArea, 'STAFF')) return 'JEFATURA DE STAFF';
        if (str_contains($userArea, 'ASIS') || str_contains($userArea, 'ADM')) return 'ASIS ADM';
        if (str_contains($userArea, 'CULTURA')) return 'CULTURA ORGANIZACIONAL';

        return 'CONTABILIDAD';
    }

    public function mount()
    {
        $this->isAdminUser = $this->checkIsAdmin();
        $this->userAreaName = $this->getNormalizedUserArea();

        if ($this->isAdminUser) {
            $this->selectedArea = ''; // Admin defaults to Corporate Monitor View
        } else {
            $this->selectedArea = $this->userAreaName; // Non-admin locks to their area
        }

        $this->selectedMonth = 8; // Default to August
        $this->weeksList = $this->getWeeksOfMonth($this->selectedYear, $this->selectedMonth);
        $this->loadKpiData();
    }

    public function updatedSelectedMonth()
    {
        $this->weeksList = $this->getWeeksOfMonth($this->selectedYear, $this->selectedMonth);
        $this->loadKpiData();
    }

    public function selectArea($areaName)
    {
        if (!$this->isAdminUser && $areaName !== $this->userAreaName) {
            return; // Non-admin cannot switch to other areas
        }
        $this->selectedArea = $areaName;
        $this->loadKpiData();
    }

    public function updatedSelectedArea()
    {
        if (!$this->isAdminUser && empty($this->selectedArea)) {
            $this->selectedArea = $this->userAreaName;
        }
        $this->loadKpiData();
    }

    public function loadKpiData()
    {
        if (empty($this->selectedArea)) {
            $this->kpiValues = [];
            $this->kpiNotes = [];
            $this->kpiStartNotes = [];
            $this->kpiMonthlyValues = [];
            return;
        }

        $db = DB::connection('areaskpi');
        
        $area = $db->table('areas')->where('nombre', $this->selectedArea)->first();
        if (!$area) {
            $this->kpiValues = [];
            $this->kpiNotes = [];
            $this->kpiStartNotes = [];
            $this->kpiMonthlyValues = [];
            return;
        }

        $kpis = $db->table('kpis')
            ->where('area_id', $area->id)
            ->select('id', 'area_id', 'nombre as name', 'descripcion as description', 'meta as target', 'formula', 'tipo', 'periodicidad', 'is_inverse')
            ->orderBy('id', 'asc')
            ->get();

        $this->kpiValues = [];
        $this->kpiNotes = [];
        $this->kpiStartNotes = [];
        $this->kpiMonthlyValues = [];

        $totalWeeks = count($this->weeksList);

        foreach ($kpis as $kpi) {
            $monthlyRes = $db->table('kpi_values')
                ->where('kpi_id', $kpi->id)
                ->whereYear('fecha', $this->selectedYear)
                ->whereMonth('fecha', $this->selectedMonth)
                ->orderBy('fecha', 'desc')
                ->first();

            $val = ($monthlyRes && $monthlyRes->valor !== null) ? (float)$monthlyRes->valor : -1;
            $note = $monthlyRes ? ($monthlyRes->notas ?? '') : '';
            $dateStr = $monthlyRes ? \Carbon\Carbon::parse($monthlyRes->fecha)->format('d/m/Y') : '';

            $this->kpiValues[$kpi->id] = [];
            
            for ($w = 1; $w <= $totalWeeks; $w++) {
                $this->kpiValues[$kpi->id][$w] = [
                    'val' => $val == -1 ? '-' : (float)$val,
                    'date' => $dateStr,
                ];
            }

            $this->kpiMonthlyValues[$kpi->id] = $val == -1 ? '-' : (float)$val;
            $this->kpiNotes[$kpi->id] = $note;
            $this->kpiStartNotes[$kpi->id] = '';
        }
    }

    public function saveChanges()
    {
        if (empty($this->selectedArea)) {
            return;
        }

        $db = DB::connection('areaskpi');
        $totalWeeks = count($this->weeksList);

        foreach ($this->kpiValues as $kpiId => $weeks) {
            $note = $this->kpiNotes[$kpiId] ?? null;
            $sumVal = 0;
            $countVal = 0;
            $hasAnyVal = false;
            $lastValidDate = null;

            for ($w = 1; $w <= $totalWeeks; $w++) {
                $rawVal = $weeks[$w]['val'] ?? '-';
                
                if ($rawVal !== '-' && $rawVal !== '' && $rawVal !== null && is_numeric($rawVal) && (float)$rawVal >= 0) {
                    $numericVal = (float)$rawVal;
                    $sumVal += $numericVal;
                    $countVal++;
                    $hasAnyVal = true;
                    
                    $dateInput = $weeks[$w]['date'] ?? null;
                    if (!empty($dateInput)) {
                        try {
                            if (str_contains($dateInput, '/')) {
                                $parts = explode('/', $dateInput);
                                if (count($parts) === 3) {
                                    $lastValidDate = "{$parts[2]}-{$parts[1]}-{$parts[0]}";
                                }
                            } else {
                                $lastValidDate = $dateInput;
                            }
                        } catch (\Exception $e) {}
                    }
                }
            }

            $monthlyVal = ($hasAnyVal && $countVal > 0) ? round($sumVal / $countVal, 1) : null;
            $saveDate = $lastValidDate ?? sprintf('%04d-%02d-01', $this->selectedYear, $this->selectedMonth);

            $existing = $db->table('kpi_values')
                ->where('kpi_id', $kpiId)
                ->whereYear('fecha', $this->selectedYear)
                ->whereMonth('fecha', $this->selectedMonth)
                ->first();

            if ($existing) {
                $db->table('kpi_values')
                    ->where('id', $existing->id)
                    ->update([
                        'valor' => $monthlyVal,
                        'notas' => $note,
                        'updated_at' => now(),
                    ]);
            } else {
                if ($monthlyVal !== null || !empty($note)) {
                    $db->table('kpi_values')->insert([
                        'kpi_id' => $kpiId,
                        'valor' => $monthlyVal ?? 0.00,
                        'fecha' => $saveDate,
                        'notas' => $note,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        session()->flash('success', '¡Porcentajes y avances de KPI´s guardados exitosamente!');
        $this->loadKpiData();
    }

    public function openCreateModal()
    {
        $this->editingKpiId = null;
        $this->newKpiName = '';
        $this->newKpiDescription = '';
        $this->newKpiTarget = 95;
        $this->showModal = true;
    }

    public function editKpi($kpiId)
    {
        $db = DB::connection('areaskpi');
        $kpi = $db->table('kpis')->where('id', $kpiId)->first();
        if ($kpi) {
            $this->editingKpiId = $kpi->id;
            $this->newKpiName = $kpi->nombre ?? $kpi->name;
            $this->newKpiDescription = $kpi->descripcion ?? $kpi->description;
            $this->newKpiTarget = $kpi->meta ?? $kpi->target ?? 95;
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingKpiId = null;
    }

    public function saveNewKpi()
    {
        $this->validate([
            'newKpiName' => 'required|string|max:255',
        ]);

        $db = DB::connection('areaskpi');

        if ($this->editingKpiId) {
            // Update existing KPI
            $db->table('kpis')->where('id', $this->editingKpiId)->update([
                'nombre' => $this->newKpiName,
                'descripcion' => $this->newKpiDescription,
                'meta' => $this->newKpiTarget,
                'updated_at' => now(),
            ]);

            session()->flash('success', 'KPI actualizado correctamente.');
        } else {
            // Create new KPI
            $areaCat = !empty($this->selectedArea) ? $this->selectedArea : 'CONTABILIDAD';
            $area = $db->table('areas')->where('nombre', $areaCat)->first();
            $areaId = $area ? $area->id : 1;

            $kpiId = $db->table('kpis')->insertGetId([
                'area_id' => $areaId,
                'nombre' => $this->newKpiName,
                'descripcion' => $this->newKpiDescription,
                'meta' => $this->newKpiTarget,
                'tipo' => '%',
                'periodicidad' => 'mensual',
                'is_inverse' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create initial value record for current month
            $db->table('kpi_values')->insert([
                'kpi_id' => $kpiId,
                'valor' => null,
                'fecha' => sprintf('%04d-%02d-01', $this->selectedYear, $this->selectedMonth),
                'notas' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            session()->flash('success', 'Nuevo KPI agregado correctamente.');
        }

        $this->showModal = false;
        $this->editingKpiId = null;
        $this->loadKpiData();
    }

    public function deleteKpi($kpiId)
    {
        $db = DB::connection('areaskpi');
        $db->table('kpi_values')->where('kpi_id', $kpiId)->delete();
        $db->table('kpis')->where('id', $kpiId)->delete();
        session()->flash('success', 'KPI eliminado exitosamente.');
        $this->loadKpiData();
    }

    public function render()
    {
        $db = DB::connection('areaskpi');
        
        $officialAreas = [
            'ALMACEN',
            'VENTAS',
            'COMERCIAL',
            'ASEGURAMIENTO DE CALIDAD',
            'CAPITAL HUMANO',
            'JEFATURA DE STAFF',
            'ASIS ADM',
            'ADQUISICIONES',
            'SISTEMAS Y TI',
            'CONTABILIDAD',
            'CUENTAS POR COBRAR',
            'CULTURA ORGANIZACIONAL'
        ];

        $monthsList = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $latestMonthNum = (int)$this->selectedMonth;
        $prevMonthNum = $latestMonthNum === 1 ? 12 : $latestMonthNum - 1;
        $prevMonthYear = $latestMonthNum === 1 ? $this->selectedYear - 1 : $this->selectedYear;

        $latestMonthName = strtoupper($monthsList[$latestMonthNum]);
        $prevMonthName = strtoupper($monthsList[$prevMonthNum]);

        // Monitor Global Data calculation
        $monitorData = [];
        $totalKpisGlobal = 0;
        $totalMayoCumplimientoSum = 0;
        $totalJunioCumplimientoSum = 0;
        $areasEnMetaCount = 0;
        $areasEnPrevencionCount = 0;
        $areasEnAtencionCount = 0;
        $areasCount = count($officialAreas);

        $totalWeeks = count($this->weeksList);

        foreach ($officialAreas as $areaItem) {
            $areaRow = $db->table('areas')->where('nombre', $areaItem)->first();
            
            if ($areaRow) {
                $kpisForArea = $db->table('kpis')
                    ->where('area_id', $areaRow->id)
                    ->select(
                        'id',
                        'area_id',
                        'nombre as name',
                        'descripcion as description',
                        'meta as target',
                        'formula',
                        'tipo',
                        'periodicidad',
                        'is_inverse'
                    )
                    ->orderBy('id', 'asc')
                    ->get();
            } else {
                $kpisForArea = collect();
            }

            $totalKpis = count($kpisForArea);
            $totalKpisGlobal += $totalKpis;

            $kpiIds = $kpisForArea->pluck('id');

            // Previous Month Average
            $avgMayo = $totalKpis > 0 ? $db->table('kpi_values')
                ->whereIn('kpi_id', $kpiIds)
                ->whereYear('fecha', $prevMonthYear)
                ->whereMonth('fecha', $prevMonthNum)
                ->whereNotNull('valor')
                ->where('valor', '>=', 0)
                ->avg('valor') : null;
            $pctMayo = $avgMayo !== null ? round($avgMayo, 1) : 0;
            $totalMayoCumplimientoSum += $pctMayo;

            // Selected Month Average
            $avgJunio = $totalKpis > 0 ? $db->table('kpi_values')
                ->whereIn('kpi_id', $kpiIds)
                ->whereYear('fecha', $this->selectedYear)
                ->whereMonth('fecha', $latestMonthNum)
                ->whereNotNull('valor')
                ->where('valor', '>=', 0)
                ->avg('valor') : null;
            $pctJunio = $avgJunio !== null ? round($avgJunio, 1) : 0;
            $totalJunioCumplimientoSum += $pctJunio;

            if ($pctJunio >= 95) {
                $semaforo = 'VERDE';
                $label = 'En Meta (≥ 95%)';
                $color = '#10b981';
                $badgeBg = '#d1fae5';
                $areasEnMetaCount++;
            } elseif ($pctJunio >= 80) {
                $semaforo = 'AMARILLO';
                $label = 'Prevención (80% - 94%)';
                $color = '#d97706';
                $badgeBg = '#fef3c7';
                $areasEnPrevencionCount++;
            } else {
                $semaforo = 'ROJO';
                $label = 'Atención Requerida (< 80%)';
                $color = '#dc2626';
                $badgeBg = '#fee2e2';
                $areasEnAtencionCount++;
            }

            // Build individual KPI dots for Previous Month
            $kpiDotsMayo = [];
            foreach ($kpisForArea as $kpiObj) {
                $resM = $db->table('kpi_values')
                    ->where('kpi_id', $kpiObj->id)
                    ->whereYear('fecha', $prevMonthYear)
                    ->whereMonth('fecha', $prevMonthNum)
                    ->orderBy('fecha', 'desc')
                    ->first();

                $vM = ($resM && $resM->valor !== null) ? (float)$resM->valor : -1;

                if ($vM < 0) {
                    $dotColor = '#94a3b8';
                    $vStr = '-';
                } elseif ($vM >= 95.0) {
                    $dotColor = '#10b981';
                    $vStr = $vM . '%';
                } elseif ($vM >= 80) {
                    $dotColor = '#f59e0b';
                    $vStr = $vM . '%';
                } else {
                    $dotColor = '#ef4444';
                    $vStr = $vM . '%';
                }

                $kpiDotsMayo[] = [
                    'name' => $kpiObj->name,
                    'val' => $vStr,
                    'color' => $dotColor,
                ];
            }

            // Build detailed individual KPI dots for Selected Month
            $kpiDotsJunio = [];
            $kpisEnMeta = 0;
            $kpisEnPrevencion = 0;
            $kpisEnAtencion = 0;
            $kpisSinRegistro = 0;
            $areaKpisSum = 0;
            $areaKpisCountWithVal = 0;

            foreach ($kpisForArea as $kpiObj) {
                $targetVal = (float)($kpiObj->target ?? 95.0);

                $monthlyRes = $db->table('kpi_values')
                    ->where('kpi_id', $kpiObj->id)
                    ->whereYear('fecha', $this->selectedYear)
                    ->whereMonth('fecha', $latestMonthNum)
                    ->orderBy('fecha', 'desc')
                    ->first();

                $vJ = ($monthlyRes && $monthlyRes->valor !== null) ? (float)$monthlyRes->valor : -1;

                // Build weekly breakdown representation
                $weeksBreakdown = [];
                for ($w = 1; $w <= $totalWeeks; $w++) {
                    $wLabel = $this->weeksList[$w]['label'] ?? "Semana $w";
                    $wRange = $this->weeksList[$w]['range'] ?? '';
                    $wVal = $vJ >= 0 ? $vJ : null;
                    $wDate = $monthlyRes ? \Carbon\Carbon::parse($monthlyRes->fecha)->format('d/m/Y') : '';

                    if ($wVal !== null) {
                        if ($wVal >= $targetVal) {
                            $wColor = '#10b981';
                        } elseif ($wVal >= 80.0) {
                            $wColor = '#f59e0b';
                        } else {
                            $wColor = '#ef4444';
                        }
                    } else {
                        $wColor = '#94a3b8';
                    }

                    $weeksBreakdown[] = [
                        'semana' => $w,
                        'label' => $wLabel,
                        'range' => $wRange,
                        'val' => $wVal !== null ? $wVal . '%' : '-',
                        'numeric_val' => $wVal,
                        'color' => $wColor,
                        'date' => $wDate,
                    ];
                }

                if ($vJ < 0) {
                    $dotColor = '#94a3b8';
                    $statusText = 'Sin Registro / Pendiente';
                    $statusBadgeBg = '#f1f5f9';
                    $statusBadgeColor = '#64748b';
                    $vStr = '-';
                    $kpisSinRegistro++;
                } elseif ($vJ >= $targetVal) {
                    $dotColor = '#10b981';
                    $statusText = 'En Meta (≥ ' . number_format($targetVal, 0) . '%)';
                    $statusBadgeBg = '#d1fae5';
                    $statusBadgeColor = '#065f46';
                    $vStr = $vJ . '%';
                    $kpisEnMeta++;
                    $areaKpisSum += $vJ;
                    $areaKpisCountWithVal++;
                } elseif ($vJ >= 80.0) {
                    $dotColor = '#f59e0b';
                    $statusText = 'Prevención (80% - 94%)';
                    $statusBadgeBg = '#fef3c7';
                    $statusBadgeColor = '#92400e';
                    $vStr = $vJ . '%';
                    $kpisEnPrevencion++;
                    $areaKpisSum += $vJ;
                    $areaKpisCountWithVal++;
                } else {
                    $dotColor = '#ef4444';
                    $statusText = 'Atención Requerida (< 80%)';
                    $statusBadgeBg = '#fee2e2';
                    $statusBadgeColor = '#991b1b';
                    $vStr = $vJ . '%';
                    $kpisEnAtencion++;
                    $areaKpisSum += $vJ;
                    $areaKpisCountWithVal++;
                }

                if ($vJ >= 0) {
                    $calcExplanation = "Puntaje consolidado de evaluación para {$latestMonthName}: {$vJ}%" . (!empty($kpiObj->formula) ? " (Fórmula: {$kpiObj->formula})" : "");
                } else {
                    $calcExplanation = "No se ha capturado evaluación para este indicador en {$latestMonthName}." . (!empty($kpiObj->formula) ? " (Fórmula: {$kpiObj->formula})" : "");
                }

                $kpiDotsJunio[] = [
                    'id' => $kpiObj->id,
                    'name' => $kpiObj->name,
                    'description' => $kpiObj->description ?? '',
                    'target' => $targetVal,
                    'val' => $vStr,
                    'raw_val' => $vJ,
                    'color' => $dotColor,
                    'status_text' => $statusText,
                    'status_bg' => $statusBadgeBg,
                    'status_color' => $statusBadgeColor,
                    'weeks' => $weeksBreakdown,
                    'weeks_count' => $vJ >= 0 ? 1 : 0,
                    'calc_explanation' => $calcExplanation,
                    'cierre_notas' => $monthlyRes->notas ?? '',
                    'inicio_notas' => '',
                ];
            }

            // Delta calculation
            $diff = round($pctJunio - $pctMayo, 1);
            if ($diff > 0) {
                $trendText = "+{$diff}%";
                $trendIcon = 'fa-arrow-trend-up';
                $trendColor = '#10b981';
                $trendBg = '#d1fae5';
            } elseif ($diff < 0) {
                $trendText = "{$diff}%";
                $trendIcon = 'fa-arrow-trend-down';
                $trendColor = '#ef4444';
                $trendBg = '#fee2e2';
            } else {
                $trendText = "0.0%";
                $trendIcon = 'fa-minus';
                $trendColor = '#64748b';
                $trendBg = '#f1f5f9';
            }

            $monitorData[] = [
                'area' => $areaItem,
                'total_kpis' => $totalKpis,
                'cumplimiento_mayo' => $pctMayo,
                'cumplimiento_junio' => $pctJunio,
                'diff' => $diff,
                'trendText' => $trendText,
                'trendIcon' => $trendIcon,
                'trendColor' => $trendColor,
                'trendBg' => $trendBg,
                'semaforo' => $semaforo,
                'label' => $label,
                'color' => $color,
                'badgeBg' => $badgeBg,
                'kpi_dots_mayo' => $kpiDotsMayo,
                'kpi_dots_junio' => $kpiDotsJunio,
                'kpis_en_meta_count' => $kpisEnMeta,
                'kpis_en_prevencion_count' => $kpisEnPrevencion,
                'kpis_en_atencion_count' => $kpisEnAtencion,
                'kpis_sin_registro_count' => $kpisSinRegistro,
                'area_kpis_count_with_val' => $areaKpisCountWithVal,
            ];
        }

        $cumplimientoMayoAvg = $areasCount > 0 ? round($totalMayoCumplimientoSum / $areasCount, 1) : 0;
        $cumplimientoJunioAvg = $areasCount > 0 ? round($totalJunioCumplimientoSum / $areasCount, 1) : 0;
        $globalDiff = round($cumplimientoJunioAvg - $cumplimientoMayoAvg, 1);

        // Individual Area Data if an area is selected
        $kpis = collect();
        $weeklyAverages = [];

        if (!empty($this->selectedArea)) {
            $areaObj = $db->table('areas')->where('nombre', $this->selectedArea)->first();
            if ($areaObj) {
                $kpis = $db->table('kpis')
                    ->where('area_id', $areaObj->id)
                    ->select(
                        'id',
                        'area_id',
                        'nombre as name',
                        'descripcion as description',
                        'meta as target',
                        'formula',
                        'tipo',
                        'periodicidad',
                        'is_inverse'
                    )
                    ->orderBy('id', 'asc')
                    ->get();
            }

            $totalWeeks = count($this->weeksList);
            for ($w = 1; $w <= $totalWeeks; $w++) {
                $sum = 0;
                $count = 0;
                $kpiListForWeek = [];

                foreach ($kpis as $kpi) {
                    $wVal = $this->kpiValues[$kpi->id][$w]['val'] ?? '-';
                    if ($wVal !== '-' && $wVal !== '' && is_numeric($wVal) && (float)$wVal >= 0) {
                        $sum += (float)$wVal;
                        $count++;
                        $kpiListForWeek[] = [
                            'name' => $kpi->name,
                            'val' => (float)$wVal . '%'
                        ];
                    }
                }

                $weeklyAverages[$w] = [
                    'avg' => $count > 0 ? round($sum / $count, 1) : '-',
                    'count' => $count,
                    'total' => count($kpis),
                    'kpis' => $kpiListForWeek
                ];
            }
        }

        return view('livewire.kpis-areas', [
            'officialAreas' => $officialAreas,
            'monitorData' => $monitorData,
            'totalKpisGlobal' => $totalKpisGlobal,
            'areasCount' => $areasCount,
            'cumplimientoMayoAvg' => $cumplimientoMayoAvg,
            'cumplimientoJunioAvg' => $cumplimientoJunioAvg,
            'globalDiff' => $globalDiff,
            'areasEnMetaCount' => $areasEnMetaCount,
            'areasEnPrevencionCount' => $areasEnPrevencionCount,
            'areasEnAtencionCount' => $areasEnAtencionCount,
            'prevMonthName' => $prevMonthName,
            'latestMonthName' => $latestMonthName,
            'showModal' => $this->showModal,
            'editingKpiId' => $this->editingKpiId,
            'isAdminUser' => $this->isAdminUser,
            'userAreaName' => $this->userAreaName,
            'selectedArea' => $this->selectedArea,
            'kpis' => $kpis,
            'monthsList' => $monthsList,
            'weeksList' => $this->weeksList,
            'weeklyAverages' => $weeklyAverages
        ])->extends('layouts.app')->section('content');
    }
}
