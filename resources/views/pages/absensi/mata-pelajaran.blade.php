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
                <input type="text" id="search_mapel" class="form-control form-control-solid w-250px ps-14" placeholder="Cari mata pelajaran..." />
            </div>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_tambah_mapel">
                {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-2") !!} Tambah Mata Pelajaran
            </button>
        </div>
    </div>
    <div class="card-body py-4">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_mapel">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-30px">No</th>
                    <th class="min-w-80px">Kode</th>
                    <th class="min-w-200px">Nama Mata Pelajaran</th>
                    <th class="min-w-150px">Guru Pengampu</th>
                    <th class="min-w-100px">Jam / Minggu</th>
                    <th class="text-end min-w-70px">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @foreach ($mataPelajarans as $i => $mp)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $mp->kode }}</strong></td>
                    <td>{{ $mp->nama }}</td>
                    <td>{{ $mp->guru->nama ?? 'Belum Ditentukan' }}</td>
                    <td>{{ $mp->jam_per_minggu }} jam</td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 btn-edit"
                                   data-id="{{ $mp->id }}"
                                   data-kode="{{ $mp->kode }}"
                                   data-nama="{{ $mp->nama }}"
                                   data-guru="{{ $mp->guru_id ?? '' }}"
                                   data-jam="{{ $mp->jam_per_minggu }}">
                                    Ubah
                                </a>
                            </div>
                            <div class="menu-item px-3">
                                <form action="{{ route('mata-pelajaran.destroy', $mp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')">
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
                        <input type="text" name="kode" class="form-control form-control-solid" placeholder="Kode mapel" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Nama Mata Pelajaran</label>
                        <input type="text" name="nama" class="form-control form-control-solid" placeholder="Nama mata pelajaran" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">Guru Pengampu</label>
                        <select name="guru_id" class="form-select form-select-solid">
                            <option value="">-- Pilih Guru (Opsional) --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Jam Per Minggu</label>
                        <input type="number" name="jam_per_minggu" class="form-control form-control-solid" min="1" max="40" value="2" required />
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
                        <input type="text" name="kode" class="form-control form-control-solid" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Nama Mata Pelajaran</label>
                        <input type="text" name="nama" class="form-control form-control-solid" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">Guru Pengampu</label>
                        <select name="guru_id" class="form-select form-select-solid">
                            <option value="">-- Pilih Guru (Opsional) --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Jam Per Minggu</label>
                        <input type="number" name="jam_per_minggu" class="form-control form-control-solid" min="1" max="40" required />
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
    var table = $('#kt_table_mapel').DataTable({ 
        dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', 
        info:true, 
        order:[], 
        pageLength:5, 
        lengthChange:true, 
        columnDefs:[{orderable:false,targets:[0,5]}] 
    });
    $('#search_mapel').on('keyup', function() { 
        table.search(this.value).draw(); 
    });

    $('.btn-edit').on('click', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var kode = $(this).data('kode');
        var nama = $(this).data('nama');
        var guru = $(this).data('guru');
        var jam = $(this).data('jam');
        
        var form = $('#modal_ubah_mapel form');
        form.attr('action', '{{ url("absensi/master/mata-pelajaran") }}/' + id);
        form.find('input[name="kode"]').val(kode);
        form.find('input[name="nama"]').val(nama);
        form.find('select[name="guru_id"]').val(guru);
        form.find('input[name="jam_per_minggu"]').val(jam);
        
        $('#modal_ubah_mapel').modal('show');
    });
});
</script>
@endsection
</x-base-layout>
