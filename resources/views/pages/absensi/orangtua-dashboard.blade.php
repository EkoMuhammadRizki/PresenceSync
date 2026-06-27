<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => ''
])

<!--begin::Welcome Card-->
<div class="card mb-8">
    <div class="card-body p-9">
        <div class="d-flex align-items-center">
            <div class="symbol symbol-60px symbol-circle me-5">
                <div class="symbol-label fs-1 bg-light-warning text-warning fw-bolder">
                    {{ substr($user->first_name, 0, 1) }}
                </div>
            </div>
            <div class="flex-grow-1">
                <h1 class="text-gray-800 fw-boldest mb-1">{{ $user->name }}</h1>
                <div class="text-muted fw-bold fs-6">
                    Orang Tua &bull;
                    {{ collect($dataAnak)->pluck('siswa.nama')->join(', ') }}
                </div>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge badge-light-warning fs-7 fw-bold px-4 py-3">
                    {!! theme()->getSvgIcon("icons/duotune/communication/com006.svg", "svg-icon-4 me-1") !!}
                    Orang Tua
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
@endphp

@foreach ($dataAnak as $anakIndex => $data)
<!--begin::Child Section-->
<div class="mb-8">
    <!--begin::Info Cards-->
    <div class="row g-6 g-xl-9 mb-6">
        <div class="col-12">
            <div class="d-flex align-items-center mb-3">
                <div class="symbol symbol-40px symbol-circle me-3">
                    <div class="symbol-label fs-5 bg-light-primary text-primary fw-bolder">
                        {{ substr($data['siswa']->nama, 0, 1) }}
                    </div>
                </div>
                <div>
                    <h3 class="fw-bolder text-gray-800 mb-0">{{ $data['siswa']->nama }}</h3>
                    <span class="text-muted fw-bold fs-7">
                        Kelas: {{ $data['siswa']->kelas ? $data['siswa']->kelas->tingkat . ' ' . $data['siswa']->kelas->nama : 'Belum Masuk Kelas' }}
                        &bull; NIS: {{ $data['siswa']->nis ?? '-' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-dashed flex-center min-w-175px my-3 p-6">
                <span class="fs-4 fw-bold text-info pb-1px">Status Hari Ini</span>
                @if ($data['kehadiranHariIni'])
                    @if ($data['kehadiranHariIni']->status === 'hadir')
                        <span class="fs-2hx fw-bolder text-success">Tepat Waktu</span>
                        <span class="fs-7 fw-bold text-gray-400">Jam Masuk: {{ $data['kehadiranHariIni']->jam_masuk }}</span>
                    @elseif ($data['kehadiranHariIni']->status === 'terlambat')
                        <span class="fs-2hx fw-bolder text-warning">Terlambat</span>
                        <span class="fs-7 fw-bold text-gray-400">Jam Masuk: {{ $data['kehadiranHariIni']->jam_masuk }}</span>
                    @elseif ($data['kehadiranHariIni']->status === 'sakit')
                        <span class="fs-2hx fw-bolder text-primary">Sakit</span>
                        <span class="fs-7 fw-bold text-gray-400">{{ $data['kehadiranHariIni']->keterangan }}</span>
                    @elseif ($data['kehadiranHariIni']->status === 'izin')
                        <span class="fs-2hx fw-bolder text-info">Izin</span>
                        <span class="fs-7 fw-bold text-gray-400">{{ $data['kehadiranHariIni']->keterangan }}</span>
                    @endif
                @else
                    <span class="fs-2hx fw-bolder text-danger">Belum Absen</span>
                    <span class="fs-7 fw-bold text-gray-400">Anak Anda belum melakukan presensi</span>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-dashed flex-center min-w-175px my-3 p-6">
                <span class="fs-4 fw-bold text-success pb-1px">Total Hadir</span>
                <span class="fs-2hx fw-bolder text-dark">{{ $data['totalHadir'] }}</span>
                <span class="fs-7 fw-bold text-gray-400">Pertemuan Semester Ini</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-dashed flex-center min-w-175px my-3 p-6">
                <span class="fs-4 fw-bold text-warning pb-1px">Ketidakhadiran</span>
                <span class="fs-2hx fw-bolder text-dark">{{ $data['totalAbsen'] }}</span>
                <span class="fs-7 fw-bold text-gray-400">Sakit, Izin & Alpha</span>
            </div>
        </div>
    </div>
    <!--end::Info Cards-->

    <!--begin::Riwayat Kehadiran-->
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <h3 class="fw-bolder">Riwayat Kehadiran — {{ $data['siswa']->nama }}</h3>
            </div>
        </div>

        <div class="card-body py-4">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_orangtua_{{ $anakIndex }}">
                <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                        <th class="w-50px">No</th>
                        <th class="min-w-100px">Hari</th>
                        <th class="min-w-120px">Tanggal</th>
                        <th class="min-w-100px">Jam Masuk</th>
                        <th class="min-w-150px">Status</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">
                    @foreach ($data['kehadirans'] as $kh)
                        @php
                            $dateObj = \Carbon\Carbon::parse($kh->tanggal);
                            $hari = $daysIndo[$dateObj->format('l')] ?? 'Senin';
                            $tanggalFormatted = $dateObj->translatedFormat('d F Y');
                            $dayNumber = $dateObj->day;
                        @endphp
                        <tr>
                            <td class="text-gray-800">{{ $dayNumber }}</td>
                            <td>{{ $hari }}</td>
                            <td>{{ $tanggalFormatted }}</td>
                            <td>{{ $kh->jam_masuk ?? '-' }}</td>
                            <td>
                                @if ($kh->status === 'hadir')
                                    <span class="badge badge-light-success fw-bolder">Tepat</span>
                                @elseif ($kh->status === 'terlambat')
                                    <span class="badge badge-light-warning fw-bolder">Terlambat</span>
                                @elseif ($kh->status === 'sakit')
                                    <span class="badge badge-light-primary fw-bolder">Sakit</span>
                                @elseif ($kh->status === 'izin')
                                    <span class="badge badge-light-info fw-bolder">Izin</span>
                                @else
                                    <span class="badge badge-light-danger fw-bolder">Alpha</span>
                                @endif

                                @if($kh->keterangan)
                                    <div class="fs-7 text-muted fw-normal mt-1">{{ $kh->keterangan }}</div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!--end::Riwayat Kehadiran-->
</div>
<!--end::Child Section-->
@endforeach

@section('scripts')
<script>
$(document).ready(function() {
    @foreach ($dataAnak as $anakIndex => $data)
    $('#kt_table_orangtua_{{ $anakIndex }}').DataTable({
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
