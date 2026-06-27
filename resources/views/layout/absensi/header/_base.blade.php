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
			<div class="d-flex align-items-center d-lg-none ms-n3 me-1">
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
			<div class="d-flex align-items-center d-lg-none me-3">
				<a href="{{ theme()->getPageUrl('') }}">
					<span class="text-dark fs-6 fw-bolder">PresenceSync</span>
				</a>
			</div>
			<!--end::Mobile logo-->
		@endif

		<!--begin::Wrapper-->
		<div class="d-flex align-items-stretch justify-content-between flex-grow-1">
			<!--begin::Page Title & Date-->
            <div class="d-flex flex-column justify-content-center ms-2 ms-lg-5">
                @php
                    $user = auth()->user();
                    $roleName = 'Admin';
                    if ($user) {
                        if (\App\Models\Siswa::where('user_id', $user->id)->exists()) {
                            $roleName = 'Siswa';
                        } elseif ($user->hasRole('kesiswaan')) {
                            $roleName = 'Kesiswaan';
                        } elseif ($user->hasRole('orang_tua')) {
                            $roleName = 'Orang Tua';
                        } elseif (\App\Models\Guru::where('user_id', $user->id)->exists()) {
                            $roleName = 'Guru';
                        }
                    }
                @endphp
                <h1 class="text-dark fw-bolder fs-6 fs-lg-3 mb-0">Dashboard {{ $roleName }}</h1>
                <span class="text-gray-400 fs-8 fs-lg-7 fw-bold d-none d-sm-block">{{ $tanggalStr }}</span>
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
