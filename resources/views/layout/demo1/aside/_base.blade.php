@php
    $logoFileName = 'logo-1-dark.svg';

    if (theme()->getOption('layout', 'aside/theme') === 'light') {
        $logoFileName = 'logo-1.svg';
    }
@endphp

{{--begin::Aside--}}
<div
    id="kt_aside"
    class="aside {{ theme()->printHtmlClasses('aside', false) }}"
    data-kt-drawer="true"
    data-kt-drawer-name="aside"
    data-kt-drawer-activate="{default: true, lg: false}"
    data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'200px', '300px': '250px'}"
    data-kt-drawer-direction="start"
    data-kt-drawer-toggle="#kt_aside_mobile_toggle"
>

    {{--begin::Brand--}}
    <div class="aside-logo flex-column-auto" id="kt_aside_logo">
        {{--begin::Logo--}}
        <a href="{{ theme()->getPageUrl('') }}" class="d-flex align-items-center me-2">
            <div class="d-flex align-items-center logo-default brand-text-container">
                <img alt="Logo" src="{{ asset('demo1/media/logos/Siap_Logo.png') }}" class="h-40px me-3" />
                <div class="d-flex flex-column">
                    <span class="text-white fw-bolder fs-2 lh-1">SIAP</span>
                    <span class="text-gray-400 fs-9 lh-1 mt-1">Sistem Informasi Absensi Presensi</span>
                </div>
            </div>
            <img alt="Logo" src="{{ asset('demo1/media/logos/Siap_Logo.png') }}" class="h-40px logo-minimize" />
        </a>
        <style>
            /* Ketika sidebar mengecil & TIDAK di-hover: sembunyikan default/teks, tampilkan minimize logo */
            [data-kt-aside-minimize="on"] .aside:not(:hover) .aside-logo .logo-default,
            [data-kt-aside-minimize="on"] .aside:not(:hover) .aside-logo .brand-text-container {
                display: none !important;
            }
            [data-kt-aside-minimize="on"] .aside:not(:hover) .aside-logo .logo-minimize {
                display: inline-block !important;
            }

            /* Ketika sidebar mengecil & SEDANG di-hover: tampilkan default/teks, sembunyikan minimize logo */
            [data-kt-aside-minimize="on"] .aside:hover .aside-logo .logo-default,
            [data-kt-aside-minimize="on"] .aside:hover .aside-logo .brand-text-container {
                display: flex !important;
            }
            [data-kt-aside-minimize="on"] .aside:hover .aside-logo .logo-minimize {
                display: none !important;
            }
        </style>
        {{--end::Logo--}}

        @if (theme()->getOption('layout', 'aside/minimize') === true)
            {{--begin::Aside toggler--}}
            <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle"
                 data-kt-toggle="true"
                 data-kt-toggle-state="active"
                 data-kt-toggle-target="body"
                 data-kt-toggle-name="aside-minimize"
            >

                {!! theme()->getSvgIcon("icons/duotune/arrows/arr080.svg", "svg-icon-1 rotate-180") !!}
            </div>
            {{--end::Aside toggler--}}
        @endif
    </div>
    {{--end::Brand--}}

    {{--begin::Aside menu--}}
    <div class="aside-menu flex-column-fluid">
        {{ theme()->getView('layout/aside/_menu') }}
    </div>
    {{--end::Aside menu--}}

    {{--begin::Footer--}}
    <div class="aside-footer flex-column-auto pt-5 pb-7 px-5" id="kt_aside_footer">
        <a href="{{ theme()->getPageUrl('documentation/getting-started/overview') }}" class="btn btn-custom btn-primary w-100" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-delay-show="8000" title="Check out the complete documentation with over 100 components">
        <span class="btn-label">
            {{ __('Documentation') }}
        </span>
            {!! theme()->getSvgIcon("icons/duotune/general/gen005.svg", "btn-icon svg-icon-2") !!}
        </a>
    </div>
    {{--end::Footer--}}
</div>
{{--end::Aside--}}
