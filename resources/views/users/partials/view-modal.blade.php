{{-- User View Modal - Reusable component --}}
<div class="modal" id="userViewModal" style="display:none;">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3>User Details</h3>
            <span class="close-modal" id="closeUserModal">&times;</span>
        </div>
        <div class="modal-body" id="userModalBody">
            <div class="loading-spinner" style="text-align:center; padding: 2rem;">
                <p>Loading...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('userViewModal');
            const closeModal = document.getElementById('closeUserModal');

            document.querySelectorAll('.view-user').forEach(button => {
                button.addEventListener('click', async function () {
                    const userId = this.getAttribute('data-id');

                    modal.style.display = 'flex';
                    const modalBody = document.getElementById('userModalBody');
                    modalBody.innerHTML = '<div style="text-align:center; padding: 2rem;"><p>Loading...</p></div>';

                    try {
                        const response = await fetch(`/users/${userId}`);

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();

                        const roleLabels = {
                            'super_admin': 'Super Admin',
                            'admin': 'Admin',
                            'bhw': 'Barangay Health Worker',
                            'staff': 'Staff'
                        };

                        const roleBadges = {
                            'super_admin': 'badge-super-admin',
                            'admin': 'badge-admin',
                            'bhw': 'badge-bhw',
                            'staff': 'badge-staff'
                        };

                        const statusBadge = data.status === 'active' ? 'status-active' : 'status-inactive';

                        modalBody.innerHTML = `
                                <div class="form-section section-patient-info">
                                    <h3 class="section-header"><span class="section-indicator"></span>Account Information</h3>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>Username:</strong></label>
                                            <p>${data.username || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Email Address:</strong></label>
                                            <p>${data.email || 'N/A'}</p>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>User Role:</strong></label>
                                            <p><span class="badge ${roleBadges[data.role] || 'badge-staff'}">${roleLabels[data.role] || 'N/A'}</span></p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Account Status:</strong></label>
                                            <p><span class="status-badge ${statusBadge}">${data.status ? data.status.charAt(0).toUpperCase() + data.status.slice(1) : 'N/A'}</span></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section section-screening">
                                    <h3 class="section-header"><span class="section-indicator"></span>Personal Information</h3>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>First Name:</strong></label>
                                            <p>${data.first_name || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Middle Name:</strong></label>
                                            <p>${data.middle_name || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Last Name:</strong></label>
                                            <p>${data.last_name || 'N/A'}</p>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>Contact Number:</strong></label>
                                            <p>${data.contact_number || 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Address:</strong></label>
                                            <p>${data.address || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section section-history">
                                    <h3 class="section-header"><span class="section-indicator"></span>Additional Information</h3>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label><strong>Account Created:</strong></label>
                                            <p>${data.created_at ? new Date(data.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'N/A'}</p>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Last Updated:</strong></label>
                                            <p>${data.updated_at ? new Date(data.updated_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'N/A'}</p>
                                        </div>
                                    </div>
                                    ${data.notes ? `
                                    <div class="form-row">
                                        <div class="form-group full-width">
                                            <label><strong>Notes/Remarks:</strong></label>
                                            <p>${data.notes}</p>
                                        </div>
                                    </div>
                                    ` : ''}
                                </div>
                            `;
                    } catch (error) {
                        modalBody.innerHTML = '<div style="text-align:center; padding: 2rem; color: red;"><p>Error loading user details.</p></div>';
                    }
                });
            });

            closeModal.addEventListener('click', () => modal.style.display = 'none');
            window.addEventListener('click', (event) => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    </script>
@endpush