<x-base-layout>
@include('pages.absensi._partials.toolbar')



<div class="card mt-2">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                <input type="text" id="search_mapel" class="form-control form-control-solid w-250px ps-14" placeholder="Cari mata pelajaran..." />
            </div>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_tambah_mapel">
                {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-2") !!} Tambah Mata Pelajaran
            </button>
        </div>
    </div>
    <div class="card-body py-4">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_mapel">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-30px">No</th>
                    <th class="min-w-80px">Kode</th>
                    <th class="min-w-200px">Nama Mata Pelajaran</th>
                    <th class="min-w-100px">Tingkat</th>
                    <th class="text-end min-w-70px">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                <tr>
                    <td>1</td><td>MTK</td><td>Matematika</td><td>X, XI, XII</td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#modal_ubah_mapel">Ubah</a></div>
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger">Hapus</a></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>2</td><td>BIN</td><td>Bahasa Indonesia</td><td>X, XI, XII</td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#modal_ubah_mapel">Ubah</a></div>
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger">Hapus</a></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>3</td><td>FIS</td><td>Fisika</td><td>X, XI IPA, XII IPA</td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#modal_ubah_mapel">Ubah</a></div>
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger">Hapus</a></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modal_tambah_mapel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header"><h2 class="fw-bolder">Tambah Mata Pelajaran</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body mx-5 my-7">
                <form class="form">
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Kode</label><input type="text" class="form-control form-control-solid" placeholder="Kode mapel" /></div>
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Nama Mata Pelajaran</label><input type="text" class="form-control form-control-solid" placeholder="Nama mata pelajaran" /></div>
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Tingkat</label><input type="text" class="form-control form-control-solid" placeholder="Contoh: X, XI, XII" /></div>
                    <div class="text-center pt-5"><button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Ubah -->
<div class="modal fade" id="modal_ubah_mapel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header"><h2 class="fw-bolder">Ubah Mata Pelajaran</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body mx-5 my-7">
                <form class="form">
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Kode</label><input type="text" class="form-control form-control-solid" value="MTK" /></div>
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Nama Mata Pelajaran</label><input type="text" class="form-control form-control-solid" value="Matematika" /></div>
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Tingkat</label><input type="text" class="form-control form-control-solid" value="X, XI, XII" /></div>
                    <div class="text-center pt-5"><button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#kt_table_mapel').DataTable({ dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', info:false, order:[], pageLength:10, lengthChange:false, columnDefs:[{orderable:false,targets:[0,4]}] });
    $('#search_mapel').on('keyup', function() { table.search(this.value).draw(); });
});
</script>
@endsection
</x-base-layout>
