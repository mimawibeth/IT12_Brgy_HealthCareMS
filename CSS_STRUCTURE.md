# 🎨 CSS File Structure

## Overview

Each Blade template now has its own dedicated CSS file in the `public/css/` folder for better organization and maintainability.

---

## 📁 CSS Files Structure

```
public/css/
├── main-styles.css      # Global styles (buttons, forms, tables, cards, etc.)
├── layout.css           # Layout styles (sidebar, header, containers)
├── login.css            # Login page specific styles
├── dashboard.css        # Dashboard page specific styles
├── residents.css        # Patient records page specific styles
├── consultations.css    # Consultations & appointments page specific styles
├── inventory.css        # Medicine inventory page specific styles
├── statistics.css       # Health statistics & reports page specific styles
└── audit-logs.css       # Audit logs page specific styles
```

---

## 🔗 How It Works

### Global Styles (Always Loaded)
The main layout file (`layouts/app.blade.php`) loads these CSS files on every page:
- `main-styles.css` - Core styles (colors, typography, components)
- `layout.css` - Layout structure (sidebar, header, grid system)

### Page-Specific Styles (Loaded Per Page)
Each Blade template uses `@push('styles')` to load its own CSS file:

**Example:**
```blade
@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush
```

---

## 📄 CSS Files Breakdown

### 1. **dashboard.css**
**Used by:** `dashboard.blade.php`
**Contains:**
- Dashboard statistics cards with icons
- Quick actions styling
- Low stock alerts styling
- Dashboard-specific responsive layouts

**Key Classes:**
- `.dashboard-stats`
- `.stat-icon`
- `.dashboard-quick-actions`
- `.low-stock-alert`

---

### 2. **residents.css**
**Used by:** `residents.blade.php`
**Contains:**
- Patient records filter section
- Residents table actions
- Health status badges
- Resident detail modal styles

**Key Classes:**
- `.residents-filter-section`
- `.residents-table-actions`
- `.health-status-badge`
- `.resident-detail-item`

---

### 3. **consultations.css**
**Used by:** `consultations.blade.php`
**Contains:**
- Appointment header actions
- Quick transaction buttons with gradient background
- Action toolbar with print/export buttons
- Appointment tabs styling
- Appointment status badges

**Key Classes:**
- `.consultations-header-actions`
- `.quick-transactions-card`
- `.quick-transaction-btn`
- `.action-toolbar`
- `.tab-btn`
- `.appointments-table-actions`

---

### 4. **inventory.css**
**Used by:** `inventory.blade.php`
**Contains:**
- Inventory header actions
- Quick inventory actions (stock count, transfer, etc.)
- Inventory toolbar with filters
- Stock status indicators
- Category badges
- Purchase order item checkboxes

**Key Classes:**
- `.inventory-header-actions`
- `.inventory-quick-actions-card`
- `.inventory-toolbar`
- `.stock-in-stock`, `.stock-low-stock`, `.stock-out-stock`
- `.category-medicine`, `.category-vitamin`, etc.
- `.purchase-order-items`

---

### 5. **statistics.css**
**Used by:** `statistics.blade.php`
**Contains:**
- Quick report templates styling
- Statistics filters
- Chart placeholder styles
- Report type badges
- Report includes options

**Key Classes:**
- `.statistics-header-actions`
- `.quick-reports-card`
- `.quick-report-btn`
- `.chart-placeholder`
- `.report-type-health`, `.report-type-consultation`, etc.
- `.report-includes-options`

---

### 6. **audit-logs.css**
**Used by:** `audit-logs.blade.php`
**Contains:**
- Audit filters layout
- Activity timeline styling
- Severity badges
- Activity type badges
- Log detail modal styles

**Key Classes:**
- `.audit-header-actions`
- `.audit-filters`
- `.activity-timeline`
- `.timeline-item`, `.timeline-icon`, `.timeline-content`
- `.severity-info`, `.severity-warning`, `.severity-critical`
- `.activity-login`, `.activity-create`, `.activity-delete`, etc.

---

## 🎯 Benefits of Separate CSS Files

### 1. **Better Organization**
- Each page has its own styles
- Easy to locate and modify styles
- No confusion about which styles affect which page

