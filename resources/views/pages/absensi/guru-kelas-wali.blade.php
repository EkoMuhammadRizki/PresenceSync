<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => '
        <a href="' . route('guru.dashboard') . '" class="btn btn-sm btn-light">
            ' . theme()->getSvgIcon("icons/duotune/arrows/arr063.svg", "svg-icon-4") . ' Kembali
        </a>'
])

@if (!$kelas)
<div class="card mt-2">
    <div class="card-body p-10 text-center">
        <div class="text-gray-600 fs-5">
            {!! theme()->getSvgIcon("icons/duotune/general/gen046.svg", "svg-icon-3x text-gray-300 mb-4") !!}
            <div class="fw-bold mt-3">Anda belum menjadi wali kelas manapun.</div>
            <div class="text-muted fs-7 mt-1">Hubungi administrator untuk pengaturan kelas.</div>
        </div>
    </div>
</div>
@else

<!--begin::Header Card-->
<div class="card mb-5 mb-xl-10 mt-2">
    <div class="card-body pt-9 pb-0">
        <!--begin::Details-->
        <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
            <!--begin: Pic-->
            <div class="me-7 mb-4">
                <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                    <div class="symbol-label bg-light-primary" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                        {!! theme()->getSvgIcon("icons/duotune/communication/com014.svg", "svg-icon-4x svg-icon-primary") !!}
                    </div>
                </div>
            </div>
            <!--end::Pic-->

            <!--begin::Info-->
            <div class="flex-grow-1">
                <!--begin::Title-->
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <!--begin::User-->
                    <div class="d-flex flex-column">
                        <!--begin::Name-->
                        <div class="d-flex align-items-center mb-2">
                            <span class="text-gray-800 fs-2 fw-bolder me-1">{{ $kelas->nama_lengkap }}</span>
                            <span class="badge badge-light-primary fw-bolder ms-2 fs-8 py-1 px-3">Kelas Wali</span>
                            <span class="badge badge-light-success fw-bolder ms-2 fs-8 py-1 px-3">Aktif</span>
                        </div>
                        <!--end::Name-->

                        <!--begin::Info-->
                        <div class="d-flex flex-wrap fw-bold fs-6 mb-4 pe-2">
                            <span class="d-flex align-items-center text-gray-400 me-5 mb-2">
                                {!! theme()->getSvgIcon("icons/duotune/communication/com006.svg", "svg-icon-4 me-1") !!}
                                Wali Kelas: {{ $guru->nama }}
                            </span>
                            <span class="d-flex align-items-center text-gray-400 me-5 mb-2">
                                {!! theme()->getSvgIcon("icons/duotune/general/gen018.svg", "svg-icon-4 me-1") !!}
                                Tingkat: {{ $kelas->tingkat }}
                            </span>
                            <span class="d-flex align-items-center text-gray-400 mb-2">
                                {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-4 me-1") !!}
                                Status: Aktif
                            </span>
                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end::User-->
                </div>
                <!--end::Title-->

                <!--begin::Stats-->
                <div class="d-flex flex-wrap flex-stack">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-column flex-grow-1 pe-8">
                        <!--begin::Stats-->
                        <div class="d-flex flex-wrap">
                            <!-- Stat 1: Total Siswa -->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-2 fw-bolder text-gray-800">{{ $siswas->count() }}</div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Total Siswa</div>
                            </div>
                            
                            <!-- Stat 2: Total Kehadiran -->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-2 fw-bolder text-gray-800">{{ $kehadirans->total() ?? 0 }}</div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Total Kehadiran</div>
                            </div>

                            <!-- Stat 3: Rata-rata Kehadiran -->
                            @php
                                $avgPresence = count($rekap) > 0 ? round(collect($rekap)->avg('persentase')) : 0;
                            @endphp
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-2 fw-bolder text-gray-800">{{ $avgPresence }}%</div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Rata-rata Kehadiran</div>
                            </div>
                        </div>
                        <!--end::Stats-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Stats-->
            </div>
            <!--end::Info-->
        </div>
        <!--end::Details-->

        <!--begin::Navs-->
        <div class="d-flex overflow-auto h-55px">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder flex-nowrap" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary me-6 active" data-bs-toggle="tab" href="#tab_siswa" role="tab">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-2 me-1") !!}
                        Data Siswa
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary me-6" data-bs-toggle="tab" href="#tab_kehadiran" role="tab">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen049.svg", "svg-icon-2 me-1") !!}
                        Riwayat Kehadiran
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary me-6" data-bs-toggle="tab" href="#tab_pelaporan" role="tab">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen005.svg", "svg-icon-2 me-1") !!}
                        Pelaporan
                    </a>
                </li>
            </ul>
        </div>
        <!--begin::Navs-->
    </div>
</div>
<!--end::Header Card-->

<!--begin::Tab Content-->
<div class="tab-content">
    {{-- TAB 1: Data Siswa --}}
    <div class="tab-pane fade show active" id="tab_siswa" role="tabpanel">
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title d-flex align-items-center gap-3">
                    <h3 class="fw-bolder mb-0">Daftar Siswa</h3>
                    <div class="d-flex align-items-center position-relative my-1">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-2 position-absolute ms-4") !!}
                        <input type="text" id="search_siswa" class="form-control form-control-solid w-200px ps-12" placeholder="Cari siswa..." />
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('guru.kelas-wali.export-pdf') }}" class="btn btn-sm btn-danger">
                            {!! theme()->getSvgIcon("icons/duotune/files/fil003.svg", "svg-icon-4") !!} Export PDF
                        </a>
                        <a href="{{ route('guru.kelas-wali.export-excel') }}" class="btn btn-sm btn-success">
                            {!! theme()->getSvgIcon("icons/duotune/files/fil021.svg", "svg-icon-4") !!} Export Excel
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body py-4">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_siswa">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="w-30px">No</th>
                            <th class="min-w-100px">NISN</th>
                            <th class="min-w-150px">Nama</th>
                            <th class="min-w-90px">Jenis Kelamin</th>
                            <th class="min-w-100px">Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @forelse ($siswas as $i => $siswa)
                        @php
                            $initial = strtoupper(substr($siswa->nama, 0, 1));
                            $bgColors = ['success', 'primary', 'warning', 'danger', 'info'];
                            $bgColor = $bgColors[ord($initial) % count($bgColors)];
                            $pct = $rekap[$siswa->id]['persentase'] ?? 0;
                            $badgeClass = $pct >= 85 ? 'badge-light-success' : ($pct >= 70 ? 'badge-light-warning' : 'badge-light-danger');
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $siswa->nisn ?? '-' }}</td>
                            <td class="d-flex align-items-center">
                                <div class="symbol symbol-circle symbol-35px overflow-hidden me-3">
                                    <div class="symbol-label fs-6 bg-light-{{ $bgColor }} text-{{ $bgColor }} fw-bolder">{{ $initial }}</div>
                                </div>
                                <span>{{ $siswa->nama }}</span>
                            </td>
                            <td>{{ $siswa->jenis_kelamin ?? '-' }}</td>
                            <td><span class="badge {{ $badgeClass }} fw-bolder">{{ $pct }}%</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">Belum ada siswa di kelas ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- TAB 2: Riwayat Kehadiran --}}
    <div class="tab-pane fade" id="tab_kehadiran" role="tabpanel">
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <h3 class="fw-bolder">Riwayat Kehadiran</h3>
                </div>
            </div>
            <div class="card-body py-4">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_kehadiran">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="w-30px">No</th>
                            <th class="min-w-150px">Nama Siswa</th>
                            <th class="min-w-100px">Tanggal</th>
                            <th class="min-w-100px">Jam Masuk</th>
                            <th class="min-w-90px">Status</th>
                            <th class="min-w-150px">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @forelse ($kehadirans as $i => $kh)
                        <tr>
                            <td>{{ $kehadirans->firstItem() + $i }}</td>
                            <td>{{ $kh->siswa->nama ?? '-' }}</td>
                            <td>{{ Carbon\Carbon::parse($kh->tanggal)->format('d M Y') }}</td>
                            <td>
                                @if ($kh->jam_masuk)
                                    <span class="badge badge-light-primary fw-bolder">{{ Carbon\Carbon::parse($kh->jam_masuk)->format('H:i') }}</span>
                                @else
                                    <span class="badge badge-light-secondary fw-bold">-</span>
                                @endif
                            </td>
                            <td>
                                @if ($kh->status === 'hadir')
                                    <span class="badge badge-light-success fw-bolder">Hadir</span>
                                @elseif ($kh->status === 'terlambat')
                                    <span class="badge badge-light-warning fw-bolder">Terlambat</span>
                                @elseif ($kh->status === 'sakit')
                                    <span class="badge badge-light-primary fw-bolder">Sakit</span>
                                @elseif ($kh->status === 'izin')
                                    <span class="badge badge-light-info fw-bolder">Izin</span>
                                @else
                                    <span class="badge badge-light-danger fw-bolder">Alpha</span>
                                @endif
                            </td>
                            <td>{{ $kh->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">Belum ada data kehadiran.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-end mt-5">
                    {{ $kehadirans->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 3: Pelaporan --}}
    <div class="tab-pane fade" id="tab_pelaporan" role="tabpanel">
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <h3 class="fw-bolder">Rekap Kehadiran</h3>
                </div>
            </div>
            <div class="card-body py-4">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_pelaporan">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="w-30px">No</th>
                            <th class="min-w-150px">Nama Siswa</th>
                            <th class="min-w-100px">NISN</th>
                            <th class="min-w-80px">Total Hadir</th>
                            <th class="min-w-80px">Total Data</th>
                            <th class="min-w-100px">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @forelse ($siswas as $i => $siswa)
                        @php
                            $r = $rekap[$siswa->id] ?? ['hadir' => 0, 'total' => 0, 'persentase' => 0];
                            $badgeClass = $r['persentase'] >= 85 ? 'badge-light-success' : ($r['persentase'] >= 70 ? 'badge-light-warning' : 'badge-light-danger');
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $siswa->nama }}</td>
                            <td>{{ $siswa->nisn ?? '-' }}</td>
                            <td><span class="badge badge-light-primary fw-bolder">{{ $r['hadir'] }}</span></td>
                            <td><span class="badge badge-light-secondary fw-bolder">{{ $r['total'] }}</span></td>
                            <td><span class="badge {{ $badgeClass }} fw-bolder">{{ $r['persentase'] }}%</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">Belum ada data untuk dilaporkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!--end::Tab Content-->
@endif

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#kt_table_siswa').DataTable({
        dom: "<'table-responsive'tr><'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'li><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>",
        info: true,
        order: [],
        pageLength: 10,
        lengthChange: true,
        columnDefs: [{ orderable: false, targets: 0 }]
    });

    $('#search_siswa').on('keyup', function() {
        table.search(this.value).draw();
    });
});
</script>
@endsection
</x-base-layout>
