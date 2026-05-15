<x-base-layout>
@include('pages.absensi._partials.toolbar')

<!--begin::Card-->
<div class="card mt-2">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                <input type="text" id="search_log" class="form-control form-control-solid w-250px ps-14" placeholder="Cari log aktivitas..." />
            </div>
        </div>
    </div>
    <div class="card-body py-4">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_log">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-30px">No</th>
                    <th class="min-w-100px">Username</th>
                    <th class="min-w-120px">Nama</th>
                    <th class="min-w-100px">Jenis Pengguna</th>
                    <th class="min-w-200px">Aktivitas</th>
                    <th class="min-w-80px">Metode</th>
                    <th class="min-w-150px">Waktu</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                <tr>
                    <td>1</td><td>admin.sekolah</td><td>Administrator</td>
                    <td><span class="badge badge-light-primary fw-bolder">Admin</span></td>
                    <td>Login ke sistem</td>
                    <td><span class="badge badge-light-success fw-bolder">POST</span></td>
                    <td>15 Mei 2026, 08:30</td>
                </tr>
                <tr>
                    <td>2</td><td>guru.budi</td><td>Budi Santoso</td>
                    <td><span class="badge badge-light-info fw-bolder">Guru</span></td>
                    <td>Melihat data kehadiran kelas X IPA 1</td>
                    <td><span class="badge badge-light-primary fw-bolder">GET</span></td>
                    <td>15 Mei 2026, 09:15</td>
                </tr>
                <tr>
                    <td>3</td><td>admin.sekolah</td><td>Administrator</td>
                    <td><span class="badge badge-light-primary fw-bolder">Admin</span></td>
                    <td>Menambah data siswa baru</td>
                    <td><span class="badge badge-light-success fw-bolder">POST</span></td>
                    <td>15 Mei 2026, 10:00</td>
                </tr>
                <tr>
                    <td>4</td><td>guru.siti</td><td>Siti Rahayu</td>
                    <td><span class="badge badge-light-info fw-bolder">Guru</span></td>
                    <td>Mengubah jadwal pelajaran Matematika</td>
                    <td><span class="badge badge-light-warning fw-bolder">PUT</span></td>
                    <td>15 Mei 2026, 11:30</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#kt_table_log').DataTable({ dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', info:false, order:[], pageLength:10, lengthChange:false, columnDefs:[{orderable:false,targets:0}] });
    $('#search_log').on('keyup', function() { table.search(this.value).draw(); });
});
</script>
@endsection
</x-base-layout>
