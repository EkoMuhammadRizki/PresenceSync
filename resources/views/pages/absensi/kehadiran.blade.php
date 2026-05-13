<x-base-layout>
    @php
        $breadcrumb = bootstrap()->getBreadcrumb();
    @endphp

    <!--begin::Toolbar-->
    @if (!empty($breadcrumb))
    <div class="toolbar py-2 py-lg-3" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="{{ theme()->printHtmlClasses('toolbar-container', false) }} d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column me-3">
                <!--begin::Title-->
                <h1 class="d-flex text-dark fw-bolder my-1 fs-3">
                    Kehadiran Siswa
                </h1>
                <!--end::Title-->

                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                    @foreach ($breadcrumb as $item)
                        <!--begin::Item-->
                        @if ($item['active'] === true)
                            <li class="breadcrumb-item text-dark">
                                {{ $item['title'] }}
                            </li>
                        @else
                            <li class="breadcrumb-item text-muted">
                                @if (!empty($item['path']))
                                    <a href="{{ theme()->getPageUrl($item['path']) }}" class="text-muted text-hover-primary">
                                        {{ $item['title'] }}
                                    </a>
                                @else
                                    {{ $item['title'] }}
                                @endif
                            </li>
                        @endif
                        <!--end::Item-->

                        @if (next($breadcrumb))
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-200 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                        @endif
                    @endforeach
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
        </div>
        <!--end::Container-->
    </div>
    @endif
    <!--end::Toolbar-->

    <!--begin::Card-->
    <div class="card mt-2 mt-lg-5">
        <!--begin::Card body-->
        <div class="card-body">
            <!--begin::Heading-->
            <div class="text-center mb-10">
                <h1 class="text-dark mb-3">Kehadiran Siswa</h1>
                <div class="text-muted fw-bold fs-5">
                    Halaman daftar kehadiran siswa sedang dipersiapkan.
                </div>
            </div>
            <!--end::Heading-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
</x-base-layout>
