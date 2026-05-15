<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => '
        <a href="' . theme()->getPageUrl('absensi/master/kelas/data') . '" class="btn btn-sm btn-light">
            ' . theme()->getSvgIcon("icons/duotune/arrows/arr063.svg", "svg-icon-4") . ' Kembali
        </a>'
])


<div class="d-flex flex-column flex-lg-row mt-2">
    <div class="flex-column flex-lg-row-auto w-lg-250px mb-10">
        <div class="card mb-5">
            <div class="card-body pt-15">
                <div class="d-flex flex-center flex-column mb-5">
                    <div class="symbol symbol-100px symbol-circle mb-7">
                        <div class="symbol-label fs-1 bg-light-primary text-primary fw-bolder">X</div>
                    </div>
                    <span class="fs-3 text-gray-800 fw-bolder mb-1">X IPA 1</span>
                    <div class="fs-5 fw-bold text-muted mb-6">Tahun Ajaran 2025/2026</div>
                    <div class="d-flex flex-wrap flex-center">
                        <div class="border border-dashed rounded min-w-90px py-3 px-4 mx-2 mb-3">
                            <div class="fs-6 fw-bolder text-gray-700">32</div>
                            <div class="fw-bold text-gray-400 fs-7">Total Siswa</div>
                        </div>
                        <div class="border border-dashed rounded min-w-90px py-3 px-4 mx-2 mb-3">
                            <div class="fs-6 fw-bolder text-gray-700">89%</div>
                            <div class="fw-bold text-gray-400 fs-7">Rata-rata Hadir</div>
                        </div>
                    </div>
                </div>
                <div class="separator"></div>
                <div class="collapse show">
                    <div class="pb-5 fs-6">
                        <div class="fw-bolder mt-5">Tingkat</div>
                        <div class="text-gray-600">Kelas 10</div>
                        <div class="fw-bolder mt-5">Jurusan</div>
                        <div class="text-gray-600">Ilmu Pengetahuan Alam</div>
                        <div class="fw-bolder mt-5">Wali Kelas</div>
                        <div class="text-gray-600">Budi Santoso, S.Pd</div>
                        <div class="fw-bolder mt-5">Status</div>
                        <div class="text-gray-600"><span class="badge badge-light-success fw-bolder">Aktif</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-lg-row-fluid ms-lg-15">
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title"><h3 class="fw-bolder">Daftar Siswa</h3></div>
            </div>
            <div class="card-body py-4">
                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed mb-9 p-6">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg", "svg-icon-2tx svg-icon-primary me-4") !!}
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-bold">
                            <h4 class="text-gray-900 fw-bolder">Halaman Profil Kelas</h4>
                            <div class="fs-6 text-gray-700">Halaman ini akan menampilkan daftar siswa, rekap kehadiran, dan jadwal pelajaran kelas ini secara lengkap.</div>
                        </div>
                    </div>
                </div>
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_siswa_kelas">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="w-30px">No</th>
                            <th class="min-w-100px">NISN</th>
                            <th class="min-w-150px">Nama</th>
                            <th class="min-w-90px">Jenis Kelamin</th>
                            <th class="min-w-100px">Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        <tr>
                            <td>1</td><td>1234567890</td>
                            <td class="d-flex align-items-center"><div class="symbol symbol-circle symbol-35px overflow-hidden me-3"><div class="symbol-label fs-6 bg-light-danger text-danger fw-bolder">A</div></div><span>Ahmad Subarjo</span></td>
                            <td>Laki-laki</td><td><span class="badge badge-light-success fw-bolder">92%</span></td>
                        </tr>
                        <tr>
                            <td>2</td><td>1234567891</td>
                            <td class="d-flex align-items-center"><div class="symbol symbol-circle symbol-35px overflow-hidden me-3"><div class="symbol-label fs-6 bg-light-warning text-warning fw-bolder">B</div></div><span>Bela Puspita</span></td>
                            <td>Perempuan</td><td><span class="badge badge-light-success fw-bolder">97%</span></td>
                        </tr>
                        <tr>
                            <td>3</td><td>1234567892</td>
                            <td class="d-flex align-items-center"><div class="symbol symbol-circle symbol-35px overflow-hidden me-3"><div class="symbol-label fs-6 bg-light-primary text-primary fw-bolder">C</div></div><span>Candra Wijaya</span></td>
                            <td>Laki-laki</td><td><span class="badge badge-light-warning fw-bolder">78%</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    $('#kt_table_siswa_kelas').DataTable({ dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', info:false, order:[], pageLength:10, lengthChange:false, columnDefs:[{orderable:false,targets:0}] });
});
</script>
@endsection
</x-base-layout>
