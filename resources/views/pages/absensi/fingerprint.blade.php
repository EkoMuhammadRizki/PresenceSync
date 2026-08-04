<x-base-layout>

@section('title', 'Manajemen Fingerprint')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}">
<style>
    .device-card { transition: all 0.2s ease; }
    .device-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.10) !important; }
    .connection-indicator { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
    .connection-indicator.connected { background: #50cd89; box-shadow: 0 0 6px rgba(80,205,137,0.7); }
    .connection-indicator.disconnected { background: #f1416c; box-shadow: 0 0 6px rgba(241,65,108,0.5); }
    .connection-indicator.testing { background: #ffc700; box-shadow: 0 0 6px rgba(255,199,0,0.7); animation: pulse 1s infinite; }
    @keyframes pulse { 0%,100%{ opacity:1; } 50%{ opacity:0.4; } }
</style>
@endpush

@include('pages.absensi._partials.toolbar', ['pageTitle' => 'Manajemen Fingerprint', 'toolbarActions' => ''])

<div class="post d-flex flex-column-fluid" id="kt_post">

    {{-- Alert Flash --}}
    @if(session('success'))
    <div class="alert alert-dismissible bg-light-success d-flex align-items-center p-5 mb-5">
        <i class="bi bi-check-circle-fill text-success fs-2 me-4"></i>
        <div class="d-flex flex-column"><span class="text-dark fw-bold">{{ session('success') }}</span></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ===================================================== --}}
    {{-- ROW 1: 6 Stats Cards --}}
    {{-- ===================================================== --}}
    <div class="row g-4 mb-6">
        {{-- Total Device --}}
        <div class="col-6 col-sm-4 col-xl-2">
            <div class="card p-4 text-center" style="border-top: 4px solid #009ef7;">
                <span class="fs-7 fw-bold text-gray-600">Total Device</span>
                <span class="fs-2x fw-boldest text-dark">{{ $stats['total_devices'] }}</span>
            </div>
        </div>
        {{-- Device Terhubung --}}
        <div class="col-6 col-sm-4 col-xl-2">
            <div class="card p-4 text-center" style="border-top: 4px solid #50cd89;">
                <span class="fs-7 fw-bold text-gray-600">Terhubung</span>
                <span class="fs-2x fw-boldest text-success">{{ $stats['active_devices'] }}</span>
            </div>
        </div>
        {{-- Total Log --}}
        <div class="col-6 col-sm-4 col-xl-2">
            <div class="card p-4 text-center" style="border-top: 4px solid #7239ea;">
                <span class="fs-7 fw-bold text-gray-600">Total Log</span>
                <span class="fs-2x fw-boldest text-dark">{{ $stats['total_logs'] }}</span>
            </div>
        </div>
        {{-- Diproses --}}
        <div class="col-6 col-sm-4 col-xl-2">
            <div class="card p-4 text-center" style="border-top: 4px solid #50cd89;">
                <span class="fs-7 fw-bold text-gray-600">Berhasil di Proses</span>
                <span class="fs-2x fw-boldest text-success">{{ $stats['processed_logs'] }}</span>
            </div>
        </div>
        {{-- Pending --}}
        <div class="col-6 col-sm-4 col-xl-2">
            <div class="card p-4 text-center" style="border-top: 4px solid #ffc700;">
                <span class="fs-7 fw-bold text-gray-600">Pending</span>
                <span class="fs-2x fw-boldest text-warning">{{ $stats['pending_logs'] }}</span>
            </div>
        </div>
        {{-- Siswa Enrolled --}}
        <div class="col-6 col-sm-4 col-xl-2">
            <div class="card p-4 text-center" style="border-top: 4px solid #009ef7;">
                <span class="fs-7 fw-bold text-gray-600">Siswa Enrolled</span>
                <span class="fs-2x fw-boldest text-primary">{{ $stats['siswa_enrolled'] }}</span>
            </div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- ROW 2: Device List + Add Device Form --}}
    {{-- ===================================================== --}}
    <div class="row g-6 mb-6">

        {{-- Daftar Device (kiri) --}}
        <div class="col-xl-8">
            <div class="card shadow-sm h-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-4 text-dark">
                            {!! theme()->getSvgIcon("icons/duotune/electronics/elc004.svg", "svg-icon-3 text-primary me-2") !!}
                            Perangkat Fingerprint
                        </span>
                        <span class="text-muted mt-1 fw-bold fs-7">Semua device fingerprint yang terdaftar di sistem</span>
                    </h3>
                    <div class="card-toolbar d-flex align-items-center gap-2">
                        <form action="{{ route('fingerprint.sync-all-templates') }}" method="POST" id="form-sync-all-templates" class="d-inline">
                            @csrf
                            <button type="button" class="btn btn-light-primary btn-sm" onclick="confirmSyncAllTemplates()" title="Salin data sidik jari siswa ke seluruh mesin">
                                <i class="bi bi-arrow-repeat me-1"></i>Sync Sidik Jari Antar Mesin
                            </button>
                        </form>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal_tambah_device">
                            <i class="bi bi-plus-lg me-1"></i>Tambah Device
                        </button>
                    </div>
                </div>
                <div class="card-body pt-2" style="max-height: 480px; overflow-y: auto;">
                    @forelse($devices as $device)
                    <div class="device-card card card-flush mb-4 p-4" id="device-card-{{ $device->id }}">
                        <div class="d-flex align-items-start justify-content-between">
                            {{-- Info Device --}}
                            <div class="d-flex align-items-center gap-3">
                                <div class="symbol symbol-45px symbol-circle bg-light-primary">
                                    {!! theme()->getSvgIcon("icons/duotune/electronics/elc004.svg", "svg-icon-2 text-primary") !!}
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="fw-bolder fs-6 text-dark">{{ $device->nama }}</span>
                                        <span class="badge {{ $device->status_badge }} fs-8" id="status-badge-{{ $device->id }}">{{ $device->status_label }}</span>
                                        <span class="connection-indicator disconnected" id="conn-indicator-{{ $device->id }}" title="Status koneksi belum dicek"></span>
                                    </div>
                                    <span class="text-muted fs-7">
                                        <i class="bi bi-hdd-network me-1"></i>{{ $device->ip_address }}:{{ $device->port }}
                                        @if($device->com_key > 0)
                                            &nbsp;·&nbsp;<i class="bi bi-key me-1"></i>Key: {{ $device->com_key }}
                                        @endif
                                        @if($device->serial_number)
                                            &nbsp;·&nbsp;<i class="bi bi-fingerprint me-1"></i>SN: {{ $device->serial_number }}
                                        @endif
                                    </span>
                                    @if($device->last_synced_at)
                                    <div class="text-muted fs-8 mt-1">
                                        <i class="bi bi-clock me-1"></i>Terakhir sync: {{ $device->last_synced_at->diffForHumans() }}
                                        &nbsp;·&nbsp;Total: {{ number_format($device->total_synced_logs) }} log
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <button class="btn btn-light-primary btn-sm py-1 px-3"
                                    onclick="testConnection({{ $device->id }}, '{{ $device->nama }}')"
                                    title="Test Koneksi" id="btn-test-{{ $device->id }}">
                                    <i class="bi bi-wifi me-1"></i>Test
                                </button>
                                <button class="btn btn-light-success btn-sm py-1 px-3"
                                    onclick="triggerSync({{ $device->id }}, '{{ $device->nama }}')"
                                    title="Sync Manual (Cadangan) — Gunakan hanya jika scan otomatis belum masuk" id="btn-sync-{{ $device->id }}">
                                    <i class="bi bi-arrow-repeat me-1"></i>Sync Manual (Cadangan)
                                </button>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm py-1 px-2" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="syncTime({{ $device->id }}, '{{ $device->nama }}')">
                                                <i class="bi bi-clock me-2"></i>Sinkronkan Waktu
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="uploadNames({{ $device->id }}, '{{ $device->nama }}')">
                                                <i class="bi bi-upload me-2"></i>Upload Nama Siswa
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                               data-bs-target="#modal_edit_device_{{ $device->id }}">
                                                <i class="bi bi-pencil me-2"></i>Edit Device
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="#"
                                               onclick="confirmDelete({{ $device->id }}, '{{ $device->nama }}')">
                                                <i class="bi bi-power me-2"></i>Nonaktifkan
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Edit Device --}}
                    <div class="modal fade" id="modal_edit_device_{{ $device->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <form class="modal-content" method="POST" action="{{ route('fingerprint.update', $device) }}">
                                @csrf @method('PUT')
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bolder">Edit Device: {{ $device->nama }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold required">Nama Lokasi Device</label>
                                        <input type="text" name="nama" class="form-control form-control-solid"
                                               value="{{ $device->nama }}" required>
                                    </div>
                                    <div class="row g-3 mb-4">
                                        <div class="col-8">
                                            <label class="form-label fw-bold required">IP Address</label>
                                            <input type="text" name="ip_address" class="form-control form-control-solid"
                                                   value="{{ $device->ip_address }}" placeholder="192.168.1.201" required>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label fw-bold required">Port</label>
                                            <input type="number" name="port" class="form-control form-control-solid"
                                                   value="{{ $device->port }}" min="1" max="65535" required>
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-4">
                                        <div class="col-6">
                                            <label class="form-label fw-bold">Comm Key</label>
                                            <input type="number" name="com_key" class="form-control form-control-solid"
                                                   value="{{ $device->com_key }}" min="0" placeholder="0">
                                            <span class="text-muted fs-8">Isi 0 jika tidak ada password</span>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label fw-bold">Serial Number</label>
                                            <input type="text" name="serial_number" class="form-control form-control-solid"
                                                   value="{{ $device->serial_number }}" placeholder="Opsional">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-10">
                        {!! theme()->getSvgIcon("icons/duotune/electronics/elc004.svg", "svg-icon-5x text-muted mb-4") !!}
                        <p class="text-muted fw-bold">Belum ada device fingerprint terdaftar.</p>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal_tambah_device">
                            <i class="bi bi-plus-lg me-1"></i>Tambah Device Pertama
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Form Tambah Device (kanan) --}}
        <div class="col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title">
                        <span class="card-label fw-bolder fs-5 text-dark">Panduan Cepat</span>
                    </h3>
                </div>
                <div class="card-body pt-2">
                    <div class="alert alert-light-primary d-flex align-items-start p-4 mb-4">
                        <i class="bi bi-info-circle-fill text-primary fs-4 me-3 mt-1"></i>
                        <div class="fs-7 text-gray-700">
                            <strong>Protokol SDK:</strong> Device X100-C menggunakan <strong>HTTP SOAP port 80</strong>
                            (endpoint <code>/iWsService</code>). Pastikan device dan server berada di <strong>subnet LAN yang sama</strong>.
                        </div>
                    </div>

                    <ol class="list-group list-group-numbered mb-4">
                        <li class="list-group-item border-0 ps-0">
                            <span class="fw-bold text-dark fs-7">Set IP statis di device</span>
                            <p class="text-muted fs-8 mb-0">Menu → Option → Comm Opt → IP Address</p>
                        </li>
                        <li class="list-group-item border-0 ps-0">
                            <span class="fw-bold text-dark fs-7">Tambah device di sini</span>
                            <p class="text-muted fs-8 mb-0">Masukkan IP yang sama dengan yang di-set di device</p>
                        </li>
                        <li class="list-group-item border-0 ps-0">
                            <span class="fw-bold text-dark fs-7">Test koneksi</span>
                            <p class="text-muted fs-8 mb-0">Klik tombol "Test" untuk verifikasi device bisa diakses</p>
                        </li>
                        <li class="list-group-item border-0 ps-0">
                            <span class="fw-bold text-dark fs-7">Auto-Sync Otomatis (Fitur Utama)</span>
                            <p class="text-muted fs-8 mb-0">Sistem secara otomatis mendeteksi dan mengimpor log scan jari ke database real-time. Tombol <b>Sync Manual (Cadangan)</b> digunakan sebagai fitur kedua jika scan otomatis belum masuk.</p>
                        </li>
                    </ol>

                    <div class="alert alert-light-warning d-flex align-items-start p-4">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-5 me-3 mt-1"></i>
                        <div class="fs-8 text-gray-700">
                            Pastikan kolom <strong>fingerprint_id</strong> siswa sudah diisi dengan <strong>User ID di device</strong>
                            agar data absensi bisa dipetakan dengan benar.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /row 2 --}}

    {{-- ===================================================== --}}
    {{-- ROW 3: Mapping Siswa Fingerprint (Table format with Search & Filter) --}}
    {{-- ===================================================== --}}
    <div class="row g-6 mb-6">
        {{-- Siswa sudah enrolled --}}
        <div class="col-xl-6">
            <div class="card shadow-sm h-100">
                <div class="card-header border-0 pt-5 pb-3">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-5 text-dark">
                            <i class="bi bi-fingerprint text-success me-2"></i>
                            Siswa Sudah Enrolled ({{ $siswaWithFingerprint->count() }})
                        </span>
                    </h3>
                    <div class="card-toolbar">
                        <div class="d-flex align-items-center gap-2 flex-nowrap">
                            <div class="position-relative">
                                <input type="text" id="search_enrolled" class="form-control form-control-solid form-control-sm ps-8 w-120px w-sm-140px" placeholder="Cari nama..." />
                                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-gray-500 fs-8"></i>
                            </div>
                            <select id="filter_kelas_enrolled" class="form-select form-select-solid form-select-sm w-110px">
                                <option value="">Semua Kelas</option>
                                @foreach($kelases as $k)
                                    <option value="{{ $k->nama }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0" style="max-height: 420px; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-row-dashed fs-7 gy-2 mb-0" id="table_enrolled">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-8 text-uppercase gs-0 bg-light">
                                    <th class="ps-3">Nama</th>
                                    <th>Kelas</th>
                                    <th>NIS</th>
                                    <th class="text-center">ID Fingerprint</th>
                                    <th class="pe-3 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 fw-bold">
                                @forelse($siswaWithFingerprint as $siswa)
                                <tr data-nama="{{ strtolower($siswa->nama) }}" data-kelas="{{ strtolower($siswa->kelas?->nama ?? '') }}">
                                    <td class="ps-3 text-dark fw-bolder">{{ $siswa->nama }}</td>
                                    <td>
                                        <span class="badge badge-light-info fw-semibold">{{ $siswa->kelas?->nama ?? '-' }}</span>
                                    </td>
                                    <td class="text-muted">{{ $siswa->nis ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-light-primary fw-bolder fs-8">ID: {{ $siswa->fingerprint_id }}</span>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <form action="{{ route('fingerprint.toggle-enrollment', $siswa) }}" method="POST" class="d-inline" onsubmit="return confirm('Ubah status {{ $siswa->nama }} menjadi Belum Enrolled?')">
                                            @csrf
                                            <button type="submit" class="btn btn-light-danger btn-xs py-1 px-2 fs-8" title="Tandai Belum Enrolled">
                                                <i class="bi bi-x-circle me-1 fs-9"></i>Batal Enrolled
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Belum ada siswa yang enrolled.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Siswa belum enrolled --}}
        <div class="col-xl-6">
            <div class="card shadow-sm h-100">
                <div class="card-header border-0 pt-5 pb-3">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-5 text-dark">
                            <i class="bi bi-person-x text-warning me-2"></i>
                            Siswa Belum Enrolled ({{ $siswaTanpaFingerprint->count() }})
                        </span>
                    </h3>
                    <div class="card-toolbar">
                        <div class="d-flex align-items-center gap-2 flex-nowrap">
                            <div class="position-relative">
                                <input type="text" id="search_unenrolled" class="form-control form-control-solid form-control-sm ps-8 w-120px w-sm-140px" placeholder="Cari nama..." />
                                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-gray-500 fs-8"></i>
                            </div>
                            <select id="filter_kelas_unenrolled" class="form-select form-select-solid form-select-sm w-110px">
                                <option value="">Semua Kelas</option>
                                @foreach($kelases as $k)
                                    <option value="{{ $k->nama }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0" style="max-height: 420px; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-row-dashed fs-7 gy-2 mb-0" id="table_unenrolled">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-8 text-uppercase gs-0 bg-light">
                                    <th class="ps-3">Nama</th>
                                    <th>Kelas</th>
                                    <th>NIS</th>
                                    <th class="text-center">ID Hardware</th>
                                    <th class="pe-3 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 fw-bold">
                                @forelse($siswaTanpaFingerprint as $siswa)
                                <tr data-nama="{{ strtolower($siswa->nama) }}" data-kelas="{{ strtolower($siswa->kelas?->nama ?? '') }}">
                                    <td class="ps-3 text-dark fw-bolder">{{ $siswa->nama }}</td>
                                    <td>
                                        <span class="badge badge-light-info fw-semibold">{{ $siswa->kelas?->nama ?? '-' }}</span>
                                    </td>
                                    <td class="text-muted">{{ $siswa->nis ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-light-secondary fw-bold fs-8">ID: {{ $siswa->fingerprint_id }}</span>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <form action="{{ route('fingerprint.toggle-enrollment', $siswa) }}" method="POST" class="d-inline" onsubmit="return confirm('Tandai {{ $siswa->nama }} sudah enrolled sidik jari?')">
                                            @csrf
                                            <button type="submit" class="btn btn-light-success btn-xs py-1 px-2 fs-8" title="Tandai Sudah Scan Sidik Jari / Enrolled">
                                                <i class="bi bi-check-circle me-1 fs-9"></i>Tandai Enrolled
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Semua siswa sudah enrolled. 🎉</td>
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

{{-- ===================================================== --}}
{{-- MODAL: Tambah Device --}}
{{-- ===================================================== --}}
<div class="modal fade" id="modal_tambah_device" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('fingerprint.store') }}">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bolder fs-4">Tambah Perangkat Fingerprint</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <label class="form-label fw-bold required">Nama Lokasi Device</label>
                    <input type="text" name="nama" class="form-control form-control-solid"
                           placeholder="contoh: Gerbang Utama Sekolah" required>
                    <span class="text-muted fs-8">Nama ini untuk identifikasi lokasi device di sistem</span>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-8">
                        <label class="form-label fw-bold required">IP Address Device</label>
                        <input type="text" name="ip_address" class="form-control form-control-solid"
                               placeholder="192.168.1.201" required>
                        <span class="text-muted fs-8">IP statis yang sudah diset di menu device</span>
                    </div>
                    <div class="col-4">
                        <label class="form-label fw-bold required">Port HTTP</label>
                        <input type="number" name="port" class="form-control form-control-solid"
                               value="80" min="1" max="65535" required>
                        <span class="text-muted fs-8">Default: 80</span>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <label class="form-label fw-bold">Comm Key</label>
                        <input type="number" name="com_key" class="form-control form-control-solid"
                               value="0" min="0" placeholder="0">
                        <span class="text-muted fs-8">Password koneksi device (0 = tidak ada)</span>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold">Serial Number</label>
                        <input type="text" name="serial_number" class="form-control form-control-solid"
                               placeholder="Opsional (lihat menu device)">
                    </div>
                </div>
                <div class="alert alert-light-primary d-flex align-items-center p-3">
                    <i class="bi bi-info-circle-fill text-primary me-3"></i>
                    <span class="fs-8 text-gray-700">
                        IP default pabrik X100-C adalah <strong>192.168.1.201</strong>, port <strong>80</strong>.
                        Ubah sesuai subnet LAN sekolah Anda jika diperlukan.
                    </span>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Device
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Hidden forms untuk DELETE --}}
@foreach($devices as $device)
<form id="form_delete_{{ $device->id }}" method="POST" action="{{ route('fingerprint.destroy', $device) }}" class="d-none">
    @csrf @method('DELETE')
