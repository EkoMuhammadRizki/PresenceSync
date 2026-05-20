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
                <input type="text" id="search_guru" class="form-control form-control-solid w-250px ps-14" placeholder="Cari guru..." />
            </div>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_tambah_guru">
                {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-2") !!}
                Tambah Guru
            </button>
        </div>
    </div>
    <div class="card-body py-4">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_guru">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-30px">No</th>
                    <th class="min-w-150px">NIP</th>
                    <th class="min-w-200px">Nama</th>
                    <th class="min-w-150px">Email</th>
                    <th class="min-w-120px">No HP</th>
                    <th class="min-w-150px">Wali Kelas</th>
                    <th class="min-w-120px">Mata Pelajaran</th>
                    <th class="text-end min-w-70px">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @foreach ($gurus as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $item->nip ?? '-' }}</strong></td>
                    <td class="d-flex align-items-center border-0">
                        <div class="symbol symbol-circle symbol-40px overflow-hidden me-3">
                            <div class="symbol-label fs-4 bg-light-primary text-primary fw-bolder">
                                {{ substr($item->nama, 0, 1) }}
                            </div>
                        </div>
                        <span>{{ $item->nama }}</span>
                    </td>
                    <td>{{ $item->email ?? '-' }}</td>
                    <td>{{ $item->no_hp ?? '-' }}</td>
                    <td>
                        @if($item->kelas_count > 0)
                            <span class="badge badge-light-success fw-bolder">{{ $item->kelas_count }} Kelas</span>
                        @else
                            <span class="badge badge-light-secondary text-muted fw-bold">Bukan Wali</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-light-info fw-bolder">{{ $item->mata_pelajarans_count }} Mapel</span>
                    </td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 btn-edit"
                                   data-id="{{ $item->id }}"
                                   data-nama="{{ $item->nama }}"
                                   data-nip="{{ $item->nip ?? '' }}"
                                   data-email="{{ $item->email ?? '' }}"
                                   data-nohp="{{ $item->no_hp ?? '' }}"
                                   data-alamat="{{ $item->alamat ?? '' }}">
                                    Ubah
                                </a>
                            </div>
                            <div class="menu-item px-3">
                                <form action="{{ route('guru.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus guru ini?')">
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

<!-- Modal Tambah Guru -->
<div class="modal fade" id="modal_tambah_guru" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Tambah Guru</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form class="form" action="{{ route('guru.store') }}" method="POST">
                    @csrf
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control form-control-solid" placeholder="Nama Lengkap Beserta Gelar" required />
                    </div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">NIP</label>
                            <input type="text" name="nip" class="form-control form-control-solid" placeholder="NIP Resmi Guru" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">Email</label>
                            <input type="email" name="email" class="form-control form-control-solid" placeholder="contoh@sekolah.sch.id" />
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">No. HP (WhatsApp)</label>
                        <input type="text" name="no_hp" class="form-control form-control-solid" placeholder="Contoh: 081234567890" />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control form-control-solid" rows="3" placeholder="Alamat tinggal saat ini"></textarea>
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

<!-- Modal Ubah Guru -->
<div class="modal fade" id="modal_ubah_guru" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Ubah Guru</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form class="form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control form-control-solid" required />
                    </div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">NIP</label>
                            <input type="text" name="nip" class="form-control form-control-solid" />
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fw-bold fs-6 mb-2">Email</label>
                            <input type="email" name="email" class="form-control form-control-solid" />
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">No. HP (WhatsApp)</label>
                        <input type="text" name="no_hp" class="form-control form-control-solid" />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control form-control-solid" rows="3"></textarea>
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
    var table = $('#kt_table_guru').DataTable({ 
        dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', 
        info:true, 
        order:[], 
        pageLength:5, 
        lengthChange:true, 
        columnDefs:[{orderable:false,targets:[0,7]}] 
    });
    
    $('#search_guru').on('keyup', function() { 
        table.search(this.value).draw(); 
    });

    $('.btn-edit').on('click', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var nip = $(this).data('nip');
        var email = $(this).data('email');
        var nohp = $(this).data('nohp');
        var alamat = $(this).data('alamat');
        
        var form = $('#modal_ubah_guru form');
        form.attr('action', '{{ url("absensi/master/guru") }}/' + id);
        form.find('input[name="nama"]').val(nama);
        form.find('input[name="nip"]').val(nip);
        form.find('input[name="email"]').val(email);
        form.find('input[name="no_hp"]').val(nohp);
        form.find('textarea[name="alamat"]').val(alamat);
        
        $('#modal_ubah_guru').modal('show');
    });
});
</script>
@endsection
</x-base-layout>
