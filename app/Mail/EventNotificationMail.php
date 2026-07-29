<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class EventNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔔 Nuevo Evento en tu Calendario: ' . $this->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.event_notification',
        );
    }

    public function attachments(): array
    {
        $icsContent = $this->generateIcs();
        
        return [
            Attachment::fromData(fn () => $icsContent, 'invitacion.ics')
                ->withMime('text/calendar'),
        ];
    }

    private function generateIcs(): string
    {
        $start = $this->event->start->format('Ymd\THis');
        $end = $this->event->end->format('Ymd\THis');
        $title = $this->event->title;
        $description = strip_tags($this->event->description);
        $location = $this->event->meet_link ?: $this->event->location ?: 'Sin ubicación';
        $uid = uniqid() . '@mapetzin.com';
        $stamp = Carbon::now()->format('Ymd\THis');

        return "BEGIN:VCALENDAR\r\n" .
               "VERSION:2.0\r\n" .
               "PRODID:-//MAPE+TZIN//Agenda de Acuerdos//ES\r\n" .
               "METHOD:REQUEST\r\n" . // REQUEST mode triggers 'add to calendar' UI
               "BEGIN:VEVENT\r\n" .
               "UID:{$uid}\r\n" .
               "DTSTAMP:{$stamp}\r\n" .
               "DTSTART:{$start}\r\n" .
               "DTEND:{$end}\r\n" .
               "SUMMARY:{$title}\r\n" .
               "DESCRIPTION:{$description}\r\n" .
               "LOCATION:{$location}\r\n" .
               "TRANSP:OPAQUE\r\n" .
               "SEQUENCE:0\r\n" .
               "STATUS:CONFIRMED\r\n" .
               "END:VEVENT\r\n" .
               "END:VCALENDAR";
    }
}
