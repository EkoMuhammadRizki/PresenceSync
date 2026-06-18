<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => ''
])

<!--begin::Welcome Card-->
<div class="card mb-8">
    <div class="card-body p-9">
        <div class="d-flex align-items-center">
            <div class="symbol symbol-60px symbol-circle me-5">
                <div class="symbol-label fs-1 bg-light-primary text-primary fw-bolder">
                    {{ substr($siswa->nama, 0, 1) }}
                </div>
            </div>
            <div class="flex-grow-1">
                <h1 class="text-gray-800 fw-boldest mb-1">{{ $siswa->nama }}</h1>
                <div class="text-muted fw-bold fs-6">Siswa Kelas: {{ $siswa->kelas ? $siswa->kelas->tingkat . ' ' . $siswa->kelas->nama : 'Belum Masuk Kelas' }}</div>
            </div>
        </div>
    </div>
</div>
<!--end::Welcome Card-->

<!--begin::Info Cards-->
<div class="row g-6 g-xl-9 mb-8">
    <div class="col-md-4">
        <!--begin::Card-->
        <div class="card card-dashed flex-center min-w-175px my-3 p-6">
            <span class="fs-4 fw-bold text-info pb-1px">Status Hari Ini</span>
            @if ($hasCheckedInToday && $kehadiranHariIni)
                @if ($kehadiranHariIni->status === 'hadir')
                    <span class="fs-2hx fw-bolder text-success">Tepat Waktu</span>
                    <span class="fs-7 fw-bold text-gray-400">Jam Masuk: {{ $kehadiranHariIni->jam_masuk }}</span>
                @elseif ($kehadiranHariIni->status === 'terlambat')
                    <span class="fs-2hx fw-bolder text-warning">Terlambat</span>
                    <span class="fs-7 fw-bold text-gray-400">Jam Masuk: {{ $kehadiranHariIni->jam_masuk }}</span>
                @elseif ($kehadiranHariIni->status === 'sakit')
                    <span class="fs-2hx fw-bolder text-primary">Sakit</span>
                    <span class="fs-7 fw-bold text-gray-400">{{ $kehadiranHariIni->keterangan }}</span>
                @elseif ($kehadiranHariIni->status === 'izin')
                    <span class="fs-2hx fw-bolder text-info">Izin</span>
                    <span class="fs-7 fw-bold text-gray-400">{{ $kehadiranHariIni->keterangan }}</span>
                @endif
            @else
                <span class="fs-2hx fw-bolder text-danger">Belum Absen</span>
                <span class="fs-7 fw-bold text-gray-400">Silakan lakukan presensi hari ini</span>
            @endif
        </div>
        <!--end::Card-->
    </div>

    <div class="col-md-4">
        <!--begin::Card-->
        <div class="card card-dashed flex-center min-w-175px my-3 p-6">
            <span class="fs-4 fw-bold text-success pb-1px">Total Hadir</span>
            <span class="fs-2hx fw-bolder text-dark">{{ $kehadirans->whereIn('status', ['hadir', 'terlambat'])->count() }}</span>
            <span class="fs-7 fw-bold text-gray-400">Pertemuan Semester Ini</span>
        </div>
        <!--end::Card-->
    </div>

    <div class="col-md-4">
        <!--begin::Card-->
        <div class="card card-dashed flex-center min-w-175px my-3 p-6">
            <span class="fs-4 fw-bold text-warning pb-1px">Ketidakhadiran & Izin</span>
            <span class="fs-2hx fw-bolder text-dark">{{ $kehadirans->whereIn('status', ['sakit', 'izin', 'alpha'])->count() }}</span>
            <span class="fs-7 fw-bold text-gray-400">Sakit, Izin & Alpha</span>
        </div>
        <!--end::Card-->
    </div>
</div>
<!--end::Info Cards-->

