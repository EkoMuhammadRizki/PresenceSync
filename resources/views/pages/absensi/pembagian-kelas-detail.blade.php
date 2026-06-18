<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => '
        <a href="' . route('pembagian-kelas.index') . '" class="btn btn-sm btn-light">
            ' . theme()->getSvgIcon("icons/duotune/arrows/arr063.svg", "svg-icon-4") . ' Kembali
        </a>'
])

@if(session('success'))
    <div class="alert alert-success d-flex align-items-center p-5 mb-10">
        <span class="svg-icon svg-icon-2hx svg-icon-success me-4">
            {!! theme()->getSvgIcon("icons/duotune/general/gen048.svg") !!}
        </span>
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-dark">Sukses</h4>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
        <span class="svg-icon svg-icon-2hx svg-icon-danger me-4">
            {!! theme()->getSvgIcon("icons/duotune/general/gen040.svg") !!}
        </span>
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-dark">Error</h4>
            <span>{{ session('error') }}</span>
        </div>
    </div>
@endif

<!--begin::Back Button-->
<div class="mb-5 mt-2">
    <a href="{{ route('pembagian-kelas.index') }}" class="btn btn-light-primary btn-sm">
        {!! theme()->getSvgIcon("icons/duotune/arrows/arr063.svg", "svg-icon-4") !!}
        Kembali ke Pembagian Kelas
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
                        <div class="symbol-label fs-1 bg-light-primary text-primary fw-bolder">{{ substr($kelas->nama, 0, 1) }}</div>
                    </div>
                    <!--end::Avatar-->
                    <span class="fs-3 text-gray-800 fw-bolder mb-1">{{ $kelas->nama }}</span>
                    <div class="fs-5 fw-bold text-muted mb-6">{{ $kelas->jurusan->nama ?? '-' }}</div>
                    <div class="d-flex flex-wrap flex-center">
                        <div class="border border-dashed rounded min-w-90px py-3 px-4 mx-2 mb-3">
                            <div class="fs-6 fw-bolder text-gray-700">{{ $kelas->siswas_count }}</div>
                            <div class="fw-bold text-gray-400 fs-7">Total Siswa</div>
                        </div>
                    </div>
                </div>
                <!--end::Summary-->
                <div class="separator"></div>
                <!--begin::Details-->
                <div class="collapse show">
                    <div class="pb-5 fs-6">
                        <div class="fw-bolder mt-5">Tingkat</div>
                        <div class="text-gray-600">Kelas {{ $kelas->tingkat }}</div>
                        <div class="fw-bolder mt-5">Jurusan</div>
                        <div class="text-gray-600">{{ $kelas->jurusan->nama ?? '-' }}</div>
                        <div class="fw-bolder mt-5">Wali Kelas</div>
                        <div class="text-gray-600">{{ $kelas->guru->nama ?? 'Belum Ditentukan' }}</div>
                        <div class="fw-bolder mt-5">Status</div>
                        <div class="text-gray-600">
                            @if ($kelas->status === 'aktif')
                                <span class="badge badge-light-success fw-bolder">Aktif</span>
                            @else
                                <span class="badge badge-light-danger fw-bolder">Non Aktif</span>
                            @endif
                        </div>
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
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <!--begin::Tab Nav-->
                    <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x fs-5 fw-bolder">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_daftar_siswa">Daftar Siswa</a>
                        </li>
                    </ul>
                    <!--end::Tab Nav-->
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal_tambah_siswa_kelas">
                        {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-2") !!}
                        Tambah Siswa
                    </button>
                </div>
            </div>
            <div class="card-body py-4">
                <!--begin::Tab Content-->
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab_daftar_siswa">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_siswa_kelas">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="w-30px">No</th>
                                    <th class="min-w-100px">NIS</th>
                                    <th class="min-w-150px">Nama</th>
                                    <th class="min-w-90px">Jenis Kelamin</th>
                                    <th class="text-end min-w-70px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @foreach ($kelas->siswas as $i => $siswa)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $siswa->nis ?? '-' }}</td>
                                    <td class="d-flex align-items-center">
                                        <div class="symbol symbol-circle symbol-35px overflow-hidden me-3">
                                            <div class="symbol-label fs-6 bg-light-{{ $siswa->jenis_kelamin === 'L' ? 'primary' : 'danger' }} text-{{ $siswa->jenis_kelamin === 'L' ? 'primary' : 'danger' }} fw-bolder">
                                                {{ substr($siswa->nama, 0, 1) }}
                                            </div>
                                        </div>
                                        <span>{{ $siswa->nama }}</span>
                                    </td>
                                    <td>{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('pembagian-kelas.remove-siswa', [$kelas->id, $siswa->id]) }}" method="POST" class="d-inline form-remove-siswa">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light-danger btn-sm btn-remove-siswa">
                                                {!! theme()->getSvgIcon("icons/duotune/general/gen027.svg", "svg-icon-4") !!}
                                                Keluarkan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--end::Tab Content-->
            </div>
        </div>
    </div>
    <!--end::Content-->
