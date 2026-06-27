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
    <div class="alert alert-success d-flex align-items-center p-5 mb-10">
        <span class="svg-icon svg-icon-2hx svg-icon-success me-4">
            {!! theme()->getSvgIcon("icons/duotune/general/gen048.svg") !!}
        </span>
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-dark">Import Berhasil</h4>
            <span>
                Berhasil diimport: <strong>{{ session('import_success')['success_count'] }}</strong> guru.<br>
                Tidak diimport (sudah ada di database): <strong>{{ session('import_success')['skip_count'] }}</strong> guru.
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
                <a href="{{ route('guru.download-template') }}" class="btn btn-light-success btn-sm btn-md-md">
                    {!! theme()->getSvgIcon("icons/duotune/files/fil021.svg", "svg-icon-2") !!}
                    <span class="d-none d-sm-inline">Download Template</span>
                    <span class="d-inline d-sm-none">Template</span>
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
                        <th class="min-w-150px">NIP</th>
                        <th class="min-w-200px">Nama</th>
                        <th class="min-w-150px">Email</th>
                        <th class="min-w-120px">No HP</th>
                        <th class="min-w-150px">Wali Kelas</th>
                        <th class="min-w-120px">Mata Pelajaran</th>
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
                        <td><strong>{{ $item->nip ?? '-' }}</strong></td>
                        <td class="d-flex align-items-center border-0">
                            <div class="symbol symbol-circle symbol-40px overflow-hidden me-3">
                                <div class="symbol-label fs-4 bg-light-primary text-primary fw-bolder">
                                    {{ substr($item->nama, 0, 1) }}
                                </div>
                            </div>
                            <a href="{{ theme()->getPageUrl('absensi/profil-guru') }}?id={{ $item->id }}" class="text-gray-800 text-hover-primary">{{ $item->nama }}</a>
                        </td>
                        <td>{{ $item->email ?? '-' }}</td>
                        <td>{{ $item->no_hp ?? '-' }}</td>
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
                                       data-nohp="{{ $item->no_hp ?? '' }}"
                                       data-alamat="{{ $item->alamat ?? '' }}">
                                        Ubah
                                    </a>
                                </div>
                                <div class="menu-item px-3">
                                    <form action="{{ route('guru.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus guru ini?')">
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
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Tambah Guru</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <!-- Stepper Nav -->
                <div class="d-flex justify-content-center mb-9 border-bottom pb-5" id="tambah_guru_stepper">
                    <div class="d-flex align-items-center me-10" data-step="1">
                        <span class="stepper-number bg-primary text-white p-2 rounded-circle fw-bold me-2 d-flex align-items-center justify-content-center" style="width:30px; height:30px;">1</span>
                        <span class="stepper-title fw-bolder text-gray-800">Akun User</span>
                    </div>
                    <div class="d-flex align-items-center" data-step="2">
                        <span class="stepper-number bg-light-primary text-primary p-2 rounded-circle fw-bold me-2 d-flex align-items-center justify-content-center" style="width:30px; height:30px;">2</span>
                        <span class="stepper-title fw-bolder text-gray-800">Profil Guru</span>
                    </div>
                </div>

                <form class="form" action="{{ route('guru.store') }}" method="POST">
                    @csrf

                    <!-- Step 1: User Account -->
                    <div id="tambah_guru_step_1">
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Email (Gmail / Email Resmi)</label>
                            <input type="email" name="email" class="form-control form-control-solid" placeholder="Contoh: budi.santoso@sekolah.sch.id" required />
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

                    <!-- Step 2: Guru Profile -->
                    <div id="tambah_guru_step_2" class="d-none">
                        <div class="fv-row mb-7">
                            <label class="required fw-bold fs-6 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control form-control-solid" placeholder="Nama Lengkap Beserta Gelar" required />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">NIP</label>
                            <input type="text" name="nip" class="form-control form-control-solid" placeholder="NIP Resmi Guru" />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">No. HP (WhatsApp)</label>
                            <input type="text" name="no_hp" class="form-control form-control-solid" placeholder="Contoh: 081234567890" />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="fw-bold fs-6 mb-2">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control form-control-solid" rows="3" placeholder="Alamat tinggal saat ini"></textarea>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="text-center pt-10 border-top mt-5">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal" id="tambah_guru_btn_cancel">Batal</button>
                        <button type="button" class="btn btn-light-primary me-3 d-none" id="tambah_guru_btn_prev">Sebelumnya</button>
                        <button type="button" class="btn btn-primary" id="tambah_guru_btn_next">Berikutnya</button>
                        <button type="submit" class="btn btn-success d-none" id="tambah_guru_btn_submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import Guru -->
