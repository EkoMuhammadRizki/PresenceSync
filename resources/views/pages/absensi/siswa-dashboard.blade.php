<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => ''
])

<!--begin::Row 1: 5 Metric Cards Personal Siswa (SAMA PERSIS GAYA UI DASHBOARD ADMIN)-->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-5 mb-8">
    <!-- Card 1: Profil Siswa -->
    <div class="col">
        <div class="card flex-center h-100 min-w-100px p-6 bg-light-primary border border-primary text-center">
            <span class="svg-icon svg-icon-3x svg-icon-primary mb-2">
                {!! theme()->getSvgIcon("icons/duotune/communication/com006.svg", "svg-icon-3x svg-icon-primary") !!}
            </span>
            <span class="fs-6 fw-bold text-gray-700 pb-1">Profil Siswa</span>
            <span class="fs-3 fw-bolder text-primary text-truncate w-100" title="{{ $siswa->nama }}">{{ $siswa->nama }}</span>
            <span class="fs-8 fw-bold text-gray-500">NIS: {{ $siswa->nis }}</span>
            <span class="fs-8 fw-bold text-gray-500">Kelas {{ $siswa->kelas ? $siswa->kelas->tingkat . ' ' . $siswa->kelas->nama : '-' }}</span>
        </div>
    </div>

    <!-- Card 2: Status Absen Hari Ini -->
    <div class="col">
        @php
            $bgStatus = 'bg-light-danger border-danger';
            $textStatus = 'text-danger';
            $svgColor = 'svg-icon-danger';
            if ($hasCheckedInToday && $kehadiranHariIni) {
                if ($kehadiranHariIni->status === 'hadir') {
                    $bgStatus = 'bg-light-success border-success';
                    $textStatus = 'text-success';
                    $svgColor = 'svg-icon-success';
                } elseif ($kehadiranHariIni->status === 'terlambat') {
                    $bgStatus = 'bg-light-warning border-warning';
                    $textStatus = 'text-warning';
                    $svgColor = 'svg-icon-warning';
                } elseif (in_array($kehadiranHariIni->status, ['sakit', 'izin'])) {
                    $bgStatus = 'bg-light-info border-info';
                    $textStatus = 'text-info';
                    $svgColor = 'svg-icon-info';
                }
            }
        @endphp
        <div class="card flex-center h-100 min-w-100px p-6 {{ $bgStatus }} border text-center">
            <span class="svg-icon svg-icon-3x {{ $svgColor }} mb-2">
                {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-3x " . $svgColor) !!}
            </span>
            <span class="fs-6 fw-bold text-gray-700 pb-1">Status Hari Ini</span>
            @if ($hasCheckedInToday && $kehadiranHariIni)
                <span class="fs-2hx fw-bolder {{ $textStatus }}">{{ ucfirst($kehadiranHariIni->status) }}</span>
                <span class="fs-8 fw-bold text-gray-500">Masuk: {{ $kehadiranHariIni->jam_masuk ?? '-' }}</span>
            @else
                <span class="fs-2hx fw-bolder text-danger">Belum Absen</span>
                <span class="fs-8 fw-bold text-gray-500">Silakan Presensi</span>
            @endif
        </div>
    </div>

    <!-- Card 3: Total Hadir -->
    <div class="col">
        <div class="card flex-center h-100 min-w-100px p-6 bg-light-info border border-info text-center">
            <span class="svg-icon svg-icon-3x svg-icon-info mb-2">
                {!! theme()->getSvgIcon("icons/duotune/communication/com013.svg", "svg-icon-3x svg-icon-info") !!}
            </span>
            <span class="fs-6 fw-bold text-gray-700 pb-1">Total Hadir</span>
            <span class="fs-2hx fw-bolder text-info">{{ $kehadirans->whereIn('status', ['hadir', 'terlambat'])->count() }}</span>
            <span class="fs-8 fw-bold text-gray-500">Hari Hadir</span>
        </div>
    </div>

    <!-- Card 4: Sakit & Izin -->
    <div class="col">
        <div class="card flex-center h-100 min-w-100px p-6 bg-light-warning border border-warning text-center">
            <span class="svg-icon svg-icon-3x svg-icon-warning mb-2">
                {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg", "svg-icon-3x svg-icon-warning") !!}
            </span>
            <span class="fs-6 fw-bold text-gray-700 pb-1">Sakit & Izin</span>
            <span class="fs-2hx fw-bolder text-warning">{{ $kehadirans->whereIn('status', ['sakit', 'izin'])->count() }}</span>
            <span class="fs-8 fw-bold text-gray-500">Permohonan Izin</span>
        </div>
    </div>

    <!-- Card 5: Tanpa Keterangan / Alpha -->
    <div class="col">
        <div class="card flex-center h-100 min-w-100px p-6 bg-light-danger border border-danger text-center">
            <span class="svg-icon svg-icon-3x svg-icon-danger mb-2">
                {!! theme()->getSvgIcon("icons/duotune/general/gen050.svg", "svg-icon-3x svg-icon-danger") !!}
            </span>
            <span class="fs-6 fw-bold text-gray-700 pb-1">Tanpa Keterangan</span>
            <span class="fs-2hx fw-bolder text-danger">{{ $kehadirans->where('status', 'alpha')->count() }}</span>
            <span class="fs-8 fw-bold text-gray-500">Hari Alpha</span>
        </div>
    </div>
</div>
<!--end::Row 1-->

<!--begin::Row 2: Tren Kehadiran & Aktivitas Terakhir-->
<div class="row g-5 g-xl-8 mb-8">
    <!-- Kolom Kiri: Tren & Aksi Presensi -->
    <div class="col-xl-7">
        <div class="card card-flush h-xl-100 shadow-sm">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-boldest text-gray-800 fs-4">Ringkasan Kehadiran Saya</span>
                    <span class="text-gray-400 mt-1 fw-bold fs-7">Analisis data kehadiran personal siswa</span>
                </h3>
                <div class="card-toolbar">
                    @if (!$hasCheckedInToday)
                        <button type="button" class="btn btn-warning btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modal_izin">
                            <i class="bi bi-file-earmark-text me-1"></i> Ajukan Izin
                        </button>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modal_presensi">
                            <i class="bi bi-camera me-1"></i> Presensi Sekarang
                        </button>
                    @else
                        <span class="badge badge-light-success p-3 fw-bold fs-7">
                            <i class="bi bi-check-circle-fill text-success me-1"></i> Anda Sudah Absen Hari Ini
                        </span>
                    @endif
                </div>
            </div>
            <div class="card-body pt-5">
                <!-- Stat Badges Breakdown -->
                <div class="d-flex flex-wrap gap-4 mb-6">
                    <div class="border border-dashed border-gray-300 rounded p-4 flex-grow-1 text-center bg-light">
                        <div class="fs-7 text-gray-400 fw-bold">Hadir Tepat Waktu</div>
                        <div class="fs-2 fw-boldest text-success">{{ $kehadirans->where('status', 'hadir')->count() }}</div>
                    </div>
                    <div class="border border-dashed border-gray-300 rounded p-4 flex-grow-1 text-center bg-light">
                        <div class="fs-7 text-gray-400 fw-bold">Terlambat</div>
                        <div class="fs-2 fw-boldest text-warning">{{ $kehadirans->where('status', 'terlambat')->count() }}</div>
                    </div>
                    <div class="border border-dashed border-gray-300 rounded p-4 flex-grow-1 text-center bg-light">
                        <div class="fs-7 text-gray-400 fw-bold">Sakit</div>
                        <div class="fs-2 fw-boldest text-primary">{{ $kehadirans->where('status', 'sakit')->count() }}</div>
                    </div>
                    <div class="border border-dashed border-gray-300 rounded p-4 flex-grow-1 text-center bg-light">
                        <div class="fs-7 text-gray-400 fw-bold">Izin</div>
                        <div class="fs-2 fw-boldest text-info">{{ $kehadirans->where('status', 'izin')->count() }}</div>
                    </div>
                </div>

                <!-- Link ke Tabel Lengkap -->
                <div class="d-flex align-items-center justify-content-between p-5 rounded bg-light-primary border border-primary border-opacity-25">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-table fs-1 text-primary me-3"></i>
                        <div>
                            <div class="fw-boldest text-gray-800 fs-6">Tabel Kehadiran Lengkap</div>
                            <div class="fs-7 text-gray-600">Lihat seluruh riwayat presensi, filter bulanan, dan cetak laporan</div>
                        </div>
                    </div>
                    <a href="{{ url('/absensi/siswa/kehadiran') }}" class="btn btn-primary btn-sm fw-bold">
                        Buka Tabel <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Aktivitas Presensi Terbaru -->
    <div class="col-xl-5">
        <div class="card card-flush h-xl-100 shadow-sm">
            <div class="card-header pt-7 mb-3">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-boldest text-gray-800 fs-4">Riwayat Terakhir Saya</span>
                    <span class="text-gray-400 mt-1 fw-bold fs-7">5 log presensi paling baru</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                <div class="timeline-label">
                    @forelse($kehadirans->take(5) as $kh)
                        <div class="timeline-item">
                            <div class="timeline-label fw-boldest text-gray-800 fs-7" style="width: 70px;">
                                {{ \Carbon\Carbon::parse($kh->tanggal)->format('d/m') }}
                            </div>
                            <div class="timeline-badge">
                                @if($kh->status === 'hadir')
                                    <i class="fa fa-genderless text-success fs-1"></i>
                                @elseif($kh->status === 'terlambat')
                                    <i class="fa fa-genderless text-warning fs-1"></i>
                                @elseif($kh->status === 'sakit')
                                    <i class="fa fa-genderless text-primary fs-1"></i>
                                @elseif($kh->status === 'izin')
                                    <i class="fa fa-genderless text-info fs-1"></i>
                                @else
                                    <i class="fa fa-genderless text-danger fs-1"></i>
                                @endif
                            </div>
                            <div class="timeline-content fw-bold text-gray-800 ps-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-7 fw-boldest">
                                        @if($kh->status === 'hadir')
                                            <span class="badge badge-light-success fs-8">Hadir</span>
                                        @elseif($kh->status === 'terlambat')
                                            <span class="badge badge-light-warning fs-8">Terlambat</span>
                                        @elseif($kh->status === 'sakit')
                                            <span class="badge badge-light-primary fs-8">Sakit</span>
                                        @elseif($kh->status === 'izin')
                                            <span class="badge badge-light-info fs-8">Izin</span>
                                        @else
                                            <span class="badge badge-light-danger fs-8">Alpha</span>
                                        @endif
                                    </span>
                                    <span class="text-gray-400 fs-8 fw-semibold">{{ $kh->jam_masuk ?? '-' }}</span>
                                </div>
                                <div class="text-gray-500 fs-8 fw-normal mt-1">
                                    {{ $kh->keterangan ?? ($kh->jam_masuk ? 'Masuk pukul ' . $kh->jam_masuk : 'Tidak ada keterangan') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-8">
                            Belum ada catatan presensi.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Row 2-->

</x-base-layout>
