@php
    $breadcrumb = (!empty($customBreadcrumbs ?? null)) ? $customBreadcrumbs : bootstrap()->getBreadcrumb();
    $pageTitle  = theme()->getPageTitle();
@endphp

<!--begin::Toolbar-->
@if (!empty($breadcrumb) || !empty($toolbarActions ?? null))
<div class="card card-flush py-3 px-6 d-flex flex-row align-items-center justify-content-between mb-6">
    <div class="page-title d-flex flex-column me-3">
        @if (!empty($breadcrumb))
            <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                @foreach ($breadcrumb as $item)
                    @if ($item['active'] === true)
                        <li class="breadcrumb-item text-dark fw-bolder">{{ $item['title'] }}</li>
                    @else
                        <li class="breadcrumb-item text-muted">
                            @if (!empty($item['path']))
                                @php
                                    $url = ($item['path'] === 'index' || strtolower($item['title']) === 'home') ? theme()->getPageUrl('index') : theme()->getPageUrl($item['path']);
                                @endphp
                                <a href="{{ $url }}" class="text-muted text-hover-primary">{{ $item['title'] }}</a>
                            @else
                                {{ $item['title'] }}
                            @endif
                        </li>
                    @endif
                    @if (!$loop->last)
                        <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px ms-2 me-2"></span></li>
                    @endif
                @endforeach
            </ul>
        @endif
    </div>
    @if (!empty($toolbarActions ?? null))
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            {!! $toolbarActions !!}
        </div>
    @endif
</div>
@endif
<!--end::Toolbar-->

