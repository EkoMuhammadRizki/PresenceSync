<x-base-layout>
@php 
    $showPanduan = $showPanduan ?? false; 
    $syncHostingUrl = env('HOSTING_SYNC_URL');
    $syncHostingHost = $syncHostingUrl ? parse_url($syncHostingUrl, PHP_URL_HOST) : null;
    $currentHost = request()->getHost();
    $showSyncBtn = auth()->user() && auth()->user()->hasRole('admin') 
                    && !empty($syncHostingUrl) 
                    && !env('IS_HOSTING', false) 
                    && ($syncHostingHost !== $currentHost);
@endphp
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => $showSyncBtn ? '
        <button type="button" id="btn-sync-database" class="btn btn-sm btn-warning fw-bold text-white shadow-sm" onclick="confirmSyncDatabase()">
            <i class="bi bi-cloud-upload me-1 text-white"></i>
            <span id="sync-btn-text">Sinkronkan DB ke Hosting</span>
        </button>' : ''
])

<!-- Extra Styles for Dashboard Visualizations -->
@push('styles')
<link href="{{ asset(theme()->getDemo() . '/plugins/custom/flatpickr/flatpickr.bundle.css') }}" rel="stylesheet" type="text/css"/>
<style>
    .scrollable-table-container {
        max-height: 420px;
        overflow-y: auto;
        overflow-x: hidden;
        width: 100%;
        display: block;
    }
    .late-table {
        table-layout: fixed;
        width: 100%;
        margin-bottom: 0;
    }
    .late-table th,
    .late-table td {
        padding-left: 0.35rem;
        padding-right: 0.35rem;
        vertical-align: middle;
    }
    .late-table .col-nama {
        width: 32%;
        max-width: 0;
    }
    .late-table .col-kelas {
        width: 14%;
    }
    .late-table .col-status {
        width: 18%;
    }
    .late-table .col-masuk {
        width: 16%;
    }
    .late-table .col-durasi {
        width: 20%;
    }
    .late-table th:last-child,
    .late-table td:last-child {
        padding-right: 0;
    }
    .late-table .col-durasi span {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .late-card-body {
        overflow: hidden;
    }
    .late-table .nama-cell {
        min-width: 0;
    }
    .late-table .nama-cell .symbol {
        flex-shrink: 0;
    }
    .late-table .nama-cell .nama-text {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        min-width: 0;
    }
    /* Accent Top Border styling for parameter cards */
    .trend-summary-card {
        transition: all 0.25s ease-in-out;
        cursor: pointer;
        user-select: none;
        border-radius: 0.5rem !important;
        position: relative;
    }
    .trend-summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
    }

    .trend-summary-card.active {
        opacity: 1 !important;
        background-color: #ffffff !important;
        box-shadow: 0 6px 18px rgba(0,0,0,0.1) !important;
        transform: translateY(-2px);
    }

    .trend-summary-card[data-criteria="kehadiran"].active,
    .trend-summary-card-guru[data-criteria="kehadiran"].active {
        border: 2px solid #009ef7 !important;
    }
    .trend-summary-card[data-criteria="ketidakhadiran"].active,
    .trend-summary-card-guru[data-criteria="ketidakhadiran"].active {
        border: 2px solid #f1416c !important;
    }
    .trend-summary-card[data-criteria="izin"].active,
    .trend-summary-card-guru[data-criteria="izin"].active {
        border: 2px solid #7239ea !important;
    }
    .trend-summary-card[data-criteria="sakit"].active,
    .trend-summary-card-guru[data-criteria="sakit"].active {
        border: 2px solid #f59e0b !important;
    }
    .trend-summary-card[data-criteria="alpa"].active,
    .trend-summary-card-guru[data-criteria="alpa"].active {
        border: 2px solid #3f4254 !important;
    }

    .trend-summary-card:not(.active),
    .trend-summary-card-guru:not(.active) {
        opacity: 0.45 !important;
        background-color: #f8f9fa !important;
        box-shadow: none !important;
        border: 1px solid #eef2f5 !important;
        filter: grayscale(30%);
        transform: none !important;
    }

    .trend-summary-card-guru {
        transition: all 0.25s ease-in-out;
        cursor: pointer;
        user-select: none;
        border-radius: 0.5rem !important;
        position: relative;
    }
    .trend-summary-card-guru:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
    }

    .trend-summary-card-guru.active {
        opacity: 1 !important;
        background-color: #ffffff !important;
        box-shadow: 0 6px 18px rgba(0,0,0,0.1) !important;
        transform: translateY(-2px);
    }

    .trend-card-title {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        display: block !important;
        font-size: 0.75rem !important;
    }
