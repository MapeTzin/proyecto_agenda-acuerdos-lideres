<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Area;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\EventNotificationMail;

use Spatie\GoogleCalendar\Event as GoogleEvent;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()->can('planeador.view'), 403, 'No tiene permiso para consultar el Planeador.');
        $areas = Area::all();
        $users = User::all();
        return view('calendar.index', compact('areas', 'users'));
    }

    public function import()
    {
        abort_unless(Auth::user()->can('planeador.manage'), 403, 'No tiene permiso para importar eventos al Planeador.');
        try {
            $googleEvents = GoogleEvent::get();
            $count = 0;

            // Get user's area color
            $userAreaName = Auth::user()->area;
            $userArea = Area::where('name', $userAreaName)->first();
            $defaultColor = $userArea->color ?? '#4285F4';
            $areaId = $userArea->id ?? (Area::first()->id ?? 1);

            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

            foreach ($googleEvents as $gEvent) {
                // Determine start and end
                $start = $gEvent->startDateTime ?? Carbon::parse($gEvent->startDate);
                $end = $gEvent->endDateTime ?? Carbon::parse($gEvent->endDate);

                // Create or update local event
                Event::updateOrCreate(
                    ['location' => 'google_id:' . $gEvent->id],
                    [
                        'title' => $gEvent->name,
                        'description' => $gEvent->description,
                        'start' => $start,
                        'end' => $end,
                        'all_day' => $gEvent->allDay,
                        'area_id' => $areaId,
                        'created_by' => Auth::id(),
                        'color' => $defaultColor,
                        'is_public' => true
                    ]
                );
                $count++;
            }

            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

            return response()->json(['success' => true, 'count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Configuración de Google Calendar incompleta o inválida.']);
        }
    }

    public function importICS(Request $request)
    {
        abort_unless(Auth::user()->can('planeador.manage'), 403, 'No tiene permiso para importar eventos al Planeador.');

        $request->validate([
            'ics_file' => 'required|file',
        ]);

        try {
            $content = file_get_contents($request->file('ics_file')->path());
            
            // Basic ICS Parsing logic
            $events = [];
            $currentEvent = null;
            
            $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $content));
            
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === 'BEGIN:VEVENT') {
                    $currentEvent = [];
                } else if ($line === 'END:VEVENT') {
                    if ($currentEvent) $events[] = $currentEvent;
                    $currentEvent = null;
                } else if ($currentEvent !== null) {
                    if (strpos($line, ':') !== false) {
                        $parts = explode(':', $line, 2);
                        $key = $parts[0];
                        $value = $parts[1];
                        // Handling basic params like DTSTART;VALUE=DATE:20260223
                        if (strpos($key, ';') !== false) {
                            $key = explode(';', $key)[0];
                        }
                        $currentEvent[$key] = $value;
                    }
                }
            }

            // Get user's area color
            $userAreaName = Auth::user()->area;
            $userArea = Area::where('name', $userAreaName)->first();
            $defaultColor = $userArea->color ?? '#10b981';
            $areaId = $userArea->id ?? (Area::first()->id ?? 1);

            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

            $count = 0;
            foreach ($events as $evt) {
                $title = $evt['SUMMARY'] ?? 'Sin título';
                $description = $evt['DESCRIPTION'] ?? '';
                $location = $evt['LOCATION'] ?? '';
                
                $startStr = $evt['DTSTART'] ?? null;
                $endStr = $evt['DTEND'] ?? $startStr;

                if (!$startStr) continue;

                try {
                    $start = Carbon::parse($startStr);
                    $end = Carbon::parse($endStr);
                } catch (\Exception $e) {
                    continue;
                }

                Event::create([
                    'title' => $title,
                    'description' => $description,
                    'start' => $start,
                    'end' => $end,
                    'location' => $location,
                    'area_id' => $areaId,
                    'created_by' => Auth::id(),
                    'color' => $defaultColor, 
                    'is_public' => true,
                    'all_day' => (strlen($startStr) <= 8)
                ]);
                $count++;
            }

            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

            return response()->json(['success' => true, 'count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Error al procesar el archivo: ' . $e->getMessage()]);
        }
    }

    public function fetch(Request $request)
    {
        abort_unless(Auth::user()->can('planeador.view'), 403);

        $query = Event::with('multiple_areas');

        // Filter by date range if provided by FullCalendar
        if ($request->has('start') && $request->has('end')) {
            $query->whereBetween('start', [
                Carbon::parse($request->start)->toDateTimeString(),
                Carbon::parse($request->end)->toDateTimeString()
            ]);
        }

        // Check if user has role Administrator or Director General
        $isAdmin = Auth::user()->hasRole(['Administrador', 'Director General']);

        $userAreas = Area::whereIn('name', Auth::user()->areas)->get();
        $userAreaIds = $userAreas->pluck('id')->toArray();

        if (!$isAdmin) {
            $query->where(function ($q) use ($userAreaIds) {
                $q->where('is_public', true)
                    ->orWhere('created_by', Auth::id())
                    ->orWhereHas('users', function ($u) {
                        $u->where('users.id', Auth::id());
                    });
                    
                if (!empty($userAreaIds)) {
                    $q->orWhereHas('multiple_areas', function ($a) use ($userAreaIds) {
                        $a->whereIn('areas.id', $userAreaIds);
                    });
                }
            });
        }

        return response()->json(
            $query->get()->map(function($event) {
                $start = $event->all_day ? $event->start->format('Y-m-d') : $event->start->toIso8601String();
                $end = $event->all_day ? $event->end->format('Y-m-d') : $event->end->toIso8601String();
                
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $start,
                    'end' => $end,
                    'allDay' => (bool) $event->all_day,
                    'color' => $event->color,
                    'description' => $event->description,
                    'location' => $event->location,
                    'meet_link' => $event->meet_link,
                    'extendedProps' => [
                        'is_public' => $event->is_public,
                        'involved_areas' => $event->multiple_areas->pluck('id')->toArray()
                    ]
                ];
            })
        );
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->can('planeador.manage'), 403, 'No tiene permiso para crear eventos en el Planeador.');

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'area_id' => 'required|exists:areas,id',
            'users' => 'array',
            'involved_areas' => 'array',
        ]);

        // Get color based on user's area
        $userAreaName = Auth::user()->area;
        $userArea = Area::where('name', $userAreaName)->first();
        $forcedColor = $userArea ? $userArea->color : '#3788d8';

        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        $event = Event::create([
            'title' => $data['title'],
            'start' => $data['start'],
            'end' => $data['end'],
            'area_id' => $data['area_id'],
            'created_by' => Auth::id(),
            'is_public' => $request->boolean('is_public'),
            'color' => $forcedColor, // Se ignora lo enviado y se fuerza el color del área del usuario logueado
            'description' => $request->description,
            'location' => $request->location,
            'meet_link' => $request->meet_link,
            'all_day' => $request->boolean('all_day'),
        ]);

        if (!empty($request->users)) {
            $event->users()->sync($request->users);
        }

        if (!empty($request->involved_areas)) {
            $event->multiple_areas()->sync($request->involved_areas);
        }

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $this->notifyInvolved($event);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, Event $event)
    {
        abort_unless(Auth::user()->can('planeador.manage'), 403, 'No tiene permiso para modificar eventos en el Planeador.');

        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        $event->update($request->only([
            'title',
            'start',
            'end',
            'color',
            'is_public',
            'description',
            'location',
            'meet_link',
            'all_day'
        ]));

        if ($request->has('users')) {
            $event->users()->sync($request->users);
        }

        if ($request->has('involved_areas')) {
            $event->multiple_areas()->sync($request->involved_areas);
        }

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $this->notifyInvolved($event);

        return response()->json(['success' => true]);
    }

    private function notifyInvolved(Event $event)
    {
        // Ejecutar el envío de correo en un proceso en segundo plano para evitar que la pantalla se quede pasmada
        $basePath = base_path();
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $cmd = "start /B cmd /C \"php \"{$basePath}\\artisan\" event:notify {$event->id} > NUL 2> NUL\"";
            pclose(popen($cmd, "r"));
        } else {
            exec("php {$basePath}/artisan event:notify {$event->id} > /dev/null 2>&1 &");
        }
    }

    public function destroy(Event $event)
    {
        abort_unless(Auth::user()->can('planeador.manage'), 403, 'No tiene permiso para eliminar eventos del Planeador.');

        $event->delete();

        return response()->json(['success' => true]);
    }
}
