<x-base-layout>
    @php
        $backUrl = ($userRole === 'guru' || $userRole === 'kesiswaan') ? url('/') : theme()->getPageUrl('absensi/master/guru');
        $backText = ($userRole === 'guru' || $userRole === 'kesiswaan') ? 'Kembali ke Dashboard' : 'Kembali ke Daftar Guru';
    @endphp
    <!--begin::Toolbar-->
    @include('pages.absensi._partials.toolbar', [
        'customBreadcrumbs' => [
            ['title' => 'Home', 'path' => 'index', 'active' => false],
            ['title' => 'Master Data', 'path' => '', 'active' => false],
            ['title' => 'Guru', 'path' => 'absensi/master/guru', 'active' => false],
            ['title' => $guru ? $guru->nama : ($user->name ?? 'Profil Guru'), 'path' => '', 'active' => true],
        ],
        'toolbarActions' => '
            <a href="' . $backUrl . '" class="btn btn-sm btn-light me-2">
                ' . theme()->getSvgIcon("icons/duotune/arrows/arr063.svg", "svg-icon-4") . ' ' . $backText . '
            </a>'
    ])
    <!--end::Toolbar-->

    <!--begin::Navbar-->
    @include('profile.partials.profile-header', [
        'user' => $user,
        'info' => $info,
        'stats' => $stats,
        'userRole' => $userRole,
        'completionRate' => $completionRate,
        'guru' => $guru
    ])
    <!--end::Navbar-->

    <!--begin::Tab Content-->
    <div class="tab-content" id="profileTabContent">
        <!--begin::Tab Pane - Informasi Dasar (Active by Default)-->
        <div class="tab-pane fade show active" id="tab_jadwal_dan_kelas" role="tabpanel">
            <!-- Biodata / Details cards -->
            @include('profile.partials.profile-info-card', [
                'user' => $user,
                'info' => $info,
                'userRole' => $userRole,
                'guru' => $guru,
                'kelasDiwali' => $kelasDiwali
            ])
        </div>
        <!--end::Tab Pane - Informasi Dasar-->

        <!--begin::Tab Pane - Jadwal Mengajar-->
        <div class="tab-pane fade" id="tab_jadwal_mengajar" role="tabpanel">
            @include('profile.partials.profile-schedule', [
                'schedules' => $schedules,
                'semesters' => $semesters,
                'userRole' => $userRole
            ])
        </div>
        <!--end::Tab Pane - Jadwal Mengajar-->

        <!--begin::Tab Pane - Edit Profil-->
        <div class="tab-pane fade" id="tab_pengaturan" role="tabpanel">
            @include('profile.partials.profile-settings', [
                'user' => $user,
                'info' => $info,
                'userRole' => $userRole,
                'guru' => $guru
            ])
        </div>
        <!--end::Tab Pane - Edit Profil-->
    </div>
    <!--end::Tab Content-->

    @section('scripts')
        @if(session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'OK',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
            </script>
        @endif

        @if(session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'Tutup',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });
            </script>
        @endif

        @if ($errors->any())
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    html: '<ul class="text-start">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                    confirmButtonText: 'Perbaiki',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });
            </script>
        @endif
    @endsection
</x-base-layout>
