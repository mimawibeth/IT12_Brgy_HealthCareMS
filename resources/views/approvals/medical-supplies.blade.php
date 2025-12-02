@extends('layouts.app')

@section('title', 'Medical Supply Request')
@section('page-title', 'Medical Supply Requests')

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
                    <option value="fulfilled">Fulfilled</option>
                </select>
                <select class="filter-select">
                    <option value="">Priority</option>
                    <option value="urgent">Urgent</option>
                    <option value="high">High</option>
                    <option value="normal">Normal</option>
                </select>
            </div>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Requested By</th>
                        <th>Date Requested</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="8" style="text-align: center;">No medical supply requests found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
@endsection