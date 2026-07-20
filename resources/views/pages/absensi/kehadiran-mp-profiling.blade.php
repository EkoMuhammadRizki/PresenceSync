<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => ''
])

@php
    $dateObj = \Carbon\Carbon::parse($tanggal);
    $tanggalLabel = $dateObj->isoFormat('dddd, D MMMM Y');
@endphp

<!--begin::Header Card-->
<div class="card mb-6">
    <div class="card-body p-6">
        <div class="d-flex align-items-center flex-wrap gap-4">
            <a href="{{ route('siswa.kehadiran-mp') }}" class="btn btn-icon btn-light-primary btn-sm rounded-circle">
                <i class="bi bi-arrow-left fs-4"></i>
            </a>
            <div class="flex-grow-1">
                <h3 class="fw-bolder mb-0">Profiling Kehadiran</h3>
                <span class="text-muted fw-bold fs-6">{{ $tanggalLabel }}</span>
            </div>
        </div>
    </div>
</div>
<!--end::Header Card-->

<!--begin::Form Card-->
<div class="card card-flush shadow-sm mb-6">
    <div class="card-header bg-primary py-3 rounded-top">
        <div class="card-title text-white fw-bolder fs-5 m-0 d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle text-white fs-4"></i> Tambah Data Kehadiran
        </div>
    </div>
    <div class="card-body py-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold fs-7 mb-1">Pilih Mata Pelajaran</label>
                <select id="mp_select" class="form-select form-select-solid" data-control="select2" data-placeholder="Ketik mata pelajaran...">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold fs-7 mb-1">Jam Mulai</label>
                <div class="input-group">
                    <input type="number" id="jam_mulai_h" class="form-control form-control-solid text-center" min="0" max="23" placeholder="HH" value="">
                    <span class="input-group-text bg-transparent border-0 fw-bolder px-0">:</span>
                    <input type="number" id="jam_mulai_m" class="form-control form-control-solid text-center" min="0" max="59" placeholder="mm" value="">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold fs-7 mb-1">Jam Selesai</label>
                <div class="input-group">
                    <input type="number" id="jam_selesai_h" class="form-control form-control-solid text-center" min="0" max="23" placeholder="HH" value="">
                    <span class="input-group-text bg-transparent border-0 fw-bolder px-0">:</span>
                    <input type="number" id="jam_selesai_m" class="form-control form-control-solid text-center" min="0" max="59" placeholder="mm" value="">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold fs-7 mb-1">&nbsp;</label>
                <button type="button" id="btn_tambah_profiling" class="btn btn-primary w-100">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Data
                </button>
            </div>
        </div>
    </div>
</div>
<!--end::Form Card-->

