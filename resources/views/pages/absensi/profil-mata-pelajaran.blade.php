<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'customBreadcrumbs' => [
        ['title' => 'Home', 'path' => 'index', 'active' => false],
        ['title' => 'Master Data', 'path' => '', 'active' => false],
        ['title' => 'Mata Pelajaran', 'path' => 'absensi/master/mata-pelajaran', 'active' => false],
        ['title' => $mataPelajaran->nama, 'path' => '', 'active' => true],
    ],
    'toolbarActions' => '
        <a href="' . theme()->getPageUrl('absensi/master/mata-pelajaran') . '" class="btn btn-sm btn-light">
            ' . theme()->getSvgIcon("icons/duotune/arrows/arr063.svg", "svg-icon-4") . ' Kembali ke Data Mata Pelajaran
        </a>'
])

<!--begin::Navbar Card-->
<div class="card mb-5 mb-xl-10">
    <div class="card-body pt-9 pb-0">
        <!--begin::Details-->
        <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
            <!--begin: Pic-->
            <div class="me-7 mb-4">
                <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                    <div class="symbol-label fs-1 bg-light-info text-info fw-bolder" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">{{ substr($mataPelajaran->nama, 0, 1) }}</div>
                </div>
            </div>
            <!--end::Pic-->

            <!--begin::Info-->
            <div class="flex-grow-1">
                <!--begin::Title-->
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <!--begin::User-->
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center mb-2">
                            <a href="#" class="text-gray-800 text-hover-primary fs-2 fw-bolder me-1">{{ $mataPelajaran->nama }}</a>
                            <span class="badge badge-light-info fw-bolder ms-2 fs-8 py-1 px-3">Mapel</span>
                            <span class="badge badge-light-{{ $mataPelajaran->guru_id ? 'success' : 'warning' }} fw-bolder ms-2 fs-8 py-1 px-3">{{ $mataPelajaran->guru_id ? 'Aktif' : 'Belum Ditentukan' }}</span>
                        </div>
                        <div class="d-flex flex-wrap fw-bold fs-6 mb-4 pe-2">
                            <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                {!! theme()->getSvgIcon("icons/duotune/communication/com006.svg", "svg-icon-4 me-1") !!}
                                Kode: {{ $mataPelajaran->kode }}
                            </a>
                            <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                {!! theme()->getSvgIcon("icons/duotune/general/gen018.svg", "svg-icon-4 me-1") !!}
                                {{ $mataPelajaran->guru->nama ?? 'Belum Ditentukan' }}
                            </a>
                        </div>
                    </div>
                    <!--end::User-->

                    <!--begin::Actions-->
                    <div class="d-flex my-4">
                        <a href="{{ theme()->getPageUrl('absensi/master/mata-pelajaran') }}" class="btn btn-sm btn-light">
                            {!! theme()->getSvgIcon("icons/duotune/arrows/arr063.svg", "svg-icon-3") !!}
                            Kembali
                        </a>
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Title-->

                <!--begin::Stats-->
                <div class="d-flex flex-wrap flex-stack">
                    <div class="d-flex flex-column flex-grow-1 pe-8">
                        <div class="d-flex flex-wrap">
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    {!! theme()->getSvgIcon("icons/duotune/general/gen028.svg", "svg-icon-3 svg-icon-primary me-2") !!}
                                    <div class="fs-2 fw-bolder">{{ $mataPelajaran->jadwalPelajarans->count() }}</div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Total Jadwal</div>
                            </div>
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    {!! theme()->getSvgIcon("icons/duotune/communication/com006.svg", "svg-icon-3 svg-icon-success me-2") !!}
                                    <div class="fs-2 fw-bolder">{{ $mataPelajaran->jadwalPelajarans->pluck('kelas_id')->unique()->count() }}</div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Kelas Diajarkan</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Stats-->
            </div>
            <!--end::Info-->
        </div>
        <!--end::Details-->

        <!--begin::Navs-->
        <div class="d-flex overflow-auto h-55px">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder flex-nowrap">
                <li class="nav-item">
                    <a class="nav-link text-active-primary me-6 active" data-bs-toggle="tab" href="#tab_mapel_info">Informasi Mata Pelajaran</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-active-primary me-6" data-bs-toggle="tab" href="#tab_mapel_jadwal">Jadwal Mengajar</a>
                </li>
            </ul>
        </div>
        <!--end::Navs-->
    </div>
</div>
<!--end::Navbar Card-->

