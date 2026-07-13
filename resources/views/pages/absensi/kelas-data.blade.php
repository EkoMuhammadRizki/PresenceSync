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

<div class="card mt-2">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                <input type="text" id="search_kelas" class="form-control form-control-solid w-250px ps-14" placeholder="Cari kelas..." />
            </div>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_tambah_kelas">
                {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-2") !!}
                Tambah Kelas
            </button>
        </div>
    </div>
    <div class="card-body py-4">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_kelas" data-bulk-type="kelas">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-30px">
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input select-all-checkbox" type="checkbox" />
                        </div>
                    </th>
                    <th class="min-w-120px">Nama Kelas</th>
                    <th class="min-w-80px">Tingkat</th>
                    <th class="min-w-180px">Wali Kelas</th>
                    <th class="min-w-100px">Jumlah Siswa</th>
                    <th class="min-w-80px">Status</th>
                    <th class="text-end min-w-70px">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @foreach ($kelas as $i => $item)
                <tr>
                    <td>
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input select-item-checkbox" type="checkbox" value="{{ $item->id }}" />
                        </div>
                    </td>
                    <td><a href="{{ route('kelas.show', $item->id) }}" class="text-gray-800 text-hover-primary"><strong>{{ $item->nama }}</strong></a></td>
                    <td>{{ $item->tingkat }}</td>
                    <td>{{ $item->guru->nama ?? 'Belum Ditentukan' }}</td>
                    <td>{{ $item->siswas_count ?? 0 }} siswa</td>
                    <td>
                        @if ($item->status === 'aktif')
                            <span class="badge badge-light-success fw-bolder">Aktif</span>
                        @elseif ($item->status === 'lulus')
                            <span class="badge badge-light-purple fw-bolder">Lulus</span>
                        @else
                            <span class="badge badge-light-danger fw-bolder">Non Aktif</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="{{ route('kelas.show', $item->id) }}" class="menu-link px-3">Detail</a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 btn-edit"
                                   data-id="{{ $item->id }}"
                                   data-nama="{{ $item->nama }}"
                                   data-guru="{{ $item->guru_id ?? '' }}"
                                   data-tingkat="{{ $item->tingkat }}"
                                   data-status="{{ $item->status }}">
                                    Ubah
                                </a>
                            </div>
                            <div class="menu-item px-3">
                                <form action="{{ route('kelas.destroy', $item->id) }}" method="POST" class="d-inline form-konfirmasi">
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

<!-- Modal Tambah Kelas -->
<div class="modal fade" id="modal_tambah_kelas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Tambah Kelas</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form class="form" action="{{ route('kelas.store') }}" method="POST">
                    @csrf
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Nama Kelas</label>
                        <input type="text" name="nama" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Contoh: X-1, XI-1" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Tingkat</label>
                        <select name="tingkat" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modal_tambah_kelas" required>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">Wali Kelas</label>
                        <select name="guru_id" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modal_tambah_kelas">
                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach($gurus as $g)
                                @php
                                    $isAssigned = in_array($g->id, $kelas->pluck('guru_id')->filter()->toArray());
                                @endphp
                                <option value="{{ $g->id }}" {{ $isAssigned ? 'disabled' : '' }}>
                                    {{ $g->nama }} {{ $isAssigned ? '(Sudah Menjadi Wali Kelas)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Status</label>
                        <select name="status" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modal_tambah_kelas" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
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

<!-- Modal Ubah Kelas -->
<div class="modal fade" id="modal_ubah_kelas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Ubah Kelas</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form class="form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Nama Kelas</label>
                        <input type="text" name="nama" class="form-control form-control-solid mb-3 mb-lg-0" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Tingkat</label>
                        <select name="tingkat" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modal_ubah_kelas" required>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">Wali Kelas</label>
                        <select name="guru_id" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modal_ubah_kelas">
                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach($gurus as $g)
                                @php
                                    $isAssigned = in_array($g->id, $kelas->pluck('guru_id')->filter()->toArray());
                                @endphp
                                <option value="{{ $g->id }}" data-assigned="{{ $isAssigned ? 'true' : 'false' }}">
                                    {{ $g->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Status</label>
                        <select name="status" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modal_ubah_kelas" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
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

<style>
    #kt_table_kelas tbody tr {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    #kt_table_kelas tbody tr:hover {
        background-color: var(--bs-table-hover-bg) !important;
    }
</style>

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#kt_table_kelas').DataTable({ 
        dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', 
        info:true, 
        order:[], 
        pageLength:5, 
        lengthChange:true, 
        columnDefs:[{orderable:false,targets:[0,6]}] 
    });
    
    $('#search_kelas').on('keyup', function() { 
        table.search(this.value).draw(); 
    });

    // Row click → navigate to class detail
    $('#kt_table_kelas').on('click', 'tbody tr', function(e) {
        var targetTd = $(e.target).closest('td');
        if (targetTd.length === 0) return;
        var idx = targetTd.index();
        if (idx === 0 || idx === 6 || $(e.target).closest('.menu').length || $(e.target).closest('[data-kt-menu-trigger]').length) {
            return;
        }
        var link = $(this).find('td:nth-child(2) a');
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
        var nama = $(this).data('nama');
        var guru = $(this).data('guru');
        var tingkat = $(this).data('tingkat');
        var status = $(this).data('status');
        
        var form = $('#modal_ubah_kelas form');
        form.attr('action', '{{ url("absensi/master/kelas/data") }}/' + id);
        form.find('input[name="nama"]').val(nama);
        
        // Dynamically adjust disabled options based on whether they are already assigned, 
        // while allowing the current class's homeroom teacher (if any) to remain selectable.
        var select = form.find('select[name="guru_id"]');
        select.find('option').each(function() {
            var val = $(this).val();
            var isAssigned = $(this).attr('data-assigned') === 'true';
            
            if (val === '') {
                $(this).prop('disabled', false);
            } else if (parseInt(val) === parseInt(guru)) {
                $(this).prop('disabled', false);
                $(this).text($(this).text().replace(' (Sudah Menjadi Wali Kelas)', ''));
            } else if (isAssigned) {
                $(this).prop('disabled', true);
                if (!$(this).text().includes(' (Sudah Menjadi Wali Kelas)')) {
                    $(this).text($(this).text() + ' (Sudah Menjadi Wali Kelas)');
                }
            } else {
                $(this).prop('disabled', false);
                $(this).text($(this).text().replace(' (Sudah Menjadi Wali Kelas)', ''));
            }
        });
        
        select.val(guru).trigger('change');
        form.find('select[name="tingkat"]').val(tingkat).trigger('change');
        form.find('select[name="status"]').val(status).trigger('change');
        
        $('#modal_ubah_kelas').modal('show');
    });
});
</script>
@endsection
</x-base-layout>
