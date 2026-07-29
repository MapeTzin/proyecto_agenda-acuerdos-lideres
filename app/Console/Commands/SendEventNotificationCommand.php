<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventNotificationMail;
use App\Models\Area;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SendEventNotificationCommand extends Command
{
    protected $signature = 'event:notify {event_id}';
    protected $description = 'Sends event notification email in background';

    public function handle()
    {
        $eventId = $this->argument('event_id');
        $event = Event::find($eventId);
        if (!$event) return;

        // Get involved areas names
        $involvedAreasIds = $event->multiple_areas->pluck('id');
        $areaNames = Area::whereIn('id', $involvedAreasIds)->pluck('name');
        
        // Find users in those areas in memory since 'area' is a dynamic attribute
        $usersInAreas = User::all()->filter(function ($user) use ($areaNames) {
            return $areaNames->contains($user->area);
        });
        
        // Also include manually added users if any
        $manualUsers = $event->users;
        
        $recipients = $usersInAreas->merge($manualUsers)->unique('email');
        
        $emails = $recipients->pluck('email')->filter()->values()->toArray();
        if (empty($emails)) return;

        $firstEmail = array_shift($emails);

        try {
            $mail = Mail::to($firstEmail);
            if (!empty($emails)) {
                $mail->bcc($emails);
            }
            $mail->send(new EventNotificationMail($event));
        } catch (\Exception $e) {
            Log::error("Error sending event notification: " . $e->getMessage());
        }
    }
}
