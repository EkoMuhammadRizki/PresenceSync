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
                    <div class="card card-body bg-white border border-success border-opacity-25 py-3 px-4 mt-2" style="max-height: 220px; overflow-y: auto;">
                        <ul class="list-unstyled mb-0">
                            @foreach(session('import_success')['imported_names'] as $idx => $m)
                                <li class="py-1 border-bottom border-gray-200 d-flex justify-content-between align-items-center fs-7">
                                    <span class="text-gray-800 fw-bold">{{ $idx + 1 }}. {{ $m['nama'] }} <span class="text-muted">({{ $m['guru'] ?? '-' }})</span></span>
                                    <div>
                                        <span class="badge badge-light-primary">Kode: {{ $m['kode'] }}</span>
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
                    <th class="min-w-180px">Guru Pengampu</th>
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
                    <td><span class="badge badge-light-primary fw-bolder fs-7">{{ $mp->kode }}</span></td>
                    <td><a href="{{ route('mata-pelajaran.show', $mp->id) }}" class="text-gray-800 text-hover-primary fw-boldest fs-6">{{ $mp->nama }}</a></td>
                    <td>
                        @if($mp->guru)
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-check-fill text-success me-2 fs-5"></i>
                                <span>{{ $mp->guru->nama }}</span>
                            </div>
                        @else
                            <span class="badge badge-light-warning">Belum Ditentukan</span>
                        @endif
                    </td>
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

{{-- Datalist Nama Mapel Umum --}}
<datalist id="list_mapel_umum">
    <option value="Biologi">
    <option value="Kimia">
    <option value="Matematika">
    <option value="Fisika">
    <option value="Informatika">
    <option value="Geografi">
    <option value="Sosiologi">
    <option value="Sejarah">
    <option value="Ekonomi">
    <option value="Bahasa Indonesia">
    <option value="Bahasa Inggris">
    <option value="Bahasa Sunda">
    <option value="Pendidikan Agama Islam dan Budi Pekerti">
    <option value="Pendidikan Jasmani, Olahraga, dan Kesehatan">
    <option value="Pendidikan Pancasila">
    <option value="Prakarya dan Kewirausahaan">
    <option value="Seni Budaya">
    <option value="Bimbingan Konseling">
</datalist>

<!-- Modal Tambah (Auto-Kode & Bersih) -->
<div class="modal fade" id="modal_tambah_mapel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-550px">
        <div class="modal-content">
            <div class="modal-header bg-light-primary py-4">
                <h3 class="fw-bolder text-primary mb-0">
                    <i class="bi bi-book-half text-primary me-2"></i> Tambah Mata Pelajaran
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-6">
                <form class="form" action="{{ route('mata-pelajaran.store') }}" method="POST" id="form_tambah_mapel">
                    @csrf

                    {{-- Nama Mapel --}}
                    <div class="fv-row mb-5">
                        <label class="required fw-bold fs-6 mb-2">Nama Mata Pelajaran</label>
                        <input type="text" name="nama" list="list_mapel_umum" class="form-control form-control-solid" placeholder="Ketik atau pilih nama mapel (misal: Biologi)" required />
                        <div class="form-text fs-8 text-muted">Pilih dari saran mapel umum atau ketik nama baru.</div>
                    </div>

                    {{-- Guru Pengampu --}}
                    <div class="fv-row mb-5">
                        <label class="fw-bold fs-6 mb-2">Guru Pengampu</label>
                        <select name="guru_id" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modal_tambah_mapel" data-placeholder="-- Cari & Pilih Guru Pengampu --">
                            <option value="">-- Pilih Guru (Opsional) --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                        <div class="form-text fs-8 text-muted">Guru yang mengajar mata pelajaran ini.</div>
                    </div>

                    {{-- Kode Mapel (Opsional) --}}
                    <div class="fv-row mb-5">
                        <label class="fw-bold fs-6 mb-2">Kode Mata Pelajaran <span class="text-muted fw-normal fs-7">(Opsional)</span></label>
                        <input type="text" name="kode" class="form-control form-control-solid" placeholder="Otomatis di-generate jika dikosongkan (misal: BIO)" />
                        <div class="form-text fs-8 text-muted">Biarkan kosong agar sistem otomatis membuat singkatan kode (misal: BIO, MAT, KIM).</div>
                    </div>

                    <div class="text-end pt-3">
                        <button type="reset" class="btn btn-light me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Simpan Mata Pelajaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ubah -->
