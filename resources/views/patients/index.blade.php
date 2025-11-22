{{-- Patient List Page: Displays all registered patients --}}
@extends('layouts.app')

@section('title', 'Patient List')
@section('page-title', 'Patient List')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
    <div class="page-content">

        <!-- Header with Add Button -->
        <div class="content-header">
            <h2>All Patients</h2>
            <a href="{{ route('patients.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Add New Patient
            </a>
        </div>

        <!-- Search and Filter Section -->
        <div class="filters">
            <div class="search-box">
                <input type="text" placeholder="Search patients..." class="search-input">
                <button class="btn-search"><i class="bi bi-search"></i> Search</button>
            </div>

            <div class="filter-options">
                <select class="filter-select">
                    <option value="">All Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>

                <select class="filter-select">
                    <option value="">All Ages</option>
                    <option value="child">Children (0-12)</option>
                    <option value="teen">Teenagers (13-19)</option>
                    <option value="adult">Adults (20-59)</option>
                    <option value="senior">Senior (60+)</option>
                </select>
            </div>
        </div>

        <!-- Patients Table -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date Registered</th>
                        <th>Patient No.</th>
                        <th>Name</th>
                        <th>Sex</th>
                        <th>Birthday</th>
                        <th>Address</th>
                        <th>Contact Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Sample Patient Row -->
                    <tr>
                        <td>Nov 20, 2025</td>
                        <td>P-001</td>
                        <td>Santos, Maria A.</td>
                        <td>F</td>
                        <td>Jan 15, 1997</td>
                        <td>Purok 1, Brgy Sto. Niño</td>
                        <td>0912-345-6789</td>
                        <td class="actions">
                            <a href="#" class="btn-action btn-view">View</a>
                        </td>
                    </tr>

                    <tr>
                        <td>Nov 18, 2025</td>
                        <td>P-002</td>
                        <td>Dela Cruz, Juan B.</td>
                        <td>M</td>
                        <td>Mar 22, 1980</td>
                        <td>Purok 2, Brgy Sto. Niño</td>
                        <td>0923-456-7890</td>
                        <td class="actions">
                            <a href="#" class="btn-action btn-view">View</a>
                        </td>
                    </tr>

                    <tr>
                        <td>Nov 15, 2025</td>
                        <td>P-003</td>
                        <td>Reyes, Ana C.</td>
                        <td>F</td>
                        <td>Jul 08, 1993</td>
                        <td>Purok 3, Brgy Sto. Niño</td>
                        <td>0934-567-8901</td>
                        <td class="actions">
                            <a href="#" class="btn-action btn-view">View</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <button class="btn-page">« Previous</button>
            <span class="page-info">Page 1 of 10</span>
            <button class="btn-page">Next »</button>
        </div>

    </div>
@endsection