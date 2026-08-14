<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => ''
])

<!--begin::Welcome Card-->
<div class="card mb-8">
    <div class="card-body p-9">
        <div class="d-flex align-items-center">
            <div class="symbol symbol-60px symbol-circle me-5">
                <div class="symbol-label fs-1 bg-light-primary text-primary fw-bolder">
                    {{ substr($siswa->nama, 0, 1) }}
                </div>
            </div>
            <div class="flex-grow-1">
                <h1 class="text-gray-800 fw-boldest mb-1">{{ $siswa->nama }}</h1>
                <div class="text-muted fw-bold fs-6">
                    Siswa NIS: {{ $siswa->nis }} | Kelas: {{ $siswa->kelas ? $siswa->kelas->tingkat . ' ' . $siswa->kelas->nama : 'Belum Masuk Kelas' }}
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Welcome Card-->

<!--begin::Card - Riwayat Kehadiran Siswa-->
<div class="card card-flush shadow-sm">
    <!-- Title bar with blue background -->
    <div class="card-header bg-primary py-3 rounded-top">
        <div class="card-title text-white fw-bolder fs-5 m-0 d-flex align-items-center gap-2">
            <i class="bi bi-journal-text text-white fs-4"></i> Rekapitulasi Kehadiran Saya
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="card-body py-4 border-bottom">
        <form method="GET" action="{{ url('/absensi/siswa/kehadiran') }}" id="filter_form" class="d-flex align-items-center flex-wrap gap-5 justify-content-between">
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
                        <option value="{{ $pVal }}" {{ $periode == $pVal ? 'selected' : '' }}>
                            {{ $pLabel }}
                        </option>
                    @endfor
                </select>
                
                @if($periode)
                    @php
                        $selectedMonthName = \Carbon\Carbon::createFromDate(substr($periode, 0, 4), substr($periode, 4, 2), 1)->isoFormat('MMMM Y');
                    @endphp
                    <div class="d-flex align-items-center bg-light-primary rounded border border-primary border-dashed px-3 py-1 fs-7 text-primary fw-bolder">
                        Periode: {{ $selectedMonthName }}
                        <a href="{{ url('/absensi/siswa/kehadiran') }}" class="btn btn-icon btn-xs btn-active-color-primary ms-2 text-primary p-0">✗</a>
                    </div>
                @endif
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="text-gray-600 fs-7 fw-bold" id="showing_count_label">
                    Menampilkan 1-{{ $daysInMonth }} dari {{ $daysInMonth }} data
                </div>
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered align-middle gs-4 gy-3 mb-0" id="kt_table_kehadiran_siswa">
                <thead>
                    <tr class="bg-light fw-bolder fs-7 text-uppercase text-gray-800 text-center border-bottom border-gray-300">
                        <th class="w-50px border-end">No</th>
                        <th class="min-w-100px border-end">NISN / NIS</th>
                        <th class="min-w-150px border-end">Nama</th>
                        <th class="w-150px border-end">Tanggal</th>
                        <th class="w-80px border-end">Msk/Lbr</th>
                        <th class="w-100px border-end">Masuk Jam</th>
                        <th class="w-100px border-end">Pulang Jam</th>
                        <th class="min-w-120px border-end">Keterangan</th>
                    </tr>
                </thead>

                <tbody class="text-gray-700 fw-bold fs-7">
                    @foreach ($attendanceRows as $row)
                        <tr class="{{ $row['is_libur'] ? 'bg-light-danger text-muted' : '' }} border-bottom border-gray-200">
                            <td class="text-center border-end" data-col="no">{{ $row['no'] }}</td>
                            <td class="border-end" data-col="nisn">{{ $row['nisn'] }}</td>
                            <td class="border-end" data-col="nama">{{ $row['nama'] }}</td>
                            <td class="border-end" data-col="tanggal">{{ $row['tanggal'] }}</td>
                            <td class="text-center border-end" data-col="msk_lbr">
                                @if ($row['msk_lbr'] === '✓')
                                    <span class="text-success fw-bolder fs-5">✓</span>
                                @elseif ($row['msk_lbr'] === '✗')
                                    <span class="text-danger fw-bolder fs-5">✗</span>
                                @else
                                    -
                                @endif
                            <td class="text-center border-end text-primary" data-col="msk_jam">{{ $row['msk_jam'] ?: '-' }}</td>
                            <td class="text-center border-end text-success" data-col="plg_jam">{{ $row['plg_jam'] ?: '-' }}</td>
                            <td class="border-end" data-col="keterangan">{{ $row['keterangan'] ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--end::Card - Riwayat Kehadiran Siswa-->
</x-base-layout>
