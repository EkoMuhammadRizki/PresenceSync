@if($userRole === 'admin')
    <!-- Total User -->
    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
        <div class="d-flex align-items-center">
            {!! theme()->getSvgIcon("icons/duotune/communication/com013.svg", "svg-icon-3 svg-icon-primary me-2") !!}
            <div class="fs-2 fw-bolder">{{ $stats['total_users'] ?? 0 }}</div>
        </div>
        <div class="fw-bold fs-6 text-gray-400">Total User</div>
    </div>

    <!-- Total Guru -->
    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
        <div class="d-flex align-items-center">
            {!! theme()->getSvgIcon("icons/duotune/communication/com006.svg", "svg-icon-3 svg-icon-success me-2") !!}
            <div class="fs-2 fw-bolder">{{ $stats['total_gurus'] ?? 0 }}</div>
        </div>
        <div class="fw-bold fs-6 text-gray-400">Total Guru</div>
    </div>

    <!-- Total Siswa -->
    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
        <div class="d-flex align-items-center">
            {!! theme()->getSvgIcon("icons/duotune/general/gen049.svg", "svg-icon-3 svg-icon-info me-2") !!}
            <div class="fs-2 fw-bolder">{{ $stats['total_siswas'] ?? 0 }}</div>
        </div>
        <div class="fw-bold fs-6 text-gray-400">Total Siswa</div>
    </div>
@elseif($userRole === 'guru' || $userRole === 'kesiswaan')
    <!-- Total Kelas -->
    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
        <div class="d-flex align-items-center">
            {!! theme()->getSvgIcon("icons/duotune/general/gen018.svg", "svg-icon-3 svg-icon-primary me-2") !!}
            <div class="fs-2 fw-bolder">{{ $stats['total_kelas'] ?? 0 }}</div>
        </div>
        <div class="fw-bold fs-6 text-gray-400">Total Kelas</div>
    </div>

    <!-- Total Mapel -->
    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
        <div class="d-flex align-items-center">
            {!! theme()->getSvgIcon("icons/duotune/general/gen028.svg", "svg-icon-3 svg-icon-success me-2") !!}
            <div class="fs-2 fw-bolder">{{ $stats['total_mapel'] ?? 0 }}</div>
        </div>
        <div class="fw-bold fs-6 text-gray-400">Total Mapel</div>
    </div>

@elseif($userRole === 'siswa')
    <!-- Persentase Kehadiran -->
    <div class="border border-gray-300 border-dashed rounded min-w-100px py-2 px-3 me-3 mb-3">
        <div class="d-flex align-items-center">
            {!! theme()->getSvgIcon("icons/duotune/graphs/gra010.svg", "svg-icon-3 svg-icon-success me-2") !!}
            <div class="fs-2 fw-bolder text-gray-800">{{ $stats['attendance_rate'] ?? '0%' }}</div>
        </div>
        <div class="fw-bold fs-7 text-gray-400">Kehadiran</div>
    </div>

    <!-- Izin -->
    <div class="border border-gray-300 border-dashed rounded min-w-100px py-2 px-3 me-3 mb-3">
        <div class="d-flex align-items-center">
            {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-3 svg-icon-primary me-2") !!}
            <div class="fs-2 fw-bolder text-gray-800">{{ $stats['izin'] ?? 0 }}</div>
        </div>
        <div class="fw-bold fs-7 text-gray-400">Izin</div>
    </div>

    <!-- Sakit -->
    <div class="border border-gray-300 border-dashed rounded min-w-100px py-2 px-3 me-3 mb-3">
        <div class="d-flex align-items-center">
            {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg", "svg-icon-3 svg-icon-warning me-2") !!}
            <div class="fs-2 fw-bolder text-gray-800">{{ $stats['sakit'] ?? 0 }}</div>
        </div>
        <div class="fw-bold fs-7 text-gray-400">Sakit</div>
    </div>

    <!-- Alpa -->
    <div class="border border-gray-300 border-dashed rounded min-w-100px py-2 px-3 me-3 mb-3">
        <div class="d-flex align-items-center">
            {!! theme()->getSvgIcon("icons/duotune/general/gen040.svg", "svg-icon-3 svg-icon-danger me-2") !!}
            <div class="fs-2 fw-bolder text-gray-800">{{ $stats['alpa'] ?? 0 }}</div>
        </div>
        <div class="fw-bold fs-7 text-gray-400">Alpa</div>
    </div>

    <!-- Total Pengaduan -->
    <div class="border border-gray-300 border-dashed rounded min-w-100px py-2 px-3 me-3 mb-3">
        <div class="d-flex align-items-center">
            {!! theme()->getSvgIcon("icons/duotune/communication/com007.svg", "svg-icon-3 svg-icon-info me-2") !!}
            <div class="fs-2 fw-bolder text-gray-800">{{ $stats['total_pengaduan'] ?? 0 }}</div>
        </div>
        <div class="fw-bold fs-7 text-gray-400">Total Pengaduan</div>
    </div>
@endif