<div class="modal fade" id="modal_ubah_mapel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-550px">
        <div class="modal-content">
            <div class="modal-header bg-light-info py-4">
                <h3 class="fw-bolder text-info mb-0">
                    <i class="bi bi-pencil-square text-info me-2"></i> Ubah Mata Pelajaran
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-6">
                <form class="form" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="fv-row mb-5">
                        <label class="required fw-bold fs-6 mb-2">Nama Mata Pelajaran</label>
                        <input type="text" name="nama" list="list_mapel_umum" class="form-control form-control-solid" required />
                    </div>

                    <div class="fv-row mb-5">
                        <label class="fw-bold fs-6 mb-2">Guru Pengampu</label>
                        <select name="guru_id" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modal_ubah_mapel">
                            <option value="">-- Pilih Guru (Opsional) --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="fv-row mb-5">
                        <label class="required fw-bold fs-6 mb-2">Kode</label>
                        <input type="text" name="kode" class="form-control form-control-solid" required />
                    </div>

                    <div class="text-end pt-3">
                        <button type="reset" class="btn btn-light me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
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
            <div class="modal-header bg-light-success py-4">
                <h3 class="fw-bolder text-success mb-0">
                    <i class="bi bi-file-earmark-spreadsheet text-success me-2"></i> Import Mata Pelajaran Excel
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-6">
                <form id="form_import_mapel" class="form" action="{{ route('mata-pelajaran.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-5 mb-7">
                        <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                            {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg") !!}
                        </span>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-bold">
                                <h5 class="text-gray-900 fw-bolder mb-1">Panduan Template Excel</h5>
                                <div class="fs-7 text-gray-700">
                                    <ul class="ps-4 mb-2">
                                        <li>Kolom template: <strong>id_matpel</strong>, <strong>kd_matpel</strong>, <strong>nama_matpel</strong>, <strong>nama_guru</strong>.</li>
                                        <li>Kolom <strong>kd_matpel</strong> boleh dikosongkan (otomatis di-generate menjadi singkatan seperti BIO, MAT, KIM).</li>
                                        <li>Kolom <strong>nama_guru</strong> otomatis dicocokkan dengan data guru yang ada di sistem.</li>
                                    </ul>
                                    <a href="{{ route('mata-pelajaran.download-template', ['empty' => 1]) }}" class="btn btn-sm btn-light-primary fw-bolder">
                                        <i class="bi bi-download me-1"></i> Download Template Kosong (.xlsx)
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Pilih File Excel (.xlsx / .xls / .csv)</label>
                        <input type="file" name="file" class="form-control form-control-solid" accept=".xlsx, .xls, .csv" required />
                    </div>

                    <div class="text-end pt-3">
                        <button type="reset" class="btn btn-light me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-upload me-1"></i> Mulai Import
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
        columnDefs:[{orderable:false,targets:[0,4]}] 
    });
    $('#search_mapel').on('keyup', function() { 
        table.search(this.value).draw(); 
    });

    // Row click → navigate to subject profile
    $('#kt_table_mapel').on('click', 'tbody tr', function(e) {
        var targetTd = $(e.target).closest('td');
        if (targetTd.length === 0) return;
        var idx = targetTd.index();
        if (idx === 0 || idx === 4 || $(e.target).closest('.menu').length || $(e.target).closest('[data-kt-menu-trigger]').length) {
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

    // Edit button modal filler
    $(document).on('click', '.btn-edit', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var kode = $(this).data('kode');
        var nama = $(this).data('nama');
        var guru = $(this).data('guru');
        
        var form = $('#modal_ubah_mapel form');
        form.attr('action', '{{ url("absensi/master/mata-pelajaran") }}/' + id);
        form.find('input[name="kode"]').val(kode);
        form.find('input[name="nama"]').val(nama);
        form.find('select[name="guru_id"]').val(guru).trigger('change');
        
        $('#modal_ubah_mapel').modal('show');
    });
});
</script>
@endsection
</x-base-layout>
