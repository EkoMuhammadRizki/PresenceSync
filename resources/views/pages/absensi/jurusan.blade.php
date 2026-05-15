<x-base-layout>
@include('pages.absensi._partials.toolbar')



<div class="card mt-2">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                <input type="text" id="search_jurusan" class="form-control form-control-solid w-250px ps-14" placeholder="Cari jurusan..." />
            </div>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_tambah_jurusan">
                {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-2") !!}
                Tambah Jurusan
            </button>
        </div>
    </div>
    <div class="card-body py-4">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_jurusan">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-30px">No</th>
                    <th class="min-w-80px">Kode</th>
                    <th class="min-w-150px">Nama</th>
                    <th class="min-w-200px">Deskripsi</th>
                    <th class="min-w-100px">Jumlah Kelas</th>
                    <th class="text-end min-w-70px">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                <tr>
                    <td>1</td><td>IPA</td><td>Ilmu Pengetahuan Alam</td>
                    <td>Jurusan sains & eksakta</td><td>6</td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#modal_ubah_jurusan">Ubah</a></div>
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger">Hapus</a></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>2</td><td>IPS</td><td>Ilmu Pengetahuan Sosial</td>
                    <td>Jurusan sosial & humaniora</td><td>4</td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#modal_ubah_jurusan">Ubah</a></div>
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger">Hapus</a></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>3</td><td>BHS</td><td>Bahasa</td>
                    <td>Jurusan bahasa & sastra</td><td>2</td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#modal_ubah_jurusan">Ubah</a></div>
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger">Hapus</a></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Jurusan -->
<div class="modal fade" id="modal_tambah_jurusan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-550px">
        <div class="modal-content">
            <div class="modal-header"><h2 class="fw-bolder">Tambah Jurusan</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form class="form">
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Kode</label><input type="text" class="form-control form-control-solid" placeholder="Contoh: IPA" /></div>
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Nama Jurusan</label><input type="text" class="form-control form-control-solid" placeholder="Nama jurusan" /></div>
                    <div class="fv-row mb-7"><label class="fw-bold fs-6 mb-2">Deskripsi</label><textarea class="form-control form-control-solid" rows="3" placeholder="Deskripsi jurusan..."></textarea></div>
                    <div class="text-center pt-5"><button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ubah Jurusan -->
<div class="modal fade" id="modal_ubah_jurusan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-550px">
        <div class="modal-content">
            <div class="modal-header"><h2 class="fw-bolder">Ubah Jurusan</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form class="form">
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Kode</label><input type="text" class="form-control form-control-solid" value="IPA" /></div>
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Nama Jurusan</label><input type="text" class="form-control form-control-solid" value="Ilmu Pengetahuan Alam" /></div>
                    <div class="fv-row mb-7"><label class="fw-bold fs-6 mb-2">Deskripsi</label><textarea class="form-control form-control-solid" rows="3">Jurusan sains & eksakta</textarea></div>
                    <div class="text-center pt-5"><button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#kt_table_jurusan').DataTable({ dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', info:false, order:[], pageLength:10, lengthChange:false, columnDefs:[{orderable:false,targets:[0,5]}] });
    $('#search_jurusan').on('keyup', function() { table.search(this.value).draw(); });
});
</script>
@endsection
</x-base-layout>
