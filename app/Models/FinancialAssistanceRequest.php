<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialAssistanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'reason',
        'amount',
        'description',
        'status',
        'admin_id',
        'superadmin_id',
        'admin_notes',
        'superadmin_notes',
        'submitted_at',
        'admin_reviewed_at',
        'superadmin_reviewed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'submitted_at' => 'datetime',
        'admin_reviewed_at' => 'datetime',
        'superadmin_reviewed_at' => 'datetime',
    ];

    /**
     * Get the BHW user who submitted the request
     */
    public function requestor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the admin who reviewed the request
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the superadmin who approved/rejected the request
     */
    public function superadmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'superadmin_id');
    }

    /**
     * Check if request is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request is approved by superadmin
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved_by_superadmin';
    }

    /**
     * Check if request is rejected by superadmin
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected_by_superadmin';
    }

    /**
     * Check if request is pending admin review
     */
    public function isPendingAdminReview(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request is awaiting superadmin review
     */
    public function isAwaitingSuperadminReview(): bool
    {
        return $this->status === 'approved_by_admin';
    }

    /**
     * Get status badge color/label
     */
    public function getStatusBadge(): array
    {
        return match($this->status) {
            'pending' => ['label' => 'Pending', 'class' => 'badge-warning'],
            'approved_by_admin' => ['label' => 'Forwarded to Admin', 'class' => 'badge-info'],
            'rejected_by_admin' => ['label' => 'Rejected by Admin', 'class' => 'badge-danger'],
            'approved_by_superadmin' => ['label' => 'Approved', 'class' => 'badge-success'],
            'rejected_by_superadmin' => ['label' => 'Rejected', 'class' => 'badge-danger'],
            default => ['label' => 'Unknown', 'class' => 'badge-secondary'],
        };
    }
}
