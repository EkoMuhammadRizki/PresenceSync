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
    <!-- Total Mapel -->
    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
        <div class="d-flex align-items-center">
            {!! theme()->getSvgIcon("icons/duotune/general/gen028.svg", "svg-icon-3 svg-icon-primary me-2") !!}
            <div class="fs-2 fw-bolder">{{ $stats['total_mapel'] ?? 0 }}</div>
        </div>
        <div class="fw-bold fs-6 text-gray-400">Total Mapel</div>
    </div>

    <!-- Kehadiran -->
    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
        <div class="d-flex align-items-center">
            {!! theme()->getSvgIcon("icons/duotune/graphs/gra010.svg", "svg-icon-3 svg-icon-success me-2") !!}
            <div class="fs-2 fw-bolder">{{ $stats['attendance_rate'] ?? '100%' }}</div>
        </div>
        <div class="fw-bold fs-6 text-gray-400">Kehadiran</div>
    </div>

    <!-- Nilai Rata-rata -->
    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
        <div class="d-flex align-items-center">
            {!! theme()->getSvgIcon("icons/duotune/graphs/gra008.svg", "svg-icon-3 svg-icon-info me-2") !!}
            <div class="fs-2 fw-bolder">{{ number_format($stats['nilai_rata'] ?? 85.0, 1) }}</div>
        </div>
        <div class="fw-bold fs-6 text-gray-400">Nilai Rata-rata</div>
    </div>
@endif
