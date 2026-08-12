@php
    $siswa = $siswa ?? null;
    $guru = $guru ?? null;

    // Determine the profile owner's role rather than the logged-in visitor's role
    $profileRole = 'admin';
    if ($siswa) {
        $profileRole = 'siswa';
    } elseif ($guru) {
        if (isset($user) && $user->hasRole('kesiswaan')) {
            $profileRole = 'kesiswaan';
        } else {
            $profileRole = 'guru';
        }
    }
@endphp
<div class="row g-5 g-xxl-8">
    <!--begin::Col - Personal Info-->
    <div class="col-xl-6">
        <div class="card card-xl-stretch mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 text-dark">Informasi Pribadi</span>
                    <span class="text-muted mt-1 fw-bold fs-7">Biodata dasar pengguna</span>
                </h3>
            </div>
            <div class="card-body pt-3">
                <!-- Nama -->
                <div class="d-flex align-items-center mb-7">
                    <div class="flex-grow-1">
                        <span class="text-muted fw-bold d-block fs-7">Nama Lengkap</span>
                        <span class="text-gray-800 fw-bolder fs-6">{{ $guru->nama ?? ($siswa->nama ?? $user->name) }}</span>
                    </div>
                </div>

                <!-- NIP/NIS/Username -->
                <div class="d-flex align-items-center mb-7">
                    <div class="flex-grow-1">
                        @if($profileRole === 'admin')
                            <span class="text-muted fw-bold d-block fs-7">Username</span>
                            <span class="text-gray-800 fw-bolder fs-6">{{ $user->username }}</span>
                        @elseif($profileRole === 'guru' || $profileRole === 'kesiswaan')
                            <span class="text-muted fw-bold d-block fs-7">NIP (Nomor Induk Pegawai)</span>
                            <span class="text-gray-800 fw-bolder fs-6">{{ $guru->nip ?? '-' }}</span>
                        @else
                            <span class="text-muted fw-bold d-block fs-7">NIS</span>
                            <span class="text-gray-800 fw-bolder fs-6">
                                {{ $siswa->nis ?? '-' }}
                            </span>
                        @endif
                    </div>
                </div>

                @if($profileRole === 'siswa')
                    <!-- Jenis Kelamin -->
                    <div class="d-flex align-items-center mb-7">
                        <div class="flex-grow-1">
                            <span class="text-muted fw-bold d-block fs-7">Jenis Kelamin</span>
                            <span class="text-gray-800 fw-bolder fs-6">
                                {{ ($siswa->jenis_kelamin ?? '') === 'L' ? 'Laki-laki' : (($siswa->jenis_kelamin ?? '') === 'P' ? 'Perempuan' : '-') }}
                            </span>
                        </div>
                    </div>

                    <!-- Tanggal Lahir -->
                    <div class="d-flex align-items-center mb-7">
                        <div class="flex-grow-1">
                            <span class="text-muted fw-bold d-block fs-7">Tanggal Lahir</span>
                            <span class="text-gray-800 fw-bolder fs-6">
                                @if($siswa)
                                    {{ $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d F Y') : '-' }}
                                @else
                                    {{ $guru->tanggal_lahir ? \Carbon\Carbon::parse($guru->tanggal_lahir)->format('d F Y') : '-' }}
                                @endif
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Alamat -->
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted fw-bold d-block fs-7">Alamat Lengkap</span>
                        <span class="text-gray-800 fw-bolder fs-6">{{ $guru->alamat ?? ($siswa->alamat ?? '-') }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($kelasDiwali))
            <!-- Card - Kelas yang Diwali -->
            <div class="card mb-5 mb-xxl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 text-dark">Kelas yang Diwali</span>
                        <span class="text-muted mt-1 fw-bold fs-7">Daftar kelas tempat Anda menjadi Wali Kelas</span>
                    </h3>
                </div>
                <div class="card-body pt-3 pb-5">
                    @forelse($kelasDiwali as $kelasItem)
                        <div class="d-flex align-items-center {{ !$loop->last ? 'mb-7' : '' }}">
                            <div class="symbol symbol-50px me-5">
                                <div class="symbol-label bg-light-primary text-primary fw-bolder fs-5">
                                    {{ $kelasItem->tingkat }}
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-dark fw-bolder fs-6">{{ $kelasItem->nama_lengkap }}</span>
                                <span class="text-muted d-block fw-bold fs-7">
                                    {{ $kelasItem->siswas_count }} Siswa • Status: {{ ucfirst($kelasItem->status) }}
                                </span>
                            </div>
                            <a href="{{ url('absensi/master/kelas/pembagian/' . $kelasItem->id) }}" class="btn btn-sm btn-light-primary">Lihat Detail Kelas</a>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            Guru ini tidak/belum menjadi Wali Kelas di kelas manapun.
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
    <!--end::Col-->

    <!--begin::Col - Contact & Account Info-->
    <div class="col-xl-6">
        <!--begin::Contact Info-->
        <div class="card mb-5 mb-xxl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 text-dark">Informasi Kontak</span>
                    <span class="text-muted mt-1 fw-bold fs-7">Detail kontak untuk komunikasi</span>
                </h3>
            </div>
            <div class="card-body pt-3 pb-5">


                <!-- Nomor HP -->
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted fw-bold d-block fs-7">Nomor HP / Telepon</span>
                        <span class="text-gray-800 fw-bolder fs-6">{{ $guru->no_hp ?? ($siswa->no_hp ?? '-') }}</span>
                    </div>
                </div>
                
                @php
                    $parentName = $siswa->nama_orang_tua ?? ($parentProfile->nama_ayah ?? ($parentProfile->nama_ibu ?? null));
                    $parentPhone = $siswa->no_hp_orang_tua ?? ($parentProfile->no_hp_ayah ?? ($parentProfile->no_hp_ibu ?? null));
                @endphp
                @if($parentName)
                    <!-- Orang Tua / Wali -->
                    <div class="d-flex align-items-center mt-7">
                        <div class="flex-grow-1">
                            <span class="text-muted fw-bold d-block fs-7">Nama Orang Tua & Kontak Wali</span>
                            <span class="text-gray-800 fw-bolder fs-6">
                                {{ $parentName }} ({{ $parentPhone ?? '-' }})
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!--end::Contact Info-->

        <!--begin::Account Info-->
        <div class="card mb-5 mb-xxl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 text-dark">Informasi Akun</span>
                    <span class="text-muted mt-1 fw-bold fs-7">Status keanggotaan dan log aktivitas</span>
                </h3>
            </div>
            <div class="card-body pt-3 pb-5">
                <!-- Status -->
                <div class="d-flex align-items-center mb-7">
                    <div class="flex-grow-1">
                        <span class="text-muted fw-bold d-block fs-7">Status Akun</span>
                        @php
                            $statusVal = ($siswa && isset($siswa->status)) ? $siswa->status : ($user->status ?? 'aktif');
                            $statusClass = $statusVal === 'aktif' ? 'badge-light-success' : 'badge-light-danger';
                        @endphp
                        <span class="badge {{ $statusClass }} fw-bolder fs-7">{{ ucfirst($statusVal) }}</span>
                    </div>
                </div>

                <!-- Terakhir Login -->
                <div class="d-flex align-items-center mb-7">
                    <div class="flex-grow-1">
                        <span class="text-muted fw-bold d-block fs-7">Terakhir Kali Login</span>
                        <span class="text-gray-800 fw-bolder fs-6">
                            {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d M Y, H:i') : '-' }}
                        </span>
                    </div>
                </div>

                <!-- Tanggal Terdaftar -->
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted fw-bold d-block fs-7">Tanggal Terdaftar</span>
                        <span class="text-gray-800 fw-bolder fs-6">
                            {{ $user->created_at ? $user->created_at->format('d F Y, H:i') : '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Account Info-->
    </div>
    <!--end::Col-->
</div>
