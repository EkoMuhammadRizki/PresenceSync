<!--begin::Menu-->
<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
    <!--begin::Menu item-->
    <div class="menu-item px-3">
        <div class="menu-content d-flex align-items-center px-3">
            <!--begin::Avatar-->
            <div class="symbol symbol-50px me-5">
                <img alt="Logo" src="{{ auth()->user()->avatar_url }}" style="object-fit: cover; object-position: center; border-radius: 0.475rem;"/>
            </div>
            <!--end::Avatar-->

            <!--begin::Username-->
            <div class="d-flex flex-column">
                <div class="fw-bolder d-flex align-items-center fs-5">
                    {{ auth()->user()->name }}
                </div>
                @php
                    $displayIdentifier = auth()->user()->email;
                    if (str_ends_with($displayIdentifier, '@siswa.internal')) {
                        $displayIdentifier = auth()->user()->siswa->nis ?? str_replace('@siswa.internal', '', $displayIdentifier);
                    }
                @endphp
                <span class="fw-bold text-muted fs-7">{{ $displayIdentifier }}</span>
            </div>
            <!--end::Username-->
        </div>
    </div>
    <!--end::Menu item-->

    <!--begin::Menu separator-->
    <div class="separator my-2"></div>
    <!--end::Menu separator-->

    <!--begin::Menu item-->
    <div class="menu-item px-5">
        <a href="{{ theme()->getPageUrl('settings.index') }}" class="menu-link px-5" data-ajax="false">
            {{ __('Profil saya') }}
        </a>
    </div>
    <!--end::Menu item-->

    @if(auth()->user()->hasRole('siswa') || auth()->user()->siswa)
    <!--begin::Menu item-->
    <div class="menu-item px-5">
        <a href="{{ url('absensi/siswa/profil') }}" class="menu-link px-5" data-ajax="false">
            {{ __('Edit Profil Orang Tua') }}
        </a>
    </div>
    <!--end::Menu item-->
    @endif

    <!--begin::Menu item-->
    <div class="menu-item px-5">
        <a href="#" data-action="{{ theme()->getPageUrl('logout') }}" data-method="post" data-csrf="{{ csrf_token() }}" data-reload="true" class="button-ajax menu-link px-5">
            {{ __('Keluar') }}
        </a>
    </div>
    <!--end::Menu item-->


</div>
<!--end::Menu-->
