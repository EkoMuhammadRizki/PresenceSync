<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => ''
])

<!--begin::Welcome Card-->
<div class="card mb-8">
    <div class="card-body p-9">
        <div class="d-flex align-items-center">
            <div class="symbol symbol-60px symbol-circle me-5">
                <div class="symbol-label fs-1 bg-light-danger text-danger fw-bolder">
                    {{ substr($user->first_name, 0, 1) }}
                </div>
            </div>
            <div class="flex-grow-1">
                <h1 class="text-gray-800 fw-boldest mb-1">{{ $user->name }}</h1>
                <div class="text-muted fw-bold fs-6">Bagian Kesiswaan &bull; Rekap Seluruh Sekolah</div>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge badge-light-danger fs-7 fw-bold px-4 py-3">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen049.svg", "svg-icon-4 me-1") !!}
                    Kesiswaan
                </span>
            </div>
        </div>
    </div>
</div>
<!--end::Welcome Card-->

@php
    $daysIndo = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
    ];
    $todayDay = $daysIndo[now()->format('l')] ?? 'Senin';
    $todayDate = now()->translatedFormat('d F Y');
@endphp

<!--begin::Info Cards-->
<div class="row g-6 g-xl-9 mb-8">
    <div class="col-md-3">
        <div class="card card-dashed flex-center min-w-175px my-3 p-6">
            <span class="fs-4 fw-bold text-primary pb-1px">Total Siswa</span>
            <span class="fs-2hx fw-bolder text-dark">{{ $totalSiswa }}</span>
            <span class="fs-7 fw-bold text-gray-400">Seluruh Sekolah</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-dashed flex-center min-w-175px my-3 p-6">
            <span class="fs-4 fw-bold text-success pb-1px">Hadir</span>
            <span class="fs-2hx fw-bolder text-success">{{ $hadirHariIni }}</span>
            <span class="fs-7 fw-bold text-gray-400">Hari Ini (Termasuk Terlambat: {{ $terlambatHariIni }})</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-dashed flex-center min-w-175px my-3 p-6">
            <span class="fs-4 fw-bold text-warning pb-1px">Izin / Sakit</span>
            <span class="fs-2hx fw-bolder text-warning">{{ $izinSakitHariIni }}</span>
            <span class="fs-7 fw-bold text-gray-400">Hari Ini</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-dashed flex-center min-w-175px my-3 p-6">
            <span class="fs-4 fw-bold text-danger pb-1px">Belum Absen</span>
            <span class="fs-2hx fw-bolder text-danger">{{ $belumAbsen }}</span>
            <span class="fs-7 fw-bold text-gray-400">Hari Ini</span>
        </div>
    </div>
</div>
<!--end::Info Cards-->

<!--begin::Rekap Per Kelas-->
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bolder">Rekap Kehadiran Per Kelas</h3>
        </div>
        <div class="card-toolbar">
            <span class="text-muted fw-bold fs-7">{{ $todayDay }}, {{ $todayDate }}</span>
        </div>
    </div>

    <div class="card-body py-4">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_rekap_kesiswaan">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-50px">No</th>
                    <th class="min-w-100px">Kelas</th>
                    <th class="min-w-150px">Wali Kelas</th>
                    <th class="min-w-80px text-center">Siswa</th>
                    <th class="min-w-80px text-center">Hadir</th>
                    <th class="min-w-80px text-center">Terlambat</th>
                    <th class="min-w-80px text-center">Izin</th>
                    <th class="min-w-80px text-center">Sakit</th>
                    <th class="min-w-80px text-center">Alpha</th>
                    <th class="min-w-100px text-center">Persentase</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @foreach ($rekapKelas as $index => $rekap)
                    @php
                        $persentase = $rekap['total_siswa'] > 0
                            ? round(($rekap['hadir'] / $rekap['total_siswa']) * 100, 1)
                            : 0;
                    @endphp
                    <tr>
                        <td class="text-gray-800">{{ $index + 1 }}</td>
                        <td class="fw-bolder">{{ $rekap['kelas']->tingkat }} {{ $rekap['kelas']->nama }}</td>
                        <td>{{ $rekap['wali_kelas'] }}</td>
                        <td class="text-center">{{ $rekap['total_siswa'] }}</td>
                        <td class="text-center">
                            <span class="badge badge-light-success">{{ $rekap['hadir'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light-warning">{{ $rekap['terlambat'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light-info">{{ $rekap['izin'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light-primary">{{ $rekap['sakit'] }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light-danger">{{ $rekap['alpha'] }}</span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center">
                                <span class="fw-bolder me-2 {{ $persentase >= 80 ? 'text-success' : ($persentase >= 50 ? 'text-warning' : 'text-danger') }}">
                                    {{ $persentase }}%
                                </span>
                                <div class="progress h-6px w-60px">
                                    <div class="progress-bar {{ $persentase >= 80 ? 'bg-success' : ($persentase >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                         style="width: {{ $persentase }}%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!--end::Rekap Per Kelas-->

@section('scripts')
<script>
$(document).ready(function() {
    $('#kt_table_rekap_kesiswaan').DataTable({
        dom:"<'table-responsive'tr><'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'li><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>",
        info: true,
        order: [],
        pageLength: 10,
        lengthChange: true
    });
});
</script>
@endsection
</x-base-layout>
