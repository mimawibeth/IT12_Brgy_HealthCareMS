{{-- Dashboard Page: Shows overview and statistics --}}
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css?v=' . time()) }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css?v=' . time()) }}">
@endpush

@section('content')
    <div class="page-content">

        <!-- Weekly Statistics -->
        <div class="stats-grid">
            <!-- Patients Registered Weekly -->
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-blue">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <div class="stat-text">
                    <h3 class="stat-title">Patients Registered (Weekly)</h3>
                    <p class="stat-number">{{ number_format($patientsRegisteredWeekly ?? 0) }}</p>
                    <span class="stat-trend">Total: {{ number_format($totalPatients ?? 0) }}</span>
                </div>
            </div>

            <!-- Prenatal Records Weekly -->
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-red">
                    <i class="bi bi-heart-pulse-fill"></i>
                </div>
                <div class="stat-text">
                    <h3 class="stat-title">Prenatal Records (Weekly)</h3>
                    <p class="stat-number">{{ number_format($prenatalRecordsWeekly ?? 0) }}</p>
                    <span class="stat-trend">Total: {{ number_format($totalPrenatalRecords ?? 0) }}</span>
                </div>
            </div>

            <!-- FP Records Weekly -->
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-purple">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-text">
                    <h3 class="stat-title">Family Planning (Weekly)</h3>
                    <p class="stat-number">{{ number_format($fpRecordsWeekly ?? 0) }}</p>
                    <span class="stat-trend">Total: {{ number_format($totalFPRecords ?? 0) }}</span>
                </div>
            </div>

            <!-- Immunization Records Weekly -->
            <div class="stat-card">
                <div class="stat-icon-wrapper bg-green">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="stat-text">
                    <h3 class="stat-title">Immunization (Weekly)</h3>
                    <p class="stat-number">{{ number_format($immunizationRecordsWeekly ?? 0) }}</p>
                    <span class="stat-trend">Total: {{ number_format($totalImmunizationRecords ?? 0) }}</span>
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="monthly-stats">
            <div class="section-header-inline">
                <h2>Upcoming Events</h2>
                <p>Events scheduled this month</p>
            </div>

            @if(count($eventsThisMonth ?? []) > 0)
                <div class="activity-list">
                    @foreach($eventsThisMonth as $event)
                        <div class="activity-item">
                            <span class="activity-icon icon-purple">
                                <i class="bi bi-calendar-event"></i>
                            </span>
                            <div class="activity-details">
                                <p class="activity-text">{{ $event->title ?? 'Event' }}</p>
                                <span class="activity-time">{{ $event->start_date?->format('M d, Y') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="no-data-message">No events scheduled this month</p>
            @endif
        </div>
    </div>
@endsection