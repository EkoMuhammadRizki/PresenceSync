<x-base-layout>
@include('pages.absensi._partials.toolbar')

@if(session('success'))
    <div class="alert alert-success d-flex align-items-center p-5 mb-10">
        <span class="svg-icon svg-icon-2hx svg-icon-success me-4">
            {!! theme()->getSvgIcon("icons/duotune/general/gen048.svg") !!}
        </span>
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-dark">Sukses</h4>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
        <span class="svg-icon svg-icon-2hx svg-icon-danger me-4">
            {!! theme()->getSvgIcon("icons/duotune/general/gen040.svg") !!}
        </span>
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-dark">Error</h4>
            <span>{{ session('error') }}</span>
        </div>
    </div>
@endif

@if(session('import_success'))
    <div class="alert bg-light-primary border border-primary alert-dismissible fade show p-5 mb-10 position-relative">
        <div class="d-flex align-items-center mb-3 pe-8">
            <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
                {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg") !!}
            </span>
            <div class="d-flex flex-column flex-grow-1">
                <h4 class="mb-1 text-primary">Hasil Import Data Siswa</h4>
                <span class="text-gray-700 fs-6">
                    Berhasil diproses/diimport: <strong class="text-success">{{ session('import_success')['success_count'] }}</strong> siswa
                    @if(!empty(session('import_success')['skip_count']) && session('import_success')['skip_count'] > 0)
                        | Dilewati: <strong class="text-warning">{{ session('import_success')['skip_count'] }}</strong> baris
                    @endif
                </span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

        @if(!empty(session('import_success')['imported_names']))
            <div class="mt-4 pt-3 border-top border-primary border-opacity-25">
                <a class="btn btn-sm btn-light-success fw-bolder mb-2" data-bs-toggle="collapse" href="#collapseSiswaImported" role="button" aria-expanded="false">
                    <i class="bi bi-check-circle me-1"></i> Lihat Daftar Siswa yang Berhasil Diimport ({{ session('import_success')['success_count'] }} Siswa)
                </a>
                <div class="collapse show" id="collapseSiswaImported">
                    <div class="card card-body bg-white border border-success border-opacity-25 py-3 px-4 mt-2" style="max-height: 200px; overflow-y: auto;">
                        <ul class="list-unstyled mb-0">
                            @foreach(session('import_success')['imported_names'] as $idx => $s)
                                <li class="py-1 border-bottom border-gray-200 d-flex justify-content-between align-items-center fs-7">
                                    <span class="text-gray-800 fw-bold">{{ $idx + 1 }}. {{ $s['nama'] }}</span>
                                    <div>
                                        <span class="badge badge-light-primary me-1">NIS: {{ $s['nis'] }}</span>
                                        @if(!empty($s['kelas']) && $s['kelas'] !== '-')
                                            <span class="badge badge-light-info">Kelas: {{ $s['kelas'] }}</span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        @if(session('import_success')['success_count'] > count(session('import_success')['imported_names']))
                            <div class="text-center text-muted fs-8 pt-2">
                                ... dan <strong>{{ session('import_success')['success_count'] - count(session('import_success')['imported_names']) }}</strong> siswa lainnya berhasil diimport.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if(!empty(session('import_success')['skipped_names']))
            <div class="mt-3">
                <a class="btn btn-sm btn-light-warning fw-bolder mb-2" data-bs-toggle="collapse" href="#collapseSiswaSkipped" role="button" aria-expanded="false">
                    <i class="bi bi-exclamation-triangle me-1"></i> Lihat {{ count(session('import_success')['skipped_names']) }} Data yang Dilewati
                </a>
                <div class="collapse" id="collapseSiswaSkipped">
                    <div class="card card-body bg-white border border-warning border-opacity-25 py-3 px-4 mt-2" style="max-height: 180px; overflow-y: auto;">
                        <ul class="list-unstyled mb-0">
                            @foreach(session('import_success')['skipped_names'] as $idx => $s)
                                <li class="py-1 border-bottom border-gray-200 d-flex justify-content-between align-items-center fs-7">
                                    <span class="text-gray-800">{{ $idx + 1 }}. {{ $s['nama'] }} (NIS: {{ $s['nis'] }})</span>
                                    <span class="badge badge-light-danger">{{ $s['alasan'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif

@if(session('delete_success'))
    <div class="alert bg-light-danger border border-danger alert-dismissible fade show p-5 mb-10 position-relative">
        <div class="d-flex align-items-center mb-3 pe-8">
            <span class="svg-icon svg-icon-2hx svg-icon-danger me-4">
                {!! theme()->getSvgIcon("icons/duotune/general/gen040.svg") !!}
            </span>
            <div class="d-flex flex-column flex-grow-1">
                <h4 class="mb-1 text-danger">Hasil Penghapusan Data Siswa</h4>
                <span class="text-gray-700 fs-6">
                    Sebanyak <strong class="text-danger">{{ session('delete_success')['count'] }} data siswa</strong> telah berhasil dihapus secara permanen dari database.
                </span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

        @if(!empty(session('delete_success')['items']))
            <div class="mt-4 pt-3 border-top border-danger border-opacity-25">
                <a class="btn btn-sm btn-light-danger fw-bolder mb-2" data-bs-toggle="collapse" href="#collapseSiswaDeleted" role="button" aria-expanded="false">
                    <i class="bi bi-trash me-1"></i> Lihat Daftar Siswa yang Dihapus ({{ session('delete_success')['count'] }} Siswa)
                </a>
                <div class="collapse show" id="collapseSiswaDeleted">
                    <div class="card card-body bg-white border border-danger border-opacity-25 py-3 px-4 mt-2" style="max-height: 200px; overflow-y: auto;">
                        <ul class="list-unstyled mb-0">
                            @foreach(session('delete_success')['items'] as $idx => $s)
                                <li class="py-1 border-bottom border-gray-200 d-flex justify-content-between align-items-center fs-7">
                                    <span class="text-gray-800 fw-bold">{{ $idx + 1 }}. {{ $s['nama'] }}</span>
                                    <span class="badge badge-light-danger">{{ $s['code'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                        @if(session('delete_success')['count'] > count(session('delete_success')['items']))
                            <div class="text-center text-muted fs-8 pt-2">
                                ... dan <strong>{{ session('delete_success')['count'] - count(session('delete_success')['items']) }}</strong> siswa lainnya telah dihapus.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger p-5 mb-10">
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-dark">Validasi Gagal</h4>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="card mt-2">
    <div class="card-header border-0 pt-6 flex-column flex-md-row gap-3">
        <div class="card-title my-0">
            <div class="d-flex align-items-center position-relative my-1 w-100 w-md-250px">
                {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                <input type="text" id="search_siswa" class="form-control form-control-solid ps-14 w-100" placeholder="Cari siswa..." />
            </div>
        </div>
        <div class="card-toolbar my-0">
            <div class="d-flex flex-wrap gap-2">
                <form id="form_post_ke_mesin" action="{{ route('siswa.push-to-devices') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="selected_ids" id="post_selected_ids" value="" />
                    <button type="button" onclick="confirmPostKeMesin()" class="btn btn-light-info btn-sm btn-md-md position-relative" title="POST & Sinkronisasi data nama & ID siswa ke mesin Solution X100-C">
                        {!! theme()->getSvgIcon("icons/duotune/arrows/arr078.svg", "svg-icon-2") !!}
                        <span class="d-none d-sm-inline" id="btn_post_text">Post ke Mesin</span>
                        <span class="d-inline d-sm-none">Post</span>
                        @if(isset($unpushedCount) && $unpushedCount > 0)
                            <span class="badge badge-circle badge-warning ms-1" id="badge_unpushed" title="{{ $unpushedCount }} siswa belum/perlu di-post ke mesin">{{ $unpushedCount }}</span>
                        @endif
                    </button>
                </form>

                <a href="{{ route('siswa.download-template', ['empty' => 1]) }}" class="btn btn-light-warning btn-sm btn-md-md">
                    {!! theme()->getSvgIcon("icons/duotune/files/fil021.svg", "svg-icon-2") !!}
                    <span class="d-none d-sm-inline">Download Template</span>
                    <span class="d-inline d-sm-none">Template</span>
                </a>
                <a href="{{ route('siswa.download-template') }}" class="btn btn-light-success btn-sm btn-md-md">
                    {!! theme()->getSvgIcon("icons/duotune/files/fil021.svg", "svg-icon-2") !!}
                    <span class="d-none d-sm-inline">Ekspor Data Siswa</span>
                    <span class="d-inline d-sm-none">Ekspor</span>
                </a>
                <button type="button" class="btn btn-light-primary btn-sm btn-md-md" data-bs-toggle="modal" data-bs-target="#modal_import_siswa">
                    {!! theme()->getSvgIcon("icons/duotune/files/fil022.svg", "svg-icon-2") !!}
                    <span class="d-none d-sm-inline">Import Excel</span>
                    <span class="d-inline d-sm-none">Import</span>
                </button>
                <button type="button" class="btn btn-primary btn-sm btn-md-md" data-bs-toggle="modal" data-bs-target="#modal_tambah_siswa">
                    {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-2") !!}
                    Tambah
                </button>
                {{-- Tombol Tandai Lulus (muncul di sebelah kanan tombol Tambah saat ada checkbox terpilih) --}}
                <form id="form_mark_lulus" action="{{ route('siswa.mark-lulus') }}" method="POST" class="d-inline d-none">
                    @csrf
                    {{-- IDs dikirim sebagai hidden fields array, diisi oleh JS --}}
                    <div id="lulus_ids_container"></div>
                    <button type="button" onclick="confirmMarkLulus()" class="btn btn-success btn-sm btn-md-md" id="btn_mark_lulus" title="Tandai siswa terpilih sebagai Lulus & hapus dari mesin fingerprint">
                        <i class="bi bi-mortarboard-fill me-1"></i>
                        <span>Tandai Lulus (<span id="lulus_count">0</span>)</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 w-100" id="kt_table_siswa" data-bulk-type="siswa" style="width: 100%;">
                <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                        <th class="w-30px">
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input select-all-checkbox" type="checkbox" />
                            </div>
                        </th>
                        <th class="min-w-80px">NIS</th>
                        <th class="min-w-150px">Nama</th>
                        <th class="min-w-90px">Jenis Kelamin</th>
                        <th class="min-w-100px">Kelas</th>
                        <th class="min-w-100px">Fingerprint ID</th>
                        <th class="min-w-90px">Status</th>
                        <th class="text-end min-w-70px">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">
                    @foreach ($siswas as $i => $item)
                    <tr>
                        <td>
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input select-item-checkbox" type="checkbox" value="{{ $item->id }}" />
                            </div>
                        </td>
                        <td>
                            <span class="text-gray-800">{{ $item->nis ?? '-' }}</span>
                        </td>
                        <td class="d-flex align-items-center" data-order="{{ $item->nama }}">
                            @php
                                $itemAvatar = ($item->user && $item->user->info && !empty($item->user->info->avatar) && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->user->info->avatar))
                                    ? asset('storage/' . ltrim($item->user->info->avatar, '/'))
                                    : null;
                            @endphp
                            <div class="symbol symbol-circle symbol-40px overflow-hidden me-3 flex-shrink-0">
                                @if($itemAvatar)
                                    <div class="symbol-label" style="background-image:url('{{ $itemAvatar }}'); background-size: cover; background-position: center;"></div>
                                @else
                                    <div class="symbol-label fs-4 bg-light-{{ $item->jenis_kelamin === 'L' ? 'primary' : 'danger' }} text-{{ $item->jenis_kelamin === 'L' ? 'primary' : 'danger' }} fw-bolder">
                                        {{ substr($item->nama, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <a href="{{ theme()->getPageUrl('absensi/profil-siswa') }}?id={{ $item->id }}" class="text-gray-800 text-hover-primary">{{ $item->nama }}</a>
                        </td>
                        <td>{{ $item->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                        <td>
                            <span>{{ $item->kelas->nama ?? 'Belum Ada' }}</span>
                        </td>
                        <td>
                            @if($item->fingerprint_id)
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge badge-light-primary fw-boldest fs-7">ID: {{ $item->fingerprint_id }}</span>
                                    @if(strtolower($item->status ?? 'aktif') !== 'aktif')
                                        <span class="badge badge-light-secondary text-gray-500 fw-bold fs-8" title="Siswa tidak aktif di mesin">Tidak Aktif</span>
                                    @elseif($item->is_pushed)
                                        <span class="badge badge-light-success fw-bold fs-8" title="Nama & ID sudah ter-sync ke mesin">Ter-sync</span>
                                    @else
                                        <span class="badge badge-light-warning fw-bold fs-8" title="Data baru/diubah, perlu di-POST ulang ke mesin">Perlu Post</span>
                                    @endif
                                </div>
                            @else
                                <span class="badge badge-light-secondary text-gray-500 fw-bold">Belum Registrasi</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $rawStatus = strtolower(trim($item->status ?? ''));
                                $statusVal = in_array($rawStatus, ['aktif', 'lulus', 'keluar']) ? $rawStatus : 'aktif';
                                $statusBadge = match($statusVal) {
                                    'lulus'  => 'badge-light-primary',
                                    'keluar' => 'badge-light-danger',
                                    default  => 'badge-light-success',
                                };
                                $statusLabel = match($statusVal) {
                                    'lulus'  => 'Lulus',
                                    'keluar' => 'Keluar',
                                    default  => 'Aktif',
                                };
                            @endphp
                            <span class="badge {{ $statusBadge }} fw-bold fs-8">{{ $statusLabel }}</span>
                        </td>
                        <td class="text-end">
                            <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                            </a>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-150px py-4" data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <a href="{{ theme()->getPageUrl('absensi/profil-siswa') }}?id={{ $item->id }}" class="menu-link px-3">
                                        Detail
                                    </a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 text-info fw-bold" onclick="confirmPostSingleSiswa('{{ $item->id }}', '{{ addslashes($item->nama) }}')">
                                        Post ke Mesin
                                    </a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 btn-edit"
                                       data-id="{{ $item->id }}"
                                       data-nama="{{ $item->nama }}"
                                       data-nisn="{{ $item->nisn ?? '' }}"
                                       data-nis="{{ $item->nis ?? '' }}"
                                       data-kelas="{{ $item->kelas_id ?? '' }}"
                                       data-jk="{{ $item->jenis_kelamin }}"
                                       data-lahir="{{ $item->tanggal_lahir ? $item->tanggal_lahir->format('Y-m-d') : '' }}"
                                       data-alamat="{{ $item->alamat ?? '' }}"
                                       data-fingerprint="{{ $item->fingerprint_id ?? '' }}"
                                       data-status="{{ $statusVal }}">
                                        Ubah
                                    </a>
                                </div>
                                @if($statusVal === 'aktif')
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 text-warning" onclick="confirmMarkKeluar('{{ $item->id }}', '{{ addslashes($item->nama) }}')">
                                        Tandai Keluar
                                    </a>
                                </div>
                                @else
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 text-success fw-bold" onclick="confirmMarkAktif('{{ $item->id }}', '{{ addslashes($item->nama) }}')">
                                        Aktifkan Kembali
                                    </a>
                                </div>
                                @endif
                                <div class="menu-item px-3">
                                    <form action="{{ route('siswa.destroy', $item->id) }}" method="POST" class="d-inline form-konfirmasi">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="menu-link px-3 text-danger border-0 bg-transparent w-100 text-start">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Siswa -->
<div class="modal fade" id="modal_tambah_siswa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Tambah Siswa</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <!-- Stepper Nav -->
                <div class="d-flex justify-content-center mb-9 border-bottom pb-5" id="tambah_siswa_stepper">
                    <div class="d-flex align-items-center me-10" data-step="1">
                        <span class="stepper-number bg-primary text-white p-2 rounded-circle fw-bold me-2 d-flex align-items-center justify-content-center" style="width:30px; height:30px;">1</span>
                        <span class="stepper-title fw-bolder text-gray-800">Akun User</span>
                    </div>
                    <div class="d-flex align-items-center" data-step="2">
                        <span class="stepper-number bg-light-primary text-primary p-2 rounded-circle fw-bold me-2 d-flex align-items-center justify-content-center" style="width:30px; height:30px;">2</span>
                        <span class="stepper-title fw-bolder text-gray-800">Profil Siswa</span>
                    </div>
                </div>

                <form class="form" id="form_tambah_siswa" action="{{ route('siswa.store') }}" method="POST" novalidate>
                    @csrf
                    
                    <!-- Step 1: User Account -->
                    <div id="tambah_siswa_step_1">
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">NIS (Nomor Induk Siswa)</label>
                            <input type="text" name="nis" class="form-control form-control-solid" placeholder="Nomor Induk Siswa (digunakan untuk login)" maxlength="20" required />
                            <div class="form-text text-muted">NIS digunakan sebagai identitas login siswa. Wajib diisi.</div>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Password</label>
                            <div class="position-relative">
                                <input type="password" name="password" class="form-control form-control-solid pe-12" placeholder="Masukkan password (minimal 6 karakter)" required />
                                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-1 toggle-password" style="cursor: pointer;">
                                    <i class="bi bi-eye-slash fs-2"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Student Profile -->
                    <div id="tambah_siswa_step_2" class="d-none">
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control form-control-solid" placeholder="Nama Lengkap Siswa" required />
                        </div>
                        <div class="row g-9 mb-7">
                            <div class="col-md-6 fv-row">
                                <label class="required fw-bold fs-6 mb-2">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select form-select-solid" required>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="required fw-bold fs-6 mb-2">Kelas</label>
                                <select name="kelas_id" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modal_tambah_siswa" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control form-control-solid" max="{{ date('Y-m-d') }}" />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control form-control-solid" rows="3" placeholder="Alamat lengkap siswa"></textarea>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="text-center pt-10 border-top mt-5">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal" id="tambah_siswa_btn_cancel">Batal</button>
                        <button type="button" class="btn btn-light-primary me-3 d-none" id="tambah_siswa_btn_prev" onclick="goToStep1()">Sebelumnya</button>
                        <button type="button" class="btn btn-primary" id="tambah_siswa_btn_next" onclick="goToStep2()">Berikutnya</button>
                        <button type="submit" class="btn btn-success d-none" id="tambah_siswa_btn_submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import Siswa -->
<div class="modal fade" id="modal_import_siswa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Import Data Siswa</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form id="form_import_siswa" class="form" action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-9">
                        <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                            {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg") !!}
                        </span>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-bold">
                                <h4 class="text-gray-900 fw-bolder">Petunjuk Import</h4>
                                <div class="fs-6 text-gray-700">
                                    <ul class="ps-4 mb-0">
                                        <li>Gunakan template Excel Kosong yang telah disediakan. <a href="{{ route('siswa.download-template', ['empty' => 1]) }}" class="fw-bolder text-primary text-decoration-underline">Download Template Excel</a></li>
                                        <li>Kolom utama: <strong>NIS (wajib), Nama, Jenis Kelamin, Kelas</strong>.</li>
                                        <li>NIS digunakan sebagai identitas & password login default. Siswa tanpa NIS tidak akan diimport.</li>
                                        <li>Siswa dengan NIS yang sudah ada akan dilewati (tidak digandakan).</li>
                                        <li>Format file: <strong>.xlsx</strong> atau <strong>.xls</strong>.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Pilih File Excel (.xlsx / .xls)</label>
                        <input type="file" name="file" class="form-control form-control-solid" accept=".xlsx, .xls" required />
                    </div>

                    <div class="text-center pt-5">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" id="btn_submit_import_siswa">
                            {!! theme()->getSvgIcon("icons/duotune/files/fil022.svg", "svg-icon-2") !!}
                            Mulai Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ubah Siswa -->
<div class="modal fade" id="modal_ubah_siswa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Ubah Siswa</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form class="form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control form-control-solid" required />
                    </div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-12 fv-row">
                            <label class="fw-bold fs-6 mb-2">NIS</label>
                            <input type="text" name="nis" class="form-control form-control-solid" maxlength="20" />
                        </div>
                    </div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select form-select-solid" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Kelas</label>
                            <select name="kelas_id" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modal_ubah_siswa" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control form-control-solid" max="{{ date('Y-m-d') }}" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">ID Fingerprint</label>
                            <input type="text" name="fingerprint_id" class="form-control form-control-solid" placeholder="Contoh: 1" />
                            <div class="form-text text-muted">ID PIN pada mesin fingerprint.</div>
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Status Siswa</label>
                        <select name="status" class="form-select form-select-solid" required>
                            <option value="aktif">Aktif</option>
                            <option value="lulus">Lulus</option>
                            <option value="keluar">Keluar</option>
                        </select>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control form-control-solid" rows="3"></textarea>
                    </div>
                    <div class="text-center pt-5">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    #kt_table_siswa tbody tr {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    #kt_table_siswa tbody tr:hover {
        background-color: var(--bs-table-hover-bg) !important;
    }
</style>

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#kt_table_siswa').DataTable({ 
        dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', 
        info:true, 
        order:[[2, 'asc']], 
        pageLength:20, 
        lengthMenu:[[10, 20, 50, 100, -1], [10, 20, 50, 100, "Semua"]],
        lengthChange:true, 
        columnDefs:[{orderable:false,targets:[0,7]}]  // kolom 0=checkbox, 7=aksi (status ada di 6)
    });

    // Make entire table row clickable (excluding checkbox and actions)
    $('#kt_table_siswa').on('click', 'tbody tr', function(e) {
        var targetTd = $(e.target).closest('td');
        if (targetTd.length === 0) return;
        var idx = targetTd.index();
        // Skip first column (checkbox) and last column (actions dropdown)
        if (idx === 0 || idx === 7 || $(e.target).closest('.menu').length || $(e.target).closest('[data-kt-menu-trigger]').length) {
            return;
        }
        var link = $(this).find('td:nth-child(3) a');
        if (link.length) {
            window.location.href = link.attr('href');
        }
    });

    // Fungsi untuk update tombol Tandai Lulus saat checkbox berubah
    function updateLulusButton() {
        var checkedBoxes = $('#kt_table_siswa tbody input[type="checkbox"].select-item-checkbox:checked');
        var count = checkedBoxes.length;
        var $wrapper = $('#form_mark_lulus');
        if (count > 0) {
            $('#lulus_count').text(count);
            $wrapper.removeClass('d-none');
        } else {
            $wrapper.addClass('d-none');
        }
    }

    // Pasang listener pada checkbox item
    $(document).on('change', '.select-item-checkbox', function() {
        updateLulusButton();
    });

    // Pasang listener pada checkbox select-all
    $(document).on('change', '.select-all-checkbox', function() {
        var checked = $(this).prop('checked');
        // Check semua checkbox di halaman yang terlihat saat ini
        table.rows({ page: 'current' }).nodes().to$().find('.select-item-checkbox').prop('checked', checked);
        updateLulusButton();
    });
    
    $('#search_siswa').on('keyup', function() { 
        table.search(this.value).draw(); 
    });

    // Re-init Metronic menu instances on table redraw (pagination, search, sort)
    table.on('draw', function() {
        if (window.KTMenu) {
            KTMenu.createInstances();
        }
    });

    // Stepper navigation in modal_tambah_siswa
    var currentStep = 1;

    window.showStep = function(step) {
        if (step === 1) {
            $('#tambah_siswa_step_1').removeClass('d-none').css('display', 'block');
            $('#tambah_siswa_step_2').addClass('d-none').css('display', 'none');
            
            $('#tambah_siswa_btn_cancel').removeClass('d-none').css('display', 'inline-block');
            $('#tambah_siswa_btn_prev').addClass('d-none').css('display', 'none');
            $('#tambah_siswa_btn_next').removeClass('d-none').css('display', 'inline-block');
            $('#tambah_siswa_btn_submit').addClass('d-none').css('display', 'none');
            
            // Stepper nav colors
            $('#tambah_siswa_stepper [data-step="1"] .stepper-number').removeClass('bg-light-primary text-primary').addClass('bg-primary text-white');
            $('#tambah_siswa_stepper [data-step="2"] .stepper-number').removeClass('bg-primary text-white').addClass('bg-light-primary text-primary');
        } else if (step === 2) {
            $('#tambah_siswa_step_1').addClass('d-none').css('display', 'none');
            $('#tambah_siswa_step_2').removeClass('d-none').css('display', 'block');
            
            $('#tambah_siswa_btn_cancel').addClass('d-none').css('display', 'none');
            $('#tambah_siswa_btn_prev').removeClass('d-none').css('display', 'inline-block');
            $('#tambah_siswa_btn_next').addClass('d-none').css('display', 'none');
            $('#tambah_siswa_btn_submit').removeClass('d-none').css('display', 'inline-block');

            // Stepper nav colors
            $('#tambah_siswa_stepper [data-step="1"] .stepper-number').removeClass('bg-primary text-white').addClass('bg-light-primary text-primary');
            $('#tambah_siswa_stepper [data-step="2"] .stepper-number').removeClass('bg-light-primary text-primary').addClass('bg-primary text-white');
        }
        currentStep = step;
    };

    window.goToStep2 = function() {
        var nis = $('#modal_tambah_siswa input[name="nis"]').val() ? $('#modal_tambah_siswa input[name="nis"]').val().trim() : '';
        var password = $('#modal_tambah_siswa input[name="password"]').val() ? $('#modal_tambah_siswa input[name="password"]').val().trim() : '';

        if (!nis || !password) {
            Swal.fire({
                text: 'Silakan isi NIS dan password terlebih dahulu.',
                icon: 'error',
                buttonsStyling: false,
                confirmButtonText: 'Oke, mengerti!',
                customClass: {
                    confirmButton: 'btn btn-primary'
                }
            });
            return false;
        }

        if (password.length < 6) {
            Swal.fire({
                text: 'Password minimal harus 6 karakter.',
                icon: 'error',
                buttonsStyling: false,
                confirmButtonText: 'Oke, mengerti!',
                customClass: {
                    confirmButton: 'btn btn-primary'
                }
            });
            return false;
        }

        window.showStep(2);
        return false;
    };

    window.goToStep1 = function() {
        window.showStep(1);
        return false;
    };

    // ─── Loading Bar saat Import Siswa ────────────────────────────────────────
    $('#form_import_siswa').on('submit', function(e) {
        var fileInput = $(this).find('input[type="file"]')[0];
        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
            return;
        }
        
        var fileName = fileInput.files[0].name;
        $('#btn_submit_import_siswa').prop('disabled', true).html('<span class="spinner-border spinner-border-sm align-middle me-2"></span> Mengunggah...');
        
        Swal.fire({
            title: 'Mengimpor Data Siswa...',
            html: '<div class="mb-3 text-gray-700 fs-6">Sistem sedang membaca file <strong>' + fileName + '</strong> dan memproses data akun & siswa...</div>' +
                  '<div class="progress h-20px w-100 bg-light-primary mb-2">' +
                  '  <div class="progress-bar progress-bar-striped progress-bar-animated bg-success fw-bold" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Memproses Excel...</div>' +
                  '</div>' +
                  '<div class="text-muted fs-7">Mohon tidak menutup atau merefresh halaman ini.</div>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false
        });
    });

    $(document).on('click', '#tambah_siswa_btn_next', function(e) {
        e.preventDefault();
        window.goToStep2();
    });

    $(document).on('click', '#tambah_siswa_btn_prev', function(e) {
        e.preventDefault();
        window.goToStep1();
    });

    $(document).on('submit', '#form_tambah_siswa', function(e) {
        var nama = $('#modal_tambah_siswa input[name="nama"]').val() ? $('#modal_tambah_siswa input[name="nama"]').val().trim() : '';
        var kelas = $('#modal_tambah_siswa select[name="kelas_id"]').val();

        if (!nama || !kelas) {
            e.preventDefault();
            Swal.fire({
                text: 'Silakan isi nama lengkap dan pilih kelas terlebih dahulu.',
                icon: 'error',
                buttonsStyling: false,
                confirmButtonText: 'Oke, mengerti!',
                customClass: {
                    confirmButton: 'btn btn-primary'
                }
            });
            return false;
        }
    });

    // When modal is shown
    $('#modal_tambah_siswa').on('show.bs.modal', function () {
        // Reset the form
        var form = $(this).find('form')[0];
        if (form) form.reset();
        
        // Reset select2 fields
        $(this).find('select[name="jenis_kelamin"]').val('L').trigger('change');
        $(this).find('select[name="kelas_id"]').val('').trigger('change');
        
        showStep(1);
    });

    // Use event delegation so edit button works on all pages and after searching/sorting
    $(document).on('click', '.btn-edit', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var nisn = $(this).data('nisn');
        var nis = $(this).data('nis');
        var kelas = $(this).data('kelas');
        var jk = $(this).data('jk');
        var lahir = $(this).data('lahir');
        var alamat = $(this).data('alamat');
        var fingerprint = $(this).data('fingerprint');
        var status = $(this).data('status');
        
        var form = $('#modal_ubah_siswa form');
        form.attr('action', '{{ url("absensi/master/siswa") }}/' + id);
        form.find('input[name="nama"]').val(nama);
        form.find('input[name="nisn"]').val(nisn);
        form.find('input[name="nis"]').val(nis);
        form.find('select[name="kelas_id"]').val(kelas).trigger('change');
        form.find('select[name="jenis_kelamin"]').val(jk).trigger('change');
        form.find('input[name="tanggal_lahir"]').val(lahir);
        form.find('textarea[name="alamat"]').val(alamat);
        form.find('input[name="fingerprint_id"]').val(fingerprint);
        form.find('select[name="status"]').val(status || 'aktif').trigger('change');
        
        $('#modal_ubah_siswa').modal('show');
    });

    // Toggle Password Visibility
    $(document).on('click', '.toggle-password', function(e) {
        e.preventDefault();
        var input = $(this).siblings('input');
        var icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        } else {
            input.attr('type', 'password');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        }
    });

    // Enforce numeric only input for NIS
    $(document).on('input', 'input[name="nis"]', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
});

function confirmMarkLulus() {
    var checkedBoxes = $('#kt_table_siswa tbody input[type="checkbox"].select-item-checkbox:checked');
    var selectedIds = [];
    checkedBoxes.each(function() {
        selectedIds.push($(this).val());
    });

    if (selectedIds.length === 0) {
        Swal.fire({ text: 'Pilih minimal 1 siswa terlebih dahulu.', icon: 'warning', confirmButtonText: 'Oke', buttonsStyling: false, customClass: { confirmButton: 'btn btn-primary' } });
        return;
    }

    Swal.fire({
        title: 'Tandai ' + selectedIds.length + ' Siswa sebagai Lulus?',
        html: '<b>' + selectedIds.length + ' siswa terpilih</b> akan ditandai sebagai <b class="text-success">Lulus</b>.<br><br>' +
              '<span class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Data siswa tersebut akan <b>dihapus dari mesin fingerprint</b> (X100C) sehingga tidak dapat melakukan absensi lagi.</span>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-mortarboard-fill me-1"></i> Ya, Tandai Lulus',
        cancelButtonText: 'Batal',
        customClass: {
            confirmButton: 'btn btn-success fw-bold px-6',
            cancelButton: 'btn btn-light fw-bold px-6 me-3'
        },
        buttonsStyling: false,
        reverseButtons: true
    }).then(function(result) {
        if (result.isConfirmed) {
            // Isi container dengan hidden inputs array
            var $container = $('#lulus_ids_container');
            $container.empty();
            $.each(selectedIds, function(i, id) {
                $container.append('<input type="hidden" name="ids[]" value="' + id + '">');
            });
            $('#form_mark_lulus').submit();
        }
    });
}

function confirmMarkKeluar(siswaId, siswaNama) {
    Swal.fire({
        title: 'Tandai Siswa sebagai Keluar?',
        html: 'Siswa <b>' + siswaNama + '</b> akan ditandai sebagai <b class="text-danger">Keluar</b>.<br><br>' +
              '<span class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Data siswa ini akan <b>dihapus dari mesin fingerprint</b> (X100C).</span>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-box-arrow-right me-1"></i> Ya, Tandai Keluar',
        cancelButtonText: 'Batal',
        customClass: {
            confirmButton: 'btn btn-warning fw-bold px-6',
            cancelButton: 'btn btn-light fw-bold px-6 me-3'
        },
        buttonsStyling: false,
        reverseButtons: true
    }).then(function(result) {
        if (result.isConfirmed) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url("absensi/master/siswa") }}/' + siswaId + '/mark-keluar';
            var csrf = document.createElement('input');
            csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmMarkAktif(siswaId, siswaNama) {
    Swal.fire({
        title: 'Aktifkan Kembali Siswa?',
        html: 'Siswa <b>' + siswaNama + '</b> akan ditandai sebagai <b class="text-success">Aktif</b> kembali.<br><br>' +
              '<span class="text-info"><i class="bi bi-info-circle-fill me-1"></i>Data nama & ID siswa akan <b>di-upload ulang ke mesin fingerprint</b> (X100C).</span>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-check-circle me-1"></i> Ya, Aktifkan Kembali',
        cancelButtonText: 'Batal',
        customClass: {
            confirmButton: 'btn btn-success fw-bold px-6',
            cancelButton: 'btn btn-light fw-bold px-6 me-3'
        },
        buttonsStyling: false,
        reverseButtons: true
    }).then(function(result) {
        if (result.isConfirmed) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url("absensi/master/siswa") }}/' + siswaId + '/mark-aktif';
            var csrf = document.createElement('input');
            csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmPostKeMesin() {
    var checkedBoxes = $('#kt_table_siswa tbody input[type="checkbox"].select-item-checkbox:checked');
    var selectedIds = [];
    checkedBoxes.each(function() {
        selectedIds.push($(this).val());
    });

    if (selectedIds.length > 0) {
        $('#post_selected_ids').val(JSON.stringify(selectedIds));
        var titleText = 'Post ' + selectedIds.length + ' Siswa Terpilih ke Mesin?';
        var htmlText = 'Hanya <b>' + selectedIds.length + ' siswa terpilih</b> yang akan di-upload dan disinkronkan ke seluruh mesin fingerprint yang aktif.';
        var btnText = '<i class="bi bi-send me-1"></i> Ya, Post ' + selectedIds.length + ' Siswa';
    } else {
        $('#post_selected_ids').val('');
        var titleText = 'Post Seluruh Data Siswa ke Mesin?';
        var htmlText = 'Seluruh nama & ID siswa akan di-upload dan disinkronkan ke seluruh mesin fingerprint yang aktif.';
        var btnText = '<i class="bi bi-send me-1"></i> Ya, Post Semua Siswa';
    }

    Swal.fire({
        title: titleText,
        html: htmlText,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: btnText,
        cancelButtonText: 'Batal',
        customClass: {
            confirmButton: 'btn btn-info fw-bold px-6',
            cancelButton: 'btn btn-light fw-bold px-6 me-3'
        },
        buttonsStyling: false,
        reverseButtons: true
    }).then(function(result) {
        if (result.isConfirmed) {
            document.getElementById('form_post_ke_mesin').submit();
        }
    });
}

function confirmPostSingleSiswa(siswaId, siswaNama) {
    $('#post_selected_ids').val(JSON.stringify([siswaId]));
    Swal.fire({
        title: 'Post Siswa ke Mesin?',
        html: 'Data siswa <b>' + siswaNama + '</b> akan di-upload & disinkronkan ke seluruh mesin fingerprint yang aktif.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-send me-1"></i> Ya, Post ke Mesin',
        cancelButtonText: 'Batal',
        customClass: {
            confirmButton: 'btn btn-info fw-bold px-6',
            cancelButton: 'btn btn-light fw-bold px-6 me-3'
        },
        buttonsStyling: false,
        reverseButtons: true
    }).then(function(result) {
        if (result.isConfirmed) {
            document.getElementById('form_post_ke_mesin').submit();
        }
    });
}
</script>
@endsection
</x-base-layout>
