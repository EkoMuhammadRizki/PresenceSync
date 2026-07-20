<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => ''
])

@php
    $dateObj = \Carbon\Carbon::parse($record->tanggal);
    $tanggalLabel = $dateObj->isoFormat('dddd, D MMMM Y');
@endphp

<!--begin::Header Card-->
<div class="card mb-6">
    <div class="card-body p-6">
        <div class="d-flex align-items-center flex-wrap gap-4">
            <a href="{{ route('siswa.kehadiran-mp.profiling', $record->tanggal->format('Y-m-d')) }}" class="btn btn-icon btn-light-primary btn-sm rounded-circle">
                <i class="bi bi-arrow-left fs-4"></i>
            </a>
            <div class="flex-grow-1">
                <h3 class="fw-bolder mb-0">Daftar Kehadiran Mata Pelajaran {{ $record->mataPelajaran->nama }}</h3>
                <span class="text-muted fw-bold fs-6">{{ $tanggalLabel }}</span>
            </div>
        </div>
    </div>
</div>
<!--end::Header Card-->

<!--begin::Info Card-->
<div class="card card-flush shadow-sm mb-6">
    <div class="card-body p-6">
        <div class="row g-6">
            <div class="col-md-4">
                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
                    <div class="fs-6 text-gray-800 fw-bolder">{{ $record->mataPelajaran->nama }}</div>
                    <div class="fw-bold text-gray-400">Nama Mata Pelajaran</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
                    <div class="fs-6 text-gray-800 fw-bolder">
                        {{ $record->jam_mulai ? \Carbon\Carbon::parse($record->jam_mulai)->format('H:i') : '-' }} - 
                        {{ $record->jam_selesai ? \Carbon\Carbon::parse($record->jam_selesai)->format('H:i') : '-' }}
                    </div>
                    <div class="fw-bold text-gray-400">Jam Mulai dan Selesai</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
                    <div class="fs-6 text-gray-800 fw-bolder" id="teacher_status_badges">
                        <span class="badge badge-light-{{ $record->is_guru_hadir ? 'success' : 'danger' }} me-1" id="badge_is_guru_hadir">
                            {{ $record->is_guru_hadir ? 'Guru Hadir' : 'Guru Tidak Hadir' }}
                        </span>
                        <span class="badge badge-light-{{ $record->ada_konfirmasi_guru ? 'info' : 'warning' }}" id="badge_ada_konfirmasi_guru">
                            {{ $record->ada_konfirmasi_guru ? 'Ada Konfirmasi' : 'Tidak Ada Konfirmasi' }}
                        </span>
                    </div>
                    <div class="fw-bold text-gray-400">Status Kehadiran Guru</div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Info Card-->

