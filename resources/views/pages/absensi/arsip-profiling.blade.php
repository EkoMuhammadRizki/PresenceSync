<x-base-layout>
@include('pages.absensi._partials.toolbar')

@push('styles')
<style>
    /* ── Table ── */
    .profiling-table thead th {
        background: #f5f8fa;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #e9ecef;
        white-space: nowrap;
    }
    .profiling-table tbody tr { transition: background 0.15s; }
    .profiling-table tbody tr:hover { background: #f5f8ff !important; }

    .progress-track {
        height: 8px;
        border-radius: 4px;
        background: #e9ecef;
        width: 80px;
        display: inline-block;
        vertical-align: middle;
    }
    .profiling-header-card {
        background: #fff;
        border-radius: 12px;
        border-left: 5px solid #009ef7;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
</style>
@endpush


    {{-- ─── Back Button ─── --}}
    <div class="mb-5">
        <a href="{{ route('arsip.index') }}" class="btn btn-sm btn-light fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Arsip
        </a>
    </div>

    {{-- ─── Profiling Header Card ─── --}}
    <div class="profiling-header-card d-flex align-items-center gap-4 mb-7">
        <div class="flex-shrink-0">
            <span class="svg-icon svg-icon-3x svg-icon-primary">
                {!! theme()->getSvgIcon("icons/duotune/files/fil017.svg", "svg-icon-3x svg-icon-primary") !!}
            </span>
        </div>
        <div>
            <div class="text-muted fs-7 fw-semibold mb-1 text-uppercase" style="letter-spacing:.08em;">
                Arsip Profiling Kehadiran Siswa
            </div>
            <h2 class="text-dark fw-bolder mb-1" style="font-size:1.45rem;">
                Tahun Ajaran {{ $semester->tahunAjaran->nama ?? '-' }}
                &mdash; Semester {{ ucfirst($semester->jenis) }}
            </h2>
            <div class="text-muted fs-6">
                @if($semester->tanggal_mulai && $semester->tanggal_selesai)
                    {{ $semester->tanggal_mulai->format('d M Y') }} &ndash; {{ $semester->tanggal_selesai->format('d M Y') }}
                @endif
            </div>
        </div>
    </div>

    {{-- ─── Summary Stats Cards (gaya laporan-siswa) ─── --}}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-6 g-5 mb-8">

        {{-- Total Siswa --}}
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-primary border border-primary">
                <span class="svg-icon svg-icon-3x svg-icon-primary mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/communication/com013.svg", "svg-icon-3x svg-icon-primary") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Total Siswa</span>
                <span class="fs-2hx fw-bolder text-primary">{{ $summary['total_siswa'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Keseluruhan</span>
            </div>
        </div>

        {{-- Total Hari --}}
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light border border-secondary">
                <span class="svg-icon svg-icon-3x svg-icon-gray-600 mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-3x svg-icon-gray-600") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Total Hari</span>
                <span class="fs-2hx fw-bolder text-gray-800">{{ $summary['total_hari'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Efektif</span>
            </div>
        </div>

        {{-- Total Hadir --}}
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-success border border-success">
                <span class="svg-icon svg-icon-3x svg-icon-success mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen026.svg", "svg-icon-3x svg-icon-success") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Hadir</span>
                <span class="fs-2hx fw-bolder text-success">{{ $summary['total_hadir'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Total Hadir</span>
            </div>
        </div>

        {{-- Terlambat --}}
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-warning border border-warning">
                <span class="svg-icon svg-icon-3x svg-icon-warning mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen013.svg", "svg-icon-3x svg-icon-warning") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Terlambat</span>
                <span class="fs-2hx fw-bolder text-warning">{{ $summary['total_terlambat'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Total</span>
            </div>
        </div>

        {{-- Sakit / Izin --}}
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-info border border-info">
                <span class="svg-icon svg-icon-3x svg-icon-info mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/medicine/med001.svg", "svg-icon-3x svg-icon-info") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Sakit / Izin</span>
                <span class="fs-2hx fw-bolder text-info">{{ $summary['total_sakit'] + $summary['total_izin'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Total</span>
            </div>
        </div>

        {{-- Alpha --}}
        <div class="col">
            <div class="card flex-center min-w-100px p-6 bg-light-danger border border-danger">
                <span class="svg-icon svg-icon-3x svg-icon-danger mb-2">
                    {!! theme()->getSvgIcon("icons/duotune/arrows/arr015.svg", "svg-icon-3x svg-icon-danger") !!}
                </span>
                <span class="fs-6 fw-bold text-gray-700 pb-1">Alpha</span>
                <span class="fs-2hx fw-bolder text-danger">{{ $summary['total_alpha'] }}</span>
                <span class="fs-8 fw-bold text-gray-500">Tidak Hadir</span>
            </div>
        </div>

    </div>

    {{-- ─── Rata-rata Kehadiran Bar ─── --}}
    <div class="card rounded-3 mb-7 shadow-sm border-0">
        <div class="card-body d-flex align-items-center gap-4 py-4 px-6">
            <div class="fw-bold text-dark fs-5 me-3" style="white-space:nowrap;">Rata-rata Kehadiran</div>
            <div class="flex-grow-1">
                <div class="progress" style="height:12px; border-radius:6px;">
                    <div class="progress-bar bg-success" role="progressbar"
                         style="width: {{ $summary['rata_hadir'] }}%; border-radius:6px;"
                         aria-valuenow="{{ $summary['rata_hadir'] }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
            <div class="fw-bolder text-success fs-4 ms-3" style="white-space:nowrap;">{{ $summary['rata_hadir'] }}%</div>
        </div>
    </div>

    {{-- ─── Tabel Profiling Per Siswa ─── --}}
    <div class="card rounded-3 shadow-sm border-0">
        <div class="card-header border-0 pt-6 pb-4">
            <div class="card-title">
                <h4 class="fw-bolder text-dark">Detail Kehadiran Per Siswa</h4>
            </div>
            <div class="card-toolbar">
                <input type="text" id="search-siswa" class="form-control form-control-sm w-200px"
                       placeholder="Cari nama siswa...">
            </div>
        </div>
        <div class="card-body pt-0">
            @if($siswas->isEmpty())
                <div class="text-center py-10 text-muted">
                    <div class="fs-1 mb-3">📭</div>
                    <p class="fw-semibold">Tidak ada data kehadiran untuk semester ini.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-200 align-middle profiling-table" id="profiling-table">
                        <thead>
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th class="text-center">
                                    <span class="text-success">Hadir</span>
                                </th>
                                <th class="text-center">
                                    <span class="text-warning">Terlambat</span>
                                </th>
                                <th class="text-center">
                                    <span class="text-info">Sakit</span>
                                </th>
                                <th class="text-center">
                                    <span class="text-primary">Izin</span>
                                </th>
                                <th class="text-center">
                                    <span class="text-danger">Alpha</span>
                                </th>
                                <th class="text-center">% Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswas as $index => $siswa)
                            <tr>
                                <td class="ps-4 text-muted fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="symbol symbol-35px symbol-circle">
                                            <span class="symbol-label fw-bolder fs-7"
                                                  style="background: hsl({{ abs(crc32($siswa->nama)) % 360 }}, 60%, 90%);
                                                         color: hsl({{ abs(crc32($siswa->nama)) % 360 }}, 60%, 35%);">
                                                {{ strtoupper(substr($siswa->nama, 0, 2)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-bolder text-dark siswa-name">{{ $siswa->nama }}</div>
                                            <div class="text-muted fs-7">NIS: {{ $siswa->nis }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-light-info fw-semibold">
                                        {{ $siswa->kelas->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light-success fw-bold px-3">{{ $siswa->total_hadir }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light-warning fw-bold px-3">{{ $siswa->total_terlambat }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light-info fw-bold px-3">{{ $siswa->total_sakit }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light-primary fw-bold px-3">{{ $siswa->total_izin }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light-danger fw-bold px-3">{{ $siswa->total_alpha }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <div class="progress-track">
                                            <div style="width: {{ $siswa->persen_hadir }}%;
                                                height:8px; border-radius:4px;
                                                background: {{ $siswa->persen_hadir >= 80 ? '#50cd89' : ($siswa->persen_hadir >= 60 ? '#ffc700' : '#f1416c') }};">
                                            </div>
                                        </div>
                                        <span class="fw-bolder fs-7
                                            {{ $siswa->persen_hadir >= 80 ? 'text-success' : ($siswa->persen_hadir >= 60 ? 'text-warning' : 'text-danger') }}">
                                            {{ $siswa->persen_hadir }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.getElementById('search-siswa').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#profiling-table tbody tr').forEach(row => {
            const name = row.querySelector('.siswa-name')?.textContent.toLowerCase() || '';
            row.style.display = name.includes(q) ? '' : 'none';
        });
    });
</script>
@endpush
</x-base-layout>
