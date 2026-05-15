<x-base-layout>
@include('pages.absensi._partials.toolbar')



<div class="card mt-2">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                <input type="text" id="search_fp" class="form-control form-control-solid w-250px ps-14" placeholder="Cari perangkat..." />
            </div>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_tambah_fp">
                {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-2") !!} Tambah Perangkat
            </button>
        </div>
    </div>
    <div class="card-body py-4">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_fingerprint">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-30px">No</th>
                    <th class="min-w-80px">Kode</th>
                    <th class="min-w-150px">Nama Perangkat</th>
                    <th class="min-w-100px">Lokasi</th>
                    <th class="min-w-120px">IP</th>
                    <th class="min-w-60px">Port</th>
                    <th class="min-w-150px">Token API</th>
                    <th class="min-w-80px">Status</th>
                    <th class="text-end min-w-70px">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                <tr>
                    <td>1</td><td>FP-001</td><td>Fingerprint Gerbang Utama</td>
                    <td>Pintu Depan</td><td>192.168.1.101</td><td>4370</td>
                    <td><code class="text-gray-600 fs-8">a1b2c3d4e5f6...</code></td>
                    <td><span class="badge badge-light-success fw-bolder">Online</span></td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#modal_ubah_fp">Ubah</a></div>
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger">Hapus</a></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>2</td><td>FP-002</td><td>Fingerprint Lab Komputer</td>
                    <td>Gedung B Lt. 2</td><td>192.168.1.102</td><td>4370</td>
                    <td><code class="text-gray-600 fs-8">g7h8i9j0k1l2...</code></td>
                    <td><span class="badge badge-light-danger fw-bolder">Offline</span></td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#modal_ubah_fp">Ubah</a></div>
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger">Hapus</a></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Perangkat -->
<div class="modal fade" id="modal_tambah_fp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header"><h2 class="fw-bolder">Tambah Perangkat Fingerprint</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body mx-5 my-7">
                <form class="form">
                    <div class="row g-9 mb-7">
                        <div class="col-md-4 fv-row"><label class="required fw-bold fs-6 mb-2">Kode</label><input type="text" class="form-control form-control-solid" placeholder="FP-001" /></div>
                        <div class="col-md-8 fv-row"><label class="required fw-bold fs-6 mb-2">Nama Perangkat</label><input type="text" class="form-control form-control-solid" placeholder="Nama perangkat" /></div>
                    </div>
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Lokasi</label><input type="text" class="form-control form-control-solid" placeholder="Lokasi pemasangan" /></div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-8 fv-row"><label class="required fw-bold fs-6 mb-2">IP Address</label><input type="text" class="form-control form-control-solid" placeholder="192.168.1.xxx" /></div>
                        <div class="col-md-4 fv-row"><label class="required fw-bold fs-6 mb-2">Port</label><input type="number" class="form-control form-control-solid" value="4370" /></div>
                    </div>
                    <div class="fv-row mb-7"><label class="fw-bold fs-6 mb-2">Token API</label><input type="text" class="form-control form-control-solid" placeholder="Token otentikasi API" /></div>
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Status</label><select class="form-select form-select-solid fw-bolder"><option value="online">Online</option><option value="offline">Offline</option></select></div>
                    <div class="text-center pt-5"><button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Ubah Perangkat -->
<div class="modal fade" id="modal_ubah_fp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header"><h2 class="fw-bolder">Ubah Perangkat Fingerprint</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body mx-5 my-7">
                <form class="form">
                    <div class="row g-9 mb-7">
                        <div class="col-md-4 fv-row"><label class="required fw-bold fs-6 mb-2">Kode</label><input type="text" class="form-control form-control-solid" value="FP-001" /></div>
                        <div class="col-md-8 fv-row"><label class="required fw-bold fs-6 mb-2">Nama Perangkat</label><input type="text" class="form-control form-control-solid" value="Fingerprint Gerbang Utama" /></div>
                    </div>
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Lokasi</label><input type="text" class="form-control form-control-solid" value="Pintu Depan" /></div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-8 fv-row"><label class="required fw-bold fs-6 mb-2">IP Address</label><input type="text" class="form-control form-control-solid" value="192.168.1.101" /></div>
                        <div class="col-md-4 fv-row"><label class="required fw-bold fs-6 mb-2">Port</label><input type="number" class="form-control form-control-solid" value="4370" /></div>
                    </div>
                    <div class="fv-row mb-7"><label class="fw-bold fs-6 mb-2">Token API</label><input type="text" class="form-control form-control-solid" value="a1b2c3d4e5f6..." /></div>
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Status</label><select class="form-select form-select-solid fw-bolder"><option value="online" selected>Online</option><option value="offline">Offline</option></select></div>
                    <div class="text-center pt-5"><button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#kt_table_fingerprint').DataTable({ dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', info:false, order:[], pageLength:10, lengthChange:false, columnDefs:[{orderable:false,targets:[0,8]}] });
    $('#search_fp').on('keyup', function() { table.search(this.value).draw(); });
});
</script>
@endsection
</x-base-layout>
