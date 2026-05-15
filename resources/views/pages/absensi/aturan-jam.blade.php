<x-base-layout>
@include('pages.absensi._partials.toolbar')



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
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_jam">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-30px">No</th>
                    <th class="min-w-120px">Hari</th>
                    <th class="min-w-120px">Jam Masuk</th>
                    <th class="min-w-120px">Jam Pulang</th>
                    <th class="min-w-100px">Status Aktif</th>
                    <th class="text-end min-w-70px">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @php
                    $hariData = [
                        ['Senin', '07:00', '15:30', true],
                        ['Selasa', '07:00', '15:30', true],
                        ['Rabu', '07:00', '15:30', true],
                        ['Kamis', '07:00', '15:30', true],
                        ['Jumat', '07:00', '11:30', true],
                        ['Sabtu', '-', '-', false],
                    ];
                @endphp
                @foreach ($hariData as $i => $hari)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $hari[0] }}</strong></td>
                    <td>{{ $hari[1] }}</td>
                    <td>{{ $hari[2] }}</td>
                    <td>
                        @if ($hari[3])
                            <span class="badge badge-light-success fw-bolder">Aktif</span>
                        @else
                            <span class="badge badge-light-danger fw-bolder">Libur</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#modal_ubah_jam">Ubah</a></div>
                            <div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger">Hapus</a></div>
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
            <div class="modal-header"><h2 class="fw-bolder">Tambah Aturan Jam</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body mx-5 my-7">
                <form class="form">
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Hari</label><select class="form-select form-select-solid fw-bolder"><option>Pilih hari...</option><option>Senin</option><option>Selasa</option><option>Rabu</option><option>Kamis</option><option>Jumat</option><option>Sabtu</option></select></div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Jam Masuk</label><input type="time" class="form-control form-control-solid" /></div>
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Jam Pulang</label><input type="time" class="form-control form-control-solid" /></div>
                    </div>
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Status Aktif</label><select class="form-select form-select-solid fw-bolder"><option value="1">Aktif</option><option value="0">Libur</option></select></div>
                    <div class="text-center pt-5"><button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Ubah Jam -->
<div class="modal fade" id="modal_ubah_jam" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header"><h2 class="fw-bolder">Ubah Aturan Jam</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body mx-5 my-7">
                <form class="form">
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Hari</label><select class="form-select form-select-solid fw-bolder"><option selected>Senin</option><option>Selasa</option><option>Rabu</option></select></div>
                    <div class="row g-9 mb-7">
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Jam Masuk</label><input type="time" class="form-control form-control-solid" value="07:00" /></div>
                        <div class="col-md-6 fv-row"><label class="required fw-bold fs-6 mb-2">Jam Pulang</label><input type="time" class="form-control form-control-solid" value="15:30" /></div>
                    </div>
                    <div class="fv-row mb-7"><label class="required fw-bold fs-6 mb-2">Status Aktif</label><select class="form-select form-select-solid fw-bolder"><option value="1" selected>Aktif</option><option value="0">Libur</option></select></div>
                    <div class="text-center pt-5"><button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    $('#kt_table_jam').DataTable({ dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', info:false, order:[], pageLength:10, lengthChange:false, columnDefs:[{orderable:false,targets:[0,5]}] });
});
</script>
@endsection
</x-base-layout>
