{{-- View User Details --}}
@extends('layouts.app')

@section('title', 'User Details')
@section('page-title', 'User Details')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
@endpush

@section('content')
    <div class="page-content">

        <!-- Detail Container -->
        <div class="detail-container">
            <div class="detail-header">
                <div>
                    <h2 class="detail-title">User Account Information</h2>
                    <p class="detail-subtitle">Complete details of the user account</p>
                </div>
                <div class="detail-actions">
                    <a href="{{ route('users.all-users') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Edit User
                    </a>
                </div>
            </div>

            <!-- Account Information Section -->
            <div class="detail-section">
                <h3 class="section-header"><span class="section-indicator"></span>Account Information</h3>

                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Username</label>
                        <p>{{ $user->username }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Email Address</label>
                        <p>{{ $user->email }}</p>
                    </div>
                    <div class="detail-item">
                        <label>User Role</label>
                        <p>
                            @php
                                $roleBadges = [
                                    'super_admin' => ['label' => 'Super Admin', 'class' => 'badge-super-admin'],
                                    'admin' => ['label' => 'Admin', 'class' => 'badge-admin'],
                                    'bhw' => ['label' => 'BHW', 'class' => 'badge-bhw'],
                                ];
                                $roleMeta = $roleBadges[$user->role] ?? ['label' => ucfirst($user->role), 'class' => 'badge-admin'];
                            @endphp
                            <span class="badge {{ $roleMeta['class'] }}">{{ $roleMeta['label'] }}</span>
                        </p>
                    </div>
                    <div class="detail-item">
                        <label>Account Status</label>
                        <p>
                            @if($user->status === 'active')
                                <span class="status-badge status-active">Active</span>
                            @else
                                <span class="status-badge status-inactive">Inactive</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="detail-section">
                <h3 class="section-header"><span class="section-indicator"></span>Personal Information</h3>

                <div class="detail-grid">
                    <div class="detail-item">
                        <label>First Name</label>
                        <p>{{ $user->first_name ?? '—' }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Middle Name</label>
                        <p>{{ $user->middle_name ?? '—' }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Last Name</label>
                        <p>{{ $user->last_name ?? '—' }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Full Name</label>
                        <p>{{ $user->name }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Contact Number</label>
                        <p>{{ $user->contact_number ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Activity Information Section -->
            <div class="detail-section">
                <h3 class="section-header"><span class="section-indicator"></span>Activity Information</h3>

                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Account Created</label>
                        <p>{{ optional($user->created_at)->format('F d, Y \a\t g:i A') ?? '—' }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Last Updated</label>
                        <p>{{ optional($user->updated_at)->format('F d, Y \a\t g:i A') ?? '—' }}</p>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection