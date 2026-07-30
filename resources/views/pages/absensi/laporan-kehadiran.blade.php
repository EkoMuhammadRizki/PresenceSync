<x-base-layout>
@include('pages.absensi._partials.toolbar')

@push('styles')
<link href="{{ asset(theme()->getDemo() . '/plugins/custom/flatpickr/flatpickr.bundle.css') }}" rel="stylesheet" type="text/css"/>
<style>
    .stat-card { border-radius: 12px; padding: 1.25rem 1.5rem; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-card .stat-value { font-size: 1.8rem; font-weight: 700; }
    .stat-card .stat-label { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.06em; opacity: 0.75; }
    .filter-card { border-radius: 12px; }
    .table-laporan thead th { background: #f5f8fa; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e9ecef; }
    .badge-hadir     { background-color: #e8fff3; color: #50cd89; }
    .badge-terlambat { background-color: #fff8dd; color: #f6c000; }
    .badge-sakit     { background-color: #e8f4fd; color: #009ef7; }
    .badge-izin      { background-color: #f8f5ff; color: #7239ea; }
    .badge-alpha     { background-color: #fff5f8; color: #f1416c; }
</style>
@endpush

<div id="kt_content_container" class="container-xxl">

    {{-- ─── Statistik ─── --}}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-6 g-5 mb-8">
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-primary border border-primary">
                <span class="svg-icon svg-icon-3x svg-icon-primary mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-3x svg-icon-primary") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Total Record</span>
                <span class="fs-2hx fw-bolder text-primary">{{ $rekap['total'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Keseluruhan</span>
            </div>
        </div>
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-success border border-success">
                <span class="svg-icon svg-icon-3x svg-icon-success mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen026.svg", "svg-icon-3x svg-icon-success") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Hadir</span>
                <span class="fs-2hx fw-bolder text-success">{{ $rekap['hadir'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Tepat Waktu</span>
            </div>
        </div>
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-warning border border-warning">
                <span class="svg-icon svg-icon-3x svg-icon-warning mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen050.svg", "svg-icon-3x svg-icon-warning") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Terlambat</span>
                <span class="fs-2hx fw-bolder text-warning">{{ $rekap['terlambat'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Masuk Terlambat</span>
            </div>
        </div>
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-info border border-info">
                <span class="svg-icon svg-icon-3x svg-icon-info mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg", "svg-icon-3x svg-icon-info") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Sakit</span>
                <span class="fs-2hx fw-bolder text-info">{{ $rekap['sakit'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Surat Dokter</span>
            </div>
        </div>
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light border border-secondary">
                <span class="svg-icon svg-icon-3x svg-icon-gray-600 mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/communication/com002.svg", "svg-icon-3x svg-icon-gray-600") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Izin</span>
                <span class="fs-2hx fw-bolder text-gray-800">{{ $rekap['izin'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Keperluan Keterangan</span>
            </div>
        </div>
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-danger border border-danger">
                <span class="svg-icon svg-icon-3x svg-icon-danger mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen040.svg", "svg-icon-3x svg-icon-danger") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Alpha</span>
                <span class="fs-2hx fw-bolder text-danger">{{ $rekap['alpha'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Tanpa Keterangan</span>
            </div>
        </div>
    </div>

    {{-- ─── Filter ─── --}}
    <div class="card filter-card mb-5">
        <div class="card-body py-4">
            <form method="GET" action="{{ route('laporan.kehadiran') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold fs-7">Tanggal Mulai</label>
                    <input type="text" name="start_date" id="fp_start" value="{{ $startDate }}" class="form-control form-control-sm" placeholder="Pilih tanggal mulai" autocomplete="off">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold fs-7">Tanggal Akhir</label>
                    <input type="text" name="end_date" id="fp_end" value="{{ $endDate }}" class="form-control form-control-sm" placeholder="Pilih tanggal akhir" autocomplete="off">
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold fs-7">Kelas</label>
                    <select name="kelas_id" class="form-select form-select-sm">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold fs-7">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="hadir" {{ request('status') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="terlambat" {{ request('status') === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                        <option value="sakit" {{ request('status') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="izin" {{ request('status') === 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="alpha" {{ request('status') === 'alpha' ? 'selected' : '' }}>Alpha</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('laporan.kehadiran') }}" class="btn btn-light btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- ─── Tabel ─── --}}
    <div class="card">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-4">Data Kehadiran Siswa</span>
                <span class="text-muted mt-1 fw-semibold fs-7">
                    {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}
                    — {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                    &bull; {{ $kehadirans->count() }} record
                </span>
            </h3>
            <div class="card-toolbar">
                <div class="d-flex gap-2">
                    <a href="{{ route('laporan.kehadiran.export-pdf', request()->query()) }}" class="btn btn-sm btn-danger fw-bold" target="_blank" data-ajax="false">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                    </a>
                    <a href="{{ route('laporan.kehadiran.export-excel', request()->query()) }}" class="btn btn-sm btn-success fw-bold" target="_blank" data-ajax="false">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-hover table-laporan align-middle gs-0 gy-3">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 rounded-start" style="width:50px;">No</th>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>NIS</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th>Jam Masuk</th>
                            <th class="rounded-end">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kehadirans as $i => $k)
                        @php
                            $badgeClass = match($k->status) {
                                'hadir'     => 'badge-hadir',
                                'terlambat' => 'badge-terlambat',
                                'sakit'     => 'badge-sakit',
                                'izin'      => 'badge-izin',
                                'alpha'     => 'badge-alpha',
                                default     => 'badge-light-secondary',
                            };
                            $statusLabel = match($k->status) {
                                'hadir'     => 'Hadir',
                                'terlambat' => 'Terlambat',
                                'sakit'     => 'Sakit',
                                'izin'      => 'Izin',
                                'alpha'     => 'Alpha',
                                default     => ucfirst($k->status),
                            };
                        @endphp
                        <tr>
                            <td class="ps-4 text-muted fs-7">{{ $i + 1 }}</td>
                            <td class="fw-semibold fs-7">{{ $k->tanggal ? \Carbon\Carbon::parse($k->tanggal)->translatedFormat('d M Y') : '-' }}</td>
                            <td>
                                <span class="fw-bold text-gray-800 fs-7">{{ $k->siswa?->nama ?? '-' }}</span>
                            </td>
                            <td class="text-gray-600 fs-7">{{ $k->siswa?->nis ?? '-' }}</td>
                            <td>
                                @if($k->siswa?->kelas)
                                    <span class="badge badge-light-info fw-semibold">{{ $k->siswa->kelas->nama }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $badgeClass }} fw-semibold">{{ $statusLabel }}</span>
                            </td>
                            <td class="text-gray-600 fs-7">
                                {{ $k->jam_masuk ? \Carbon\Carbon::parse($k->jam_masuk)->format('H:i') : '-' }}
                            </td>
                            <td class="text-gray-600 fs-7">{{ $k->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-8">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Tidak ada data kehadiran pada filter yang dipilih.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="{{ asset(theme()->getDemo() . '/plugins/custom/flatpickr/flatpickr.bundle.js') }}"></script>
<script>
flatpickr('#fp_start', { dateFormat: 'Y-m-d', locale: 'id', allowInput: true });
flatpickr('#fp_end',   { dateFormat: 'Y-m-d', locale: 'id', allowInput: true });
</script>
@endpush

</x-base-layout>
