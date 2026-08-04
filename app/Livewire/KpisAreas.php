<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class KpisAreas extends Component
{
    public $selectedArea = ''; // Empty string = Monitor Global (Vista Corporativa)
    public $selectedYear = 2026;
    public $isAdminUser = false;
    public $userAreaName = 'CONTABILIDAD';
    
    // KPI input data binding: [kpi_id => [month => ['val' => ..., 'date' => ...]]]
    public $kpiValues = [];
    public $kpiNotes = [];

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
        $userArea = strtoupper(auth()->user()->area ?? 'CONTABILIDAD');

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
            return;
        }

        $db = DB::connection('sistema_tickets');
        
        $kpis = $db->table('kpis')
            ->where('is_active', 1)
            ->where(function($q) {
                $q->where('category', $this->selectedArea)
                  ->orWhere('category', 'LIKE', '%' . $this->selectedArea . '%');
            })
            ->orderBy('id', 'asc')
            ->get();

        $this->kpiValues = [];
        $this->kpiNotes = [];

        foreach ($kpis as $kpi) {
            $results = $db->table('kpi_results')
                ->where('kpi_id', $kpi->id)
                ->where('year', $this->selectedYear)
                ->get()
                ->keyBy('month');

            $this->kpiValues[$kpi->id] = [];
            
            $latestNote = '';
            for ($m = 1; $m <= 8; $m++) {
                $res = $results->get($m);
                $val = $res ? $res->value : -1;
                if ($val === null) {
                    $val = -1;
                }
                
                $dateStr = '';
                if ($res && $res->period_date) {
                    $dateStr = \Carbon\Carbon::parse($res->period_date)->format('d/m/Y');
                }

                $this->kpiValues[$kpi->id][$m] = [
                    'val' => $val == -1 ? '-' : (float)$val,
                    'date' => $dateStr,
                ];

                if ($res && !empty($res->notes)) {
                    $latestNote = $res->notes;
                }
            }

            $this->kpiNotes[$kpi->id] = $latestNote;
        }
    }

    public function saveChanges()
    {
        if (empty($this->selectedArea)) {
            return;
        }

        $db = DB::connection('sistema_tickets');

        foreach ($this->kpiValues as $kpiId => $months) {
            $note = $this->kpiNotes[$kpiId] ?? null;

            for ($m = 1; $m <= 8; $m++) {
                $rawVal = $months[$m]['val'] ?? '-';
                
                if ($rawVal === '-' || $rawVal === '' || $rawVal === null) {
                    $numericVal = -1;
                    $dbDate = null;
                } else {
                    $numericVal = (float)$rawVal;
                    $dateInput = $months[$m]['date'] ?? null;
                    if (empty($dateInput)) {
                        $dbDate = now()->format('Y-m-d');
                    } else {
                        try {
                            if (str_contains($dateInput, '/')) {
                                $parts = explode('/', $dateInput);
                                if (count($parts) === 3) {
                                    $dbDate = "{$parts[2]}-{$parts[1]}-{$parts[0]}";
                                } else {
                                    $dbDate = now()->format('Y-m-d');
                                }
                            } else {
                                $dbDate = $dateInput;
                            }
                        } catch (\Exception $e) {
                            $dbDate = now()->format('Y-m-d');
                        }
                    }
                }

                $db->table('kpi_results')->updateOrInsert(
                    [
                        'kpi_id' => $kpiId,
                        'year' => $this->selectedYear,
                        'month' => $m
                    ],
                    [
                        'value' => $numericVal,
                        'target_value' => 95.00,
                        'period_date' => $dbDate,
                        'notes' => $m == 8 ? $note : ($m == 7 && empty($months[8]['date']) ? $note : null),
                        'updated_at' => now(),
                    ]
                );
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
        $db = DB::connection('sistema_tickets');
        $kpi = $db->table('kpis')->where('id', $kpiId)->first();
        if ($kpi) {
            $this->editingKpiId = $kpi->id;
            $this->newKpiName = $kpi->name;
            $this->newKpiDescription = $kpi->description;
            $this->newKpiTarget = $kpi->target;
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

        $db = DB::connection('sistema_tickets');

        if ($this->editingKpiId) {
            // Update existing KPI
            $db->table('kpis')->where('id', $this->editingKpiId)->update([
                'name' => $this->newKpiName,
                'description' => $this->newKpiDescription,
                'target' => $this->newKpiTarget,
                'updated_at' => now(),
            ]);

            session()->flash('success', 'KPI actualizado correctamente.');
        } else {
            // Create new KPI
            $areaCat = !empty($this->selectedArea) ? $this->selectedArea : 'CONTABILIDAD';
            $code = strtoupper(substr($areaCat, 0, 4)) . '_' . time();

            $kpiId = $db->table('kpis')->insertGetId([
                'name' => $this->newKpiName,
                'code' => $code,
                'description' => $this->newKpiDescription,
                'category' => $areaCat,
                'target' => $this->newKpiTarget,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            for ($m = 1; $m <= 8; $m++) {
                $db->table('kpi_results')->insert([
                    'kpi_id' => $kpiId,
                    'year' => $this->selectedYear,
                    'month' => $m,
                    'value' => -1,
                    'target_value' => $this->newKpiTarget,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            session()->flash('success', 'Nuevo KPI agregado correctamente.');
        }

        $this->showModal = false;
        $this->editingKpiId = null;
        $this->loadKpiData();
    }

    public function deleteKpi($kpiId)
    {
        $db = DB::connection('sistema_tickets');
        $db->table('kpis')->where('id', $kpiId)->update(['is_active' => 0, 'updated_at' => now()]);
        session()->flash('success', 'KPI eliminado exitosamente.');
        $this->loadKpiData();
    }

    public function render()
    {
        $db = DB::connection('sistema_tickets');
        
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
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto'
        ];

        $prevMonthNum = 7;
        $prevMonthName = 'JULIO';

        $latestMonthNum = 8;
        $latestMonthName = 'AGOSTO';

        // Monitor Global Data calculation
        $monitorData = [];
        $totalKpisGlobal = 0;
        $totalMayoCumplimientoSum = 0;
        $totalJunioCumplimientoSum = 0;
        $areasEnMetaCount = 0;
        $areasCount = count($officialAreas);

        foreach ($officialAreas as $areaItem) {
            $kpisForArea = $db->table('kpis')
                ->where('is_active', 1)
                ->where(function($q) use ($areaItem) {
                    $q->where('category', $areaItem)
                      ->orWhere('category', 'LIKE', '%' . $areaItem . '%');
                })
                ->orderBy('id', 'asc')
                ->get();

            $totalKpis = count($kpisForArea);
            $totalKpisGlobal += $totalKpis;

            $kpiIds = $kpisForArea->pluck('id');

            // Mayo Average (Month 5)
            $avgMayo = $totalKpis > 0 ? $db->table('kpi_results')
                ->whereIn('kpi_id', $kpiIds)
                ->where('year', 2026)
                ->where('month', $prevMonthNum)
                ->where('value', '>=', 0)
                ->avg('value') : null;
            $pctMayo = $avgMayo !== null ? round($avgMayo, 1) : 0;
            $totalMayoCumplimientoSum += $pctMayo;

            // Junio Average (Month 6)
            $avgJunio = $totalKpis > 0 ? $db->table('kpi_results')
                ->whereIn('kpi_id', $kpiIds)
                ->where('year', 2026)
                ->where('month', $latestMonthNum)
                ->where('value', '>=', 0)
                ->avg('value') : null;
            $pctJunio = $avgJunio !== null ? round($avgJunio, 1) : 0;
            $totalJunioCumplimientoSum += $pctJunio;

            if ($pctJunio >= 95) {
                $semaforo = 'VERDE';
                $label = 'En Meta (≥ 95%)';
                $color = '#10b981';
                $badgeBg = '#d1fae5';
                $areasEnMetaCount++;
            } elseif ($pctJunio >= 90) {
                $semaforo = 'AMARILLO';
                $label = 'Prevención (90% - 94%)';
                $color = '#d97706';
                $badgeBg = '#fef3c7';
            } else {
                $semaforo = 'ROJO';
                $label = 'Atención Requerida (< 90%)';
                $color = '#dc2626';
                $badgeBg = '#fee2e2';
            }

            // Build individual KPI dots for MAYO (Month 5)
            $kpiDotsMayo = [];
            foreach ($kpisForArea as $kpiObj) {
                $resM = $db->table('kpi_results')
                    ->where('kpi_id', $kpiObj->id)
                    ->where('year', 2026)
                    ->where('month', $prevMonthNum)
                    ->first();

                $vM = ($resM && $resM->value !== null) ? (float)$resM->value : -1;

                if ($vM < 0) {
                    $dotColor = '#94a3b8';
                    $vStr = '-';
                } elseif ($vM >= $kpiObj->target) {
                    $dotColor = '#10b981';
                    $vStr = $vM . '%';
                } elseif ($vM >= ($kpiObj->target - 5)) {
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

            // Build individual KPI dots for JUNIO (Month 6)
            $kpiDotsJunio = [];
            foreach ($kpisForArea as $kpiObj) {
                $resJ = $db->table('kpi_results')
                    ->where('kpi_id', $kpiObj->id)
                    ->where('year', 2026)
                    ->where('month', $latestMonthNum)
                    ->first();

                $vJ = ($resJ && $resJ->value !== null) ? (float)$resJ->value : -1;

                if ($vJ < 0) {
                    $dotColor = '#94a3b8';
                    $vStr = '-';
                } elseif ($vJ >= $kpiObj->target) {
                    $dotColor = '#10b981';
                    $vStr = $vJ . '%';
                } elseif ($vJ >= ($kpiObj->target - 5)) {
                    $dotColor = '#f59e0b';
                    $vStr = $vJ . '%';
                } else {
                    $dotColor = '#ef4444';
                    $vStr = $vJ . '%';
                }

                $kpiDotsJunio[] = [
                    'name' => $kpiObj->name,
                    'val' => $vStr,
                    'color' => $dotColor,
                ];
            }

            // Delta calculation
            $diff = round($pctJunio - $pctMayo, 1);
            if ($diff > 0) {
                $trendText = "+{$diff}%";
                $trendIcon = 'fa-arrow-trend-up';
                $trendColor = '#10b981'; // Green
                $trendBg = '#d1fae5';
            } elseif ($diff < 0) {
                $trendText = "{$diff}%";
                $trendIcon = 'fa-arrow-trend-down';
                $trendColor = '#ef4444'; // Red
                $trendBg = '#fee2e2';
            } else {
                $trendText = "0.0%";
                $trendIcon = 'fa-minus';
                $trendColor = '#64748b'; // Grey
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
            ];
        }

        $cumplimientoMayoAvg = $areasCount > 0 ? round($totalMayoCumplimientoSum / $areasCount, 1) : 0;
        $cumplimientoJunioAvg = $areasCount > 0 ? round($totalJunioCumplimientoSum / $areasCount, 1) : 0;
        $globalDiff = round($cumplimientoJunioAvg - $cumplimientoMayoAvg, 1);

        // Individual Area Data if an area is selected
        $kpis = collect();
        $monthlyAverages = [];

        if (!empty($this->selectedArea)) {
            $kpis = $db->table('kpis')
                ->where('is_active', 1)
                ->where(function($q) {
                    $q->where('category', $this->selectedArea)
                      ->orWhere('category', 'LIKE', '%' . $this->selectedArea . '%');
                })
                ->orderBy('id', 'asc')
                ->get();

            foreach ($monthsList as $mNum => $mName) {
                $sum = 0;
                $count = 0;

                foreach ($kpis as $kpi) {
                    $mVal = $this->kpiValues[$kpi->id][$mNum]['val'] ?? '-';
                    if ($mVal !== '-' && $mVal !== '' && is_numeric($mVal) && (float)$mVal >= 0) {
                        $sum += (float)$mVal;
                        $count++;
                    }
                }

                $monthlyAverages[$mNum] = $count > 0 ? round($sum / $count, 1) : '-';
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
            'prevMonthName' => $prevMonthName,
            'latestMonthName' => $latestMonthName,
            'showModal' => $this->showModal,
            'editingKpiId' => $this->editingKpiId,
            'isAdminUser' => $this->isAdminUser,
            'userAreaName' => $this->userAreaName,
            'selectedArea' => $this->selectedArea,
            'kpis' => $kpis,
            'monthsList' => $monthsList,
            'monthlyAverages' => $monthlyAverages
        ])->extends('layouts.app')->section('content');
    }
}
