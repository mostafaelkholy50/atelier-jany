<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Atelier Jany</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Check local storage for desktop sidebar state before rendering to prevent flashing
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    </script>

    <style>
        :root {
            --sky-light: #ddeeff;
            --sky-mid: #b8d8f0;
            --sky-deep: #8ebfdf;
            --cloud-white: #ffffff;
            --mist: rgba(255, 255, 255, 0.55);
            --accent-blue: #4a90c4;
            --accent-dark: #1c4a6e;
            --petal: #c9dff2;
            --gold: #b9985a;
            --text-main: #111827; /* Near black for maximum clarity */
            --text-muted: #374151; /* Darker muted text */
            --sidebar-w: 260px;
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

        @font-face {
            font-family: 'Cairo';
            src: url('{{ asset('Font/Cairo-Bold.ttf') }}') format('truetype');
            font-weight: 700;
            font-style: normal;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Cairo', sans-serif;
            font-weight: 600;
            line-height: 1.8;
            color: #000000;
            min-height: 100vh;
            overflow-x: hidden;
            background: linear-gradient(175deg, #cce4f5 0%, #ddeeff 30%, #edf6ff 60%, #f7fbff 100%);
            -webkit-font-smoothing: initial;
            -moz-osx-font-smoothing: initial;
            font-size: 18px;
            text-rendering: optimizeLegibility;
        }

        .sky-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .cloud-layer {
            position: absolute;
            width: 200%;
            height: 100%;
            background-repeat: repeat-x;
            background-size: auto 100%;
        }

        .cloud-layer-1 {
            background-image: url('{{ asset('images/background.png') }}');
            background-size: cover;
            background-position: center top;
            width: 100%;
            opacity: 0.85;
        }

        .cloud-puff {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(ellipse at center, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.3) 60%, transparent 80%);
            animation: floatCloud linear infinite;
            pointer-events: none;
        }

        @keyframes floatCloud {
            0% {
                transform: translateX(0) translateY(0);
            }

            33% {
                transform: translateX(12px) translateY(-6px);
            }

            66% {
                transform: translateX(-8px) translateY(4px);
            }

            100% {
                transform: translateX(0) translateY(0);
            }
        }

        .sidebar {
            position: fixed;
            right: 0;
            top: 0;
            width: var(--sidebar-w);
            height: 100vh;
            z-index: 50;
            display: flex;
            flex-direction: column;
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(20px) saturate(1.4);
            -webkit-backdrop-filter: blur(20px) saturate(1.4);
            border-left: 1px solid rgba(180, 215, 240, 0.5);
            box-shadow: -8px 0 40px rgba(74, 144, 196, 0.08), -2px 0 12px rgba(255, 255, 255, 0.6) inset;
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-logo {
            padding: 24px 20px 16px;
            border-bottom: 1px solid rgba(140, 190, 220, 0.25);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .sidebar-logo img {
            width: 50px;
            height: auto;
            filter: drop-shadow(0 4px 12px rgba(74, 144, 196, 0.2));
        }

        .sidebar-logo-text {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--accent-dark);
            letter-spacing: 0.03em;
            text-align: center;
            line-height: 1.3;
        }

        .sidebar-logo-sub {
            font-family: 'Cairo', sans-serif;
            font-size: 0.72rem;
            font-weight: 500;
            color: var(--text-muted);
            letter-spacing: 0.04em;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 16px 12px;
            /* Hide scrollbar for IE, Edge and Firefox */
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .sidebar-nav::-webkit-scrollbar {
            display: none;
        }

        .nav-section-label {
            font-size: 0.65rem;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 12px 10px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 12px;
            margin-bottom: 3px;
            color: var(--text-muted);
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            position: relative;
            cursor: pointer;
        }

        .nav-item:hover {
            background: rgba(178, 215, 240, 0.35);
            color: var(--accent-dark);
        }

        .nav-item.active {
            background: linear-gradient(135deg, rgba(74, 144, 196, 0.18) 0%, rgba(178, 215, 240, 0.25) 100%);
            color: var(--accent-dark);
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(74, 144, 196, 0.12);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: var(--accent-blue);
            border-radius: 3px 0 0 3px;
        }

        .nav-icon {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            background: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
            flex-shrink: 0;
            transition: all 0.2s ease;
            box-shadow: 0 1px 4px rgba(74, 144, 196, 0.1);
        }

        .nav-item:hover .nav-icon,
        .nav-item.active .nav-icon {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 8px rgba(74, 144, 196, 0.18);
        }

        .sidebar-footer {
            padding: 14px 12px;
            border-top: 1px solid rgba(140, 190, 220, 0.25);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(178, 215, 240, 0.2);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .user-card:hover {
            background: rgba(178, 215, 240, 0.35);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--sky-mid), var(--accent-blue));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.85rem;
            font-weight: 600;
            flex-shrink: 0;
        }

        .user-name {
            font-size: 0.83rem;
            font-weight: 500;
            color: var(--text-main);
            flex: 1;
        }

        .user-role {
            font-size: 0.68rem;
            color: var(--text-muted);
        }

        .topbar {
            position: fixed;
            top: 0;
            right: var(--sidebar-w);
            left: 0;
            height: 64px;
            z-index: 40;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(16px) saturate(1.3);
            -webkit-backdrop-filter: blur(16px) saturate(1.3);
            border-bottom: 1px solid rgba(180, 215, 240, 0.35);
            box-shadow: 0 4px 20px rgba(74, 144, 196, 0.06);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .topbar-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.35rem;
            font-weight: 600;
            color: var(--accent-dark);
            letter-spacing: 0.01em;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.75);
            border: 1px solid rgba(180, 215, 240, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            color: var(--text-muted);
            font-size: 1rem;
            position: relative;
        }

        .topbar-btn:hover {
            background: white;
            color: var(--accent-blue);
            box-shadow: 0 3px 12px rgba(74, 144, 196, 0.15);
            transform: translateY(-1px);
        }

        .badge {
            position: absolute;
            top: 6px;
            left: 6px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #e05a8a;
            border: 2px solid white;
        }

        .main-wrapper {
            margin-right: var(--sidebar-w);
            padding-top: 64px;
            min-height: 100vh;
            position: relative;
            z-index: 1;
            transition: margin-right 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-content {
            padding: 28px 32px;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 16px 12px;
            }
            .topbar {
                padding: 0 16px;
                right: 0 !important;
            }
            .topbar-title {
                font-size: 1.15rem;
            }
        }

        @media (max-width: 480px) {
            .topbar-title {
                font-size: 1rem;
            }
        }

        .floating-petal {
            position: fixed;
            width: 6px;
            height: 6px;
            border-radius: 50% 0 50% 0;
            background: rgba(178, 215, 240, 0.6);
            pointer-events: none;
            animation: petalFloat linear infinite;
            z-index: 0;
        }

        @keyframes petalFloat {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 0.6;
            }

            100% {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.68);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(180, 215, 240, 0.4);
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(74, 144, 196, 0.08), 0 1px 4px rgba(255, 255, 255, 0.8) inset;
        }

        .cloud-divider {
            height: 1px;
            background: linear-gradient(to left, transparent, rgba(140, 190, 220, 0.5), transparent);
            margin: 8px 0;
        }

        /* Desktop Sidebar Toggle Styles */
        html.sidebar-collapsed .sidebar {
            transform: translateX(100%);
        }

        html.sidebar-collapsed .main-wrapper {
            margin-right: 0;
        }

        html.sidebar-collapsed .topbar {
            right: 0;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(100%);
            }

            html.sidebar-mobile-open .sidebar {
                transform: translateX(0);
                box-shadow: -10px 0 50px rgba(0, 0, 0, 0.15);
            }

            .main-wrapper {
                margin-right: 0 !important;
            }

            .topbar {
                right: 0 !important;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }        /* --- Maximum Font Clarity Overrides --- */
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
        
        th {
            font-weight: 800 !important;
            color: #000000 !important;
            background-color: rgba(0,0,0,0.05) !important;
        }

        td {
            color: #000000 !important;
        }
        /* -------------------------------------- */
    </style>
</head>

<body>

    <div class="sky-canvas">
        <div class="cloud-layer cloud-layer-1"></div>
        <div class="cloud-puff" style="width:180px;height:90px;top:8%;left:15%;animation-duration:14s;opacity:0.4;">
        </div>
        <div class="cloud-puff" style="width:120px;height:60px;top:20%;left:55%;animation-duration:18s;opacity:0.3;">
        </div>
        <div class="cloud-puff" style="width:250px;height:110px;top:5%;left:70%;animation-duration:22s;opacity:0.25;">
        </div>
    </div>

    <div class="floating-petal" style="left:10%;animation-duration:20s;animation-delay:0s;"></div>
    <div class="floating-petal" style="left:25%;animation-duration:26s;animation-delay:5s;"></div>
    <div class="floating-petal" style="left:60%;animation-duration:18s;animation-delay:9s;"></div>
    <div class="floating-petal" style="left:80%;animation-duration:23s;animation-delay:3s;"></div>



    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Jenny's Atelier" onerror="this.style.display='none'">
            <div class="sidebar-logo-text">Jenny's Atelier</div>
            <div class="sidebar-logo-sub">تفصيل ملابس حريمي</div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">الرئيسية</div>

            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <div class="nav-icon">🏠</div>
                <span>لوحة التحكم</span>
            </a>

            <div class="cloud-divider"></div>
            <div class="nav-section-label">إدارة الأعمال</div>

            <a href="{{ route('appointments.index') }}"
                class="nav-item {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                <div class="nav-icon">📅</div>
                <span>مواعيد التسليم</span>
            </a>

            <a href="{{ route('orders.index') }}" class="nav-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <div class="nav-icon">📋</div>
                <span>الطلبات</span>
            </a>
            
            <a href="{{ route('clients.index') }}"
                class="nav-item {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <div class="nav-icon">👗</div>
                <span>العميلات</span>
            </a>

            <a href="{{ route('categories.index') }}"
                class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <div class="nav-icon">📏</div>
                <span>أنواع الموديلات</span>
            </a>


            <div class="cloud-divider"></div>
            <div class="nav-section-label">الإعدادات</div>

            <a href="{{ route('profile.edit') }}"
                class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <div class="nav-icon">⚙️</div>
                <span>الإعدادات</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">{{ substr(Auth::user()->name ?? 'J', 0, 1) }}</div>
                <div>
                    <div class="user-name">{{ Auth::user()->name ?? 'المستخدمة' }}</div>
                    <div class="user-role">مديرة الأتيليه</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin-right:auto">
                    @csrf
                    <button type="submit"
                        style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:0.9rem;"
                        title="تسجيل الخروج">↩</button>
                </form>
            </div>
        </div>
    </aside>

    <header class="topbar">
        <div style="display: flex; align-items: center; gap: 14px;">
            <button onclick="toggleSidebar()"
                style="background:none; border:none; cursor:pointer; color:var(--accent-dark); font-size:1.4rem; outline:none; display:flex; align-items:center; justify-content:center; padding-bottom:3px; transition:transform 0.2s;"
                onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                ☰
            </button>
            @isset($header)
                <div class="topbar-title">{{ $header }}</div>
            @else
                <div class="topbar-title">Jenny's Atelier ✦</div>
            @endisset
        </div>
    </header>

    <div class="main-wrapper">
        <main class="main-content fade-in">
            <x-success-alert />
            <x-validation-errors />
            {{ $slot }}
        </main>
    </div>

    <!-- Image Modal Overlay -->
    <div id="imageModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/95 backdrop-blur-md cursor-zoom-out" onclick="closeImageModal()">
        <button onclick="closeImageModal()" class="absolute top-6 right-6 text-white text-5xl font-light hover:text-gray-300 transition-colors">&times;</button>
        <img id="modalImg" src="" class="max-w-[95%] max-h-[95vh] object-contain rounded-sm shadow-2xl transition-all duration-300 transform scale-95 opacity-0" onclick="event.stopPropagation()">
    </div>

    <script>
        function openImageModal(src) {
            const modal = document.getElementById('imageModal');
            const img = document.getElementById('modalImg');
            img.src = src;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Stop scrolling
            setTimeout(() => {
                img.classList.remove('scale-95', 'opacity-0');
                img.classList.add('scale-100', 'opacity-100');
            }, 50);
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            const img = document.getElementById('modalImg');
            img.classList.remove('scale-100', 'opacity-100');
            img.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = ''; // Resume scrolling
            }, 300);
        }

        function toggleSidebar() {
            if (window.innerWidth <= 768) {
                // Mobile: toggle specific mobile class
                document.documentElement.classList.toggle('sidebar-mobile-open');
            } else {
                // Desktop: toggle collapsed state and save preferrence
                const isCollapsed = document.documentElement.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebar-collapsed', isCollapsed);
            }
        }
    </script>

</body>

</html>
