# Approval System - Quick Reference

## ✅ System Status: OPERATIONAL

All issues have been fixed. The 3-tier approval system is ready for use.

---

## Quick Access URLs

### Public Pages
- **Dashboard**: `/approvals` - View your requests and approval status
- **Submit Financial Request**: `/approvals/financial/create` - Fill out form and submit
- **Submit Medical Request**: `/approvals/medical/create` - Fill out form and submit

### API Endpoints (AJAX)
- **Get Request Details**: `GET /approvals/{type}/{id}` - Returns JSON for modal
- **Admin Approve**: `POST /approvals/{type}/{id}/admin-approve` - Forward to superadmin
- **Admin Reject**: `POST /approvals/{type}/{id}/admin-reject` - Reject request
- **Superadmin Approve**: `POST /approvals/{type}/{id}/superadmin-approve` - Final approval
- **Superadmin Reject**: `POST /approvals/{type}/{id}/superadmin-reject` - Final rejection

---

## Request Types

### Financial Assistance
- **Fields**: Type, Amount, Reason, Description
- **Types**: Emergency, Medical, Educational, Other
- **Amount**: In Philippine Peso (₱)

### Medical Supplies
- **Fields**: Item Name, Quantity, Reason, Description
- **Quantity**: Numeric value
- **Reason**: Why the item is needed

---

## User Roles & Permissions

### BHW User (Health Worker)
- ✅ View own submitted requests
- ✅ Submit financial assistance requests
- ✅ Submit medical supplies requests
- ✅ See request status (Pending/Approved/Rejected)
- ❌ Cannot approve other requests

### Admin User
- ✅ View all pending requests
- ✅ View requests they reviewed
- ✅ Forward requests to superadmin (Approve button)
- ✅ Reject requests (Reject button)
- ✅ Add notes to requests
- ❌ Cannot make final approval

### Superadmin User
- ✅ View only admin-approved requests
- ✅ View full request details in modal
- ✅ See admin review notes
- ✅ Make final approval decision
- ✅ Make final rejection decision
- ❌ Cannot approve pending requests (must go through admin first)

---

## Status Flow Diagram

```
BHW Submits
    ↓
[PENDING] (status: 'pending')
    ↓
Admin Reviews
    ├→ ✅ APPROVE → [APPROVED BY ADMIN] (status: 'approved_by_admin')
    │                    ↓
    │                Superadmin Reviews
    │                    ├→ ✅ APPROVE → [FINAL APPROVED] (status: 'approved_by_superadmin')
    │                    └→ ❌ REJECT → [FINAL REJECTED] (status: 'rejected_by_superadmin')
    │
    └→ ❌ REJECT → [REJECTED] (status: 'rejected_by_admin')
                      ↓
                   END - Request Denied
```

---

## Status Badges

| Status | Color | Meaning |
|--------|-------|---------|
| Pending | Yellow | Waiting for admin review |
| Approved by Admin | Blue | Forwarded to superadmin |
| Approved | Green | Final approval granted |
| Rejected | Red | Request denied |

---

## Database Tables

### financial_assistance_requests
```
id, user_id, type, amount, reason, description, status
admin_id, admin_reviewed_at, admin_notes
superadmin_id, superadmin_reviewed_at, superadmin_notes
submitted_at, created_at, updated_at
```

### medical_supplies_requests
```
id, user_id, item_name, quantity, reason, description, status
admin_id, admin_reviewed_at, admin_notes
superadmin_id, superadmin_reviewed_at, superadmin_notes
submitted_at, created_at, updated_at
```

---

## Model Methods

### FinancialAssistanceRequest / MedicalSuppliesRequest

**Relationships:**
- `requestor()` - User who submitted (BHW)
- `admin()` - Admin who reviewed
- `superadmin()` - Superadmin who finalized

**Status Helpers:**
- `isPending()` - Is waiting for admin
- `isApproved()` - Has final approval
- `isRejected()` - Was rejected at any stage
- `isPendingAdminReview()` - Still awaiting admin decision
- `isAwaitingSuperadminReview()` - Forwarded by admin, waiting for superadmin
- `getStatusBadge()` - Returns [label, css_class] for display

---

## Troubleshooting

### "Route not found"
```bash
php artisan route:clear
php artisan view:clear
```

### "Undefined variable"
- Ensure you're accessing the dashboard via `/approvals` route (not direct view)
- Check that ApprovalController::index() is being called

### "Access denied"
- Verify your user role in database: `users.role` = 'bhw', 'admin', or 'superadmin'
- Check auth middleware is applied: `auth()` must be set

### "Form not submitting"
- Check CSRF token in form: `@csrf`
- Verify form action route name matches: `route('approvals.financial.store')`
- Check browser console for JavaScript errors

---

## Common Tasks

### Submit a Request (BHW)
1. Click "Approvals" in navigation
2. Choose "Request Financial Assistance" or "Request Medical Supplies"
3. Fill in all required fields (marked with *)
4. Click "Submit Request"
5. You'll be redirected to dashboard to view status

### Review Request (Admin)
1. Go to `/approvals` dashboard
2. Find pending request in table
3. Click ✅ (check) to approve and forward to superadmin
4. Click ✗ (X) to reject
5. Optional: Click "View" to see details before deciding

### Finalize Request (Superadmin)
1. Go to `/approvals` dashboard
2. See only admin-approved requests
3. Click 👁 (eye) icon to view full details
4. Review information in modal
5. Click ✅ (check) to approve or ✗ (X) to reject
6. Requestor will see final status

---

## Files Overview

### Key Files
- **Controller**: `app/Http/Controllers/ApprovalController.php`
- **Models**: `app/Models/FinancialAssistanceRequest.php`, `app/Models/MedicalSuppliesRequest.php`
- **Views**: `resources/views/approvals/` folder
- **Routes**: `routes/web.php` (lines 380-410)
- **Migrations**: `database/migrations/2025_12_05_*`

### Configuration
- **Database**: MySQL table `financial_assistance_requests` and `medical_supplies_requests`
- **Auth**: Laravel's built-in auth with role-based middleware
- **Validation**: Server-side only (can add client-side if needed)

---

## Performance Notes

- **Pagination**: 10 items per page on dashboard
- **Relationships**: Lazy loaded with `->with(['requestor', 'admin'])`
- **Caching**: Routes cached, views compiled
- **Database**: Indexes on user_id, admin_id, superadmin_id fields

---

## Security Features

✅ CSRF token protection on all forms
✅ Auth middleware on all routes
✅ Role-based authorization in controller
✅ SQL injection prevention (Eloquent ORM)
✅ XSS prevention (Blade escaping)
✅ User relationships prevent unauthorized access

---

## Recent Fixes (December 5, 2025)

✅ Fixed route naming issue - removed settings prefix
✅ Fixed undefined variables in views
✅ Removed obsolete stub routes
✅ Updated route cache
✅ All routes now correctly named and functional

**Status**: All errors resolved, system operational

---

For detailed documentation, see:
- `APPROVAL_SYSTEM_README.md` - Complete guide
- `APPROVAL_SYSTEM_INTEGRATION_GUIDE.md` - Integration steps
- `APPROVAL_SYSTEM_BUG_FIXES.md` - What was fixed
- `APPROVAL_SYSTEM_CHECKLIST.md` - Implementation checklist
