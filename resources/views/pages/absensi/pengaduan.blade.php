<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => ''
])

<!--begin::Header Card-->
<div class="card mb-6">
    <div class="card-body p-6">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-4">
            <div>
                <h3 class="fw-bolder mb-1">Pengaduan Siswa</h3>
                <span class="text-muted fw-bold fs-6">Sekretaris Kelas: {{ $siswa->kelas->nama ?? '-' }}</span>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_tambah_pengaduan">
                <i class="bi bi-plus-circle me-1"></i> Tambah Pengaduan
            </button>
        </div>
    </div>
</div>
<!--end::Header Card-->

<!--begin::Filter Card-->
<div class="card mb-6 shadow-sm">
    <div class="card-body p-5">
        <form method="GET" action="{{ route('siswa.pengaduan') }}" id="filter_form" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="d-flex align-items-center position-relative my-1">
                {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-2 position-absolute ms-4") !!}
                <input type="text" name="tanggal_range" id="filter_tanggal" class="form-control form-control-solid w-275px ps-12" placeholder="Pilih Rentang Tanggal" readonly="readonly" value="{{ request('tanggal_range') }}" />
            </div>
            <button type="button" id="reset_filter_tanggal" class="btn btn-light-danger btn-sm" style="{{ request('tanggal_range') ? '' : 'display: none;' }}">
                Reset
            </button>
        </form>
    </div>
</div>
<!--end::Filter Card-->

<!--begin::Table Card-->
<div class="card card-flush shadow-sm">
    <div class="card-header bg-primary py-3 rounded-top">
        <div class="card-title text-white fw-bolder fs-5 m-0 d-flex align-items-center gap-2">
            <i class="bi bi-journal-text text-white fs-4"></i> Daftar Pengaduan
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered align-middle gs-4 gy-3 mb-0" id="table_pengaduan">
                <thead>
                    <tr class="bg-light fw-bolder fs-7 text-uppercase text-gray-800 text-center border-bottom border-gray-300">
                        <th class="w-50px border-end">No</th>
                        <th class="min-w-150px border-end">Tanggal</th>
                        <th class="min-w-300px border-end">Deskripsi Isi Pengaduan</th>
                        <th class="w-150px border-end">Bukti</th>
                        <th class="min-w-150px border-end">Tanggal Input</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 fw-bold fs-7">
                    @forelse ($records as $index => $row)
                        @php
                            $tanggalFormatted = $row->tanggal->isoFormat('ddd, DD MMMM Y');
                        @endphp
                        <tr>
                             <td class="text-center border-end">{{ ($records->currentPage() - 1) * $records->perPage() + $index + 1 }}</td>
                            <td class="border-end">{{ $tanggalFormatted }}</td>
                            <td class="border-end text-wrap">{{ $row->deskripsi }}</td>
                            <td class="text-center border-end">
                                @if($row->bukti)
                                    <button type="button" class="btn btn-light-info btn-sm btn-view-bukti" data-src="{{ asset('storage/' . $row->bukti) }}">
                                        <i class="bi bi-image me-1"></i> Lihat Bukti
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="border-end text-center">{{ $row->created_at->format('d-m-Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-8">Belum ada data pengaduan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($records->hasPages())
            <div class="card-footer py-4 d-flex justify-content-end">
                {!! $records->links() !!}
            </div>
        @endif
    </div>
</div>
<!--end::Table Card-->

<!--begin::Modal Tambah Pengaduan-->
<div class="modal fade" id="modal_tambah_pengaduan" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <form id="form_tambah_pengaduan" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h2 class="fw-bolder">Tambah Pengaduan</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body mx-4 my-4">
                    <div class="mb-5">
                        <label class="form-label fw-bold required mb-1">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control form-control-solid" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-5">
                        <label class="form-label fw-bold required mb-1">Deskripsi Isi Pengaduan</label>
                        <textarea name="deskripsi" class="form-control form-control-solid" rows="4" placeholder="Ketik deskripsi isi pengaduan..." required></textarea>
                    </div>
                    <div class="mb-5">
                        <label class="form-label fw-bold required mb-1">Upload Bukti (Gambar)</label>
                        <input type="file" name="bukti" class="form-control form-control-solid" accept="image/*" required>
                        <div class="form-text text-muted fs-7 mt-1">File berupa gambar (JPEG, PNG, JPG). File akan dikompres secara otomatis (5-50KB).</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn_simpan_pengaduan">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Upload & Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal Tambah Pengaduan-->

<!--begin::Modal View Bukti-->
<div class="modal fade" id="modal_view_bukti" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Foto Bukti Pengaduan</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <span class="svg-icon svg-icon-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                        </svg>
                    </span>
                </div>
            </div>
            <div class="modal-body text-center p-9">
                <img id="img_bukti_preview" src="" alt="Foto Bukti" class="img-fluid rounded shadow-sm" style="max-height: 450px; object-fit: contain; width: 100%;" />
            </div>
        </div>
    </div>
</div>
<!--end::Modal View Bukti-->

@section('scripts')
<script>
$(document).ready(function() {
    // Open image modal
    $(document).on('click', '.btn-view-bukti', function() {
        var src = $(this).data('src');
        $('#img_bukti_preview').attr('src', src);
        $('#modal_view_bukti').modal('show');
    });

    $('#form_tambah_pengaduan').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        var btn = $('#btn_simpan_pengaduan');

        // Show loading state
        btn.attr('disabled', true).html('<span class="spinner-border spinner-border-sm align-middle ms-2"></span> Mengunggah...');

        $.ajax({
            url: '{{ route("siswa.pengaduan.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message || 'Pengaduan berhasil ditambahkan',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                }).then(function() {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                btn.attr('disabled', false).html('<i class="bi bi-cloud-arrow-up me-1"></i> Upload & Simpan');
                var msg = xhr.responseJSON?.error || 'Gagal menyimpan data';
                if (xhr.responseJSON?.errors) {
                    var errList = '<ul>';
                    $.each(xhr.responseJSON.errors, function(k, v) {
                        errList += '<li>' + v[0] + '</li>';
                    });
                    errList += '</ul>';
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        html: errList
                    });
                } else {
                    Swal.fire('Error', msg, 'error');
                }
            }
        });
    });

    // Lokalisasi Bahasa Indonesia untuk Flatpickr
    var indonesianLocale = {
        firstDayOfWeek: 1,
        weekdays: {
            shorthand: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
            longhand: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"]
        },
        months: {
            shorthand: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agt", "Sep", "Okt", "Nov", "Des"],
            longhand: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"]
        },
        rangeSeparator: " hingga "
    };

    // Inisialisasi Flatpickr
    var fp = $("#filter_tanggal").flatpickr({
        mode: "range",
        dateFormat: "Y-m-d",
        locale: indonesianLocale,
        onClose: function(selectedDates, dateStr, instance) {
            if (selectedDates.length > 0) {
                $('#reset_filter_tanggal').show();
                $('#filter_form').submit();
            } else {
                $('#reset_filter_tanggal').hide();
            }
        }
    });

    // Reset filter klik
    $('#reset_filter_tanggal').on('click', function() {
        if (fp) {
            fp.clear();
        }
        $(this).hide();
        $('#filter_form').submit();
    });
});
</script>
<script src="{{ asset(theme()->getDemo() . '/plugins/custom/flatpickr/flatpickr.bundle.js') }}"></script>
@endsection
</x-base-layout>