<!--begin::Card - Riwayat Kehadiran-->
<div class="card">
    <!--begin::Card Header-->
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bolder">Daftar Kehadiran</h3>
        </div>
        <div class="card-toolbar">
            @if (!$hasCheckedInToday)
                <!-- Tombol Izin -->
                <button type="button" class="btn btn-warning btn-sm me-3" data-bs-toggle="modal" data-bs-target="#modal_izin">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg", "svg-icon-3") !!}
                    Izin
                </button>

                <!-- Tombol Presensi -->
                <form action="{{ route('siswa.presensi') }}" method="POST" id="form_presensi">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen048.svg", "svg-icon-3") !!}
                        Presensi
                    </button>
                </form>
            @else
                <button type="button" class="btn btn-light-success btn-sm disabled" disabled>
                    {!! theme()->getSvgIcon("icons/duotune/general/gen043.svg", "svg-icon-3") !!}
                    Sudah Absen Hari Ini
                </button>
            @endif
        </div>
    </div>
    <!--end::Card Header-->

    <!--begin::Card Body-->
    <div class="card-body py-4">
        @php
            $daysIndo = [
                'Sunday' => 'Minggu',
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
            ];
        @endphp

        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_kehadiran_siswa">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="min-w-150px">Nama</th>
                    <th class="min-w-100px">Hari</th>
                    <th class="min-w-120px">Tanggal</th>
                    <th class="min-w-100px">Jam Masuk</th>
                    <th class="min-w-150px">Keterangan Masuk</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @foreach ($kehadirans as $kh)
                    @php
                        $hari = $daysIndo[\Carbon\Carbon::parse($kh->tanggal)->format('l')] ?? 'Senin';
                        $tanggalFormatted = \Carbon\Carbon::parse($kh->tanggal)->translatedFormat('d F Y');
                    @endphp
                    <tr>
                        <td>{{ $siswa->nama }}</td>
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
    <!--end::Card Body-->
</div>
<!--end::Card-->

<!--begin::Modal Izin-->
<div class="modal fade" id="modal_izin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-450px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Formulir Izin / Sakit</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-5">
                <form class="form" action="{{ route('siswa.izin') }}" method="POST" id="form_izin_siswa">
                    @csrf
                    
                    <!-- Pilihan Status -->
                    <div class="fv-row mb-6">
                        <label class="required fw-bold fs-6 mb-2">Jenis Pengajuan</label>
                        <select name="status" class="form-select form-select-solid" required>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                        </select>
                    </div>

                    <!-- Keterangan Alasan -->
                    <div class="fv-row mb-8">
                        <label class="required fw-bold fs-6 mb-2">Keterangan / Alasan</label>
                        <textarea name="keterangan" class="form-control form-control-solid" rows="4" placeholder="Tuliskan keterangan detail alasan izin..." required></textarea>
                    </div>

                    <!-- Submit / Batal -->
                    <div class="text-center pt-5">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning" id="btn_submit_izin">
                            <span class="indicator-label">Kirim Pengajuan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::Modal Izin-->

@section('scripts')
<script>
$(document).ready(function() {
    $('#kt_table_kehadiran_siswa').DataTable({
        dom:'<'table-responsive'tr><'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'li><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>,
        info: true,
        order: [],
        pageLength: 10,
        lengthChange: true
    });

    // Konfirmasi Presensi menggunakan SweetAlert2
    $('#form_presensi').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            icon: 'question',
            title: 'Mulai Presensi?',
            text: 'Presensi masuk akan dicatat sesuai waktu server saat ini.',
            showCancelButton: true,
            confirmButtonText: 'Ya, Presensi',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#50CD89',
            cancelButtonColor: '#7E8299'
        }).then(function(result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Konfirmasi Pengajuan Izin menggunakan SweetAlert2
    $('#form_izin_siswa').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            icon: 'warning',
            title: 'Kirim Pengajuan?',
            text: 'Apakah Anda yakin ingin mengajukan izin/sakit hari ini?',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#F1416C',
            cancelButtonColor: '#7E8299'
        }).then(function(result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{{ session('success') }}',
        confirmButtonText: 'Selesai',
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