<!--begin::Table Card-->
<div class="card card-flush shadow-sm">
    <div class="card-header bg-primary py-3 rounded-top d-flex align-items-center justify-content-between">
        <div class="card-title text-white fw-bolder fs-5 m-0 d-flex align-items-center gap-2">
            <i class="bi bi-people text-white fs-4"></i> Kehadiran Siswa
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-light-warning fw-bold" id="btn_konfirmasi_guru" data-bs-toggle="modal" data-bs-target="#modal_konfirmasi_guru">
                <i class="bi bi-person-check fs-6 me-1"></i> Konfirmasi Guru
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered align-middle gs-4 gy-3 mb-0" id="table_kehadiran_siswa">
                <thead>
                    <tr class="bg-light fw-bolder fs-7 text-uppercase text-gray-800 text-center border-bottom border-gray-300">
                        <th class="w-50px border-end">No</th>
                        <th class="min-w-100px border-end">NIS</th>
                        <th class="min-w-200px border-end">Nama Siswa</th>
                        <th class="w-100px border-end">Hadir</th>
                        <th class="min-w-250px border-end">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 fw-bold fs-7" id="tbody_siswa">
                    @forelse ($siswaKelas as $index => $s)
                        <tr>
                            <td class="text-center border-end">{{ $index + 1 }}</td>
                            <td class="border-end">{{ $s['nis'] }}</td>
                            <td class="border-end">{{ $s['nama'] }}</td>
                            <td class="text-center border-end">
                                <div class="form-check form-switch form-check-custom form-check-solid d-flex justify-content-center">
                                    <input class="form-check-input toggle-hadir" type="checkbox" data-siswa-id="{{ $s['siswa_id'] }}" {{ $s['status'] ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td class="border-end">
                                <input type="text" class="form-control form-control-solid form-control-sm input-keterangan" data-siswa-id="{{ $s['siswa_id'] }}" value="{{ $s['keterangan'] }}" placeholder="Keterangan...">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-8">Tidak ada data siswa di kelas ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-end py-4">
        <a href="{{ route('siswa.kehadiran-mp.profiling', $record->tanggal->format('Y-m-d')) }}" class="btn btn-light me-3">Batal</a>
        <button type="button" class="btn btn-success" id="btn_simpan_kehadiran">
            <i class="bi bi-check-circle me-1"></i> Simpan Kehadiran
        </button>
    </div>
</div>
<!--end::Table Card-->

<!--begin::Modal Konfirmasi Guru-->
<div class="modal fade" id="modal_konfirmasi_guru" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered mw-450px">
        <div class="modal-content">
            <form id="form_konfirmasi_guru">
                @csrf
                <div class="modal-header">
                    <h3 class="modal-title fw-bolder">Konfirmasi Kehadiran Guru</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="modal-body mx-4 my-4">
                    <div class="mb-5">
                        <label class="form-label fw-bold required mb-1">Kehadiran Guru</label>
                        <select name="is_guru_hadir" id="select_is_guru_hadir" class="form-select form-select-solid" required>
                            <option value="1" {{ $record->is_guru_hadir ? 'selected' : '' }}>Guru Hadir</option>
                            <option value="0" {{ !$record->is_guru_hadir ? 'selected' : '' }}>Guru Tidak Hadir</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="form-label fw-bold required mb-1">Konfirmasi Guru</label>
                        <select name="ada_konfirmasi_guru" id="select_ada_konfirmasi_guru" class="form-select form-select-solid" required>
                            <option value="1" {{ $record->ada_konfirmasi_guru ? 'selected' : '' }}>Ada Konfirmasi Guru</option>
                            <option value="0" {{ !$record->ada_konfirmasi_guru ? 'selected' : '' }}>Tidak Ada konfirmasi Guru</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn_submit_konfirmasi_guru">
                        <i class="bi bi-check-circle me-1"></i> Simpan Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal Konfirmasi Guru-->

@section('styles')
<style>
    .toggle-hadir:checked {
        background-color: #50CD89 !important;
        border-color: #50CD89 !important;
    }
    .is-invalid {
        border-color: #F1416C !important;
        background-image: none !important;
    }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var simpanUrl = '{{ route("siswa.sekretaris.kehadiran-mp.simpan", $record->id) }}';
    var redirectUrl = '{{ route("siswa.kehadiran-mp.profiling", $record->tanggal->format("Y-m-d")) }}';
    var konfirmasiGuruUrl = '{{ route("siswa.sekretaris.kehadiran-mp.konfirmasi-guru", $record->id) }}';

    // Submit Konfirmasi Guru
    $('#form_konfirmasi_guru').on('submit', function(e) {
        e.preventDefault();
        
        var btn = $('#btn_submit_konfirmasi_guru');
        btn.attr('disabled', true).html('<span class="spinner-border spinner-border-sm align-middle ms-2"></span> Menyimpan...');

        $.ajax({
            url: konfirmasiGuruUrl,
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                btn.attr('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Simpan Konfirmasi');
                $('#modal_konfirmasi_guru').modal('hide');
                
                // Update badges dynamically
                $('#badge_is_guru_hadir').removeClass('badge-light-success badge-light-danger')
                    .addClass(res.is_guru_hadir ? 'badge-light-success' : 'badge-light-danger')
                    .text(res.is_guru_hadir ? 'Guru Hadir' : 'Guru Tidak Hadir');
                $('#badge_ada_konfirmasi_guru').removeClass('badge-light-info badge-light-warning')
                    .addClass(res.ada_konfirmasi_guru ? 'badge-light-info' : 'badge-light-warning')
                    .text(res.ada_konfirmasi_guru ? 'Ada Konfirmasi' : 'Tidak Ada Konfirmasi');

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message || 'Konfirmasi kehadiran guru berhasil disimpan',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
            },
            error: function(xhr) {
                btn.attr('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Simpan Konfirmasi');
                var msg = xhr.responseJSON?.error || 'Gagal menyimpan konfirmasi';
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    // Clear validation status on input or state toggle
    $(document).on('input', '.input-keterangan', function() {
        if ($(this).val().trim() !== '') {
            $(this).removeClass('is-invalid');
        }
    });

    $(document).on('change', '.toggle-hadir', function() {
        var row = $(this).closest('tr');
        var inputKet = row.find('.input-keterangan');
        if ($(this).is(':checked')) {
            inputKet.removeClass('is-invalid');
        }
    });

    $('#btn_simpan_kehadiran').on('click', function() {
        var siswaData = [];
        var isValid = true;
        var firstInvalidInput = null;
        var firstInvalidName = '';

        $('#tbody_siswa tr').each(function() {
            var toggle = $(this).find('.toggle-hadir');
            var keterangan = $(this).find('.input-keterangan');
            var namaSiswa = $(this).find('td:nth-child(3)').text().trim();

            if (toggle.length) {
                var statusVal = toggle.is(':checked') ? 1 : 0;
                var ketVal = keterangan.val().trim();

                // If not present, Keterangan is required
                if (statusVal === 0 && ketVal === '') {
                    isValid = false;
                    keterangan.addClass('is-invalid');
                    if (!firstInvalidInput) {
                        firstInvalidInput = keterangan;
                        firstInvalidName = namaSiswa;
                    }
                }

                siswaData.push({
                    siswa_id: toggle.data('siswa-id'),
                    status: statusVal,
                    keterangan: ketVal
                });
            }
        });

        if (!isValid) {
            Swal.fire({
                icon: 'warning',
                title: 'Keterangan Wajib Diisi',
                text: 'Siswa "' + firstInvalidName + '" tidak hadir, kolom keterangan wajib diisi (misal: Sakit, Izin, Alpa).',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-warning'
                }
            }).then(function() {
                if (firstInvalidInput) {
                    firstInvalidInput.focus();
                }
            });
            return;
        }

        if (siswaData.length === 0) {
            return;
        }

        // Show loading state
        var btn = $(this);
        btn.attr('disabled', true).html('<span class="spinner-border spinner-border-sm align-middle ms-2"></span> Menyimpan...');

        $.ajax({
            url: simpanUrl,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                siswa: siswaData
            },
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message || 'Data kehadiran berhasil disimpan',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                }).then(function() {
                    window.location.href = redirectUrl;
                });
            },
            error: function(xhr) {
                btn.attr('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Simpan Kehadiran');
                var msg = xhr.responseJSON?.error || 'Gagal menyimpan data';
                Swal.fire('Error', msg, 'error');
            }
        });
    });
});
</script>
@endsection
</x-base-layout>
