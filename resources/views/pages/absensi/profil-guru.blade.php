<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'customBreadcrumbs' => [
        ['title' => 'Home', 'path' => 'index', 'active' => false],
        ['title' => 'Master Data', 'path' => '', 'active' => false],
        ['title' => 'Guru', 'path' => 'absensi/master/guru', 'active' => false],
        ['title' => $guru ? $guru->nama : 'Profil Guru', 'path' => '', 'active' => true],
    ],
    'toolbarActions' => '
        <a href="' . theme()->getPageUrl('absensi/master/guru') . '" class="btn btn-sm btn-light me-2">
            ' . theme()->getSvgIcon("icons/duotune/arrows/arr063.svg", "svg-icon-4") . ' Kembali ke Data Guru
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
                        <form action="{{ route('profil-guru.update', $guru->id) }}" method="POST" enctype="multipart/form-data" id="form_edit_profil_guru">
                            @csrf
                            @method('PUT')

                            <!-- Avatar Upload -->
                            <div class="row g-9 mb-8">
                                <div class="col-md-12 fv-row">
                                    <label class="fs-6 fw-bold mb-2">Foto Profil</label>
                                    <div class="image-input image-input-outline {{ !($guru->user->info->avatar ?? null) ? 'image-input-empty' : '' }}" data-kt-image-input="true" style="background-image: url({{ asset('absensi/media/avatars/blank.png') }})">
                                        <div class="image-input-wrapper w-125px h-125px" style="background-image: url({{ $guru->user->avatar_url ?? asset('absensi/media/avatars/blank.png') }}); background-size: cover; background-position: center;"></div>
                                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ubah foto">
                                            <i class="bi bi-pencil-fill fs-7"></i>
                                            <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                                            <input type="hidden" name="avatar_remove" />
                                        </label>
                                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Batalkan">
                                            <i class="bi bi-x fs-2"></i>
                                        </span>
                                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Hapus foto">
                                            <i class="bi bi-x fs-2"></i>
                                        </span>
                                    </div>
                                    <div class="form-text">Tipe file yang didukung: png, jpg, jpeg (Otomatis terkompresi).</div>
                                </div>
                            </div>

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
                                <!-- No HP -->
                                <div class="col-md-12 fv-row">
                                    <label class="fs-6 fw-bold mb-2">No. HP / Telepon</label>
                                    <input type="text" name="no_hp" class="form-control form-control-solid" value="{{ old('no_hp', $guru->no_hp) }}" inputmode="numeric" pattern="[0-9]*" maxlength="15" />
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
    // Filter non-numeric input on phone fields
    $(document).on('input', 'input[inputmode="numeric"]', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    $('#kt_table_jadwal_guru').DataTable({ 
        dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', 
        info:true, 
        order:[], 
        pageLength:10, 
        lengthMenu:[[10, 20, 50, 100, -1], [10, 20, 50, 100, "Semua"]],
        lengthChange:true, 
        columnDefs:[{orderable:false,targets:0}] 
    });

    // Image Compression untuk Avatar Upload
    var MAX_WIDTH = 800;
    var MAX_HEIGHT = 800;
    var QUALITY = 0.7;

    $('input[name="avatar"]').on('change', function(e) {
        var file = e.target.files[0];
        if (!file) return;
        if (file.size <= 100 * 1024) return;

        var reader = new FileReader();
        reader.onload = function(ev) {
            var img = new Image();
            img.onload = function() {
                var canvas = document.createElement('canvas');
                var w = img.width, h = img.height;
                if (w > MAX_WIDTH || h > MAX_HEIGHT) {
                    var ratio = Math.min(MAX_WIDTH / w, MAX_HEIGHT / h);
                    w = Math.round(w * ratio);
                    h = Math.round(h * ratio);
                }
                canvas.width = w;
                canvas.height = h;
                var ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, w, h);
                canvas.toBlob(function(blob) {
                    if (blob.size < file.size) {
                        var newFile = new File([blob], file.name, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        });
                        var dt = new DataTransfer();
                        dt.items.add(newFile);
                        e.target.files = dt.files;
                    }
                }, 'image/jpeg', QUALITY);
            };
            img.src = ev.target.result;
        };
        reader.readAsDataURL(file);
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
