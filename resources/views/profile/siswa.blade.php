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
            <div class="row g-5 g-xxl-8">
                <!--begin::Col Left - Personal & Class Info-->
                <div class="col-xl-6">
                    <!--Card 1: Informasi Pribadi-->
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 text-dark">Informasi Pribadi</span>
                                <span class="text-muted mt-1 fw-bold fs-7">Biodata dasar pengguna</span>
                            </h3>
                        </div>
                        <div class="card-body pt-3">
                            <!-- Nama -->
                            <div class="d-flex align-items-center mb-7">
                                <div class="flex-grow-1">
                                    <span class="text-muted fw-bold d-block fs-7">Nama Lengkap</span>
                                    <span class="text-gray-800 fw-bolder fs-6">{{ $siswa->nama ?? $user->name }}</span>
                                </div>
                            </div>

                            <!-- NIS -->
                            <div class="d-flex align-items-center mb-7">
                                <div class="flex-grow-1">
                                    <span class="text-muted fw-bold d-block fs-7">NIS</span>
                                    <span class="text-gray-800 fw-bolder fs-6">
                                        {{ $siswa->nis ?? '-' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Jenis Kelamin -->
                            <div class="d-flex align-items-center mb-7">
                                <div class="flex-grow-1">
                                    <span class="text-muted fw-bold d-block fs-7">Jenis Kelamin</span>
                                    <span class="text-gray-800 fw-bolder fs-6">
                                        {{ ($siswa->jenis_kelamin ?? '') === 'L' ? 'Laki-laki' : (($siswa->jenis_kelamin ?? '') === 'P' ? 'Perempuan' : '-') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Tanggal Lahir -->
                            <div class="d-flex align-items-center mb-7">
                                <div class="flex-grow-1">
                                    <span class="text-muted fw-bold d-block fs-7">Tanggal Lahir</span>
                                    <span class="text-gray-800 fw-bolder fs-6">
                                        {{ $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d F Y') : '-' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Alamat -->
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <span class="text-muted fw-bold d-block fs-7">Alamat Lengkap</span>
                                    <span class="text-gray-800 fw-bolder fs-6">{{ $siswa->alamat ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Card 2: Informasi Kelas & Wali-->
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
                <!--end::Col Left-->

                <!--begin::Col Right - Contact, Account & Subject Info-->
                <div class="col-xl-6">
                    <!--Card 3: Informasi Kontak-->
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 text-dark">Informasi Kontak</span>
                                <span class="text-muted mt-1 fw-bold fs-7">Detail kontak untuk komunikasi</span>
                            </h3>
                        </div>
                        <div class="card-body pt-3">


                            <!-- Nomor HP -->
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <span class="text-muted fw-bold d-block fs-7">Nomor HP / Telepon</span>
                                    <span class="text-gray-800 fw-bolder fs-6">{{ $siswa->no_hp ?? '-' }}</span>
                                </div>
                            </div>
                            
                            @if($siswa && $siswa->nama_orang_tua)
                                <!-- Orang Tua / Wali -->
                                <div class="d-flex align-items-center mt-7">
                                    <div class="flex-grow-1">
                                        <span class="text-muted fw-bold d-block fs-7">Nama Orang Tua & Kontak Wali</span>
                                        <span class="text-gray-800 fw-bolder fs-6">
                                            {{ $siswa->nama_orang_tua }} ({{ $siswa->no_hp_orang_tua ?? '-' }})
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!--Card 4: Informasi Akun-->
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 text-dark">Informasi Akun</span>
                                <span class="text-muted mt-1 fw-bold fs-7">Status keanggotaan dan log aktivitas</span>
                            </h3>
                        </div>
                        <div class="card-body pt-3">
                            <!-- Status Akun -->
                            <div class="d-flex align-items-center mb-7">
                                <div class="flex-grow-1">
                                    <span class="text-muted fw-bold d-block fs-7">Status Akun</span>
                                    @php
                                        $statusVal = $siswa->status ?? ($user->status ?? 'aktif');
                                        $statusClass = $statusVal === 'aktif' ? 'badge-light-success' : 'badge-light-danger';
                                    @endphp
                                    <span class="badge {{ $statusClass }} fw-bolder fs-7">{{ ucfirst($statusVal) }}</span>
                                </div>
                            </div>

                            <!-- Terakhir Login -->
                            <div class="d-flex align-items-center mb-7">
                                <div class="flex-grow-1">
                                    <span class="text-muted fw-bold d-block fs-7">Terakhir Kali Login</span>
                                    <span class="text-gray-800 fw-bolder fs-6">
                                        {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d M Y, H:i') : '-' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Tanggal Terdaftar -->
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <span class="text-muted fw-bold d-block fs-7">Tanggal Terdaftar</span>
                                    <span class="text-gray-800 fw-bolder fs-6">
                                        {{ $user->created_at ? $user->created_at->format('d F Y, H:i') : '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Card 5: Mata Pelajaran Diikuti-->
                    @php
                        $uniqueMapels = $schedules->map(function($s) {
                            return $s->mataPelajaran;
                        })->filter()->unique('id');
                        $mapelCount = $uniqueMapels->count();
                        $isScrollable = $mapelCount > 5;
                    @endphp
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 text-dark">Mata Pelajaran Diikuti</span>
                                <span class="text-muted mt-1 fw-bold fs-7">Daftar {{ $mapelCount }} mata pelajaran semester ini</span>
                            </h3>
                        </div>
                        <div class="card-body pt-3 {{ $isScrollable ? 'scroll-y me-n5 pe-5' : '' }}" @if($isScrollable) style="max-height: 300px; overflow-y: auto;" @endif>
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
                <!--end::Col Right-->
            </div>
        </div>
        <!--end::Tab Pane - Informasi Siswa-->
        <!--begin::Tab Pane - Riwayat Kehadiran-->
        <div class="tab-pane fade {{ request()->has('periode') ? 'show active' : '' }}" id="tab_riwayat" role="tabpanel">
            
            <!--Sub-tab Navigation Bar-->
            <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6 fw-bold border-0">
                <li class="nav-item">
                    <a class="nav-link active text-active-primary me-6 pb-3 fw-bolder" data-bs-toggle="tab" href="#subtab_daftar_kehadiran">
                        <i class="bi bi-table fs-4 me-2"></i> Daftar Kehadiran
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-active-primary me-6 pb-3 fw-bolder" data-bs-toggle="tab" href="#subtab_ringkasan_kehadiran">
                        <i class="bi bi-pie-chart-fill fs-4 me-2"></i> Ringkasan Kehadiran
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="subTabKehadiranContent">
                <!--==================== SUB-TAB 1: DAFTAR KEHADIRAN ====================-->
                <div class="tab-pane fade show active" id="subtab_daftar_kehadiran" role="tabpanel">
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
                                            <th class="w-100px border-end">Pulang Jam</th>
                                            <th class="min-w-150px border-end">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700 fw-bold fs-7">
                                        @forelse ($attendanceRows as $row)
                                            <tr class="{{ $row['is_libur'] ? 'bg-light-danger' : '' }}">
                                                <td class="text-center border-end">{{ $row['no'] }}</td>
                                                <td class="border-end">{{ $row['nisn'] }}</td>
                                                <td class="border-end">{{ $row['nama'] }}</td>
                                                <td class="border-end text-center">{{ $row['tanggal'] }}</td>
                                                <td class="text-center border-end fw-bolder {{ $row['is_libur'] ? 'text-danger' : 'text-success' }}">
                                                    {{ $row['msk_lbr'] }}
                                                </td>
                                                <td class="text-center border-end text-primary">{{ $row['msk_jam'] ?: '-' }}</td>
                                                <td class="text-center border-end text-success">{{ $row['plg_jam'] ?: '-' }}</td>
                                                <td class="border-end">
                                                    @if($row['keterangan'] === 'Libur')
                                                        <span class="badge badge-light-danger">Libur</span>
                                                    @elseif($row['keterangan'] === 'Tepat Waktu')
                                                        <span class="badge badge-light-success">Tepat Waktu</span>
                                                    @elseif($row['keterangan'] === 'Terlambat')
                                                        <span class="badge badge-light-warning">Terlambat</span>
                                                    @elseif($row['keterangan'] === 'Sakit')
                                                        <span class="badge badge-light-info">Sakit</span>
                                                    @elseif($row['keterangan'] === 'Izin')
                                                        <span class="badge badge-light-primary">Izin</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-8">Belum ada data kehadiran</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!--==================== SUB-TAB 2: RINGKASAN KEHADIRAN ====================-->
                <div class="tab-pane fade" id="subtab_ringkasan_kehadiran" role="tabpanel">
                    @php
                        $yr = substr($periode, 0, 4);
                        $mo = substr($periode, 4, 2);
                        $mStart = \Carbon\Carbon::createFromDate($yr, $mo, 1)->startOfMonth()->format('Y-m-d');
                        $mEnd = \Carbon\Carbon::createFromDate($yr, $mo, 1)->endOfMonth()->format('Y-m-d');

                        $mRecs = $siswa->kehadirans()->whereBetween('tanggal', [$mStart, $mEnd])->get();
                        $mHadir = $mRecs->where('status', 'hadir')->count();
                        $mTerlambat = $mRecs->where('status', 'terlambat')->count();
                        $mIzin = $mRecs->where('status', 'izin')->count();
                        $mSakit = $mRecs->where('status', 'sakit')->count();
                        $mAlpa = $mRecs->where('status', 'alpha')->count();
                        $mMasuk = $mHadir + $mTerlambat;
                        $mTotalEff = $mHadir + $mTerlambat + $mIzin + $mSakit + $mAlpa;
                        $mPct = $mTotalEff > 0 ? round(($mMasuk / $mTotalEff) * 100, 1) : 100;

                        $allRecs = $siswa->kehadirans()->get();
                        $aHadir = $allRecs->where('status', 'hadir')->count();
                        $aTerlambat = $allRecs->where('status', 'terlambat')->count();
                        $aIzin = $allRecs->where('status', 'izin')->count();
                        $aSakit = $allRecs->where('status', 'sakit')->count();
                        $aAlpa = $allRecs->where('status', 'alpha')->count();
                        $aMasuk = $aHadir + $aTerlambat;
                        $aTotalEff = $aHadir + $aTerlambat + $aIzin + $aSakit + $aAlpa;
                        $aPct = $aTotalEff > 0 ? round(($aMasuk / $aTotalEff) * 100, 1) : 100;

                        $evalTitle = 'Sangat Baik';
                        $evalBadge = 'badge-light-success';
                        $evalNote = 'Siswa memiliki tingkat kedisiplinan dan kehadiran yang sangat tinggi dalam mengikuti kegiatan pembelajaran.';
                        if ($aAlpa >= 3 || $aPct < 75) {
                            $evalTitle = 'Perlu Perhatian Khusus';
                            $evalBadge = 'badge-light-danger';
                            $evalNote = 'Siswa memiliki akumulasi Alpa / Ketidakhadiran yang tergolong tinggi. Diperlukan tindakan pembinaan atau konseling dari Tim Kesiswaan / BK.';
                        } elseif ($aTerlambat >= 5 || $aPct < 85) {
                            $evalTitle = 'Cukup / Evaluasi Keterlambatan';
                            $evalBadge = 'badge-light-warning';
                            $evalNote = 'Catatan keterlambatan atau izin mulai menumpuk. Diperlukan koordinasi wali kelas dan penyesuaian jam datang siswa.';
                        }

                        $mTidakHadir = $mIzin + $mSakit + $mAlpa;
                        $aTidakHadir = $aIzin + $aSakit + $aAlpa;
                    @endphp

                    <!-- Banner Status Evaluasi Kesiswaan -->
                    <div class="card bg-light-primary border border-primary border-dashed mb-6 shadow-sm">
                        <div class="card-body p-6 d-flex align-items-center justify-content-between flex-wrap gap-4">
                            <div class="d-flex align-items-center gap-4">
                                <!-- Sleek Percentage Badge -->
                                <div class="d-flex flex-column align-items-center justify-content-center bg-primary text-white rounded-3 px-4 py-3 min-w-90px shadow-sm">
                                    <span class="fs-1 fw-bolder lh-1 mb-1">{{ $aPct }}%</span>
                                    <span class="fs-9 fw-bold text-white text-opacity-75 uppercase">Kehadiran</span>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h4 class="text-gray-900 fw-boldest mb-0">Persentase Kehadiran Akumulatif</h4>
                                        <span class="badge {{ $evalBadge }} fw-bolder fs-7 px-3 py-2">{{ $evalTitle }}</span>
                                    </div>
                                    <p class="text-gray-600 fs-7 mb-0">{{ $evalNote }}</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-4">
                                <div class="text-end border-start border-gray-300 ps-4">
                                    <span class="text-gray-500 fs-8 fw-bold d-block">Persentase Periode Ini</span>
                                    <span class="text-primary fw-boldest fs-3">{{ $mPct }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 5 Card Ringkasan Statistik (Dashboard Style) -->
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-5 g-5 mb-6">
                        <!-- Card 1: Hadir (Tepat Waktu) -->
                        <div class="col">
                            <div class="card flex-center min-w-100px p-6 bg-light-success border border-success">
                                <span class="svg-icon svg-icon-3x svg-icon-success mb-2">
                                    {!! theme()->getSvgIcon("icons/duotune/general/gen043.svg", "svg-icon-3x svg-icon-success") !!}
                                </span>
                                <span class="fs-6 fw-bold text-gray-700 pb-1">Hadir (Tepat Waktu)</span>
                                <span class="fs-2hx fw-bolder text-success">{{ $aHadir }}</span>
                                <span class="fs-8 fw-bold text-gray-500">Bulan ini: {{ $mHadir }} hari</span>
                            </div>
                        </div>

                        <!-- Card 2: Terlambat -->
                        <div class="col">
                            <div class="card flex-center min-w-100px p-6 bg-light-warning border border-warning">
                                <span class="svg-icon svg-icon-3x svg-icon-warning mb-2">
                                    {!! theme()->getSvgIcon("icons/duotune/general/gen050.svg", "svg-icon-3x svg-icon-warning") !!}
                                </span>
                                <span class="fs-6 fw-bold text-gray-700 pb-1">Terlambat</span>
                                <span class="fs-2hx fw-bolder text-warning">{{ $aTerlambat }}</span>
                                <span class="fs-8 fw-bold text-gray-500">Bulan ini: {{ $mTerlambat }} hari</span>
                            </div>
                        </div>

                        <!-- Card 3: Izin -->
                        <div class="col">
                            <div class="card flex-center min-w-100px p-6 bg-light-primary border border-primary">
                                <span class="svg-icon svg-icon-3x svg-icon-primary mb-2">
                                    {!! theme()->getSvgIcon("icons/duotune/general/gen025.svg", "svg-icon-3x svg-icon-primary") !!}
                                </span>
                                <span class="fs-6 fw-bold text-gray-700 pb-1">Izin</span>
                                <span class="fs-2hx fw-bolder text-primary">{{ $aIzin }}</span>
                                <span class="fs-8 fw-bold text-gray-500">Bulan ini: {{ $mIzin }} hari</span>
                            </div>
                        </div>

                        <!-- Card 4: Sakit -->
                        <div class="col">
                            <div class="card flex-center min-w-100px p-6 bg-light-info border border-info">
                                <span class="svg-icon svg-icon-3x svg-icon-info mb-2">
                                    {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-3x svg-icon-info") !!}
                                </span>
                                <span class="fs-6 fw-bold text-gray-700 pb-1">Sakit</span>
                                <span class="fs-2hx fw-bolder text-info">{{ $aSakit }}</span>
                                <span class="fs-8 fw-bold text-gray-500">Bulan ini: {{ $mSakit }} hari</span>
                            </div>
                        </div>

                        <!-- Card 5: Alpa -->
                        <div class="col">
                            <div class="card flex-center min-w-100px p-6 bg-light-danger border border-danger">
                                <span class="svg-icon svg-icon-3x svg-icon-danger mb-2">
                                    {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg", "svg-icon-3x svg-icon-danger") !!}
                                </span>
                                <span class="fs-6 fw-bold text-gray-700 pb-1">Alpa</span>
                                <span class="fs-2hx fw-bolder text-danger">{{ $aAlpa }}</span>
                                <span class="fs-8 fw-bold text-gray-500">Bulan ini: {{ $mAlpa }} hari</span>
                            </div>
                        </div>
                    </div>

                    <!-- Breakdown Detail & Progress Bars -->
                    <div class="row g-6 mb-6">
                        <!-- Left: Visual Distribution -->
                        <div class="col-xl-6">
                            <div class="card card-flush h-100 shadow-sm">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bolder fs-4 text-gray-900">Distribusi Kehadiran Siswa</span>
                                        <span class="text-muted mt-1 fw-bold fs-7">Rincian persentase setiap kriteria kehadiran</span>
                                    </h3>
                                </div>
                                <div class="card-body pt-3">
                                    @php
                                        $pHadir = $aTotalEff > 0 ? round(($aHadir / $aTotalEff) * 100, 1) : 0;
                                        $pTerlambat = $aTotalEff > 0 ? round(($aTerlambat / $aTotalEff) * 100, 1) : 0;
                                        $pIzin = $aTotalEff > 0 ? round(($aIzin / $aTotalEff) * 100, 1) : 0;
                                        $pSakit = $aTotalEff > 0 ? round(($aSakit / $aTotalEff) * 100, 1) : 0;
                                        $pAlpa = $aTotalEff > 0 ? round(($aAlpa / $aTotalEff) * 100, 1) : 0;
                                    @endphp

                                    <!-- Hadir -->
                                    <div class="mb-5">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold text-gray-700 fs-7">Hadir Tepat Waktu</span>
                                            <span class="fw-boldest text-success fs-7">{{ $aHadir }} Hari ({{ $pHadir }}%)</span>
                                        </div>
                                        <div class="progress h-8px bg-light-success">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pHadir }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Terlambat -->
                                    <div class="mb-5">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold text-gray-700 fs-7">Terlambat</span>
                                            <span class="fw-boldest text-warning fs-7">{{ $aTerlambat }} Hari ({{ $pTerlambat }}%)</span>
                                        </div>
                                        <div class="progress h-8px bg-light-warning">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $pTerlambat }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Izin -->
                                    <div class="mb-5">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold text-gray-700 fs-7">Izin</span>
                                            <span class="fw-boldest text-primary fs-7">{{ $aIzin }} Hari ({{ $pIzin }}%)</span>
                                        </div>
                                        <div class="progress h-8px bg-light-primary">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $pIzin }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Sakit -->
                                    <div class="mb-5">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold text-gray-700 fs-7">Sakit</span>
                                            <span class="fw-boldest text-info fs-7">{{ $aSakit }} Hari ({{ $pSakit }}%)</span>
                                        </div>
                                        <div class="progress h-8px bg-light-info">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $pSakit }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Alpa -->
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold text-gray-700 fs-7">Alpa (Tanpa Keterangan)</span>
                                            <span class="fw-boldest text-danger fs-7">{{ $aAlpa }} Hari ({{ $pAlpa }}%)</span>
                                        </div>
                                        <div class="progress h-8px bg-light-danger">
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $pAlpa }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Quick Summary Info Box -->
                        <div class="col-xl-6">
                            <div class="card card-flush h-100 shadow-sm">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bolder fs-4 text-gray-900">Rekapitulasi Untuk Kesiswaan</span>
                                        <span class="text-muted mt-1 fw-bold fs-7">Rangkuman poin evaluasi siswa</span>
                                    </h3>
                                </div>
                                <div class="card-body pt-3">
                                    <div class="table-responsive">
                                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3">
                                            <thead>
                                                <tr class="fw-bolder text-muted fs-8 text-uppercase">
                                                    <th>Parameter</th>
                                                    <th class="text-center">Bulan Ini</th>
                                                    <th class="text-center">Total Akumulatif</th>
                                                    <th class="text-end">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="fs-7 fw-bold">
                                                <tr>
                                                    <td class="text-gray-800">Total Masuk Sekolah</td>
                                                    <td class="text-center text-success fw-boldest">{{ $mMasuk }} Hari</td>
                                                    <td class="text-center text-success fw-boldest">{{ $aMasuk }} Hari</td>
                                                    <td class="text-end"><span class="badge badge-light-success">Aktif</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-gray-800">Total Ketidakhadiran</td>
                                                    <td class="text-center text-danger fw-boldest">{{ $mTidakHadir }} Hari</td>
                                                    <td class="text-center text-danger fw-boldest">{{ $aTidakHadir }} Hari</td>
                                                    <td class="text-end">
                                                        @if($aTidakHadir == 0)
                                                            <span class="badge badge-light-success">Nihil</span>
                                                        @elseif($aTidakHadir <= 3)
                                                            <span class="badge badge-light-warning">Wajar</span>
                                                        @else
                                                            <span class="badge badge-light-danger">Tinggi</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-gray-800">Frekuensi Alpa (Tanpa Ket.)</td>
                                                    <td class="text-center text-danger fw-boldest">{{ $mAlpa }} Hari</td>
                                                    <td class="text-center text-danger fw-boldest">{{ $aAlpa }} Hari</td>
                                                    <td class="text-end">
                                                        @if($aAlpa == 0)
                                                            <span class="badge badge-light-success">Bebas Alpa</span>
                                                        @elseif($aAlpa <= 2)
                                                            <span class="badge badge-light-warning">Teguran</span>
                                                        @else
                                                            <span class="badge badge-light-danger">Panggilan Ortua</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-gray-800">Persentase Kehadiran</td>
                                                    <td class="text-center text-primary fw-boldest">{{ $mPct }}%</td>
                                                    <td class="text-center text-primary fw-boldest">{{ $aPct }}%</td>
                                                    <td class="text-end">
                                                        <span class="badge {{ $mPct >= 90 ? 'badge-light-success' : ($mPct >= 75 ? 'badge-light-warning' : 'badge-light-danger') }}">
                                                            {{ $mPct >= 90 ? 'A (Baik)' : ($mPct >= 75 ? 'B (Cukup)' : 'C (Kurang)') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
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

                <!-- Toolbar Filter & Search -->
                <div class="card-body py-4 border-bottom">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center position-relative my-1">
                            {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-2 position-absolute ms-4") !!}
                            <input type="text" id="search_pengaduan" class="form-control form-control-solid form-control-sm w-200px w-md-250px ps-12" placeholder="Cari pengaduan..." />
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div class="d-flex align-items-center position-relative my-1">
                                {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-2 position-absolute ms-3") !!}
                                <input type="text" id="filter_tanggal_pengaduan" class="form-control form-control-solid form-control-sm w-225px w-md-275px ps-10" placeholder="Pilih Rentang Tanggal" readonly="readonly" />
                            </div>
                            <button type="button" id="reset_filter_tanggal_pengaduan" class="btn btn-light-danger btn-sm my-1" style="display: none;">
                                Reset
                            </button>
                        </div>
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
                                @foreach ($pengaduans as $index => $row)
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
                                @endforeach
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
                    @if (!$parentProfile)
                        <div class="alert alert-light-primary d-flex align-items-center p-5 mb-8">
                            <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
                                <i class="bi bi-info-circle-fill text-primary fs-2"></i>
                            </span>
                            <div class="d-flex flex-column">
                                <h5 class="mb-1 text-dark fw-bold">Data Belum Diisi</h5>
                                <span class="text-muted fs-7">Orang tua belum mengisi form profil. Tampilan di bawah ini adalah form default (read-only).</span>
                            </div>
                        </div>
                    @endif

                    <div class="row g-9">
                        <!--==================== FATHER PROFILE COLUMN ====================-->
                        <div class="col-lg-6 col-md-12 border-end-lg pe-lg-8">
                            <div class="d-flex align-items-center mb-6">
                                <div class="symbol symbol-35px symbol-circle me-3 bg-light-primary text-primary d-flex align-items-center justify-content-center fw-boldest p-2">
                                    <i class="bi bi-person-fill text-primary fs-3"></i>
                                </div>
                                <h4 class="text-gray-800 fw-boldest mb-0">Profil Ayah</h4>
                            </div>

                            <!-- NIK Ayah -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">NIK Ayah</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="NIK Ayah" value="{{ $parentProfile->nik_ayah ?? '' }}" readonly />
                                </div>
                            </div>

                            <!-- Nama Ayah -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Nama Ayah</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Nama Lengkap Ayah" value="{{ $parentProfile->nama_ayah ?? '' }}" readonly />
                                </div>
                            </div>

                            <!-- Tahun Lahir Ayah -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Tahun Lahir</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Tahun Lahir Ayah" value="{{ $parentProfile->tahun_lahir_ayah ?? '' }}" readonly />
                                </div>
                            </div>

                            <!-- Pekerjaan Ayah -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Pekerjaan</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Pekerjaan Ayah" value="{{ $parentProfile->pekerjaan_ayah ?? '' }}" readonly />
                                </div>
                            </div>

                            <!-- Ket Pekerjaan Ayah -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Ket. Pekerjaan</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Keterangan Pekerjaan" value="{{ $parentProfile->ket_pekerjaan_ayah ?? '' }}" readonly />
                                </div>
                            </div>

                            <!-- Pendidikan Ayah -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Pendidikan</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Pendidikan Terakhir" value="{{ $parentProfile->pendidikan_ayah ?? '' }}" readonly />
                                </div>
                            </div>

                            <!-- Alamat Ayah -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Alamat</label>
                                <div class="col-lg-8">
                                    <textarea class="form-control form-control-lg form-control-solid" rows="3" placeholder="Alamat Tinggal Ayah" readonly>{{ $parentProfile->alamat_ayah ?? '' }}</textarea>
                                </div>
                            </div>

                            <!-- Nomor HP Ayah -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Nomor HP</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Nomor Telepon Seluler" value="{{ $parentProfile->no_hp_ayah ?? '' }}" readonly />
                                </div>
                            </div>

                            <!-- Penghasilan Ayah -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Penghasilan</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Penghasilan Per Bulan" value="{{ $parentProfile->penghasilan_ayah_formatted ?? '' }}" readonly />
                                </div>
                            </div>
                        </div>

                        <!--==================== MOTHER PROFILE COLUMN ====================-->
                        <div class="col-lg-6 col-md-12 ps-lg-8">
                            <div class="d-flex align-items-center mb-6">
                                <div class="symbol symbol-35px symbol-circle me-3 bg-light-danger text-danger d-flex align-items-center justify-content-center fw-boldest p-2">
                                    <i class="bi bi-person-fill text-danger fs-3"></i>
                                </div>
                                <h4 class="text-gray-800 fw-boldest mb-0">Profil Ibu</h4>
                            </div>

                            <!-- NIK Ibu -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">NIK Ibu</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="NIK Ibu" value="{{ $parentProfile->nik_ibu ?? '' }}" readonly />
                                </div>
                            </div>

                            <!-- Nama Ibu -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Nama Ibu</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Nama Lengkap Ibu" value="{{ $parentProfile->nama_ibu ?? '' }}" readonly />
                                </div>
                            </div>

                            <!-- Tahun Lahir Ibu -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Tahun Lahir</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Tahun Lahir Ibu" value="{{ $parentProfile->tahun_lahir_ibu ?? '' }}" readonly />
                                </div>
                            </div>

                            <!-- Pekerjaan Ibu -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Pekerjaan</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Pekerjaan Ibu" value="{{ $parentProfile->pekerjaan_ibu ?? '' }}" readonly />
                                </div>
                            </div>

                            <!-- Ket Pekerjaan Ibu -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Ket. Pekerjaan</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Keterangan Pekerjaan" value="{{ $parentProfile->ket_pekerjaan_ibu ?? '' }}" readonly />
                                </div>
                            </div>

                            <!-- Pendidikan Ibu -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Pendidikan</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Pendidikan Terakhir" value="{{ $parentProfile->pendidikan_ibu ?? '' }}" readonly />
                                </div>
                            </div>

                            <!-- Alamat Ibu -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Alamat</label>
                                <div class="col-lg-8">
                                    <textarea class="form-control form-control-lg form-control-solid" rows="3" placeholder="Alamat Tinggal Ibu" readonly>{{ $parentProfile->alamat_ibu ?? '' }}</textarea>
                                </div>
                            </div>

                            <!-- Nomor HP Ibu -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Nomor HP</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Nomor Telepon Seluler" value="{{ $parentProfile->no_hp_ibu ?? '' }}" readonly />
                                </div>
                            </div>

                            <!-- Penghasilan Ibu -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Penghasilan</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Penghasilan Per Bulan" value="{{ $parentProfile->penghasilan_ibu_formatted ?? '' }}" readonly />
                                </div>
                            </div>
                        </div>
                    </div>
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
        
        <script src="{{ asset(theme()->getDemo() . '/plugins/custom/flatpickr/flatpickr.bundle.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.jQuery && $.fn.dataTable) {
                    var tablePengaduan = $('#table_pengaduan').DataTable({
                        dom: "<'table-responsive'tr><'row px-5 py-3'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'li><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>",
                        info: true,
                        order: [[1, 'desc']],
                        pageLength: 5,
                        lengthMenu: [5, 10, 25, 50],
                        language: {
                            emptyTable: "Belum ada data pengaduan",
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                            infoEmpty: "Menampilkan 0 dari 0 data",
                            infoFiltered: "(disaring dari _MAX_ total data)",
                            zeroRecords: "Tidak ada data pengaduan yang cocok",
                            lengthMenu: "Tampilkan _MENU_",
                            paginate: {
                                previous: "<i class='bi bi-chevron-left'></i>",
                                next: "<i class='bi bi-chevron-right'></i>"
                            }
                        },
                        columnDefs: [{ orderable: false, targets: [0, 3] }]
                    });

                    $('#search_pengaduan').on('keyup', function() {
                        tablePengaduan.search(this.value).draw();
                    });

                    // Lokalisasi Flatpickr Bahasa Indonesia
                    var indonesianLocale = {
                        firstDayOfWeek: 1,
                        weekdays: {
                            shorthand: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                            longhand: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"]
                        },
                        months: {
                            shorthand: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agt", "Sep", "Okt", "Nov", "Des"],
                            longhand: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"]
                        },
                        rangeSeparator: " hingga "
                    };

                    function parseIndonesianDate(dateStr) {
                        if (!dateStr) return null;
                        var cleanStr = dateStr.trim();
                        var parts = cleanStr.includes(',') ? cleanStr.split(',')[1].trim().split(' ') : cleanStr.split(' ');
                        if (parts.length < 3) return null;
                        
                        var day = parseInt(parts[0], 10);
                        var monthStr = parts[1].toLowerCase();
                        var year = parseInt(parts[2], 10);
                        
                        var months = {
                            'januari': 0, 'februari': 1, 'maret': 2, 'april': 3, 'mei': 4, 'juni': 5,
                            'juli': 6, 'agustus': 7, 'september': 8, 'oktober': 9, 'november': 10, 'desember': 11,
                            'jan': 0, 'feb': 1, 'mar': 2, 'apr': 3, 'mei': 4, 'jun': 5, 'jul': 6, 'agt': 7, 'sep': 8, 'okt': 9, 'nov': 10, 'des': 11
                        };
                        
                        var month = months[monthStr] !== undefined ? months[monthStr] : -1;
                        if (month === -1) return null;
                        
                        return new Date(year, month, day, 0, 0, 0);
                    }

                    $.fn.dataTable.ext.search.push(
                        function(settings, data, dataIndex) {
                            if (settings.nTable.id !== 'table_pengaduan') {
                                return true;
                            }
                            
                            var dateVal = $('#filter_tanggal_pengaduan').val();
                            if (!dateVal) {
                                return true;
                            }
                            
                            var fpEl = document.getElementById("filter_tanggal_pengaduan");
                            var fp = fpEl ? fpEl._flatpickr : null;
                            if (!fp || fp.selectedDates.length === 0) {
                                return true;
                            }
                            
                            var startDate = fp.selectedDates[0];
                            var endDate = fp.selectedDates[1] || startDate;
                            
                            var minDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate(), 0, 0, 0);
                            var maxDate = new Date(endDate.getFullYear(), endDate.getMonth(), endDate.getDate(), 23, 59, 59);
                            
                            var rowDateStr = data[1];
                            var rowDate = parseIndonesianDate(rowDateStr);
                            
                            if (!rowDate) {
                                return true;
                            }
                            
                            return rowDate >= minDate && rowDate <= maxDate;
                        }
                    );

                    if ($('#filter_tanggal_pengaduan').length && typeof flatpickr !== 'undefined') {
                        var fpPengaduan = $("#filter_tanggal_pengaduan").flatpickr({
                            mode: "range",
                            dateFormat: "Y-m-d",
                            locale: indonesianLocale,
                            onClose: function(selectedDates) {
                                if (selectedDates.length > 0) {
                                    $('#reset_filter_tanggal_pengaduan').show();
                                } else {
                                    $('#reset_filter_tanggal_pengaduan').hide();
                                }
                                tablePengaduan.draw();
                            }
                        });

                        $('#reset_filter_tanggal_pengaduan').on('click', function() {
                            if (fpPengaduan) {
                                fpPengaduan.clear();
                            }
                            $(this).hide();
                            tablePengaduan.draw();
                        });
                    }
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
