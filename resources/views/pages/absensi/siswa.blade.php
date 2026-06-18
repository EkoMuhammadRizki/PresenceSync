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
    <div class="alert alert-info d-flex align-items-center p-5 mb-10">
        <span class="svg-icon svg-icon-2hx svg-icon-info me-4">
            {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg") !!}
        </span>
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-dark">Informasi Import Data Siswa</h4>
            <span>
                Berhasil diimport: <strong>{{ session('import_success')['success_count'] }}</strong> siswa.<br>
                Tidak diimport (sudah ada di database): <strong>{{ session('import_success')['skip_count'] }}</strong> siswa.
            </span>
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

<div class="card mt-2">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                <input type="text" id="search_siswa" class="form-control form-control-solid w-250px ps-14" placeholder="Cari siswa..." />
            </div>
        </div>
        <div class="card-toolbar">
            <div class="d-flex gap-2">
                <a href="{{ route('siswa.download-template') }}" class="btn btn-light-success">
                    {!! theme()->getSvgIcon("icons/duotune/files/fil021.svg", "svg-icon-2") !!}
                    Download Template
                </a>
                <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#modal_import_siswa">
                    {!! theme()->getSvgIcon("icons/duotune/files/fil022.svg", "svg-icon-2") !!}
                    Import Excel
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_tambah_siswa">
                    {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-2") !!}
                    Tambah Siswa
                </button>
            </div>
        </div>
    </div>
    <div class="card-body py-4">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_siswa" data-bulk-type="siswa">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-30px">
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input select-all-checkbox" type="checkbox" />
                        </div>
                    </th>
                    <th class="min-w-80px">NIS / NISN</th>
                    <th class="min-w-150px">Nama</th>
                    <th class="min-w-90px">Jenis Kelamin</th>
                    <th class="min-w-100px">Kelas</th>
                    <th class="min-w-100px">Fingerprint ID</th>
                    <th class="min-w-150px">Akun Email</th>
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
                        <div class="fs-7 text-muted">NISN: {{ $item->nisn ?? '-' }}</div>
                    </td>
                    <td class="d-flex align-items-center">
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-3">
                            <div class="symbol-label fs-4 bg-light-{{ $item->jenis_kelamin === 'L' ? 'primary' : 'danger' }} text-{{ $item->jenis_kelamin === 'L' ? 'primary' : 'danger' }} fw-bolder">
                                {{ substr($item->nama, 0, 1) }}
                            </div>
                        </div>
                        <a href="{{ theme()->getPageUrl('absensi/profil-siswa') }}?id={{ $item->id }}" class="text-gray-800 text-hover-primary">{{ $item->nama }}</a>
                    </td>
                    <td>{{ $item->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    <td>
                        <span>{{ $item->kelas->nama ?? 'Belum Ada' }}</span>
                        <div class="fs-7 text-muted">{{ $item->kelas->jurusan->kode ?? '' }}</div>
                    </td>
                    <td>
                        @if($item->fingerprint_id)
                            <span class="badge badge-light-success fw-bolder">{{ $item->fingerprint_id }}</span>
                        @else
                            <span class="badge badge-light-warning fw-bolder">Belum Registrasi</span>
                        @endif
                    </td>
                    <td>
                        <span class="text-gray-600 fs-7">{{ $item->user->email ?? '-' }}</span>
                    </td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="{{ theme()->getPageUrl('absensi/profil-siswa') }}?id={{ $item->id }}" class="menu-link px-3">
                                    Detail
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
                                   data-fingerprint="{{ $item->fingerprint_id ?? '' }}">
                                    Ubah
                                </a>
                            </div>
                            <div class="menu-item px-3">
                                <form action="{{ route('siswa.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus siswa ini? Akun login terkait juga akan dihapus.')">
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

                <form class="form" action="{{ route('siswa.store') }}" method="POST">
                    @csrf
                    
                    <!-- Step 1: User Account -->
                    <div id="tambah_siswa_step_1">
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Email (Gmail)</label>
                            <input type="email" name="email" class="form-control form-control-solid" placeholder="Contoh: ahmad@gmail.com" required />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Password</label>
                            <input type="password" name="password" class="form-control form-control-solid" placeholder="Masukkan password (minimal 6 karakter)" required />
                        </div>
                    </div>

                    <!-- Step 2: Student Profile -->
                    <div id="tambah_siswa_step_2" class="d-none">
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control form-control-solid" placeholder="Nama Lengkap Siswa" required />
                        </div>
                        <div class="row g-9 mb-7">
                            <div class="col-md-12 fv-row">
                                <label class="fw-bold fs-6 mb-2">NIS (Nomor Induk Siswa)</label>
                                <input type="text" name="nis" class="form-control form-control-solid" placeholder="Nomor Induk Siswa" />
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
                                <select name="kelas_id" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modal_tambah_siswa" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}">{{ $k->tingkat }} {{ $k->nama }} ({{ $k->jurusan->kode }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control form-control-solid" />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control form-control-solid" rows="3" placeholder="Alamat lengkap siswa"></textarea>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="text-center pt-10 border-top mt-5">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal" id="tambah_siswa_btn_cancel">Batal</button>
                        <button type="button" class="btn btn-light-primary me-3 d-none" id="tambah_siswa_btn_prev">Sebelumnya</button>
                        <button type="button" class="btn btn-primary" id="tambah_siswa_btn_next">Berikutnya</button>
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
                <form class="form" action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-9">
                        <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                            {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg") !!}
                        </span>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-bold">
                                <h4 class="text-gray-900 fw-bolder">Template Excel</h4>
                                <div class="fs-6 text-gray-700">
                                    Silakan unduh template excel terlebih dahulu untuk mengisi data siswa dengan benar.
                                    <a href="{{ route('siswa.download-template') }}" class="fw-bolder text-primary">Download Template Excel</a>
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
                        <button type="submit" class="btn btn-success">Mulai Import</button>
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
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">NISN</label>
                            <input type="text" name="nisn" class="form-control form-control-solid" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">NIS</label>
                            <input type="text" name="nis" class="form-control form-control-solid" />
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
                                    <option value="{{ $k->id }}">{{ $k->tingkat }} {{ $k->nama }} ({{ $k->jurusan->kode }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control form-control-solid" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">ID Fingerprint</label>
                            <input type="text" name="fingerprint_id" class="form-control form-control-solid" placeholder="Contoh: FP001" />
                        </div>
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
        background-color: #f5f8fa !important;
    }
</style>

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#kt_table_siswa').DataTable({ 
        dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', 
        info:true, 
        order:[], 
        pageLength:5, 
        lengthChange:true, 
        columnDefs:[{orderable:false,targets:[0,7]}] 
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

    function showStep(step) {
        if (step === 1) {
            $('#tambah_siswa_step_1').removeClass('d-none');
            $('#tambah_siswa_step_2').addClass('d-none');
            
            $('#tambah_siswa_btn_cancel').removeClass('d-none');
            $('#tambah_siswa_btn_prev').addClass('d-none');
            $('#tambah_siswa_btn_next').removeClass('d-none');
            $('#tambah_siswa_btn_submit').addClass('d-none');
            
            // Stepper nav colors
            $('#tambah_siswa_stepper [data-step="1"] .stepper-number').removeClass('bg-light-primary text-primary').addClass('bg-primary text-white');
            $('#tambah_siswa_stepper [data-step="2"] .stepper-number').removeClass('bg-primary text-white').addClass('bg-light-primary text-primary');
        } else if (step === 2) {
            $('#tambah_siswa_step_1').addClass('d-none');
            $('#tambah_siswa_step_2').removeClass('d-none');
            
            $('#tambah_siswa_btn_cancel').addClass('d-none');
            $('#tambah_siswa_btn_prev').removeClass('d-none');
            $('#tambah_siswa_btn_next').addClass('d-none');
            $('#tambah_siswa_btn_submit').removeClass('d-none');

            // Stepper nav colors
            $('#tambah_siswa_stepper [data-step="1"] .stepper-number').removeClass('bg-primary text-white').addClass('bg-light-primary text-primary');
            $('#tambah_siswa_stepper [data-step="2"] .stepper-number').removeClass('bg-light-primary text-primary').addClass('bg-primary text-white');
        }
        currentStep = step;
    }

    $('#tambah_siswa_btn_next').on('click', function() {
        // Validate step 1 fields
        var email = $('#modal_tambah_siswa input[name="email"]').val();
        var password = $('#modal_tambah_siswa input[name="password"]').val();

        if (!email || !password) {
            Swal.fire({
                text: 'Silakan isi email dan password terlebih dahulu.',
                icon: 'error',
                buttonsStyling: false,
                confirmButtonText: 'Oke, mengerti!',
                customClass: {
                    confirmButton: 'btn btn-primary'
                }
            });
            return;
        }

        // Basic email format validation
        if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            Swal.fire({
                text: 'Format email tidak valid.',
                icon: 'error',
                buttonsStyling: false,
                confirmButtonText: 'Oke, mengerti!',
                customClass: {
                    confirmButton: 'btn btn-primary'
                }
            });
            return;
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
            return;
        }

        showStep(2);
    });

    $('#tambah_siswa_btn_prev').on('click', function() {
        showStep(1);
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
        
        $('#modal_ubah_siswa').modal('show');
    });
});
</script>
@endsection
</x-base-layout>
