<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => '
        <a href="' . theme()->getPageUrl('absensi/master/guru') . '" class="btn btn-sm btn-light me-2">
            ' . theme()->getSvgIcon("icons/duotune/arrows/arr063.svg", "svg-icon-4") . ' Kembali
        </a>'
])


<div class="d-flex flex-column flex-lg-row mt-2">
    <!--begin::Sidebar-->
    <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-body pt-15">
                <div class="d-flex flex-center flex-column mb-5">
                    <div class="symbol symbol-100px symbol-circle mb-7">
                        <div class="symbol-label fs-1 bg-light-info text-info fw-bolder">B</div>
                    </div>
                    <span class="fs-3 text-gray-800 text-hover-primary fw-bolder mb-1">Budi Santoso, S.Pd</span>
                    <div class="fs-5 fw-bold text-muted mb-6">NIP: 197805102005011001</div>
                    <div class="d-flex flex-wrap flex-center">
                        <div class="border border-dashed rounded min-w-90px py-3 px-4 mx-2 mb-3">
                            <div class="fs-6 fw-bolder text-gray-700">3</div>
                            <div class="fw-bold text-gray-400 fs-7">Kelas Diampu</div>
                        </div>
                        <div class="border border-dashed rounded min-w-90px py-3 px-4 mx-2 mb-3">
                            <div class="fs-6 fw-bolder text-gray-700">12</div>
                            <div class="fw-bold text-gray-400 fs-7">Jam/Minggu</div>
                        </div>
                    </div>
                </div>
                <div class="separator"></div>
                <div class="collapse show">
                    <div class="pb-5 fs-6">
                        <div class="fw-bolder mt-5">Status Kepegawaian</div>
                        <div class="text-gray-600"><span class="badge badge-light-success fw-bolder">PNS</span></div>
                        <div class="fw-bolder mt-5">Jenis Kelamin</div>
                        <div class="text-gray-600">Laki-laki</div>
                        <div class="fw-bolder mt-5">No HP</div>
                        <div class="text-gray-600">081234567890</div>
                        <div class="fw-bolder mt-5">Email</div>
                        <div class="text-gray-600">budi@sekolah.sch.id</div>
                        <div class="fw-bolder mt-5">Alamat</div>
                        <div class="text-gray-600">Jl. Merdeka No. 10, Jakarta</div>
                        <div class="fw-bolder mt-5">Status Akun</div>
                        <div class="text-gray-600"><span class="badge badge-light-success fw-bolder">Aktif</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Sidebar-->

    <!--begin::Content-->
    <div class="flex-lg-row-fluid ms-lg-15">
        <!--begin::Card - Jadwal Mengajar-->
        <div class="card mb-5">
            <div class="card-header border-0 pt-6">
                <div class="card-title"><h3 class="fw-bolder">Jadwal Mengajar</h3></div>
                <div class="card-toolbar">
                    <select class="form-select form-select-solid form-select-sm fw-bolder w-200px">
                        <option>2025/2026 - Genap</option>
                        <option>2025/2026 - Ganjil</option>
                    </select>
                </div>
            </div>
            <div class="card-body py-4">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_jadwal_guru">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="w-30px">No</th>
                            <th class="min-w-80px">Hari</th>
                            <th class="min-w-120px">Mata Pelajaran</th>
                            <th class="min-w-80px">Kelas</th>
                            <th class="min-w-120px">Jam</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        <tr>
                            <td>1</td><td>Senin</td><td>Matematika</td><td>X IPA 1</td><td>07:30 – 09:00</td>
                        </tr>
                        <tr>
                            <td>2</td><td>Rabu</td><td>Matematika</td><td>XI IPA 2</td><td>09:15 – 10:45</td>
                        </tr>
                        <tr>
                            <td>3</td><td>Jumat</td><td>Matematika</td><td>XII IPA 1</td><td>07:30 – 09:00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!--end::Jadwal-->

        <!--begin::Card - Kelas yang Diwali-->
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title"><h3 class="fw-bolder">Kelas yang Diwali</h3></div>
            </div>
            <div class="card-body py-4">
                <div class="d-flex align-items-center mb-8">
                    <div class="symbol symbol-50px me-5">
                        <div class="symbol-label bg-light-primary text-primary fw-bolder fs-5">X</div>
                    </div>
                    <div class="flex-grow-1">
                        <span class="text-dark fw-bolder text-hover-primary fs-6">X IPA 1</span>
                        <span class="text-muted d-block fw-bold">32 Siswa • Tahun Ajaran 2025/2026</span>
                    </div>
                    <a href="{{ theme()->getPageUrl('absensi/profil-kelas') }}" class="btn btn-sm btn-light-primary">Lihat Kelas</a>
                </div>
            </div>
        </div>
    </div>
    <!--end::Content-->
</div>

@section('scripts')
<script>
$(document).ready(function() {
    $('#kt_table_jadwal_guru').DataTable({ dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', info:false, order:[], pageLength:10, lengthChange:false, columnDefs:[{orderable:false,targets:0}] });
});
</script>
@endsection
</x-base-layout>
