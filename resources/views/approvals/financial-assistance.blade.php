@extends('layouts.app')

@section('title', 'Financial Assistance Requests')
@section('page-title', 'Financial Assistance Requests')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
    <link rel="stylesheet" href="{{ asset('css/approvals.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <!-- Header Actions -->
        <div class="content-header"
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div></div>
            @if(auth()->user()->role !== 'super_admin')
                <div class="header-actions">
                    <button class="btn btn-primary" id="openRequestModal">
                        <i class="bi bi-plus-circle"></i> New Request
                    </button>
                </div>
            @endif
        </div>

        @if(session('success'))
            <div class="alert alert-success"
                style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #28a745;">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="filters-container" style="background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <form method="GET" action="{{ route('approvals.financial.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
                <div style="margin-bottom: 0;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #333;">Status</label>
                    <select name="status" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; background: white;">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved_by_admin" {{ request('status') == 'approved_by_admin' ? 'selected' : '' }}>Approved by Admin</option>
                        <option value="rejected_by_admin" {{ request('status') == 'rejected_by_admin' ? 'selected' : '' }}>Rejected by Admin</option>
                        <option value="approved_by_superadmin" {{ request('status') == 'approved_by_superadmin' ? 'selected' : '' }}>Approved by Superadmin</option>
                        <option value="rejected_by_superadmin" {{ request('status') == 'rejected_by_superadmin' ? 'selected' : '' }}>Rejected by Superadmin</option>
                    </select>
                </div>
                <div style="margin-bottom: 0;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #333;">Type</label>
                    <select name="type" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; background: white;">
                        <option value="">All Types</option>
                        <option value="Emergency" {{ request('type') == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                        <option value="Medical" {{ request('type') == 'Medical' ? 'selected' : '' }}>Medical</option>
                        <option value="Educational" {{ request('type') == 'Educational' ? 'selected' : '' }}>Educational</option>
                        <option value="Livelihood" {{ request('type') == 'Livelihood' ? 'selected' : '' }}>Livelihood</option>
                        <option value="Housing" {{ request('type') == 'Housing' ? 'selected' : '' }}>Housing</option>
                    </select>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" style="padding: 0.5rem 1.5rem; background: #2f6d7e; color: white; border: none; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 500;">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('approvals.financial.index') }}" style="padding: 0.5rem 1.5rem; background: #6c757d; color: white; border: none; border-radius: 4px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 500;">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Financial Assistance Requests Table -->
        <div class="table-container" id="requestsTable">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Reason</th>
                        <th>Requested By</th>
                        <th>Submitted</th>
                        <th>Admin Status</th>
                        <th>Superadmin Status</th>
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr>
                            <td>{{ $request->id }}</td>
                            <td>{{ $request->type }}</td>
                            <td>₱{{ number_format($request->amount, 2) }}</td>
                            <td>{{ Str::limit($request->reason, 50) }}</td>
                            <td>{{ $request->user->name ?? 'N/A' }}</td>
                            <td>{{ $request->submitted_at->format('M d, Y') }}</td>
                            <td>
                                @if($request->status === 'pending')
                                    <span class="status-badge badge-pending">Pending</span>
                                @elseif($request->status === 'approved_by_admin')
                                    <span class="status-badge badge-approved">Approved</span>
                                @elseif($request->status === 'rejected_by_admin')
                                    <span class="status-badge badge-rejected">Rejected</span>
                                @else
                                    <span class="status-badge badge-pending">{{ ucfirst(str_replace('_', ' ', $request->status)) }}</span>
                                @endif
                            </td>
                            <td>
                                @if(in_array($request->status, ['approved_by_superadmin', 'rejected_by_superadmin']))
                                    @if($request->status === 'approved_by_superadmin')
                                        <span class="status-badge badge-approved">Approved</span>
                                    @else
                                        <span class="status-badge badge-rejected">Rejected</span>
                                    @endif
                                @elseif($request->status === 'approved_by_admin')
                                    <span class="status-badge badge-pending">Pending</span>
                                @else
                                    <span class="status-badge" style="background: #f5f5f5; color: #9e9e9e;">N/A</span>
                                @endif
                            </td>
                            @if(auth()->user()->role === 'admin')
                                <td class="actions">
                                    @if($request->isPending())
                                        <button class="btn-action btn-view approve-btn" data-id="{{ $request->id }}"
                                            title="Approve & Forward">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                        <button class="btn-action btn-delete reject-btn" data-id="{{ $request->id }}" title="Reject">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @else
                                        <span style="color: #7f8c8d; font-size: 0.875rem;">Reviewed</span>
                                    @endif
                                </td>
                            @elseif(auth()->user()->role === 'super_admin')
                                <td class="actions">
                                    @if($request->isAwaitingSuperadminReview())
                                        <button class="btn-action btn-view superadmin-approve-btn" data-id="{{ $request->id }}"
                                            title="Final Approve">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </button>
                                        <button class="btn-action btn-delete superadmin-reject-btn" data-id="{{ $request->id }}"
                                            title="Reject">
                                            <i class="bi bi-x-circle"></i> Reject
                                        </button>
                                    @else
                                        <span style="color: #7f8c8d; font-size: 0.875rem;">Reviewed</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 2rem; color: #7f8c8d;">
                                <i class="bi bi-inbox"
                                    style="font-size: 48px; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                                No financial assistance requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Request Form Modal -->
        <div class="modal" id="requestModal" style="display: none;">
            <div class="modal-content" style="max-width: 600px;">
                <div class="form-header">
                    <h2 class="form-title">Request Financial Assistance</h2>
                    <p class="form-subtitle">Submit a request for financial assistance</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger"
                        style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #dc3545;">
                        <ul class="mb-0" style="margin: 0; padding-left: 1.5rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('approvals.financial.store') }}" method="POST" class="patient-form">
                    @csrf

                    <div class="form-section section-patient-info">
                        <h3 class="section-header">
                            <span class="section-indicator"></span>Request Details
                        </h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="type">Assistance Type <span class="required-asterisk">*</span></label>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="">Select assistance type...</option>
                                    <option value="Emergency">Emergency</option>
                                    <option value="Medical">Medical</option>
                                    <option value="Educational">Educational</option>
                                    <option value="Livelihood">Livelihood</option>
                                    <option value="Housing">Housing</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="amount">Amount (₱) <span class="required-asterisk">*</span></label>
                                <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0"
                                    placeholder="0.00" required value="{{ old('amount') }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="reason">Reason <span class="required-asterisk">*</span></label>
                                <textarea name="reason" id="reason" class="form-control" rows="3"
                                    placeholder="Describe the reason for your assistance request..."
                                    required>{{ old('reason') }}</textarea>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="description">Additional Details</label>
                                <textarea name="description" id="description" class="form-control" rows="3"
                                    placeholder="Optional: Any additional information...">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="form-actions"
                            style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
                            <button type="button" class="btn btn-secondary" id="cancelForm">
                                <i class="bi bi-x-circle"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Submit Request
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('requestModal');
                const openBtn = document.getElementById('openRequestModal');
                const cancelBtn = document.getElementById('cancelForm');

                if (openBtn) {
                    openBtn.addEventListener('click', () => {
                        modal.style.display = 'flex';
                    });
                }

                if (cancelBtn) {
                    cancelBtn.addEventListener('click', () => {
                        modal.style.display = 'none';
                    });
                }

                // Close modal when clicking outside
                window.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.style.display = 'none';
                    }
                });

                // Admin action buttons
                @if(auth()->user()->role === 'admin')
                    document.querySelectorAll('.approve-btn').forEach(btn => {
                        btn.addEventListener('click', function () {
                            const requestId = this.getAttribute('data-id');
                            if (confirm('Forward this request to superadmin for approval?')) {
                                fetch(`/approvals/financial/${requestId}/admin-approve`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({ notes: '' })
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.message) {
                                            alert(data.message);
                                            location.reload();
                                        } else if (data.error) {
                                            alert('Error: ' + data.error);
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert('An error occurred while processing the request.');
                                    });
                            }
                        });
                    });

                    document.querySelectorAll('.reject-btn').forEach(btn => {
                        btn.addEventListener('click', function () {
                            const requestId = this.getAttribute('data-id');
                            if (confirm('Reject this request? This action cannot be undone.')) {
                                const notes = prompt('Reason for rejection (optional):');
                                fetch(`/approvals/financial/${requestId}/admin-reject`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({ notes: notes || '' })
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.message) {
                                            alert(data.message);
                                            location.reload();
                                        } else if (data.error) {
                                            alert('Error: ' + data.error);
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert('An error occurred while processing the request.');
                                    });
                            }
                        });
                    });
                @endif

                // Superadmin action buttons
                @if(auth()->user()->role === 'super_admin')
                    document.querySelectorAll('.superadmin-approve-btn').forEach(btn => {
                        btn.addEventListener('click', function () {
                            const requestId = this.getAttribute('data-id');
                            if (confirm('Give final approval to this request?')) {
                                fetch(`/approvals/financial/${requestId}/superadmin-approve`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({ notes: '' })
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.message) {
                                            alert(data.message);
                                            location.reload();
                                        } else if (data.error) {
                                            alert('Error: ' + data.error);
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert('An error occurred while processing the request.');
                                    });
                            }
                        });
                    });

                    document.querySelectorAll('.superadmin-reject-btn').forEach(btn => {
                        btn.addEventListener('click', function () {
                            const requestId = this.getAttribute('data-id');
                            if (confirm('Reject this request? This action cannot be undone.')) {
                                const notes = prompt('Reason for rejection (optional):');
                                fetch(`/approvals/financial/${requestId}/superadmin-reject`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({ notes: notes || '' })
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.message) {
                                            alert(data.message);
                                            location.reload();
                                        } else if (data.error) {
                                            alert('Error: ' + data.error);
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert('An error occurred while processing the request.');
                                    });
                            }
                        });
                    });
                @endif
                    });
        </script>
    @endpush <style>
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }

        .form-header {
            margin-bottom: 2rem;
        }

        .form-header h1 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            border: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
@endsection