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

@if(session('import_success'))
    <div class="alert bg-light-primary border border-primary d-flex align-items-center p-5 mb-10">
        <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
            {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg") !!}
        </span>
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-primary">Informasi Import Data Guru</h4>
            <span class="text-primary">
                Berhasil diimport: <strong>{{ session('import_success')['success_count'] }}</strong> guru.<br>
                Tidak diimport (sudah ada / dilewati): <strong>{{ session('import_success')['skip_count'] }}</strong> guru.
            </span>
        </div>
    </div>
@endif

<div class="card mt-2">
    <div class="card-header border-0 pt-6 flex-column flex-md-row gap-3">
        <div class="card-title my-0">
            <div class="d-flex align-items-center position-relative my-1 w-100 w-md-250px">
                {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                <input type="text" id="search_guru" class="form-control form-control-solid ps-14 w-100" placeholder="Cari guru..." />
            </div>
        </div>
        <div class="card-toolbar my-0">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('guru.download-template', ['empty' => 1]) }}" class="btn btn-light-warning btn-sm btn-md-md">
                    {!! theme()->getSvgIcon("icons/duotune/files/fil021.svg", "svg-icon-2") !!}
                    <span class="d-none d-sm-inline">Download Template</span>
                    <span class="d-inline d-sm-none">Template</span>
                </a>
                <a href="{{ route('guru.download-template') }}" class="btn btn-light-success btn-sm btn-md-md">
                    {!! theme()->getSvgIcon("icons/duotune/files/fil021.svg", "svg-icon-2") !!}
                    <span class="d-none d-sm-inline">Ekspor Data Guru</span>
                    <span class="d-inline d-sm-none">Ekspor</span>
                </a>
                <button type="button" class="btn btn-light-primary btn-sm btn-md-md" data-bs-toggle="modal" data-bs-target="#modal_import_guru">
                    {!! theme()->getSvgIcon("icons/duotune/files/fil022.svg", "svg-icon-2") !!}
                    <span class="d-none d-sm-inline">Import Excel</span>
                    <span class="d-inline d-sm-none">Import</span>
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_tambah_guru">
                    {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-2") !!}
                    Tambah
                </button>
            </div>
        </div>
    </div>
    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 w-100" id="kt_table_guru" data-bulk-type="guru" style="width: 100%;">
                <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                        <th class="w-30px">
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input select-all-checkbox" type="checkbox" />
                            </div>
                        </th>
                        <th class="min-w-150px">NIP / NUPTK</th>
                        <th class="min-w-200px">Nama Guru</th>
                        <th class="min-w-140px">Kepegawaian</th>
                        <th class="min-w-120px">No HP</th>
                        <th class="min-w-150px">Wali Kelas</th>
                        <th class="min-w-120px">Mata Pelajaran</th>
                        <th class="min-w-80px">Status</th>
                        <th class="text-end min-w-70px">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">
                    @foreach ($gurus as $i => $item)
                    <tr>
                        <td>
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input select-item-checkbox" type="checkbox" value="{{ $item->id }}" />
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-gray-800 fw-bolder fs-6">{{ $item->nip ?? '-' }}</span>
                                @if($item->nuptk)
                                    <span class="text-muted fs-8">NUPTK: {{ $item->nuptk }}</span>
                                @elseif($item->nik)
                                    <span class="text-muted fs-8">NIK: {{ $item->nik }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-circle symbol-40px overflow-hidden me-3">
                                    <div class="symbol-label fs-4 bg-light-primary text-primary fw-bolder">
                                        {{ substr($item->nama, 0, 1) }}
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="{{ theme()->getPageUrl('absensi/profil-guru') }}?id={{ $item->id }}" class="text-gray-800 text-hover-primary fw-bolder">{{ $item->nama }}</a>
                                    <span class="text-muted fs-7">
                                        {{ $item->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-laki' }}
                                        @if($item->email)
                                            • {{ $item->email }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @php
                                    $pegawai = strtolower($item->status_kepegawaian ?? '');
                                    $badgeClass = 'badge-light-secondary text-gray-700';
                                    if (str_contains($pegawai, 'pns')) {
                                        $badgeClass = 'badge-light-success';
                                    } elseif (str_contains($pegawai, 'pppk')) {
                                        $badgeClass = 'badge-light-primary';
                                    } elseif (str_contains($pegawai, 'gtt') || str_contains($pegawai, 'honorer')) {
                                        $badgeClass = 'badge-light-warning';
                                    }
                                @endphp
                                @if($item->status_kepegawaian)
                                    <span class="badge {{ $badgeClass }} fw-bolder w-fit">{{ $item->status_kepegawaian }}</span>
                                @else
                                    <span class="badge badge-light-secondary text-muted fw-normal w-fit">-</span>
                                @endif
                                
                                @if($item->pangkat_golongan)
                                    <span class="text-gray-600 fs-8 fw-semibold">Gol: {{ $item->pangkat_golongan }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($item->no_hp)
                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $item->no_hp)) }}" target="_blank" class="text-gray-800 text-hover-primary">
                                    <i class="bi bi-whatsapp text-success me-1"></i> {{ $item->no_hp }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($item->kelas_count > 0)
                                <span class="badge badge-light-success fw-bolder">{{ $item->kelas_count }} Kelas</span>
                            @else
                                <span class="badge badge-light-secondary text-muted fw-bold">Bukan Wali</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-light-info fw-bolder">{{ $item->mata_pelajarans_count }} Mapel</span>
                        </td>
                        <td>
                            @if(($item->status ?? 'aktif') === 'aktif')
                                <span class="badge badge-light-success fw-bolder">Aktif</span>
                            @else
                                <span class="badge badge-light-danger fw-bolder">{{ ucfirst($item->status) }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                            </a>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <a href="{{ theme()->getPageUrl('absensi/profil-guru') }}?id={{ $item->id }}" class="menu-link px-3">
                                        Detail
                                    </a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 btn-edit"
                                       data-id="{{ $item->id }}"
                                       data-nama="{{ $item->nama }}"
                                       data-nip="{{ $item->nip ?? '' }}"
                                       data-email="{{ $item->email ?? '' }}"
                                       data-jk="{{ $item->jenis_kelamin ?? 'L' }}"
                                       data-tempat-lahir="{{ $item->tempat_lahir ?? '' }}"
                                       data-tanggal-lahir="{{ $item->tanggal_lahir ? $item->tanggal_lahir->format('Y-m-d') : '' }}"
                                       data-agama="{{ $item->agama ?? '' }}"
                                       data-nohp="{{ $item->no_hp ?? '' }}"
                                       data-alamat="{{ $item->alamat ?? '' }}"
                                       data-status="{{ $item->status ?? 'aktif' }}"
                                       data-nik="{{ $item->nik ?? '' }}"
                                       data-npwp="{{ $item->npwp ?? '' }}"
                                       data-nuptk="{{ $item->nuptk ?? '' }}"
                                       data-status-kepegawaian="{{ $item->status_kepegawaian ?? '' }}"
                                       data-tugas-tambahan="{{ $item->tugas_tambahan ?? '' }}"
                                       data-sk-cpns="{{ $item->sk_cpns ?? '' }}"
                                       data-tanggal-cpns="{{ $item->tanggal_cpns ? $item->tanggal_cpns->format('Y-m-d') : '' }}"
                                       data-sk-pengangkatan="{{ $item->sk_pengangkatan ?? '' }}"
                                       data-tmt-pengangkatan="{{ $item->tmt_pengangkatan ? $item->tmt_pengangkatan->format('Y-m-d') : '' }}"
                                       data-lembaga-pengangkatan="{{ $item->lembaga_pengangkatan ?? '' }}"
                                       data-pangkat-golongan="{{ $item->pangkat_golongan ?? '' }}">
                                        Ubah
                                    </a>
                                </div>
                                <div class="menu-item px-3">
                                    <form action="{{ route('guru.destroy', $item->id) }}" method="POST" class="d-inline form-konfirmasi">
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

<!-- Modal Tambah Guru -->
<div class="modal fade" id="modal_tambah_guru" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Tambah Guru Baru</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <!-- Stepper Nav -->
                <div class="d-flex justify-content-center mb-9 border-bottom pb-5" id="tambah_guru_stepper">
                    <div class="d-flex align-items-center me-6" data-step="1">
                        <span class="stepper-number bg-primary text-white p-2 rounded-circle fw-bold me-2 d-flex align-items-center justify-content-center" style="width:30px; height:30px;">1</span>
                        <span class="stepper-title fw-bolder text-gray-800">Akun Login</span>
                    </div>
                    <div class="d-flex align-items-center me-6" data-step="2">
                        <span class="stepper-number bg-light-primary text-primary p-2 rounded-circle fw-bold me-2 d-flex align-items-center justify-content-center" style="width:30px; height:30px;">2</span>
                        <span class="stepper-title fw-bolder text-gray-800">Biodata Guru</span>
                    </div>
                    <div class="d-flex align-items-center" data-step="3">
                        <span class="stepper-number bg-light-primary text-primary p-2 rounded-circle fw-bold me-2 d-flex align-items-center justify-content-center" style="width:30px; height:30px;">3</span>
                        <span class="stepper-title fw-bolder text-gray-800">Kepegawaian</span>
                    </div>
                </div>

                <form class="form" action="{{ route('guru.store') }}" method="POST">
                    @csrf

                    <!-- Step 1: User Account -->
                    <div id="tambah_guru_step_1">
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">NIP (Nomor Induk Pegawai)</label>
                            <input type="text" name="nip" class="form-control form-control-solid" placeholder="Contoh: 198501152010011002" required />
                            <div class="form-text text-muted">NIP digunakan sebagai identitas login utama guru.</div>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Password</label>
                            <div class="position-relative">
                                <input type="password" name="password" class="form-control form-control-solid pe-12" placeholder="Minimal 6 karakter" required />
                                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-1 toggle-password" style="cursor: pointer;">
                                    <i class="bi bi-eye-slash fs-2"></i>
                                </span>
                            </div>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">Email</label>
                            <input type="email" name="email" class="form-control form-control-solid" placeholder="Contoh: guru@sekolah.sch.id (opsional)" />
                        </div>
                    </div>

                    <!-- Step 2: Biodata Guru -->
                    <div id="tambah_guru_step_2" class="d-none">
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Nama Lengkap & Gelar</label>
                            <input type="text" name="nama" class="form-control form-control-solid" placeholder="Contoh: Ahmad Fauzi, S.Pd., M.Pd." required />
                        </div>

                        <div class="row g-9 mb-7">
                            <div class="col-md-6 fv-row">
                                <label class="required fw-bold fs-6 mb-2">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select form-control-solid">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="fw-bold fs-6 mb-2">Agama</label>
                                <select name="agama" class="form-select form-control-solid">
                                    <option value="Islam">Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katolik">Katolik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Buddha">Buddha</option>
                                    <option value="Konghucu">Konghucu</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-9 mb-7">
                            <div class="col-md-6 fv-row">
                                <label class="fw-bold fs-6 mb-2">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control form-control-solid" placeholder="Kota / Kabupaten" />
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="fw-bold fs-6 mb-2">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control form-control-solid" />
                            </div>
                        </div>

                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" class="form-control form-control-solid" placeholder="Contoh: 081234567890" />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control form-control-solid" rows="2" placeholder="Alamat domisili saat ini"></textarea>
                        </div>
                    </div>

                    <!-- Step 3: Kepegawaian -->
                    <div id="tambah_guru_step_3" class="d-none">
                        <div class="row g-9 mb-7">
                            <div class="col-md-6 fv-row">
                                <label class="fw-bold fs-6 mb-2">Status Kepegawaian</label>
                                <input type="text" name="status_kepegawaian" class="form-control form-control-solid" placeholder="Contoh: PNS / PPPK / Guru Tetap Yayasan" />
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="fw-bold fs-6 mb-2">Pangkat / Golongan</label>
                                <input type="text" name="pangkat_golongan" class="form-control form-control-solid" placeholder="Contoh: Penata Muda / III-a" />
                            </div>
                        </div>

                        <div class="row g-9 mb-7">
                            <div class="col-md-4 fv-row">
                                <label class="fw-bold fs-6 mb-2">NUPTK</label>
                                <input type="text" name="nuptk" class="form-control form-control-solid" placeholder="Nomor NUPTK" />
                            </div>
                            <div class="col-md-4 fv-row">
                                <label class="fw-bold fs-6 mb-2">NIK</label>
                                <input type="text" name="nik" class="form-control form-control-solid" placeholder="16 digit NIK" />
                            </div>
                            <div class="col-md-4 fv-row">
                                <label class="fw-bold fs-6 mb-2">NPWP</label>
                                <input type="text" name="npwp" class="form-control form-control-solid" placeholder="Nomor NPWP" />
                            </div>
                        </div>

                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">Tugas Tambahan</label>
                            <input type="text" name="tugas_tambahan" class="form-control form-control-solid" placeholder="Contoh: Wali Kelas / Kepala Perpustakaan / Pembina OSIS" />
                        </div>

                        <div class="row g-9 mb-7">
                            <div class="col-md-6 fv-row">
                                <label class="fw-bold fs-6 mb-2">SK Pengangkatan</label>
                                <input type="text" name="sk_pengangkatan" class="form-control form-control-solid" placeholder="Nomor SK Pengangkatan" />
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="fw-bold fs-6 mb-2">TMT Pengangkatan</label>
                                <input type="date" name="tmt_pengangkatan" class="form-control form-control-solid" />
                            </div>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="text-center pt-10 border-top mt-5">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal" id="tambah_guru_btn_cancel">Batal</button>
                        <button type="button" class="btn btn-light-primary me-3 d-none" id="tambah_guru_btn_prev">Sebelumnya</button>
                        <button type="button" class="btn btn-primary" id="tambah_guru_btn_next">Berikutnya</button>
                        <button type="submit" class="btn btn-success d-none" id="tambah_guru_btn_submit">Simpan Data Guru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import Guru -->
<div class="modal fade" id="modal_import_guru" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Import Data Guru dari Excel</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-9">
                    <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg") !!}
                    </span>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-bold">
                            <h4 class="text-gray-900 fw-bolder">Petunjuk Import Format Baru</h4>
                            <div class="fs-6 text-gray-700">
                                <ul class="ps-4 mb-2">
                                    <li>Gunakan format template Excel resmi yang didukung sistem (21 kolom lengkap). <a href="{{ route('guru.download-template', ['empty' => 1]) }}" class="fw-bolder text-primary text-decoration-underline">Download Template Excel</a></li>
                                    <li>Kolom yang tersedia: <strong>nip, nama, jenis_kelamin, tempat_lahir, tanggal_lahir, agama, alamat, no_telepon, email, status, nik, npwp, nuptk, status_kepegawaian, tugas_tambahan, sk_cpns, tanggal_cpns, sk_pengangkatan, tmt_pengangkatan, lembaga_pengangkatan, pangkat_golongan</strong>.</li>
                                    <li>Kolom <strong>NIP</strong> dan <strong>Nama</strong> wajib diisi. NIP digunakan sebagai username login default.</li>
                                    <li>Jika NIP sudah terdaftar, data akan otomatis dilewati agar tidak terjadi duplikasi.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <form action="{{ route('guru.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Pilih File Excel (.xlsx / .xls)</label>
                        <input type="file" name="file" id="file_import_guru"
                            class="form-control form-control-solid"
                            accept=".xlsx,.xls" required />
                        <div class="form-text text-muted">Pastikan file dalam format Microsoft Excel (.xlsx atau .xls)</div>
                    </div>
                    <div class="text-center pt-5">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            {!! theme()->getSvgIcon("icons/duotune/files/fil022.svg", "svg-icon-2") !!}
                            Upload & Import Data Guru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ubah Guru -->
<div class="modal fade" id="modal_ubah_guru" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Ubah Data Guru</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form class="form" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control form-control-solid" required />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">NIP</label>
                            <input type="text" name="nip" class="form-control form-control-solid" />
                        </div>
                    </div>

                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select form-control-solid">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">Agama</label>
                            <select name="agama" class="form-select form-control-solid">
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Konghucu">Konghucu</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control form-control-solid" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control form-control-solid" />
                        </div>
                    </div>

                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">Status Kepegawaian</label>
                            <input type="text" name="status_kepegawaian" class="form-control form-control-solid" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">Pangkat / Golongan</label>
                            <input type="text" name="pangkat_golongan" class="form-control form-control-solid" />
                        </div>
                    </div>

                    <div class="row g-9 mb-7">
                        <div class="col-md-4 fv-row">
                            <label class="fw-bold fs-6 mb-2">NUPTK</label>
                            <input type="text" name="nuptk" class="form-control form-control-solid" />
                        </div>
                        <div class="col-md-4 fv-row">
                            <label class="fw-bold fs-6 mb-2">NIK</label>
                            <input type="text" name="nik" class="form-control form-control-solid" />
                        </div>
                        <div class="col-md-4 fv-row">
                            <label class="fw-bold fs-6 mb-2">NPWP</label>
                            <input type="text" name="npwp" class="form-control form-control-solid" />
                        </div>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">Tugas Tambahan</label>
                        <input type="text" name="tugas_tambahan" class="form-control form-control-solid" />
                    </div>

                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">No. HP (WhatsApp)</label>
                            <input type="text" name="no_hp" class="form-control form-control-solid" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">Email</label>
                            <input type="email" name="email" class="form-control form-control-solid" />
                        </div>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control form-control-solid" rows="2"></textarea>
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
    #kt_table_guru tbody tr {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    #kt_table_guru tbody tr:hover {
        background-color: var(--bs-table-hover-bg) !important;
    }
    .w-fit {
        width: fit-content !important;
    }
</style>

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#kt_table_guru').DataTable({ 
        dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', 
        info:true, 
        order:[], 
        pageLength:5, 
        lengthChange:true, 
        columnDefs:[{orderable:false,targets:[0,8]}] 
    });

    // Make entire table row clickable (excluding checkbox and actions)
    $('#kt_table_guru').on('click', 'tbody tr', function(e) {
        var targetTd = $(e.target).closest('td');
        if (targetTd.length === 0) return;
        var idx = targetTd.index();
        // Skip first column (checkbox) and last column (actions dropdown)
        if (idx === 0 || idx === 8 || $(e.target).closest('.menu').length || $(e.target).closest('[data-kt-menu-trigger]').length) {
            return;
        }
        var link = $(this).find('td:nth-child(3) a');
        if (link.length) {
            window.location.href = link.attr('href');
        }
    });
    
    $('#search_guru').on('keyup', function() { 
        table.search(this.value).draw(); 
    });

    // Re-init Metronic menu instances on table redraw (pagination, search, sort)
    table.on('draw', function() {
        if (window.KTMenu) {
            KTMenu.createInstances();
        }
    });

    // Stepper navigation in modal_tambah_guru
    var currentStep = 1;

    function showStep(step) {
        $('#tambah_guru_step_1, #tambah_guru_step_2, #tambah_guru_step_3').addClass('d-none');
        $('#tambah_guru_step_' + step).removeClass('d-none');

        if (step === 1) {
            $('#tambah_guru_btn_cancel').removeClass('d-none');
            $('#tambah_guru_btn_prev').addClass('d-none');
            $('#tambah_guru_btn_next').removeClass('d-none');
            $('#tambah_guru_btn_submit').addClass('d-none');
        } else if (step === 2) {
            $('#tambah_guru_btn_cancel').addClass('d-none');
            $('#tambah_guru_btn_prev').removeClass('d-none');
            $('#tambah_guru_btn_next').removeClass('d-none');
            $('#tambah_guru_btn_submit').addClass('d-none');
        } else if (step === 3) {
            $('#tambah_guru_btn_cancel').addClass('d-none');
            $('#tambah_guru_btn_prev').removeClass('d-none');
            $('#tambah_guru_btn_next').addClass('d-none');
            $('#tambah_guru_btn_submit').removeClass('d-none');
        }

        // Stepper nav colors
        for (var s = 1; s <= 3; s++) {
            var $item = $('#tambah_guru_stepper [data-step="' + s + '"] .stepper-number');
            if (s === step) {
                $item.removeClass('bg-light-primary text-primary').addClass('bg-primary text-white');
            } else if (s < step) {
                $item.removeClass('bg-primary text-white').addClass('bg-light-success text-success');
            } else {
                $item.removeClass('bg-primary text-white bg-light-success text-success').addClass('bg-light-primary text-primary');
            }
        }
        currentStep = step;
    }

    $('#tambah_guru_btn_next').on('click', function() {
        if (currentStep === 1) {
            var nip = $('#modal_tambah_guru input[name="nip"]').val();
            var password = $('#modal_tambah_guru input[name="password"]').val();

            if (!nip || !password) {
                Swal.fire({
                    text: 'Silakan isi NIP dan password terlebih dahulu.',
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'Oke, mengerti!',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }

            if (password.length < 6) {
                Swal.fire({
                    text: 'Password minimal harus 6 karakter.',
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'Oke, mengerti!',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }
            showStep(2);
        } else if (currentStep === 2) {
            var nama = $('#modal_tambah_guru input[name="nama"]').val();
            if (!nama) {
                Swal.fire({
                    text: 'Silakan isi nama lengkap guru terlebih dahulu.',
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'Oke, mengerti!',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }
            showStep(3);
        }
    });

    $('#tambah_guru_btn_prev').on('click', function() {
        showStep(currentStep - 1);
    });

    // When modal is shown
    $('#modal_tambah_guru').on('show.bs.modal', function () {
        var form = $(this).find('form')[0];
        if (form) form.reset();
        showStep(1);
    });

    // Use event delegation so edit button works on all pages and after searching/sorting
    $(document).on('click', '.btn-edit', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var id = $btn.data('id');
        
        var form = $('#modal_ubah_guru form');
        form.attr('action', '{{ url("absensi/master/guru") }}/' + id);
        form.find('input[name="nama"]').val($btn.data('nama'));
        form.find('input[name="nip"]').val($btn.data('nip'));
        form.find('input[name="email"]').val($btn.data('email'));
        form.find('select[name="jenis_kelamin"]').val($btn.data('jk') || 'L');
        form.find('input[name="tempat_lahir"]').val($btn.data('tempat-lahir'));
        form.find('input[name="tanggal_lahir"]').val($btn.data('tanggal-lahir'));
        form.find('select[name="agama"]').val($btn.data('agama') || 'Islam');
        form.find('input[name="no_hp"]').val($btn.data('nohp'));
        form.find('textarea[name="alamat"]').val($btn.data('alamat'));
        form.find('input[name="status_kepegawaian"]').val($btn.data('status-kepegawaian'));
        form.find('input[name="pangkat_golongan"]').val($btn.data('pangkat-golongan'));
        form.find('input[name="nuptk"]').val($btn.data('nuptk'));
        form.find('input[name="nik"]').val($btn.data('nik'));
        form.find('input[name="npwp"]').val($btn.data('npwp'));
        form.find('input[name="tugas_tambahan"]').val($btn.data('tugas-tambahan'));
        
        $('#modal_ubah_guru').modal('show');
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
});
</script>
@endsection
</x-base-layout>