<!--begin::Card Grid-->
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-6" id="card_grid">
    @forelse ($records as $record)
    @php
        $totalSiswa = count($siswaKelas);
        $isFilled = $record->details->isNotEmpty();
        if ($isFilled) {
            $hadir = $record->details->where('status', true)->count();
            $tidakHadir = $totalSiswa - $hadir;
            $presentPercentage = $totalSiswa > 0 ? round(($hadir / $totalSiswa) * 100) : 0;
        } else {
            $hadir = 0;
            $tidakHadir = 0;
            $presentPercentage = 0;
        }
    @endphp
    <div class="col card-item" data-id="{{ $record->id }}" data-jam-mulai="{{ $record->jam_mulai }}">
        <div class="card card-bordered card-shadow h-100" style="cursor: pointer;" onclick="window.location='{{ route('siswa.sekretaris.kehadiran-mp.daftar-hadir', $record->id) }}'">
            <div class="card-body d-flex flex-column p-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="badge badge-light-primary fs-7 px-3 py-2">{{ $record->jam_mulai ? \Carbon\Carbon::parse($record->jam_mulai)->format('H:i') : '-' }} - {{ $record->jam_selesai ? \Carbon\Carbon::parse($record->jam_selesai)->format('H:i') : '-' }}</span>
                    <div class="d-flex gap-1">
                        <a href="{{ route('siswa.sekretaris.kehadiran-mp.daftar-hadir', $record->id) }}" class="btn btn-sm btn-icon btn-light-primary rounded-circle" title="Detail Siswa" onclick="event.stopPropagation();">
                            <i class="bi bi-eye fs-6"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-hapus-mp rounded-circle" data-id="{{ $record->id }}" title="Hapus">
                            <i class="bi bi-trash fs-6"></i>
                        </button>
                    </div>
                </div>
                <h5 class="fw-boldest text-gray-800 mb-1">{{ $record->mataPelajaran->nama ?? '-' }}</h5>
                <div class="text-muted fw-bold fs-7 mb-3">
                    <i class="bi bi-person text-primary me-1"></i> {{ $record->mataPelajaran->guru->nama ?? 'Tanpa Guru' }}
                </div>
                
                <!-- Ringkasan Kehadiran & Chart Donut -->
                <div class="d-flex align-items-center justify-content-between mt-auto bg-light rounded p-3">
                    <div class="d-flex flex-column">
                        <span class="fs-8 text-gray-500 fw-bold text-uppercase">Ringkasan Kehadiran</span>
                        @if ($isFilled)
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge badge-light-success fs-8 fw-bolder">Hadir: {{ $hadir }}</span>
                                <span class="badge badge-light-danger fs-8 fw-bolder">Absen: {{ $tidakHadir }}</span>
                            </div>
                        @else
                            <div class="mt-1">
                                <span class="badge badge-light-warning fs-8 fw-bolder">Belum Diisi</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="position-relative d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <svg width="50" height="50" viewBox="0 0 36 36" class="donut">
                            @if ($isFilled)
                                <circle cx="18" cy="18" r="15.915" fill="transparent" stroke="#F1416C" stroke-width="4"></circle>
                                <circle cx="18" cy="18" r="15.915" fill="transparent" stroke="#50CD89" stroke-width="4" 
                                        stroke-dasharray="{{ $presentPercentage }} {{ 100 - $presentPercentage }}" stroke-dashoffset="25"></circle>
                            @else
                                <circle cx="18" cy="18" r="15.915" fill="transparent" stroke="#E4E6EF" stroke-width="4"></circle>
                            @endif
                        </svg>
                        <div class="position-absolute d-flex flex-column align-items-center justify-content-center">
                            <span class="fs-9 fw-boldest text-gray-800">{{ $isFilled ? $presentPercentage . '%' : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 d-flex justify-content-center">
        <div class="text-muted fw-bold fs-6 py-8" id="empty_msg">Belum ada data kehadiran untuk tanggal ini</div>
    </div>
    @endforelse
</div>
<!--end::Card Grid-->

<!--begin::Modal Detail Kehadiran Mata Pelajaran-->
<div class="modal fade" id="modal_detail_mp" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered mw-700px">
        <div class="modal-content">
            <div class="modal-header" id="modal_detail_mp_header">
                <h2 class="fw-bolder" id="modal_detail_mp_title">Detail Kehadiran</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body mx-4 my-4">
                <div class="mb-4">
                    <span class="fw-bold fs-6" id="modal_detail_mp_info"></span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle gs-4 gy-3 mb-0" id="table_detail_mp">
                        <thead>
                            <tr class="bg-light fw-bolder fs-7 text-uppercase text-gray-800 text-center border-bottom border-gray-300">
                                <th class="w-50px border-end">No</th>
                                <th class="min-w-100px border-end">NISN</th>
                                <th class="min-w-150px border-end">Nama Siswa</th>
                                <th class="w-100px border-end">Hadir</th>
                                <th class="min-w-200px border-end">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_detail_mp" class="text-gray-700 fw-bold fs-7">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="btn_simpan_detail_mp">
                    <i class="bi bi-check-circle me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal Detail Kehadiran Mata Pelajaran-->

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
    .card-shadow {
        box-shadow: 0 0.1rem 0.5rem rgba(0,0,0,0.06);
        transition: box-shadow 0.2s ease;
    }
    .card-shadow:hover {
        box-shadow: 0 0.3rem 1rem rgba(0,0,0,0.1);
    }
    .input-group .form-control {
        flex: 0 0 60px;
    }
    .input-group-text {
        font-size: 1.1rem;
    }
    /* Hilangkan spinner arrow di number input */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var detailUrl = '{{ route("siswa.sekretaris.kehadiran-mp.detail", ":id") }}';
    var simpanUrl = '{{ route("siswa.sekretaris.kehadiran-mp.simpan", ":id") }}';
    var hapusUrl = '{{ route("siswa.sekretaris.kehadiran-mp.destroy", ":id") }}';
    var storeUrl = '{{ route("siswa.sekretaris.kehadiran-mp.store") }}';
    var jadwalUrl = '{{ route("siswa.sekretaris.jadwal") }}';
    var tanggal = '{{ $tanggal }}';

    var detailMpId = null;

    // Load jadwal on page load
    $.ajax({
        url: jadwalUrl,
        data: { tanggal: tanggal },
        success: function(data) {
            var mpSelect = $('#mp_select');
            mpSelect.empty().append('<option value="">-- Pilih Mata Pelajaran --</option>');

            if (data.length === 0) {
                mpSelect.append('<option value="" disabled>Tidak ada jadwal pada hari ini</option>');
                return;
            }

            $.each(data, function(i, item) {
                mpSelect.append(
                    '<option value="' + item.mata_pelajaran_id + '" data-guru="' + item.guru + '" data-jam-mulai="' + item.jam_mulai + '" data-jam-selesai="' + item.jam_selesai + '">' +
                        item.mata_pelajaran + ' - ' + item.guru +
                    '</option>'
                );
            });

            mpSelect.trigger('change.select2');
        },
        error: function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.error || 'Gagal memuat jadwal', 'error');
        }
    });

    // Auto-fill time when subject is selected
    $('#mp_select').on('change', function() {
        var selected = $(this).find('option:selected');
        var jamMulai = selected.data('jam-mulai') || '';
        var jamSelesai = selected.data('jam-selesai') || '';
        if (jamMulai && jamSelesai) {
            var partsMulai = jamMulai.split(':');
            var partsSelesai = jamSelesai.split(':');
            $('#jam_mulai_h').val(partsMulai[0] || '');
            $('#jam_mulai_m').val(partsMulai[1] || '');
            $('#jam_selesai_h').val(partsSelesai[0] || '');
            $('#jam_selesai_m').val(partsSelesai[1] || '');
        }
    });

    function padZero(n) {
        return n.toString().padStart(2, '0');
    }

    // Validate hour (0-23) and minute (0-59) inputs
    $(document).on('input', 'input[id$="_h"]', function() {
        var val = parseInt($(this).val()) || 0;
        if (val > 23) $(this).val(23);
        if (val < 0) $(this).val(0);
    });
    $(document).on('input', 'input[id$="_m"]', function() {
        var val = parseInt($(this).val()) || 0;
        if (val > 59) $(this).val(59);
        if (val < 0) $(this).val(0);
    });

    // Tambah Data
    $('#btn_tambah_profiling').on('click', function() {
        var mpId = $('#mp_select').val();
        var jmH = $('#jam_mulai_h').val();
        var jmM = $('#jam_mulai_m').val();
        var jsH = $('#jam_selesai_h').val();
        var jsM = $('#jam_selesai_m').val();

        if (!mpId) {
            Swal.fire('Peringatan', 'Silakan pilih mata pelajaran', 'warning');
            return;
        }
        if (!jmH || jmH === '') {
            Swal.fire('Peringatan', 'Silakan isi jam mulai (HH)', 'warning');
            return;
        }
        if (!jsH || jsH === '') {
            Swal.fire('Peringatan', 'Silakan isi jam selesai (HH)', 'warning');
            return;
        }

        var jamMulai = padZero(jmH) + ':' + padZero(jmM || 0) + ':00';
        var jamSelesai = padZero(jsH) + ':' + padZero(jsM || 0) + ':00';

        if (jamMulai >= jamSelesai) {
            Swal.fire('Peringatan', 'Jam selesai harus setelah jam mulai', 'warning');
            return;
        }

        $.ajax({
            url: storeUrl,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                tanggal: tanggal,
                mata_pelajaran_id: mpId,
                jam_mulai: jamMulai,
                jam_selesai: jamSelesai
            },
            success: function(res) {
                Swal.fire('Berhasil', res.message, 'success');
                // Reset form
                $('#mp_select').val('').trigger('change.select2');
                $('#jam_mulai_h, #jam_mulai_m, #jam_selesai_h, #jam_selesai_m').val('');
                // Add card
                addCard(res.data);
            },
            error: function(xhr) {
                var msg = xhr.responseJSON?.error || 'Gagal menyimpan data';
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    var daftarHadirUrl = '{{ route("siswa.sekretaris.kehadiran-mp.daftar-hadir", ":id") }}';

    function addCard(data) {
        $('#empty_msg').remove();
        var grid = $('#card_grid');
        var mp = data.mata_pelajaran || {};
        var guru = mp.guru || {};
        var mpNama = mp.nama || '-';
        var guruNama = guru.nama || 'Tanpa Guru';
        var jamMulai = data.jam_mulai ? data.jam_mulai.substring(0, 5) : '-';
        var jamSelesai = data.jam_selesai ? data.jam_selesai.substring(0, 5) : '-';
        var url = daftarHadirUrl.replace(':id', data.id);

        var card = [
            '<div class="col card-item" data-id="' + data.id + '" data-jam-mulai="' + data.jam_mulai + '">',
                '<div class="card card-bordered card-shadow h-100" style="cursor: pointer;" onclick="window.location=\'' + url + '\'">',
                    '<div class="card-body d-flex flex-column p-5">',
                        '<div class="d-flex align-items-center justify-content-between mb-3">',
                            '<span class="badge badge-light-primary fs-7 px-3 py-2">' + jamMulai + ' - ' + jamSelesai + '</span>',
                            '<div class="d-flex gap-1">',
                                '<a href="' + url + '" class="btn btn-sm btn-icon btn-light-primary rounded-circle" title="Detail Siswa" onclick="event.stopPropagation();"><i class="bi bi-eye fs-6"></i></a>',
                                '<button type="button" class="btn btn-sm btn-icon btn-light-danger btn-hapus-mp rounded-circle" data-id="' + data.id + '" title="Hapus"><i class="bi bi-trash fs-6"></i></button>',
                            '</div>',
                        '</div>',
                        '<h5 class="fw-boldest text-gray-800 mb-1">' + mpNama + '</h5>',
                        '<div class="text-muted fw-bold fs-7 mb-3">',
                            '<i class="bi bi-person text-primary me-1"></i> ' + guruNama,
                        '</div>',
                        '<div class="d-flex align-items-center justify-content-between mt-auto bg-light rounded p-3">',
                            '<div class="d-flex flex-column">',
                                '<span class="fs-8 text-gray-500 fw-bold text-uppercase">Ringkasan Kehadiran</span>',
                                '<div class="mt-1">',
                                    '<span class="badge badge-light-warning fs-8 fw-bolder">Belum Diisi</span>',
                                '</div>',
                            '</div>',
                            '<div class="position-relative d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">',
                                '<svg width="50" height="50" viewBox="0 0 36 36" class="donut">',
                                    '<circle cx="18" cy="18" r="15.915" fill="transparent" stroke="#E4E6EF" stroke-width="4"></circle>',
                                '</svg>',
                                '<div class="position-absolute d-flex flex-column align-items-center justify-content-center">',
                                    '<span class="fs-9 fw-boldest text-gray-800">-</span>',
                                '</div>',
                            '</div>',
                        '</div>',
                    '</div>',
                '</div>',
            '</div>'
        ].join('');

        if (grid.find('.card-item').length === 0) {
            grid.empty();
        }
        grid.append(card);
        sortCards();
    }

    function sortCards() {
        var grid = $('#card_grid');
        var cards = grid.children('.card-item').get();
        cards.sort(function(a, b) {
            var valA = $(a).data('jam-mulai') || '';
            var valB = $(b).data('jam-mulai') || '';
            return valA.localeCompare(valB);
        });
        $.each(cards, function(idx, item) {
            grid.append(item);
        });
    }

    // Detail button
    $(document).on('click', '.btn-detail-mp', function(e) {
        e.stopPropagation();
        var id = $(this).data('id');
        detailMpId = id;

        $.ajax({
            url: detailUrl.replace(':id', id),
            success: function(res) {
                $('#modal_detail_mp_title').text('Detail Kehadiran');
                $('#modal_detail_mp_info').text(res.record.mata_pelajaran + ' - ' + res.record.hari + ', ' + res.record.tanggal_label);

                var tbody = $('#tbody_detail_mp');
                tbody.empty();

                $.each(res.siswa, function(i, s) {
                    var no = i + 1;
                    var checked = s.status ? 'checked' : '';
                    tbody.append(
                        '<tr>' +
                            '<td class="text-center border-end">' + no + '</td>' +
                            '<td class="border-end">' + s.nisn + '</td>' +
                            '<td class="border-end">' + s.nama + '</td>' +
                            '<td class="text-center border-end">' +
                                '<div class="form-check form-switch form-check-custom form-check-solid d-flex justify-content-center">' +
                                    '<input class="form-check-input toggle-hadir" type="checkbox" data-siswa-id="' + s.siswa_id + '" ' + checked + '>' +
                                '</div>' +
                            '</td>' +
                            '<td class="border-end">' +
                                '<input type="text" class="form-control form-control-sm input-keterangan" data-siswa-id="' + s.siswa_id + '" value="' + (s.keterangan || '') + '" placeholder="Keterangan...">' +
                            '</td>' +
                        '</tr>'
                    );
                });

                $('#modal_detail_mp').modal('show');
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.error || 'Gagal memuat detail', 'error');
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

    // Simpan detail
    $('#btn_simpan_detail_mp').on('click', function() {
        if (!detailMpId) return;

        var siswaData = [];
        var isValid = true;
        var firstInvalidInput = null;
        var firstInvalidName = '';

        $('#tbody_detail_mp tr').each(function() {
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

        $.ajax({
            url: simpanUrl.replace(':id', detailMpId),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                siswa: siswaData
            },
            success: function(res) {
                Swal.fire('Berhasil', res.message, 'success');
                $('#modal_detail_mp').modal('hide');
            },
            error: function(xhr) {
                var msg = xhr.responseJSON?.error || 'Gagal menyimpan data';
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    // Hapus data
    $(document).on('click', '.btn-hapus-mp', function(e) {
        e.stopPropagation();
        var id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Data?',
            text: 'Data kehadiran mata pelajaran akan dihapus permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#F1416C'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: hapusUrl.replace(':id', id),
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire('Berhasil', res.message, 'success');
                        $('.card-item[data-id="' + id + '"]').remove();
                        if ($('#card_grid').find('.card-item').length === 0) {
                            $('#card_grid').html('<div class="col-12 d-flex justify-content-center"><div class="text-muted fw-bold fs-6 py-8" id="empty_msg">Belum ada data kehadiran untuk tanggal ini</div></div>');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.error || 'Gagal menghapus data', 'error');
                    }
                });
            }
        });
    });

    // Reset detail modal on close
    $('#modal_detail_mp').on('hidden.bs.modal', function() {
        detailMpId = null;
    });
});
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{{ session('success') }}',
        confirmButtonText: 'Selesai',
        buttonsStyling: false,
        customClass: { confirmButton: 'btn btn-primary' }
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: '{{ session('error') }}',
        confirmButtonText: 'Tutup',
        buttonsStyling: false,
        customClass: { confirmButton: 'btn btn-danger' }
    });
</script>
@endif

@if ($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Validasi Gagal',
        html: '<ul class="text-start">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
        confirmButtonText: 'Perbaiki',
        buttonsStyling: false,
        customClass: { confirmButton: 'btn btn-danger' }
    });
</script>
@endif
@endsection
</x-base-layout>
