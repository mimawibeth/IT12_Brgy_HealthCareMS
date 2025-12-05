# Integration Guide - Approval System

## Adding Approval Menu Item to Navigation

To add the Approvals link to your main navigation menu, find your layout file (typically `resources/views/layouts/app.blade.php`) and add:

```blade
<a href="{{ route('approvals.index') }}" class="nav-link">
    <i class="bi bi-clipboard-check"></i> Approvals
</a>
```

## Database Setup Verification

Run the following commands to verify the system is set up correctly:

```bash
# Check migration status
php artisan migrate:status

# Should show:
# 2025_12_05_000001_create_financial_assistance_requests_table ................ [3] Ran
# 2025_12_05_000002_create_medical_supplies_requests_table .................... [3] Ran
```

## Routes Verification

Check that all routes are registered:

```bash
php artisan route:list | grep approvals

# Should show 11 routes starting with:
# GET|HEAD  approvals ................................................. approvals.index
```

## Testing the System

### Prerequisites
- Ensure you have at least 3 user accounts with different roles:
  - 1 BHW user
  - 1 Admin user
  - 1 Superadmin user

### Test Case 1: BHW Submits Financial Request
1. Login as BHW user
2. Go to Approvals dashboard
3. Click "Request Financial Assistance"
4. Fill form:
   - Type: Emergency
   - Amount: 5000
   - Reason: Medical emergency
   - Description: Emergency medical treatment
5. Submit
6. Verify request appears on dashboard with "Pending" status

### Test Case 2: Admin Reviews Request
1. Login as Admin user
2. Go to Approvals dashboard
3. See the BHW's pending request
4. Click approve (✓) button
5. Confirm action
6. Verify:
   - Status changes to "Approved by Admin"
   - Admin name appears
   - Timestamp is recorded

### Test Case 3: Superadmin Finalizes Request
1. Login as Superadmin user
2. Go to Approvals dashboard
3. See only admin-approved requests
4. Click view (👁) button
5. Modal shows full details including admin review
6. Click approve (✓) button
7. Confirm action
8. Verify:
   - Status changes to "Approved by Superadmin"
   - Superadmin name appears
   - Timestamp is recorded

### Test Case 4: Rejection at Admin Level
1. Login as Admin user
2. Go to Approvals dashboard
3. Click reject (✗) button on a pending request
4. Verify:
   - Status changes to "Rejected"
   - Request no longer appears for Superadmin

### Test Case 5: Medical Supplies Request
1. Login as BHW user
2. Click "Request Medical Supplies"
3. Fill form:
   - Item: First Aid Kit
   - Quantity: 5
   - Reason: Clinic needs supplies
   - Description: For emergency response
4. Submit
5. Verify it appears on dashboard in Medical Supplies section

## API Endpoints

The following JSON endpoints are available for AJAX calls:

### Get Request Details
```
GET /approvals/{type}/{id}
```
Response:
```json
{
  "id": 1,
  "type": "Emergency",
  "amount": "5000.00",
  "reason": "Medical emergency",
  "description": "Emergency medical treatment",
  "status": "approved_by_admin",
  "submitted_at": "2025-12-05T10:00:00Z",
  "admin_reviewed_at": "2025-12-05T11:00:00Z",
  "admin_notes": null,
  "requestor": {
    "id": 2,
    "name": "Juan Dela Cruz"
  },
  "admin": {
    "id": 3,
    "name": "Admin User"
  },
  "superadmin": null
}
```

### Approve Request (Admin)
```
POST /approvals/{type}/{id}/admin-approve
Headers: X-CSRF-TOKEN, Content-Type: application/json
Body: { "notes": "optional admin notes" }
```

### Reject Request (Admin)
```
POST /approvals/{type}/{id}/admin-reject
Headers: X-CSRF-TOKEN, Content-Type: application/json
Body: { "notes": "optional rejection reason" }
```

### Approve Request (Superadmin)
```
POST /approvals/{type}/{id}/superadmin-approve
Headers: X-CSRF-TOKEN, Content-Type: application/json
Body: { "notes": "optional superadmin notes" }
```

### Reject Request (Superadmin)
```
POST /approvals/{type}/{id}/superadmin-reject
Headers: X-CSRF-TOKEN, Content-Type: application/json
Body: { "notes": "optional rejection reason" }
```

## Customization Guide

### Changing Status Enum Values

To add or modify status values, edit both migration files:

```php
// In both migration files
$table->enum('status', [
    'pending',
    'approved_by_admin',
    'rejected_by_admin',
    'approved_by_superadmin',
    'rejected_by_superadmin',
    // Add new statuses here
])->default('pending');
```

Then create a new migration to modify the enum:
```bash
php artisan make:migration alter_approval_statuses_table
```

