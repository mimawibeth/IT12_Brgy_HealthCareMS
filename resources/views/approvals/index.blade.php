@extends('layouts.app')

@section('title', 'Approvals')
@section('page-title', 'Approvals')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/approvals.css') }}">
@endpush

@section('content')
    <div class="page-content">
        <div class="approvals-header">
            <h1>Approvals Management</h1>
            @if(auth()->user()->role === 'bhw')
                <div class="approval-actions">
                    <a href="{{ route('approvals.financial.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Request Financial Assistance
                    </a>
                    <a href="{{ route('approvals.medical.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Request Medical Supplies
                    </a>
                </div>
            @endif
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Financial Assistance Requests Section -->
        <div class="approvals-section">
            <h2>Financial Assistance Requests</h2>
            
            @if($financialRequests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Reason</th>
                                @if(auth()->user()->role === 'bhw')
                                    <th>Submitted</th>
                                    <th>Status</th>
                                @elseif(auth()->user()->role === 'admin')
                                    <th>Submitted By</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                @else
                                    <th>Submitted By</th>
                                    <th>Admin Notes</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($financialRequests as $request)
                                <tr>
                                    <td>#{{ $request->id }}</td>
                                    <td>{{ $request->type }}</td>
                                    <td>₱{{ number_format($request->amount, 2) }}</td>
                                    <td>{{ Str::limit($request->reason, 50) }}</td>
                                    @if(auth()->user()->role === 'bhw')
                                        <td>{{ $request->submitted_at->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge {{ $request->getStatusBadge()['class'] }}">
                                                {{ $request->getStatusBadge()['label'] }}
                                            </span>
                                        </td>
                                    @elseif(auth()->user()->role === 'admin')
                                        <td>{{ $request->requestor->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $request->getStatusBadge()['class'] }}">
                                                {{ $request->getStatusBadge()['label'] }}
                                            </span>
                                        </td>
                                        <td class="admin-actions">
                                            @if($request->isPending())
                                                <button class="btn btn-sm btn-success approve-btn" data-type="financial" data-id="{{ $request->id }}" title="Forward to Superadmin">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger reject-btn" data-type="financial" data-id="{{ $request->id }}" title="Reject">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            @else
                                                <span class="text-muted">Reviewed</span>
                                            @endif
                                        </td>
                                    @else
                                        <td>{{ $request->requestor->name ?? 'N/A' }}</td>
                                        <td>{{ $request->admin_notes ? Str::limit($request->admin_notes, 30) : '—' }}</td>
                                        <td>
                                            <span class="badge {{ $request->getStatusBadge()['class'] }}">
                                                {{ $request->getStatusBadge()['label'] }}
                                            </span>
                                        </td>
                                        <td class="superadmin-actions">
                                            @if($request->isAwaitingSuperadminReview())
                                                <button class="btn btn-sm btn-success approve-btn" data-type="financial" data-id="{{ $request->id }}" title="Approve">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger reject-btn" data-type="financial" data-id="{{ $request->id }}" title="Reject">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                                <button class="btn btn-sm btn-info view-btn" data-type="financial" data-id="{{ $request->id }}" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            @else
                                                <span class="text-muted">Finalized</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" style="text-align: center; padding: 2rem;">
                                        <p class="text-muted">No financial assistance requests found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $financialRequests->links() }}
            @else
                <div class="alert alert-info">
                    No financial assistance requests at this time.
                </div>
            @endif
        </div>

        <!-- Medical Supplies Requests Section -->
        <div class="approvals-section">
            <h2>Medical Supplies Requests</h2>
            
            @if($medicalRequests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Reason</th>
                                @if(auth()->user()->role === 'bhw')
                                    <th>Submitted</th>
                                    <th>Status</th>
                                @elseif(auth()->user()->role === 'admin')
                                    <th>Submitted By</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                @else
                                    <th>Submitted By</th>
                                    <th>Admin Notes</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($medicalRequests as $request)
                                <tr>
                                    <td>#{{ $request->id }}</td>
                                    <td>{{ $request->item_name }}</td>
                                    <td>{{ $request->quantity }}</td>
                                    <td>{{ Str::limit($request->reason, 50) }}</td>
                                    @if(auth()->user()->role === 'bhw')
                                        <td>{{ $request->submitted_at->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge {{ $request->getStatusBadge()['class'] }}">
                                                {{ $request->getStatusBadge()['label'] }}
                                            </span>
                                        </td>
                                    @elseif(auth()->user()->role === 'admin')
                                        <td>{{ $request->requestor->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $request->getStatusBadge()['class'] }}">
                                                {{ $request->getStatusBadge()['label'] }}
                                            </span>
                                        </td>
                                        <td class="admin-actions">
                                            @if($request->isPending())
                                                <button class="btn btn-sm btn-success approve-btn" data-type="medical" data-id="{{ $request->id }}" title="Forward to Superadmin">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger reject-btn" data-type="medical" data-id="{{ $request->id }}" title="Reject">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            @else
                                                <span class="text-muted">Reviewed</span>
                                            @endif
                                        </td>
                                    @else
                                        <td>{{ $request->requestor->name ?? 'N/A' }}</td>
                                        <td>{{ $request->admin_notes ? Str::limit($request->admin_notes, 30) : '—' }}</td>
                                        <td>
                                            <span class="badge {{ $request->getStatusBadge()['class'] }}">
                                                {{ $request->getStatusBadge()['label'] }}
                                            </span>
                                        </td>
                                        <td class="superadmin-actions">
                                            @if($request->isAwaitingSuperadminReview())
                                                <button class="btn btn-sm btn-success approve-btn" data-type="medical" data-id="{{ $request->id }}" title="Approve">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger reject-btn" data-type="medical" data-id="{{ $request->id }}" title="Reject">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                                <button class="btn btn-sm btn-info view-btn" data-type="medical" data-id="{{ $request->id }}" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            @else
                                                <span class="text-muted">Finalized</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" style="text-align: center; padding: 2rem;">
                                        <p class="text-muted">No medical supplies requests found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $medicalRequests->links() }}
            @else
                <div class="alert alert-info">
                    No medical supplies requests at this time.
                </div>
            @endif
        </div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <p>Loading...</p>
                </div>
                <div class="modal-footer" id="modalFooter">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
                const userRole = '{{ auth()->user()->role }}';

                // View details button
                document.querySelectorAll('.view-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const type = this.dataset.type;
                        const id = this.dataset.id;
                        loadRequestDetails(type, id);
                    });
                });

                // Approve button
                document.querySelectorAll('.approve-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const type = this.dataset.type;
                        const id = this.dataset.id;
                        const endpoint = userRole === 'admin' 
                            ? `/approvals/${type}/${id}/admin-approve`
                            : `/approvals/${type}/${id}/superadmin-approve`;
                        
                        approveRequest(type, id, endpoint);
                    });
                });

                // Reject button
                document.querySelectorAll('.reject-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const type = this.dataset.type;
                        const id = this.dataset.id;
                        const endpoint = userRole === 'admin' 
                            ? `/approvals/${type}/${id}/admin-reject`
                            : `/approvals/${type}/${id}/superadmin-reject`;
                        
                        rejectRequest(type, id, endpoint);
                    });
                });

                function loadRequestDetails(type, id) {
                    fetch(`/approvals/${type}/${id}`)
                        .then(response => response.json())
                        .then(data => {
                            displayRequestDetails(data, type);
                            modal.show();
                        })
                        .catch(error => console.error('Error:', error));
                }

                function displayRequestDetails(request, type) {
                    let content = `
                        <div class="request-details">
                            <div class="detail-section">
                                <h6>Requestor Information</h6>
                                <p><strong>Name:</strong> ${request.requestor?.name || 'N/A'}</p>
                            </div>
                    `;

                    if (type === 'financial') {
                        content += `
                            <div class="detail-section">
                                <h6>Financial Assistance Details</h6>
                                <p><strong>Type:</strong> ${request.type}</p>
                                <p><strong>Amount:</strong> ₱${parseFloat(request.amount).toFixed(2)}</p>
                                <p><strong>Reason:</strong> ${request.reason}</p>
                                <p><strong>Description:</strong> ${request.description || 'N/A'}</p>
                            </div>
                        `;
                    } else {
                        content += `
                            <div class="detail-section">
                                <h6>Medical Supplies Details</h6>
                                <p><strong>Item:</strong> ${request.item_name}</p>
                                <p><strong>Quantity:</strong> ${request.quantity}</p>
                                <p><strong>Reason:</strong> ${request.reason}</p>
                                <p><strong>Description:</strong> ${request.description || 'N/A'}</p>
                            </div>
                        `;
                    }

                    if (request.admin) {
                        content += `
                            <div class="detail-section">
                                <h6>Admin Review</h6>
                                <p><strong>Reviewed By:</strong> ${request.admin.name}</p>
                                <p><strong>Reviewed At:</strong> ${new Date(request.admin_reviewed_at).toLocaleString()}</p>
                                <p><strong>Notes:</strong> ${request.admin_notes || 'N/A'}</p>
                            </div>
                        `;
                    }

                    content += `</div>`;
                    document.getElementById('modalBody').innerHTML = content;
                    document.getElementById('modalTitle').textContent = `${type === 'financial' ? 'Financial Assistance' : 'Medical Supplies'} Request Details`;
                }

                function approveRequest(type, id, endpoint) {
                    if (!confirm('Are you sure you want to approve this request?')) return;

                    fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ notes: '' })
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert('Request approved successfully!');
                        location.reload();
                    })
                    .catch(error => console.error('Error:', error));
                }

                function rejectRequest(type, id, endpoint) {
                    if (!confirm('Are you sure you want to reject this request?')) return;

                    fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ notes: '' })
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert('Request rejected.');
                        location.reload();
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        </script>
    @endpush
        </div>

    </div>
@endsection