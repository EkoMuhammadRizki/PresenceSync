<x-base-layout>
@include('pages.absensi._partials.toolbar')

@push('styles')
<link href="{{ asset(theme()->getDemo() . '/plugins/custom/flatpickr/flatpickr.bundle.css') }}" rel="stylesheet" type="text/css"/>
<style>
    .stat-card { border-radius: 12px; padding: 1.25rem 1.5rem; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-card .stat-value { font-size: 2rem; font-weight: 700; }
    .stat-card .stat-label { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.06em; opacity: 0.75; }
    .filter-card { border-radius: 12px; }
    .table-laporan thead th { background: #f5f8fa; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e9ecef; }
    .desc-truncate { max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: pointer; }
</style>
@endpush


    {{-- ─── Statistik ─── --}}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-5 mb-8">
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-danger border border-danger h-100">
                <span class="svg-icon svg-icon-3x svg-icon-danger mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg", "svg-icon-3x svg-icon-danger") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Total Pengaduan</span>
                <span class="fs-2hx fw-bolder text-danger">{{ $rekap['total'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Laporan Masuk</span>
            </div>
        </div>
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-primary border border-primary h-100">
                <span class="svg-icon svg-icon-3x svg-icon-primary mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-3x svg-icon-primary") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Periode Mulai</span>
                <span class="fs-2 fw-bolder text-primary mt-1">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}</span>
                <span class="fs-8 fw-bold text-gray-500">Tanggal Awal Filter</span>
            </div>
        </div>
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-info border border-info h-100">
                <span class="svg-icon svg-icon-3x svg-icon-info mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-3x svg-icon-info") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Periode Akhir</span>
                <span class="fs-2 fw-bolder text-info mt-1">{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</span>
                <span class="fs-8 fw-bold text-gray-500">Tanggal Akhir Filter</span>
            </div>
        </div>
    </div>

    {{-- ─── Filter ─── --}}
    <div class="card filter-card mb-5">
        <div class="card-body py-4">
            <form method="GET" action="{{ route('laporan.pengaduan') }}" class="row g-3 align-items-end">
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
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold fs-7">Cari Nama Siswa</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Nama siswa...">
                </div>
                <div class="col-12 col-md-1 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('laporan.pengaduan') }}" class="btn btn-light btn-sm" title="Reset">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ─── Tabel ─── --}}
    <div class="card">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-4">Data Pengaduan Siswa</span>
                <span class="text-muted mt-1 fw-semibold fs-7">
                    {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}
                    — {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                    &bull; {{ $pengaduans->count() }} pengaduan
                </span>
            </h3>
            <div class="card-toolbar">
                <div class="d-flex gap-2">
                    <a href="{{ route('laporan.pengaduan.export-pdf', request()->query()) }}" class="btn btn-sm btn-danger fw-bold" target="_blank" data-ajax="false">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                    </a>
                    <a href="{{ route('laporan.pengaduan.export-excel', request()->query()) }}" class="btn btn-sm btn-success fw-bold" target="_blank" data-ajax="false">
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
                            <th class="rounded-end">Deskripsi Pengaduan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengaduans as $i => $p)
                        <tr>
                            <td class="ps-4 text-muted fs-7">{{ $i + 1 }}</td>
                            <td class="fw-semibold fs-7">
                                {{ $p->tanggal ? \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="symbol symbol-30px symbol-circle">
                                        <span class="symbol-label bg-light-danger text-danger fw-bold fs-8">
                                            {{ strtoupper(substr($p->siswa?->nama ?? '?', 0, 1)) }}
                                        </span>
                                    </div>
                                    <span class="fw-bold text-gray-800 fs-7">{{ $p->siswa?->nama ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="text-gray-600 fs-7">{{ $p->siswa?->nis ?? '-' }}</td>
                            <td>
                                @if($p->siswa?->kelas)
                                    <span class="badge badge-light-info fw-semibold">{{ $p->siswa->kelas->nama }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="desc-truncate fs-7 text-gray-700" title="{{ $p->deskripsi }}">
                                    {{ $p->deskripsi ?? '-' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-8">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Tidak ada pengaduan pada periode yang dipilih.
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
