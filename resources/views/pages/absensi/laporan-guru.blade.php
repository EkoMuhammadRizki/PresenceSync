<x-base-layout>
@include('pages.absensi._partials.toolbar')

@push('styles')
<style>
    .stat-card { border-radius: 12px; padding: 1.25rem 1.5rem; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-card .stat-value { font-size: 2rem; font-weight: 700; }
    .stat-card .stat-label { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.06em; opacity: 0.75; }
    .filter-card { border-radius: 12px; }
    .table-laporan thead th { background: #f5f8fa; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e9ecef; }
    .progress-xs { height: 5px; border-radius: 4px; }
</style>
@endpush

<div id="kt_content_container" class="container-xxl">

    {{-- ─── Statistik ─── --}}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-5 mb-8">
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-primary border border-primary h-100">
                <span class="svg-icon svg-icon-3x svg-icon-primary mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/communication/com014.svg", "svg-icon-3x svg-icon-primary") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Total Guru</span>
                <span class="fs-2hx fw-bolder text-primary">{{ $rekap['total'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Guru Terdaftar</span>
            </div>
        </div>
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-success border border-success h-100">
                <span class="svg-icon svg-icon-3x svg-icon-success mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen025.svg", "svg-icon-3x svg-icon-success") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Wali Kelas</span>
                <span class="fs-2hx fw-bolder text-success">{{ $gurus->filter(fn($g) => $g->kelas->isNotEmpty())->count() }}</span>
                <span class="fs-8 fw-bold text-gray-500">Memiliki Kelas Wali</span>
            </div>
        </div>
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-warning border border-warning h-100">
                <span class="svg-icon svg-icon-3x svg-icon-warning mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-3x svg-icon-warning") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Total Presensi</span>
                <span class="fs-2hx fw-bolder text-warning">{{ $kehadiranRekap->sum('total') }}</span>
                <span class="fs-8 fw-bold text-gray-500">Record Kehadiran</span>
            </div>
        </div>
    </div>

    {{-- ─── Filter ─── --}}
    <div class="card filter-card mb-5">
        <div class="card-body py-4">
            <form method="GET" action="{{ route('laporan.guru') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold fs-7">Cari Nama / NIP / Email</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Ketik nama guru, NIP, atau email...">
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('laporan.guru') }}" class="btn btn-light btn-sm">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ─── Tabel ─── --}}
    <div class="card">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-4">Data Guru</span>
                <span class="text-muted mt-1 fw-semibold fs-7">{{ $gurus->count() }} guru ditemukan</span>
            </h3>
            <div class="card-toolbar">
                <div class="d-flex gap-2">
                    <a href="{{ route('laporan.guru.export-pdf', request()->query()) }}" class="btn btn-sm btn-danger fw-bold" target="_blank" data-ajax="false">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                    </a>
                    <a href="{{ route('laporan.guru.export-excel', request()->query()) }}" class="btn btn-sm btn-success fw-bold" target="_blank" data-ajax="false">
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
                            <th>Nama Guru</th>
                            <th>NIP</th>
                            <th>Email</th>
                            <th>No. HP</th>
                            <th>Kelas Wali</th>
                            <th>Kehadiran</th>
                            <th class="text-center rounded-end">Terlambat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($gurus as $i => $guru)
                        @php
                            $rekap = $kehadiranRekap->get($guru->id);
                            $total     = $rekap?->total ?? 0;
                            $hadir     = $rekap?->hadir ?? 0;
                            $terlambat = $rekap?->terlambat ?? 0;
                            $pct = $total > 0 ? round(($hadir / $total) * 100) : 0;
                            $kelasWali = $guru->kelas->first();
                        @endphp
                        <tr>
                            <td class="ps-4 text-muted fs-7">{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="symbol symbol-35px symbol-circle">
                                        <span class="symbol-label bg-light-warning text-warning fw-bold fs-7">
                                            {{ strtoupper(substr($guru->nama, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-gray-800 fs-7">{{ $guru->nama }}</span>
                                        @if($guru->alamat)
                                            <div class="text-muted fs-8">{{ Str::limit($guru->alamat, 40) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-gray-600 fs-7">{{ $guru->nip ?? '-' }}</td>
                            <td class="text-gray-600 fs-7">{{ $guru->email ?? '-' }}</td>
                            <td class="text-gray-600 fs-7">{{ $guru->no_hp ?? '-' }}</td>
                            <td>
                                @if($kelasWali)
                                    <span class="badge badge-light-primary fw-semibold">{{ $kelasWali->nama }}</span>
                                @else
                                    <span class="text-muted fs-8">-</span>
                                @endif
                            </td>
                            <td style="min-width:140px;">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold fs-7 mb-1">{{ $hadir }}/{{ $total }} <span class="fw-normal text-muted">({{ $pct }}%)</span></span>
                                    <div class="progress progress-xs bg-light-success">
                                        <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($terlambat > 0)
                                    <span class="badge badge-light-warning fw-semibold">{{ $terlambat }}x</span>
                                @else
                                    <span class="text-muted fs-8">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-8">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Tidak ada data guru yang sesuai filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</x-base-layout>
