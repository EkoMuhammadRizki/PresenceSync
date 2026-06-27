<x-base-layout>
    @php
        $backUrl = ($userRole === 'siswa') ? route('siswa.dashboard') : theme()->getPageUrl('absensi/master/siswa');
        $backText = ($userRole === 'siswa') ? 'Kembali ke Dashboard' : 'Kembali ke Daftar Siswa';

        if (request('back') === 'kehadiran') {
            $backUrl = theme()->getPageUrl('absensi/kehadiran');
            $backText = 'Kembali ke Kehadiran Siswa';
        } elseif (request('back') === 'dashboard') {
            $backUrl = theme()->getPageUrl('absensi/dashboard');
            $backText = 'Kembali ke Dashboard';
        }
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
        'siswa' => $siswa
    ])
    <!--end::Navbar-->

    <!--begin::Tab Content-->
    <div class="tab-content" id="profileTabContent">
        <!--begin::Tab Pane - Informasi Siswa (Active by Default)-->
        <div class="tab-pane fade show active" id="tab_riwayat_kehadiran" role="tabpanel">
            <!-- Biodata Info Cards -->
            @include('profile.partials.profile-info-card', [
                'user' => $user,
                'info' => $info,
                'userRole' => $userRole,
                'siswa' => $siswa
            ])

            <!-- Kelas & Wali Kelas Info Row -->
            <div class="row g-5 g-xxl-8 mt-5">
                <!--begin::Col - Wali Kelas & Kelas Info-->
                <div class="col-xl-6">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 text-dark">Informasi Kelas & Wali</span>
                                <span class="text-muted mt-1 fw-bold fs-7">Detail kelas yang sedang ditempati</span>
                            </h3>
                        </div>
                        <div class="card-body pt-3">
                            <!-- Kelas -->
                            <div class="d-flex align-items-center mb-7">
                                <div class="symbol symbol-50px me-5">
                                    <div class="symbol-label bg-light-primary text-primary fw-bolder fs-5">
                                        {{ $siswa->kelas->tingkat ?? '-' }}
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="text-muted fw-bold d-block fs-7">Kelas</span>
                                    <span class="text-gray-800 fw-bolder fs-6">{{ $siswa->kelas->nama_lengkap ?? 'Belum Ditentukan' }}</span>
                                </div>
                            </div>

                            <!-- Wali Kelas -->
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-50px me-5">
                                    <div class="symbol-label bg-light-success text-success fw-bolder fs-5">
                                        W
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="text-muted fw-bold d-block fs-7">Wali Kelas</span>
                                    <span class="text-gray-800 fw-bolder fs-6">{{ $siswa->kelas->guru->nama ?? 'Belum Ditentukan' }}</span>
                                    @if(isset($siswa->kelas->guru->nip))
                                        <span class="text-muted d-block fs-7">NIP: {{ $siswa->kelas->guru->nip }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col-->

                <!--begin::Col - Subject list-->
                <div class="col-xl-6">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 text-dark">Mata Pelajaran Diikuti</span>
                                <span class="text-muted mt-1 fw-bold fs-7">Daftar mata pelajaran semester ini</span>
                            </h3>
                        </div>
                        <div class="card-body pt-3" style="max-height: 250px; overflow-y: auto;">
                            @php
                                $uniqueMapels = $schedules->map(function($s) {
                                    return $s->mataPelajaran;
                                })->filter()->unique('id');
                            @endphp

                            @forelse($uniqueMapels as $mapel)
                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-35px me-3">
                                        <div class="symbol-label bg-light-info text-info fw-bold fs-6">
                                            {{ substr($mapel->nama, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="text-gray-800 fw-bolder fs-6">{{ $mapel->nama }}</span>
                                        <span class="text-muted d-block fs-7">Kode: {{ $mapel->kode ?? '-' }} • Guru: {{ $mapel->guru->nama ?? '-' }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-5">
                                    Tidak ada mata pelajaran terdaftar.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <!--end::Col-->
            </div>
        </div>
        <!--end::Tab Pane - Informasi Siswa-->

        <!--begin::Tab Pane - Riwayat Kehadiran-->
        <div class="tab-pane fade" id="tab_riwayat" role="tabpanel">
            <div class="card mt-2">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <h3 class="fw-bolder">Riwayat Kehadiran Terakhir</h3>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_profile_riwayat_table">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="w-50px">No</th>
                                    <th class="min-w-120px">Tanggal</th>
                                    <th class="min-w-100px">Jam Masuk</th>
                                    <th class="min-w-100px">Jam Pulang</th>
                                    <th class="min-w-120px">Status</th>
                                    <th class="min-w-200px">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @foreach($siswa->kehadirans()->latest('tanggal')->take(10)->get() as $index => $kh)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $kh->tanggal ? $kh->tanggal->format('d M Y') : '-' }}</td>
                                        <td>{{ $kh->jam_masuk ?? '-' }}</td>
                                        <td>{{ $kh->jam_pulang ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $kh->badge_class }} fw-bolder">{{ ucfirst($kh->status) }}</span>
                                        </td>
                                        <td>{{ $kh->keterangan ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Tab Pane - Riwayat Kehadiran-->

        <!--begin::Tab Pane - Edit Profil-->
        <div class="tab-pane fade" id="tab_pengaturan" role="tabpanel">
            @include('profile.partials.profile-settings', [
                'user' => $user,
                'info' => $info,
                'userRole' => $userRole,
                'siswa' => $siswa,
                'kelas' => $kelas
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
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.jQuery && $.fn.dataTable) {
                    $('#kt_profile_riwayat_table').DataTable({
                        dom: "<'table-responsive'tr><'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'li><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>",
                        info: true,
                        order: [],
                        pageLength: 5,
                        lengthChange: true,
                        columnDefs: [{ orderable: false, targets: [0] }],
                        language: {
                            emptyTable: "Belum ada riwayat kehadiran tercatat.",
                            infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                            zeroRecords: "Tidak ada riwayat kehadiran yang sesuai"
                        }
                    });
                }
            });
        </script>
    @endsection
</x-base-layout>
