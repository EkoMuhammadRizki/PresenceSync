<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => ''
])

<!--begin::Welcome Card-->
<div class="card mb-8">
    <div class="card-body p-9">
        <div class="d-flex align-items-center">
            <div class="symbol symbol-60px symbol-circle me-5">
                <div class="symbol-label fs-1 bg-light-info text-info fw-bolder">
                    {{ substr($guru->nama, 0, 1) }}
                </div>
            </div>
            <div class="flex-grow-1">
                <h1 class="text-gray-800 fw-boldest mb-1">{{ $guru->nama }}</h1>
                <div class="text-muted fw-bold fs-6">NIP: {{ $guru->nip }} &bull; Wali Kelas</div>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge badge-light-info fs-7 fw-bold px-4 py-3">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen049.svg", "svg-icon-4 me-1") !!}
                    Guru
                </span>
            </div>
        </div>
    </div>
</div>
<!--end::Welcome Card-->

<!--begin::Info Cards-->
<div class="row g-6 g-xl-9 mb-8">
    <div class="col-md-3">
        <div class="card card-dashed flex-center min-w-175px my-3 p-6">
            <span class="fs-4 fw-bold text-info pb-1px">Total Siswa</span>
            <span class="fs-2hx fw-bolder text-dark">{{ $totalSiswa }}</span>
            <span class="fs-7 fw-bold text-gray-400">Di Kelas Anda</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-dashed flex-center min-w-175px my-3 p-6">
            <span class="fs-4 fw-bold text-success pb-1px">Hadir Hari Ini</span>
            <span class="fs-2hx fw-bolder text-success">{{ $hadirHariIni }}</span>
            <span class="fs-7 fw-bold text-gray-400">Termasuk Terlambat</span>
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
            <span class="fs-2hx fw-bolder text-danger">{{ $alphaHariIni }}</span>
            <span class="fs-7 fw-bold text-gray-400">Hari Ini</span>
        </div>
    </div>
</div>
<!--end::Info Cards-->

<!--begin::Kelas Detail-->
@foreach ($kelasDetail as $detail)
<div class="card mb-8">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bolder">Kelas {{ $detail['kelas']->tingkat }} {{ $detail['kelas']->nama }}</h3>
        </div>
        <div class="card-toolbar">
            <span class="badge badge-light-success fs-7 fw-bold px-4 py-2">
                {{ $detail['hadir'] }} / {{ $detail['total'] }} Hadir
            </span>
        </div>
    </div>

    <div class="card-body py-4">
        @php
            $daysIndo = [
                'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
            ];
            $todayDay = $daysIndo[now()->format('l')] ?? 'Senin';
            $todayDate = now()->translatedFormat('d F Y');
        @endphp

        <div class="text-muted fw-bold fs-7 mb-4">
            {{ $todayDay }}, {{ $todayDate }}
        </div>

        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_guru_kelas_{{ $detail['kelas']->id }}">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-50px">No</th>
                    <th class="min-w-150px">Nama Siswa</th>
                    <th class="min-w-100px">NIS</th>
                    <th class="min-w-100px">Jam Masuk</th>
                    <th class="min-w-120px">Status</th>
                    <th class="min-w-150px">Keterangan</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @foreach ($detail['siswa'] as $index => $data)
                    <tr>
                        <td class="text-gray-800">{{ $index + 1 }}</td>
                        <td>{{ $data['siswa']->nama }}</td>
                        <td>{{ $data['siswa']->nis ?? '-' }}</td>
                        <td>{{ $data['jam_masuk'] ?? '-' }}</td>
                        <td>
                            @if ($data['status'] === 'hadir')
                                <span class="badge badge-light-success fw-bolder">Hadir</span>
                            @elseif ($data['status'] === 'terlambat')
                                <span class="badge badge-light-warning fw-bolder">Terlambat</span>
                            @elseif ($data['status'] === 'sakit')
                                <span class="badge badge-light-primary fw-bolder">Sakit</span>
                            @elseif ($data['status'] === 'izin')
                                <span class="badge badge-light-info fw-bolder">Izin</span>
                            @elseif ($data['status'] === 'alpha')
                                <span class="badge badge-light-danger fw-bolder">Alpha</span>
                            @else
                                <span class="badge badge-light fw-bolder">Belum Absen</span>
                            @endif
                        </td>
                        <td>{{ $data['keterangan'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach
<!--end::Kelas Detail-->

@if (empty($kelasDetail))
<div class="card">
    <div class="card-body p-10 text-center">
        <div class="text-gray-600 fs-5">
            {!! theme()->getSvgIcon("icons/duotune/general/gen046.svg", "svg-icon-3x text-gray-300 mb-4") !!}
            <div class="fw-bold mt-3">Anda belum menjadi wali kelas manapun.</div>
            <div class="text-muted fs-7 mt-1">Hubungi administrator untuk pengaturan kelas.</div>
        </div>
    </div>
</div>
@endif

@section('scripts')
<script>
$(document).ready(function() {
    @foreach ($kelasDetail as $detail)
    $('#kt_table_guru_kelas_{{ $detail['kelas']->id }}').DataTable({
        dom:"<'table-responsive'tr><'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'li><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>",
        info: true,
        order: [],
        pageLength: 10,
        lengthChange: true
    });
    @endforeach
});
</script>
@endsection
</x-base-layout>
