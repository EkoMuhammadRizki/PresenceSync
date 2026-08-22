<x-base-layout>
    <!--begin::Toolbar-->
    @include('pages.absensi._partials.toolbar', [
        'customBreadcrumbs' => [
            ['title' => 'Home', 'path' => 'index', 'active' => false],
            ['title' => 'Master Data', 'path' => '', 'active' => false],
            ['title' => 'Kelas', 'path' => 'absensi/master/kelas/data', 'active' => false],
            ['title' => 'Data Kelas', 'path' => 'absensi/master/kelas/data', 'active' => false],
            ['title' => $kelas->nama, 'path' => '', 'active' => true],
        ],
        'toolbarActions' => '
            <a href="' . theme()->getPageUrl('absensi/master/kelas/data') . '" class="btn btn-sm btn-light me-2">
                ' . theme()->getSvgIcon("icons/duotune/arrows/arr063.svg", "svg-icon-4") . ' Kembali ke Data Kelas
            </a>'
    ])
    <!--end::Toolbar-->

    @php
        $totalSiswa = $kelas->siswas ? $kelas->siswas->count() : 0;
        $totalL = $kelas->siswas ? $kelas->siswas->where('jenis_kelamin', 'L')->count() : 0;
        $totalP = $kelas->siswas ? $kelas->siswas->where('jenis_kelamin', 'P')->count() : 0;
        $totalJadwal = $kelas->jadwalPelajarans ? $kelas->jadwalPelajarans->count() : 0;
        $waliKelas = $kelas->guru;
        $statusClass = ($kelas->status === 'aktif') ? 'badge-light-success' : 'badge-light-danger';
    @endphp

    <!--begin::Navbar Header Card-->
    <div class="card mb-5 mb-xl-10">
        <div class="card-body pt-9 pb-0">
            <!--begin::Details-->
            <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                <!--begin::Pic / Badge Kelas-->
                <div class="me-7 mb-4">
                    <div class="symbol symbol-100px symbol-lg-140px symbol-fixed position-relative">
                        <div class="symbol-label fs-1 fw-boldest bg-light-primary text-primary" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 2.2rem !important; border-radius: 12px;">
                            {{ $kelas->nama }}
                        </div>
                        <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
                    </div>
                </div>
                <!--end::Pic-->

                <!--begin::Info-->
                <div class="flex-grow-1">
                    <!--begin::Title-->
                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                        <!--begin::User-->
                        <div class="d-flex flex-column">
                            <!--begin::Name-->
                            <div class="d-flex align-items-center mb-2">
                                <span class="text-gray-800 fs-2 fw-bolder me-2">Kelas {{ $kelas->nama }}</span>
                                <span class="badge badge-light-primary fw-bolder me-2 fs-8 py-1 px-3">Tingkat {{ $kelas->tingkat }}</span>
                                <span class="badge {{ $statusClass }} fw-bolder fs-8 py-1 px-3">{{ ucfirst($kelas->status) }}</span>
                            </div>
                            <!--end::Name-->

                            <!--begin::Info-->
                            <div class="d-flex flex-wrap fw-bold fs-6 mb-4 pe-2">
                                <span class="d-flex align-items-center text-gray-500 me-5 mb-2">
                                    {!! theme()->getSvgIcon("icons/duotune/communication/com006.svg", "svg-icon-4 me-1") !!}
                                    Wali Kelas: <strong class="text-gray-700 ms-1">{{ $waliKelas->nama ?? 'Belum Ditentukan' }}</strong>
                                </span>
                                <span class="d-flex align-items-center text-gray-500 me-5 mb-2">
                                    {!! theme()->getSvgIcon("icons/duotune/general/gen018.svg", "svg-icon-4 me-1") !!}
                                    {{ $totalSiswa }} Siswa Terdaftar
                                </span>
                                <span class="d-flex align-items-center text-gray-500 mb-2">
                                    {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-4 me-1") !!}
                                    SMAN 1 Ciparay
                                </span>
                            </div>
                            <!--end::Info-->
                        </div>
                        <!--end::User-->

                        <!--begin::Actions-->
                        <div class="d-flex my-4">
                            <button type="button" class="btn btn-sm btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#modal_edit_kelas_profil">
                                {!! theme()->getSvgIcon("icons/duotune/art/art005.svg", "svg-icon-3") !!}
                                Edit Kelas
                            </button>
                            <a href="{{ route('pembagian-kelas.show', $kelas->id) }}" class="btn btn-sm btn-primary">
                                {!! theme()->getSvgIcon("icons/duotune/general/gen035.svg", "svg-icon-3") !!}
                                Kelola Pembagian Siswa
                            </a>
                        </div>
                        <!--end::Actions-->
                    </div>
                    <!--end::Title-->

                    <!--begin::Stats Summary-->
                    <div class="d-flex flex-wrap flex-stack">
                        <div class="d-flex flex-wrap">
                            <!--Stat Total Siswa-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder text-gray-800">{{ $totalSiswa }}</div>
                                </div>
                                <div class="fw-bold fs-7 text-gray-400">Total Siswa</div>
                            </div>
                            <!--Stat Laki-laki-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder text-primary">{{ $totalL }}</div>
                                </div>
                                <div class="fw-bold fs-7 text-gray-400">Laki-laki</div>
                            </div>
                            <!--Stat Perempuan-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder text-danger">{{ $totalP }}</div>
                                </div>
                                <div class="fw-bold fs-7 text-gray-400">Perempuan</div>
                            </div>
                            <!--Stat Jadwal Mapel-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder text-success">{{ $totalJadwal }}</div>
                                </div>
                                <div class="fw-bold fs-7 text-gray-400">Jadwal Mapel</div>
                            </div>
                        </div>
                    </div>
                    <!--end::Stats Summary-->
                </div>
                <!--end::Info-->
            </div>
            <!--end::Details-->

            <!--begin::Navs-->
            <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-6 fw-bold">
                <li class="nav-item">
                    <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#tab_info_kelas">
                        Informasi Kelas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#tab_daftar_siswa">
                        Daftar Siswa ({{ $totalSiswa }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#tab_jadwal_pelajaran">
                        Jadwal Pelajaran ({{ $totalJadwal }})
                    </a>
                </li>
            </ul>
            <!--end::Navs-->
        </div>
    </div>
    <!--end::Navbar Header Card-->

    <!--begin::Tab Content-->
    <div class="tab-content" id="kelasProfileTabContent">
        <!--begin::Tab Pane - Informasi Kelas (Active by Default)-->
        <div class="tab-pane fade show active" id="tab_info_kelas" role="tabpanel">
            <div class="row g-5 g-xxl-8">
                <!--begin::Col Left - Informasi Dasar Kelas-->
                <div class="col-xl-6">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 text-dark">Informasi Dasar Kelas</span>
                                <span class="text-muted mt-1 fw-bold fs-7">Spesifikasi dan identitas rombel kelas</span>
                            </h3>
                        </div>
                        <div class="card-body pt-3">
                            <div class="d-flex align-items-center mb-7">
                                <div class="flex-grow-1">
                                    <span class="text-muted fw-bold d-block fs-7">Nama Rombongan Belajar (Rombel)</span>
                                    <span class="text-gray-800 fw-bolder fs-6">Kelas {{ $kelas->nama }}</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-7">
                                <div class="flex-grow-1">
                                    <span class="text-muted fw-bold d-block fs-7">Tingkat Pendidikan</span>
                                    <span class="badge badge-light-primary fw-boldest fs-7">Tingkat {{ $kelas->tingkat }} (Kelas {{ $kelas->tingkat }})</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-7">
                                <div class="flex-grow-1">
                                    <span class="text-muted fw-bold d-block fs-7">Jumlah Siswa Terdaftar</span>
                                    <span class="text-gray-800 fw-bolder fs-6">{{ $totalSiswa }} Siswa (Laki-laki: {{ $totalL }}, Perempuan: {{ $totalP }})</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-grow-1">
                                    <span class="text-muted fw-bold d-block fs-7">Status Kelas</span>
                                    <span class="badge {{ $statusClass }} fw-bolder fs-7">{{ ucfirst($kelas->status) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col Left-->

                <!--begin::Col Right - Informasi Wali Kelas & Kontak-->
                <div class="col-xl-6">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 text-dark">Informasi Wali Kelas</span>
                                <span class="text-muted mt-1 fw-bold fs-7">Tenaga pendidik yang membimbing kelas</span>
                            </h3>
                        </div>
                        <div class="card-body pt-3">
                            <div class="d-flex align-items-center mb-7">
                                <div class="flex-grow-1">
                                    <span class="text-muted fw-bold d-block fs-7">Nama Wali Kelas</span>
                                    @if($waliKelas)
                                        <a href="{{ theme()->getPageUrl('absensi/profil-guru') }}?id={{ $waliKelas->id }}" class="text-primary fw-bolder fs-6 text-hover-underline">
                                            {{ $waliKelas->nama }}
                                        </a>
                                    @else
                                        <span class="text-danger fw-bold fs-6">Belum Ditentukan</span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-7">
                                <div class="flex-grow-1">
                                    <span class="text-muted fw-bold d-block fs-7">NIP / NUPTK Wali Kelas</span>
                                    <span class="text-gray-800 fw-bold fs-6">
                                        NIP: {{ $waliKelas->nip ?? '-' }} • NUPTK: {{ $waliKelas->nuptk ?? '-' }}
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-7">
                                <div class="flex-grow-1">
                                    <span class="text-muted fw-bold d-block fs-7">Nomor Telepon / WhatsApp</span>
                                    <span class="text-gray-800 fw-bold fs-6">{{ $waliKelas->no_hp ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-grow-1">
                                    <span class="text-muted fw-bold d-block fs-7">Email Resmi</span>
                                    <span class="text-gray-800 fw-bold fs-6">{{ $waliKelas->email ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Col Right-->
            </div>
        </div>
        <!--end::Tab Pane - Informasi Kelas-->

        <!--begin::Tab Pane - Daftar Siswa-->
        <div class="tab-pane fade" id="tab_daftar_siswa" role="tabpanel">
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <h3 class="fw-bolder">Daftar Siswa Kelas {{ $kelas->nama }}</h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ route('pembagian-kelas.show', $kelas->id) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-person-plus-fill me-1"></i> Tambah / Kelola Siswa
                        </a>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5 w-100" id="kt_table_siswa_kelas" style="width: 100%;">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="w-40px">No</th>
                                    <th class="min-w-100px">NIS</th>
                                    <th class="min-w-200px">Nama Siswa</th>
                                    <th class="min-w-100px">Jenis Kelamin</th>
                                    <th class="min-w-130px">Nomor Telepon</th>
                                    <th class="min-w-90px">Status</th>
                                    <th class="text-end min-w-100px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @forelse ($kelas->siswas as $idx => $s)
                                    @php
                                        $sAvatar = ($s->user && $s->user->info && !empty($s->user->info->avatar) && \Illuminate\Support\Facades\Storage::disk('public')->exists($s->user->info->avatar))
                                            ? asset('storage/' . ltrim($s->user->info->avatar, '/'))
                                            : null;
                                    @endphp
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td><span class="text-gray-800">{{ $s->nis ?? '-' }}</span></td>
                                        <td data-order="{{ $s->nama }}">
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-circle symbol-35px overflow-hidden me-3 flex-shrink-0">
                                                    @if($sAvatar)
                                                        <div class="symbol-label" style="background-image:url('{{ $sAvatar }}'); background-size: cover; background-position: center;"></div>
                                                    @else
                                                        <div class="symbol-label fs-6 bg-light-{{ $s->jenis_kelamin === 'L' ? 'primary' : 'danger' }} text-{{ $s->jenis_kelamin === 'L' ? 'primary' : 'danger' }} fw-bolder">
                                                            {{ substr($s->nama, 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <a href="{{ theme()->getPageUrl('absensi/profil-siswa') }}?id={{ $s->id }}" class="text-gray-800 text-hover-primary fw-bolder">
                                                    {{ $s->nama }}
                                                </a>
                                            </div>
                                        </td>
                                        <td>{{ $s->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                        <td>{{ $s->no_hp ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-light-{{ $s->status === 'aktif' ? 'success' : 'danger' }} fw-bolder">
                                                {{ ucfirst($s->status ?? 'aktif') }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ theme()->getPageUrl('absensi/profil-siswa') }}?id={{ $s->id }}" class="btn btn-sm btn-light-primary btn-icon" title="Lihat Profil Siswa">
                                                <i class="bi bi-eye-fill fs-6"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-8">
                                            Belum ada data siswa di kelas ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Tab Pane - Daftar Siswa-->

        <!--begin::Tab Pane - Jadwal Pelajaran-->
        <div class="tab-pane fade" id="tab_jadwal_pelajaran" role="tabpanel">
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <h3 class="fw-bolder">Jadwal Pelajaran Kelas {{ $kelas->nama }}</h3>
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5 w-100" id="kt_table_jadwal_kelas" style="width: 100%;">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-90px">Hari</th>
                                    <th class="min-w-120px">Jam Pelajaran</th>
                                    <th class="min-w-180px">Mata Pelajaran</th>
                                    <th class="min-w-180px">Guru Pengampu</th>
                                    <th class="min-w-90px">Ruangan</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @forelse ($kelas->jadwalPelajarans as $jadwal)
                                    <tr>
                                        <td><span class="badge badge-light-primary fw-bolder">{{ ucfirst($jadwal->hari ?? '-') }}</span></td>
                                        <td>{{ $jadwal->jam_mulai ?? '-' }} - {{ $jadwal->jam_selesai ?? '-' }}</td>
                                        <td><span class="text-gray-800 fw-bolder">{{ $jadwal->mataPelajaran->nama ?? '-' }}</span></td>
                                        <td>{{ $jadwal->mataPelajaran->guru->nama ?? '-' }}</td>
                                        <td>{{ $jadwal->ruangan ?? 'Kelas ' . $kelas->nama }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-8">
                                            Belum ada jadwal pelajaran untuk kelas ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Tab Pane - Jadwal Pelajaran-->
    </div>
    <!--end::Tab Content-->

    <!-- Modal Edit Kelas -->
    <div class="modal fade" id="modal_edit_kelas_profil" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-500px">
            <div class="modal-content">
                <form action="{{ route('kelas.update', $kelas->id) }}" method="POST" id="form_edit_kelas_profil">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h2 class="fw-bolder">Ubah Data Kelas</h2>
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                            {!! theme()->getSvgIcon("icons/duotune/arrows/arr061.svg", "svg-icon-1") !!}
                        </div>
                    </div>
                    <div class="modal-body py-10 px-lg-17">
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-bold mb-2">Nama Kelas</label>
                            <input type="text" class="form-control form-control-solid" name="nama" value="{{ $kelas->nama }}" required />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-bold mb-2">Tingkat</label>
                            <select name="tingkat" class="form-select form-select-solid" required>
                                <option value="10" {{ $kelas->tingkat == 10 ? 'selected' : '' }}>10 (Sepuluh)</option>
                                <option value="11" {{ $kelas->tingkat == 11 ? 'selected' : '' }}>11 (Sebelas)</option>
                                <option value="12" {{ $kelas->tingkat == 12 ? 'selected' : '' }}>12 (Dua Belas)</option>
                            </select>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-bold mb-2">Wali Kelas</label>
                            <select name="guru_id" class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih Wali Kelas" data-dropdown-parent="#modal_edit_kelas_profil">
                                <option value="">-- Pilih Wali Kelas --</option>
                                @foreach(\App\Models\Guru::orderBy('nama')->get() as $g)
                                    <option value="{{ $g->id }}" {{ $kelas->guru_id == $g->id ? 'selected' : '' }}>
                                        {{ $g->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="required fs-6 fw-bold mb-2">Status</label>
                            <select name="status" class="form-select form-select-solid" required>
                                <option value="aktif" {{ $kelas->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ $kelas->status === 'nonaktif' ? 'selected' : '' }}>Non Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @section('scripts')
    <script>
    $(document).ready(function() {
        if ($('#kt_table_siswa_kelas tbody tr').length > 0 && !$('#kt_table_siswa_kelas tbody tr td[colspan]').length) {
            $('#kt_table_siswa_kelas').DataTable({
                dom: '<"table-responsive"tr><"row"<"col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start"li><"col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end"p>>',
                info: true,
                order: [[2, 'asc']],
                pageLength: 20,
                lengthMenu: [[10, 20, 50, -1], [10, 20, 50, "Semua"]],
                lengthChange: true,
                columnDefs: [{ orderable: false, targets: [0, 6] }]
            });
        }
    });
    </script>
    @endsection
</x-base-layout>
