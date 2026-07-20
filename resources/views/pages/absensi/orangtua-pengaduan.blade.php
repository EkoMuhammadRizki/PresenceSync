<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => ''
])

<!--begin::Header Card-->
<div class="card mb-6 shadow-sm">
    <div class="card-body p-6 d-flex align-items-center justify-content-between flex-wrap gap-4">
        <div>
            <h1 class="text-gray-800 fw-boldest fs-3 mb-1">Daftar Pengaduan Anak</h1>
            <div class="text-muted fw-bold fs-7">
                Anak: <span class="text-primary fw-bolder">{{ $siswa->nama }}</span> (NIS: {{ $siswa->nis }})
            </div>
        </div>
    </div>
</div>
<!--end::Header Card-->

<!--begin::Filter Card-->
<div class="card mb-6 shadow-sm">
    <div class="card-body p-5">
        <form method="GET" action="{{ route('orangtua.pengaduan') }}" id="filter_form" class="d-flex align-items-center gap-3 flex-wrap">
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
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0" id="kt_table_pengaduan_ortu">
                <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0 bg-light">
                        <th class="w-50px border-end text-center ps-4">No</th>
                        <th class="min-w-150px border-end ps-4">Tanggal</th>
                        <th class="min-w-300px border-end ps-4">Deskripsi Pengaduan</th>
                        <th class="w-150px border-end text-center">Bukti</th>
                        <th class="min-w-150px text-center">Tanggal Submit</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 fw-bold fs-7">
                    @forelse ($records as $index => $row)
                        @php
                            $tanggalFormatted = $row->tanggal->isoFormat('ddd, DD MMMM Y');
                        @endphp
                        <tr>
                            <td class="text-center border-end ps-4">{{ $index + 1 }}</td>
                            <td class="border-end ps-4">{{ $tanggalFormatted }}</td>
                            <td class="border-end ps-4 text-wrap">{{ $row->deskripsi }}</td>
                            <td class="text-center border-end">
                                @if($row->bukti)
                                    <button type="button" class="btn btn-light-info btn-sm btn-view-bukti" data-src="{{ asset('storage/' . $row->bukti) }}">
                                        <i class="bi bi-image me-1"></i> Lihat Bukti
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $row->created_at->format('d-m-Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-8">Belum ada data pengaduan untuk anak Anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--end::Table Card-->

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
