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
        <div class="card-title"><h3 class="fw-bolder text-muted fs-6">Aturan jam berlaku untuk absensi fingerprint otomatis</h3></div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_tambah_jam">
                {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-2") !!} Tambah Aturan Jam
            </button>
        </div>
    </div>
    <div class="card-body py-4">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_jam" data-bulk-type="aturan-jam">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-30px">
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input select-all-checkbox" type="checkbox" />
                        </div>
                    </th>
                    <th class="min-w-150px">Hari</th>
                    <th class="min-w-120px">Jam Masuk</th>
                    <th class="min-w-150px">Batas Absen Pulang</th>
                    <th class="min-w-100px">Status Aktif</th>
                    <th class="text-end min-w-70px">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @foreach ($aturanJams as $i => $item)
                <tr>
                    <td>
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input select-item-checkbox" type="checkbox" value="{{ $item->id }}" />
                        </div>
                    </td>
                    <td>
                        <strong>{{ $item->hari ?? '-' }}</strong>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}</td>
                    @php
                        $batasJam = floor($item->batas_awal_pulang / 60);
                        $batasMenit = $item->batas_awal_pulang % 60;
                        $batasText = [];
                        if ($batasJam > 0) $batasText[] = $batasJam . ' jam';
                        if ($batasMenit > 0 || empty($batasText)) $batasText[] = $batasMenit . ' menit';
                    @endphp
                    <td>{{ implode(' ', $batasText) }} setelah masuk</td>
                    <td>
                        @if ($item->is_aktif)
                            <span class="badge badge-light-success fw-bolder">Aktif</span>
                        @else
                            <span class="badge badge-light-danger fw-bolder">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-150px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 btn-edit" 
                                   data-id="{{ $item->id }}"
                                   data-hari="{{ $item->hari }}"
                                   data-masuk="{{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}"
                                   data-batas="{{ $item->batas_awal_pulang }}"
                                   data-aktif="{{ $item->is_aktif ? '1' : '0' }}">
                                    Ubah
                                </a>
                            </div>
                            <div class="menu-item px-3">
                                <form action="{{ route('aturan-jam.destroy', $item->id) }}" method="POST" class="d-inline form-konfirmasi">
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

<!-- Modal Tambah Jam -->
<div class="modal fade" id="modal_tambah_jam" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Tambah Aturan Jam</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form class="form" action="{{ route('aturan-jam.store') }}" method="POST">
                    @csrf
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Hari</label>
                        <select name="hari" class="form-select form-select-solid fw-bolder" data-control="select2" data-dropdown-parent="#modal_tambah_jam" required>
                            <option value="">-- Pilih Hari --</option>
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Jam Masuk</label>
                        <input type="time" name="jam_masuk" class="form-control form-control-solid" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Batas Awal Absen Pulang</label>
                        <div class="d-flex align-items-center">
                            <input type="number" name="batas_awal_pulang_jam" class="form-control form-control-solid me-2" value="2" min="0" required placeholder="Jam" title="Jam" />
                            <span class="me-4 fw-bold">Jam</span>
                            <input type="number" name="batas_awal_pulang_menit" class="form-control form-control-solid me-2" value="0" min="0" required placeholder="Menit" title="Menit" />
                            <span class="fw-bold">Menit</span>
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Status Aktif</label>
                        <select name="is_aktif" class="form-select form-select-solid fw-bolder" data-control="select2" data-dropdown-parent="#modal_tambah_jam">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
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

<!-- Modal Ubah Jam -->
<div class="modal fade" id="modal_ubah_jam" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Ubah Aturan Jam</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form class="form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Hari</label>
                        <select name="hari" class="form-select form-select-solid fw-bolder" id="edit_hari" data-control="select2" data-dropdown-parent="#modal_ubah_jam" required>
                            <option value="">-- Pilih Hari --</option>
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $h)
                                <option value="{{ $h }}">{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Jam Masuk</label>
                        <input type="time" name="jam_masuk" class="form-control form-control-solid" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Batas Awal Absen Pulang</label>
                        <div class="d-flex align-items-center">
                            <input type="number" name="batas_awal_pulang_jam" class="form-control form-control-solid me-2" min="0" required placeholder="Jam" title="Jam" />
                            <span class="me-4 fw-bold">Jam</span>
                            <input type="number" name="batas_awal_pulang_menit" class="form-control form-control-solid me-2" min="0" required placeholder="Menit" title="Menit" />
                            <span class="fw-bold">Menit</span>
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Status Aktif</label>
                        <select name="is_aktif" class="form-select form-select-solid fw-bolder" id="edit_is_aktif" data-control="select2" data-dropdown-parent="#modal_ubah_jam">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
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

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#kt_table_jam').DataTable({ 
        dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', 
        info:true, 
        order:[], 
        pageLength:10, 
        lengthMenu:[[10, 20, 50, 100, -1], [10, 20, 50, 100, "Semua"]],
        lengthChange:true, 
        columnDefs:[{orderable:false,targets:[0,5]}] 
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
        var hari = $(this).data('hari');
        var masuk = $(this).data('masuk');
        var batas = $(this).data('batas');
        var aktif = $(this).data('aktif');
        
        var form = $('#modal_ubah_jam form');
        form.attr('action', '{{ url("absensi/master/aturan-jam") }}/' + id);
        form.find('select[name="hari"]').val(hari).trigger('change');
        form.find('input[name="jam_masuk"]').val(masuk);
        
        var batasTotal = parseInt(batas) || 0;
        var batasJam = Math.floor(batasTotal / 60);
        var batasMenit = batasTotal % 60;
        form.find('input[name="batas_awal_pulang_jam"]').val(batasJam);
        form.find('input[name="batas_awal_pulang_menit"]').val(batasMenit);
        
        form.find('select[name="is_aktif"]').val(aktif).trigger('change');
        
        $('#modal_ubah_jam').modal('show');
    });
});
</script>
@endsection
</x-base-layout>
