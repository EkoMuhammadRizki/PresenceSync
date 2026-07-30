<x-base-layout>
@include('pages.absensi._partials.toolbar')

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

@if ($errors->any())
    <div class="alert alert-danger p-5 mb-10">
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-dark">Validasi Gagal</h4>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<!--begin::Card-->
<div class="card mt-2">
    <!--begin::Card header-->
    <div class="card-header border-0 pt-6">
        <!--begin::Card title-->
        <div class="card-title">
            <!--begin::Search-->
            <div class="d-flex align-items-center position-relative my-1">
                {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Cari siswa..." />
            </div>
            <!--end::Search-->
        </div>
        <!--begin::Card title-->

        <!--begin::Card toolbar-->
        <div class="card-toolbar">
            <!--begin::Toolbar-->
            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                <!--begin::Filter-->
                <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                {!! theme()->getSvgIcon("icons/duotune/general/gen031.svg", "svg-icon-2") !!}
                Filter</button>
                
                <!--begin::Menu 1-->
                <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                    <!--begin::Header-->
                    <div class="px-7 py-5">
                        <div class="fs-5 text-dark fw-bolder">Opsi Filter</div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Separator-->
                    <div class="separator border-gray-200"></div>
                    <!--end::Separator-->
                    <!--begin::Content-->
                    <div class="px-7 py-5" data-kt-user-table-filter="form">
                        <form method="GET" action="{{ route('kehadiran.index') }}">
                            <!--begin::Input group-->
                            <div class="mb-5">
                                <label class="form-label fs-6 fw-bold">Kelas:</label>
                                <select name="kelas_id" class="form-select form-select-solid fw-bolder" data-control="select2" data-placeholder="Pilih kelas" data-allow-clear="true">
                                    <option></option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Input group-->
                            
                            <!--begin::Input group-->
                            <div class="mb-10">
                                <label class="form-label fs-6 fw-bold">Tanggal:</label>
                                <input type="date" name="tanggal" class="form-control form-control-solid" value="{{ request('tanggal') }}" />
                            </div>
                            <!--end::Input group-->

                            <!--begin::Actions-->
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('kehadiran.index') }}" class="btn btn-light btn-active-light-primary fw-bold me-2 px-6">Reset</a>
                                <button type="submit" class="btn btn-primary fw-bold px-6">Apply</button>
                            </div>
                            <!--end::Actions-->
                        </form>
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Menu 1-->
                <!--end::Filter-->

                <!--begin::Add button-->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_tambah_kehadiran">
                {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-2") !!}
                Tambah Absensi</button>
                <!--end::Add button-->
            </div>
            <!--end::Toolbar-->
        </div>
        <!--end::Card toolbar-->
    </div>
    <!--end::Card header-->

    <!--begin::Card body-->
    <div class="card-body py-4">
        <!--begin::Table-->
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_kehadiran">
            <!--begin::Table head-->
            <thead>
                <!--begin::Table row-->
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-10px pe-2">
                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                            <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_kehadiran .form-check-input" value="1" />
                        </div>
                    </th>
                    <th class="min-w-125px">Nama Siswa</th>
                    <th class="min-w-125px">Kelas</th>
                    <th class="min-w-125px">Tanggal</th>
                    <th class="min-w-120px">Jam Masuk</th>
                    <th class="min-w-100px">Status</th>
                    <th class="text-end min-w-100px">Aksi</th>
                </tr>
                <!--end::Table row-->
            </thead>
            <!--end::Table head-->
            <!--begin::Table body-->
            <tbody class="text-gray-600 fw-bold">
                @foreach ($kehadirans as $i => $kh)
                <tr>
                    <td>
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" value="{{ $kh->id }}" />
                        </div>
                    </td>
                    <td class="d-flex align-items-center">
                        @php
                            $initial = strtoupper(substr($kh->siswa->nama ?? 'A', 0, 1));
                            $bgColors = ['success', 'primary', 'warning', 'danger', 'info'];
                            $bgColor = $bgColors[ord($initial) % count($bgColors)];
                        @endphp
                        <!--begin:: Avatar -->
                        <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                            <a href="{{ $kh->siswa ? route('profil-siswa.show', ['id' => $kh->siswa->id, 'back' => 'kehadiran']) : '#' }}">
                                <div class="symbol-label fs-3 bg-light-{{ $bgColor }} text-{{ $bgColor }}">{{ $initial }}</div>
                            </a>
                        </div>
                        <!--end::Avatar-->
                        <!--begin::User details-->
                        <div class="d-flex flex-column">
                            @if($kh->siswa)
                                <a href="{{ route('profil-siswa.show', ['id' => $kh->siswa->id, 'back' => 'kehadiran']) }}" class="text-gray-800 text-hover-primary mb-1">{{ $kh->siswa->nama }}</a>
                            @else
                                <span class="text-gray-800 mb-1">-</span>
                            @endif
                            <span>NIS: {{ $kh->siswa->nis ?? '-' }}</span>
                        </div>
                        <!--end::User details-->
                    </td>
                    <td>{{ $kh->siswa->kelas->nama ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($kh->tanggal)->format('d M Y') }}</td>
                    <td>
                        @if ($kh->jam_masuk)
                            <span class="badge badge-light-primary fw-bolder">{{ \Carbon\Carbon::parse($kh->jam_masuk)->format('H:i') }}</span>
                        @else
                            <span class="badge badge-light-secondary fw-bold">-</span>
                        @endif
                    </td>
                    <td>
                        @if ($kh->status === 'hadir')
                            <span class="badge badge-light-success fw-bolder">Hadir</span>
                        @elseif ($kh->status === 'terlambat')
                            <span class="badge badge-light-warning fw-bolder">Terlambat</span>
                        @elseif ($kh->status === 'sakit')
                            <span class="badge badge-light-primary fw-bolder">Sakit</span>
                        @elseif ($kh->status === 'izin')
                            <span class="badge badge-light-info fw-bolder">Izin</span>
                        @else
                            <span class="badge badge-light-danger fw-bolder">Alpha</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Aksi
                        {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}</a>
                        <!--begin::Menu-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 btn-edit"
                                   data-id="{{ $kh->id }}"
                                   data-siswa="{{ $kh->siswa->nama ?? '-' }}"
                                   data-status="{{ $kh->status }}"
                                   data-masuk="{{ $kh->jam_masuk ? \Carbon\Carbon::parse($kh->jam_masuk)->format('H:i') : '' }}"
                                   data-pulang="{{ $kh->jam_pulang ? \Carbon\Carbon::parse($kh->jam_pulang)->format('H:i') : '' }}"
                                   data-keterangan="{{ $kh->keterangan ?? '' }}">Edit</a>
                            </div>
                            <!--end::Menu item-->
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <form action="{{ route('kehadiran.destroy', $kh->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus absensi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="menu-link px-3 text-danger border-0 bg-transparent w-100 text-start">Hapus</button>
                                </form>
                            </div>
                            <!--end::Menu item-->
                        </div>
                        <!--end::Menu-->
                    </td>
                </tr>
                @endforeach
            </tbody>
            <!--end::Table body-->
        </table>
        <!--end::Table-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Card-->

<!-- Modal Tambah Absensi -->
<div class="modal fade" id="modal_tambah_kehadiran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-600px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Tambah Kehadiran Siswa</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form class="form" action="{{ route('kehadiran.store') }}" method="POST">
                    @csrf
                    
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Siswa</label>
                        <select name="siswa_id" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modal_tambah_kehadiran" data-placeholder="Pilih siswa..." required>
                            <option value="">Pilih siswa...</option>
                            @foreach ($siswas as $siswa)
                                <option value="{{ $siswa->id }}">{{ $siswa->nama }} ({{ $siswa->kelas->nama ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Semester</label>
                        <select name="semester_id" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modal_tambah_kehadiran" required>
                                @foreach ($semesters as $sem)
                                    <option value="{{ $sem->id }}" {{ $activeSemester && $activeSemester->id == $sem->id ? 'selected' : '' }}>
                                        {{ $sem->tahunAjaran->nama ?? '-' }} - {{ ucfirst($sem->jenis) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control form-control-solid" value="{{ date('Y-m-d') }}" required />
                        </div>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Status Kehadiran</label>
                        <select name="status" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modal_tambah_kehadiran" required>
                            <option value="hadir">Hadir</option>
                            <option value="terlambat">Terlambat</option>
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                            <option value="alpha">Alpha</option>
                        </select>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">Jam Masuk (Opsional)</label>
                        <input type="time" name="jam_masuk" class="form-control form-control-solid" />
                    </div>

                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">Keterangan (Opsional)</label>
                        <textarea name="keterangan" class="form-control form-control-solid" rows="3" placeholder="Alasan sakit/izin, dll..."></textarea>
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

<!-- Modal Ubah Absensi -->
<div class="modal fade" id="modal_ubah_kehadiran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-600px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Ubah Kehadiran Siswa</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form class="form" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">Nama Siswa</label>
                        <input type="text" id="edit_siswa_nama" class="form-control form-control-solid" disabled />
                    </div>

                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Status Kehadiran</label>
                        <select name="status" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modal_ubah_kehadiran" required>
                            <option value="hadir">Hadir</option>
                            <option value="terlambat">Terlambat</option>
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                            <option value="alpha">Alpha</option>
                        </select>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">Jam Masuk (Opsional)</label>
                        <input type="time" name="jam_masuk" class="form-control form-control-solid" />
                    </div>

                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">Keterangan (Opsional)</label>
                        <textarea name="keterangan" class="form-control form-control-solid" rows="3"></textarea>
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

@section('scripts')
<script>
    $(document).ready(function() {
        var table = $('#kt_table_kehadiran').DataTable({
            dom: "<'table-responsive'tr><'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'li><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>",
            "info": true,
            'order': [],
            'pageLength': 10,
            "lengthChange": true,
            'columnDefs': [
                { orderable: false, targets: 0 }, // Disable order on column 0 (checkbox)
                { orderable: false, targets: 6 }, // Disable order on column 6 (actions)
            ]
        });

        // Search handler
        $('[data-kt-user-table-filter="search"]').on('keyup', function(e) {
            table.search(e.target.value).draw();
        });

        // Edit handler
        $('.btn-edit').on('click', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var nama = $(this).data('siswa');
            var status = $(this).data('status');
            var masuk = $(this).data('masuk');
            var pulang = $(this).data('pulang');
            var keterangan = $(this).data('keterangan');

            var form = $('#modal_ubah_kehadiran form');
            form.attr('action', '{{ url("absensi/kehadiran") }}/' + id);
            $('#edit_siswa_nama').val(nama);
            form.find('select[name="status"]').val(status).trigger('change');
            form.find('input[name="jam_masuk"]').val(masuk);
            form.find('input[name="jam_pulang"]').val(pulang);
            form.find('textarea[name="keterangan"]').val(keterangan);

            $('#modal_ubah_kehadiran').modal('show');
        });
    });
</script>
@endsection
</x-base-layout>
