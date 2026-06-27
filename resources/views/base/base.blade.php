<!DOCTYPE html>
{{--
Product Name: {{ theme()->getOption('product', 'description') }}
Author: KeenThemes
Purchase: {{ theme()->getOption('product', 'purchase') }}
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
License: {{ theme()->getOption('product', 'license') }}
--}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"{!! theme()->printHtmlAttributes('html') !!} {{ theme()->printHtmlClasses('html') }}>
{{-- begin::Head --}}
<head>
    <meta charset="utf-8"/>
    <title>{{ ucfirst(theme()->getOption('meta', 'title')) }} | Keenthemes</title>
    <meta name="description" content="{{ ucfirst(theme()->getOption('meta', 'description')) }}"/>
    <meta name="keywords" content="{{ theme()->getOption('meta', 'keywords') }}"/>
    <link rel="canonical" href="{{ ucfirst(theme()->getOption('meta', 'canonical')) }}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="shortcut icon" href="{{ asset(theme()->getDemo() . '/' .theme()->getOption('assets', 'favicon')) }}"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- begin::Fonts --}}
    {{ theme()->includeFonts() }}
    {{-- end::Fonts --}}

    @if (theme()->hasOption('page', 'assets/vendors/css'))
        {{-- begin::Page Vendor Stylesheets(used by this page) --}}
        @foreach (array_unique(theme()->getOption('page', 'assets/vendors/css')) as $file)
            {!! preloadCss(assetCustom($file)) !!}
        @endforeach
        {{-- end::Page Vendor Stylesheets --}}
    @endif

    @if (theme()->hasOption('page', 'assets/custom/css'))
        {{-- begin::Page Custom Stylesheets(used by this page) --}}
        @foreach (array_unique(theme()->getOption('page', 'assets/custom/css')) as $file)
            {!! preloadCss(assetCustom($file)) !!}
        @endforeach
        {{-- end::Page Custom Stylesheets --}}
    @endif

    @if (theme()->hasOption('assets', 'css'))
        {{-- begin::Global Stylesheets Bundle(used by all pages) --}}
        @foreach (array_unique(theme()->getOption('assets', 'css')) as $file)
            @if (strpos($file, 'plugins') !== false)
                {!! preloadCss(assetCustom($file)) !!}
            @else
                <link href="{{ assetCustom($file) }}" rel="stylesheet" type="text/css"/>
            @endif
        @endforeach
        {{-- end::Global Stylesheets Bundle --}}
    @endif

    @if (theme()->getViewMode() === 'preview')
        {{ theme()->getView('partials/trackers/_ga-general') }}
        {{ theme()->getView('partials/trackers/_ga-tag-manager-for-head') }}
    @endif

    <style>
        /* ─── Status Badge: Lulus (Ungu) ──────────────────────────────────── */
        .badge-light-purple {
            color: #7239ea;
            background-color: #f8f5ff;
        }
        [data-bs-theme="dark"] .badge-light-purple {
            color: #b899f8;
            background-color: rgba(114, 57, 234, 0.2);
        }

        /* Custom Header Styling with Two Sorting/Filtering Buttons */
        .table th {
            position: relative;
            vertical-align: middle !important;
        }
        .table th .column-title {
            font-weight: 700;
        }
        .table th .column-actions {
            display: inline-flex;
            opacity: 0.35;
            transition: opacity 0.2s ease-in-out;
        }
        .table th:hover .column-actions,
        .table th.sorting_asc .column-actions,
        .table th.sorting_desc .column-actions {
            opacity: 1 !important;
        }
        .table th .column-actions button {
            border: none;
            background: transparent;
            cursor: pointer;
        }
        .table th .column-actions .dropdown-menu {
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
            border-radius: 0.475rem !important;
            z-index: 1050;
        }
        /* Disable standard DataTables background sorting arrows */
        .table th.sorting::after,
        .table th.sorting::before,
        .table th.sorting_asc::after,
        .table th.sorting_asc::before,
        .table th.sorting_desc::after,
        .table th.sorting_desc::before {
            display: none !important;
            content: none !important;
        }
        .table th.sorting,
        .table th.sorting_asc,
        .table th.sorting_desc {
            background-image: none !important;
            padding-right: 0.75rem !important;
        }

        /* Fix Select2 selection box vertical cutoff / height override */
        .select2-container--bootstrap5 .select2-selection--single {
            height: auto !important;
            display: flex !important;
            align-items: center !important;
        }
        .select2-container--bootstrap5 .select2-selection--single .select2-selection__rendered {
            line-height: normal !important;
            display: block !important;
            width: 100% !important;
        }

        /* Sleek premium top loading progress bar */
        #top-loading-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(to right, #009ef7, #50cd89);
            z-index: 99999;
            width: 0%;
            transition: width 0.3s ease, opacity 0.3s ease;
            box-shadow: 0 0 10px rgba(0, 158, 247, 0.5);
            opacity: 0;
            pointer-events: none;
        }

        /* ─── Responsive Layout Overrides ────────────────────────────────── */
        @media (max-width: 767.98px) {
            .card-header {
                flex-direction: column !important;
                align-items: stretch !important;
                height: auto !important;
                padding-top: 1.25rem !important;
                padding-bottom: 1.25rem !important;
                gap: 1rem !important;
            }
            .card-header .card-title {
                margin: 0 !important;
                width: 100% !important;
            }
            .card-header .card-title .position-relative {
                width: 100% !important;
            }
            .card-header .card-title input {
                width: 100% !important;
            }
            .card-header .card-toolbar {
                margin: 0 !important;
                width: 100% !important;
            }
            .card-header .card-toolbar .d-flex {
                width: 100% !important;
                flex-wrap: wrap !important;
                gap: 0.5rem !important;
            }
            .card-header .card-toolbar .d-flex > * {
                flex: 1 1 auto !important;
                justify-content: center !important;
                margin: 0 !important;
            }
        }

        @media (max-width: 991.98px) {
            .card-body {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
            }
        }

        /* Hide minimized logo by default in expanded aside mode */
        .aside .logo-minimize {
            display: none;
        }

        /* Shift content layout to prevent sidebar overlap when minimized aside is hovered on desktop */
        @media (min-width: 992px) {
            .aside-enabled.aside-fixed[data-kt-aside-minimize=on] .aside:hover ~ .wrapper {
                padding-left: 265px !important;
                transition: padding-left 0.3s ease;
            }

            .aside-enabled.aside-fixed.header-fixed[data-kt-aside-minimize=on] .aside:hover ~ .wrapper .header {
                left: 265px !important;
                transition: left 0.3s ease;
            }

            .aside-enabled.aside-fixed.toolbar-fixed[data-kt-aside-minimize=on] .aside:hover ~ .wrapper .toolbar {
                left: 265px !important;
                transition: left 0.3s ease;
            }
        }
    </style>

    {{-- SweetAlert2 CSS --}}
    <link href="{{ asset('absensi/plugins/custom/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css"/>

    @yield('styles')
