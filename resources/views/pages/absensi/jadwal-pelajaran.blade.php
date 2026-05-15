<x-base-layout>
@include('pages.absensi._partials.toolbar')



<div class="card mt-2">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                <input type="text" id="search_jadwal" class="form-control form-control-solid w-250px ps-14" placeholder="Cari jadwal..." />
            </div>
        </div>
        <div class="card-toolbar">
            <div class="me-3">
                <span class="badge badge-light-success fs-7 fw-bold px-4 py-3">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-4 me-1") !!}
                    Tahun Ajaran Aktif: <strong>2025/2026 - Genap</strong>
                </span>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_tambah_jadwal">
                {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-2") !!} Tambah Jadwal
            </button>
        </div>
    </div>
    <div class="card-body py-4">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_jadwal">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-30px">No</th>
                    <th class="min-w-100px">Tahun Ajaran</th>
                    <th class="min-w-80px">Semester</th>
                    <th class="min-w-80px">Kelas</th>
                    <th class="min-w-150px">Mata Pelajaran</th>
                    <th class="min-w-120px">Guru</th>
                    <th class="min-w-200px">Hari, Jam Mulai – Selesai</th>
                    <th class="text-end min-w-70px">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                <tr>
                    <td>1</td><td>2025/2026</td><td>Genap</td><td>X IPA 1</td>
                    <td>Matematika</td><td>Budi Santoso, S.Pd</td>
                    <td><span class="badge badge-light-info fw-bolder">Senin, 07:30 – 09:00</span></td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#modal_ubah_jadwal">Ubah</a></div>
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger">Hapus</a></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>2</td><td>2025/2026</td><td>Genap</td><td>XI IPS 1</td>
                    <td>Bahasa Indonesia</td><td>Siti Rahayu, M.Pd</td>
                    <td><span class="badge badge-light-info fw-bolder">Selasa, 09:15 – 10:45</span></td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#modal_ubah_jadwal">Ubah</a></div>
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger">Hapus</a></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Jadwal -->
<div class="modal fade" id="modal_tambah_jadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header"><h2 class="fw-bolder">Tambah Jadwal Pelajaran</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body mx-5 my-7">
                <form class="form">
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Tahun Ajaran</label><select class="form-select form-select-solid fw-bolder"><option>2025/2026</option></select></div>
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Semester</label><select class="form-select form-select-solid fw-bolder"><option>Genap</option><option>Ganjil</option></select></div>
                    </div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Kelas</label><select class="form-select form-select-solid fw-bolder"><option>Pilih kelas...</option><option>X IPA 1</option><option>XI IPS 1</option></select></div>
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Mata Pelajaran</label><select class="form-select form-select-solid fw-bolder"><option>Pilih mapel...</option><option>Matematika</option><option>Bahasa Indonesia</option></select></div>
                    </div>
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Guru</label><select class="form-select form-select-solid fw-bolder"><option>Pilih guru...</option><option>Budi Santoso, S.Pd</option><option>Siti Rahayu, M.Pd</option></select></div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-4 fv-row"><label class="required fw-bold fs-6 mb-2">Hari</label><select class="form-select form-select-solid fw-bolder"><option>Senin</option><option>Selasa</option><option>Rabu</option><option>Kamis</option><option>Jumat</option></select></div>
                        <div class="col-md-4 fv-row"><label class="required fw-bold fs-6 mb-2">Jam Mulai</label><input type="time" class="form-control form-control-solid" /></div>
                        <div class="col-md-4 fv-row"><label class="required fw-bold fs-6 mb-2">Jam Selesai</label><input type="time" class="form-control form-control-solid" /></div>
                    </div>
                    <div class="text-center pt-5"><button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Ubah Jadwal -->
<div class="modal fade" id="modal_ubah_jadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header"><h2 class="fw-bolder">Ubah Jadwal Pelajaran</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body mx-5 my-7">
                <form class="form">
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Tahun Ajaran</label><select class="form-select form-select-solid fw-bolder"><option selected>2025/2026</option></select></div>
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Semester</label><select class="form-select form-select-solid fw-bolder"><option selected>Genap</option><option>Ganjil</option></select></div>
                    </div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Kelas</label><select class="form-select form-select-solid fw-bolder"><option selected>X IPA 1</option><option>XI IPS 1</option></select></div>
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Mata Pelajaran</label><select class="form-select form-select-solid fw-bolder"><option selected>Matematika</option><option>Bahasa Indonesia</option></select></div>
                    </div>
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Guru</label><select class="form-select form-select-solid fw-bolder"><option selected>Budi Santoso, S.Pd</option><option>Siti Rahayu, M.Pd</option></select></div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-4 fv-row"><label class="required fw-bold fs-6 mb-2">Hari</label><select class="form-select form-select-solid fw-bolder"><option selected>Senin</option><option>Selasa</option><option>Rabu</option></select></div>
                        <div class="col-md-4 fv-row"><label class="required fw-bold fs-6 mb-2">Jam Mulai</label><input type="time" class="form-control form-control-solid" value="07:30" /></div>
                        <div class="col-md-4 fv-row"><label class="required fw-bold fs-6 mb-2">Jam Selesai</label><input type="time" class="form-control form-control-solid" value="09:00" /></div>
                    </div>
                    <div class="text-center pt-5"><button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#kt_table_jadwal').DataTable({ dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', info:false, order:[], pageLength:10, lengthChange:false, columnDefs:[{orderable:false,targets:[0,7]}] });
    $('#search_jadwal').on('keyup', function() { table.search(this.value).draw(); });
});
</script>
@endsection
</x-base-layout>
