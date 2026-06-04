<x-base-layout>
@include('pages.absensi._partials.toolbar')

@if(session('success'))
    <div class="alert alert-success d-flex align-items-center p-5 mb-10">
        <span class="svg-icon svg-icon-2します svg-icon-success me-4">
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
                <input type="text" id="search_jadwal" class="form-control form-control-solid w-250px ps-14" placeholder="Cari jadwal..." />
            </div>
        </div>
        <div class="card-toolbar">
            <div class="me-3">
                <span class="badge badge-light-success fs-7 fw-bold px-4 py-3">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-4 me-1") !!}
                    Tahun Ajaran Aktif: 
                    <strong>
                        @if($semesters->first())
                            {{ $semesters->first()->tahunAjaran->nama ?? '-' }} - {{ ucfirst($semesters->first()->jenis) }}
                        @else
                            Tidak Ada Semester Aktif
                        @endif
                    </strong>
                </span>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_tambah_jadwal">
                {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-2") !!} Tambah Jadwal
            </button>
        </div>
    </div>
    <div class="card-body py-4">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_jadwal" data-bulk-type="jadwal-pelajaran">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-30px">
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input select-all-checkbox" type="checkbox" />
                        </div>
                    </th>
                    <th class="min-w-100px">Tahun Ajaran</th>
                    <th class="min-w-80px">Semester</th>
                    <th class="min-w-80px">Kelas</th>
                    <th class="min-w-150px">Mata Pelajaran</th>
                    <th class="min-w-120px">Guru</th>
                    <th class="min-w-200px">Hari, Jam Mulai – Selesai</th>
                    <th class="text-end min-w-70px">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @foreach ($jadwals as $i => $j)
                <tr>
                    <td>
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input select-item-checkbox" type="checkbox" value="{{ $j->id }}" />
                        </div>
                    </td>
                    <td>{{ $j->semester->tahunAjaran->nama ?? '-' }}</td>
                    <td>{{ ucfirst($j->semester->jenis ?? '-') }}</td>
                    <td><strong>{{ $j->kelas->nama ?? '-' }}</strong></td>
                    <td>{{ $j->mataPelajaran->nama ?? '-' }}</td>
                    <td>{{ $j->mataPelajaran->guru->nama ?? 'Belum Ditentukan' }}</td>
                    <td>
                        <span class="badge badge-light-info fw-bolder">
                            {{ $j->hari }}, {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} – {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 btn-edit"
                                   data-id="{{ $j->id }}"
                                   data-kelas="{{ $j->kelas_id }}"
                                   data-mapel="{{ $j->mata_pelajaran_id }}"
                                   data-semester="{{ $j->semester_id }}"
                                   data-hari="{{ $j->hari }}"
                                   data-mulai="{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}"
                                   data-selesai="{{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}">
                                    Ubah
                                </a>
                            </div>
                            <div class="menu-item px-3">
                                <form action="{{ route('jadwal-pelajaran.destroy', $j->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
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

<!-- Modal Tambah Jadwal -->
<div class="modal fade" id="modal_tambah_jadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Tambah Jadwal Pelajaran</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form class="form" action="{{ route('jadwal-pelajaran.store') }}" method="POST">
                    @csrf
                    <div class="row g-9 mb-7">
                        <div class="col-md-12 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Semester & Tahun Ajaran</label>
                            <select name="semester_id" class="form-select form-select-solid fw-bolder" required>
                                <option value="">Pilih semester...</option>
                                @foreach ($semesters as $s)
                                    <option value="{{ $s->id }}">{{ $s->tahunAjaran->nama ?? '-' }} - {{ ucfirst($s->jenis) }} (Aktif)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Kelas</label>
                            <select name="kelas_id" class="form-select form-select-solid fw-bolder" required>
                                <option value="">Pilih kelas...</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Mata Pelajaran</label>
                            <select name="mata_pelajaran_id" class="form-select form-select-solid fw-bolder" required>
                                <option value="">Pilih mapel...</option>
                                @foreach ($mataPelajarans as $mp)
                                    <option value="{{ $mp->id }}">{{ $mp->kode }} - {{ $mp->nama }} ({{ $mp->guru->nama ?? 'No Guru' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-4 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Hari</label>
                            <select name="hari" class="form-select form-select-solid fw-bolder" required>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                            </select>
                        </div>
                        <div class="col-md-4 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control form-control-solid" required />
                        </div>
                        <div class="col-md-4 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control form-control-solid" required />
                        </div>
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

<!-- Modal Ubah Jadwal -->
<div class="modal fade" id="modal_ubah_jadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Ubah Jadwal Pelajaran</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-5 my-7">
                <form class="form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-9 mb-7">
                        <div class="col-md-12 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Semester & Tahun Ajaran</label>
                            <select name="semester_id" class="form-select form-select-solid fw-bolder" required>
                                @foreach ($semesters as $s)
                                    <option value="{{ $s->id }}">{{ $s->tahunAjaran->nama ?? '-' }} - {{ ucfirst($s->jenis) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Kelas</label>
                            <select name="kelas_id" class="form-select form-select-solid fw-bolder" required>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Mata Pelajaran</label>
                            <select name="mata_pelajaran_id" class="form-select form-select-solid fw-bolder" required>
                                @foreach ($mataPelajarans as $mp)
                                    <option value="{{ $mp->id }}">{{ $mp->kode }} - {{ $mp->nama }} ({{ $mp->guru->nama ?? 'No Guru' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-4 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Hari</label>
                            <select name="hari" class="form-select form-select-solid fw-bolder" required>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                            </select>
                        </div>
                        <div class="col-md-4 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control form-control-solid" required />
                        </div>
                        <div class="col-md-4 fv-row">
                            <label class="required fw-bold fs-6 mb-2">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control form-control-solid" required />
                        </div>
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
    var table = $('#kt_table_jadwal').DataTable({ 
        dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', 
        info:true, 
        order:[], 
        pageLength:5, 
        lengthChange:true, 
        columnDefs:[{orderable:false,targets:[0,7]}] 
    });
    $('#search_jadwal').on('keyup', function() { 
        table.search(this.value).draw(); 
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
        var kelas = $(this).data('kelas');
        var mapel = $(this).data('mapel');
        var semester = $(this).data('semester');
        var hari = $(this).data('hari');
        var mulai = $(this).data('mulai');
        var selesai = $(this).data('selesai');
        
        var form = $('#modal_ubah_jadwal form');
        form.attr('action', '{{ url("absensi/master/jadwal-pelajaran") }}/' + id);
        form.find('select[name="semester_id"]').val(semester);
        form.find('select[name="kelas_id"]').val(kelas);
        form.find('select[name="mata_pelajaran_id"]').val(mapel);
        form.find('select[name="hari"]').val(hari);
        form.find('input[name="jam_mulai"]').val(mulai);
        form.find('input[name="jam_selesai"]').val(selesai);
        
        $('#modal_ubah_jadwal').modal('show');
    });
});
</script>
@endsection
</x-base-layout>