</div>
<!--end::Layout-->

<!-- Modal Tambah Siswa ke Kelas -->
<div class="modal fade" id="modal_tambah_siswa_kelas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Tambah Siswa ke {{ $kelas->nama }}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form class="form" action="{{ route('pembagian-kelas.add-siswa', $kelas->id) }}" method="POST" id="form_tambah_siswa_kelas">
                    @csrf
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Pilih Siswa</label>
                        <select name="siswa_ids[]" id="select_siswa_kelas" class="form-select form-select-solid" data-dropdown-parent="#modal_tambah_siswa_kelas" multiple="multiple" data-placeholder="Cari dan pilih siswa..." style="width: 100%;">
                            @foreach($availableSiswas as $siswa)
                                <option value="{{ $siswa->id }}">{{ $siswa->nama }} {{ $siswa->nis ? '('.$siswa->nis.')' : '' }}</option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">Pilih satu atau lebih siswa yang belum memiliki kelas.</div>
                    </div>
                    <div class="text-center pt-5">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn_submit_siswa_kelas">
                            <span class="indicator-label">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    // DataTable for student list
    $('#kt_table_siswa_kelas').DataTable({
        dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>',
        info: true,
        order: [],
        pageLength: 10,
        lengthChange: true,
        columnDefs: [{orderable: false, targets: [0, 4]}]
    });

    // Initialize Select2 for multi-select
    $('#select_siswa_kelas').select2({
        dropdownParent: $('#modal_tambah_siswa_kelas'),
        placeholder: 'Cari dan pilih siswa...',
        allowClear: true,
        width: '100%'
    });

    // Form validation with SweetAlert2
    $('#form_tambah_siswa_kelas').on('submit', function(e) {
        var selected = $('#select_siswa_kelas').val();
        if (!selected || selected.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Pilih minimal satu siswa untuk ditambahkan ke kelas.',
                confirmButtonText: 'OK'
            });
            return false;
        }

        e.preventDefault();
        var form = this;
        Swal.fire({
            icon: 'question',
            title: 'Konfirmasi',
            text: 'Tambahkan ' + selected.length + ' siswa ke kelas ini?',
            showCancelButton: true,
            confirmButtonText: 'Ya, Tambahkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#009EF7',
            cancelButtonColor: '#F1416C',
        }).then(function(result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Remove siswa confirmation with SweetAlert2
    $(document).on('submit', '.form-remove-siswa', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            icon: 'warning',
            title: 'Keluarkan Siswa?',
            text: 'Siswa akan dikeluarkan dari kelas ini. Anda bisa menambahkannya kembali kapan saja.',
            showCancelButton: true,
            confirmButtonText: 'Ya, Keluarkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#F1416C',
            cancelButtonColor: '#7E8299',
        }).then(function(result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endsection
</x-base-layout>
