<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Asta Dasa Parwa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

<div class="login-container">
    <div class="card login-card shadow">
        <div class="card-body">
            <h3 class="text-center mb-2">Login to Account</h3>
            <p class="text-center text-muted mb-4">
                Please enter your email and password to continue
            </p>

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    <input 
                        type="email" 
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="example@gmail.com"
                        value="{{ old('email') }}"
                        required
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-2 d-flex justify-content-between">
                    <label class="form-label">Password</label>
                    <a href="{{ route('password.request') }}" class="link">
                        Forget Password?
                    </a>
                </div>

                <div class="mb-3 position-relative">
                    <label class="form-label">Password</label>
                    <input
                        type="password"
                        name="password"
                        id="login_password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="••••••••"
                        required
                    >

                    <span class="toggle-password" onclick="togglePassword('login_password', this)">
                        👁
                    </span>


                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4 form-check">
                    <input 
                        type="checkbox" 
                        name="remember"
                        class="form-check-input"
                        id="remember"
                    >
                    <label class="form-check-label" for="remember">
                        Remember Password
                    </label>
                </div>

                <button type="submit" class="btn btn-login w-100 mb-3">
                    Sign In
                </button>

                <p class="text-center mb-0">
                    Don’t have an account?
                    <a href="{{ route('register') }}" class="link">
                        Create Account
                    </a>
                </p>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="{{ asset('js/login.js') }}"></script>

</body>
</html>