</head>
{{-- end::Head --}}

{{-- begin::Body --}}
<body {!! theme()->printHtmlAttributes('body') !!} {!! theme()->printHtmlClasses('body') !!} {!! theme()->printCssVariables('body') !!}>

@if (theme()->getOption('layout', 'loader/display') === true)
    {{ theme()->getView('layout/_loader') }}
@endif

@yield('content')

{{-- begin::Javascript --}}
@if (theme()->hasOption('assets', 'js'))
    {{-- begin::Global Javascript Bundle(used by all pages) --}}
    @foreach (array_unique(theme()->getOption('assets', 'js')) as $file)
        <script src="{{ asset(theme()->getDemo() . '/' .$file) }}"></script>
    @endforeach
    {{-- end::Global Javascript Bundle --}}
@endif

@if (theme()->hasOption('page', 'assets/vendors/js'))
    {{-- begin::Page Vendors Javascript(used by this page) --}}
    @foreach (array_unique(theme()->getOption('page', 'assets/vendors/js')) as $file)
        <script src="{{ asset(theme()->getDemo() . '/' .$file) }}"></script>
    @endforeach
    {{-- end::Page Vendors Javascript --}}
@endif

@if (theme()->hasOption('page', 'assets/custom/js'))
    {{-- begin::Page Custom Javascript(used by this page) --}}
    @foreach (array_unique(theme()->getOption('page', 'assets/custom/js')) as $file)
        <script src="{{ asset(theme()->getDemo() . '/' .$file) }}"></script>
    @endforeach
    {{-- end::Page Custom Javascript --}}