</style>
@endpush

        <!-- ==================== ROW 1: TOP STATISTICS CARDS ==================== -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-5 mb-8">
            
            <!-- Card 1: Jumlah Siswa -->
            <div class="col">
                <div class="card flex-center min-w-100px p-6 bg-light-primary border border-primary">
                    <span class="svg-icon svg-icon-3x svg-icon-primary mb-2">
                        {!! theme()->getSvgIcon("icons/duotune/communication/com013.svg", "svg-icon-3x svg-icon-primary") !!}
                    </span>
                    <span class="fs-6 fw-bold text-gray-700 pb-1">Total Siswa</span>
                    <span class="fs-2hx fw-bolder text-primary">{{ $totalSiswa }}</span>
                    <span class="fs-8 fw-bold text-gray-500">Siswa Aktif</span>
                </div>
            </div>

            <!-- Card 2: Jumlah Guru -->
            <div class="col">
                <div class="card flex-center min-w-100px p-6 bg-light-success border border-success">
                    <span class="svg-icon svg-icon-3x svg-icon-success mb-2">
                        {!! theme()->getSvgIcon("icons/duotune/communication/com014.svg", "svg-icon-3x svg-icon-success") !!}
                    </span>
                    <span class="fs-6 fw-bold text-gray-700 pb-1">Total Guru</span>
                    <span class="fs-2hx fw-bolder text-success">{{ $totalGuru }}</span>
                    <span class="fs-8 fw-bold text-gray-500">Guru Terdaftar</span>
                </div>
            </div>

            <!-- Card 3: Jumlah Kelas -->
            <div class="col">
                <div class="card flex-center min-w-100px p-6 bg-light-info border border-info">
                    <span class="svg-icon svg-icon-3x svg-icon-info mb-2">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen025.svg", "svg-icon-3x svg-icon-info") !!}
                    </span>
                    <span class="fs-6 fw-bold text-gray-700 pb-1">Total Kelas</span>
                    <span class="fs-2hx fw-bolder text-info">{{ $totalKelas }}</span>
                    <span class="fs-8 fw-bold text-gray-500">Kelas Aktif</span>
                </div>
            </div>

            <!-- Card 4: Kehadiran Hari Ini -->
            <div class="col">
                <div class="card flex-center min-w-100px p-6 bg-light-warning border border-warning">
                    <span class="svg-icon svg-icon-3x svg-icon-warning mb-2">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-3x svg-icon-warning") !!}
                    </span>
                    <span class="fs-6 fw-bold text-gray-700 pb-1">Hadir Hari Ini</span>
                    <span class="fs-2hx fw-bolder text-warning">{{ $totalHadir }}</span>
                    <span class="fs-8 fw-bold text-gray-500">Hadir + Terlambat</span>
                </div>
            </div>

            <!-- Card 5: Terlambat Hari Ini -->
            <div class="col">
                <div class="card flex-center min-w-100px p-6 bg-light-danger border border-danger">
                    <span class="svg-icon svg-icon-3x svg-icon-danger mb-2">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen050.svg", "svg-icon-3x svg-icon-danger") !!}
                    </span>
                    <span class="fs-6 fw-bold text-gray-700 pb-1">Terlambat Hari Ini</span>
                    <span class="fs-2hx fw-bolder text-danger">{{ $totalTerlambat }}</span>
                    <span class="fs-8 fw-bold text-gray-500">Butuh Evaluasi</span>
                </div>
            </div>

        </div>

        <!-- ==================== ROW 2: TRENDS & LATE COMERS ==================== -->
        <div class="row g-5 g-xl-8">
            
            <!-- COLUMN 1: TREND KEHADIRAN (GRID 7) -->
            <div class="col-xl-7">
                <div class="card card-xxl-stretch mb-5 mb-xl-8">
                    <!-- Header with Tabs -->
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 text-dark">Tren Kehadiran</span>
                            <span class="text-muted mt-1 fw-bold fs-7">Analisis data tren kehadiran berkala</span>
                        </h3>
                        <div class="card-toolbar">
                            <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0">
                                <li class="nav-item">
                                    <a class="nav-link active text-active-primary fw-bolder" data-bs-toggle="tab" href="#tab_siswa_trend">Kehadiran Siswa</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-active-primary fw-bolder" data-bs-toggle="tab" href="#tab_guru_trend">Kehadiran Guru</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="tab-content">
                            
                            <!-- TAB 1: KEHADIRAN SISWA -->
                            <div class="tab-pane fade show active" id="tab_siswa_trend" role="tabpanel">
                                
                                <!-- Filters: Date Range & Class Dropdown -->
                                <div class="d-flex flex-wrap align-items-center justify-content-between mb-5 gap-3">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <label class="fs-7 fw-bold text-gray-700">Periode:</label>
                                        <div class="d-flex align-items-center position-relative my-1">
                                            {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-2 position-absolute ms-3") !!}
                                            <input type="text" id="filter_tanggal" class="form-control form-control-solid form-control-sm w-225px w-md-275px ps-10" placeholder="Pilih Rentang Tanggal" readonly="readonly" />
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <label class="fs-7 fw-bold text-gray-700">Kelas:</label>
                                        <select class="form-select form-select-solid form-select-sm w-150px" data-control="select2" data-placeholder="Semua Kelas" data-allow-clear="true" id="filter_kelas">
                                            <option></option>
                                            @foreach($kelas as $k)
                                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- 5 Summary Parameter Cards -->
                                <div class="row row-cols-2 row-cols-sm-5 g-2 g-xl-3 mb-6">
                                    <!-- Kehadiran -->
                                    <div class="col">
                                        <div class="card trend-summary-card p-3 active" data-criteria="kehadiran" onclick="selectTrendCriteria('kehadiran')" style="border-top: 4px solid #009ef7 !important; border-left: 1px solid #eef2f5 !important; border-right: 1px solid #eef2f5 !important; border-bottom: 1px solid #eef2f5 !important; cursor: pointer; user-select: none;">
                                            <span class="fw-bold text-gray-600 trend-card-title" title="Kehadiran" style="pointer-events: none;">Kehadiran</span>
                                            <span class="fs-3 fw-boldest text-dark" id="summary_kehadiran" style="pointer-events: none;">0</span>
                                        </div>
                                    </div>
                                    <!-- Ketidakhadiran -->
                                    <div class="col">
                                        <div class="card trend-summary-card p-3" data-criteria="ketidakhadiran" onclick="selectTrendCriteria('ketidakhadiran')" style="border-top: 4px solid #f1416c !important; border-left: 1px solid #eef2f5 !important; border-right: 1px solid #eef2f5 !important; border-bottom: 1px solid #eef2f5 !important; opacity: 0.55; cursor: pointer; user-select: none;">
                                            <span class="fw-bold text-gray-600 trend-card-title" title="Ketidakhadiran" style="pointer-events: none;">Ketidakhadiran</span>
                                            <span class="fs-3 fw-boldest text-dark" id="summary_ketidakhadiran" style="pointer-events: none;">0</span>
                                        </div>
                                    </div>
                                    <!-- Izin -->
                                    <div class="col">
                                        <div class="card trend-summary-card p-3" data-criteria="izin" onclick="selectTrendCriteria('izin')" style="border-top: 4px solid #7239ea !important; border-left: 1px solid #eef2f5 !important; border-right: 1px solid #eef2f5 !important; border-bottom: 1px solid #eef2f5 !important; opacity: 0.55; cursor: pointer; user-select: none;">
                                            <span class="fw-bold text-gray-600 trend-card-title" title="Izin" style="pointer-events: none;">Izin</span>
                                            <span class="fs-3 fw-boldest text-dark" id="summary_izin" style="pointer-events: none;">0</span>
                                        </div>
                                    </div>
                                    <!-- Sakit -->
                                    <div class="col">
                                        <div class="card trend-summary-card p-3" data-criteria="sakit" onclick="selectTrendCriteria('sakit')" style="border-top: 4px solid #f59e0b !important; border-left: 1px solid #eef2f5 !important; border-right: 1px solid #eef2f5 !important; border-bottom: 1px solid #eef2f5 !important; opacity: 0.55; cursor: pointer; user-select: none;">
                                            <span class="fw-bold text-gray-600 trend-card-title" title="Sakit" style="pointer-events: none;">Sakit</span>
                                            <span class="fs-3 fw-boldest text-dark" id="summary_sakit" style="pointer-events: none;">0</span>
                                        </div>
                                    </div>
                                    <!-- Alpa -->
                                    <div class="col">
                                        <div class="card trend-summary-card p-3" data-criteria="alpa" onclick="selectTrendCriteria('alpa')" style="border-top: 4px solid #3f4254 !important; border-left: 1px solid #eef2f5 !important; border-right: 1px solid #eef2f5 !important; border-bottom: 1px solid #eef2f5 !important; opacity: 0.55; cursor: pointer; user-select: none;">
                                            <span class="fw-bold text-gray-600 trend-card-title" title="Alpa" style="pointer-events: none;">Alpa</span>
                                            <span class="fs-3 fw-boldest text-dark" id="summary_alpa" style="pointer-events: none;">0</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Header Grafik & Dynamic Legend -->
                                <div class="d-flex flex-wrap align-items-center justify-content-between my-4 pt-2">
                                    <h4 class="fw-bolder text-dark mb-0 fs-5">Grafik setiap Kriteria</h4>
                                    <div class="d-flex align-items-center gap-4 flex-wrap">
                                        <div id="chart_legend_container" class="d-flex align-items-center gap-3 flex-wrap"></div>
                                        <span class="text-muted fw-bold fs-7" id="criteria_counter">Kriteria Dipilih 5 / 5</span>
                                    </div>
                                </div>

                                <!-- Chart Trend Container -->
                                <div id="chart_trend_kehadiran_siswa"></div>

                            </div>

                            <!-- TAB 2: KEHADIRAN GURU -->
                            <div class="tab-pane fade" id="tab_guru_trend" role="tabpanel">
                                
                                <!-- Filters: Date Range -->
                                <div class="d-flex flex-wrap align-items-center justify-content-between mb-5 gap-3">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <label class="fs-7 fw-bold text-gray-700">Periode:</label>
                                        <div class="d-flex align-items-center position-relative my-1">
                                            {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-2 position-absolute ms-3") !!}
                                            <input type="text" id="filter_tanggal_guru" class="form-control form-control-solid form-control-sm w-225px w-md-275px ps-10" placeholder="Pilih Rentang Tanggal" readonly="readonly" />
                                        </div>
                                    </div>
                                </div>

                                <!-- 5 Summary Parameter Cards for Guru -->
                                <div class="row row-cols-2 row-cols-sm-5 g-2 g-xl-3 mb-6">
                                    <!-- Kehadiran -->
                                    <div class="col">
                                        <div class="card trend-summary-card-guru p-3 active" data-criteria="kehadiran" onclick="selectGuruTrendCriteria('kehadiran')" style="border-top: 4px solid #009ef7 !important; border-left: 1px solid #eef2f5 !important; border-right: 1px solid #eef2f5 !important; border-bottom: 1px solid #eef2f5 !important; cursor: pointer; user-select: none;">
                                            <span class="fw-bold text-gray-600 trend-card-title" title="Kehadiran" style="pointer-events: none;">Kehadiran</span>
                                            <span class="fs-3 fw-boldest text-dark" id="summary_guru_kehadiran" style="pointer-events: none;">0</span>
                                        </div>
                                    </div>
                                    <!-- Ketidakhadiran -->
                                    <div class="col">
                                        <div class="card trend-summary-card-guru p-3" data-criteria="ketidakhadiran" onclick="selectGuruTrendCriteria('ketidakhadiran')" style="border-top: 4px solid #f1416c !important; border-left: 1px solid #eef2f5 !important; border-right: 1px solid #eef2f5 !important; border-bottom: 1px solid #eef2f5 !important; opacity: 0.55; cursor: pointer; user-select: none;">
                                            <span class="fw-bold text-gray-600 trend-card-title" title="Ketidakhadiran" style="pointer-events: none;">Ketidakhadiran</span>
                                            <span class="fs-3 fw-boldest text-dark" id="summary_guru_ketidakhadiran" style="pointer-events: none;">0</span>
                                        </div>
                                    </div>
                                    <!-- Izin -->
                                    <div class="col">
                                        <div class="card trend-summary-card-guru p-3" data-criteria="izin" onclick="selectGuruTrendCriteria('izin')" style="border-top: 4px solid #7239ea !important; border-left: 1px solid #eef2f5 !important; border-right: 1px solid #eef2f5 !important; border-bottom: 1px solid #eef2f5 !important; opacity: 0.55; cursor: pointer; user-select: none;">
                                            <span class="fw-bold text-gray-600 trend-card-title" title="Izin" style="pointer-events: none;">Izin</span>
                                            <span class="fs-3 fw-boldest text-dark" id="summary_guru_izin" style="pointer-events: none;">0</span>
                                        </div>
                                    </div>
                                    <!-- Sakit -->
                                    <div class="col">
                                        <div class="card trend-summary-card-guru p-3" data-criteria="sakit" onclick="selectGuruTrendCriteria('sakit')" style="border-top: 4px solid #f59e0b !important; border-left: 1px solid #eef2f5 !important; border-right: 1px solid #eef2f5 !important; border-bottom: 1px solid #eef2f5 !important; opacity: 0.55; cursor: pointer; user-select: none;">
                                            <span class="fw-bold text-gray-600 trend-card-title" title="Sakit" style="pointer-events: none;">Sakit</span>
                                            <span class="fs-3 fw-boldest text-dark" id="summary_guru_sakit" style="pointer-events: none;">0</span>
                                        </div>
                                    </div>
                                    <!-- Alpa -->
                                    <div class="col">
                                        <div class="card trend-summary-card-guru p-3" data-criteria="alpa" onclick="selectGuruTrendCriteria('alpa')" style="border-top: 4px solid #3f4254 !important; border-left: 1px solid #eef2f5 !important; border-right: 1px solid #eef2f5 !important; border-bottom: 1px solid #eef2f5 !important; opacity: 0.55; cursor: pointer; user-select: none;">
                                            <span class="fw-bold text-gray-600 trend-card-title" title="Alpa" style="pointer-events: none;">Alpa</span>
                                            <span class="fs-3 fw-boldest text-dark" id="summary_guru_alpa" style="pointer-events: none;">0</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Header Grafik & Dynamic Legend Guru -->
                                <div class="d-flex flex-wrap align-items-center justify-content-between my-4 pt-2">
                                    <h4 class="fw-bolder text-dark mb-0 fs-5">Grafik Kehadiran Guru</h4>
                                    <div class="d-flex align-items-center gap-4 flex-wrap">
                                        <div id="chart_guru_legend_container" class="d-flex align-items-center gap-3 flex-wrap"></div>
                                        <span class="text-muted fw-bold fs-7" id="guru_criteria_counter">Kriteria Dipilih 5 / 5</span>
                                    </div>
                                </div>

                                <!-- Chart Trend Container Guru -->
                                <div id="chart_trend_kehadiran_guru"></div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMN 2: KETERLAMBATAN HARI INI (GRID 5) -->
            <div class="col-xl-5">
                <div class="card card-xxl-stretch mb-5 mb-xl-8">
                    <!-- Card Header -->
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 text-dark">Keterlambatan Hari Ini</span>
                            <span class="text-muted mt-1 fw-bold fs-7">Urutan check-in terlambat paling baru</span>
                        </h3>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body pt-3 px-6 late-card-body">
                        <div class="scrollable-table-container">
                            @php $defaultAvatar = asset(theme()->getMediaUrlPath() . 'svg/avatars/blank.svg'); @endphp
                            <div class="table-responsive">
                                <table class="table late-table align-middle table-row-dashed fs-8 gy-2">
                                    <thead>
                                        <tr class="text-start text-muted fw-bolder fs-9 text-uppercase gs-0">
                                            <th class="col-nama">Nama</th>
                                            <th class="col-kelas text-nowrap">Kelas</th>
                                            <th class="col-status text-nowrap">Status</th>
                                            <th class="col-masuk text-nowrap">Masuk</th>
                                            <th class="col-durasi text-center text-nowrap">Terlambat</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-bold">
                                        @forelse($terlambats as $t)
                                        <tr>
                                            <td class="col-nama">
                                                <div class="d-flex align-items-center nama-cell">
                                                    <div class="symbol symbol-25px symbol-circle me-2">
                                                        <img
                                                            src="{{ $t['foto'] ?: $defaultAvatar }}"
                                                            alt="{{ $t['nama'] }}"
                                                            class="object-fit-cover"
                                                            onerror="this.onerror=null;this.src='{{ $defaultAvatar }}';"
                                                        />
                                                    </div>
                                                    <span class="text-gray-800 text-hover-primary fw-bolder nama-text" title="{{ $t['nama'] }}">{{ $t['nama'] }}</span>
                                                </div>
                                            </td>
                                            <td class="col-kelas text-nowrap">{{ $t['kelas'] }}</td>
                                            <td class="col-status">
                                                <span class="badge badge-light-danger fs-9 px-2 py-1">Terlambat</span>
                                            </td>
                                            <td class="col-masuk text-nowrap">
                                                <span class="text-gray-600 fw-bolder fs-9">{{ \Illuminate\Support\Str::substr($t['waktu'], 0, 5) }}</span>
                                            </td>
                                            <td class="col-durasi text-center text-danger fw-bolder fs-9">
                                                <span title="{{ $t['durasi_terlambat'] }}">{{ $t['durasi_terlambat_singkat'] }}</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-8 text-muted">
                                                Tidak ada keterlambatan tercatat hari ini.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ==================== ROW 3: KEHADIRAN PER KELAS & AKTIVITAS TERBARU ==================== -->
        <div class="row g-5 g-xl-8 mt-1">
            
            <!-- COLUMN 1: PERINGKAT & KEHADIRAN PER KELAS (GRID 7) -->
            <div class="col-xl-7">
                <div class="card card-xxl-stretch mb-5 mb-xl-8">
                    <!-- Header -->
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 text-dark">Kehadiran per Kelas</span>
                            <span class="text-muted mt-1 fw-bold fs-7">Persentase & rekap tingkat kehadiran siswa per kelas hari ini</span>
                        </h3>
                        <div class="card-toolbar">
                            <span class="badge badge-light-primary fw-bolder fs-8 px-3 py-2">Real-time</span>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="card-body pt-3">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-7 gy-3">
                                <thead>
                                    <tr class="text-start text-muted fw-bolder fs-8 text-uppercase gs-0">
                                        <th class="min-w-125px">Nama Kelas</th>
                                        <th class="min-w-100px text-center">Kehadiran</th>
                                        <th class="min-w-150px">Progress</th>
                                        <th class="min-w-100px text-end">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-bold">
                                    @forelse($kehadiranPerKelas as $kp)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-35px symbol-circle bg-light-primary text-primary me-3 flex-center fw-bolder fs-6">
                                                    {{ substr($kp['nama'], 0, 2) }}
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <a href="{{ route('laporan.siswa', ['kelas_id' => $kp['id']]) }}" class="text-gray-800 text-hover-primary fw-bolder fs-6">{{ $kp['nama'] }}</a>
                                                    <span class="text-muted fs-8">{{ $kp['total_siswa'] }} Siswa Terdaftar</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-dark fw-bolder fs-6">{{ $kp['hadir'] }}</span>
                                            <span class="text-muted fs-8">/ {{ $kp['total_siswa'] }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center w-100 me-2">
                                                <div class="progress h-6px w-100 me-3 bg-light">
                                                    @php
                                                        $barClass = $kp['persentase'] >= 90 ? 'bg-success' : ($kp['persentase'] >= 75 ? 'bg-warning' : 'bg-danger');
                                                    @endphp
                                                    <div class="progress-bar {{ $barClass }}" role="progressbar" style="width: {{ $kp['persentase'] }}%" aria-valuenow="{{ $kp['persentase'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="text-gray-800 fw-bolder fs-7">{{ $kp['persentase'] }}%</span>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            @if($kp['persentase'] >= 90)
                                                <span class="badge badge-light-success fw-bolder px-3 py-1 fs-8">Sangat Baik</span>
                                            @elseif($kp['persentase'] >= 75)
                                                <span class="badge badge-light-warning fw-bolder px-3 py-1 fs-8">Cukup</span>
                                            @else
                                                <span class="badge badge-light-danger fw-bolder px-3 py-1 fs-8">Perlu Evaluasi</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-6 text-muted">
                                            Belum ada data kelas aktif.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMN 2: AKTIVITAS ABSENSI TERBARU (GRID 5) -->
            <div class="col-xl-5">
                <div class="card card-xxl-stretch mb-5 mb-xl-8">
                    <!-- Header -->
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 text-dark">Aktivitas Terbaru</span>
                            <span class="text-muted mt-1 fw-bold fs-7">Log pemindaian & absensi terkini</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('kehadiran.index') }}" class="btn btn-sm btn-light-primary fw-bolder fs-8">Lihat Semua</a>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="card-body pt-3">
                        <div class="timeline-label">
                            @forelse($aktivitasTerbaru as $act)
                            <div class="timeline-item">
                                <!-- Label Time -->
                                <div class="timeline-label fw-bolder text-gray-800 fs-7 min-w-50px">{{ $act['waktu'] }}</div>

                                <!-- Badge Icon -->
                                <div class="timeline-badge">
                                    @if($act['status'] == 'hadir')
                                        <i class="fa fa-genderless text-success fs-1"></i>
                                    @elseif($act['status'] == 'terlambat')
                                        <i class="fa fa-genderless text-warning fs-1"></i>
                                    @elseif($act['status'] == 'sakit' || $act['status'] == 'izin')
                                        <i class="fa fa-genderless text-info fs-1"></i>
                                    @else
                                        <i class="fa fa-genderless text-danger fs-1"></i>
                                    @endif
                                </div>

                                <!-- Text Content -->
                                <div class="timeline-content d-flex align-items-center justify-content-between ps-3 w-100">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bolder text-gray-800 fs-7">{{ $act['nama'] }}</span>
                                        <span class="text-muted fs-8">{{ $act['role'] }} &bull; {{ $act['created_at_human'] }}</span>
                                    </div>
                                    <div>
                                        @if($act['status'] == 'hadir')
                                            <span class="badge badge-light-success fs-9 px-2 py-1">Hadir</span>
                                        @elseif($act['status'] == 'terlambat')
                                            <span class="badge badge-light-warning fs-9 px-2 py-1">Terlambat</span>
                                        @elseif($act['status'] == 'sakit')
                                            <span class="badge badge-light-info fs-9 px-2 py-1">Sakit</span>
                                        @elseif($act['status'] == 'izin')
                                            <span class="badge badge-light-primary fs-9 px-2 py-1">Izin</span>
                                        @else
                                            <span class="badge badge-light-danger fs-9 px-2 py-1">Alpha</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8 text-muted fs-7">
                                Belum ada aktivitas absensi tercatat hari ini.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>

@if($showPanduan)
    <!-- Modal Panduan Singkat -->
    <div class="modal fade" id="modal_panduan_singkat" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-550px">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0 justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body scroll-y mx-5 mx-xl-10 pt-0 pb-8">
                    <div class="text-center mb-6">
                        <span class="svg-icon svg-icon-2hx svg-icon-primary mb-2 d-block text-center justify-content-center">
                            {!! theme()->getSvgIcon("demo1/media/icons/duotune/general/book-icon.svg", "svg-icon-2hx svg-icon-primary") !!}
                        </span>
                        <h2 class="mb-1 text-gray-900 fs-2">Panduan Singkat Penggunaan</h2>
                        <div class="text-muted fw-bold fs-6">Langkah cepat mengonfigurasi sistem Presence Sync</div>
                    </div>
                    
                    <div class="mb-8">
                        <!-- Flow steps -->
                        <div class="d-flex align-items-center mb-4">
                            <span class="badge badge-circle badge-light-primary fw-bolder fs-6 me-3 w-30px h-30px d-flex align-items-center justify-content-center">1</span>
                            <div class="flex-grow-1">
                                <h5 class="text-gray-800 fw-bolder mb-0 fs-6">Setup Tahun Ajaran & Aturan Jam</h5>
                                <span class="text-muted fw-bold fs-7">Aktifkan Tahun Ajaran saat ini dan atur jam masuk/pulang sekolah.</span>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center mb-4">
                            <span class="badge badge-circle badge-light-primary fw-bolder fs-6 me-3 w-30px h-30px d-flex align-items-center justify-content-center">2</span>
                            <div class="flex-grow-1">
                                <h5 class="text-gray-800 fw-bolder mb-0 fs-6">Input Data Guru & Kelas</h5>
                                <span class="text-muted fw-bold fs-7">Daftarkan Guru dan buat data Kelas dengan Wali Kelas terpilih.</span>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center mb-4">
                            <span class="badge badge-circle badge-light-primary fw-bolder fs-6 me-3 w-30px h-30px d-flex align-items-center justify-content-center">3</span>
                            <div class="flex-grow-1">
                                <h5 class="text-gray-800 fw-bolder mb-0 fs-6">Input Siswa & Pembagian Kelas</h5>
                                <span class="text-muted fw-bold fs-7">Import data Siswa lalu masukkan mereka ke dalam kelas masing-masing.</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-4">
                            <span class="badge badge-circle badge-light-primary fw-bolder fs-6 me-3 w-30px h-30px d-flex align-items-center justify-content-center">4</span>
                            <div class="flex-grow-1">
                                <h5 class="text-gray-800 fw-bolder mb-0 fs-6">Mata Pelajaran & Aturan Jam</h5>
                                <span class="text-muted fw-bold fs-7">Daftarkan Mata Pelajaran beserta Guru Pengampu.</span>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center mb-4">
                            <span class="badge badge-circle badge-light-primary fw-bolder fs-6 me-3 w-30px h-30px d-flex align-items-center justify-content-center">5</span>
                            <div class="flex-grow-1">
                                <h5 class="text-gray-800 fw-bolder mb-0 fs-6">Sinkronisasi Fingerprint & Laporan</h5>
                                <span class="text-muted fw-bold fs-7">Pantau presensi otomatis dari sidik jari dan cetak rekapitulasi laporan.</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center pt-2">
                        <button type="button" class="btn btn-light btn-sm me-3" data-bs-dismiss="modal">Tutup</button>
                        <a href="{{ route('panduan.index') }}" class="btn btn-primary btn-sm">Buka Panduan Lengkap</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php session()->put('panduan_singkat_shown', true); @endphp
    @push('scripts')
    <script>
        $(document).ready(function() {
            var modal = new bootstrap.Modal(document.getElementById('modal_panduan_singkat'));
            modal.show();
        });
    </script>
    @endpush
@endif

@push('scripts')
<script src="{{ asset(theme()->getDemo() . '/plugins/custom/flatpickr/flatpickr.bundle.js') }}"></script>
<script>
    window.trendChartInstance = window.trendChartInstance || null;
    window.rawTrendChartData = window.rawTrendChartData || [];

    window.criteriaConfig = {
        kehadiran: { key: 'kehadiran', label: 'Kehadiran', color: '#009ef7', active: true },
        ketidakhadiran: { key: 'ketidakhadiran', label: 'Ketidakhadiran', color: '#f1416c', active: false },
        izin: { key: 'izin', label: 'Izin', color: '#7239ea', active: false },
        sakit: { key: 'sakit', label: 'Sakit', color: '#f59e0b', active: false },
        alpa: { key: 'alpa', label: 'Alpa', color: '#3f4254', active: false }
    };

    window.selectTrendCriteria = function(selectedKey) {
        if (!selectedKey || !window.criteriaConfig[selectedKey]) return;

        var cfg = window.criteriaConfig[selectedKey];
        var activeCount = Object.keys(window.criteriaConfig).filter(function(k) {
            return window.criteriaConfig[k].active;
        }).length;

        // Prevent deselecting the last active one
        if (cfg.active && activeCount <= 1) return;

        cfg.active = !cfg.active;

        var $card = $('.trend-summary-card[data-criteria="' + selectedKey + '"]');
        if (cfg.active) {
            $card.addClass('active').css('opacity', '1');
        } else {
            $card.removeClass('active').css('opacity', '0.45');
        }

        window.renderTrendChart();
    };

    window.initChartAndFetchData = function() {
        var chartEl = document.querySelector("#chart_trend_kehadiran_siswa");
        if (!chartEl) return;

        // Flatpickr setup - Default to TODAY for hourly time axis (06:00 - 07:30)
        if ($('#filter_tanggal').length && typeof flatpickr !== 'undefined') {
            var today = new Date();
            var todayStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');

            flatpickr("#filter_tanggal", {
                mode: "range",
                dateFormat: "Y-m-d",
                defaultDate: [todayStr, todayStr],
                onChange: function(selectedDates) {
                    if (selectedDates.length === 2 || selectedDates.length === 1) {
                        window.fetchTrendData();
                    }
                },
                onClose: function() {
                    window.fetchTrendData();
                }
            });
        }

        $(document).off('change select2:select select2:clear', '#filter_kelas').on('change select2:select select2:clear', '#filter_kelas', function() {
            window.fetchTrendData();
        });

        if (window.trendChartInstance) {
            try { window.trendChartInstance.destroy(); } catch(err) {}
            window.trendChartInstance = null;
        }

        window.fetchTrendData();
    };

    window.fetchTrendData = function() {
        var dateRangeVal = $('#filter_tanggal').val() || '';
        var dates = dateRangeVal.split(/ to | hingga | - /);
        var start_date = dates[0] ? dates[0].trim() : '';
        var end_date = dates[1] ? dates[1].trim() : start_date;
        var kelas_id = $('#filter_kelas').val() || '';

        $('.trend-summary-card span.fs-3').text('...');

        $.ajax({
            url: '{{ route("admin.dashboard.trend-data") }}',
            method: 'GET',
            data: {
                start_date: start_date,
                end_date: end_date,
                kelas_id: kelas_id
            },
            success: function(res) {
                if (res && res.success) {
                    $('#summary_kehadiran').text(res.totals.kehadiran);
                    $('#summary_ketidakhadiran').text(res.totals.ketidakhadiran);
                    $('#summary_izin').text(res.totals.izin);
                    $('#summary_sakit').text(res.totals.sakit);
                    $('#summary_alpa').text(res.totals.alpa);

                    window.rawTrendChartData = res.chart || [];
                    window.renderTrendChart();
                }
            },
            error: function() {
                $('.trend-summary-card span.fs-3').text('0');
            }
        });
    };

    window.renderTrendChart = function() {
        var chartEl = document.querySelector("#chart_trend_kehadiran_siswa");
        if (!chartEl || !window.rawTrendChartData || window.rawTrendChartData.length === 0) return;

        // Jangan render ke container tersembunyi (0px SVG issue)
        if (chartEl.offsetWidth === 0 || $(chartEl).is(':hidden')) {
            return;
        }

        var categories = [];
        var activeSeries = [];
        var activeColors = [];
        var activeCount = 0;
        var totalCriteria = Object.keys(window.criteriaConfig).length;

        window.rawTrendChartData.forEach(function(item) {
            categories.push(item.tanggal);
        });

        var legendHtml = '';

        Object.keys(window.criteriaConfig).forEach(function(key) {
            var cfg = window.criteriaConfig[key];
            if (cfg.active) {
                activeCount++;
                activeColors.push(cfg.color);

                var seriesData = window.rawTrendChartData.map(function(item) {
                    return item[key] || 0;
                });

                activeSeries.push({
                    name: cfg.label,
                    data: seriesData
                });

                legendHtml += '<span class="d-flex align-items-center fs-7 fw-bold text-gray-700 me-2">' +
                    '<span class="badge badge-circle me-2" style="background-color: ' + cfg.color + '; width: 9px; height: 9px;"></span>' +
                    cfg.label +
                    '</span>';
            }
        });

        $('#chart_legend_container').html(legendHtml);
        $('#criteria_counter').text('Kriteria Dipilih ' + activeCount + ' / ' + totalCriteria);

        var options = {
            series: activeSeries,
            chart: {
                height: 350,
                type: 'line',
                zoom: { enabled: false },
                toolbar: { show: false }
            },
            colors: activeColors,
            dataLabels: { enabled: false },
            stroke: {
                width: 3,
                curve: 'smooth'
            },
            markers: {
                size: 5,
                strokeWidth: 2,
                hover: { sizeOffset: 2 }
            },
            legend: { show: false },
            grid: {
                borderColor: '#eef2f5',
                strokeDashArray: 4,
                row: { colors: ['transparent'], opacity: 0.5 }
            },
            xaxis: {
                categories: categories,
                axisBorder: { show: true, color: '#e0e0e0' },
                axisTicks: { show: true }
            },
            yaxis: {
                title: {
                    text: 'Jumlah Siswa',
                    style: { color: '#6c757d', fontSize: '12px', fontWeight: '600' }
                },
                min: 0,
                forceNiceScale: true,
                labels: {
                    formatter: function(val) { return Math.round(val); }
                }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(val) { return val + " siswa"; }
                }
            }
        };

        if (typeof ApexCharts === 'undefined') return;

        try {
            if (window.trendChartInstance) {
                try { window.trendChartInstance.destroy(); } catch(e) {}
                window.trendChartInstance = null;
            }
            chartEl.innerHTML = '';
            window.trendChartInstance = new ApexCharts(chartEl, options);
            window.trendChartInstance.render();
        } catch(e) {
            console.error('ApexCharts Siswa render error:', e);
        }
    };

    // Auto run initialization
    setTimeout(function() {
        if (typeof window.initChartAndFetchData === 'function') {
            window.initChartAndFetchData();
        }
        if (typeof window.fetchGuruTrendData === 'function') {
            window.fetchGuruTrendData();
        }
    }, 100);

    // -------------------------------------------------------------
    // GURU ATTENDANCE TREND LOGIC
    // -------------------------------------------------------------
    window.guruCriteriaConfig = {
        kehadiran: { label: 'Kehadiran', color: '#009ef7', active: true },
        ketidakhadiran: { label: 'Ketidakhadiran', color: '#f1416c', active: false },
        izin: { label: 'Izin', color: '#7239ea', active: false },
        sakit: { label: 'Sakit', color: '#f59e0b', active: false },
        alpa: { label: 'Alpa', color: '#3f4254', active: false }
    };

    window.selectGuruTrendCriteria = function(criteriaKey) {
        if (!window.guruCriteriaConfig[criteriaKey]) return;

        var cfg = window.guruCriteriaConfig[criteriaKey];
        var activeCount = Object.keys(window.guruCriteriaConfig).filter(function(k) {
            return window.guruCriteriaConfig[k].active;
        }).length;

        if (cfg.active && activeCount <= 1) return;

        cfg.active = !cfg.active;

        var $card = $('.trend-summary-card-guru[data-criteria="' + criteriaKey + '"]');
        if (cfg.active) {
            $card.addClass('active').css('opacity', '1');
        } else {
            $card.removeClass('active').css('opacity', '0.45');
        }

        window.renderGuruTrendChart();
    };

    if ($('#filter_tanggal_guru').length && typeof flatpickr !== 'undefined') {
        var today = new Date();
        var todayStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');

        flatpickr("#filter_tanggal_guru", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: [todayStr, todayStr],
            onChange: function(selectedDates) {
                if (selectedDates.length === 2 || selectedDates.length === 1) {
                    window.fetchGuruTrendData();
                }
            },
            onClose: function() {
                window.fetchGuruTrendData();
            }
        });
    }

    window.fetchGuruTrendData = function() {
        var dateRangeVal = $('#filter_tanggal_guru').val() || '';
        var dates = dateRangeVal.split(/ to | hingga | - /);
        var start_date = dates[0] ? dates[0].trim() : '';
        var end_date = dates[1] ? dates[1].trim() : start_date;

        $('.trend-summary-card-guru span.fs-3').text('...');

        $.ajax({
            url: '{{ route("admin.dashboard.guru-trend-data") }}',
            method: 'GET',
            data: {
                start_date: start_date,
                end_date: end_date
            },
            success: function(res) {
                if (res && res.success) {
                    $('#summary_guru_kehadiran').text(res.totals.kehadiran);
                    $('#summary_guru_ketidakhadiran').text(res.totals.ketidakhadiran);
                    $('#summary_guru_izin').text(res.totals.izin);
                    $('#summary_guru_sakit').text(res.totals.sakit);
                    $('#summary_guru_alpa').text(res.totals.alpa);

                    window.rawGuruTrendChartData = res.chart || [];
                    window.renderGuruTrendChart();
                }
            },
            error: function() {
                $('.trend-summary-card-guru span.fs-3').text('0');
            }
        });
    };

    window.renderGuruTrendChart = function() {
        var chartEl = document.querySelector("#chart_trend_kehadiran_guru");
        if (!chartEl || !window.rawGuruTrendChartData || window.rawGuruTrendChartData.length === 0) return;

        // Jangan render ke container tersembunyi (0px SVG issue)
        if (chartEl.offsetWidth === 0 || $(chartEl).is(':hidden')) {
            return;
        }

        var categories = [];
        var activeSeries = [];
        var activeColors = [];
        var activeCount = 0;
        var totalCriteria = Object.keys(window.guruCriteriaConfig).length;

        window.rawGuruTrendChartData.forEach(function(item) {
            categories.push(item.tanggal);
        });

        var legendHtml = '';

        Object.keys(window.guruCriteriaConfig).forEach(function(key) {
            var cfg = window.guruCriteriaConfig[key];
            if (cfg.active) {
                activeCount++;
                activeColors.push(cfg.color);

                var seriesData = window.rawGuruTrendChartData.map(function(item) {
                    return item[key] || 0;
                });

                activeSeries.push({
                    name: cfg.label,
                    data: seriesData
                });

                legendHtml += '<span class="d-flex align-items-center fs-7 fw-bold text-gray-700 me-2">' +
                    '<span class="badge badge-circle me-2" style="background-color: ' + cfg.color + '; width: 9px; height: 9px;"></span>' +
                    cfg.label +
                    '</span>';
            }
        });

        $('#chart_guru_legend_container').html(legendHtml);
        $('#guru_criteria_counter').text('Kriteria Dipilih ' + activeCount + ' / ' + totalCriteria);

        var options = {
            series: activeSeries,
            chart: {
                height: 350,
                type: 'line',
                zoom: { enabled: false },
                toolbar: { show: false }
            },
            colors: activeColors,
            dataLabels: { enabled: false },
            stroke: {
                width: 3,
                curve: 'smooth'
            },
            markers: {
                size: 5,
                strokeWidth: 2,
                hover: { sizeOffset: 2 }
            },
            legend: { show: false },
            grid: {
                borderColor: '#eef2f5',
                strokeDashArray: 4,
                row: { colors: ['transparent'], opacity: 0.5 }
            },
            xaxis: {
                categories: categories,
                axisBorder: { show: true, color: '#e0e0e0' },
                axisTicks: { show: true }
            },
            yaxis: {
                title: {
                    text: 'Jumlah Guru',
                    style: { color: '#6c757d', fontSize: '12px', fontWeight: '600' }
                },
                min: 0,
                forceNiceScale: true,
                labels: {
                    formatter: function(val) { return Math.round(val); }
                }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(val) { return val + " guru"; }
                }
            }
        };

        if (typeof ApexCharts === 'undefined') return;

        try {
            if (window.guruTrendChartInstance) {
                try { window.guruTrendChartInstance.destroy(); } catch(e) {}
                window.guruTrendChartInstance = null;
            }
            chartEl.innerHTML = '';
            window.guruTrendChartInstance = new ApexCharts(chartEl, options);
            window.guruTrendChartInstance.render();
        } catch(e) {
            console.error('ApexCharts Guru render error:', e);
        }
    };

    // Re-render chart on tab switch
    $(document).on('shown.bs.tab', 'a[data-bs-toggle="tab"]', function(e) {
        var target = $(e.target).attr('href');
        if (target === '#tab_guru_trend') {
            setTimeout(function() {
                if (window.rawGuruTrendChartData && window.rawGuruTrendChartData.length > 0) {
                    window.renderGuruTrendChart();
                } else if (typeof window.fetchGuruTrendData === 'function') {
                    window.fetchGuruTrendData();
                }
            }, 50);
        } else if (target === '#tab_siswa_trend') {
            setTimeout(function() {
                if (window.rawTrendChartData && window.rawTrendChartData.length > 0) {
                    window.renderTrendChart();
                } else if (typeof window.fetchTrendData === 'function') {
                    window.fetchTrendData();
                }
            }, 50);
        }
    });

    // ==================== SYNC DATABASE TO HOSTING ====================
    function confirmSyncDatabase() {
        Swal.fire({
            title: 'Sinkronkan Database ke Hosting?',
            html: `
                <div class="text-start">
                    <p class="text-gray-700 mb-3">Sistem akan menyelaraskan data lokal ke hosting secara <strong>Smart Merge (Aman & Non-Destructive)</strong>.</p>
                    <div class="alert alert-light-success d-flex align-items-center p-3 mb-2 rounded border border-success">
                        <i class="bi bi-shield-check fs-2 text-success me-3"></i>
                        <div class="text-gray-800 fs-7"><strong>Data Hosting Aman:</strong> Data di hosting yang tidak ada di lokal (seperti riwayat absensi atau data siswa tambahan) <strong>tidak akan terhapus</strong>.</div>
                    </div>
                    <div class="alert alert-light-primary d-flex align-items-center p-3 rounded border border-primary">
                        <i class="bi bi-cloud-arrow-up fs-2 text-primary me-3"></i>
                        <div class="text-gray-800 fs-7"><strong>Data Lokal Bertambah:</strong> Data baru dari lokal akan otomatis dimasukkan ke hosting.</div>
                    </div>
                </div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-cloud-upload me-1"></i> Ya, Sinkronkan Sekarang!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#009ef7',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                startSyncDatabase();
            }
        });
    }

    function startSyncDatabase() {
        const btn = document.getElementById('btn-sync-database');
        const btnText = document.getElementById('sync-btn-text');

        // Loading state
        btn.disabled = true;
        btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Menyinkronkan...';

        fetch('{{ route("sync.send-to-hosting") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btnText.innerHTML = '<i class="bi bi-cloud-upload me-2"></i>Sinkronkan DB ke Hosting';

            if (data.success) {
                let summaryHtml = '';
                if (data.summary) {
                    const deletedCount = data.summary.deleted || 0;
                    summaryHtml = `
                        <div class="d-flex justify-content-center gap-3 my-4 flex-wrap">
                            <div class="bg-light-success p-3 rounded text-center min-w-100px border border-success">
                                <span class="fs-2 fw-bolder text-success d-block">+${data.summary.new_inserted || 0}</span>
                                <span class="fs-8 fw-bold text-gray-700">Data Baru</span>
                            </div>
                            <div class="bg-light-primary p-3 rounded text-center min-w-100px border border-primary">
                                <span class="fs-2 fw-bolder text-primary d-block">${data.summary.already_existing || 0}</span>
                                <span class="fs-8 fw-bold text-gray-700">Diperbarui</span>
                            </div>
                            ${deletedCount > 0 ? `
                            <div class="bg-light-danger p-3 rounded text-center min-w-100px border border-danger">
                                <span class="fs-2 fw-bolder text-danger d-block">-${deletedCount}</span>
                                <span class="fs-8 fw-bold text-gray-700">Dihapus</span>
                            </div>` : ''}
                        </div>
                        <p class="text-gray-600 fs-7">${data.summary.details || data.message}</p>
                    `;
                } else {
                    summaryHtml = `<p class="text-gray-700 my-3">${data.message}</p>`;
                }

                Swal.fire({
                    title: 'Sinkronisasi Berhasil!',
                    html: summaryHtml,
                    icon: 'success',
                    confirmButtonText: 'Selesai',
                    confirmButtonColor: '#50cd89',
                });
            } else {
                Swal.fire({
                    title: 'Sinkronisasi Gagal',
                    html: `<p class="text-danger fw-bold">${data.message}</p>${data.detail ? '<pre class="text-start fs-8 text-danger bg-light p-3 rounded mt-3" style="max-height:150px;overflow:auto">' + data.detail + '</pre>' : ''}`,
                    icon: 'error',
                    confirmButtonText: 'Tutup',
                });
            }
        })
        .catch(error => {
            btn.disabled = false;
            btnText.innerHTML = '<i class="bi bi-cloud-upload me-2"></i>Sinkronkan DB ke Hosting';
            Swal.fire({
                title: 'Koneksi Gagal',
                text: 'Tidak dapat terhubung ke server hosting. Pastikan koneksi internet Anda aktif.',
                icon: 'error',
            });
        });
    }
</script>
@endpush

</x-base-layout>
