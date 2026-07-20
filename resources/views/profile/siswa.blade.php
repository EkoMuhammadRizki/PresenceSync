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
        <div class="tab-pane fade {{ request()->has('periode') ? '' : 'show active' }}" id="tab_riwayat_kehadiran" role="tabpanel">
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
        <div class="tab-pane fade {{ request()->has('periode') ? 'show active' : '' }}" id="tab_riwayat" role="tabpanel">
            <div class="card card-flush shadow-sm">
                <!-- Title bar with blue background -->
                <div class="card-header bg-primary py-3 rounded-top">
                    <div class="card-title text-white fw-bolder fs-5 m-0 d-flex align-items-center gap-2">
                        <i class="bi bi-journal-text text-white fs-4"></i> Daftar Kehadiran
                    </div>
                </div>

                <!-- Filter Toolbar -->
                <div class="card-body py-4 border-bottom">
                    <form method="GET" action="{{ route('profil-siswa.show') }}" id="filter_form" class="d-flex align-items-center flex-wrap gap-5 justify-content-between">
                        <input type="hidden" name="id" value="{{ $siswa->id }}" />
                        <input type="hidden" name="back" value="{{ request('back') }}" />
                        <div class="d-flex align-items-center gap-3">
                            <label class="form-label fw-bold mb-0 me-2 text-nowrap">Periode:</label>
                            <select name="periode" class="form-select form-select-solid form-select-sm w-180px" onchange="document.getElementById('filter_form').submit()">
                                @php
                                    $startMonth = \Carbon\Carbon::now()->startOfMonth()->subMonths(6);
                                @endphp
                                @for ($i = 0; $i < 13; $i++)
                                    @php
                                        $pVal = $startMonth->format('Ym');
                                        $pLabel = $startMonth->isoFormat('MMMM Y');
                                        $startMonth->addMonth();
                                    @endphp
                                    <option value="{{ $pVal }}" {{ $periode == $pVal ? 'selected' : '' }}>
                                        {{ $pLabel }}
                                    </option>
                                @endfor
                            </select>
                            
                            @if($periode)
                                @php
                                    $selectedMonthName = \Carbon\Carbon::createFromDate(substr($periode, 0, 4), substr($periode, 4, 2), 1)->isoFormat('MMMM Y');
                                @endphp
                                <div class="d-flex align-items-center bg-light-primary rounded border border-primary border-dashed px-3 py-1 fs-7 text-primary fw-bolder">
                                    Periode: {{ $selectedMonthName }}
                                    <a href="{{ route('profil-siswa.show', ['id' => $siswa->id, 'back' => request('back')]) }}" class="btn btn-icon btn-xs btn-active-color-primary ms-2 text-primary p-0">✗</a>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('siswa.dashboard.export', ['periode' => $periode, 'siswa_id' => $siswa->id]) }}" class="btn btn-light-success btn-sm btn-md-md">
                                {!! theme()->getSvgIcon("icons/duotune/files/fil021.svg", "svg-icon-2") !!}
                                <span class="d-none d-sm-inline">Ekspor Daftar Kehadiran</span>
                                <span class="d-inline d-sm-none">Ekspor</span>
                            </a>

                            <div class="text-gray-600 fs-7 fw-bold" id="showing_count_label">
                                Menampilkan 1-{{ $daysInMonth }} dari {{ $daysInMonth }} data
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Table Container -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle gs-4 gy-3 mb-0" id="kt_table_kehadiran_siswa">
                            <thead>
                                <tr class="bg-light fw-bolder fs-7 text-uppercase text-gray-800 text-center border-bottom border-gray-300">
                                    <th class="w-50px border-end">No</th>
                                    <th class="min-w-100px border-end">NISN</th>
                                    <th class="min-w-150px border-end">Nama</th>
                                    <th class="w-150px border-end">Tanggal</th>
                                    <th class="w-80px border-end">Msk/Lbr</th>
                                    <th class="w-100px border-end">Masuk Jam</th>
                                    <th class="min-w-120px border-end">Keterangan</th>
                                </tr>
                            </thead>

                            <tbody class="text-gray-700 fw-bold fs-7">
                                @foreach ($attendanceRows as $row)
                                    <tr class="{{ $row['is_libur'] ? 'bg-light-danger text-muted' : '' }} border-bottom border-gray-200">
                                        <td class="text-center border-end">{{ $row['no'] }}</td>
                                        <td class="border-end">{{ $row['nisn'] }}</td>
                                        <td class="border-end">{{ $row['nama'] }}</td>
                                        <td class="border-end">{{ $row['tanggal'] }}</td>
                                        <td class="text-center border-end">
                                            @if ($row['msk_lbr'] === '✓')
                                                <span class="text-success fw-bolder fs-5">✓</span>
                                            @elseif ($row['msk_lbr'] === '✗')
                                                <span class="text-danger fw-bolder fs-5">✗</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center border-end text-danger">{{ $row['msk_jam'] ?: '-' }}</td>
                                        <td class="border-end">{{ $row['keterangan'] ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Tab Pane - Riwayat Kehadiran-->

        <!--begin::Tab Pane - Laporan Pengaduan-->
        <div class="tab-pane fade" id="tab_pengaduan_sekretaris" role="tabpanel">
            <div class="card card-flush shadow-sm">
                <!-- Title bar with blue background -->
                <div class="card-header bg-primary py-3 rounded-top">
                    <div class="card-title text-white fw-bolder fs-5 m-0 d-flex align-items-center gap-2">
                        <i class="bi bi-journal-text text-white fs-4"></i> Daftar Pengaduan
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle gs-4 gy-3 mb-0" id="table_pengaduan">
                            <thead>
                                <tr class="bg-light fw-bolder fs-7 text-uppercase text-gray-800 text-center border-bottom border-gray-300">
                                    <th class="w-50px border-end">No</th>
                                    <th class="min-w-150px border-end">Tanggal</th>
                                    <th class="min-w-300px border-end">Deskripsi Isi Pengaduan</th>
                                    <th class="w-150px border-end">Bukti</th>
                                    <th class="min-w-150px border-end">Tanggal Input</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 fw-bold fs-7">
                                @forelse ($pengaduans as $index => $row)
                                    @php
                                        $tanggalFormatted = $row->tanggal->isoFormat('ddd, DD MMMM Y');
                                    @endphp
                                    <tr>
                                        <td class="text-center border-end">{{ $index + 1 }}</td>
                                        <td class="border-end">{{ $tanggalFormatted }}</td>
                                        <td class="border-end text-wrap">{{ $row->deskripsi }}</td>
                                        <td class="text-center border-end">
                                            @if($row->bukti)
                                                <button type="button" class="btn btn-light-info btn-sm btn-view-bukti" data-src="{{ asset('storage/' . $row->bukti) }}">
                                                    <i class="bi bi-image me-1"></i> Lihat Bukti
                                                </button>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="border-end text-center">{{ $row->created_at->format('d-m-Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-8">Belum ada data pengaduan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Tab Pane - Laporan Pengaduan-->

        <!--begin::Tab Pane - Informasi Orang Tua-->
        <div class="tab-pane fade" id="tab_orang_tua" role="tabpanel">
            <div class="card mt-2 shadow-sm">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <h3 class="fw-bolder">Informasi Orang Tua</h3>
                    </div>
                </div>
                <div class="card-body py-4">
                    @if ($parentProfile)
                        <div class="row g-9">
                            <!-- Profil Ayah -->
                            <div class="col-md-6 border-end-md pe-md-8">
                                <div class="d-flex align-items-center mb-6">
                                    <div class="symbol symbol-35px symbol-circle me-3 bg-light-primary text-primary d-flex align-items-center justify-content-center fw-boldest p-2">
                                        <i class="bi bi-person-fill text-primary fs-3"></i>
                                    </div>
                                    <h4 class="text-gray-800 fw-boldest mb-0">Profil Ayah</h4>
                                </div>

                                <table class="table align-middle table-row-dashed fs-6 gy-4">
                                    <tbody>
                                        <tr>
                                            <td class="text-gray-400 fw-bold w-150px">NIK Ayah</td>
                                            <td class="text-gray-800 fw-bolder">{{ $parentProfile->nik_ayah ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-400 fw-bold">Nama Ayah</td>
                                            <td class="text-gray-800 fw-bolder">{{ $parentProfile->nama_ayah ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-400 fw-bold">Pekerjaan</td>
                                            <td class="text-gray-800 fw-bolder">{{ $parentProfile->pekerjaan_ayah ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-400 fw-bold">Ket. Pekerjaan</td>
                                            <td class="text-gray-800 fw-bolder">{{ $parentProfile->ket_pekerjaan_ayah ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-400 fw-bold">Pendidikan</td>
                                            <td class="text-gray-800 fw-bolder">{{ $parentProfile->pendidikan_ayah ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-400 fw-bold">Alamat</td>
                                            <td class="text-gray-800 fw-bolder">{{ $parentProfile->alamat_ayah ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-400 fw-bold">Nomor HP</td>
                                            <td class="text-gray-800 fw-bolder">{{ $parentProfile->no_hp_ayah ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-400 fw-bold">Penghasilan</td>
                                            <td class="text-gray-800 fw-bolder">{{ $parentProfile->penghasilan_ayah ?: '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Profil Ibu -->
                            <div class="col-md-6 ps-md-8">
                                <div class="d-flex align-items-center mb-6">
                                    <div class="symbol symbol-35px symbol-circle me-3 bg-light-danger text-danger d-flex align-items-center justify-content-center fw-boldest p-2">
                                        <i class="bi bi-person-fill text-danger fs-3"></i>
                                    </div>
                                    <h4 class="text-gray-800 fw-boldest mb-0">Profil Ibu</h4>
                                </div>

                                <table class="table align-middle table-row-dashed fs-6 gy-4">
                                    <tbody>
                                        <tr>
                                            <td class="text-gray-400 fw-bold w-150px">NIK Ibu</td>
                                            <td class="text-gray-800 fw-bolder">{{ $parentProfile->nik_ibu ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-400 fw-bold">Nama Ibu</td>
                                            <td class="text-gray-800 fw-bolder">{{ $parentProfile->nama_ibu ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-400 fw-bold">Pekerjaan</td>
                                            <td class="text-gray-800 fw-bolder">{{ $parentProfile->pekerjaan_ibu ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-400 fw-bold">Ket. Pekerjaan</td>
                                            <td class="text-gray-800 fw-bolder">{{ $parentProfile->ket_pekerjaan_ibu ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-400 fw-bold">Pendidikan</td>
                                            <td class="text-gray-800 fw-bolder">{{ $parentProfile->pendidikan_ibu ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-400 fw-bold">Alamat</td>
                                            <td class="text-gray-800 fw-bolder">{{ $parentProfile->alamat_ibu ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-400 fw-bold">Nomor HP</td>
                                            <td class="text-gray-800 fw-bolder">{{ $parentProfile->no_hp_ibu ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-400 fw-bold">Penghasilan</td>
                                            <td class="text-gray-800 fw-bolder">{{ $parentProfile->penghasilan_ibu ?: '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-8">
                            Profil data orang tua belum diisi.
                        </div>
                    @endif
                </div>
            </div>
        </div>

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

    <!--begin::Modal View Bukti-->
    <div class="modal fade" id="modal_view_bukti" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Foto Bukti Pengaduan</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body text-center p-9">
                    <img id="img_bukti_preview" src="" alt="Foto Bukti" class="img-fluid rounded shadow-sm" style="max-height: 450px; object-fit: contain; width: 100%;" />
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal View Bukti-->

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

                // Open image modal for complaints
                $(document).on('click', '.btn-view-bukti', function() {
                    var src = $(this).data('src');
                    $('#img_bukti_preview').attr('src', src);
                    $('#modal_view_bukti').modal('show');
                });
            });
        </script>
    @endsection
</x-base-layout>