<div class="modal fade" id="modal_import_guru" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-550px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Import Data Guru</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6 mb-7">
                    <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg") !!}
                    </span>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-bold">
                            <h4 class="text-gray-800 fw-bolder">Petunjuk Import</h4>
                            <div class="fs-6 text-gray-600">
                                <ul class="ps-4 mb-0">
                                    <li>Gunakan template Excel yang telah disediakan.</li>
                                    <li>Kolom: <strong>Nama, NIP, Email, No HP, Alamat</strong>.</li>
                                    <li>Guru dengan NIP or Email yang sudah ada akan dilewati (tidak digandakan).</li>
                                    <li>Format file: <strong>.xlsx</strong> atau <strong>.xls</strong>.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <form action="{{ route('guru.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">File Excel</label>
                        <input type="file" name="file" id="file_import_guru"
                            class="form-control form-control-solid"
                            accept=".xlsx,.xls" required />
                        <div class="form-text text-muted">Format yang diterima: .xlsx, .xls</div>
                    </div>
                    <div class="text-center pt-5">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            {!! theme()->getSvgIcon("icons/duotune/files/fil022.svg", "svg-icon-2") !!}
                            Upload & Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ubah Guru -->
<div class="modal fade" id="modal_ubah_guru" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Ubah Guru</h2>
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
                            <label class="fw-bold fs-6 mb-2">NIP</label>
                            <input type="text" name="nip" class="form-control form-control-solid" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">Email</label>
                            <input type="email" name="email" class="form-control form-control-solid" />
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">No. HP (WhatsApp)</label>
                        <input type="text" name="no_hp" class="form-control form-control-solid" />
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
    #kt_table_guru tbody tr {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    #kt_table_guru tbody tr:hover {
        background-color: #f5f8fa !important;
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
        columnDefs:[{orderable:false,targets:[0,7]}] 
    });

    // Make entire table row clickable (excluding checkbox and actions)
    $('#kt_table_guru').on('click', 'tbody tr', function(e) {
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
        if (step === 1) {
            $('#tambah_guru_step_1').removeClass('d-none');
            $('#tambah_guru_step_2').addClass('d-none');
            
            $('#tambah_guru_btn_cancel').removeClass('d-none');
            $('#tambah_guru_btn_prev').addClass('d-none');
            $('#tambah_guru_btn_next').removeClass('d-none');
            $('#tambah_guru_btn_submit').addClass('d-none');
            
            // Stepper nav colors
            $('#tambah_guru_stepper [data-step="1"] .stepper-number').removeClass('bg-light-primary text-primary').addClass('bg-primary text-white');
            $('#tambah_guru_stepper [data-step="2"] .stepper-number').removeClass('bg-primary text-white').addClass('bg-light-primary text-primary');
        } else if (step === 2) {
            $('#tambah_guru_step_1').addClass('d-none');
            $('#tambah_guru_step_2').removeClass('d-none');
            
            $('#tambah_guru_btn_cancel').addClass('d-none');
            $('#tambah_guru_btn_prev').removeClass('d-none');
            $('#tambah_guru_btn_next').addClass('d-none');
            $('#tambah_guru_btn_submit').removeClass('d-none');

            // Stepper nav colors
            $('#tambah_guru_stepper [data-step="1"] .stepper-number').removeClass('bg-primary text-white').addClass('bg-light-primary text-primary');
            $('#tambah_guru_stepper [data-step="2"] .stepper-number').removeClass('bg-light-primary text-primary').addClass('bg-primary text-white');
        }
        currentStep = step;
    }

    $('#tambah_guru_btn_next').on('click', function() {
        // Validate step 1 fields
        var email = $('#modal_tambah_guru input[name="email"]').val();
        var password = $('#modal_tambah_guru input[name="password"]').val();

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

    $('#tambah_guru_btn_prev').on('click', function() {
        showStep(1);
    });

    // When modal is shown
    $('#modal_tambah_guru').on('show.bs.modal', function () {
        // Reset the form
        var form = $(this).find('form')[0];
        if (form) form.reset();
        
        showStep(1);
    });

    // Use event delegation so edit button works on all pages and after searching/sorting
    $(document).on('click', '.btn-edit', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var nip = $(this).data('nip');
        var email = $(this).data('email');
        var nohp = $(this).data('nohp');
        var alamat = $(this).data('alamat');
        
        var form = $('#modal_ubah_guru form');
        form.attr('action', '{{ url("absensi/master/guru") }}/' + id);
        form.find('input[name="nama"]').val(nama);
        form.find('input[name="nip"]').val(nip);
        form.find('input[name="email"]').val(email);
        form.find('input[name="no_hp"]').val(nohp);
        form.find('textarea[name="alamat"]').val(alamat);
        
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
