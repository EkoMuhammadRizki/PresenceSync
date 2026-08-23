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

@if(session('import_success'))
    <div class="alert bg-light-primary border border-primary alert-dismissible fade show p-5 mb-10 position-relative">
        <div class="d-flex align-items-center mb-3 pe-8">
            <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
                {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg") !!}
            </span>
            <div class="d-flex flex-column flex-grow-1">
                <h4 class="mb-1 text-primary">Hasil Import Mata Pelajaran</h4>
                <span class="text-gray-700 fs-6">
                    Berhasil diproses/diimport: <strong class="text-success">{{ session('import_success')['success_count'] }}</strong> mata pelajaran
                    @if(!empty(session('import_success')['skip_count']) && session('import_success')['skip_count'] > 0)
                        | Dilewati: <strong class="text-warning">{{ session('import_success')['skip_count'] }}</strong> baris
                    @endif
                </span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

        @if(!empty(session('import_success')['imported_names']))
            <div class="mt-4 pt-3 border-top border-primary border-opacity-25">
                <a class="btn btn-sm btn-light-success fw-bolder mb-2" data-bs-toggle="collapse" href="#collapseMapelImported" role="button" aria-expanded="false">
                    <i class="bi bi-check-circle me-1"></i> Lihat Daftar yang Berhasil Diimport ({{ session('import_success')['success_count'] }} Matpel)
                </a>
                <div class="collapse show" id="collapseMapelImported">
                    <div class="card card-body bg-white border border-success border-opacity-25 py-3 px-4 mt-2" style="max-height: 200px; overflow-y: auto;">
                        <ul class="list-unstyled mb-0">
                            @foreach(session('import_success')['imported_names'] as $idx => $m)
                                <li class="py-1 border-bottom border-gray-200 d-flex justify-content-between align-items-center fs-7">
                                    <span class="text-gray-800 fw-bold">{{ $idx + 1 }}. {{ $m['nama'] }}</span>
                                    <div>
                                        <span class="badge badge-light-primary me-1">Kode: {{ $m['kode'] }}</span>
                                        <span class="badge badge-light-info">Tingkat: {{ $m['tingkat'] }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
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

<div class="card mt-2">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                <input type="text" id="search_mapel" class="form-control form-control-solid w-250px ps-14" placeholder="Cari mata pelajaran..." />
            </div>
        </div>
        <div class="card-toolbar">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('mata-pelajaran.download-template', ['empty' => 1]) }}" class="btn btn-light-warning btn-sm btn-md-md">
                    {!! theme()->getSvgIcon("icons/duotune/files/fil021.svg", "svg-icon-2") !!}
                    <span class="d-none d-sm-inline">Download Template</span>
                    <span class="d-inline d-sm-none">Template</span>
                </a>
                <button type="button" class="btn btn-light-primary btn-sm btn-md-md" data-bs-toggle="modal" data-bs-target="#modal_import_mapel">
                    {!! theme()->getSvgIcon("icons/duotune/files/fil022.svg", "svg-icon-2") !!}
                    <span class="d-none d-sm-inline">Import Excel</span>
                    <span class="d-inline d-sm-none">Import</span>
                </button>
                <button type="button" class="btn btn-primary btn-sm btn-md-md" data-bs-toggle="modal" data-bs-target="#modal_tambah_mapel">
                    {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-2") !!}
                    <span>Tambah Mata Pelajaran</span>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body py-4">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_mapel" data-bulk-type="mata-pelajaran">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-30px">
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input select-all-checkbox" type="checkbox" />
                        </div>
                    </th>
                    <th class="min-w-80px">Kode</th>
                    <th class="min-w-200px">Nama Mata Pelajaran</th>
                    <th class="min-w-100px">Tingkat</th>
                    <th class="min-w-150px">Guru Pengampu</th>
                    <th class="text-end min-w-70px">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @foreach ($mataPelajarans as $i => $mp)
                <tr>
                    <td>
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input select-item-checkbox" type="checkbox" value="{{ $mp->id }}" />
                        </div>
                    </td>
                    <td><strong>{{ $mp->kode }}</strong></td>
                    <td><a href="{{ route('mata-pelajaran.show', $mp->id) }}" class="text-gray-800 text-hover-primary">{{ $mp->nama }}</a></td>
                    <td>
                        @php
                            $badgeColor = 'primary';
                            if ($mp->tingkat == '11') $badgeColor = 'info';
                            elseif ($mp->tingkat == '12') $badgeColor = 'success';
                            elseif ($mp->tingkat == 'Semua' || !$mp->tingkat) $badgeColor = 'dark';
                        @endphp
                        <span class="badge badge-light-{{ $badgeColor }} fw-bolder py-1 px-2">
                            {{ $mp->tingkat && $mp->tingkat !== 'Semua' ? 'Tingkat ' . $mp->tingkat : 'Semua Tingkat' }}
                        </span>
                    </td>
                    <td>{{ $mp->guru->nama ?? 'Belum Ditentukan' }}</td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="{{ route('mata-pelajaran.show', $mp->id) }}" class="menu-link px-3">Detail</a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 btn-edit"
                                   data-id="{{ $mp->id }}"
                                   data-kode="{{ $mp->kode }}"
                                   data-nama="{{ $mp->nama }}"
                                   data-tingkat="{{ $mp->tingkat ?? '' }}"
                                   data-guru="{{ $mp->guru_id ?? '' }}">
                                    Ubah
                                </a>
                            </div>
                            <div class="menu-item px-3">
                                <form action="{{ route('mata-pelajaran.destroy', $mp->id) }}" method="POST" class="d-inline form-konfirmasi">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="menu-link px-3 text-danger border-0 bg-transparent w-100 text-start">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modal_tambah_mapel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Tambah Mata Pelajaran</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form class="form" action="{{ route('mata-pelajaran.store') }}" method="POST">
                    @csrf
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Kode</label>
                        <input type="text" name="kode" class="form-control form-control-solid" placeholder="Misal : BIN-10 / BIN-11 / BIN-12" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Nama Mata Pelajaran</label>
                        <input type="text" name="nama" class="form-control form-control-solid" placeholder="Nama mata pelajaran" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Tingkat</label>
                        <select name="tingkat" class="form-select form-select-solid" required>
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="10">Tingkat 10 (Kelas X)</option>
                            <option value="11">Tingkat 11 (Kelas XI)</option>
                            <option value="12">Tingkat 12 (Kelas XII)</option>
                            <option value="Semua">Semua Tingkat (Umum)</option>
                        </select>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">Guru Pengampu</label>
                        <select name="guru_id" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modal_tambah_mapel">
                            <option value="">-- Pilih Guru (Opsional) --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
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

<!-- Modal Ubah -->
<div class="modal fade" id="modal_ubah_mapel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Ubah Mata Pelajaran</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form class="form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Kode</label>
                        <input type="text" name="kode" class="form-control form-control-solid" placeholder="Misal : BIN-10 / BIN-11 / BIN-12" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Nama Mata Pelajaran</label>
                        <input type="text" name="nama" class="form-control form-control-solid" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Tingkat</label>
                        <select name="tingkat" class="form-select form-select-solid" required>
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="10">Tingkat 10 (Kelas X)</option>
                            <option value="11">Tingkat 11 (Kelas XI)</option>
                            <option value="12">Tingkat 12 (Kelas XII)</option>
                            <option value="Semua">Semua Tingkat (Umum)</option>
                        </select>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">Guru Pengampu</label>
                        <select name="guru_id" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modal_ubah_mapel">
                            <option value="">-- Pilih Guru (Opsional) --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
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

<!-- Modal Import Mata Pelajaran -->
<div class="modal fade" id="modal_import_mapel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-600px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Import Mata Pelajaran</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form id="form_import_mapel" class="form" action="{{ route('mata-pelajaran.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-5 mb-7">
                        <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                            {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg") !!}
                        </span>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-bold">
                                <h5 class="text-gray-900 fw-bolder mb-1">Format Template Excel</h5>
                                <div class="fs-7 text-gray-700">
                                    <ul class="ps-4 mb-0">
                                        <li>Kolom template: <strong>kd_matpel</strong>, <strong>nama_matpel</strong>, <strong>tingkat</strong> (10 / 11 / 12).</li>
                                        <li>Guru pengampu tidak perlu diisi di Excel karena ditentukan oleh admin pada sistem.</li>
                                        <li><a href="{{ route('mata-pelajaran.download-template', ['empty' => 1]) }}" class="fw-bolder text-primary text-decoration-underline">Download Template Excel (.xlsx)</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Pilih File Excel (.xlsx / .xls / .csv)</label>
                        <input type="file" name="file" class="form-control form-control-solid" accept=".xlsx, .xls, .csv" required />
                    </div>

                    <div class="text-center pt-5">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            {!! theme()->getSvgIcon("icons/duotune/files/fil022.svg", "svg-icon-2") !!}
                            Mulai Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    #kt_table_mapel tbody tr {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    #kt_table_mapel tbody tr:hover {
        background-color: var(--bs-table-hover-bg) !important;
    }
</style>

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#kt_table_mapel').DataTable({ 
        dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', 
        info:true, 
        order:[], 
        pageLength:10, 
        lengthMenu:[[10, 25, 50, -1], [10, 25, 50, "Semua"]],
        lengthChange:true, 
        columnDefs:[{orderable:false,targets:[0,5]}] 
    });
    $('#search_mapel').on('keyup', function() { 
        table.search(this.value).draw(); 
    });

    // Row click → navigate to subject profile
    $('#kt_table_mapel').on('click', 'tbody tr', function(e) {
        var targetTd = $(e.target).closest('td');
        if (targetTd.length === 0) return;
        var idx = targetTd.index();
        if (idx === 0 || idx === 5 || $(e.target).closest('.menu').length || $(e.target).closest('[data-kt-menu-trigger]').length) {
            return;
        }
        var link = $(this).find('td:nth-child(3) a');
        if (link.length) {
            window.location.href = link.attr('href');
        }
    });

    // Re-init Metronic menu instances on table redraw (pagination, search, sort)
    table.on('draw', function() {
        if (window.KTMenu) {
            KTMenu.createInstances();
        }
    });

    // Use event delegation so edit button works on all pages and after searching/sorting
    $(document).on('click', '.btn-edit', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var kode = $(this).data('kode');
        var nama = $(this).data('nama');
        var tingkat = $(this).data('tingkat');
        var guru = $(this).data('guru');
        
        var form = $('#modal_ubah_mapel form');
        form.attr('action', '{{ url("absensi/master/mata-pelajaran") }}/' + id);
        form.find('input[name="kode"]').val(kode);
        form.find('input[name="nama"]').val(nama);
        form.find('select[name="tingkat"]').val(tingkat);
        form.find('select[name="guru_id"]').val(guru).trigger('change');
        
        $('#modal_ubah_mapel').modal('show');
    });
});
</script>
@endsection
</x-base-layout>
