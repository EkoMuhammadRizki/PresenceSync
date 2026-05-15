@php
    $breadcrumb = bootstrap()->getBreadcrumb();
    $pageTitle  = theme()->getPageTitle();
@endphp

<!--begin::Toolbar-->
@if (!empty($breadcrumb))
<div class="toolbar py-2 py-lg-3" id="kt_toolbar">
    <div id="kt_toolbar_container" class="{{ theme()->printHtmlClasses('toolbar-container', false) }} d-flex flex-stack">
        <div class="page-title d-flex flex-column me-3">
            <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                @foreach ($breadcrumb as $item)
                    @if ($item['active'] === true)
                        <li class="breadcrumb-item text-dark">{{ $item['title'] }}</li>
                    @else
                        <li class="breadcrumb-item text-muted">
                            @if (!empty($item['path']))
                                <a href="{{ theme()->getPageUrl($item['path']) }}" class="text-muted text-hover-primary">{{ $item['title'] }}</a>
                            @else
                                {{ $item['title'] }}
                            @endif
                        </li>
                    @endif
                    @if (next($breadcrumb))
                        <li class="breadcrumb-item"><span class="bullet bg-gray-200 w-5px h-2px"></span></li>
                    @endif
                @endforeach
            </ul>
        </div>
        @if (!empty($toolbarActions ?? null))
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                {!! $toolbarActions !!}
            </div>
        @endif
    </div>
</div>
@endif
<!--end::Toolbar-->
