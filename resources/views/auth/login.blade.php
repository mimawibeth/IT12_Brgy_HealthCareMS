<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Login - Barangay Health Center</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <div class="login-container">
        <!-- Left Panel - Branding -->
        <div class="branding-panel">
            <div class="logo-container">
                <img src="{{ asset('images/brgy.logo.png') }}" alt="Barangay Logo" class="logo-img">
            </div>
            <h1 class="brand-title">Barangay Sto. Niño</h1>
            <p class="brand-subtitle">Integrated Healthcare and Community Services Management System</p>
            <div class="feature-list">
                <div class="feature-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Healthcare Services</span>
                </div>
                <div class="feature-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Health Programs</span>
                </div>
                <div class="feature-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Records Management</span>
                </div>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="form-panel">
            <div class="form-header">
                <h2>Welcome Back</h2>
                <p>Please sign in to continue</p>
            </div>

            <form method="POST" action="{{ route('login.post') }}" class="login-form">
                @csrf

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="alert alert-error">
                        <svg class="alert-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Email Input -->
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <div class="input-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                </path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </div>
                        <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}"
                            required autofocus placeholder="example@email.com">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <div class="input-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <input type="password" id="password" name="password" class="form-input" required
                            placeholder="Enter your password">
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <svg id="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg id="eye-off-icon" style="display: none;" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <path
                                    d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                </path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Forgot Password -->
                <div class="form-options">
                    <a href="#" class="forgot-password-link">Forgot password?</a>
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn-login">
                    <span>Sign In</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>

            <!-- Footer -->
            <div class="form-footer">
                <p>Need assistance? Contact your System Administrator.</p>
            </div>
        </div>
    </div>

    <script>
        // Prevent authenticated users from accessing login page (including via browser back button)
        (function() {
            function checkAuthAndRedirect() {
                fetch('{{ route("auth.check") }}', {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.authenticated) {
                        window.location.href = '{{ route("dashboard") }}';
                    }
                })
                .catch(() => {
                    // If request fails, assume not authenticated (which is fine for login page)
                });
            }

            // Check immediately on page load
            checkAuthAndRedirect();

            // Check when page becomes visible (user switches back to tab/window)
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    checkAuthAndRedirect();
                }
            });

            // Check when page is loaded from browser cache (back/forward button)
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    // Page was loaded from cache, check auth status
                    checkAuthAndRedirect();
                }
            });

            // Also check on focus (when user clicks back into the window)
            window.addEventListener('focus', function() {
                checkAuthAndRedirect();
            });
        })();

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                eyeIcon.style.display = 'block';
                eyeOffIcon.style.display = 'none';
            }
        }
    </script>
</body>

</html>