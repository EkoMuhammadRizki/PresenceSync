<x-base-layout>
    @php
        $backUrl = ($userRole === 'guru' || $userRole === 'kesiswaan') ? url('/') : theme()->getPageUrl('absensi/master/guru');
        $backText = ($userRole === 'guru' || $userRole === 'kesiswaan') ? 'Kembali ke Dashboard' : 'Kembali ke Daftar Guru';
    @endphp
    <!--begin::Toolbar-->
    @include('pages.absensi._partials.toolbar', [
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
                'guru' => $guru
            ])

            <!-- Kelas yang Diwali Card -->
            <div class="card mb-5 mb-xl-8 mt-5">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <h3 class="fw-bolder">Kelas yang Diwali</h3>
                    </div>
                </div>
                <div class="card-body py-4">
                    @forelse($kelasDiwali as $kelasItem)
                        <div class="d-flex align-items-center mb-7">
                            <div class="symbol symbol-50px me-5">
                                <div class="symbol-label bg-light-primary text-primary fw-bolder fs-5">
                                    {{ $kelasItem->tingkat }}
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-dark fw-bolder fs-6">{{ $kelasItem->nama_lengkap }}</span>
                                <span class="text-muted d-block fw-bold">
                                    {{ $kelasItem->siswas_count }} Siswa • Status: {{ ucfirst($kelasItem->status) }}
                                </span>
                            </div>
                            <a href="{{ url('absensi/master/kelas/pembagian/' . $kelasItem->id) }}" class="btn btn-sm btn-light-primary">Lihat Detail Kelas</a>
                        </div>
                    @empty
                        <div class="text-center text-muted py-10">
                            Guru ini tidak/belum menjadi Wali Kelas di kelas manapun.
                        </div>
                    @endforelse
                </div>
            </div>
            <!--end::Card - Kelas yang Diwali-->
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