<!--begin::Tab Content-->
<div class="tab-content" id="profileTabContent">
    <!--begin::Tab Pane - Info-->
    <div class="tab-pane fade show active" id="tab_mapel_info" role="tabpanel">
        <div class="row g-5 g-xxl-8">
            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 text-dark">Informasi Mata Pelajaran</span>
                            <span class="text-muted mt-1 fw-bold fs-7">Data utama mata pelajaran</span>
                        </h3>
                    </div>
                    <div class="card-body pt-3">
                        <div class="d-flex align-items-center mb-7"><div class="flex-grow-1"><span class="text-muted fw-bold d-block fs-7">Kode</span><span class="text-gray-800 fw-bolder fs-6">{{ $mataPelajaran->kode }}</span></div></div>
                        <div class="d-flex align-items-center mb-7"><div class="flex-grow-1"><span class="text-muted fw-bold d-block fs-7">Nama</span><span class="text-gray-800 fw-bolder fs-6">{{ $mataPelajaran->nama }}</span></div></div>
                        <div class="d-flex align-items-center mb-7"><div class="flex-grow-1"><span class="text-muted fw-bold d-block fs-7">Guru Pengampu</span><span class="text-gray-800 fw-bolder fs-6">{{ $mataPelajaran->guru->nama ?? 'Belum Ditentukan' }}</span></div></div>
                        <div class="d-flex align-items-center"><div class="flex-grow-1"><span class="text-muted fw-bold d-block fs-7">Total Jadwal</span><span class="text-gray-800 fw-bolder fs-6">{{ $mataPelajaran->jadwalPelajarans->count() }} jadwal</span></div></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 text-dark">Kelas yang Diajarkan</span>
                            <span class="text-muted mt-1 fw-bold fs-7">Daftar kelas target</span>
                        </h3>
                    </div>
                    <div class="card-body pt-3">
                        @php
                            $kelasList = $mataPelajaran->jadwalPelajarans->pluck('kelas')->filter()->unique('id');
                        @endphp
                        @if ($kelasList->count() > 0)
                            @foreach ($kelasList as $kls)
                                <div class="d-flex align-items-center mb-7">
                                    <div class="symbol symbol-50px me-5">
                                        <div class="symbol-label bg-light-primary text-primary fw-bolder fs-5">{{ substr($kls->nama, 0, 1) }}</div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="text-dark fw-bolder text-hover-primary fs-6">{{ $kls->nama }}</span>
                                        <span class="text-muted d-block fw-bold">Tingkat {{ $kls->tingkat }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-10"><span class="text-muted fw-bold">Belum ada kelas yang diajarkan.</span></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Tab Pane - Info-->

    <!--begin::Tab Pane - Jadwal-->
    <div class="tab-pane fade" id="tab_mapel_jadwal" role="tabpanel">
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title"><h3 class="fw-bolder">Jadwal Mengajar</h3></div>
            </div>
            <div class="card-body py-4">
                @if ($mataPelajaran->jadwalPelajarans->count() > 0)
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_jadwal_mapel">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="w-30px">No</th>
                                <th class="min-w-100px">Kelas</th>
                                <th class="min-w-100px">Hari</th>
                                <th class="min-w-150px">Jam</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            @foreach ($mataPelajaran->jadwalPelajarans as $i => $jadwal)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="d-flex align-items-center">
                                    <div class="symbol symbol-circle symbol-35px overflow-hidden me-3">
                                        <div class="symbol-label fs-6 bg-light-primary text-primary fw-bolder">{{ substr($jadwal->kelas->nama ?? '?', 0, 1) }}</div>
                                    </div>
                                    <span>{{ $jadwal->kelas->nama ?? '-' }}</span>
                                </td>
                                <td>{{ $jadwal->hari ?? '-' }}</td>
                                <td>{{ $jadwal->jam_mulai ? $jadwal->jam_mulai . ' - ' . $jadwal->jam_selesai : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-10">
                        <span class="svg-icon svg-icon-3x svg-icon-muted mb-3 d-block">
                            {!! theme()->getSvgIcon("icons/duotune/general/gen025.svg") !!}
                        </span>
                        <span class="text-muted fw-bold">Belum ada jadwal untuk mata pelajaran ini.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!--end::Tab Pane - Jadwal-->
</div>
<!--end::Tab Content-->

@section('scripts')
<script>
$(document).ready(function() {
    $('#kt_table_jadwal_mapel').DataTable({ 
        dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', 
        info:true, 
        order:[], 
        pageLength:5, 
        lengthChange:true, 
        columnDefs:[{orderable:false,targets:0}] 
    });
});
</script>
@endsection
</x-base-layout>
