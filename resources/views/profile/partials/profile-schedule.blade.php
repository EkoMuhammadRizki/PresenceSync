<!--begin::Card - Jadwal-->
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bolder">
                @if($userRole === 'siswa')
                    Jadwal Pelajaran Siswa
                @else
                    Jadwal Mengajar Guru
                @endif
            </h3>
        </div>
        <div class="card-toolbar">
            <!-- Filter Form -->
            <form method="GET" action="{{ request()->url() }}" id="filter_schedule_form" class="d-flex align-items-center gap-2">
                @if(request('id'))
                    <input type="hidden" name="id" value="{{ request('id') }}">
                @endif
                <label class="fs-7 fw-bold text-gray-700 me-2 d-none d-sm-inline">Pilih Semester:</label>
                <select name="semester_id" onchange="this.form.submit()" class="form-select form-select-solid form-select-sm fw-bolder w-250px" data-control="select2">
                    <option value="">-- Semua Semester --</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                            {{ $sem->tahunAjaran->tahun ?? '' }} - {{ ucfirst($sem->nama) }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_profile_schedule_table">
                <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                        <th class="w-50px">No</th>
                        <th class="min-w-100px">Hari</th>
                        <th class="min-w-175px">Mata Pelajaran</th>
                        @if($userRole === 'siswa')
                            <th class="min-w-175px">Guru Pengampu</th>
                            <th class="min-w-100px">Ruangan</th>
                        @else
                            <th class="min-w-120px">Kelas</th>
                        @endif
                        <th class="min-w-150px">Jam Pelajaran</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">
                    @foreach($schedules as $index => $schedule)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="badge badge-light-primary fw-bolder fs-7">{{ $schedule->hari }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-bold fs-6">{{ $schedule->mataPelajaran->nama ?? '-' }}</span>
                                    <span class="text-muted fs-7">Kode: {{ $schedule->mataPelajaran->kode ?? '-' }}</span>
                                </div>
                            </td>
                            @if($userRole === 'siswa')
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-circle symbol-35px overflow-hidden me-3">
                                            <div class="symbol-label fs-6 bg-light-info text-info fw-bolder">
                                                {{ substr($schedule->mataPelajaran->guru->nama ?? '?', 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 fw-bold">{{ $schedule->mataPelajaran->guru->nama ?? 'Belum Ditentukan' }}</span>
                                            <span class="text-muted fs-7">NIP: {{ $schedule->mataPelajaran->guru->nip ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-light-secondary fw-bold fs-7">{{ $schedule->kelas->nama_lengkap ?? 'Ruang Kelas' }}</span>
                                </td>
                            @else
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-circle symbol-35px overflow-hidden me-3">
                                            <div class="symbol-label fs-6 bg-light-success text-success fw-bolder">
                                                {{ substr($schedule->kelas->nama ?? '?', 0, 1) }}
                                            </div>
                                        </div>
                                        <span class="text-gray-800 fw-bold">{{ $schedule->kelas->nama_lengkap ?? '-' }}</span>
                                    </div>
                                </td>
                            @endif
                            <td>
                                <span class="text-gray-800 fw-bold fs-6">{{ $schedule->jam_mulai }} – {{ $schedule->jam_selesai }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--end::Card - Jadwal-->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.jQuery && $.fn.dataTable) {
            $('#kt_profile_schedule_table').DataTable({
                dom: "<'table-responsive'tr><'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'li><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>",
                info: true,
                order: [],
                pageLength: 10,
                lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, "Semua"]],
                lengthChange: true,
                columnDefs: [{ orderable: false, targets: [0] }],
                language: {
                    emptyTable: "Jadwal pelajaran tidak ditemukan untuk semester ini.",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                    zeroRecords: "Tidak ada jadwal pelajaran yang sesuai"
                }
            });
        }
    });
</script>
