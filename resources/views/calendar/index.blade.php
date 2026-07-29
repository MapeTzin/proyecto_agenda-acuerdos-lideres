@extends('layouts.app')

@section('title', 'Planeador Estratégico - Calendario')

@section('content')
    <div class="card" style="padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h2 style="margin: 0; display: flex; align-items: center; gap: 0.75rem; color: #1e293b;">
                    <i class="fas fa-calendar-alt" style="color: var(--primary);"></i>
                    Planeador de Actividades
                </h2>
                <p style="color: var(--secondary); margin-top: 0.25rem;">Gestión centralizada de eventos y compromisos</p>
            </div>
            @if(auth()->user()->hasRole('Administrador') || auth()->user()->email === 'v.arochi@mapetzin.com')
        <div style="display: flex; gap: 0.5rem;">
            <button class="btn" style="background: #ffffff; color: var(--secondary); border: 1px solid #cbd5e0;" onclick="document.getElementById('ics_file').click()">
                <i class="fas fa-file-import"></i> Importar .ICS
            </button>
            <input type="file" id="ics_file" style="display: none;" accept=".ics" onchange="importFromICS(this)">
            <button class="btn" style="background: #ffffff; color: #4285F4; border: 1px solid #4285F4;" onclick="importFromGoogle()">
                <i class="fab fa-google"></i> Importar desde Google
            </button>
            <button class="btn btn-primary" onclick="openModal()">
                <i class="fas fa-plus"></i> Nuevo Evento
            </button>
        </div>
        @endif
        </div>

        <div id="calendar" style="min-height: 700px;"></div>
    </div>

    <!-- Event Modal -->
    <div id="eventModal"
        style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); align-items: center; justify-content: center;">
        <div class="card"
            style="width: 500px; max-width: 90%; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
            <h3 id="modalTitle" style="margin-bottom: 1.5rem;">Nuevo Evento</h3>
            <form id="eventForm">
                <input type="hidden" id="eventId">
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Título</label>
                    <input type="text" id="title" class="form-control" placeholder="Ej: Revisión Mensual" required
                        style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; background: #f1f5f9; padding: 0.6rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
                        <i class="far fa-clock" style="color: #64748b; margin-right: 0.5rem;"></i>
                        <input type="text" id="event_date" class="form-control" placeholder="Seleccionar fecha" 
                            style="flex: 2; border: none; background: transparent; font-weight: 500; cursor: pointer; outline: none;">
                        <div id="time_pickers" style="display: flex; align-items: center; gap: 0.5rem; flex: 3;">
                            <input type="text" id="start_time" class="form-control" placeholder="12:00 PM" 
                                style="width: 80px; text-align: center; border: none; background: #fff; border-radius: 4px; padding: 2px; font-weight: 500; cursor: pointer;">
                            <span style="color: #64748b;">–</span>
                            <input type="text" id="end_time" class="form-control" placeholder="1:00 PM" 
                                style="width: 80px; text-align: center; border: none; background: #fff; border-radius: 4px; padding: 2px; font-weight: 500; cursor: pointer;">
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; padding-left: 0.5rem;">
                        <input type="checkbox" id="all_day" style="width: 16px; height: 16px; cursor: pointer;">
                        <label for="all_day" style="font-weight: 500; color: #475569; cursor: pointer; margin: 0;">Todo el día</label>
                    </div>
                </div>

                <input type="hidden" id="start">
                <input type="hidden" id="end">

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.4rem;">
                        <i class="fas fa-video" style="color: #00897b;"></i> Videoconferencia (Google Meet)
                    </label>
                    <input type="url" id="meet_link" class="form-control" placeholder="Ej: https://meet.google.com/xxx-xxxx-xxx"
                        style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Descripción</label>
                    <div id="editor-container" style="height: 150px; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem; background-color: #f8fafc;"></div>
                </div>

                @php
                    $userArea = \App\Models\Area::where('name', auth()->user()->area)->first();
                    $userColor = $userArea->color ?? '#3788d8';
                    $userAreaId = $userArea->id ?? 1;
                @endphp

                <input type="hidden" id="area_id" value="{{ $userAreaId }}">
                <input type="hidden" id="color" value="{{ $userColor }}">

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Área Solicitante</label>
                    <input type="text" class="form-control" value="{{ implode(' y ', auth()->user()->areas) }}" readonly
                        style="width: 100%; padding: 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; background-color: #f8fafc; font-weight: 600; color: #475569;">
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Áreas Involucradas</label>
                    <div style="max-height: 120px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0.5rem; background: #fff;">
                        @foreach($areas as $area)
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                <input type="checkbox" name="involved_areas" value="{{ $area->id }}" id="area_{{ $area->id }}">
                                <label for="area_{{ $area->id }}" style="margin: 0; font-size: 0.9rem; cursor: pointer;">{{ $area->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.4rem;">Identificador Visual (Color)</label>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div id="color_preview" style="width: 100%; height: 40px; background-color: {{ $userColor }}; border-radius: 0.5rem; border: 1px solid #e2e8f0;"></div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
                    <input type="checkbox" id="is_public">
                    <label for="is_public" style="font-weight: 600;">Evento Público</label>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <button type="button" id="deleteBtn" class="btn" style="background: #fee2e2; color: #dc2626; display: none;" onclick="deleteEvent()">
                        <i class="fas fa-trash-alt"></i> Eliminar
                    </button>
                    <div style="display: flex; gap: 1rem;">
                        <button type="button" class="btn" style="background: #f1f5f9;" onclick="closeModal()">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Guardar Evento</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var quill = new Quill('#editor-container', {
                theme: 'snow',
                placeholder: 'Agregar descripción',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['link'],
                        ['clean']
                    ]
                }
            });



            // Initialize Flatpickr for Date
            const datePicker = flatpickr("#event_date", {
                locale: "es",
                dateFormat: "l, d F",
                altInput: true,
                altFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    updateHiddenDates();
                }
            });

            // Initialize Flatpickr for Time
            const startTimePicker = flatpickr("#start_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "h:i K",
                defaultDate: "12:00",
                onChange: function(selectedDates, dateStr, instance) {
                    updateHiddenDates();
                }
            });

            const endTimePicker = flatpickr("#end_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "h:i K",
                defaultDate: "13:00",
                onChange: function(selectedDates, dateStr, instance) {
                    updateHiddenDates();
                }
            });

            document.getElementById('all_day').addEventListener('change', function() {
                const timePickers = document.getElementById('time_pickers');
                if (this.checked) {
                    timePickers.style.visibility = 'hidden';
                } else {
                    timePickers.style.visibility = 'visible';
                }
                updateHiddenDates();
            });

            function updateHiddenDates() {
                let date = '';
                if (datePicker && datePicker.selectedDates && datePicker.selectedDates.length > 0) {
                    const selectedDate = datePicker.selectedDates[0];
                    const year = selectedDate.getFullYear();
                    const month = String(selectedDate.getMonth() + 1).padStart(2, '0');
                    const day = String(selectedDate.getDate()).padStart(2, '0');
                    date = `${year}-${month}-${day}`;
                } else if (datePicker && datePicker.altInput) {
                    date = datePicker.altInput.value;
                } else if (datePicker && datePicker.input) {
                    date = datePicker.input.value;
                }

                if (!date) {
                    const today = new Date();
                    const year = today.getFullYear();
                    const month = String(today.getMonth() + 1).padStart(2, '0');
                    const day = String(today.getDate()).padStart(2, '0');
                    date = `${year}-${month}-${day}`;
                }

                const isAllDay = document.getElementById('all_day').checked;

                if (isAllDay) {
                    document.getElementById('start').value = date + ' 00:00:00';
                    document.getElementById('end').value = date + ' 23:59:59';
                } else {
                    const formatTime = (picker) => {
                        if (picker && picker.selectedDates && picker.selectedDates.length > 0) {
                            const d = picker.selectedDates[0];
                            const hours = String(d.getHours()).padStart(2, '0');
                            const minutes = String(d.getMinutes()).padStart(2, '0');
                            return `${hours}:${minutes}`;
                        }
                        const val = picker && picker.input ? picker.input.value : '';
                        if (val) {
                            if (val.includes('PM') || val.includes('AM')) {
                                const [time, modifier] = val.split(' ');
                                let [hours, minutes] = time.split(':');
                                if (hours === '12') hours = '00';
                                if (modifier === 'PM') hours = parseInt(hours, 10) + 12;
                                return `${hours.toString().padStart(2, '0')}:${minutes}`;
                            }
                            return val;
                        }
                        return "00:00";
                    };

                    const startTime = formatTime(startTimePicker);
                    const endTime = formatTime(endTimePicker);

                    document.getElementById('start').value = date + ' ' + startTime + ':00';
                    document.getElementById('end').value = date + ' ' + endTime + ':00';
                }
            }

            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    day: 'Día'
                },
                events: '{{ route('events.fetch') }}',
                editable: true,
                selectable: true,
                select: function (info) {
                    openModal(info.startStr, info.endStr);
                },
                eventClick: function (info) {
                    editEvent(info.event);
                },
                eventDrop: function (info) {
                    updateEventResizeDrop(info.event);
                },
                eventResize: function (info) {
                    updateEventResizeDrop(info.event);
                }
            });
            calendar.render();

            window.openModal = function (start = '', end = '') {
                document.getElementById('eventModal').style.display = 'flex';
                document.getElementById('modalTitle').innerText = 'Nuevo Evento';
                document.getElementById('eventForm').reset();
                document.getElementById('deleteBtn').style.display = 'none';
                document.querySelectorAll('input[name="involved_areas"]').forEach(cb => cb.checked = false);
                quill.setContents([]);
                document.getElementById('eventId').value = '';
                
                if (start) {
                    const startDate = new Date(start);
                    datePicker.setDate(startDate);
                    
                    if (start.includes('T')) {
                        document.getElementById('all_day').checked = false;
                        document.getElementById('time_pickers').style.visibility = 'visible';
                        startTimePicker.setDate(startDate);
                        if (end) {
                            endTimePicker.setDate(new Date(end));
                        }
                    } else {
                        document.getElementById('all_day').checked = true;
                        document.getElementById('time_pickers').style.visibility = 'hidden';
                    }
                } else {
                    datePicker.setDate(new Date());
                    document.getElementById('all_day').checked = false;
                    document.getElementById('time_pickers').style.visibility = 'visible';
                }
                updateHiddenDates();
            };

            window.closeModal = function() {
        document.getElementById('eventModal').style.display = 'none';
    };

    window.importFromICS = function(input) {
        if (!input.files || !input.files[0]) return;

        const formData = new FormData();
        formData.append('ics_file', input.files[0]);

        Swal.fire({
            title: 'Importando Archivo...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('{{ route('events.import.ics') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            input.value = ''; // Reset input
            if (data.success) {
                 Swal.fire('¡Éxito!', `Se han importado ${data.count} eventos.`, 'success')
                 .then(() => location.reload());
            } else {
                Swal.fire('Error', data.error, 'error');
            }
        })
        .catch(error => {
            input.value = '';
            Swal.fire('Error', 'Ocurrió un error al procesar el archivo.', 'error');
        });
    };

    window.importFromGoogle = function() {
        Swal.fire({
            title: '¿Importar desde Google?',
            text: "Se buscarán los eventos cargados en tu Google Calendar configurado.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4285F4',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, importar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Importando...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('{{ route('events.import') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        calendar.refetchEvents();
                        Swal.fire('¡Éxito!', `Se han importado ${data.count} eventos.`, 'success');
                    } else {
                        Swal.fire('Error', data.error, 'error');
                    }
                });
            }
        });
    };

            window.editEvent = function (event) {
                openModal();
                document.getElementById('modalTitle').innerText = 'Editar Evento';
                document.getElementById('eventId').value = event.id;
                document.getElementById('deleteBtn').style.display = 'block';
                document.getElementById('title').value = event.title;
                
                const startDate = event.start;
                const endDate = event.end || event.start;
                
                datePicker.setDate(startDate);
                document.getElementById('all_day').checked = event.allDay;
                
                if (event.allDay) {
                    document.getElementById('time_pickers').style.visibility = 'hidden';
                } else {
                    document.getElementById('time_pickers').style.visibility = 'visible';
                    startTimePicker.setDate(startDate);
                    endTimePicker.setDate(endDate);
                }

                updateHiddenDates();
                
                document.getElementById('meet_link').value = event.extendedProps.meet_link || '';
                if (event.extendedProps.description) {
                    quill.clipboard.dangerouslyPasteHTML(event.extendedProps.description);
                } else {
                    quill.setContents([]);
                }
                // We keep the calculated color for display, even if it's read-only for new ones
                document.getElementById('color_preview').style.backgroundColor = event.backgroundColor;
                document.getElementById('is_public').checked = event.extendedProps.is_public;
                
                // Set involved areas
                const involvedAreas = event.extendedProps.involved_areas || [];
                document.querySelectorAll('input[name="involved_areas"]').forEach(cb => {
                    cb.checked = involvedAreas.includes(parseInt(cb.value));
                });
            };

            function formatDateTime(date) {
                if (!date) return '';
                return new Date(date.getTime() - (date.getTimezoneOffset() * 60000)).toISOString().substring(0, 16);
            }

            document.getElementById('eventForm').onsubmit = function (e) {
                e.preventDefault();
                const id = document.getElementById('eventId').value;
                const url = id ? `{{ url('events') }}/${id}` : '{{ route('events.store') }}';
                const method = id ? 'PUT' : 'POST';

                fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                        body: JSON.stringify({
                        title: document.getElementById('title').value,
                        start: document.getElementById('start').value,
                        end: document.getElementById('end').value,
                        meet_link: document.getElementById('meet_link').value,
                        description: quill.getText().trim() === '' ? '' : quill.root.innerHTML,
                        area_id: document.getElementById('area_id').value,
                        color: document.getElementById('color').value,
                        is_public: document.getElementById('is_public').checked,
                        all_day: document.getElementById('all_day').checked,
                        involved_areas: Array.from(document.querySelectorAll('input[name="involved_areas"]:checked')).map(cb => cb.value)
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            calendar.refetchEvents();
                            closeModal();
                            Swal.fire('Éxito', 'Evento guardado correctamente', 'success');
                        } else {
                            Swal.fire('Error', data.error || 'Algo salió mal', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Error de conexión o de servidor. No se pudo guardar el evento.', 'error');
                    });
            };

            window.deleteEvent = function () {
                const id = document.getElementById('eventId').value;
                if (!id) return;

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`{{ url('/events') }}/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                closeModal();
                                calendar.refetchEvents();
                                Swal.fire('Eliminado', 'El evento ha sido eliminado.', 'success');
                            } else {
                                Swal.fire('Error', data.error || 'No se pudo eliminar el evento.', 'error');
                            }
                        });
                    }
                });
            };

            function updateEventResizeDrop(event) {
                fetch(`{{ url('events') }}/${event.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        title: event.title,
                        start: formatDateTime(event.start),
                        end: formatDateTime(event.end),
                        color: event.backgroundColor
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            Swal.fire('Error', 'No tienes permisos para modificar este evento', 'error');
                            calendar.refetchEvents();
                        }
                    });
            }
        });
    </script>

    <style>
        .fc {
            font-family: inherit;
        }

        .fc-header-toolbar {
            margin-bottom: 2rem !important;
        }

        .fc-button {
            background: #f1f5f9 !important;
            border: 1px solid #e2e8f0 !important;
            color: #475569 !important;
            font-weight: 600 !important;
            text-transform: capitalize !important;
            padding: 0.5rem 1rem !important;
            box-shadow: none !important;
        }

        .fc-button-active {
            background: var(--primary) !important;
            color: white !important;
            border-color: var(--primary) !important;
        }

        .fc-event {
            cursor: pointer;
            border: none !important;
            padding: 2px 4px !important;
            border-radius: 4px !important;
        }

        .fc-day-today {
            background: rgba(79, 70, 229, 0.04) !important;
        }

        .ql-toolbar.ql-snow {
            background: #f1f5f9;
            border-color: #e2e8f0;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }

        .ql-container.ql-snow {
            border-color: #e2e8f0;
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }

        /* Flatpickr Customization */
        .flatpickr-calendar {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.75rem !important;
        }

        .flatpickr-day.selected {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
        }

        #start_time:hover, #end_time:hover, #event_date:hover {
            background-color: #e2e8f0 !important;
        }

        #start_time:focus, #end_time:focus, #event_date:focus {
            background-color: #cbd5e1 !important;
        }
    </style>
@endsection