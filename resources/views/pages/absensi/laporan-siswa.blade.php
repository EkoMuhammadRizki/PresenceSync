<x-base-layout>
@include('pages.absensi._partials.toolbar')

@push('styles')
<link href="{{ asset(theme()->getDemo() . '/plugins/custom/flatpickr/flatpickr.bundle.css') }}" rel="stylesheet" type="text/css"/>
<style>
    .stat-card {
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-card .stat-value { font-size: 2rem; font-weight: 700; }
    .stat-card .stat-label { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.06em; opacity: 0.75; }
    .export-btn-group .btn { font-weight: 600; }
    .filter-card { border-radius: 12px; }
    .table-laporan thead th { background: #f5f8fa; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e9ecef; }
    .badge-aktif    { background-color: #e8fff3; color: #50cd89; }
    .badge-lulus    { background-color: #f8f5ff; color: #7239ea; }
    .badge-keluar   { background-color: #fff5f8; color: #f1416c; }
    .badge-gender   { background-color: #eff8ff; color: #009ef7; }
</style>
@endpush

<div id="kt_content_container" class="container-xxl">

    {{-- ─── Statistik Rekap ─── --}}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-6 g-5 mb-8">
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-primary border border-primary">
                <span class="svg-icon svg-icon-3x svg-icon-primary mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/communication/com013.svg", "svg-icon-3x svg-icon-primary") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Total Siswa</span>
                <span class="fs-2hx fw-bolder text-primary">{{ $rekap['total'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Keseluruhan</span>
            </div>
        </div>
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-success border border-success">
                <span class="svg-icon svg-icon-3x svg-icon-success mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen026.svg", "svg-icon-3x svg-icon-success") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Aktif</span>
                <span class="fs-2hx fw-bolder text-success">{{ $rekap['aktif'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Siswa Aktif</span>
            </div>
        </div>
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-info border border-info">
                <span class="svg-icon svg-icon-3x svg-icon-info mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/arrows/arr016.svg", "svg-icon-3x svg-icon-info") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Lulus</span>
                <span class="fs-2hx fw-bolder text-info">{{ $rekap['lulus'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Alumni</span>
            </div>
        </div>
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-danger border border-danger">
                <span class="svg-icon svg-icon-3x svg-icon-danger mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/arrows/arr015.svg", "svg-icon-3x svg-icon-danger") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Keluar</span>
                <span class="fs-2hx fw-bolder text-danger">{{ $rekap['keluar'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Tidak Aktif</span>
            </div>
        </div>
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-warning border border-warning">
                <span class="svg-icon svg-icon-3x svg-icon-warning mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/communication/com006.svg", "svg-icon-3x svg-icon-warning") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Laki-laki</span>
                <span class="fs-2hx fw-bolder text-warning">{{ $rekap['L'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Siswa Laki-laki</span>
            </div>
        </div>
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light border border-secondary">
                <span class="svg-icon svg-icon-3x svg-icon-gray-600 mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/communication/com006.svg", "svg-icon-3x svg-icon-gray-600") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Perempuan</span>
                <span class="fs-2hx fw-bolder text-gray-800">{{ $rekap['P'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Siswa Perempuan</span>
            </div>
        </div>
    </div>

    {{-- ─── Filter ─── --}}
    <div class="card filter-card mb-5">
        <div class="card-body py-4">
            <form method="GET" action="{{ route('laporan.siswa') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold fs-7">Kelas</label>
                    <select name="kelas_id" class="form-select form-select-sm">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold fs-7">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="lulus" {{ request('status') === 'lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="keluar" {{ request('status') === 'keluar' ? 'selected' : '' }}>Keluar</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold fs-7">Cari Nama / NIS</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Ketik nama atau NIS...">
                </div>
                <div class="col-12 col-md-1 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request()->hasAny(['kelas_id','status','search']))
                        <a href="{{ route('laporan.siswa') }}" class="btn btn-light btn-sm w-100" title="Reset">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ─── Tabel ─── --}}
    <div class="card">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-4">Data Siswa</span>
                <span class="text-muted mt-1 fw-semibold fs-7">{{ $siswas->count() }} siswa ditemukan</span>
            </h3>
            <div class="card-toolbar">
                <div class="d-flex gap-2">
                    <a href="{{ route('laporan.siswa.export-pdf', request()->query()) }}" class="btn btn-sm btn-danger fw-bold" target="_blank" data-ajax="false">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                    </a>
                    <a href="{{ route('laporan.siswa.export-excel', request()->query()) }}" class="btn btn-sm btn-success fw-bold" target="_blank" data-ajax="false">
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
                            <th>Nama Siswa</th>
                            <th>NIS</th>
                            <th>Kelas</th>
                            <th>Jenis Kelamin</th>
                            <th>Tgl. Lahir</th>
                            <th class="rounded-end">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($siswas as $i => $siswa)
                        <tr>
                            <td class="ps-4 text-muted fs-7">{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="symbol symbol-35px symbol-circle">
                                        <span class="symbol-label bg-light-primary text-primary fw-bold fs-7">
                                            {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-gray-800 fs-7">{{ $siswa->nama }}</span>
                                        @if($siswa->no_hp)
                                            <div class="text-muted fs-8">{{ $siswa->no_hp }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-gray-600 fs-7">{{ $siswa->nis ?? '-' }}</td>
                            <td>
                                @if($siswa->kelas)
                                    <span class="badge badge-light-info fw-semibold">{{ $siswa->kelas->nama }}</span>
                                @else
                                    <span class="text-muted fs-8">-</span>
                                @endif
                            </td>
                            <td class="fs-7">
                                {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}
                            </td>
                            <td class="text-gray-600 fs-7">
                                {{ $siswa->tanggal_lahir ? $siswa->tanggal_lahir->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td>
                                @php
                                    $statusClass = match($siswa->status) {
                                        'aktif'  => 'badge-light-success',
                                        'lulus'  => 'badge-light-purple',
                                        'keluar' => 'badge-light-danger',
                                        default  => 'badge-light-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }} fw-semibold">{{ ucfirst($siswa->status ?? '-') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-8">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Tidak ada data siswa yang sesuai filter.
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
