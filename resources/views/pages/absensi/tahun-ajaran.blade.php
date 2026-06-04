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

<div class="row g-5 mt-2">
    <!--begin::Left - Tabel Tahun Ajaran-->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header border-0 pt-6">
                <div class="card-title"><h3 class="fw-bolder">Tahun Ajaran</h3></div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal_tambah_ta">
                        {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-3") !!} Tambah
                    </button>
                </div>
            </div>
            <div class="card-body py-4">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_tahun_ajaran" data-bulk-type="tahun-ajaran">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="w-30px">
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input select-all-checkbox" type="checkbox" />
                                </div>
                            </th>
                            <th>Tahun Ajaran</th>
                            <th>Bulan Mulai</th>
                            <th>Bulan Selesai</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @foreach ($tahunAjarans as $i => $ta)
                        <tr>
                            <td>
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input select-item-checkbox" type="checkbox" value="{{ $ta->id }}" />
                                </div>
                            </td>
                            <td><strong>{{ $ta->nama }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($ta->bulan_mulai)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($ta->bulan_selesai)->format('d M Y') }}</td>
                            <td>
                                @if ($ta->status === 'aktif')
                                    <span class="badge badge-light-success fw-bolder">Aktif</span>
                                @else
                                    <span class="badge badge-light-secondary fw-bolder">Selesai</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                                </a>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3 btn-edit-ta" 
                                           data-id="{{ $ta->id }}"
                                           data-nama="{{ $ta->nama }}"
                                           data-mulai="{{ $ta->bulan_mulai }}"
                                           data-selesai="{{ $ta->bulan_selesai }}"
                                           data-status="{{ $ta->status }}">
                                            Ubah
                                        </a>
                                    </div>
                                    <div class="menu-item px-3">
                                        <form action="{{ route('tahun-ajaran.destroy', $ta->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tahun ajaran ini?')">
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
    <!--end::Left-->

    <!--begin::Right - Tabel Semester-->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header border-0 pt-6">
                <div class="card-title"><h3 class="fw-bolder">Semester</h3></div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal_tambah_semester">
                        {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-3") !!} Tambah
                    </button>
                </div>
            </div>
            <div class="card-body py-4">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_semester" data-bulk-type="semester">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="w-30px">
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input select-all-checkbox" type="checkbox" />
                                </div>
                            </th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Tgl Mulai</th>
                            <th>Tgl Selesai</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @foreach ($semesters as $i => $sem)
                        <tr>
                            <td>
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input select-item-checkbox" type="checkbox" value="{{ $sem->id }}" />
                                </div>
                            </td>
                            <td>{{ $sem->tahunAjaran->nama ?? '-' }}</td>
                            <td><strong>{{ ucfirst($sem->jenis) }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($sem->tanggal_mulai)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($sem->tanggal_selesai)->format('d M Y') }}</td>
                            <td>
                                @if ($sem->status === 'aktif')
                                    <span class="badge badge-light-success fw-bolder">Aktif</span>
                                @else
                                    <span class="badge badge-light-secondary fw-bolder">Selesai</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                                </a>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3 btn-edit-semester"
                                           data-id="{{ $sem->id }}"
                                           data-ta="{{ $sem->tahun_ajaran_id }}"
                                           data-jenis="{{ $sem->jenis }}"
                                           data-mulai="{{ $sem->tanggal_mulai }}"
                                           data-selesai="{{ $sem->tanggal_selesai }}"
                                           data-status="{{ $sem->status }}">
                                            Ubah
                                        </a>
                                    </div>
                                    <div class="menu-item px-3">
                                        <form action="{{ route('semester.destroy', $sem->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semester ini?')">
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
    <!--end::Right-->
</div>

<!-- Modal Tambah Tahun Ajaran -->
<div class="modal fade" id="modal_tambah_ta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Tambah Tahun Ajaran</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form class="form" action="{{ route('tahun-ajaran.store') }}" method="POST">
                    @csrf
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Tahun Ajaran</label>
                        <input type="text" name="nama" class="form-control form-control-solid" placeholder="Contoh: 2026/2027" required />
                    </div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Bulan Mulai</label>
                            <input type="date" name="bulan_mulai" class="form-control form-control-solid" required />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Bulan Selesai</label>
                            <input type="date" name="bulan_selesai" class="form-control form-control-solid" required />
                        </div>
                    </div>
                    <div class="text-center pt-5">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ubah Tahun Ajaran -->
