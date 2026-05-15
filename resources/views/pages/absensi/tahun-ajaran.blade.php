<x-base-layout>
@include('pages.absensi._partials.toolbar')


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
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_tahun_ajaran">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="w-30px">No</th>
                            <th>Tahun Ajaran</th>
                            <th>Bulan Mulai</th>
                            <th>Bulan Selesai</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        <tr>
                            <td>1</td><td>2025/2026</td><td>Juli 2025</td><td>Juni 2026</td>
                            <td><span class="badge badge-light-success fw-bolder">Aktif</span></td>
                            <td class="text-end">
                                <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                                </a>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                    <div class="menu-item px-3"><a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#modal_ubah_ta">Ubah</a></div>
                                    <div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger">Hapus</a></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td><td>2024/2025</td><td>Juli 2024</td><td>Juni 2025</td>
                            <td><span class="badge badge-light-secondary fw-bolder">Selesai</span></td>
                            <td class="text-end">
                                <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                                </a>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                    <div class="menu-item px-3"><a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#modal_ubah_ta">Ubah</a></div>
                                    <div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger">Hapus</a></div>
                                </div>
                            </td>
                        </tr>
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
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_semester">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="w-30px">No</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Tgl Mulai</th>
                            <th>Tgl Selesai</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        <tr>
                            <td>1</td><td>2025/2026</td><td>Ganjil</td><td>14 Jul 2025</td><td>20 Des 2025</td>
                            <td><span class="badge badge-light-secondary fw-bolder">Selesai</span></td>
                            <td class="text-end">
                                <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                                </a>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                    <div class="menu-item px-3"><a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#modal_ubah_semester">Ubah</a></div>
                                    <div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger">Hapus</a></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td><td>2025/2026</td><td>Genap</td><td>05 Jan 2026</td><td>20 Jun 2026</td>
                            <td><span class="badge badge-light-success fw-bolder">Aktif</span></td>
                            <td class="text-end">
                                <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                                </a>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                    <div class="menu-item px-3"><a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#modal_ubah_semester">Ubah</a></div>
                                    <div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger">Hapus</a></div>
                                </div>
                            </td>
                        </tr>
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
            <div class="modal-header"><h2 class="fw-bolder">Tambah Tahun Ajaran</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body mx-5 my-7">
                <form class="form">
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Tahun Ajaran</label><input type="text" class="form-control form-control-solid" placeholder="Contoh: 2026/2027" /></div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Bulan Mulai</label><input type="month" class="form-control form-control-solid" /></div>
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Bulan Selesai</label><input type="month" class="form-control form-control-solid" /></div>
                    </div>
                    <div class="text-center pt-5"><button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Ubah Tahun Ajaran -->
<div class="modal fade" id="modal_ubah_ta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header"><h2 class="fw-bolder">Ubah Tahun Ajaran</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body mx-5 my-7">
                <form class="form">
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Tahun Ajaran</label><input type="text" class="form-control form-control-solid" value="2025/2026" /></div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Bulan Mulai</label><input type="month" class="form-control form-control-solid" value="2025-07" /></div>
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Bulan Selesai</label><input type="month" class="form-control form-control-solid" value="2026-06" /></div>
                    </div>
                    <div class="text-center pt-5"><button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Tambah Semester -->
<div class="modal fade" id="modal_tambah_semester" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header"><h2 class="fw-bolder">Tambah Semester</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body mx-5 my-7">
                <form class="form">
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Tahun Ajaran</label><select class="form-select form-select-solid fw-bolder"><option value="">Pilih tahun ajaran...</option><option value="2025/2026" selected>2025/2026</option></select></div>
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Semester</label><select class="form-select form-select-solid fw-bolder"><option value="">Pilih semester...</option><option value="ganjil">Ganjil</option><option value="genap">Genap</option></select></div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Tanggal Mulai</label><input type="date" class="form-control form-control-solid" /></div>
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Tanggal Selesai</label><input type="date" class="form-control form-control-solid" /></div>
                    </div>
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Status Aktif</label><select class="form-select form-select-solid fw-bolder"><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option></select></div>
                    <div class="text-center pt-5"><button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Ubah Semester -->
<div class="modal fade" id="modal_ubah_semester" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header"><h2 class="fw-bolder">Ubah Semester</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body mx-5 my-7">
                <form class="form">
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Tahun Ajaran</label><select class="form-select form-select-solid fw-bolder"><option value="2025/2026" selected>2025/2026</option></select></div>
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Semester</label><select class="form-select form-select-solid fw-bolder"><option value="genap" selected>Genap</option><option value="ganjil">Ganjil</option></select></div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Tanggal Mulai</label><input type="date" class="form-control form-control-solid" value="2026-01-05" /></div>
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Tanggal Selesai</label><input type="date" class="form-control form-control-solid" value="2026-06-20" /></div>
                    </div>
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Status Aktif</label><select class="form-select form-select-solid fw-bolder"><option value="aktif" selected>Aktif</option><option value="nonaktif">Nonaktif</option></select></div>
                    <div class="text-center pt-5"><button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    $('#kt_table_tahun_ajaran').DataTable({ dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', info:false, order:[], pageLength:10, lengthChange:false, columnDefs:[{orderable:false,targets:[0,5]}] });
    $('#kt_table_semester').DataTable({ dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', info:false, order:[], pageLength:10, lengthChange:false, columnDefs:[{orderable:false,targets:[0,6]}] });
});
</script>
@endsection
</x-base-layout>
