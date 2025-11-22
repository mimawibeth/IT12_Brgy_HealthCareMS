<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Barangay Health Center')</title>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <!-- Page-specific CSS -->
    @stack('styles')
</head>

<body>
    <!-- Main Container: holds sidebar and content -->
    <div class="app-container">

        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <!-- Logo/Header Section -->
            <div class="sidebar-header">
                <img src="{{ asset('images/brgy.logo.png') }}" alt="Barangay Logo" class="sidebar-logo">
                <h2>Brgy Sto. Niño HCS</h2>
                <p class="user-role">{{ auth()->user()->role ?? 'Guest' }}</p>
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
                        <a href="{{ route('health-programs.prenatal.records') }}"
                            class="nav-item {{ request()->routeIs('health-programs.prenatal.*') ? 'active' : '' }}">
                            <i class="bi bi-heart-pulse icon"></i>
                            <span>Prenatal Care</span>
                        </a>
                        <a href="{{ route('health-programs.fp.records') }}"
                            class="nav-item {{ request()->routeIs('health-programs.fp.*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill icon"></i>
                            <span>Family Planning</span>
                        </a>
                        <a href="{{ route('health-programs.immunization.records') }}"
                            class="nav-item {{ request()->routeIs('health-programs.immunization.*') ? 'active' : '' }}">
                            <i class="bi bi-shield-check icon"></i>
                            <span>Immunization</span>
                        </a>
                        <a href="{{ route('health-programs.other-services') }}"
                            class="nav-item {{ request()->routeIs('health-programs.other-services') ? 'active' : '' }}">
                            <i class="bi bi-bandaid icon"></i>
                            <span>Other Services</span>
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
                            <span>Medicine List</span>
                        </a>
                        <a href="{{ route('medicine.dispense') }}"
                            class="nav-item {{ request()->routeIs('medicine.dispense') ? 'active' : '' }}">
                            <i class="bi bi-prescription2 icon"></i>
                            <span>Dispense</span>
                        </a>
                    </div>
                </div>

                <!-- SYSTEM MANAGEMENT SECTION -->
                <!-- User Management Dropdown -->
                <div class="nav-dropdown">
                    <button class="nav-dropdown-toggle">
                        <i class="bi bi-person-badge icon"></i>
                        <span>User Management</span>
                        <i class="bi bi-chevron-down arrow"></i>
                    </button>
                    <div class="nav-dropdown-menu">
                        <a href="{{ route('users.bhw') }}"
                            class="nav-item {{ request()->routeIs('users.bhw') ? 'active' : '' }}">
                            <i class="bi bi-people icon"></i>
                            <span>All Users</span>
                        </a>
                        <a href="{{ route('users.create') }}"
                            class="nav-item {{ request()->routeIs('users.create') ? 'active' : '' }}">
                            <i class="bi bi-person-plus icon"></i>
                            <span>Add New User</span>
                        </a>
                        <a href="{{ route('users.admin') }}"
                            class="nav-item {{ request()->routeIs('users.admin') ? 'active' : '' }}">
                            <i class="bi bi-person-gear icon"></i>
                            <span>Admin Accounts</span>
                        </a>
                        <a href="{{ route('users.roles') }}"
                            class="nav-item {{ request()->routeIs('users.roles') ? 'active' : '' }}">
                            <i class="bi bi-shield-lock icon"></i>
                            <span>Role Management</span>
                        </a>
                    </div>
                </div>

                <!-- Reports -->
                <a href="{{ route('reports.monthly') }}"
                    class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart icon"></i>
                    <span>Reports</span>
                </a>

                <!-- Audit Logs -->
                <a href="{{ route('logs.audit') }}" class="nav-item {{ request()->routeIs('logs.*') ? 'active' : '' }}">
                    <i class="bi bi-file-text icon"></i>
                    <span>Audit Logs</span>
                </a>
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
                    <span class="user-name">{{ auth()->user()->name ?? 'User' }}</span>
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