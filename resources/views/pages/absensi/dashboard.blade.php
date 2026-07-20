<x-base-layout>
@php $showPanduan = $showPanduan ?? false; @endphp
@include('pages.absensi._partials.toolbar')

<!-- Extra Styles for Dashboard Visualizations -->
@push('styles')
<link href="{{ asset(theme()->getDemo() . '/plugins/custom/flatpickr/flatpickr.bundle.css') }}" rel="stylesheet" type="text/css"/>
<style>
    .scrollable-table-container {
        max-height: 420px;
        overflow-y: auto;
    }
    /* Dynamic bottom border highlight for Trend parameter cards */
    .trend-summary-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        cursor: pointer;
    }
    .trend-summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }
</style>
@endpush

<div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">

        <!-- ==================== ROW 1: TOP STATISTICS CARDS ==================== -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-5 mb-8">
            
            <!-- Card 1: Jumlah Siswa -->
            <div class="col">
                <div class="card card-dashed flex-center min-w-100px p-6 bg-light-primary border-primary border-dashed">
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
                <div class="card card-dashed flex-center min-w-100px p-6 bg-light-success border-success border-dashed">
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
                <div class="card card-dashed flex-center min-w-100px p-6 bg-light-info border-info border-dashed">
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
                <div class="card card-dashed flex-center min-w-100px p-6 bg-light-warning border-warning border-dashed">
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
                <div class="card card-dashed flex-center min-w-100px p-6 bg-light-danger border-danger border-dashed">
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
            
            <!-- COLUMN 1: TREND KEHADIRAN (GRID 8) -->
            <div class="col-xl-8">
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
                                    <div class="d-flex align-items-center gap-2">
                                        <label class="fs-7 fw-bold text-gray-700">Periode:</label>
                                        <input class="form-control form-control-solid form-control-sm w-200px" placeholder="Pilih Tanggal..." id="filter_tanggal" />
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
                                <div class="row row-cols-2 row-cols-sm-5 g-3 mb-6">
                                    <!-- Kehadiran -->
                                    <div class="col">
                                        <div class="card card-dashed trend-summary-card p-4 border-start border-start-5 border-start-primary bg-light">
                                            <span class="fs-8 fw-bold text-gray-600">Kehadiran</span>
                                            <span class="fs-3 fw-boldest text-dark" id="summary_kehadiran">0</span>
                                        </div>
                                    </div>
                                    <!-- Ketidakhadiran -->
                                    <div class="col">
                                        <div class="card card-dashed trend-summary-card p-4 border-start border-start-5 border-start-danger bg-light">
                                            <span class="fs-8 fw-bold text-gray-600">Ketidakhadiran</span>
                                            <span class="fs-3 fw-boldest text-dark" id="summary_ketidakhadiran">0</span>
                                        </div>
                                    </div>
                                    <!-- Izin -->
                                    <div class="col">
                                        <div class="card card-dashed trend-summary-card p-4 border-start border-start-5 border-start-info bg-light">
                                            <span class="fs-8 fw-bold text-gray-600">Izin</span>
                                            <span class="fs-3 fw-boldest text-dark" id="summary_izin">0</span>
                                        </div>
                                    </div>
                                    <!-- Sakit -->
                                    <div class="col">
                                        <div class="card card-dashed trend-summary-card p-4 border-start border-start-5 border-start-warning bg-light">
                                            <span class="fs-8 fw-bold text-gray-600">Sakit</span>
                                            <span class="fs-3 fw-boldest text-dark" id="summary_sakit">0</span>
                                        </div>
                                    </div>
                                    <!-- Alpa -->
                                    <div class="col">
                                        <div class="card card-dashed trend-summary-card p-4 border-start border-start-5 border-start-dark bg-light">
                                            <span class="fs-8 fw-bold text-gray-600">Alpa</span>
                                            <span class="fs-3 fw-boldest text-dark" id="summary_alpa">0</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chart Trend Container -->
                                <div id="chart_trend_kehadiran_siswa"></div>

                            </div>

                            <!-- TAB 2: KEHADIRAN GURU (PLACEHOLDER) -->
                            <div class="tab-pane fade" id="tab_guru_trend" role="tabpanel">
                                <div class="text-center py-15">
                                    <div class="mb-4">
                                        {!! theme()->getSvgIcon("icons/duotune/general/gen006.svg", "svg-icon-5x text-muted") !!}
                                    </div>
                                    <h4 class="text-gray-700 fw-bolder mb-1">Kehadiran Guru</h4>
                                    <p class="text-muted fw-bold fs-6">Data kehadiran guru belum dikonfigurasi pada semester ini.</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMN 2: KETERLAMBATAN HARI INI (GRID 4) -->
            <div class="col-xl-4">
                <div class="card card-xxl-stretch mb-5 mb-xl-8">
                    <!-- Card Header -->
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 text-dark">Keterlambatan Hari Ini</span>
                            <span class="text-muted mt-1 fw-bold fs-7">Urutan check-in terlambat paling baru</span>
                        </h3>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body pt-3">
                        <div class="scrollable-table-container">
                            <table class="table align-middle table-row-dashed fs-7 gy-3">
                                <thead>
                                    <tr class="text-start text-muted fw-bolder fs-8 text-uppercase gs-0">
                                        <th>Nama</th>
                                        <th>Kelas</th>
                                        <th>Status</th>
                                        <th class="text-end">Terlambat</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-bold">
                                    @forelse($terlambats as $t)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-30px symbol-circle me-3">
                                                    @if($t['foto'])
                                                        <img src="{{ asset('storage/' . $t['foto']) }}" alt="Siswa" />
                                                    @else
                                                        <div class="symbol-label fs-8 bg-light-danger text-danger fw-bolder">
                                                            {{ substr($t['nama'], 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-800 text-hover-primary fw-bolder">{{ $t['nama'] }}</span>
                                                    <span class="text-muted fs-9">{{ $t['waktu'] }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $t['kelas'] }}</td>
                                        <td>
                                            <span class="badge badge-light-danger fs-8">Terlambat</span>
                                        </td>
                                        <td class="text-end text-danger fw-bolder">{{ $t['menit_terlambat'] }} Menit</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-8 text-muted">
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

</x-base-layout>

@push('scripts')
<script src="{{ asset(theme()->getDemo() . '/plugins/custom/flatpickr/flatpickr.bundle.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    var trendChart;

    $(document).ready(function() {
        // Use native JS to calculate startDefault and endDefault
        var today = new Date();
        var priorDate = new Date();
        priorDate.setDate(today.getDate() - 6);
        
        var startDefault = priorDate.getFullYear() + '-' + String(priorDate.getMonth() + 1).padStart(2, '0') + '-' + String(priorDate.getDate()).padStart(2, '0');
        var endDefault = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');

        flatpickr("#filter_tanggal", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: [startDefault, endDefault],
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    updateTrendData();
                }
            }
        });

        // Event listener for Class Filter Select2 change
        $('#filter_kelas').on('change', function() {
            updateTrendData();
        });

        // Initialize ApexCharts with empty layout
        var options = {
            series: [],
            chart: {
                height: 350,
                type: 'line',
                zoom: {
                    enabled: false
                },
                toolbar: {
                    show: false
                }
            },
            colors: ['#009ef7', '#f1416c', '#7239ea', '#ffc700', '#181c32'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                width: [3, 3, 2, 2, 2],
                curve: 'smooth',
                dashArray: [0, 0, 5, 5, 5]
            },
            legend: {
                show: true,
                position: 'top',
                horizontalAlign: 'right',
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: [],
            },
            yaxis: {
                title: {
                    text: 'Jumlah Siswa'
                },
                labels: {
                    formatter: function(val) {
                        return Math.round(val);
                    }
                }
            }
        };

        trendChart = new ApexCharts(document.querySelector("#chart_trend_kehadiran_siswa"), options);
        trendChart.render();

        // Initial fetch
        updateTrendData();
    });

    /**
     * AJAX load and update Trend Kehadiran
     */
    function updateTrendData() {
        var dateRangeVal = $('#filter_tanggal').val();
        var dates = dateRangeVal.split(' to ');
        
        var start_date = dates[0] || '';
        var end_date = dates[1] || start_date;
        var kelas_id = $('#filter_kelas').val() || '';

        // Show loading progress on cards
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
                if (res.success) {
                    // Update parameter totals
                    $('#summary_kehadiran').text(res.totals.kehadiran);
                    $('#summary_ketidakhadiran').text(res.totals.ketidakhadiran);
                    $('#summary_izin').text(res.totals.izin);
                    $('#summary_sakit').text(res.totals.sakit);
                    $('#summary_alpa').text(res.totals.alpa);

                    // Map chart arrays
                    var categories = [];
                    var seriesKehadiran = [];
                    var seriesKetidakhadiran = [];
                    var seriesIzin = [];
                    var seriesSakit = [];
                    var seriesAlpa = [];

                    res.chart.forEach(function(item) {
                        categories.push(item.tanggal);
                        seriesKehadiran.push(item.kehadiran);
                        seriesKetidakhadiran.push(item.ketidakhadiran);
                        seriesIzin.push(item.izin);
                        seriesSakit.push(item.sakit);
                        seriesAlpa.push(item.alpa);
                    });

                    // Update ApexCharts series
                    trendChart.updateOptions({
                        xaxis: {
                            categories: categories
                        }
                    });

                    trendChart.updateSeries([
                        {
                            name: 'Kehadiran',
                            data: seriesKehadiran
                        },
                        {
                            name: 'Ketidakhadiran',
                            data: seriesKetidakhadiran
                        },
                        {
                            name: 'Izin',
                            data: seriesIzin
                        },
                        {
                            name: 'Sakit',
                            data: seriesSakit
                        },
                        {
                            name: 'Alpa',
                            data: seriesAlpa
                        }
                    ]);
                }
            },
            error: function() {
                $('.trend-summary-card span.fs-3').text('Error');
            }
        });
    }
</script>
@endpush