</form>
@endforeach

@push('scripts')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
var CSRF_TOKEN = '{{ csrf_token() }}';

// ============================================================
// Sync Sidik Jari Antar Mesin Konfirmasi SweetAlert2
// ============================================================
function confirmSyncAllTemplates() {
    Swal.fire({
        title: 'Sync Sidik Jari Antar Mesin?',
        html: 'Salin semua data nama & sidik jari siswa ke seluruh mesin (Gerbang 1 & Gerbang 2)?<br><small class="text-muted">Proses ini akan memastikan seluruh mesin memiliki data sidik jari siswa yang identik.</small>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-arrow-repeat me-1"></i>Ya, Sync Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#009ef7'
    }).then(function(result) {
        if (result.isConfirmed) {
            document.getElementById('form-sync-all-templates').submit();
        }
    });
}

// ============================================================
// Test Koneksi AJAX
// ============================================================
function testConnection(deviceId, deviceName) {
    var indicator = $('#conn-indicator-' + deviceId);
    var badge = $('#status-badge-' + deviceId);
    var btn = $('#btn-test-' + deviceId);

    indicator.removeClass('connected disconnected').addClass('testing');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Testing...');

    $.ajax({
        url: '/absensi/fingerprint/' + deviceId + '/test-connection',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
        success: function(res) {
            if (res.connected) {
                indicator.removeClass('testing').addClass('connected');
                indicator.attr('title', 'Terhubung (' + res.latency_ms + 'ms)');
                badge.removeClass('badge-light-danger badge-light-secondary').addClass('badge-light-success').text('Terhubung');
                Swal.fire({
                    icon: 'success',
                    title: 'Terhubung!',
                    html: '<b>' + deviceName + '</b><br>Latensi: <b>' + res.latency_ms + ' ms</b>',
                    timer: 2500,
                    showConfirmButton: false
                });
            } else {
                indicator.removeClass('testing').addClass('disconnected');
                badge.removeClass('badge-light-success badge-light-secondary').addClass('badge-light-danger').text('Tidak terhubung');
                Swal.fire({ icon: 'error', title: 'Gagal Koneksi', text: res.error });
            }
        },
        error: function(xhr) {
            indicator.removeClass('testing').addClass('disconnected');
            badge.removeClass('badge-light-success badge-light-secondary').addClass('badge-light-danger').text('Tidak terhubung');
            Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="bi bi-wifi me-1"></i>Test');
        }
    });
}

