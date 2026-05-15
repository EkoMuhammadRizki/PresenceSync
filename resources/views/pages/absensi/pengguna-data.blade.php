<x-base-layout>
@include('pages.absensi._partials.toolbar')


<!--begin::Card-->
<div class="card">
    <!--begin::Card header-->
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                <input type="text" id="search_pengguna" class="form-control form-control-solid w-250px ps-14" placeholder="Cari pengguna..." />
            </div>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_tambah_pengguna">
                {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-2") !!}
                Tambah Pengguna
            </button>
        </div>
    </div>
    <!--end::Card header-->

    <!--begin::Card body-->
    <div class="card-body py-4">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_pengguna">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-30px">No</th>
                    <th class="min-w-100px">Username</th>
                    <th class="min-w-150px">Email</th>
                    <th class="min-w-100px">Jenis Pengguna</th>
                    <th class="min-w-100px">Peran</th>
                    <th class="min-w-120px">Terakhir Login</th>
                    <th class="min-w-100px">Status Akun</th>
                    <th class="text-end min-w-70px">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                <tr>
                    <td>1</td>
                    <td>admin.sekolah</td>
                    <td>admin@sekolah.sch.id</td>
                    <td>Admin</td>
                    <td><span class="badge badge-light-primary fw-bolder">Super Admin</span></td>
                    <td>15 Mei 2026, 08:30</td>
                    <td><span class="badge badge-light-success fw-bolder">Aktif</span></td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#modal_ubah_pengguna">Ubah</a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 text-danger">Hapus</a>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>guru.budi</td>
                    <td>budi@sekolah.sch.id</td>
                    <td>Guru</td>
                    <td><span class="badge badge-light-info fw-bolder">Wali Kelas</span></td>
                    <td>14 Mei 2026, 14:15</td>
                    <td><span class="badge badge-light-success fw-bolder">Aktif</span></td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#modal_ubah_pengguna">Ubah</a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 text-danger">Hapus</a>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>siswa.ahmad</td>
                    <td>ahmad@siswa.sch.id</td>
                    <td>Siswa</td>
                    <td><span class="badge badge-light-warning fw-bolder">Siswa</span></td>
                    <td>13 Mei 2026, 07:00</td>
                    <td><span class="badge badge-light-danger fw-bolder">Nonaktif</span></td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#modal_ubah_pengguna">Ubah</a>
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
    <!--end::Card body-->
</div>
<!--end::Card-->

<!--begin::Modal Tambah Pengguna-->
<div class="modal fade" id="modal_tambah_pengguna" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Tambah Pengguna</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="form_tambah_pengguna" class="form" action="#">
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Username</label>
                        <input type="text" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Masukkan username" />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Email</label>
                        <input type="email" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Masukkan email" />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Password</label>
                        <input type="password" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Masukkan password" />
                    </div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Jenis Pengguna</label>
                            <select class="form-select form-select-solid fw-bolder">
                                <option value="">Pilih jenis...</option>
                                <option value="admin">Admin</option>
                                <option value="guru">Guru</option>
                                <option value="siswa">Siswa</option>
                            </select>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Peran</label>
                            <select class="form-select form-select-solid fw-bolder">
                                <option value="">Pilih peran...</option>
                                <option value="super_admin">Super Admin</option>
                                <option value="wali_kelas">Wali Kelas</option>
                                <option value="guru_mapel">Guru Mapel</option>
                                <option value="siswa">Siswa</option>
                            </select>
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Status Akun</label>
                        <select class="form-select form-select-solid fw-bolder">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
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
<!--end::Modal Tambah Pengguna-->

<!--begin::Modal Ubah Pengguna-->
<div class="modal fade" id="modal_ubah_pengguna" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Ubah Pengguna</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="form_ubah_pengguna" class="form" action="#">
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Username</label>
                        <input type="text" class="form-control form-control-solid mb-3 mb-lg-0" value="admin.sekolah" />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Email</label>
                        <input type="email" class="form-control form-control-solid mb-3 mb-lg-0" value="admin@sekolah.sch.id" />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">Password Baru <span class="text-muted fs-7">(kosongkan jika tidak ingin mengubah)</span></label>
                        <input type="password" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Password baru" />
                    </div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Jenis Pengguna</label>
                            <select class="form-select form-select-solid fw-bolder">
                                <option value="admin" selected>Admin</option>
                                <option value="guru">Guru</option>
                                <option value="siswa">Siswa</option>
                            </select>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Peran</label>
                            <select class="form-select form-select-solid fw-bolder">
                                <option value="super_admin" selected>Super Admin</option>
                                <option value="wali_kelas">Wali Kelas</option>
                                <option value="guru_mapel">Guru Mapel</option>
                                <option value="siswa">Siswa</option>
                            </select>
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Status Akun</label>
                        <select class="form-select form-select-solid fw-bolder">
                            <option value="aktif" selected>Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
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
<!--end::Modal Ubah Pengguna-->

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#kt_table_pengguna').DataTable({
        dom: "<'table-responsive'tr><'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'li><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>",
        info: false, order: [], pageLength: 10, lengthChange: false,
        columnDefs: [{ orderable: false, targets: [0, 7] }]
    });
    $('#search_pengguna').on('keyup', function() { table.search(this.value).draw(); });
});
</script>
@endsection

</x-base-layout>
