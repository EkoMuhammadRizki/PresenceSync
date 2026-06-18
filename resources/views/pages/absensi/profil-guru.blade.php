<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => '
        <a href="' . theme()->getPageUrl('absensi/master/guru') . '" class="btn btn-sm btn-light me-2">
            ' . theme()->getSvgIcon("icons/duotune/arrows/arr063.svg", "svg-icon-4") . ' Kembali
        </a>'
])

<!--begin::Back Button-->
<div class="mb-5 mt-2">
    <a href="{{ theme()->getPageUrl('absensi/master/guru') }}" class="btn btn-light-primary btn-sm">
        {!! theme()->getSvgIcon("icons/duotune/arrows/arr063.svg", "svg-icon-4") !!}
        Kembali ke Daftar Guru
    </a>
</div>

<div class="d-flex flex-column flex-lg-row">
    <!--begin::Sidebar-->
    <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-body pt-15">
                <div class="d-flex flex-center flex-column mb-5">
                    <div class="symbol symbol-100px symbol-circle mb-7">
                        <div class="symbol-label fs-1 bg-light-info text-info fw-bolder">
                            {{ $guru ? substr($guru->nama, 0, 1) : '?' }}
                        </div>
                    </div>
                    <span class="fs-3 text-gray-800 text-hover-primary fw-bolder mb-1">{{ $guru ? $guru->nama : 'Belum Ada Data' }}</span>
                    <div class="fs-5 fw-bold text-muted mb-6">NIP: {{ $guru && $guru->nip ? $guru->nip : '-' }}</div>
                    <div class="d-flex flex-wrap flex-center">
                        <div class="border border-dashed rounded min-w-90px py-3 px-4 mx-2 mb-3">
                            <div class="fs-6 fw-bolder text-gray-700">{{ $guru ? $guru->kelas_count : 0 }}</div>
                            <div class="fw-bold text-gray-400 fs-7">Kelas Diwali</div>
                        </div>
                        <div class="border border-dashed rounded min-w-90px py-3 px-4 mx-2 mb-3">
                            <div class="fs-6 fw-bolder text-gray-700">{{ $guru ? $guru->mata_pelajarans_count : 0 }}</div>
                            <div class="fw-bold text-gray-400 fs-7">Mapel Diampu</div>
                        </div>
                    </div>
                </div>
                <div class="separator"></div>
                <div class="collapse show">
                    <div class="pb-5 fs-6">
                        <div class="fw-bolder mt-5">Status Kepegawaian</div>
                        <div class="text-gray-600"><span class="badge badge-light-success fw-bolder">{{ $guru && $guru->nip ? 'PNS' : 'Honorer' }}</span></div>
                        <div class="fw-bolder mt-5">No HP</div>
                        <div class="text-gray-600">{{ $guru && $guru->no_hp ? $guru->no_hp : '-' }}</div>
                        <div class="fw-bolder mt-5">Email</div>
                        <div class="text-gray-600">{{ $guru && $guru->email ? $guru->email : '-' }}</div>
                        <div class="fw-bolder mt-5">Alamat</div>
                        <div class="text-gray-600">{{ $guru && $guru->alamat ? $guru->alamat : '-' }}</div>
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
        <!--begin::Tabs Navigation-->
        <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-bold mb-8">
            <li class="nav-item">
                <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_guru_view_overview_tab">Jadwal & Kelas</a>
            </li>
            @if ($userRole !== 'siswa' && $userRole !== 'orang_tua')
            <li class="nav-item">
                <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_guru_view_edit_tab">Edit Profil</a>
            </li>
            @endif
        </ul>
        <!--end::Tabs Navigation-->

        <!--begin::Tab Content-->
        <div class="tab-content">
            <!--begin::Overview Tab-->
            <div class="tab-pane fade show active" id="kt_guru_view_overview_tab" role="tabpanel">
                <!--begin::Card - Jadwal Mengajar-->
                <div class="card mb-5">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title"><h3 class="fw-bolder">Jadwal Mengajar</h3></div>
                        <div class="card-toolbar">
                            <select class="form-select form-select-solid form-select-sm fw-bolder w-200px" data-control="select2">
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
                                    <td>1</td><td>Senin</td><td>Matematika</td><td>X-1</td><td>07:30 – 09:00</td>
                                </tr>
                                <tr>
                                    <td>2</td><td>Rabu</td><td>Matematika</td><td>XI-1</td><td>09:15 – 10:45</td>
                                </tr>
                                <tr>
                                    <td>3</td><td>Jumat</td><td>Matematika</td><td>XII-1</td><td>07:30 – 09:00</td>
                                </tr>
                                <tr>
                                    <td>4</td><td>Kamis</td><td>Matematika</td><td>X-1</td><td>10:45 – 12:15</td>
                                </tr>
                                <tr>
                                    <td>5</td><td>Selasa</td><td>Matematika</td><td>XI-1</td><td>07:30 – 09:00</td>
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
                                <span class="text-dark fw-bolder text-hover-primary fs-6">X-1</span>
                                <span class="text-muted d-block fw-bold">32 Siswa • Tahun Ajaran 2025/2026</span>
                            </div>
                            <a href="{{ theme()->getPageUrl('absensi/profil-kelas') }}" class="btn btn-sm btn-light-primary">Lihat Kelas</a>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Overview Tab-->

            @if ($userRole !== 'siswa' && $userRole !== 'orang_tua')
            <!--begin::Edit Profil Tab-->
            <div class="tab-pane fade" id="kt_guru_view_edit_tab" role="tabpanel">
                <!--begin::Card-->
                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title"><h3 class="fw-bolder">Edit Informasi Profil</h3></div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('profil-guru.update', $guru->id) }}" method="POST" id="form_edit_profil_guru">
                            @csrf
                            @method('PUT')

                            <div class="row g-9 mb-8">
                                <!-- Nama -->
                                <div class="col-md-6 fv-row">
                                    <label class="required fs-6 fw-bold mb-2">Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control form-control-solid" value="{{ old('nama', $guru->nama) }}" required />
                                </div>
                                <!-- NIP -->
                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-bold mb-2">NIP</label>
                                    <input type="text" name="nip" class="form-control form-control-solid" value="{{ old('nip', $guru->nip) }}" />
                                </div>
                            </div>

                            <div class="row g-9 mb-8">
                                <!-- Email -->
                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-bold mb-2">Email</label>
                                    <input type="email" name="email" class="form-control form-control-solid" value="{{ old('email', $guru->email) }}" />
                                </div>
                                <!-- No HP -->
                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-bold mb-2">No. HP / Telepon</label>
                                    <input type="text" name="no_hp" class="form-control form-control-solid" value="{{ old('no_hp', $guru->no_hp) }}" />
                                </div>
                            </div>

                            <div class="fv-row mb-8">
                                <!-- Alamat -->
                                <label class="fs-6 fw-bold mb-2">Alamat</label>
                                <textarea name="alamat" class="form-control form-control-solid" rows="3">{{ old('alamat', $guru->alamat) }}</textarea>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" id="btn_save_profil_guru">
                                    {!! theme()->getSvgIcon("icons/duotune/general/gen043.svg", "svg-icon-2") !!}
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!--end::Card-->
            </div>
            <!--end::Edit Profil Tab-->
            @endif
        </div>
        <!--end::Tab Content-->
    </div>
    <!--end::Content-->
</div>

@section('scripts')
<script>
$(document).ready(function() {
    $('#kt_table_jadwal_guru').DataTable({ 
        dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', 
        info:true, 
        order:[], 
        pageLength:5, 
        lengthChange:true, 
        columnDefs:[{orderable:false,targets:0}] 
    });

    // Handle SweetAlert2 Confirmation for updating guru profile
    $('#form_edit_profil_guru').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            icon: 'question',
            title: 'Konfirmasi',
            text: 'Simpan perubahan profil guru?',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#009EF7',
            cancelButtonColor: '#7E8299'
        }).then(function(result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{{ session('success') }}',
        confirmButtonText: 'OK',
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-primary'
        }
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: '{{ session('error') }}',
        confirmButtonText: 'Tutup',
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-danger'
        }
    });
</script>
@endif

@if ($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Validasi Gagal',
        html: '<ul class="text-start">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
        confirmButtonText: 'Perbaiki',
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-danger'
        }
    });
</script>
@endif
@endsection
</x-base-layout>
