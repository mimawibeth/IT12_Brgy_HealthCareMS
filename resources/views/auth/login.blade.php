<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Barangay Health Center</title>
    <!-- Login Page CSS -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <!-- Login Container: centers the login box -->
    <div class="login-container">
        <!-- Login Box -->
        <div class="login-box">
            <!-- Header -->
            <div class="login-header">
                <h1>Barangay Health Center</h1>
                <p>System Login</p>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.post') }}" class="login-form">
                @csrf

                <!-- Display error messages -->
                @if($errors->any())
                    <div class="alert alert-error">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Username Input -->
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" value="{{ old('username') }}"
                        required autofocus placeholder="Enter your username">
                </div>

                <!-- Password Input -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required
                        placeholder="Enter your password">
                </div>

                <!-- Remember Me Checkbox -->
                <div class="form-group checkbox-group">
                    <label>
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn btn-primary btn-block">
                    Login
                </button>
            </form>

            <!-- Footer -->
            <div class="login-footer">
                <p>&copy; 2025 Barangay Health Center System</p>
            </div>
        </div>
    </div>
</body>

</html>