<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => ''
])

<!--begin::Header Card-->
<div class="card mb-6 shadow-sm">
    <div class="card-body p-6 d-flex align-items-center justify-content-between flex-wrap gap-4">
        <div>
            <h1 class="text-gray-800 fw-boldest fs-3 mb-1">Daftar Pengaduan Siswa</h1>
            <div class="text-muted fw-bold fs-7">
                Wali Kelas: <span class="text-primary fw-bolder">{{ $kelas ? $kelas->tingkat . ' ' . $kelas->nama : 'Belum Memiliki Kelas Wali' }}</span>
            </div>
        </div>
    </div>
</div>
<!--end::Header Card-->

@if(!$kelas)
    <div class="alert alert-danger d-flex align-items-center p-5 shadow-sm">
        <span class="svg-icon svg-icon-2hx svg-icon-danger me-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black"/>
                <rect x="11" y="14" width="2" height="2" rx="1" fill="black"/>
                <rect x="11" y="7" width="2" height="5" rx="1" fill="black"/>
            </svg>
        </span>
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-dark fw-bold">Akses Terbatas</h4>
            <span>Anda belum dikonfigurasi sebagai Wali Kelas untuk kelas aktif manapun.</span>
        </div>
    </div>
@else
    <!--begin::Filter Card-->
    <div class="card mb-6 shadow-sm">
        <div class="card-body p-5">
            <form method="GET" action="{{ route('guru.pengaduan') }}" id="filter_form" class="d-flex align-items-center justify-content-between flex-wrap gap-4">
                <!-- Left: Filters -->
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <!-- Date Picker -->
                    <div class="d-flex align-items-center position-relative my-1">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-2 position-absolute ms-4") !!}
                        <input type="text" name="tanggal_range" id="filter_tanggal" class="form-control form-control-solid w-275px ps-12" placeholder="Pilih Rentang Tanggal" readonly="readonly" value="{{ request('tanggal_range') }}" />
                    </div>
                    
                    <!-- Search Input -->
                    <div class="d-flex align-items-center position-relative my-1">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-2 position-absolute ms-4") !!}
                        <input type="text" name="search" id="search_siswa" class="form-control form-control-solid w-250px ps-12" placeholder="Cari nama siswa..." value="{{ request('search') }}" />
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm">
                        Cari & Filter
                    </button>

                    <a href="{{ route('guru.pengaduan') }}" class="btn btn-light btn-sm" id="btn_clear_filter" style="{{ (request('tanggal_range') || request('search')) ? '' : 'display: none;' }}">
                        Clear
                    </a>
                </div>

                <!-- Right: Export -->
                <div>
                    <a href="{{ route('guru.pengaduan.export', ['tanggal_range' => request('tanggal_range'), 'search' => request('search')]) }}" class="btn btn-light-success btn-sm">
                        {!! theme()->getSvgIcon("icons/duotune/files/fil021.svg", "svg-icon-2 me-1") !!}
                        Export Data
                    </a>
                </div>
            </form>
        </div>
    </div>
    <!--end::Filter Card-->

    <!--begin::Table Card-->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0" id="kt_table_pengaduan_guru">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0 bg-light">
                            <th class="w-50px border-end text-center ps-4">No</th>
                            <th class="w-100px border-end ps-4">NIS</th>
                            <th class="min-w-150px border-end ps-4">Nama Siswa</th>
                            <th class="min-w-250px border-end ps-4">Deskripsi Pengaduan</th>
                            <th class="min-w-120px border-end ps-4">Tanggal</th>
                            <th class="w-120px border-end text-center">Bukti</th>
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
                                <td class="border-end ps-4">{{ $row->siswa->nis ?? '-' }}</td>
                                <td class="border-end ps-4 text-primary">{{ $row->siswa->nama ?? '-' }}</td>
                                <td class="border-end ps-4 text-wrap">{{ $row->deskripsi }}</td>
                                <td class="border-end ps-4">{{ $tanggalFormatted }}</td>
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
                                <td colspan="7" class="text-center text-muted py-8">Tidak ditemukan data pengaduan siswa.</td>
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
@endif

@section('scripts')
@if($kelas)
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
                $('#btn_clear_filter').show();
            }
        }
    });
});
</script>
<script src="{{ asset(theme()->getDemo() . '/plugins/custom/flatpickr/flatpickr.bundle.js') }}"></script>
@endif
@endsection
</x-base-layout>
