<x-base-layout>
@include('pages.absensi._partials.toolbar')


    <!--begin::Card-->
    <div class="card mt-2 mt-lg-5">
        <!--begin::Card body-->
        <div class="card-body">
            <!--begin::Heading-->
            <div class="text-center mb-10">
                <h1 class="text-dark mb-3">{{ theme()->getPageTitle() }}</h1>
                <div class="text-muted fw-bold fs-5">
                    Halaman ini sedang dalam pengembangan.
                </div>
            </div>
            <!--end::Heading-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
</x-base-layout>
