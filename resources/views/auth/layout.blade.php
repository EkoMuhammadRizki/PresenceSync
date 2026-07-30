<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <title>PresenceSync | Masuk</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="shortcut icon" href="{{ asset('absensi/media/logos/favicon.ico') }}"/>

    <!-- Global Stylesheets Bundle -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700"/>
    <link href="/absensi/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css"/>
    <link href="/absensi/css/style.bundle.css" rel="stylesheet" type="text/css"/>
</head>
<body id="kt_body" class="bg-body">

    <div class="d-flex flex-column flex-root" style="min-height: 100vh;">
        <!--begin::Authentication - Sign-in -->
        <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed" style="background-image: url('{{ asset(theme()->getIllustrationUrl('14.png')) }}'); min-height: 100vh;">
            
            <!--begin::Header / Logo-->
            <div class="d-flex flex-center pt-10 pt-lg-15">
                <a href="{{ url('/') }}" class="text-decoration-none">
                    <h1 class="text-dark fw-bolder mb-0" style="font-size: 2.25rem !important; letter-spacing: -0.5px; font-weight: 700;">PresenceSync</h1>
                </a>
            </div>
            <!--end::Header / Logo-->

            <!--begin::Body / Card Wrapper-->
            <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
                <!--begin::Card Container-->
                <div class="w-100 bg-body rounded shadow-sm p-10 p-lg-15 mx-auto" style="max-width: 450px !important;">
                    @yield('content')
                </div>
                <!--end::Card Container-->
            </div>
            <!--end::Body / Card Wrapper-->

        </div>
        <!--end::Authentication - Sign-in-->
    </div>

    <!-- Global Javascript Bundle -->
    <script src="/absensi/plugins/global/plugins.bundle.js"></script>
    <script src="/absensi/js/scripts.bundle.js"></script>
    @yield('scripts')

</body>
</html>
