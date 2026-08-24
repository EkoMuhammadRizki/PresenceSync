<x-base-layout>

@section('title', 'Log Scan Fingerprint')

@push('styles')
<link href="{{ asset(theme()->getDemo() . '/plugins/custom/flatpickr/flatpickr.bundle.css') }}" rel="stylesheet" type="text/css"/>
<style>
    .log-card { transition: all 0.2s ease; }
    .log-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.10) !important; }
    .auto-refresh-indicator { width: 10px; height: 10px; border-radius: 50%; display: inline-block; background: #50cd89; box-shadow: 0 0 6px rgba(80,205,137,0.7); animation: pulse 1.5s infinite; }
    @keyframes pulse { 0%,100%{ opacity:1; } 50%{ opacity:0.3; } }
</style>
@endpush

@include('pages.absensi._partials.toolbar', ['pageTitle' => 'Log Scan Fingerprint', 'toolbarActions' => ''])

{{-- Alert Flash --}}
@if(session('success'))
    <div class="alert alert-dismissible bg-light-success d-flex align-items-center p-5 mb-5">
        <i class="bi bi-check-circle-fill text-success fs-2 me-4"></i>
        <div class="d-flex flex-column"><span class="text-dark fw-bold">{{ session('success') }}</span></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ===================================================== --}}
    {{-- STATS CARDS --}}
    {{-- ===================================================== --}}
    <div class="row g-4 mb-6">
        <div class="col-6 col-sm-3">
            <div class="card p-4 text-center log-card" style="border-top: 4px solid #009ef7;">
                <span class="fs-7 fw-bold text-gray-600">Total Log Scan</span>
                <span class="fs-2x fw-boldest text-dark">{{ number_format($stats['total_logs']) }}</span>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card p-4 text-center log-card" style="border-top: 4px solid #50cd89;">
                <span class="fs-7 fw-bold text-gray-600">Log Hari Ini</span>
                <span class="fs-2x fw-boldest text-success">{{ number_format($stats['today_logs']) }}</span>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card p-4 text-center log-card" style="border-top: 4px solid #7239ea;">
                <span class="fs-7 fw-bold text-gray-600">Berhasil Diproses</span>
                <span class="fs-2x fw-boldest text-primary">{{ number_format($stats['processed_logs']) }}</span>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card p-4 text-center log-card" style="border-top: 4px solid #ffc700;">
                <span class="fs-7 fw-bold text-gray-600">Pending / Belum Sync</span>
                <span class="fs-2x fw-boldest text-warning">{{ number_format($stats['pending_logs']) }}</span>
            </div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- MAIN TABLE CARD --}}
    {{-- ===================================================== --}}
    {{-- MAIN TABLE CARD --}}
    {{-- ===================================================== --}}
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6 pb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap w-100 gap-3">
                <form action="{{ route('fingerprint.logs-view') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                    {{-- Filter Device --}}
                    <div style="width: 170px;">
                        <select name="device_id" class="form-select form-select-solid form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Perangkat</option>
                            @foreach($devices as $dev)
                                <option value="{{ $dev->id }}" {{ $deviceId == $dev->id ? 'selected' : '' }}>
                                    {{ $dev->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Kelas --}}
                    <div style="width: 130px;">
                        <select name="kelas_id" class="form-select form-select-solid form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Kelas</option>
                            @foreach($kelases as $kls)
                                <option value="{{ $kls->id }}" {{ $kelasId == $kls->id ? 'selected' : '' }}>
                                    {{ $kls->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Rentang Tanggal --}}
                    <div class="d-flex align-items-center position-relative">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-1 position-absolute ms-3") !!}
                        <input type="text" name="date_range" id="filter_date_range" value="{{ $dateRange }}" class="form-control form-control-solid form-control-sm w-210px ps-10" placeholder="Pilih Rentang Tanggal" readonly="readonly" />
                    </div>

                    {{-- Search Input (Nama / User ID) --}}
                    <div class="d-flex align-items-center position-relative">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-3") !!}
                        <input type="text" name="search" value="{{ $search }}" class="form-control form-control-solid form-control-sm w-170px ps-10" placeholder="Cari Nama / ID..." />
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    @if($search || $deviceId || $kelasId || $dateRange)
                        <a href="{{ route('fingerprint.logs-view') }}" class="btn btn-light btn-sm">Reset</a>
                    @endif
                </form>

                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fs-7 d-flex align-items-center gap-2 me-2">
                        <span class="auto-refresh-indicator"></span> Auto Deteksi Log Tap
                    </span>
                    
                    @if($logs->count() > 0)
                    <button type="button" class="btn btn-light-danger btn-sm me-2" onclick="confirmClearAllLogs()">
                        <i class="bi bi-trash me-1"></i>Hapus Semua Log
                    </button>
                    <form id="form_clear_all_logs" action="{{ route('fingerprint.logs.clear') }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="device_id" value="{{ $deviceId }}">
                    </form>
                    @endif

                    <button type="button" class="btn btn-light-primary btn-sm" onclick="location.reload()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body py-4">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="table_fingerprint_logs">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="w-50px">#</th>
                            <th class="min-w-150px">Siswa (Pemilik ID)</th>
                            <th class="min-w-100px">Kelas</th>
                            <th class="min-w-100px">User ID / PIN</th>
                            <th class="min-w-150px">Perangkat Mesin</th>
                            <th class="min-w-160px">Waktu Scan Jari</th>
                            <th class="min-w-100px">Verifikasi</th>
                            <th class="min-w-120px">Status Sistem</th>
                            <th class="text-end min-w-80px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @forelse($logs as $index => $log)
                            @php
                                $siswa = $siswasMap->get($log->fingerprint_uid) ?? $log->siswa();
                            @endphp
                            <tr>
                                <td>{{ $logs->firstItem() + $index }}</td>
                                <td>
                                    @if($siswa)
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-circle symbol-35px me-3 bg-light-primary">
                                                <span class="symbol-label text-primary fw-bolder">{{ strtoupper(substr($siswa->nama, 0, 1)) }}</span>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="text-dark fw-bolder text-hover-primary fs-6">{{ $siswa->nama }}</span>
                                                <span class="text-muted fs-7">NIS: {{ $siswa->nis ?? '-' }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge badge-light-secondary text-gray-600 fw-bold fs-7">User ID Unmapped</span>
                                    @endif
                                </td>
                                <td>
                                    @if($siswa && $siswa->kelas)
                                        <span class="badge badge-light-info fw-semibold">{{ $siswa->kelas->nama }}</span>
                                    @else
                                        <span class="text-muted fs-7">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-light-primary fw-boldest fs-6 px-3 py-2">
                                        ID: {{ $log->fingerprint_uid }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-bold fs-7">{{ $log->device->nama ?? 'Mesin Fingerprint' }}</span>
                                        <span class="text-muted fs-8">{{ $log->device->ip_address ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-clock-history text-primary me-2 fs-6"></i>
                                        <span class="text-dark fw-bold fs-7">
                                            {{ $log->scan_time ? $log->scan_time->translatedFormat('d M Y, H:i:s') : '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    @if($log->verified == 1)
                                        <span class="badge badge-light-success"><i class="bi bi-fingerprint me-1 text-success"></i> Sidik Jari</span>
                                    @else
                                        <span class="badge badge-light-info"><i class="bi bi-key me-1 text-info"></i> Password/PIN</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->is_processed)
                                        <div class="d-flex flex-column align-items-start">
                                            <span class="badge badge-light-success fw-bolder px-3 py-2 mb-1">
                                                <i class="bi bi-check-all text-success me-1"></i> Berhasil Diproses
                                            </span>
                                            @if($log->kehadiran)
                                                @php
                                                    $scanTimeHims = \Carbon\Carbon::parse($log->scan_time)->format('H:i:s');
                                                @endphp
                                                @if($scanTimeHims == $log->kehadiran->jam_masuk)
                                                    <span class="badge badge-light-primary fw-bolder px-3 py-2">
                                                        <i class="bi bi-box-arrow-in-right text-primary me-1"></i> Absen Masuk
                                                    </span>
                                                @elseif($scanTimeHims == $log->kehadiran->jam_pulang)
                                                    <span class="badge badge-light-info fw-bolder px-3 py-2">
                                                        <i class="bi bi-box-arrow-right text-info me-1"></i> Absen Pulang
                                                    </span>
                                                @else
                                                    <span class="badge badge-light-secondary text-muted fw-bolder px-3 py-2">
                                                        <i class="bi bi-clock-history text-muted me-1"></i> Scan Berulang
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge badge-light-warning fw-bolder px-3 py-2">
                                            <i class="bi bi-hourglass-split text-warning me-1"></i> Pending
                                        </span>
                                    @endif

                                    @if($log->error_note)
                                        <div class="text-danger fs-8 mt-1" title="{{ $log->error_note }}">
                                            <i class="bi bi-exclamation-triangle text-danger me-1"></i> {{ Str::limit($log->error_note, 25) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-icon btn-light-danger btn-sm"
                                        onclick="confirmDeleteLog({{ $log->id }}, '{{ $log->fingerprint_uid }}')"
                                        title="Hapus Log Ini">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form id="form_delete_log_{{ $log->id }}" action="{{ route('fingerprint.logs.destroy', $log->id) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-10">
                                    <div class="d-flex flex-column align-items-center">
                                        {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg", "svg-icon-3x svg-icon-muted mb-3") !!}
                                        <span class="text-gray-600 fw-bold fs-5 mb-1">Belum ada Log Scan Fingerprint</span>
                                        <span class="text-muted fs-7">Setiap kali siswa menempelkan sidik jari di mesin, log scan akan otomatis masuk ke tabel ini.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex flex-stack flex-wrap pt-5">
                <div class="fs-6 fw-bold text-gray-700">
                    Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari total {{ $logs->total() }} log
                </div>
                <div class="d-flex align-items-center">
                    {{ $logs->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script src="{{ asset(theme()->getDemo() . '/plugins/custom/flatpickr/flatpickr.bundle.js') }}"></script>
<script>
    var indonesianLocale = {
        firstDayOfWeek: 1,
        weekdays: {
            shorthand: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
            longhand: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"]
        },
        months: {
            shorthand: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
            longhand: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"]
        }
    };

    // Inisialisasi Flatpickr Rentang Tanggal
    $("#filter_date_range").flatpickr({
        mode: "range",
        dateFormat: "Y-m-d",
        locale: indonesianLocale,
        allowInput: true,
        onClose: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2 || selectedDates.length === 1) {
                instance.element.closest('form').submit();
            }
        }
    });

    // Auto Polling real-time setiap 3 detik untuk mendeteksi scan jari baru
    setInterval(function() {
        $.ajax({
            url: "{{ route('fingerprint.auto-sync') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}" },
            success: function(res) {
                if (res.success && (res.new_logs > 0 || res.processed > 0)) {
                    console.log("Scan jari baru terdeteksi! Reloading halaman...");
                    location.reload();
                }
            }
        });
    }, 3000);

    // Konfirmasi Hapus Single Log
    function confirmDeleteLog(logId, uid) {
        Swal.fire({
            title: 'Hapus Log Scan ini?',
            html: 'Log scan untuk User ID <b>' + uid + '</b> akan dihapus dari sistem.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#f1416c'
        }).then(function(result) {
            if (result.isConfirmed) {
                document.getElementById('form_delete_log_' + logId).submit();
            }
        });
    }

    // Konfirmasi Hapus Semua Log
    function confirmClearAllLogs() {
        Swal.fire({
            title: 'Hapus Semua Log Scan?',
            html: 'Seluruh riwayat log scan fingerprint yang ada di tabel ini akan dibersihkan secara permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus Semua',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#f1416c'
        }).then(function(result) {
            if (result.isConfirmed) {
                document.getElementById('form_clear_all_logs').submit();
            }
        });
    }
</script>
@endpush

</x-base-layout>