// ============================================================
// Trigger Sync AJAX
// ============================================================
function triggerSync(deviceId, deviceName) {
    Swal.fire({
        title: 'Sync Device',
        html: 'Tarik log absensi dari <b>' + deviceName + '</b>?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-arrow-repeat me-1"></i>Sync Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#009ef7',
        showLoaderOnConfirm: true,
        preConfirm: function() {
            return fetch('/absensi/fingerprint/' + deviceId + '/sync', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            }).then(function(r) {
                return r.json();
            }).catch(function() {
                Swal.showValidationMessage('Gagal menghubungi server');
            });
        }
    }).then(function(result) {
        if (result.isConfirmed && result.value) {
            var data = result.value;
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sync Selesai!',
                    html: data.message +
                          '<br><br><small class="text-muted">Halaman akan direfresh...</small>',
                    timer: 3000,
                    showConfirmButton: false
                }).then(function() { location.reload(); });
            } else {
                Swal.fire({ icon: 'error', title: 'Sync Gagal', text: data.message });
            }
        }
    });
}

// ============================================================
// Sync Time AJAX
// ============================================================
function syncTime(deviceId, deviceName) {
    $.ajax({
        url: '/absensi/fingerprint/' + deviceId + '/sync-time',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
        success: function(res) {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Waktu Disinkronkan!',
                    text: 'Jam device ' + deviceName + ' berhasil disinkronkan dengan server.', timer: 2500, showConfirmButton: false });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: res.error });
            }
        },
        error: function() {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
        }
    });
}