### 2. **Improved Maintainability**
- Changes to one page don't affect others
- Easier debugging
- Clear separation of concerns

### 3. **Performance**
- Only load styles needed for each page
- Smaller CSS files load faster
- Browser can cache individual files

### 4. **Scalability**
- Easy to add new pages with their own styles
- No risk of CSS conflicts
- Clean code structure

### 5. **Developer Experience**
- Easier to work on specific pages
- Clear naming conventions
- Better code readability

---

## 🔧 How to Add Styles to a Page

### Step 1: Identify Which File to Edit

**For dashboard changes:**
```
Edit: public/css/dashboard.css
```

**For consultations changes:**
```
Edit: public/css/consultations.css
```

**For global changes (affecting all pages):**
```
Edit: public/css/main-styles.css
```

### Step 2: Add Your Styles

Open the appropriate CSS file and add your styles:

```css
/* Example: Adding a new style to consultations.css */
.appointment-urgent {
    background: #fee2e2;
    border-left: 4px solid #dc2626;
    padding: 12px;
}
```

### Step 3: Use the Class in Your Blade File

```blade
<div class="appointment-urgent">
    Urgent appointment
</div>
```

---

## 📱 Responsive Design

All page-specific CSS files include responsive breakpoints:

```css
/* Tablet and below */
@media (max-width: 768px) {
    /* Tablet styles */
}

/* Mobile */
@media (max-width: 480px) {
    /* Mobile styles */
}
```

---

## 🎨 CSS Variables (Global)

Defined in `main-styles.css` and available in all CSS files:

```css
:root {
    /* Colors */
    --primary-color: #0066cc;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --info-color: #3b82f6;
    
    /* Text */
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --text-muted: #9ca3af;
    
    /* Background */
    --bg-color: #f9fafb;
    --border-color: #e5e7eb;
    
    /* Spacing */
    --radius-sm: 8px;
    --radius-md: 12px;
}
```

---

## ✅ Best Practices

### 1. **Use Specific Classes**
❌ Don't:
```css
.card { margin: 20px; } /* Too generic, affects all cards */
```

✅ Do:
```css
.consultations-card { margin: 20px; } /* Page-specific */
```

### 2. **Keep Styles in the Right File**
- Page-specific → Page CSS file
- Component-specific → main-styles.css
- Layout-specific → layout.css

### 3. **Use CSS Variables**
❌ Don't:
```css
.btn-primary { background: #0066cc; }
```

✅ Do:
```css
.btn-primary { background: var(--primary-color); }
```

### 4. **Comment Complex Styles**
```css
/* Appointment timeline with vertical connector line */
.timeline-item::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 40px;
    width: 2px;
    height: calc(100% - 40px);
    background: var(--border-color);
}
```

---

## 🔄 Migration Summary

### What Changed:
1. ✅ Created 6 new page-specific CSS files
2. ✅ Moved inline styles from statistics.blade.php to statistics.css
3. ✅ Added `@push('styles')` to all blade templates
4. ✅ Organized styles by page functionality

### What Stayed the Same:
- Global styles in `main-styles.css`
- Layout styles in `layout.css`
- Login page styles in `login.css`

---

## 📚 File Reference

| Blade File | CSS File | Purpose |
|------------|----------|---------|
| `dashboard.blade.php` | `dashboard.css` | Dashboard stats and quick actions |
| `residents.blade.php` | `residents.css` | Patient records management |
| `consultations.blade.php` | `consultations.css` | Appointments and consultations |
| `inventory.blade.php` | `inventory.css` | Medicine inventory management |
| `statistics.blade.php` | `statistics.css` | Health reports and statistics |
| `audit-logs.blade.php` | `audit-logs.css` | Activity monitoring and audit trails |
| `login.blade.php` | `login.css` | Login page (existing) |
| All pages | `main-styles.css` | Global styles (existing) |
| All pages | `layout.css` | Layout structure (existing) |

---

## 🎉 Ready to Use!

Your CSS is now properly organized and separated by page. Each page loads only the styles it needs, making your application faster and easier to maintain!

To see the changes:
```bash
php artisan serve
```

Then visit any page and inspect the `<head>` section - you'll see the page-specific CSS file loaded!