### Customizing Form Fields

Edit the Blade templates to add/remove fields:

**For Financial Assistance** (`resources/views/approvals/financial-assistance.blade.php`):
```blade
<div class="form-group">
    <label for="field_name" class="form-label">Field Label</label>
    <input type="text" name="field_name" ... />
</div>
```

**Update Controller Validation** in `ApprovalController@storeFinancial()`:
```php
$validated = $request->validate([
    'field_name' => 'required|string|max:255',
    // Add your validation rules
]);
```

### Changing Badge Colors

In `index.blade.php`, modify the `getStatusBadge()` method return values:

```php
// In the model
public function getStatusBadge()
{
    return match($this->status) {
        'pending' => ['label' => 'Pending', 'class' => 'badge bg-warning text-dark'],
        'approved_by_admin' => ['label' => 'Approved by Admin', 'class' => 'badge bg-info'],
        'rejected_by_admin' => ['label' => 'Rejected', 'class' => 'badge bg-danger'],
        'approved_by_superadmin' => ['label' => 'Approved', 'class' => 'badge bg-success'],
        'rejected_by_superadmin' => ['label' => 'Rejected', 'class' => 'badge bg-danger'],
    };
}
```

### Adding Email Notifications

In `ApprovalController`, add after status updates:

```php
// After admin approval
Mail::send('emails.approval-forwarded', [...], function($message) {
    $message->to($request->requestor->email);
});

// After superadmin approval
Mail::send('emails.approval-approved', [...], function($message) {
    $message->to($request->requestor->email);
});
```

## Troubleshooting

### Routes Not Found
```bash
# Clear route cache
php artisan route:clear

# Rebuild cache
php artisan route:cache
```

### Models Not Found
```bash
# Check namespace in ApprovalController
use App\Models\FinancialAssistanceRequest;
use App\Models\MedicalSuppliesRequest;

# Verify files exist
php artisan tinker
> class_exists('App\Models\FinancialAssistanceRequest')
// Should return: true
```

### Database Errors
```bash
# Check migration status
php artisan migrate:status

# Re-run migrations if needed
php artisan migrate:refresh --step=2
```

### Permission Denied
- Verify user role is set correctly: `user.role = 'bhw'|'admin'|'superadmin'`
- Check that users have proper roles assigned in database

### Form Not Submitting
- Check CSRF token in HTML: `@csrf`
- Verify form action URL is correct: `{{ route('approvals.financial.store') }}`
- Check browser console for JavaScript errors

## Logging and Monitoring

Enable detailed logging for approval actions:

```php
// In ApprovalController, add logging
use Illuminate\Support\Facades\Log;

Log::channel('approvals')->info('Request approved', [
    'request_id' => $id,
    'user_id' => auth()->id(),
    'status' => 'approved_by_admin',
    'timestamp' => now(),
]);
```

Create the logging channel in `config/logging.php`:
```php
'approvals' => [
    'driver' => 'single',
    'path' => storage_path('logs/approvals.log'),
],
```

## Backup and Recovery

### Database Backup
```bash
# Export database
mysqldump -u username -p database_name > backup.sql

# Restore database
mysql -u username -p database_name < backup.sql
```

### Code Backup
```bash
# Git backup
git add .
git commit -m "Approval system backup"
git push

# Or manual backup
xcopy "C:\xampp\htdocs\IT12_Brgy_HealthCareMS" "C:\backups\IT12_Brgy_HealthCareMS_backup" /E /I
```

## Performance Tuning

### Database Optimization
```sql
-- Create indexes if not already present
CREATE INDEX idx_financial_user_id ON financial_assistance_requests(user_id);
CREATE INDEX idx_financial_status ON financial_assistance_requests(status);
CREATE INDEX idx_medical_user_id ON medical_supplies_requests(user_id);
CREATE INDEX idx_medical_status ON medical_supplies_requests(status);
```

### Query Caching
```php
// In ApprovalController index method
$cacheKey = 'approvals_' . auth()->id() . '_' . auth()->user()->role;
$requests = Cache::remember($cacheKey, 3600, function() {
    // Query logic here
});
```

### Load Testing
```bash
# Use Apache Bench
ab -n 1000 -c 10 http://localhost:8000/approvals
```

## Version Control

Current version: **1.0**
Release date: **December 5, 2025**

### Changelog
```
v1.0 - Initial Release
- Complete 3-tier approval system
- Financial assistance requests
- Medical supplies requests
- Role-based dashboard
- Complete CRUD operations
```

---

**For support or questions, refer to:**
- APPROVAL_SYSTEM_README.md
- APPROVAL_SYSTEM_COMPLETE.md
- APPROVAL_SYSTEM_CHECKLIST.md
