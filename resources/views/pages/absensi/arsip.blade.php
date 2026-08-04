<x-base-layout>
@include('pages.absensi._partials.toolbar')

@push('styles')
<style>
    .arsip-card {
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(13,43,107,0.08);
    }
    .arsip-table thead th {
        background: #f5f8ff;
        font-weight: 700;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #6c757d;
        border-bottom: 2px solid #e8eef8;
        white-space: nowrap;
    }
    .arsip-table tbody tr {
        cursor: pointer;
        transition: background 0.18s, box-shadow 0.18s;
    }
    .arsip-table tbody tr:hover {
        background: #eef4ff !important;
        box-shadow: 0 2px 10px rgba(27,132,255,0.08);
    }
    .badge-semester-ganjil { background: #e8fff3; color: #17a34a; }
    .badge-semester-genap  { background: #fff8e1; color: #d97706; }
    .badge-selesai         { background: #f1f5ff; color: #3b5bdb; }
    .stat-mini { font-size: 0.85rem; font-weight: 600; }
    .empty-state { padding: 4rem 2rem; text-align: center; color: #adb5bd; }
    .empty-state .icon { font-size: 4rem; margin-bottom: 1rem; }
    .arsip-header-card {
        background: #fff;
        border-radius: 12px;
        border-left: 5px solid #009ef7;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
</style>
@endpush


    {{-- ─── Header Banner ─── --}}
    <div class="arsip-header-card d-flex align-items-center gap-4 mb-7">
        <div class="flex-shrink-0">
            <span class="svg-icon svg-icon-3x svg-icon-primary">
                {!! theme()->getSvgIcon("icons/duotune/files/fil017.svg", "svg-icon-3x svg-icon-primary") !!}
            </span>
        </div>
        <div>
            <h2 class="text-dark fw-bolder mb-1" style="font-size:1.4rem;">Arsip Tahun Ajaran & Semester</h2>
            <p class="text-muted mb-0 fs-6">
                Riwayat data kehadiran siswa dari semester yang telah selesai. Klik baris untuk melihat profiling lengkap.
            </p>
        </div>
    </div>

    <div class="card arsip-card">
        <div class="card-header border-0 pt-6 pb-4">
            <div class="card-title">
                <h3 class="fw-bolder text-dark">Daftar Arsip Semester</h3>
            </div>
            <div class="card-toolbar">
                <span class="badge badge-light-primary fs-7 fw-semibold px-4 py-2">
                    {{ $arsipSemesters->count() }} Semester Diarsipkan
                </span>
            </div>
        </div>
        <div class="card-body pt-0">
            @if($arsipSemesters->isEmpty())
                <div class="empty-state">
                    <div class="icon">🗂️</div>
                    <h4 class="text-muted fw-semibold">Belum ada arsip semester</h4>
                    <p class="text-muted fs-6">Arsip akan muncul otomatis ketika semester dan tahun ajaran telah diselesaikan.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-200 align-middle arsip-table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Tahun Ajaran</th>
                                <th>Semester</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th class="text-center">Total Hari</th>
                                <th class="text-center">Riwayat Kehadiran</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($arsipSemesters as $index => $semester)
                            <tr onclick="window.location='{{ route('arsip.profiling', $semester->id) }}'">
                                <td class="ps-4 text-muted fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bolder text-dark fs-6">{{ $semester->tahunAjaran->nama ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2 fw-semibold
                                        {{ strtolower($semester->jenis) === 'ganjil' ? 'badge-semester-ganjil' : 'badge-semester-genap' }}">
                                        {{ ucfirst($semester->jenis) }}
                                    </span>
                                </td>
                                <td class="text-muted">
                                    {{ $semester->tanggal_mulai ? $semester->tanggal_mulai->format('d M Y') : '-' }}
                                </td>
                                <td class="text-muted">
                                    {{ $semester->tanggal_selesai ? $semester->tanggal_selesai->format('d M Y') : '-' }}
                                </td>
                                <td class="text-center">
                                    <span class="fw-bolder text-dark">{{ $semester->total_hari }}</span>
                                    <span class="text-muted ms-1 fs-7">hari</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bolder text-dark">{{ $semester->jumlah_siswa }}</span>
                                    <span class="text-muted ms-1 fs-7">siswa</span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('arsip.profiling', $semester->id) }}"
                                       class="btn btn-sm btn-light-primary fw-semibold"
                                       onclick="event.stopPropagation();">
                                        <i class="bi bi-eye me-1"></i> Lihat Arsip
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</x-base-layout>
