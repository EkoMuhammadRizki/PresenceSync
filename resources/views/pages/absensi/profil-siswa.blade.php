<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => '
        <a href="' . theme()->getPageUrl('absensi/master/siswa') . '" class="btn btn-sm btn-light me-2">
            ' . theme()->getSvgIcon("icons/duotune/arrows/arr063.svg", "svg-icon-4") . ' Kembali
        </a>'
])

<!--begin::Back Button-->
<div class="mb-5 mt-2">
    <a href="{{ theme()->getPageUrl('absensi/master/siswa') }}" class="btn btn-light-primary btn-sm">
        {!! theme()->getSvgIcon("icons/duotune/arrows/arr063.svg", "svg-icon-4") !!}
        Kembali ke Daftar Siswa
    </a>
</div>

<!--begin::Layout-->
<div class="d-flex flex-column flex-lg-row">
    <!--begin::Sidebar-->
    <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
        <!--begin::Card-->
        <div class="card mb-5 mb-xl-8">
            <div class="card-body pt-15">
                <!--begin::Summary-->
                <div class="d-flex flex-center flex-column mb-5">
                    <!--begin::Avatar-->
                    <div class="symbol symbol-100px symbol-circle mb-7">
                        <div class="symbol-label fs-1 bg-light-{{ ($siswa && $siswa->jenis_kelamin === 'P') ? 'danger' : 'primary' }} text-{{ ($siswa && $siswa->jenis_kelamin === 'P') ? 'danger' : 'primary' }} fw-bolder">
                            {{ $siswa ? substr($siswa->nama, 0, 1) : '?' }}
                        </div>
                    </div>
                    <!--end::Avatar-->
                    <span class="fs-3 text-gray-800 text-hover-primary fw-bolder mb-1">{{ $siswa ? $siswa->nama : 'Belum Ada Data' }}</span>
                    <div class="fs-5 fw-bold text-muted mb-6">
                        @if($siswa)
                            {{ $siswa->nisn ? 'NISN: '.$siswa->nisn : ($siswa->nis ? 'NIS: '.$siswa->nis : '-') }}
                        @else
                            -
                        @endif
                    </div>
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
                        <div class="text-gray-600">{{ $siswa && $siswa->kelas ? $siswa->kelas->tingkat . ' ' . $siswa->kelas->nama : 'Belum Masuk Kelas' }}</div>
                        <div class="fw-bolder mt-5">Wali Kelas</div>
                        <div class="text-gray-600">{{ $siswa && $siswa->kelas && $siswa->kelas->guru ? $siswa->kelas->guru->nama : '-' }}</div>
                        <div class="fw-bolder mt-5">Jenis Kelamin</div>
                        <div class="text-gray-600">{{ $siswa ? ($siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan') : '-' }}</div>
                        <div class="fw-bolder mt-5">Tanggal Lahir</div>
                        <div class="text-gray-600">{{ $siswa && $siswa->tanggal_lahir ? (\Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d M Y')) : '-' }}</div>
                        <div class="fw-bolder mt-5">Status</div>
                        <div class="text-gray-600"><span class="badge badge-light-{{ ($siswa && $siswa->status === 'nonaktif') ? 'danger' : 'success' }} fw-bolder">{{ $siswa ? ucfirst($siswa->status ?? 'aktif') : '-' }}</span></div>
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
        <!--begin::Tabs Navigation-->
        <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-bold mb-8">
            <li class="nav-item">
                <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_user_view_overview_tab">Riwayat Kehadiran</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_user_view_edit_tab">Edit Profil</a>
            </li>
        </ul>
        <!--end::Tabs Navigation-->

        <!--begin::Tab Content-->
        <div class="tab-content">
            <!--begin::Overview Tab-->
            <div class="tab-pane fade show active" id="kt_user_view_overview_tab" role="tabpanel">
                <!--begin::Card - Riwayat Kehadiran-->
                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title"><h3 class="fw-bolder">Riwayat Kehadiran</h3></div>
                        <div class="card-toolbar">
                            <select class="form-select form-select-solid form-select-sm fw-bolder w-200px" data-control="select2">
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
            <!--end::Overview Tab-->

            <!--begin::Edit Profil Tab-->
            <div class="tab-pane fade" id="kt_user_view_edit_tab" role="tabpanel">
                <!--begin::Card-->
                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title"><h3 class="fw-bolder">Edit Informasi Profil</h3></div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('profil-siswa.update', $siswa->id) }}" method="POST" id="form_edit_profil_siswa">
                            @csrf
                            @method('PUT')

                            <div class="row g-9 mb-8">
                                <!-- Nama -->
                                <div class="col-md-6 fv-row">
                                    <label class="required fs-6 fw-bold mb-2">Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control form-control-solid" value="{{ old('nama', $siswa->nama) }}" 
                                        {{ $userRole === 'siswa' ? 'readonly' : '' }} required />
                                </div>
                                <!-- NISN -->
                                <div class="col-md-3 fv-row">
                                    <label class="fs-6 fw-bold mb-2">NISN</label>
                                    <input type="text" name="nisn" class="form-control form-control-solid" value="{{ old('nisn', $siswa->nisn) }}" 
                                        {{ $userRole === 'siswa' ? 'readonly' : '' }} />
                                </div>
                                <!-- NIS -->
                                <div class="col-md-3 fv-row">
                                    <label class="fs-6 fw-bold mb-2">NIS</label>
                                    <input type="text" name="nis" class="form-control form-control-solid" value="{{ old('nis', $siswa->nis) }}" 
                                        {{ $userRole === 'siswa' ? 'readonly' : '' }} />
                                </div>
                            </div>

                            <div class="row g-9 mb-8">
                                <!-- Kelas -->
                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-bold mb-2">Kelas</label>
                                    <select name="kelas_id" class="form-select form-select-solid" data-control="select2">
                                        <option value="">Pilih Kelas...</option>
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id }}" {{ old('kelas_id', $siswa->kelas_id) == $k->id ? 'selected' : '' }}>
                                                {{ $k->tingkat }} {{ $k->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Jenis Kelamin -->
                                <div class="col-md-6 fv-row">
                                    <label class="required fs-6 fw-bold mb-2">Jenis Kelamin</label>
                                    @if ($userRole === 'siswa')
                                        <input type="text" class="form-control form-control-solid" value="{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}" readonly />
                                    @else
                                        <select name="jenis_kelamin" class="form-select form-select-solid">
                                            <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    @endif
                                </div>
                            </div>

                            <div class="row g-9 mb-8">
                                <!-- Tempat Lahir -->
                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-bold mb-2">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" class="form-control form-control-solid" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}" 
                                        {{ $userRole === 'siswa' ? 'readonly' : '' }} />
                                </div>
                                <!-- Tanggal Lahir -->
                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-bold mb-2">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" class="form-control form-control-solid" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('Y-m-d') : '') }}" 
                                        {{ $userRole === 'siswa' ? 'readonly' : '' }} />
                                </div>
                            </div>

                            <div class="row g-9 mb-8">
                                <!-- Nama Orang Tua -->
                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-bold mb-2">Nama Orang Tua / Wali</label>
                                    <input type="text" name="nama_orang_tua" class="form-control form-control-solid" value="{{ old('nama_orang_tua', $siswa->nama_orang_tua) }}" />
                                </div>
                                <!-- No HP -->
                                <div class="col-md-3 fv-row">
                                    <label class="fs-6 fw-bold mb-2">No. Telepon Siswa</label>
                                    <input type="text" name="no_hp" class="form-control form-control-solid" value="{{ old('no_hp', $siswa->no_hp) }}" />
                                </div>
                                <!-- No HP Orang Tua -->
                                <div class="col-md-3 fv-row">
                                    <label class="fs-6 fw-bold mb-2">No. Telepon Orang Tua</label>
                                    <input type="text" name="no_hp_orang_tua" class="form-control form-control-solid" value="{{ old('no_hp_orang_tua', $siswa->no_hp_orang_tua) }}" />
                                </div>
                            </div>

                            <div class="fv-row mb-8">
                                <!-- Alamat -->
                                <label class="fs-6 fw-bold mb-2">Alamat</label>
                                <textarea name="alamat" class="form-control form-control-solid" rows="3">{{ old('alamat', $siswa->alamat) }}</textarea>
                            </div>

                            <div class="row g-9 mb-8">
                                <!-- Fingerprint ID -->
                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-bold mb-2">Fingerprint ID (Sama dengan ID User)</label>
                                    <input type="text" name="fingerprint_id" class="form-control form-control-solid" value="{{ old('fingerprint_id', $siswa->fingerprint_id) }}" 
                                        readonly />
                                </div>
                                <!-- Status -->
                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-bold mb-2">Status Keaktifan</label>
                                    @if ($userRole === 'siswa')
                                        <input type="text" class="form-control form-control-solid" value="{{ ucfirst($siswa->status ?? 'aktif') }}" readonly />
                                    @else
                                        <select name="status" class="form-select form-select-solid">
                                            <option value="aktif" {{ old('status', $siswa->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                            <option value="nonaktif" {{ old('status', $siswa->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                        </select>
                                    @endif
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" id="btn_save_profil_siswa">
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
        </div>
        <!--end::Tab Content-->
    </div>
    <!--end::Content-->
</div>
<!--end::Layout-->

@section('scripts')
<script>
$(document).ready(function() {
    $('#kt_table_riwayat').DataTable({ 
        dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', 
        info:true, 
        order:[], 
        pageLength:5, 
        lengthChange:true, 
        columnDefs:[{orderable:false,targets:0}] 
    });

    // Handle SweetAlert2 Confirmation for updating profile
    $('#form_edit_profil_siswa').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            icon: 'question',
            title: 'Konfirmasi',
            text: 'Simpan perubahan profil siswa?',
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