<div class="modal fade" id="modal_ubah_ta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Ubah Tahun Ajaran</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form class="form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Tahun Ajaran</label>
                        <input type="text" name="nama" class="form-control form-control-solid" required />
                    </div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Bulan Mulai</label>
                            <input type="date" name="bulan_mulai" class="form-control form-control-solid" required />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Bulan Selesai</label>
                            <input type="date" name="bulan_selesai" class="form-control form-control-solid" required />
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Status</label>
                        <select name="status" class="form-select form-select-solid" required>
                            <option value="aktif">Aktif</option>
                            <option value="selesai">Selesai</option>
                        </select>
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

<!-- Modal Tambah Semester -->
<div class="modal fade" id="modal_tambah_semester" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Tambah Semester</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form class="form" action="{{ route('semester.store') }}" method="POST">
                    @csrf
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" class="form-select form-select-solid fw-bolder" required>
                            <option value="">Pilih tahun ajaran...</option>
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Semester</label>
                        <select name="jenis" class="form-select form-select-solid fw-bolder" required>
                            <option value="">Pilih semester...</option>
                            <option value="ganjil">Ganjil</option>
                            <option value="genap">Genap</option>
                        </select>
                    </div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control form-control-solid" required />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control form-control-solid" required />
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Status Aktif</label>
                        <select name="status" class="form-select form-select-solid fw-bolder" required>
                            <option value="aktif">Aktif</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="text-center pt-5">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ubah Semester -->
<div class="modal fade" id="modal_ubah_semester" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Ubah Semester</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form class="form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" class="form-select form-select-solid fw-bolder" required>
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Semester</label>
                        <select name="jenis" class="form-select form-select-solid fw-bolder" required>
                            <option value="ganjil">Ganjil</option>
                            <option value="genap">Genap</option>
                        </select>
                    </div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control form-control-solid" required />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control form-control-solid" required />
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Status Aktif</label>
                        <select name="status" class="form-select form-select-solid fw-bolder" required>
                            <option value="aktif">Aktif</option>
                            <option value="selesai">Selesai</option>
                        </select>
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

@section('scripts')
<script>
$(document).ready(function() {
    $('#kt_table_tahun_ajaran').DataTable({ 
        dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', 
        info:true, 
        order:[], 
        pageLength:5, 
        lengthChange:true, 
        columnDefs:[{orderable:false,targets:[0,5]}] 
    });
    
    $('#kt_table_semester').DataTable({ 
        dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', 
        info:true, 
        order:[], 
        pageLength:5, 
        lengthChange:true, 
        columnDefs:[{orderable:false,targets:[0,6]}] 
    });

    // Edit Tahun Ajaran
    $('.btn-edit-ta').on('click', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var mulai = $(this).data('mulai');
        var selesai = $(this).data('selesai');
        var status = $(this).data('status');
        
        var form = $('#modal_ubah_ta form');
        form.attr('action', '{{ url("absensi/master/tahun-ajaran") }}/' + id);
        form.find('input[name="nama"]').val(nama);
        form.find('input[name="bulan_mulai"]').val(mulai);
        form.find('input[name="bulan_selesai"]').val(selesai);
        form.find('select[name="status"]').val(status);
        
        $('#modal_ubah_ta').modal('show');
    });

    // Edit Semester
    $('.btn-edit-semester').on('click', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var ta = $(this).data('ta');
        var jenis = $(this).data('jenis');
        var mulai = $(this).data('mulai');
        var selesai = $(this).data('selesai');
        var status = $(this).data('status');
        
        var form = $('#modal_ubah_semester form');
        form.attr('action', '{{ url("absensi/master/semester") }}/' + id);
        form.find('select[name="tahun_ajaran_id"]').val(ta);
        form.find('select[name="jenis"]').val(jenis);
        form.find('input[name="tanggal_mulai"]').val(mulai);
        form.find('input[name="tanggal_selesai"]').val(selesai);
        form.find('select[name="status"]').val(status);
        
        $('#modal_ubah_semester').modal('show');
    });
});
</script>
@endsection
</x-base-layout>
