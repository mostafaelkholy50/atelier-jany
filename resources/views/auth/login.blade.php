<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول — Jenny's Atelier</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sky-light:   #cde9f7;
            --sky-mid:     #a8d4ee;
            --accent-blue: #4a90c4;
            --accent-dark: #1c4a6e;
            --text-main:   #111827; /* Near black */
            --text-muted:  #374151;
            --gold:        #b9985a;
        }

        @font-face {
            font-family: 'Cairo';
            src: url('{{ asset('Font/Cairo-Regular.ttf') }}') format('truetype');
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: 'Cairo';
            src: url('{{ asset('Font/Cairo-Medium.ttf') }}') format('truetype');
            font-weight: 500;
            font-style: normal;
        }
        
        @font-face {
            font-family: 'Cairo';
            src: url('{{ asset('Font/Cairo-SemiBold.ttf') }}') format('truetype');
            font-weight: 600;
            font-style: normal;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Cairo', sans-serif;
            font-weight: 600;
            line-height: 1.8;
            min-height: 100vh;
            overflow: hidden;
            color: #000000;
            -webkit-font-smoothing: initial;
            -moz-osx-font-smoothing: initial;
            font-size: 18px;
            text-rendering: optimizeLegibility;
        }

        /* ═══ FULL SKY BACKGROUND ═══ */
        .bg-scene {
            position: fixed;
            inset: 0;
            background: url("{{ asset('images/background.png') }}") center top / cover no-repeat;
        }

        /* Soft overlay for readability */
        .bg-overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(
                170deg,
                rgba(180,220,245,0.25) 0%,
                rgba(220,238,252,0.15) 40%,
                rgba(240,248,255,0.3) 100%
            );
        }

        /* ═══ FLOATING CLOUDS ANIMATION ═══ */
        .cloud {
            position: fixed;
            background: radial-gradient(ellipse, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.4) 55%, transparent 75%);
            border-radius: 50%;
            pointer-events: none;
        }

        .c1 { width:320px; height:130px; top:8%;  left:-5%;   animation: drift 28s ease-in-out infinite; }
        .c2 { width:220px; height:90px;  top:18%; right:10%;  animation: drift 22s ease-in-out infinite reverse; }
        .c3 { width:180px; height:75px;  top:62%; left:5%;    animation: drift 35s ease-in-out infinite 5s; }
        .c4 { width:260px; height:100px; bottom:12%; right:-3%; animation: drift 30s ease-in-out infinite 10s; }

        @keyframes drift {
            0%,100% { transform: translateX(0) translateY(0); }
            25%      { transform: translateX(18px) translateY(-8px); }
            75%      { transform: translateX(-12px) translateY(5px); }
        }

        /* ═══ FLOATING PETALS ═══ */
        .petal {
            position: fixed;
            width: 8px; height: 8px;
            border-radius: 50% 0 50% 0;
            background: rgba(180,215,245,0.7);
            pointer-events: none;
            animation: petalRise linear infinite;
        }

        @keyframes petalRise {
            0%   { bottom: -10px; opacity: 0; transform: rotate(0deg) translateX(0); }
            15%  { opacity: 0.8; }
            85%  { opacity: 0.5; }
            100% { bottom: 110%; opacity: 0; transform: rotate(280deg) translateX(30px); }
        }

        /* ═══ LOGIN LAYOUT ═══ */
        .login-scene {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* LEFT — Branding side */
        .brand-side {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px;
            position: relative;
        }

        .brand-logo-wrap {
            animation: floatLogo 6s ease-in-out infinite;
        }

        @keyframes floatLogo {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-12px); }
        }

        .brand-logo-wrap img {
            width: 220px;
            height: auto;
            filter: drop-shadow(0 12px 40px rgba(74,144,196,0.25));
        }

        /* Fallback logo (SVG text) if image fails */
        .brand-fallback {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3rem;
            font-style: italic;
            color: var(--accent-dark);
            text-shadow: 0 4px 20px rgba(74,144,196,0.3);
        }

        .brand-tagline {
            margin-top: 28px;
            text-align: center;
        }

        .brand-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.4rem;
            font-weight: 600;
            color: var(--accent-dark);
            letter-spacing: 0.02em;
        }

        .brand-arabic {
            font-family: 'Cairo', sans-serif;
            font-size: 1rem;
            color: var(--text-muted);
            margin-top: 6px;
            letter-spacing: 0.05em;
        }

        .brand-quote {
            margin-top: 32px;
            max-width: 280px;
            text-align: center;
            font-family: 'Cormorant Garamond', serif;
            font-style: italic;
            font-size: 1.05rem;
            color: var(--accent-blue);
            opacity: 0.8;
            line-height: 1.7;
        }

        /* Decorative line */
        .brand-line {
            width: 60px;
            height: 1px;
            background: linear-gradient(to right, transparent, var(--gold), transparent);
            margin: 18px auto;
        }

        /* ═══ RIGHT — Form side ═══ */
        .form-side {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 32px;
        }

        .form-card {
            width: 100%;
            max-width: 400px;
            background: rgba(255,255,255,0.72);
            backdrop-filter: blur(24px) saturate(1.5);
            -webkit-backdrop-filter: blur(24px) saturate(1.5);
            border-radius: 28px;
            padding: 44px 40px;
            border: 1px solid rgba(180,215,240,0.5);
            box-shadow:
                0 8px 40px rgba(74,144,196,0.12),
                0 2px 8px rgba(255,255,255,0.8) inset,
                0 -2px 6px rgba(180,215,240,0.2) inset;
            animation: cardIn 0.7s cubic-bezier(0.2,0,0,1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(24px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0)    scale(1); }
        }

        .form-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .form-greeting {
            font-size: 0.75rem;
            color: var(--text-muted);
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .form-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--accent-dark);
            line-height: 1.2;
        }

        .form-subtitle {
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-top: 6px;
        }

        /* ═══ FORM FIELDS ═══ */
        .field-wrap {
            margin-bottom: 18px;
        }

        .field-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--text-main);
            margin-bottom: 7px;
            letter-spacing: 0.02em;
        }

        .field-input-wrap {
            position: relative;
        }

        .field-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.95rem;
            color: var(--text-muted);
            pointer-events: none;
        }

        .field-input {
            width: 100%;
            height: 48px;
            padding: 0 42px 0 16px;
            border-radius: 13px;
            border: 1.5px solid rgba(140,190,220,0.45);
            background: rgba(255,255,255,0.7);
            font-family: 'Cairo', sans-serif;
            font-size: 0.9rem;
            color: var(--text-main);
            outline: none;
            transition: all 0.25s ease;
            direction: ltr;
        }

        .field-input:focus {
            border-color: var(--accent-blue);
            background: rgba(255,255,255,0.92);
            box-shadow: 0 0 0 4px rgba(74,144,196,0.1), 0 2px 8px rgba(74,144,196,0.08);
        }

        .field-input::placeholder {
            color: rgba(90,122,148,0.5);
            font-size: 0.85rem;
        }

        /* Remember + Forgot row */
        .field-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 4px 0 22px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 0.78rem;
            color: var(--text-muted);
            cursor: pointer;
        }

        .remember-label input {
            accent-color: var(--accent-blue);
            width: 14px;
            height: 14px;
        }

        .forgot-link {
            font-size: 0.78rem;
            color: var(--accent-blue);
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover { color: var(--accent-dark); }

        /* Submit button */
        .btn-login {
            width: 100%;
            height: 50px;
            border-radius: 14px;
            border: none;
            background: linear-gradient(135deg, #5ba3d9 0%, #3a7fbf 50%, #2a6aaa 100%);
            color: white;
            font-family: 'Cairo', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            letter-spacing: 0.04em;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(74,144,196,0.35), 0 2px 6px rgba(74,144,196,0.2);
            transition: all 0.25s ease;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 60%);
            pointer-events: none;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(74,144,196,0.4), 0 4px 10px rgba(74,144,196,0.25);
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 4px 14px rgba(74,144,196,0.3);
        }

        /* Shimmer animation on button */
        .btn-login::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%   { left: -100%; }
            50%,100% { left: 150%; }
        }

        /* Error messages */
        .field-error {
            font-size: 0.72rem;
            color: #c0392b;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Status message */
        .status-msg {
            background: rgba(74,144,196,0.12);
            border: 1px solid rgba(74,144,196,0.25);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.8rem;
            color: var(--accent-dark);
            margin-bottom: 18px;
            text-align: center;
        }

        /* Divider */
        .or-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: rgba(90,122,148,0.5);
            font-size: 0.75rem;
        }

        .or-divider::before,
        .or-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(140,190,220,0.4);
        }

        /* Bottom link */
        .form-footer-link {
            text-align: center;
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-top: 20px;
        }

        .form-footer-link a {
            color: var(--accent-blue);
            text-decoration: none;
            font-weight: 500;
        }

        /* ═══ FLOATING FLOWERS ═══ */
        .deco-flower {
            position: fixed;
            font-size: 1.2rem;
            pointer-events: none;
            animation: flowerSway ease-in-out infinite;
            opacity: 0.55;
            z-index: 5;
        }

        @keyframes flowerSway {
            0%,100% { transform: rotate(-8deg) scale(1); }
            50%      { transform: rotate(8deg) scale(1.05); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-scene { grid-template-columns: 1fr; }
            .brand-side   { padding: 32px 24px 20px; }
            .brand-logo-wrap img { width: 160px; }
            .brand-name   { font-size: 1.8rem; }
            .brand-quote  { display: none; }
            .form-side    { padding: 20px 20px 40px; }
            .form-card    { padding: 32px 26px; }
        }
        /* --- Maximum Font Clarity Overrides --- */
        .text-xs { font-size: 0.95rem !important; line-height: 1.4 !important; font-weight: 700 !important; }
        .text-sm { font-size: 1.05rem !important; line-height: 1.5 !important; font-weight: 600 !important; }
        .text-base { font-size: 1.15rem !important; }
        .text-lg { font-size: 1.25rem !important; font-weight: 700 !important; }
        .text-xl { font-size: 1.45rem !important; font-weight: 800 !important; }
        
        .text-[10px] { font-size: 0.9rem !important; font-weight: 700 !important; }
        .text-[11px] { font-size: 0.925rem !important; font-weight: 700 !important; }
        
        /* Force all grey text to be very dark */
        .text-gray-400, .text-gray-500, .text-gray-600 { color: #1f2937 !important; font-weight: 600 !important; }
        .text-gray-700, .text-gray-800 { color: #000000 !important; font-weight: 700 !important; }
        
        /* Force robust weights everywhere */
        span, div, p, a, label, input, select, textarea, button {
            font-weight: 600 !important;
        }
        /* -------------------------------------- */
    </style>
</head>
<body>

<!-- SKY BACKGROUND -->
<div class="bg-scene"></div>
<div class="bg-overlay"></div>

<!-- Floating clouds -->
<div class="cloud c1"></div>
<div class="cloud c2"></div>
<div class="cloud c3"></div>
<div class="cloud c4"></div>

<!-- Floating petals -->
<div class="petal" style="left:8%;animation-duration:14s;animation-delay:0s;"></div>
<div class="petal" style="left:20%;animation-duration:19s;animation-delay:3s;"></div>
<div class="petal" style="left:45%;animation-duration:16s;animation-delay:7s;"></div>
<div class="petal" style="left:70%;animation-duration:22s;animation-delay:1s;"></div>
<div class="petal" style="left:85%;animation-duration:17s;animation-delay:11s;"></div>

<!-- Decorative flowers -->
<div class="deco-flower" style="top:12%;left:8%;animation-duration:7s;">🌸</div>
<div class="deco-flower" style="bottom:16%;left:12%;animation-duration:9s;animation-delay:2s;">🌼</div>
<div class="deco-flower" style="top:30%;right:5%;animation-duration:8s;animation-delay:4s;font-size:0.9rem;">✿</div>

<!-- ═══ MAIN SCENE ═══ -->
<div class="login-scene">

    <!-- LEFT: Brand -->
    <div class="brand-side">
        <div class="brand-logo-wrap">
            <img src="{{ asset('images/logo.png') }}" alt="Jenny's Atelier"
                 onerror="this.outerHTML='<div class=\'brand-fallback\'>Jenny\'s</div>'">
        </div>

        <div class="brand-tagline">
            <div class="brand-name">Jenny's Atelier</div>
            <div class="brand-arabic">تفصيل ملابس حريمي</div>
        </div>

        <div class="brand-line"></div>

        <div class="brand-quote">
            "كل فستان قصة جمال تُروى بخيوط من الحب والفن"
        </div>
    </div>

    <!-- RIGHT: Form -->
    <div class="form-side">
        <div class="form-card">

            <div class="form-header">
                <div class="form-greeting">أهلاً بك</div>
                <div class="form-title">تسجيل الدخول</div>
                <div class="form-subtitle">سجّلي دخولك لإدارة الأتيليه</div>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="status-msg">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="field-wrap">
                    <label class="field-label" for="email">البريد الإلكتروني</label>
                    <div class="field-input-wrap">
                        <span class="field-icon">✉</span>
                        <input
                            id="email"
                            class="field-input"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="example@atelier.com"
                            required
                            autofocus
                            autocomplete="username"
                        >
                    </div>
                    @error('email')
                        <div class="field-error">⚠ {{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="field-wrap">
                    <label class="field-label" for="password">كلمة المرور</label>
                    <div class="field-input-wrap">
                        <span class="field-icon">🔒</span>
                        <input
                            id="password"
                            class="field-input"
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                    </div>
                    @error('password')
                        <div class="field-error">⚠ {{ $message }}</div>
                    @enderror
                </div>

                <!-- Always Remember User Securely -->
                <input type="hidden" name="remember" value="on">

                <!-- Submit -->
                <button type="submit" class="btn-login">دخول ✦</button>

                @if (Route::has('register'))
                    <div class="form-footer-link">
                        حساب جديد؟ <a href="{{ route('register') }}">إنشاء حساب</a>
                    </div>
                @endif
            </form>

        </div>
    </div>

</div>

</body>
</html>
