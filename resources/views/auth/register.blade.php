<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Asta Dasa Parwa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/regis.css') }}">
</head>
<body>

<div class="auth-container">
    <div class="card auth-card shadow">
        <div class="card-body">
            <h3 class="text-center mb-2">Create an Account</h3>
            <p class="text-center text-muted mb-4">
                Create an account to continue
            </p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        placeholder="example@gmail.com"
                        required
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Username -->
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input
                        type="text"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        placeholder="Username"
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3 position-relative">
                    <label class="form-label">Password</label>
                    <input
                        type="password"
                        name="password"
                        id="register_password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="••••••••"
                        required
                    >

                    <span class="toggle-password" onclick="togglePassword('register_password', this)">
                        👁
                    </span>

                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-3 position-relative">
                    <label class="form-label">Confirm Password</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="register_password_confirmation"
                        class="form-control"
                        placeholder="••••••••"
                        required
                    >

                    <span class="toggle-password" onclick="togglePassword('register_password_confirmation', this)">
                        👁
                    </span>
                </div>


                <!-- Terms -->
                <div class="mb-4 form-check">
                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="terms"
                        required
                    >
                    <label class="form-check-label" for="terms">
                        I accept terms and conditions
                    </label>
                </div>

                <button type="submit" class="btn btn-auth w-100 mb-3">
                    Sign Up
                </button>

                <p class="text-center mb-0">
                    Already have an account?
                    <a href="{{ route('login') }}" class="link">Login</a>
                </p>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/regis.js') }}"></script>

</body>
</html>
