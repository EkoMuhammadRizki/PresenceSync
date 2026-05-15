<x-base-layout>
@include('pages.absensi._partials.toolbar')



<div class="card mt-2">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                <input type="text" id="search_siswa" class="form-control form-control-solid w-250px ps-14" placeholder="Cari siswa..." />
            </div>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-primary">
                {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-2") !!}
                Tambah Siswa
            </button>
        </div>
    </div>
    <div class="card-body py-4">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_siswa">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-30px">No</th>
                    <th class="min-w-100px">NISN</th>
                    <th class="min-w-150px">Nama</th>
                    <th class="min-w-90px">Jenis Kelamin</th>
                    <th class="min-w-100px">Jurusan</th>
                    <th class="min-w-80px">Kelas</th>
                    <th class="min-w-120px">Wali Kelas</th>
                    <th class="min-w-80px">Status</th>
                    <th class="text-end min-w-70px">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                <tr>
                    <td>1</td>
                    <td>1234567890</td>
                    <td class="d-flex align-items-center">
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-3">
                            <div class="symbol-label fs-4 bg-light-danger text-danger fw-bolder">A</div>
                        </div>
                        <span>Ahmad Subarjo</span>
                    </td>
                    <td>Laki-laki</td>
                    <td>IPA</td>
                    <td>X IPA 1</td>
                    <td>Budi Santoso</td>
                    <td><span class="badge badge-light-success fw-bolder">Aktif</span></td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="{{ theme()->getPageUrl('absensi/profil-siswa') }}" class="menu-link px-3">Detail</a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 text-danger">Hapus</a>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>0987654321</td>
                    <td class="d-flex align-items-center">
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-3">
                            <div class="symbol-label fs-4 bg-light-primary text-primary fw-bolder">S</div>
                        </div>
                        <span>Sari Dewi</span>
                    </td>
                    <td>Perempuan</td>
                    <td>IPS</td>
                    <td>XI IPS 1</td>
                    <td>Siti Rahayu</td>
                    <td><span class="badge badge-light-success fw-bolder">Aktif</span></td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="{{ theme()->getPageUrl('absensi/profil-siswa') }}" class="menu-link px-3">Detail</a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 text-danger">Hapus</a>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>1122334455</td>
                    <td class="d-flex align-items-center">
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-3">
                            <div class="symbol-label fs-4 bg-light-warning text-warning fw-bolder">R</div>
                        </div>
                        <span>Rizky Pratama</span>
                    </td>
                    <td>Laki-laki</td>
                    <td>IPA</td>
                    <td>XII IPA 2</td>
                    <td>Dewi Lestari</td>
                    <td><span class="badge badge-light-warning fw-bolder">Pindah</span></td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="{{ theme()->getPageUrl('absensi/profil-siswa') }}" class="menu-link px-3">Detail</a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 text-danger">Hapus</a>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#kt_table_siswa').DataTable({ dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', info:false, order:[], pageLength:10, lengthChange:false, columnDefs:[{orderable:false,targets:[0,8]}] });
    $('#search_siswa').on('keyup', function() { table.search(this.value).draw(); });
});
</script>
@endsection
</x-base-layout>
