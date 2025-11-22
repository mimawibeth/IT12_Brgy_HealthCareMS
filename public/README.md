# Barangay Healthcare Management System - UI Documentation

## 📋 Overview

A comprehensive healthcare management system designed for barangay health centers. This is a **pure UI implementation** using HTML, CSS, and vanilla JavaScript with no backend integration.

## 🎯 Features

The system includes **5 main subsystems**:

1. **Resident Health Information Subsystem** - Manage resident health records
2. **Consultation and Appointment Management Subsystem** - Schedule and track appointments
3. **Audit Logs and Activity Monitoring Subsystem** - Track system activities (Admin only)
4. **Medicine and Supply Inventory Subsystem** - Manage medicine stock
5. **Health Statistics and Reporting Subsystem** - View analytics and generate reports

## 👥 User Roles

The system supports two user types:

### Admin User
- **Username:** admin
- **Password:** admin123
- **Access:** Full access to all subsystems including Audit Logs

### Employee User
- **Username:** employee
- **Password:** employee123
- **Access:** Access to all subsystems except Audit Logs

## 📁 File Structure

```
public/
├── login.html                 # Login page (entry point)
├── dashboard.html             # Main dashboard
├── residents.html             # Resident health information
├── consultations.html         # Consultations & appointments
├── inventory.html             # Medicine inventory
├── statistics.html            # Health statistics & reports
├── audit-logs.html            # Audit logs (Admin only)
├── css/
│   ├── main-styles.css        # Main CSS components & utilities
│   ├── layout.css             # Layout styles (sidebar, header)
│   └── login.css              # Login page specific styles
└── js/
    ├── main.js                # Common JavaScript functions
    ├── login.js               # Login functionality
    ├── residents.js           # Residents module
    ├── consultations.js       # Consultations module
    ├── inventory.js           # Inventory module
    ├── statistics.js          # Statistics module
    └── audit-logs.js          # Audit logs module
```

## 🚀 Getting Started

### Option 1: Direct Access (Recommended for Testing)
1. Open `public/login.html` directly in your web browser
2. Use the demo credentials provided above
3. Navigate through the system

### Option 2: Using Laravel Development Server
1. Make sure you have Laravel installed
2. Run: `php artisan serve`
3. Navigate to: `http://localhost:8000/login.html`

### Option 3: Using Vite Development Server
1. Install dependencies: `npm install`
2. Run: `npm run dev`
3. Open the provided URL in your browser

## 🎨 Design Features

### Modern & Clean UI
- Clean, professional interface
- Intuitive navigation
- Responsive design (mobile-friendly)
- Color-coded status badges
- Icon-based visual cues

### CSS Variables
The design uses CSS custom properties for easy theming:
- Primary color: `#0ea5e9` (Sky Blue)
- Success: `#22c55e` (Green)
- Warning: `#f59e0b` (Amber)
- Danger: `#ef4444` (Red)

### Components
- Cards
- Tables
- Forms
- Modals
- Alerts
- Badges
- Buttons
- Tabs
- Pagination
- Statistics cards

## 📊 Subsystem Details

### 1. Resident Health Information
**Features:**
- Add/Edit/Delete residents
- View detailed health records
- Filter by age group, gender, and health status
- Search functionality
- Track medical history, allergies, and medications

**Sample Data:** 4 pre-populated residents

### 2. Consultation & Appointment Management
**Features:**
- Schedule appointments
- Track appointment status (Scheduled, In Progress, Completed, Cancelled)
- View today's appointments
- Filter by appointment type
- Associate appointments with residents and doctors

**Sample Data:** 4 pre-populated appointments

### 3. Medicine & Supply Inventory
**Features:**
- Add/Edit/Delete inventory items
- Track stock levels
- Low stock alerts
- Stock adjustment (add/remove stock)
- Filter by category and stock status
- Expiry date tracking
- Batch number tracking

**Sample Data:** 6 pre-populated items

