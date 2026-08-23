@php
    $siswa = $siswa ?? null;
    $guru = $guru ?? null;

    $avatarUrl = $user && $user->avatar_url ? $user->avatar_url : null;
    $initial = substr($user->name ?? (($guru && $guru->nama) ? $guru->nama : (($siswa && $siswa->nama) ? $siswa->nama : '?')), 0, 1);
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

    $roleName = match($profileRole) {
        'admin' => 'Administrator',
        'guru' => 'Guru',
        'kesiswaan' => 'Kesiswaan',
        'siswa' => 'Siswa',
        default => ucfirst($profileRole)
    };

    $statusVal = ($siswa && isset($siswa->status)) ? $siswa->status : ($user->status ?? 'aktif');
    $statusClass = $statusVal === 'aktif' ? 'badge-light-success' : 'badge-light-danger';
@endphp

<!--begin::Navbar-->
<div class="card mb-5 mb-xl-10">
    <div class="card-body pt-9 pb-0">
        <!--begin::Details-->
        <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
            <!--begin: Pic-->
            <div class="me-7 mb-4">
                <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                    @if($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="image" style="object-fit: cover;"/>
                    @else
                        <div class="symbol-label fs-1 bg-light-primary text-primary fw-bolder" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                            {{ $initial }}
                        </div>
                    @endif
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
                            <a href="#" class="text-gray-800 text-hover-primary fs-2 fw-bolder me-1">{{ $guru->nama ?? ($siswa->nama ?? $user->name) }}</a>
                            <span class="badge badge-light-primary fw-bolder ms-2 fs-8 py-1 px-3">{{ $roleName }}</span>
                            <span class="badge {{ $statusClass }} fw-bolder ms-2 fs-8 py-1 px-3">{{ ucfirst($statusVal) }}</span>
                        </div>
                        <!--end::Name-->

                        <!--begin::Info-->
                        <div class="d-flex flex-wrap fw-bold fs-6 mb-4 pe-2">
                            <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                {!! theme()->getSvgIcon("icons/duotune/communication/com006.svg", "svg-icon-4 me-1") !!}
                                {{ $roleName }}
                            </a>
                            <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                {!! theme()->getSvgIcon("icons/duotune/general/gen018.svg", "svg-icon-4 me-1") !!}
                                {{ $guru->alamat ?? ($siswa->alamat ?? 'Sekolah Presensi') }}
                            </a>
                            @php
                                $displayEmail = $guru->email ?? ($siswa->user->email ?? ($user->email ?? null));
                                if ($displayEmail && (str_ends_with($displayEmail, '@guru.internal') || str_ends_with($displayEmail, '@siswa.internal'))) {
                                    $displayEmail = null;
                                }
                            @endphp
                            <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                                {!! theme()->getSvgIcon("icons/duotune/communication/com011.svg", "svg-icon-4 me-1") !!}
                                {{ $displayEmail ?: '-' }}
                            </a>
                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end::User-->

                    <!--begin::Actions-->
                    <div class="d-flex my-4">
                        @if($siswa || $guru)
                        <a href="#" class="btn btn-sm btn-light me-3 switch-to-settings-tab">
                            {!! theme()->getSvgIcon("icons/duotune/art/art005.svg", "svg-icon-3") !!}
                            Edit Profil
                        </a>
                        @endif
                        <a href="#" class="btn btn-sm btn-primary me-3 switch-to-settings-tab-password">
                            {!! theme()->getSvgIcon("icons/duotune/communication/com013.svg", "svg-icon-3") !!}
                            Ganti Password
                        </a>
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Title-->

                <!--begin::Stats-->
                <div class="d-flex flex-wrap flex-stack">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-column flex-grow-1 pe-8">
                        <!--begin::Stats-->
                        <div class="d-flex flex-wrap">
                            @include('profile.partials.profile-stats', ['stats' => $stats, 'userRole' => $profileRole])
                        </div>
                        <!--end::Stats-->
                    </div>
                    <!--end::Wrapper-->

                    @if($profileRole !== 'siswa')
                    <!--begin::Progress-->
                    <div class="d-flex align-items-center w-200px w-sm-300px flex-column mt-3">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                            <span class="fw-bold fs-6 text-gray-400">Kelengkapan Profil</span>
                            <span class="fw-bolder fs-6">{{ $completionRate }}%</span>
                        </div>

                        <div class="h-5px mx-3 w-100 bg-light mb-3">
                            <div class="bg-success rounded h-5px" role="progressbar" style="width: {{ $completionRate }}%;" aria-valuenow="{{ $completionRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <!--end::Progress-->
                    @endif
                </div>
                <!--end::Stats-->
            </div>
            <!--end::Info-->
        </div>
        <!--end::Details-->

        <!--begin::Navs-->
        <div class="d-flex overflow-auto h-55px">
            @include('profile.partials.profile-tabs', [
                'userRole' => $userRole,
                'siswa' => $siswa,
                'guru' => $guru
            ])
        </div>
        <!--begin::Navs-->
    </div>
</div>
<!--end::Navbar-->

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Helper: aktifkan tab pengaturan (tidak ada di nav, langsung manipulasi pane)
        function showSettingsTab() {
            // Nonaktifkan semua tab nav & pane yang aktif
            document.querySelectorAll('#profileTabContent .tab-pane.show.active').forEach(function(pane) {
                pane.classList.remove('show', 'active');
            });
            document.querySelectorAll('.nav-link.active').forEach(function(link) {
                link.classList.remove('active');
            });

            // Aktifkan pane tab_pengaturan
            var settingsPane = document.getElementById('tab_pengaturan');
            if (settingsPane) {
                settingsPane.classList.add('show', 'active');
            }
        }

        // Tombol Edit Profil
        document.querySelectorAll('.switch-to-settings-tab').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                showSettingsTab();
                var pane = document.getElementById('tab_pengaturan');
                if (pane) pane.scrollIntoView({ behavior: 'smooth' });
            });
        });

        // Tombol Ganti Password
        document.querySelectorAll('.switch-to-settings-tab-password').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                showSettingsTab();
                setTimeout(function() {
                    var passField = document.querySelector('input[name="current_password"]');
                    if (passField) {
                        passField.focus();
                        passField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 300);
            });
        });
    });
</script>
