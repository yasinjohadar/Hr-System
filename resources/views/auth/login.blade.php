<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول - نظام إدارة الموارد البشرية</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/auth-login.css') }}">
    <style>.d-none { display: none !important; }</style>
</head>
<body class="auth-login-page">
    <div class="auth-shell">
        {{-- لوحة العلامة التجارية (يمين في RTL) --}}
        <aside class="auth-brand" aria-hidden="false">
            <div class="auth-brand-grid" aria-hidden="true"></div>
            <div class="auth-brand-inner">
                <div class="auth-brand-logo">
                    <div class="auth-brand-logo-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                        </svg>
                    </div>
                    <span class="auth-brand-logo-text">نظام الموارد البشرية</span>
                </div>

                <h2 class="auth-brand-headline">
                    إدارة فريقك <span>بذكاء</span> ووضوح
                </h2>
                <p class="auth-brand-desc">
                    منصة متكاملة للإجازات، الحضور، الرواتب، والموافقات — كل ما يحتاجه فريق الموارد البشرية في مكان واحد.
                </p>

                <div class="auth-features">
                    <div class="auth-feature">
                        <div class="auth-feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                        </div>
                        <div class="auth-feature-text">
                            <strong>إجازات وحضور</strong>
                            <span>تتبّع الطلبات والموافقات التسلسلية</span>
                        </div>
                    </div>
                    <div class="auth-feature">
                        <div class="auth-feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                        </div>
                        <div class="auth-feature-text">
                            <strong>صلاحيات وأمان</strong>
                            <span>تحكم دقيق حسب الدور والقسم</span>
                        </div>
                    </div>
                    <div class="auth-feature">
                        <div class="auth-feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
                        </div>
                        <div class="auth-feature-text">
                            <strong>تقارير ولوحات</strong>
                            <span>رؤية فورية لأداء المنظمة</span>
                        </div>
                    </div>
                </div>
            </div>
            <p class="auth-brand-footer">&copy; {{ date('Y') }} نظام إدارة الموارد البشرية</p>
        </aside>

        {{-- نموذج الدخول --}}
        <main class="auth-form-panel">
            <div class="auth-card">
                <header class="auth-card-header">
                    <div class="mobile-logo" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <h1>مرحباً بعودتك</h1>
                    <p>سجّل دخولك للوصول إلى لوحة التحكم</p>
                </header>

                @if (session('status'))
                    <div class="auth-alert auth-alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="auth-alert auth-alert-danger" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- دخول سريع (بيئة التطوير فقط) --}}
                @if (! empty($quickLoginAccounts ?? []))
                    <div class="auth-quick" data-quick-login>
                        <div class="auth-quick-head">
                            <span class="auth-quick-badge">بيئة التطوير</span>
                            <span class="auth-quick-title">دخول سريع</span>
                        </div>
                        <div class="auth-quick-grid">
                            @foreach ($quickLoginAccounts as $account)
                                <button
                                    type="button"
                                    class="auth-quick-btn"
                                    data-quick-email="{{ $account['email'] }}"
                                    data-quick-password="{{ $account['password'] }}"
                                    title="{{ $account['hint'] ?? '' }}"
                                >
                                    <span class="auth-quick-btn-label">{{ $account['label'] }}</span>
                                    <span class="auth-quick-btn-meta">{{ $account['email'] }}</span>
                                </button>
                            @endforeach
                        </div>
                        <p class="auth-quick-note">تعبئة تلقائية وتسجيل دخول فوري. تُعطّل بـ <code>DEV_QUICK_LOGIN=false</code>.</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="login-form" novalidate>
                    @csrf

                    <div class="auth-field">
                        <label for="email">البريد الإلكتروني</label>
                        <div class="auth-input-wrap">
                            <svg class="field-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
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
                                placeholder="name@company.com"
                                class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                            >
                        </div>
                        @error('email')
                            <span class="auth-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="auth-field">
                        <label for="password">كلمة المرور</label>
                        <div class="auth-input-wrap">
                            <svg class="field-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="أدخل كلمة المرور"
                                class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                            >
                            <button type="button" class="toggle-password" id="toggle-password" aria-label="إظهار كلمة المرور">
                                <svg data-icon-show xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                <svg data-icon-hide class="d-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                            </button>
                        </div>
                        @error('password')
                            <span class="auth-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="auth-options">
                        <label class="auth-remember">
                            <input id="remember_me" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>تذكرني</span>
                        </label>
                    </div>

                    <button type="submit" class="auth-submit">
                        تسجيل الدخول
                    </button>
                </form>

                @if (Route::has('password.request'))
                    <p class="auth-forgot">
                        <a href="{{ route('password.request') }}">نسيت كلمة المرور؟</a>
                    </p>
                @endif
            </div>
        </main>
    </div>

    <script src="{{ asset('assets/js/auth-login.js') }}" defer></script>
</body>
</html>
