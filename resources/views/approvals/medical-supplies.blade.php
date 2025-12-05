@extends('layouts.app')

@section('title', 'Medical Supplies Requests')
@section('page-title', 'Medical Supplies Requests')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/patients.css') }}">
    <link rel="stylesheet" href="{{ asset('css/approvals.css') }}">

    <div class="page-content">
        <div class="page-header">
            <h1>Medical Supplies Requests</h1>
            @if(auth()->user()->role !== 'super_admin')
                <button type="button" class="btn-action btn-edit" id="openRequestModal">
                    <i class="bi bi-plus-circle"></i> New Request
                </button>
            @endif
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Filters -->
        <div class="filters-container" style="background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <form method="GET" action="{{ route('approvals.medical.index') }}" class="filters-form">
                <div class="filter-row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved_by_admin" {{ request('status') == 'approved_by_admin' ? 'selected' : '' }}>Approved by Admin</option>
                            <option value="rejected_by_admin" {{ request('status') == 'rejected_by_admin' ? 'selected' : '' }}>Rejected by Admin</option>
                            <option value="approved_by_superadmin" {{ request('status') == 'approved_by_superadmin' ? 'selected' : '' }}>Approved by Superadmin</option>
                            <option value="rejected_by_superadmin" {{ request('status') == 'rejected_by_superadmin' ? 'selected' : '' }}>Rejected by Superadmin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="item_name">Item Name</label>
                        <input type="text" name="item_name" id="item_name" class="form-control" placeholder="Search item..." value="{{ request('item_name') }}">
                    </div>
                    <div class="form-group">
                        <label for="date_from">Date From</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="form-group">
                        <label for="date_to">Date To</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="filter-actions" style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Apply Filters
                    </button>
                    <a href="{{ route('approvals.medical.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Request Form Modal -->
        <div class="modal" id="requestModal" style="display: none;">
            <div class="modal-content" style="max-width: 600px;">
                <div class="form-container">
                    <div class="form-header">
                        <h2>Submit New Request</h2>
                        <p class="text-muted">Fill in the details below to request medical supplies</p>
                    </div>

                    <form action="{{ route('approvals.medical.store') }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label for="item_name" class="form-label">Item Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="item_name" id="item_name"
                                    class="form-control @error('item_name') is-invalid @enderror"
                                    placeholder="e.g., First Aid Kit, Antiseptic Solution..." required
                                    value="{{ old('item_name') }}">
                                @error('item_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" id="quantity"
                                    class="form-control @error('quantity') is-invalid @enderror" min="1"
                                    placeholder="Number of units" required value="{{ old('quantity') }}">
                                @error('quantity')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror"
                                rows="4" placeholder="Describe the reason for requesting these medical supplies..."
                                required>{{ old('reason') }}</textarea>
                            @error('reason')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Additional Details</label>
                            <textarea name="description" id="description"
                                class="form-control @error('description') is-invalid @enderror" rows="3"
                                placeholder="Optional: Specifications, urgency, etc...">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-action btn-edit">
                                <i class="bi bi-send"></i> Submit Request
                            </button>
                            <button type="button" class="btn-action btn-delete" id="cancelModal">
                                <i class="bi bi-x-circle"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Requests Table -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Requested By</th>
                        <th>Date Requested</th>
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
                            <td>{{ $request->item_name }}</td>
                            <td>{{ $request->quantity }}</td>
                            <td>{{ $request->user->name }}</td>
                            <td>{{ $request->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($request->approved_by_admin === null)
                                    <span class="status-badge badge-pending">Pending</span>
                                @elseif($request->approved_by_admin === 1)
                                    <span class="status-badge badge-approved">Approved</span>
                                @else
                                    <span class="status-badge badge-rejected">Rejected</span>
                                @endif
                            </td>
                            <td>
                                @if($request->approved_by_superadmin === null)
                                    <span class="status-badge badge-pending">Pending</span>
                                @elseif($request->approved_by_superadmin === 1)
                                    <span class="status-badge badge-approved">Approved</span>
                                @else
                                    <span class="status-badge badge-rejected">Rejected</span>
                                @endif
                            </td>
                            @if(auth()->user()->role === 'admin')
                                <td class="actions">
                                    <button class="btn-action btn-view btn-approve" data-id="{{ $request->id }}" data-type="medical"
                                        title="Approve & Forward" {{ $request->approved_by_admin !== null ? 'disabled' : '' }}>
                                        <i class="bi bi-check-circle"></i> Approve
                                    </button>
                                    <button class="btn-action btn-delete btn-reject" data-id="{{ $request->id }}"
                                        data-type="medical" title="Reject" {{ $request->approved_by_admin !== null ? 'disabled' : '' }}>
                                        <i class="bi bi-x-circle"></i> Reject
                                    </button>
                                </td>
                            @elseif(auth()->user()->role === 'super_admin')
                                <td class="actions">
                                    <button class="btn-action btn-view superadmin-approve-btn" data-id="{{ $request->id }}"
                                        title="Final Approve" {{ ($request->approved_by_admin !== 1 || $request->approved_by_superadmin !== null) ? 'disabled' : '' }}>
                                        <i class="bi bi-check-circle"></i> Approve
                                    </button>
                                    <button class="btn-action btn-delete superadmin-reject-btn" data-id="{{ $request->id }}"
                                        title="Reject" {{ ($request->approved_by_admin !== 1 || $request->approved_by_superadmin !== null) ? 'disabled' : '' }}>
                                        <i class="bi bi-x-circle"></i> Reject
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'admin' ? '8' : '7' }}" style="text-align: center;">No
                                requests found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('requestModal');
            const openBtn = document.getElementById('openRequestModal');
            const cancelBtn = document.getElementById('cancelModal');

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

            // Approve/Reject functionality
            document.querySelectorAll('.btn-approve').forEach(button => {
                button.addEventListener('click', function () {
                    const requestId = this.dataset.id;

                    if (confirm('Forward this request to superadmin for approval?')) {
                        fetch(`/approvals/medical/${requestId}/admin-approve`, {
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

            document.querySelectorAll('.btn-reject').forEach(button => {
                button.addEventListener('click', function () {
                    const requestId = this.dataset.id;

                    if (confirm('Reject this request? This action cannot be undone.')) {
                        const notes = prompt('Reason for rejection (optional):');
                        fetch(`/approvals/medical/${requestId}/admin-reject`, {
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

            // Superadmin action buttons
            document.querySelectorAll('.superadmin-approve-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const requestId = this.dataset.id;

                    if (confirm('Give final approval to this request?')) {
                        fetch(`/approvals/medical/${requestId}/superadmin-approve`, {
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

            document.querySelectorAll('.superadmin-reject-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const requestId = this.dataset.id;

                    if (confirm('Reject this request? This action cannot be undone.')) {
                        const notes = prompt('Reason for rejection (optional):');
                        fetch(`/approvals/medical/${requestId}/superadmin-reject`, {
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
        });
    </script>

    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-header {
            margin-bottom: 1.5rem;
        }

        .form-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
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
            border-color: #2f6d7e;
            box-shadow: 0 0 0 3px rgba(47, 109, 126, 0.1);
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
            margin-top: 1.5rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .text-muted {
            color: #6c757d;
        }
    </style>
@endsection