<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => '
        <a href="' . theme()->getPageUrl('absensi/master/siswa') . '" class="btn btn-sm btn-light me-2">
            ' . theme()->getSvgIcon("icons/duotune/arrows/arr063.svg", "svg-icon-4") . ' Kembali
        </a>'
])


<!--begin::Layout-->
<div class="d-flex flex-column flex-lg-row mt-2">
    <!--begin::Sidebar-->
    <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
        <!--begin::Card-->
        <div class="card mb-5 mb-xl-8">
            <div class="card-body pt-15">
                <!--begin::Summary-->
                <div class="d-flex flex-center flex-column mb-5">
                    <!--begin::Avatar-->
                    <div class="symbol symbol-100px symbol-circle mb-7">
                        <div class="symbol-label fs-1 bg-light-danger text-danger fw-bolder">A</div>
                    </div>
                    <!--end::Avatar-->
                    <span class="fs-3 text-gray-800 text-hover-primary fw-bolder mb-1">Ahmad Subarjo</span>
                    <div class="fs-5 fw-bold text-muted mb-6">NISN: 1234567890</div>
                    <div class="d-flex flex-wrap flex-center">
                        <div class="border border-dashed rounded min-w-90px py-3 px-4 mx-2 mb-3">
                            <div class="fs-6 fw-bolder text-gray-700">92%</div>
                            <div class="fw-bold text-gray-400 fs-7">Kehadiran</div>
                        </div>
                        <div class="border border-dashed rounded min-w-90px py-3 px-4 mx-2 mb-3">
                            <div class="fs-6 fw-bolder text-gray-700">3</div>
                            <div class="fw-bold text-gray-400 fs-7">Tidak Hadir</div>
                        </div>
                        <div class="border border-dashed rounded min-w-90px py-3 px-4 mx-2 mb-3">
                            <div class="fs-6 fw-bolder text-gray-700">2</div>
                            <div class="fw-bold text-gray-400 fs-7">Terlambat</div>
                        </div>
                    </div>
                </div>
                <!--end::Summary-->
                <div class="separator"></div>
                <!--begin::Details-->
                <div id="kt_user_view_details" class="collapse show">
                    <div class="pb-5 fs-6">
                        <div class="fw-bolder mt-5">Kelas</div>
                        <div class="text-gray-600">X IPA 1</div>
                        <div class="fw-bolder mt-5">Jurusan</div>
                        <div class="text-gray-600">Ilmu Pengetahuan Alam</div>
                        <div class="fw-bolder mt-5">Wali Kelas</div>
                        <div class="text-gray-600">Budi Santoso, S.Pd</div>
                        <div class="fw-bolder mt-5">Jenis Kelamin</div>
                        <div class="text-gray-600">Laki-laki</div>
                        <div class="fw-bolder mt-5">Tanggal Lahir</div>
                        <div class="text-gray-600">10 Maret 2008</div>
                        <div class="fw-bolder mt-5">Status</div>
                        <div class="text-gray-600"><span class="badge badge-light-success fw-bolder">Aktif</span></div>
                    </div>
                </div>
                <!--end::Details-->
            </div>
        </div>
        <!--end::Card-->
    </div>
    <!--end::Sidebar-->

    <!--begin::Content-->
    <div class="flex-lg-row-fluid ms-lg-15">
        <!--begin::Card - Riwayat Kehadiran-->
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title"><h3 class="fw-bolder">Riwayat Kehadiran</h3></div>
                <div class="card-toolbar">
                    <select class="form-select form-select-solid form-select-sm fw-bolder w-200px">
                        <option>2025/2026 - Genap</option>
                        <option>2025/2026 - Ganjil</option>
                    </select>
                </div>
            </div>
            <div class="card-body py-4">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_riwayat">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="w-30px">No</th>
                            <th class="min-w-120px">Tanggal</th>
                            <th class="min-w-150px">Mata Pelajaran</th>
                            <th class="min-w-100px">Jam</th>
                            <th class="min-w-100px">Status</th>
                            <th class="min-w-150px">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        <tr>
                            <td>1</td><td>15 Mei 2026</td><td>Matematika</td><td>07:30 – 09:00</td>
                            <td><span class="badge badge-light-success fw-bolder">Hadir</span></td><td>-</td>
                        </tr>
                        <tr>
                            <td>2</td><td>15 Mei 2026</td><td>Bahasa Indonesia</td><td>09:15 – 10:45</td>
                            <td><span class="badge badge-light-success fw-bolder">Hadir</span></td><td>-</td>
                        </tr>
                        <tr>
                            <td>3</td><td>14 Mei 2026</td><td>Fisika</td><td>07:30 – 09:00</td>
                            <td><span class="badge badge-light-warning fw-bolder">Izin</span></td><td>Sakit, ada surat dokter</td>
                        </tr>
                        <tr>
                            <td>4</td><td>13 Mei 2026</td><td>Matematika</td><td>07:30 – 09:00</td>
                            <td><span class="badge badge-light-success fw-bolder">Hadir</span></td><td>-</td>
                        </tr>
                        <tr>
                            <td>5</td><td>12 Mei 2026</td><td>Kimia</td><td>10:00 – 11:30</td>
                            <td><span class="badge badge-light-danger fw-bolder">Alpha</span></td><td>Tidak ada keterangan</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!--end::Card-->
    </div>
    <!--end::Content-->
</div>
<!--end::Layout-->

@section('scripts')
<script>
$(document).ready(function() {
    $('#kt_table_riwayat').DataTable({ dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', info:false, order:[], pageLength:10, lengthChange:false, columnDefs:[{orderable:false,targets:0}] });
});
</script>
@endsection
</x-base-layout>
