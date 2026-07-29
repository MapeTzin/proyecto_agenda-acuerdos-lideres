<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f1f5f9; color: #1e293b; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 2rem auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .header { background: #6366f1; color: #ffffff; padding: 2rem; text-align: center; }
        .header h1 { margin: 0; font-size: 1.5rem; letter-spacing: 0.5px; }
        .content { padding: 2rem; line-height: 1.6; }
        .event-box { background: #f8fafc; border-left: 4px solid #6366f1; padding: 1.5rem; margin: 1.5rem 0; border-radius: 0 8px 8px 0; }
        .event-box h2 { margin: 0 0 1rem; color: #1e293b; font-size: 1.25rem; }
        .info-row { display: flex; align-items: flex-start; margin-bottom: 0.75rem; gap: 0.5rem; }
        .footer { padding: 1.5rem; text-align: center; color: #64748b; font-size: 0.85rem; border-top: 1px solid #e2e8f0; }
        .btn { display: inline-block; background: #6366f1; color: #ffffff; padding: 0.75rem 1.5rem; border-radius: 6px; text-decoration: none; margin-top: 1.5rem; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📅 Nuevo Evento del Planeador</h1>
        </div>
        <div class="content">
            <p>Hola,</p>
            <p>Se ha registrado un nuevo evento en el cual tu área se encuentra involucrada. Aquí tienes los detalles:</p>
            
            <div class="event-box">
                <h2>{{ $event->title }}</h2>
                <div class="info-row">
                    <strong>Inicio:</strong> {{ $event->start->format('d/m/Y h:i A') }}
                </div>
                <div class="info-row">
                    <strong>Fin:</strong> {{ $event->end->format('d/m/Y h:i A') }}
                </div>
                @if($event->meet_link)
                <div class="info-row">
                    <strong>Reunión:</strong> <a href="{{ $event->meet_link }}" target="_blank">Google Meet</a>
                </div>
                @endif
                @if($event->description)
                <div class="info-row">
                    <strong>Descripción:</strong> {!! $event->description !!}
                </div>
                @endif
            </div>

            <p>Para ver más detalles o el calendario completo, puedes acceder a la plataforma:</p>
            <div style="text-align: center;">
                <a href="{{ route('planeador') }}" class="btn">Ir al Planeador Estratégico</a>
            </div>
            
            <p style="margin-top: 1.5rem; font-size: 0.9rem; color: #64748b;">
                <strong>💡 Tip:</strong> Hemos adjuntado una tarjeta de invitación (invitacion.ics) para que la agregues a tu Google Calendar o Outlook personal.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} MAPE+TZIN - Agenda de Acuerdos de Líderes. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>
