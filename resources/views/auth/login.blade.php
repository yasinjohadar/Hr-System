<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول - نظام إدارة الموارد البشرية</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f3f4f6;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 24px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.06);
            padding: 40px 36px;
            width: 100%;
            max-width: 420px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-header .logo-icon {
            width: 48px;
            height: 48px;
            background-color: #2563eb;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .login-header .logo-icon svg {
            width: 24px;
            height: 24px;
            color: #ffffff;
        }

        .login-header h1 {
            color: #111827;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .login-header p {
            color: #6b7280;
            font-size: 14px;
            font-weight: 400;
        }

        .alert {
            padding: 12px 14px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.5;
        }

        .alert ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .alert-danger {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        .alert-success {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #374151;
            font-weight: 500;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper .input-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: #9ca3af;
            pointer-events: none;
        }

        .input-wrapper input {
            width: 100%;
            padding: 10px 40px 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            font-size: 14px;
            font-family: 'Cairo', sans-serif;
            color: #111827;
            background-color: #ffffff;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            outline: none;
        }

        .input-wrapper input::placeholder {
            color: #9ca3af;
        }

        .input-wrapper input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .input-wrapper input.error {
            border-color: #ef4444;
        }

        .input-wrapper input.error:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .error-message {
            color: #ef4444;
            font-size: 13px;
            margin-top: 6px;
            display: block;
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border-radius: 5px;
            border: 1px solid #d1d5db;
            cursor: pointer;
            accent-color: #2563eb;
        }

        .remember-me span {
            color: #374151;
            font-size: 14px;
            user-select: none;
        }

        .btn-login {
            width: 100%;
            padding: 11px 20px;
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Cairo', sans-serif;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        .btn-login:hover {
            background-color: #1d4ed8;
        }

        .btn-login:active {
            background-color: #1e40af;
        }

        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }

        .forgot-password a {
            color: #2563eb;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.15s ease;
        }

        .forgot-password a:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 28px 20px;
            }

            .login-header h1 {
                font-size: 22px;
            }

            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="logo-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
            </div>
            <h1>تسجيل الدخول</h1>
            <p>نظام إدارة الموارد البشرية</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <div class="input-wrapper">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="example@company.com"
                        class="{{ $errors->has('email') ? 'error' : '' }}"
                    >
                </div>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <div class="input-wrapper">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="{{ $errors->has('password') ? 'error' : '' }}"
                    >
                </div>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-options">
                <label class="remember-me">
                    <input id="remember_me" type="checkbox" name="remember">
                    <span>تذكرني</span>
                </label>
            </div>

            <button type="submit" class="btn-login">
                تسجيل الدخول
            </button>
        </form>

        @if (Route::has('password.request'))
            <div class="forgot-password">
                <a href="{{ route('password.request') }}">نسيت كلمة المرور؟</a>
            </div>
        @endif
    </div>
</body>
</html>
