<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <title>SIAP - Sistem Informasi Absensi Presensi | Masuk</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="shortcut icon" type="image/png" href="{{ asset('demo1/media/logos/Siap_Logo.png') }}"/>

    <!-- Global Stylesheets Bundle -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700"/>
    <link href="/absensi/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css"/>
    <link href="/absensi/css/style.bundle.css" rel="stylesheet" type="text/css"/>

        <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Poppins', sans-serif; min-height: 100vh; overflow-x: hidden; }

        .login-wrapper {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* LEFT PANEL */
        .login-left {
            flex: 1;
            background: linear-gradient(145deg, #0d2b6b 0%, #1a4fa0 40%, #1e7bc4 70%, #2aa8d8 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            position: relative;
            overflow: hidden;
        }
        .login-left::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 340px; height: 340px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            animation: pulseCircle 10s ease-in-out infinite alternate;
        }
        .login-left::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 260px; height: 260px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            animation: pulseCircle 8s ease-in-out infinite alternate-reverse;
        }

        @keyframes pulseCircle {
            0% { transform: scale(1) translate(0, 0); opacity: 0.5; }
            50% { transform: scale(1.12) translate(15px, -15px); opacity: 0.85; }
            100% { transform: scale(1) translate(0, 0); opacity: 0.5; }
        }

        .login-left-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 480px;
        }
        .login-left-content .badge-text {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(8px);
            color: #e0f4ff;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            border-radius: 50px;
            padding: 6px 18px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255,255,255,0.2);
            animation: slideDownFade 0.6s ease-out backwards;
            transition: all 0.3s ease;
        }
        .login-left-content .badge-text:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-2px);
        }
        .login-left-content h2 {
            color: #fff;
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.25;
            margin-bottom: 1rem;
            animation: slideDownFade 0.7s ease-out 0.1s backwards;
        }
        .login-left-content p {
            color: rgba(255,255,255,0.80);
            font-size: 0.9rem;
            line-height: 1.7;
            margin-bottom: 2rem;
            animation: fadeIn 0.8s ease-out 0.2s backwards;
        }
        .login-left-content .illus-img {
            width: 100%;
            max-width: 420px;
            filter: drop-shadow(0 15px 35px rgba(0,0,0,0.3));
            animation: floatHero 6s ease-in-out infinite, zoomIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.25s backwards;
            transition: transform 0.4s ease, filter 0.4s ease;
        }
        .login-left-content .illus-img:hover {
            transform: translateY(-8px) scale(1.02);
            filter: drop-shadow(0 25px 45px rgba(0,0,0,0.4));
        }

        @keyframes floatHero {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .feature-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-top: 1.8rem;
            animation: slideUpFade 0.8s ease-out 0.35s backwards;
        }
        .feature-pill {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 50px;
            padding: 6px 14px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: default;
        }
        .feature-pill:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-3px) scale(1.04);
            border-color: rgba(255,255,255,0.4);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        /* RIGHT PANEL */
        .login-right {
            width: 480px;
            min-width: 380px;
            background: #f5f8ff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 2.5rem 2rem 1.5rem;
            height: 100vh;
            overflow-y: auto;
        }
        .login-right-inner {
            width: 100%;
            max-width: 420px;
            animation: slideUpFade 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.15s backwards;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 1.25rem;
        }
        .login-logo img {
            width: 230px;
            height: auto;
            max-width: 100%;
            transition: transform 0.3s ease;
        }
        .login-logo img:hover {
            transform: scale(1.03);
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(13,43,107,0.12);
            padding: 1.75rem 1.5rem;
            transition: box-shadow 0.3s ease, transform 0.3s ease;
        }
        .login-card:hover {
            box-shadow: 0 16px 50px rgba(13,43,107,0.16);
        }

        /* INPUT & BUTTON INTERACTIONS */
        .form-control.form-control-solid {
            transition: all 0.25s ease !important;
        }
        .form-control.form-control-solid:focus {
            background-color: #ffffff !important;
            border-color: #1b84ff !important;
            box-shadow: 0 0 0 3px rgba(27, 132, 255, 0.15) !important;
        }
        .btn-primary {
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(27, 132, 255, 0.35) !important;
        }
        .btn-primary:active {
            transform: translateY(0);
        }

        /* KEYFRAMES */
        @keyframes slideDownFade {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideUpFade {
            0% { opacity: 0; transform: translateY(25px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        @keyframes zoomIn {
            0% { opacity: 0; transform: scale(0.92); }
            100% { opacity: 1; transform: scale(1); }
        }

        /* RESPONSIVE MEDIA QUERIES */
        @media (max-width: 960px) {
            .login-left { display: none; }
            .login-right { 
                width: 100%; 
                min-width: unset; 
                background: #f5f8ff; 
                height: 100vh;
                padding: 2rem 1.25rem;
                justify-content: center;
            }
            .login-card {
                padding: 1.5rem 1.25rem;
                border-radius: 16px;
            }
            .login-logo img {
                width: 200px;
            }
        }
        @media (max-height: 700px) {
            .login-right {
                justify-content: flex-start;
                padding-top: 1.5rem;
            }
        }
    </style>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body id="kt_body">

    <div class="login-wrapper">
        <!-- LEFT: Illustration Panel -->
        <div class="login-left">
            <div class="login-left-content">
                <div class="badge-text"><i data-lucide="fingerprint" style="width: 14px; height: 14px; vertical-align: -2px; margin-right: 4px;"></i> SIAP - Sistem informasi absensi presensi</div>
                <h2>Absensi Siswa <br>Lebih Mudah & Modern</h2>
                <p>Rekam kehadiran secara real-time menggunakan teknologi fingerprint. Cepat, akurat, dan terintegrasi penuh dengan sistem sekolah.</p>
                <img class="illus-img" src="{{ asset('demo1/media/illustrations/Hero_Visual.png') }}" alt="Fingerprint Absensi Sekolah" />
                <div class="feature-pills">
                    <div class="feature-pill"><i data-lucide="fingerprint" style="width: 16px; height: 16px;"></i> Fingerprint Scanner</div>
                    <div class="feature-pill"><i data-lucide="school" style="width: 16px; height: 16px;"></i> Absensi Sekolah</div>
                    <div class="feature-pill"><i data-lucide="bar-chart-3" style="width: 16px; height: 16px;"></i> Laporan Real-time</div>
                    <div class="feature-pill"><i data-lucide="shield-check" style="width: 16px; height: 16px;"></i> Data Aman</div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Login Form Panel -->
        <div class="login-right">
            <div class="login-right-inner">
                <div class="login-logo">
                    <a href="{{ url('/') }}">
                        <img alt="Logo SIAP" src="{{ asset('demo1/media/logos/Siap_Logo_With_Text.png') }}" />
                    </a>
                </div>
                <div class="login-card">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Global Javascript Bundle -->
    <script src="/absensi/plugins/global/plugins.bundle.js"></script>
    <script src="/absensi/js/scripts.bundle.js"></script>
    <script>
        lucide.createIcons();
    </script>
    @yield('scripts')

</body>
</html>

