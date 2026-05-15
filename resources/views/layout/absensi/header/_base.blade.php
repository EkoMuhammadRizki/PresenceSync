@php
    $hariId = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $bulanId = [
        1  => 'Januari', 2  => 'Februari', 3  => 'Maret',
        4  => 'April',   5  => 'Mei',       6  => 'Juni',
        7  => 'Juli',    8  => 'Agustus',   9  => 'September',
        10 => 'Oktober', 11 => 'November',  12 => 'Desember',
    ];

    $now        = now();
    $namaHari   = $hariId[$now->dayOfWeek];
    $namaBulan  = $bulanId[$now->month];
    $tanggalStr = $namaHari . ', ' . $now->day . ' ' . $namaBulan . ' ' . $now->year;
@endphp

<!--begin::Header-->
<div id="kt_header" style="" class="header {{ theme()->printHtmlClasses('header', false) }} align-items-stretch" {{ theme()->printHtmlAttributes('header') }}>
	<!--begin::Container-->
	<div class="{{ theme()->printHtmlClasses('header-container', false) }} d-flex align-items-stretch justify-content-between">
		<!--begin::Aside mobile toggle-->
		@if (theme()->getOption('layout', 'aside/display') === true)
			<div class="d-flex align-items-center d-lg-none ms-n3 me-1" data-bs-toggle="tooltip" title="Show aside menu">
				<div class="btn btn-icon btn-active-light-primary" id="kt_aside_mobile_toggle">
					{!! theme()->getSvgIcon("icons/duotune/abstract/abs015.svg", "svg-icon-2x mt-1") !!}
				</div>
			</div>
		@endif
		<!--end::Aside mobile toggle-->

		@if (theme()->getOption('layout', 'aside/display') === false)
			<!--begin::Logo-->
			<div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0 me-lg-15">
				<a href="{{ theme()->getPageUrl('') }}">
					<span class="text-dark fs-3 fw-bolder">PresenceSync</span>
				</a>
			</div>
			<!--end::Logo-->
		@else
			<!--begin::Mobile logo-->
			<div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
				<a href="{{ theme()->getPageUrl('') }}" class="d-lg-none">
					<span class="text-dark fs-5 fw-bolder">PresenceSync</span>
				</a>
			</div>
			<!--end::Mobile logo-->
		@endif

		<!--begin::Wrapper-->
		<div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
			<!--begin::Page Title & Date-->
            <div class="d-flex flex-column justify-content-center ms-5">
                <h1 class="text-dark fw-bolder fs-3 mb-0">Dashboard Admin</h1>
                <span class="text-gray-400 fs-7 fw-bold">{{ $tanggalStr }}</span>
            </div>
			<!--end::Page Title & Date-->

			<!--begin::Topbar-->
	        <div class="d-flex align-items-stretch flex-shrink-0">
                {{ theme()->getView('layout/header/__topbar') }}
			</div>
			<!--end::Topbar-->
		</div>
		<!--end::Wrapper-->
	</div>
	<!--end::Container-->
</div>
<!--end::Header-->
