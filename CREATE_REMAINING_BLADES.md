# Create Remaining Blade Files

Copy and paste these into the respective files in `resources/views/`

---

## 1. Create: `resources/views/consultations.blade.php`

Copy ALL of the content below (from @extends to @endpush):

```blade
@extends('layouts.app')

@section('title', 'Consultations & Appointments - Barangay Healthcare System')
@section('page-title', 'Consultations & Appointments')
@section('search-placeholder', 'Search appointments...')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="page-header-top">
        <div>
            <h1 class="page-title">Consultations & Appointments</h1>
            <p class="page-description">Manage patient consultations and appointment schedules</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-primary" onclick="openAddAppointmentModal()">
                <span>➕</span>
                Schedule Appointment
            </button>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-4" style="margin-bottom: 30px;">
    <div class="stat-card">
        <div class="stat-label">Today's Appointments</div>
        <div class="stat-value" id="todayCount">0</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending</div>
        <div class="stat-value" style="color: var(--warning-color);" id="pendingCount">0</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Completed</div>
        <div class="stat-value" style="color: var(--success-color);" id="completedCount">0</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Cancelled</div>
        <div class="stat-value" style="color: var(--danger-color);" id="cancelledCount">0</div>
    </div>
</div>

<!-- Tabs -->
<div class="tabs">
    <button class="tab-btn active" onclick="switchTab('all')">All Appointments</button>
    <button class="tab-btn" onclick="switchTab('today')">Today</button>
    <button class="tab-btn" onclick="switchTab('upcoming')">Upcoming</button>
    <button class="tab-btn" onclick="switchTab('completed')">Completed</button>
</div>

<!-- Appointments Table -->
<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Patient Name</th>
                        <th>Date & Time</th>
                        <th>Doctor</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="appointmentsTableBody">
                    <!-- Data will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('modals')
<!-- Add/Edit Appointment Modal -->
<div id="appointmentModal" class="modal-overlay hidden">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Schedule New Appointment</h3>
            <button class="modal-close" onclick="closeAppointmentModal()">×</button>
        </div>
        <div class="modal-body">
            <form id="appointmentForm">
                @csrf
                <div class="form-group">
                    <label class="form-label">Patient *</label>
                    <select class="form-select" id="patientId" required>
                        <option value="">Select patient</option>
                        <option value="RES-001">Juan Dela Cruz</option>
                        <option value="RES-002">Maria Santos</option>
                        <option value="RES-003">Pedro Garcia</option>
                        <option value="RES-004">Ana Reyes</option>
                    </select>
                </div>
                
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Appointment Date *</label>
                        <input type="date" class="form-input" id="appointmentDate" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Appointment Time *</label>
                        <input type="time" class="form-input" id="appointmentTime" required>
                    </div>
                </div>
                
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Doctor/Healthcare Worker *</label>
                        <select class="form-select" id="doctor" required>
                            <option value="">Select doctor</option>
                            <option value="Dr. Santos">Dr. Santos</option>
                            <option value="Dr. Reyes">Dr. Reyes</option>
                            <option value="Dr. Cruz">Dr. Cruz</option>
                            <option value="Nurse Garcia">Nurse Garcia</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Consultation Type *</label>
                        <select class="form-select" id="consultationType" required>
                            <option value="">Select type</option>
                            <option value="General Checkup">General Checkup</option>
                            <option value="Follow-up">Follow-up</option>
                            <option value="Prenatal">Prenatal</option>
                            <option value="Vaccination">Vaccination</option>
                            <option value="Emergency">Emergency</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Chief Complaint *</label>
                    <textarea class="form-textarea" id="complaint" rows="2" required></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea class="form-textarea" id="notes" rows="2"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select class="form-select" id="status" required>
                        <option value="scheduled">Scheduled</option>
                        <option value="in-progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeAppointmentModal()">Cancel</button>
            <button class="btn btn-primary" onclick="saveAppointment()">Save Appointment</button>
        </div>
    </div>
</div>

<!-- View Appointment Details Modal -->
<div id="viewModal" class="modal-overlay hidden">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Appointment Details</h3>
            <button class="modal-close" onclick="closeViewModal()">×</button>
        </div>
        <div class="modal-body" id="viewModalContent">
            <!-- Content will be populated by JavaScript -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeViewModal()">Close</button>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script src="{{ asset('js/consultations.js') }}"></script>
@endpush
```

---

## QUICK COPY-PASTE INSTRUCTIONS

For the remaining 3 files (inventory, statistics, audit-logs):

### Simple Method:
1. Open the HTML file in `public/` (e.g., `inventory.html`)
2. Copy everything between `<div class="content-area">` and `</main>`
3. Remove the `<div class="content-area">` tags themselves
4. Create the blade file with this structure:

```blade
@extends('layouts.app')
@section('title', 'Page Title')
@section('page-title', 'Header Title')
@section('search-placeholder', 'Search...')

@section('content')
    [PASTE CONTENT HERE]
@endsection

@push('modals')
    [PASTE MODALS HERE IF ANY]
@endpush

@push('scripts')
<script src="{{ asset('js/filename.js') }}"></script>
@endpush
```

That's it! The HTML will work because your CSS and JS are already in public/.

---

## OR Use PHP Artisan Command

```bash
# Create blade files quickly
touch resources/views/consultations.blade.php
touch resources/views/inventory.blade.php
touch resources/views/statistics.blade.php
touch resources/views/audit-logs.blade.php
```

Then copy-paste following the structure above.

---

## ✅ After Creating All Files

Test your pages:
- http://localhost:8000/login
- http://localhost:8000/dashboard
- http://localhost:8000/residents
- http://localhost:8000/consultations
- http://localhost:8000/inventory
- http://localhost:8000/statistics
- http://localhost:8000/audit-logs

All your JavaScript and CSS will work automatically because they're in the `public/` folder!
