<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => ''
])

<div class="card card-flush shadow-sm">
    <div class="card-header bg-primary py-3 rounded-top">
        <div class="card-title text-white fw-bolder fs-5 m-0 d-flex align-items-center gap-2">
            <i class="bi bi-book text-white fs-4"></i> Rekap Kehadiran Mata Pelajaran
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="card-body py-4 border-bottom">
        <form method="GET" action="{{ route('siswa.kehadiran-mp') }}" id="filter_form" class="d-flex align-items-center flex-wrap gap-5 justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <label class="form-label fw-bold mb-0 me-2 text-nowrap">Periode:</label>
                <select name="periode" class="form-select form-select-solid form-select-sm w-180px" onchange="document.getElementById('filter_form').submit()">
                    @php
                        $startMonth = \Carbon\Carbon::now()->startOfMonth()->subMonths(6);
                    @endphp
                    @for ($i = 0; $i < 13; $i++)
                        @php
                            $pVal = $startMonth->format('Ym');
                            $pLabel = $startMonth->isoFormat('MMMM Y');
                            $startMonth->addMonth();
                        @endphp
                        <option value="{{ $pVal }}" {{ ($periode ?? date('Ym')) == $pVal ? 'selected' : '' }}>
                            {{ $pLabel }}
                        </option>
                    @endfor
                </select>

                @if(request('periode'))
                    @php
                        $selectedMonthName = \Carbon\Carbon::createFromDate(substr(request('periode'), 0, 4), substr(request('periode'), 4, 2), 1)->isoFormat('MMMM Y');
                    @endphp
                    <div class="d-flex align-items-center bg-light-primary rounded border border-primary border-dashed px-3 py-1 fs-7 text-primary fw-bolder">
                        Periode: {{ $selectedMonthName }}
                        <a href="{{ route('siswa.kehadiran-mp') }}" class="btn btn-icon btn-xs btn-active-color-primary ms-2 text-primary p-0">✗</a>
                    </div>
                @endif
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('siswa.kehadiran-mp.export', ['periode' => $periode ?? date('Ym')]) }}" class="btn btn-light-success btn-sm btn-md-md">
                    {!! theme()->getSvgIcon("icons/duotune/files/fil021.svg", "svg-icon-2") !!}
                    <span class="d-none d-sm-inline">Ekspor Data</span>
                    <span class="d-inline d-sm-none">Ekspor</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Data -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered align-middle gs-4 gy-3 mb-0" id="table_kehadiran_mp">
                <thead>
                    <tr class="bg-light fw-bolder fs-7 text-uppercase text-gray-800 text-center border-bottom border-gray-300">
                        <th class="w-50px border-end">No</th>
                        <th class="min-w-200px border-end">Tanggal</th>
                        <th class="w-150px border-end">Jumlah Mapel</th>
                        <th class="min-w-250px border-end">Nama Mata Pelajaran</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 fw-bold fs-7">
                    @forelse ($records as $row)
                        @php
                            $disabled = $row['is_future'] ? ' opacity-50 pointer-events-none' : '';
                            $dateStr = $row['tanggal'];
                        @endphp
                        <tr class="border-bottom border-gray-200{{ $disabled }}" style="cursor:pointer" onclick="window.location='{{ route('siswa.kehadiran-mp.profiling', $dateStr) }}'">
                            <td class="text-center border-end">{{ $loop->iteration }}</td>
                            <td class="border-end{{ $row['is_future'] ? ' text-muted' : '' }}">{{ $row['tanggal_label'] }}</td>
                            <td class="text-center border-end">
                                @if ($row['count'] > 0)
                                    <span class="badge badge-light-primary fs-6 px-4 py-2">{{ $row['count'] }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="border-end">
                                @if ($row['mapel_list'])
                                    @php $mapels = explode('. ', $row['mapel_list']); @endphp
                                    <div class="d-flex flex-wrap gap-3">
                                        @foreach($mapels as $idx => $nama)
                                            <span class="d-inline-flex align-items-center gap-1">
                                                <span class="badge badge-light-primary fs-8 px-2 py-1">{{ $idx + 1 }}</span>
                                                <span class="text-gray-700">{{ $nama }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada data kehadiran mata pelajaran</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

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
    tr[style*="cursor:pointer"]:hover {
        background-color: #F1FAFF;
    }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var detailMpId = null;
    var detailUrl = '{{ route("siswa.sekretaris.kehadiran-mp.detail", ":id") }}';
    var simpanUrl = '{{ route("siswa.sekretaris.kehadiran-mp.simpan", ":id") }}';
    var hapusUrl = '{{ route("siswa.sekretaris.kehadiran-mp.destroy", ":id") }}';

    $('#table_kehadiran_mp').DataTable({
        paging: false,
        searching: false,
        info: false,
        order: []
    });

    // Detail button click
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

    // Simpan detail
    $('#btn_simpan_detail_mp').on('click', function() {
        if (!detailMpId) return;

        var siswaData = [];
        $('#tbody_detail_mp tr').each(function() {
            var toggle = $(this).find('.toggle-hadir');
            var keterangan = $(this).find('.input-keterangan');
            siswaData.push({
                siswa_id: toggle.data('siswa-id'),
                status: toggle.is(':checked') ? 1 : 0,
                keterangan: keterangan.val()
            });
        });

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
