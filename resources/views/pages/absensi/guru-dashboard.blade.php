<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => ''
])

<!--begin::Row 1: 5 Metric Cards Guru (SAMA PERSIS GAYA UI DASHBOARD ADMIN)-->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-5 mb-8">
    <!-- Card 1: Profil Guru -->
    <div class="col">
        <div class="card flex-center h-100 min-w-100px p-6 bg-light-primary border border-primary text-center">
            <span class="svg-icon svg-icon-3x svg-icon-primary mb-2">
                {!! theme()->getSvgIcon("icons/duotune/communication/com006.svg", "svg-icon-3x svg-icon-primary") !!}
            </span>
            <span class="fs-6 fw-bold text-gray-700 pb-1">Profil Guru</span>
            <span class="fs-3 fw-bolder text-primary text-truncate w-100" title="{{ $guru->nama }}">{{ $guru->nama }}</span>
            <span class="fs-8 fw-bold text-gray-500">NIP: {{ $guru->nip }}</span>
            <span class="fs-8 fw-bold text-gray-500">Wali Kelas {{ count($kelasDetail) > 0 ? $kelasDetail[0]['kelas']->tingkat . ' ' . $kelasDetail[0]['kelas']->nama : '-' }}</span>
        </div>
    </div>

    <!-- Card 2: Total Siswa Wali -->
    <div class="col">
        <div class="card flex-center h-100 min-w-100px p-6 bg-light-info border border-info text-center">
            <span class="svg-icon svg-icon-3x svg-icon-info mb-2">
                {!! theme()->getSvgIcon("icons/duotune/communication/com013.svg", "svg-icon-3x svg-icon-info") !!}
            </span>
            <span class="fs-6 fw-bold text-gray-700 pb-1">Total Siswa Wali</span>
            <span class="fs-2hx fw-bolder text-info">{{ $totalSiswa }}</span>
            <span class="fs-8 fw-bold text-gray-500">Siswa di Kelas Binaan</span>
        </div>
    </div>

    <!-- Card 3: Hadir Hari Ini -->
    <div class="col">
        <div class="card flex-center h-100 min-w-100px p-6 bg-light-success border border-success text-center">
            <span class="svg-icon svg-icon-3x svg-icon-success mb-2">
                {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-3x svg-icon-success") !!}
            </span>
            <span class="fs-6 fw-bold text-gray-700 pb-1">Hadir Hari Ini</span>
            <span class="fs-2hx fw-bolder text-success">{{ $hadirHariIni }}</span>
            <span class="fs-8 fw-bold text-gray-500">Termasuk Terlambat</span>
        </div>
    </div>

    <!-- Card 4: Izin / Sakit -->
    <div class="col">
        <div class="card flex-center h-100 min-w-100px p-6 bg-light-warning border border-warning text-center">
            <span class="svg-icon svg-icon-3x svg-icon-warning mb-2">
                {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg", "svg-icon-3x svg-icon-warning") !!}
            </span>
            <span class="fs-6 fw-bold text-gray-700 pb-1">Izin / Sakit</span>
            <span class="fs-2hx fw-bolder text-warning">{{ $izinSakitHariIni }}</span>
            <span class="fs-8 fw-bold text-gray-500">Permohonan Izin</span>
        </div>
    </div>

    <!-- Card 5: Belum Absen -->
    <div class="col">
        <div class="card flex-center h-100 min-w-100px p-6 bg-light-danger border border-danger text-center">
            <span class="svg-icon svg-icon-3x svg-icon-danger mb-2">
                {!! theme()->getSvgIcon("icons/duotune/general/gen050.svg", "svg-icon-3x svg-icon-danger") !!}
            </span>
            <span class="fs-6 fw-bold text-gray-700 pb-1">Belum Absen</span>
            <span class="fs-2hx fw-bolder text-danger">{{ $alphaHariIni }}</span>
            <span class="fs-8 fw-bold text-gray-500">Hari Ini</span>
        </div>
    </div>
</div>
<!--end::Row 1-->

<!--begin::Row 2: Detail Kehadiran Kelas Wali & Feed Aktivitas-->
<div class="row g-5 g-xl-8 mb-8">
    <!-- Kolom Kiri: Detail Kelas Binaan -->
    <div class="col-xl-7">
        @forelse ($kelasDetail as $kd)
            <div class="card card-flush shadow-sm mb-6">
                <div class="card-header pt-6">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-boldest text-gray-800 fs-4">Kelas {{ $kd['kelas']->tingkat }} {{ $kd['kelas']->nama }}</span>
                        <span class="text-gray-400 mt-1 fw-bold fs-7">{{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }}</span>
                    </h3>
                    <div class="card-toolbar">
                        <span class="badge badge-light-success p-3 fw-bold fs-7">
                            {{ $kd['hadir'] }} / {{ $kd['total'] }} Siswa Hadir
                        </span>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-row-bordered table-row-gray-300 align-middle gs-0 gy-3">
                            <thead>
                                <tr class="fw-bolder text-muted bg-light">
                                    <th class="ps-4 min-w-40px">NO</th>
                                    <th class="min-w-150px">NAMA SISWA</th>
                                    <th class="min-w-100px">NIS</th>
                                    <th class="min-w-100px text-center">JAM MASUK</th>
                                    <th class="min-w-120px text-center">STATUS</th>
                                    <th class="pe-4 min-w-120px">KETERANGAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kd['siswa'] as $idx => $item)
                                    <tr>
                                        <td class="ps-4 fw-bold text-gray-600">{{ $idx + 1 }}</td>
                                        <td class="fw-boldest text-gray-800">{{ $item['siswa']->nama }}</td>
                                        <td class="fw-bold text-gray-600">{{ $item['siswa']->nis }}</td>
                                        <td class="text-center">
                                            @if($item['jam_masuk'])
                                                <span class="badge badge-light-success fs-7 fw-bold">{{ $item['jam_masuk'] }}</span>
                                            @else
                                                <span class="text-muted fs-7">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($item['status'] === 'hadir')
                                                <span class="badge badge-light-success fw-bold">Hadir</span>
                                            @elseif ($item['status'] === 'terlambat')
                                                <span class="badge badge-light-warning fw-bold">Terlambat</span>
                                            @elseif ($item['status'] === 'sakit')
                                                <span class="badge badge-light-primary fw-bold">Sakit</span>
                                            @elseif ($item['status'] === 'izin')
                                                <span class="badge badge-light-info fw-bold">Izin</span>
                                            @elseif ($item['status'] === 'alpha')
                                                <span class="badge badge-light-danger fw-bold">Alpha</span>
                                            @else
                                                <span class="badge badge-light shadow-xs text-gray-600 fw-bold">Belum Absen</span>
                                            @endif
                                        </td>
                                        <td class="pe-4 fs-7 text-gray-600">
                                            {{ $item['keterangan'] ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="card card-flush shadow-sm p-8 text-center text-muted">
                <i class="bi bi-info-circle fs-2x mb-3 text-gray-400"></i>
                <div>Anda belum ditugaskan sebagai Wali Kelas pada kelas aktif manapun.</div>
            </div>
        @endforelse
    </div>

    <!-- Kolom Kanan: Feed Aktivitas Presensi Siswa Realtime -->
    <div class="col-xl-5">
        <div class="card card-flush h-xl-100 shadow-sm">
            <div class="card-header pt-7 mb-3">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-boldest text-gray-800 fs-4">Aktivitas Presensi Terkini</span>
                    <span class="text-gray-400 mt-1 fw-bold fs-7">Presensi siswa kelas wali hari ini</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                @if($recentActivities->isNotEmpty())
                    <div class="timeline-label">
                        @foreach($recentActivities as $act)
                            <div class="timeline-item">
                                <div class="timeline-label fw-boldest text-gray-800 fs-7" style="width: 70px;">
                                    {{ $act->jam_masuk ? \Carbon\Carbon::parse($act->jam_masuk)->format('H:i') : \Carbon\Carbon::parse($act->updated_at)->format('H:i') }}
                                </div>
                                <div class="timeline-badge">
                                    @if($act->status === 'hadir')
                                        <i class="fa fa-genderless text-success fs-1"></i>
                                    @elseif($act->status === 'terlambat')
                                        <i class="fa fa-genderless text-warning fs-1"></i>
                                    @elseif($act->status === 'sakit')
                                        <i class="fa fa-genderless text-primary fs-1"></i>
                                    @elseif($act->status === 'izin')
                                        <i class="fa fa-genderless text-info fs-1"></i>
                                    @else
                                        <i class="fa fa-genderless text-danger fs-1"></i>
                                    @endif
                                </div>
                                <div class="timeline-content fw-bold text-gray-800 ps-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fs-7 fw-boldest">{{ $act->siswa ? $act->siswa->nama : 'Siswa' }}</span>
                                        <span class="badge badge-light-{{ $act->status === 'hadir' ? 'success' : ($act->status === 'terlambat' ? 'warning' : ($act->status === 'sakit' ? 'primary' : ($act->status === 'izin' ? 'info' : 'danger'))) }} fs-8">
                                            {{ ucfirst($act->status) }}
                                        </span>
                                    </div>
                                    <div class="text-gray-500 fs-8 fw-normal mt-1">
                                        {{ $act->keterangan ?? ($act->status === 'hadir' ? 'Presensi tepat waktu' : ($act->status === 'terlambat' ? 'Presensi terlambat' : 'Surat keterangan ' . $act->status)) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-10">
                        <i class="bi bi-clock-history fs-2x mb-3 text-gray-400 d-block"></i>
                        <div class="fs-7 text-gray-600 fw-semibold">Belum ada aktivitas presensi siswa tercatat hari ini.</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!--end::Row 2-->

</x-base-layout>
