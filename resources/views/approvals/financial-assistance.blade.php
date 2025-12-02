@extends('layouts.app')

@section('title', 'Financial Assistance')
@section('page-title', 'Financial Assistance Requests')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
    <div class="page-content">

        <div class="filters">
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Search requests...">
                <button class="btn btn-search"><i class="bi bi-search"></i> Search</button>
                <button class="btn btn-primary" style="margin-left: 10px;">
                    <i class="bi bi-plus-circle"></i> New Request
                </button>
            </div>
            <div class="filter-options">
                <select class="filter-select">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="released">Released</option>
                </select>
                <input type="date" class="filter-select" placeholder="Date">
            </div>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Patient Name</th>
                        <th>Type of Assistance</th>
                        <th>Amount</th>
                        <th>Date Requested</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" style="text-align: center;">No financial assistance requests found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
@endsection