// ============================================================
// Upload Nama Siswa ke Device AJAX
// ============================================================
function uploadNames(deviceId, deviceName) {
    Swal.fire({
        title: 'Upload Nama Siswa?',
        html: 'Upload nama semua siswa yang sudah enrolled ke device <b>' + deviceName + '</b>.<br><small class="text-muted">Ini membantu menampilkan nama siswa di layar device saat absen.</small>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-upload me-1"></i>Upload',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#50cd89',
        showLoaderOnConfirm: true,
        preConfirm: function() {
            return fetch('/absensi/fingerprint/' + deviceId + '/upload-names', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
            }).then(r => r.json()).catch(() => Swal.showValidationMessage('Error koneksi'));
        }
    }).then(function(result) {
        if (result.isConfirmed && result.value) {
            var d = result.value;
            Swal.fire({ icon: d.success ? 'success' : 'error', title: d.success ? 'Upload Selesai!' : 'Gagal', text: d.message });
        }
    });
}

// ============================================================
// Konfirmasi Hapus/Nonaktifkan Device
// ============================================================
function confirmDelete(deviceId, deviceName) {
    Swal.fire({
        title: 'Nonaktifkan Device?',
        html: 'Device <b>' + deviceName + '</b> akan dinonaktifkan.<br>Data yang sudah ada tidak akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Nonaktifkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#f1416c'
    }).then(function(result) {
        if (result.isConfirmed) {
            document.getElementById('form_delete_' + deviceId).submit();
        }
    });
}

// ============================================================
// Real-time Search Nama & Filter Kelas Tabel Siswa
// ============================================================
$(document).ready(function() {
    function filterSiswaTable(searchInputId, filterKelasId, tableId) {
        var searchVal = $(searchInputId).val().toLowerCase().trim();
        var kelasVal = $(filterKelasId).val().toLowerCase().trim();

        $(tableId + ' tbody tr').each(function() {
            var rowNama = $(this).attr('data-nama') || '';
            var rowKelas = $(this).attr('data-kelas') || '';

            var matchNama = !searchVal || rowNama.includes(searchVal);
            var matchKelas = !kelasVal || rowKelas === kelasVal;

            if (matchNama && matchKelas) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    $('#search_enrolled, #filter_kelas_enrolled').on('input change', function() {
        filterSiswaTable('#search_enrolled', '#filter_kelas_enrolled', '#table_enrolled');
    });

    $('#search_unenrolled, #filter_kelas_unenrolled').on('input change', function() {
        filterSiswaTable('#search_unenrolled', '#filter_kelas_unenrolled', '#table_unenrolled');
    });
});
</script>
@endpush

</x-base-layout>
