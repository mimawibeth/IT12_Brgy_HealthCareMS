@extends('layouts.app')

@section('title', 'Pending Approvals')
@section('page-title', 'Pending Approvals')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
@endpush

@section('content')
    <div class="page-content">

        <div class="filters">
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Search requests...">
                <button class="btn btn-search"><i class="bi bi-search"></i> Search</button>
            </div>
            <div class="filter-options">
                <select class="filter-select">
                    <option value="">All Types</option>
                    <option value="financial">Financial Assistance</option>
                    <option value="medical">Medical Supplies</option>
                </select>
                <select class="filter-select">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Type</th>
                        <th>Requester</th>
                        <th>Date Submitted</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" style="text-align: center;">No pending approvals found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
@endsection