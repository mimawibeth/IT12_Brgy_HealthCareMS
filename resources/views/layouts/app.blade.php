<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Barangay Health Center')</title>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <!-- Page-specific CSS -->
    @stack('styles')
</head>

<body class="{{ (auth()->user()->dark_mode ?? false) ? 'dark-mode' : '' }}">
    <!-- Main Container: holds sidebar and content -->
    <div class="app-container">

        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <!-- Logo/Header Section -->
            <div class="sidebar-header">
                <img src="{{ asset('images/brgy.logo.png') }}" alt="Barangay Logo" class="sidebar-logo">
                <h2>Barangay Sto. Niño</h2>
                <p class="sidebar-subtitle">Health Center System</p>
                <p class="user-role" style="display: none;">{{ auth()->user()->role ?? 'Guest' }}</p>
            </div>

            <!-- Navigation Menu -->
            <nav class="sidebar-nav">
                <!-- Dashboard Link -->
                <a href="{{ route('dashboard') }}"
                    class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 icon"></i>
                    <span>Dashboard</span>
                </a>

                <!-- PATIENT RECORDS SECTION -->
                <!-- Patient Management Dropdown -->
                <div class="nav-dropdown">
                    <button class="nav-dropdown-toggle">
                        <i class="bi bi-people icon"></i>
                        <span>Patient Management</span>
                        <i class="bi bi-chevron-down arrow"></i>
                    </button>
                    <div class="nav-dropdown-menu">
                        <a href="{{ route('patients.index') }}"
                            class="nav-item {{ request()->routeIs('patients.index') ? 'active' : '' }}">
                            <i class="bi bi-list-ul icon"></i>
                            <span>Patient List</span>
                        </a>
                        <a href="{{ route('patients.create') }}"
                            class="nav-item {{ request()->routeIs('patients.create') ? 'active' : '' }}">
                            <i class="bi bi-person-plus icon"></i>
                            <span>Add Patient</span>
                        </a>
                    </div>
                </div>

                <!-- HEALTH SERVICES SECTION -->
                <!-- Health Programs Dropdown -->
                <div class="nav-dropdown">
                    <button class="nav-dropdown-toggle">
                        <i class="bi bi-heart-pulse icon"></i>
                        <span>Health Programs</span>
                        <i class="bi bi-chevron-down arrow"></i>
                    </button>
                    <div class="nav-dropdown-menu">
                        <a href="{{ route('health-programs.prenatal-view') }}"
                            class="nav-item {{ request()->routeIs('health-programs.prenatal-*') ? 'active' : '' }}">
                            <i class="bi bi-heart-pulse icon"></i>
                            <span>Prenatal Care</span>
                        </a>
                        <a href="{{ route('health-programs.family-planning-view') }}"
                            class="nav-item {{ request()->routeIs('health-programs.family-planning-*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill icon"></i>
                            <span>Family Planning</span>
                        </a>
                        <a href="{{ route('health-programs.new-nip-view') }}"
                            class="nav-item {{ request()->routeIs('health-programs.new-nip-*') ? 'active' : '' }}">
                            <i class="bi bi-shield-plus icon"></i>
                            <span>Immunization</span>
                        </a>

                    </div>
                </div>

                <!-- INVENTORY SECTION -->
                <!-- Medicine Dropdown -->
                <div class="nav-dropdown">
                    <button class="nav-dropdown-toggle">
                        <i class="bi bi-capsule icon"></i>
                        <span>Medicine</span>
                        <i class="bi bi-chevron-down arrow"></i>
                    </button>
                    <div class="nav-dropdown-menu">
                        <a href="{{ route('medicine.index') }}"
                            class="nav-item {{ request()->routeIs('medicine.index') ? 'active' : '' }}">
                            <i class="bi bi-list-ul icon"></i>
                            <span>Medicine Records</span>
                        </a>
                        <a href="{{ route('medicine.dispense') }}"
                            class="nav-item {{ request()->routeIs('medicine.dispense') ? 'active' : '' }}">
                            <i class="bi bi-prescription2 icon"></i>
                            <span>Dispense Medicine</span>
                        </a>
                    </div>
                </div>

                <!-- SYSTEM MANAGEMENT SECTION -->
                <!-- User Management Dropdown -->
                @if(in_array(auth()->user()->role ?? '', ['super_admin', 'admin']))
                    <div class="nav-dropdown">
                        <button class="nav-dropdown-toggle">
                            <i class="bi bi-person-badge icon"></i>
                            <span>User Management</span>
                            <i class="bi bi-chevron-down arrow"></i>
                        </button>
                        <div class="nav-dropdown-menu">
                            <a href="{{ route('users.all-users') }}"
                                class="nav-item {{ request()->routeIs('users.all-users') ? 'active' : '' }}">
                                <i class="bi bi-people icon"></i>
                                <span>All Users</span>
                            </a>
                            <a href="{{ route('users.add-new') }}"
                                class="nav-item {{ request()->routeIs('users.add-new') ? 'active' : '' }}">
                                <i class="bi bi-person-plus icon"></i>
                                <span>Add New User</span>
                            </a>
                            @if((auth()->user()->role ?? '') === 'super_admin')
                                <a href="{{ route('users.admin-accounts') }}"
                                    class="nav-item {{ request()->routeIs('users.admin-accounts') ? 'active' : '' }}">
                                    <i class="bi bi-person-gear icon"></i>
                                    <span>Admin Accounts</span>
                                </a>
                                <a href="{{ route('users.role-management') }}"
                                    class="nav-item {{ request()->routeIs('users.role-management') ? 'active' : '' }}">
                                    <i class="bi bi-shield-lock icon"></i>
                                    <span>Role Management</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                @if(in_array(auth()->user()->role ?? '', ['super_admin', 'admin']))
                    <div class="nav-dropdown">
                        <button class="nav-dropdown-toggle">
                            <i class="bi bi-ui-checks-grid icon"></i>
                            <span>Assistance & Requests</span>
                            <i class="bi bi-chevron-down arrow"></i>
                        </button>

                        <div class="nav-dropdown-menu">
                            <a href="{{ route('financial-assistance.index') }}"
                                class="nav-item {{ request()->routeIs('financial-assistance.*') ? 'active' : '' }}">
                                <i class="bi bi-wallet2 icon"></i>
                                <span>Financial Assistance</span>
                            </a>

                            <a href="{{ route('medical-supplies.request') }}"
                                class="nav-item {{ request()->routeIs('medical-supplies.*') ? 'active' : '' }}">
                                <i class="bi bi-bag-plus icon"></i>
                                <span>Medical Supply Request</span>
                            </a>

                            <a href="{{ route('approvals.index') }}"
                                class="nav-item {{ request()->routeIs('approvals.*') ? 'active' : '' }}">
                                <i class="bi bi-check2-square icon"></i>
                                <span>Pending Approvals</span>
                            </a>
                        </div>
                    </div>
                @endif


                <!-- Reports -->
                @if((auth()->user()->role ?? '') !== 'bhw')
                    <a href="{{ route('reports.monthly') }}"
                        class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart icon"></i>
                        <span>Reports</span>
                    </a>
                @endif

                <!-- Audit Logs -->
                @if(in_array(auth()->user()->role ?? '', ['super_admin', 'admin']))
                    <a href="{{ route('logs.audit') }}" class="nav-item {{ request()->routeIs('logs.*') ? 'active' : '' }}">
                        <i class="bi bi-file-text icon"></i>
                        <span>Audit Logs</span>
                    </a>
                @endif
            </nav>

            <!-- Sidebar Footer: Settings & Logout (Fixed at bottom) -->
            <div class="sidebar-footer">
                <!-- Settings -->
                <a href="{{ route('settings.index') }}"
                    class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear icon"></i>
                    <span>Settings</span>
                </a>

                <!-- Logout Button -->
                <a href="{{ route('logout') }}" class="nav-item logout-btn">
                    <i class="bi bi-box-arrow-right icon"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Top Bar with page title and user info -->
            <header class="top-bar">
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                <div class="user-info">
                    @if(in_array(auth()->user()->role ?? '', ['super_admin', 'admin']))
                        <div class="notification-bell">
                            <button class="bell-button" id="notificationBell">
                                <i class="bi bi-bell"></i>
                                <span class="notification-badge">3</span>
                            </button>
                        </div>
                    @endif
                    <div class="user-details">
                        <div class="user-role">
                            <i class="bi bi-shield-lock"></i>
                            <span id="userRole">Super Admin</span>
                        </div>
                        @php($authUser = auth()->user())
                        <div class="user-name-display">
                            @if($authUser)
                                {{ trim(($authUser->first_name ?? '') . ' ' . ($authUser->last_name ?? '')) ?: $authUser->name }}
                            @else
                                Guest
                            @endif
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success">
                        ✓ {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        ✗ {{ session('error') }}
                    </div>
                @endif

                <!-- Main Page Content -->
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Page-specific JavaScript -->
    @stack('scripts')

    <!-- Sidebar Dropdown Script -->
    <script>
        // Update user role based on actual role
        function updateUserRole() {
            const userRole = '{{ auth()->user()->role ?? "user" }}';
            const roleText = document.getElementById('userRole');

            // Map roles to display text - Only 3 user types
            const roleMap = {
                'super_admin': 'System Super Administrator',
                'admin': 'System Administrator',
                'bhw': 'Barangay Health Worker'
            };

            roleText.textContent = roleMap[userRole] || 'User';
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            updateUserRole();
        });

        // Toggle dropdown menus in sidebar
        document.querySelectorAll('.nav-dropdown-toggle').forEach(button => {
            button.addEventListener('click', function () {
                const dropdown = this.parentElement;
                const dropdownId = this.querySelector('span').textContent.trim();
                const isOpen = dropdown.classList.contains('open');

                // Close all dropdowns
                document.querySelectorAll('.nav-dropdown').forEach(d => {
                    d.classList.remove('open');
                });

                // Open clicked dropdown if it was closed
                if (!isOpen) {
                    dropdown.classList.add('open');
                    // Save open state to localStorage
                    localStorage.setItem('openDropdown', dropdownId);
                } else {
                    // Remove from localStorage if closing
                    localStorage.removeItem('openDropdown');
                }
            });
        });

        // Restore open dropdown on page load
        document.addEventListener('DOMContentLoaded', function () {
            const openDropdownId = localStorage.getItem('openDropdown');
            if (openDropdownId) {
                document.querySelectorAll('.nav-dropdown-toggle').forEach(button => {
                    if (button.querySelector('span').textContent.trim() === openDropdownId) {
                        button.parentElement.classList.add('open');
                    }
                });
            }
        });
    </script>
</body>

</html>