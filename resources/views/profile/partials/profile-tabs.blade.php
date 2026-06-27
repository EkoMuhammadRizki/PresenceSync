@php
    $profileType = isset($siswa) ? 'siswa' : (isset($guru) ? 'guru' : 'admin');
@endphp

<ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder flex-nowrap">
    <!-- Student-specific Tabs -->
    @if($profileType === 'siswa')
        <li class="nav-item">
            <a class="nav-link text-active-primary me-6 active" data-bs-toggle="tab" href="#tab_riwayat_kehadiran">
                Informasi Siswa
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-active-primary me-6" data-bs-toggle="tab" href="#tab_riwayat">
                Riwayat Kehadiran
            </a>
        </li>
    @endif

    <!-- Teacher-specific Tabs -->
    @if($profileType === 'guru')
        <li class="nav-item">
            <a class="nav-link text-active-primary me-6 active" data-bs-toggle="tab" href="#tab_jadwal_dan_kelas">
                Informasi Dasar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-active-primary me-6" data-bs-toggle="tab" href="#tab_jadwal_mengajar">
                Jadwal Mengajar
            </a>
        </li>
    @endif

    <!-- Admin-specific Tabs -->
    @if($profileType === 'admin')
        <li class="nav-item">
            <a class="nav-link text-active-primary me-6 active" data-bs-toggle="tab" href="#tab_biodata">
                Profil
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-active-primary me-6" data-bs-toggle="tab" href="#tab_pengaturan">
                Edit Profil
            </a>
        </li>
    @endif
</ul>
