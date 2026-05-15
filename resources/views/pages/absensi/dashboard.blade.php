<x-base-layout>
@include('pages.absensi._partials.toolbar')


<!--begin::Placeholder Content-->
<div class="card">
    <div class="card-body">
        <div class="text-center py-10">
            <div class="mb-5">
                {!! theme()->getSvgIcon("icons/duotune/general/gen025.svg", "svg-icon-5x text-primary") !!}
            </div>
            <h3 class="text-dark fw-bolder mb-3">Selamat Datang di PresenceSync</h3>
            <div class="text-muted fw-bold fs-5">
                Gunakan menu di sebelah kiri untuk mengakses fitur sistem absensi.
            </div>
        </div>
    </div>
</div>
<!--end::Placeholder Content-->

</x-base-layout>