### 4. Health Statistics & Reporting
**Features:**
- View key metrics
- Chart placeholders with sample data
- Generate custom reports
- Export reports (PDF, Excel, CSV)
- Time period filtering
- Report history

**Chart Types Represented:**
- Consultation trends (Line chart)
- Age distribution (Bar chart)
- Health status (Pie chart)
- Common conditions (Horizontal bar)

### 5. Audit Logs & Activity Monitoring (Admin Only)
**Features:**
- View all system activities
- Track user actions
- Monitor failed login attempts
- Filter by activity type, user, and severity
- Activity timeline view
- Export logs functionality

**Sample Data:** 12 pre-populated log entries

## 💾 Data Persistence

**Important:** This is a pure UI implementation with **no backend**.

- All data is stored in JavaScript arrays
- Data is **not persistent** across page refreshes
- Session storage is used only for authentication state
- Refresh the page to reset all demo data

## 🎯 Key Functionalities

### Authentication
- Simple session-based login
- Role-based access control
- Logout functionality
- Auto-redirect for unauthorized access

### CRUD Operations
All subsystems support:
- ✅ Create new records
- ✅ Read/View records
- ✅ Update existing records
- ✅ Delete records

### Filtering & Search
- Real-time search
- Multiple filter options
- Combined filtering capabilities

### UI Interactions
- Modal dialogs for forms
- Alert notifications
- Confirmation dialogs
- Interactive tables
- Responsive design

## 🎨 Customization Guide

### Changing Colors
Edit CSS variables in `public/css/main-styles.css`:
```css
:root {
    --primary-color: #0ea5e9;
    --success-color: #22c55e;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    /* ... other colors ... */
}
```

### Modifying Logo
Update the emoji in the sidebar:
```html
<div class="sidebar-logo-icon">⚕️</div>
```

### Changing Demo Credentials
Edit `public/js/login.js`:
```javascript
const USERS = {
    admin: {
        username: 'admin',
        password: 'admin123',
        // ...
    }
}
```

## 📱 Browser Compatibility

Tested and working on:
- ✅ Chrome (Recommended)
- ✅ Firefox
- ✅ Edge
- ✅ Safari

## 🔧 Technical Notes

### No External Dependencies
The UI uses:
- Pure HTML5
- Pure CSS3 (with CSS Variables)
- Vanilla JavaScript (ES6+)
- No frameworks or libraries

### Simple Code Structure
- Clear naming conventions
- Well-commented code
- Separated concerns (HTML, CSS, JS)
- Easy to understand and modify

### Responsive Design
- Mobile-first approach
- Breakpoints at 768px and 480px
- Touch-friendly interface
- Collapsible sidebar on mobile

## 📝 Sample Data Summary

| Module | Sample Records |
|--------|---------------|
| Residents | 4 |
| Appointments | 4 |
| Inventory Items | 6 |
| Audit Logs | 12 |
| Reports | 3 |

## 🚧 Limitations (UI Only)

Since this is a pure UI:
- ❌ No real database
- ❌ No API integration
- ❌ No data persistence
- ❌ No real authentication
- ❌ No actual file exports
- ❌ Charts are placeholders with sample data

## 🎓 Learning Resources

This project demonstrates:
- HTML5 semantic markup
- CSS Grid & Flexbox layouts
- CSS Custom Properties (Variables)
- Vanilla JavaScript DOM manipulation
- Event handling
- Form validation
- Modal dialogs
- Local/Session storage
- Responsive web design

## 📞 Support

For questions or issues:
1. Check the code comments
2. Review the sample data in each JS file
3. Use browser DevTools Console for debugging

## 🎉 Quick Start Summary

1. **Open** `public/login.html` in your browser
2. **Login** with `admin/admin123` or `employee/employee123`
3. **Explore** all 5 subsystems
4. **Test** CRUD operations (data resets on refresh)
5. **View** responsive design by resizing browser

---

**Built with ❤️ for Barangay Healthcare Management**

*Note: This is a demonstration UI. For production use, integrate with a proper backend system.*