@endif
{{-- end::Javascript --}}

@if (theme()->getViewMode() === 'preview')
    {{ theme()->getView('partials/trackers/_ga-tag-manager-for-body') }}
@endif

<script>
    if (window.jQuery && $.fn.dataTable) {
        $.extend(true, $.fn.dataTable.defaults, {
            pageLength: 5,
            lengthMenu: [5, 10, 25, 50, 100],
            language: {
                search: "Cari:",
                lengthMenu: "_MENU_",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                infoPostFix: "",
                loadingRecords: "Memuat...",
                zeroRecords: "Tidak ditemukan data yang sesuai",
                emptyTable: "Tidak ada data di dalam tabel",
                paginate: {
                    first: "Pertama",
                    previous: "Sebelumnya",
                    next: "Selanjutnya",
                    last: "Terakhir"
                },
                aria: {
                    sortAscending: ": aktifkan untuk mengurutkan kolom ke atas",
                    sortDescending: ": aktifkan untuk mengurutkan kolom ke bawah"
                }
            }
        });

        // Function to initialize custom header actions (Sort & Filter dropdown)
        function initCustomHeaders(tableEl) {
            var api = $(tableEl).DataTable();
            var ths = $(tableEl).find('thead th');
            
            ths.each(function() {
                var th = $(this);
                
                // Skip columns that are not sortable or are No / Aksi
                var titleText = th.text().trim();
                var isNoColumn = titleText.toLowerCase() === 'no';
                var isActionColumn = titleText.toLowerCase() === 'aksi' || th.hasClass('text-end') || titleText.toLowerCase() === 'action';
                
                if (isNoColumn || isActionColumn || th.hasClass('sorting_disabled') || th.data('orderable') === false) {
                    return;
                }
                
                // If already transformed, don't do it again
                if (th.find('.column-title').length > 0) {
                    return;
                }
                
                th.empty();
                
                var headerHtml = `
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <span class="column-title" style="cursor: pointer;">${titleText}</span>
                        <div class="d-flex align-items-center gap-1 column-actions ms-2">
                            <button type="button" class="btn btn-icon btn-active-color-primary btn-xs p-0 w-20px h-20px btn-sort" title="Urutkan">
                                <i class="bi bi-arrow-down-up fs-8 text-muted"></i>
                            </button>
                            <div class="dropdown column-filter-dropdown">
                                <button type="button" class="btn btn-icon btn-active-color-primary btn-xs p-0 w-20px h-20px btn-filter-menu" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" title="Filter & Urutan">
                                    <i class="bi bi-list fs-7 text-muted"></i>
                                </button>
                                <div class="dropdown-menu p-4 w-250px fs-7">
                                    <div class="mb-3">
                                        <select class="form-select form-select-solid form-select-sm filter-operator">
                                            <option value="contains" selected>Mengandung</option>
                                            <option value="equals">Sama Dengan</option>
                                            <option value="starts">Diawali</option>
                                            <option value="ends">Diakhiri</option>
                                        </select>
                                    </div>
                                    <div class="position-relative mb-3">
                                        <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                        <input type="text" class="form-control form-control-solid form-control-sm ps-9 filter-input" placeholder="Cari..." />
                                    </div>
                                    <div class="separator my-2"></div>
                                    <a href="#" class="dropdown-item py-2 px-3 btn-menu-sort-asc d-flex align-items-center gap-2">
                                        <i class="bi bi-sort-alpha-down text-muted fs-6"></i> Urutkan Ascending
                                    </a>
                                    <a href="#" class="dropdown-item py-2 px-3 btn-menu-sort-desc d-flex align-items-center gap-2">
                                        <i class="bi bi-sort-alpha-up text-muted fs-6"></i> Urutkan Descending
                                    </a>
                                    <a href="#" class="dropdown-item py-2 px-3 text-danger btn-menu-clear-sort d-flex align-items-center gap-2">
                                        <i class="bi bi-x-circle fs-6"></i> Bersihkan Urutan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                th.append(headerHtml);

                var actions = th.find('.column-actions');

                // UNBIND default sorting click handler from <th> to prevent clashes
                th.off('click.DT').off('click');

                // Helper to close dropdown after an action is triggered
                function closeDropdown(el) {
                    var dropdownToggle = th.find('.btn-filter-menu')[0];
                    if (dropdownToggle) {
                        if (window.bootstrap && bootstrap.Dropdown) {
                            var dd = bootstrap.Dropdown.getInstance(dropdownToggle);
                            if (dd) {
                                dd.hide();
                                return;
                            }
                        }
                        // Fallback: trigger a simulated click on the toggle button to close it
                        $(dropdownToggle).trigger('click');
                    }
                }

                // Prevent click inside dropdown-menu from closing it or triggering sorting
                actions.find('.dropdown-menu').on('click', function(e) {
                    e.stopPropagation();
                });

                // Manual Sorting function on title or sorting arrow click
                var toggleSort = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var colIdx = th.index();
                    var currentOrder = api.order();
                    var newDir = 'asc';
                    if (currentOrder.length && currentOrder[0][0] === colIdx) {
                        newDir = currentOrder[0][1] === 'asc' ? 'desc' : 'asc';
                    }
                    api.order([colIdx, newDir]).draw();
                };

                th.find('.column-title').on('click', toggleSort);
                actions.find('.btn-sort').on('click', toggleSort);

                // Keyup/change filter search
                actions.find('.filter-input').on('keyup change', function(e) {
                    e.stopPropagation();
                    var val = $(this).val();
                    var operator = $(this).closest('.dropdown-menu').find('.filter-operator').val();
                    var colIdx = th.index();
                    
                    var searchVal = val;
                    if (val) {
                        if (operator === 'equals') {
                            searchVal = '^' + $.fn.dataTable.util.escapeRegex(val) + '$';
                        } else if (operator === 'starts') {
                            searchVal = '^' + $.fn.dataTable.util.escapeRegex(val);
                        } else if (operator === 'ends') {
                            searchVal = $.fn.dataTable.util.escapeRegex(val) + '$';
                        } else {
                            searchVal = $.fn.dataTable.util.escapeRegex(val);
                        }
                        api.column(colIdx).search(searchVal, true, false).draw();
                    } else {
                        api.column(colIdx).search('').draw();
                    }
                });

                // Change filter operator dropdown
                actions.find('.filter-operator').on('change', function(e) {
                    e.stopPropagation();
                    $(this).closest('.dropdown-menu').find('.filter-input').trigger('change');
                });

                // Sort ascending from dropdown menu item
                actions.find('.btn-menu-sort-asc').on('click', function(e) {
                    e.preventDefault();
                    var colIdx = th.index();
                    api.order([colIdx, 'asc']).draw();
                    closeDropdown(this);
                });

                // Sort descending from dropdown menu item
                actions.find('.btn-menu-sort-desc').on('click', function(e) {
                    e.preventDefault();
                    var colIdx = th.index();
                    api.order([colIdx, 'desc']).draw();
                    closeDropdown(this);
                });

                // Clear sort from dropdown menu item
                actions.find('.btn-menu-clear-sort').on('click', function(e) {
                    e.preventDefault();
                    var colIdx = th.index();
                    api.order([]).draw();
                    
                    // Clear search input as well
                    th.find('.filter-input').val('').trigger('change');
                    closeDropdown(this);
                });
            });
            
            // Trigger draw event to update icon states
            api.draw(false);
        }

        // Global Event Listeners for Header Actions
        $(document).ready(function() {
            $('.table').each(function() {
                if ($.fn.DataTable.isDataTable(this)) {
                    initCustomHeaders(this);
                }
            });
        });

        $(document).on('init.dt', function(e, settings, json) {
            if (settings && settings.nTable) {
                initCustomHeaders(settings.nTable);
            }
        });

        // Keep icons synchronized with the active sorting direction
        $(document).on('draw.dt', function(e, settings) {
            var api = new $.fn.dataTable.Api(settings);
            var tableEl = api.table().node();
            
            $(tableEl).find('thead th').each(function() {
                var th = $(this);
                var sortBtnIcon = th.find('.btn-sort i');
                if (sortBtnIcon.length === 0) return;
                
                if (th.hasClass('sorting_asc')) {
                    sortBtnIcon.removeClass('bi-arrow-down-up bi-arrow-down').addClass('bi-arrow-up text-primary');
                } else if (th.hasClass('sorting_desc')) {
                    sortBtnIcon.removeClass('bi-arrow-down-up bi-arrow-up').addClass('bi-arrow-down text-primary');
                } else {
                    sortBtnIcon.removeClass('bi-arrow-up bi-arrow-down text-primary').addClass('bi-arrow-down-up text-muted');
                }
            });
        });
    }
</script>

<!-- Inject sleek top loading progress bar element -->
<div id="top-loading-bar"></div>

<div id="page-scripts-container">
    @yield('scripts')
</div>

<script>
    // Premium Single-page navigation transition handler with jQuery & History API
    $(function() {
        var $progressBar = $('#top-loading-bar');
        var progressInterval;

        function startProgress() {
            clearInterval(progressInterval);
            $progressBar.css({ width: '0%', opacity: 1 });
            var width = 0;
            progressInterval = setInterval(function() {
                if (width < 85) {
                    width += Math.random() * 15;
                    $progressBar.css('width', width + '%');
                }
            }, 200);
        }

        function completeProgress() {
            clearInterval(progressInterval);
            $progressBar.css('width', '100%');
            setTimeout(function() {
                $progressBar.css('opacity', 0);
                setTimeout(function() {
                    $progressBar.css('width', '0%');
                }, 300);
            }, 200);
        }

        function loadPage(url, pushToHistory) {
            startProgress();
            
            // Hide open drawer menus on transition, but preserve the desktop aside menu
            if (window.KTDrawer && typeof KTDrawer.hideAll === 'function') {
                var asideEl = document.getElementById('kt_aside');
                if (window.innerWidth >= 992 && asideEl) {
                    KTDrawer.hideAll(asideEl);
                } else {
                    KTDrawer.hideAll();
                }
            }
            
            // Clean up any existing active tooltips or popovers to prevent ghosting
            if (window.bootstrap) {
                $('[data-bs-toggle="tooltip"]').each(function() {
                    var t = bootstrap.Tooltip.getInstance(this);
                    if (t) t.dispose();
                });
                $('[data-bs-toggle="popover"]').each(function() {
                    var p = bootstrap.Popover.getInstance(this);
                    if (p) p.dispose();
                });
            }

            // Fetch page content via AJAX
            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'html',
                success: function(response) {
                    // Parse response using document context for full script parsing
                    var $tempDom = $('<div>').append($.parseHTML(response, document, true));
                    
                    var newContent = $tempDom.find('#kt_content').html();
                    var newAsideMenu = $tempDom.find('#kt_aside_menu_wrapper').html();
                    var newTitle = $tempDom.find('title').text() || $tempDom.filter('title').text();
                    var newScripts = $tempDom.find('#page-scripts-container').html();
                    
                    // Fallback to normal loading if content wrapper is not found (e.g. redirected to login page)
                    if (!newContent) {
                        window.location.href = url;
                        return;
                    }
                    
                    // Update document title
                    if (newTitle) {
                        document.title = newTitle;
                    }
                    
                    // Replace main content
                    $('#kt_content').html(newContent);
                    
                    // Replace sidebar menu content if exists to keep active navigation classes updated
                    if (newAsideMenu && $('#kt_aside_menu_wrapper').length) {
                        $('#kt_aside_menu_wrapper').html(newAsideMenu);
                    }
                    
                    // Inject and automatically execute new page script blocks
                    if ($('#page-scripts-container').length) {
                        $('#page-scripts-container').html(newScripts || '');
                    }
                    
                    // Scroll to top instantly
                    window.scrollTo({ top: 0, behavior: 'instant' });
                    
                    // Push states to window history
                    if (pushToHistory) {
                        history.pushState({ url: url }, newTitle, url);
                    }
                    
                    // Re-initialize Metronic UI components
                    if (window.KTMenu) KTMenu.createInstances();
                    if (window.KTDrawer) KTDrawer.createInstances();
                    if (window.KTScroll) KTScroll.createInstances();
                    if (window.KTScrolltop) KTScrolltop.createInstances();
                    if (window.KTToggle) KTToggle.createInstances();
                    if (window.KTSwapper) KTSwapper.createInstances();
                    
                    // Re-initialize layout apps & Bootstrap bindings
                    if (window.KTApp && typeof KTApp.init === 'function') {
                        KTApp.init();
                    }
                    
                    completeProgress();
                },
                error: function() {
                    completeProgress();
                    // Fallback to standard page load in case of failure
                    window.location.href = url;
                }
            });
        }

        // Intercept clicks on links for SPA transition
        $(document).on('click', 'a', function(e) {
            var href = $(this).attr('href');
            
            // Skip hash links and javascript: calls
            if (!href || href === '#' || href.startsWith('#') || href.startsWith('javascript:')) {
                return;
            }
            
            // Skip open in new tab / window or shortcut modifiers
            if ($(this).attr('target') === '_blank' || e.ctrlKey || e.metaKey || e.shiftKey || e.button !== 0) {
                return;
            }
            
            // Skip opted out links
            if ($(this).attr('data-ajax') === 'false' || $(this).attr('data-pjax') === 'false' || $(this).closest('[data-ajax="false"]').length) {
                return;
            }
            
            // Skip system routes that require standard reload (auth/logout actions)
            if (href.indexOf('logout') !== -1 || href.indexOf('login') !== -1 || href.indexOf('register') !== -1) {
                return;
            }
            
            // Verify if link origin is internal
            var isInternal = false;
            try {
                var originUrl = new URL(href, window.location.href);
                if (originUrl.origin === window.location.origin) {
                    isInternal = true;
                    href = originUrl.pathname + originUrl.search + originUrl.hash;
                }
            } catch (err) {
                // Relative path
                isInternal = true;
            }
            
            if (isInternal) {
                e.preventDefault();
                loadPage(href, true);
            }
        });

        // Listen to native back/forward browser buttons
        window.addEventListener('popstate', function(e) {
            loadPage(window.location.pathname + window.location.search + window.location.hash, false);
        });
    });
</script>

{{-- SweetAlert2 JS --}}
<script src="{{ asset('absensi/plugins/custom/sweetalert2/sweetalert2.all.min.js') }}"></script>

{{-- Global SweetAlert2 Handler --}}
<script>
(function() {
    // ─── Konfigurasi Tema Global SweetAlert2 ───────────────────────────────
    const SwalBase = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-danger fw-bold px-6',
            cancelButton: 'btn btn-light fw-bold px-6 me-3',
        },
        buttonsStyling: false,
        reverseButtons: true,
        focusCancel: true,
    });

    const SwalSuccess = Swal.mixin({
        icon: 'success',
        timer: 2500,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: false,
    });

    const SwalError = Swal.mixin({
        icon: 'error',
        showConfirmButton: true,
        customClass: { confirmButton: 'btn btn-primary fw-bold px-6' },
        buttonsStyling: false,
    });

    const SwalInfo = Swal.mixin({
        icon: 'info',
        timer: 2500,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
    });

    // ─── Expose globally ──────────────────────────────────────────────────
    window.SwalBase    = SwalBase;
    window.SwalSuccess = SwalSuccess;
    window.SwalError   = SwalError;
    window.SwalInfo    = SwalInfo;

    // ─── Helper: Tampilkan konfirmasi hapus ───────────────────────────────
    window.konfirmasiHapus = function(options) {
        return SwalBase.fire(Object.assign({
            title: 'Hapus Data?',
            text: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
        }, options || {}));
    };

    // ─── Helper: Tampilkan konfirmasi simpan ──────────────────────────────
    window.konfirmasiSimpan = function(options) {
        return Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-primary fw-bold px-6',
                cancelButton: 'btn btn-light fw-bold px-6 me-3',
            },
            buttonsStyling: false,
            reverseButtons: true,
        }).fire(Object.assign({
            title: 'Simpan Data?',
            text: 'Pastikan semua data sudah benar.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Periksa Lagi',
        }, options || {}));
    };

    // ─── Global: Intercept tombol Hapus (class .btn-hapus atau text "Hapus" di menu) ─
    $(document).on('click', '.btn-hapus, a.menu-link.text-danger, button.btn-hapus', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $btn = $(this);
        konfirmasiHapus().then(function(result) {
            if (result.isConfirmed) {
                // Jika punya data-url, submit DELETE via form
                var url = $btn.data('url') || $btn.attr('href');
                if (url && url !== '#') {
                    var form = $('<form method="POST" style="display:none">'
                        + '<input name="_token" value="' + $('meta[name=csrf-token]').attr('content') + '">'
                        + '<input name="_method" value="DELETE">'
                        + '</form>');
                    form.attr('action', url);
                    $('body').append(form);
                    form.submit();
                } else {
                    // Trigger custom event agar page-level script bisa handle
                    $btn.trigger('hapus:confirmed');
                }
            }
        });
    });

    // ─── Global: Intercept form submit dengan class .form-konfirmasi ──────
    $(document).on('submit', 'form.form-konfirmasi', function(e) {
        e.preventDefault();
        var $form = $(this);
        var isDelete = ($form.find('input[name=_method]').val() || '').toUpperCase() === 'DELETE';
        var promise = isDelete
            ? konfirmasiHapus()
            : konfirmasiSimpan();
        promise.then(function(result) {
            if (result.isConfirmed) {
                $form.off('submit').submit();
            }
        });
    });

    // ─── Flash session: tampilkan notifikasi dari Laravel session ─────────
    $(document).ready(function() {
        showFlashMessages();
    });

    function showFlashMessages() {
        @if(session('success'))
        SwalSuccess.fire({ title: 'Berhasil!', text: '{{ addslashes(session('success')) }}' });
        @endif
        @if(session('error'))
        SwalError.fire({ title: 'Gagal!', text: '{{ addslashes(session('error')) }}' });
        @endif
        @if(session('warning'))
        SwalInfo.fire({ title: 'Perhatian', text: '{{ addslashes(session('warning')) }}', icon: 'warning' });
        @endif
        @if(session('info'))
        SwalInfo.fire({ title: 'Info', text: '{{ addslashes(session('info')) }}', icon: 'info' });
        @endif
    }

    // Re-trigger flash on AJAX page load (SPA navigation)
    $(document).on('page:loaded spa:loaded', function() {
        showFlashMessages();
    });

    // ─── Global: Intercept Logout ─────────────────────────────────────────
    $(document).on('click', 'a[href*="logout"], [data-action*="logout"]', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var url = $(this).attr('href') || $(this).data('action');
        if (url === '#' || url === 'javascript:;') {
            url = '/logout'; // fallback default logout route
        }

        Swal.fire({
            title: 'Apakah anda yakin ingin keluar?',
            text: "Anda akan keluar dari sesi aplikasi ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Keluar!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-danger fw-bold px-6',
                cancelButton: 'btn btn-light fw-bold px-6 me-3'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.isConfirmed) {
                // Submit logout form via POST
                var form = $('<form method="POST" style="display:none">'
                    + '<input name="_token" value="' + $('meta[name=csrf-token]').attr('content') + '">'
                    + '</form>');
                form.attr('action', url);
                $('body').append(form);
                form.submit();
            }
        });
    });

    // ─── Global: Checkbox Bulk Delete Toggle ───────────────────────────────
    
    // Select all checkboxes toggle
    $(document).on('change', 'thead input[type="checkbox"].select-all-checkbox', function() {
        var checked = this.checked;
        var table = $(this).closest('table');
        table.find('tbody input[type="checkbox"].select-item-checkbox').each(function() {
            this.checked = checked;
            $(this).trigger('change');
        });
    });

    // Check individual items and update Bulk Delete button visibility/count
    $(document).on('change', 'tbody input[type="checkbox"].select-item-checkbox', function() {
        var table = $(this).closest('table');
        var card = table.closest('.card');
        
        // Dynamically inject bulk delete button if not exists
        checkAndInjectBulkDeleteButton(table);
        
        var checkedCount = table.find('tbody input[type="checkbox"].select-item-checkbox:checked').length;
        var bulkDeleteBtn = card.find('.btn-bulk-delete-global');
        
        if (checkedCount > 0) {
            bulkDeleteBtn.find('.selected-count').text(checkedCount);
            bulkDeleteBtn.show();
        } else {
            bulkDeleteBtn.hide();
            // Also uncheck the select-all if no item is checked
            table.find('thead input[type="checkbox"].select-all-checkbox').prop('checked', false);
        }
        
        // If all items are checked, check select-all, else uncheck it
        var totalCount = table.find('tbody input[type="checkbox"].select-item-checkbox').length;
        if (checkedCount === totalCount && totalCount > 0) {
            table.find('thead input[type="checkbox"].select-all-checkbox').prop('checked', true);
        } else {
            table.find('thead input[type="checkbox"].select-all-checkbox').prop('checked', false);
        }
    });

    // Inject Bulk Delete Button
    function checkAndInjectBulkDeleteButton(table) {
        var card = table.closest('.card');
        var toolbar = card.find('.card-toolbar');
        if (toolbar.length && toolbar.find('.btn-bulk-delete-global').length === 0) {
            var buttonHtml = `
                <button type="button" class="btn btn-sm btn-danger me-3 btn-bulk-delete-global" style="display: none;">
                    <span class="svg-icon svg-icon-3 m-0 pe-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                            <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"/>
                            <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                        </svg>
                    </span>
                    Hapus Terpilih (<span class="selected-count">0</span>)
                </button>
            `;
            toolbar.prepend(buttonHtml);
        }
    }

    // Trigger Bulk Delete request
    $(document).on('click', '.btn-bulk-delete-global', function() {
        var card = $(this).closest('.card');
        var table = card.find('table');
        var checkedCheckboxes = table.find('tbody input[type="checkbox"].select-item-checkbox:checked');
        var ids = [];
        
        checkedCheckboxes.each(function() {
            ids.push($(this).val());
        });
        
        var type = table.data('bulk-type');
        
        if (!type) {
            // Static mockup support
            konfirmasiHapus({ 
                title: 'Hapus Terpilih?', 
                text: 'Data terpilih akan dihapus dari tampilan prototype.',
                confirmButtonText: 'Ya, Hapus!'
            }).then(function(result) {
                if (result.isConfirmed) {
                    checkedCheckboxes.each(function() {
                        var row = $(this).closest('tr');
                        if ($.fn.DataTable.isDataTable(table[0])) {
                            table.DataTable().row(row).remove().draw();
                        } else {
                            row.remove();
                        }
                    });
                    card.find('.btn-bulk-delete-global').hide();
                    SwalSuccess.fire({ title: 'Berhasil!', text: 'Data terpilih berhasil dihapus dari prototype.' });
                }
            });
            return;
        }

        konfirmasiHapus({ 
            title: 'Hapus Terpilih?', 
            text: 'Data terpilih akan dihapus permanen dari database!',
            confirmButtonText: 'Ya, Hapus!'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("bulk-delete") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        type: type,
                        ids: ids
                    },
                    success: function(response) {
                        if (response.success) {
                            SwalSuccess.fire({ title: 'Berhasil!', text: response.message }).then(function() {
                                window.location.reload();
                            });
                        } else {
                            SwalError.fire({ title: 'Gagal!', text: response.message || 'Terjadi kesalahan.' });
                        }
                    },
                    error: function(xhr) {
                        var errMsg = 'Terjadi kesalahan saat menghapus data.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        SwalError.fire({ title: 'Gagal!', text: errMsg });
                    }
                });
            }
        });
    });

    // Automatically check and inject bulk delete buttons for existing checked checkboxes on draw/init
    $(document).on('draw.dt init.dt', function(e, settings) {
        if (settings && settings.nTable) {
            var table = $(settings.nTable);
            if (table.find('tbody input[type="checkbox"].select-item-checkbox').length > 0) {
                checkAndInjectBulkDeleteButton(table);
            }
        }
    });
})();
</script>

</body>
{{-- end::Body --}}
</html>
