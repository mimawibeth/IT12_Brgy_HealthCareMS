@extends('layouts.app')

@section('title', 'Event Calendar')
@section('page-title', 'Event Calendar')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css?v=' . time()) }}">
    <link rel="stylesheet" href="{{ asset('css/events.css?v=' . time()) }}">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="page-content">
        @php
            $user = auth()->user();
            $canManage = in_array($user->role ?? '', ['super_admin', 'admin']);
        @endphp

        <div class="events-header" style="display: flex; justify-content: flex-end; margin-bottom: 1.5rem;">
            @if($canManage)
                <div class="events-header-actions">
                    <a href="{{ route('events.create') }}" class="btn btn-primary btn-add-event"
                        style="padding: 10px 15px !important; font-size: 14px; font-weight: normal;">
                        <i class="bi bi-plus-circle"></i> Add New Event
                    </a>
                </div>
            @endif
        </div>

        <div class="calendar-wrapper">
            <div id="calendar"></div>
        </div>

        <!-- Event Details Modal -->
        <div id="eventModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="modalTitle">Event Details</h3>
                    <span class="close" id="closeModal">&times;</span>
                </div>
                <div class="modal-body">
                    <div id="eventDetails"></div>
                    <div id="eventActions" class="modal-actions" style="display: none; margin-top: 1rem; gap: 0.5rem;">
                        <a id="editEventBtn" href="#" class="btn btn-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <button id="deleteEventBtn" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const canManage = {{ $canManage ? 'true' : 'false' }};
            let currentEventId = null;

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: '{{ route("events.api") }}',
                eventClick: function (info) {
                    currentEventId = info.event.id;
                    showEventDetails(info.event);
                },
                dateClick: function (info) {
                    if (canManage) {
                        window.location.href = '{{ route("events.create") }}?date=' + info.dateStr;
                    }
                },
                eventDisplay: 'block',
                height: 'auto',
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: 'short'
                }
            });

            calendar.render();

            function showEventDetails(event) {
                const modal = document.getElementById('eventModal');
                const modalTitle = document.getElementById('modalTitle');
                const eventDetails = document.getElementById('eventDetails');
                const eventActions = document.getElementById('eventActions');
                const editBtn = document.getElementById('editEventBtn');
                const deleteBtn = document.getElementById('deleteEventBtn');

                modalTitle.textContent = event.title;

                let detailsHtml = '<div class="event-detail-item">';
                detailsHtml += '<strong><i class="bi bi-calendar"></i> Date:</strong> ';
                detailsHtml += event.start.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                detailsHtml += '</div>';

                if (event.extendedProps.start_time) {
                    detailsHtml += '<div class="event-detail-item">';
                    detailsHtml += '<strong><i class="bi bi-clock"></i> Time:</strong> ';
                    detailsHtml += event.extendedProps.start_time;
                    if (event.extendedProps.end_time) {
                        detailsHtml += ' - ' + event.extendedProps.end_time;
                    }
                    detailsHtml += '</div>';
                }

                if (event.extendedProps.location) {
                    detailsHtml += '<div class="event-detail-item">';
                    detailsHtml += '<strong><i class="bi bi-geo-alt"></i> Location:</strong> ';
                    detailsHtml += event.extendedProps.location;
                    detailsHtml += '</div>';
                }

                if (event.extendedProps.description) {
                    detailsHtml += '<div class="event-detail-item">';
                    detailsHtml += '<strong><i class="bi bi-file-text"></i> Description:</strong> ';
                    detailsHtml += '<p>' + (event.extendedProps.description || 'No description') + '</p>';
                    detailsHtml += '</div>';
                }

                detailsHtml += '<div class="event-detail-item">';
                detailsHtml += '<strong><i class="bi bi-person"></i> Created by:</strong> ';
                detailsHtml += event.extendedProps.created_by || 'Unknown';
                detailsHtml += '</div>';

                eventDetails.innerHTML = detailsHtml;

                if (canManage) {
                    eventActions.style.display = 'flex';
                    editBtn.href = '{{ url("events") }}/' + currentEventId + '/edit';
                    deleteBtn.onclick = function () {
                        if (confirm('Are you sure you want to delete this event?')) {
                            deleteEvent(currentEventId);
                        }
                    };
                } else {
                    eventActions.style.display = 'none';
                }

                modal.style.display = 'block';
            }

            function deleteEvent(eventId) {
                fetch('{{ url("events") }}/' + eventId, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                        return response.text().then(text => {
                            throw new Error(text || 'Network response was not ok');
                        });
                    })
                    .then(data => {
                        calendar.refetchEvents();
                        document.getElementById('eventModal').style.display = 'none';
                        // Show success message
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-success';
                        alert.textContent = data.message || 'Event deleted successfully.';
                        alert.style.marginBottom = '1rem';
                        document.querySelector('.page-content').insertBefore(alert, document.querySelector('.calendar-wrapper'));
                        setTimeout(() => alert.remove(), 3000);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting event. Please try again.');
                    });
            }

            // Close modal
            document.getElementById('closeModal').onclick = function () {
                document.getElementById('eventModal').style.display = 'none';
            };

            window.onclick = function (event) {
                const modal = document.getElementById('eventModal');
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            };
        });
    </script>
@endpush