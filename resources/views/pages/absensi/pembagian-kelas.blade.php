<x-base-layout>
@include('pages.absensi._partials.toolbar')

@if(session('success'))
    <div class="alert alert-success d-flex align-items-center p-5 mb-10">
        <span class="svg-icon svg-icon-2hx svg-icon-success me-4">
            {!! theme()->getSvgIcon("icons/duotune/general/gen048.svg") !!}
        </span>
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-dark">Sukses</h4>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
        <span class="svg-icon svg-icon-2hx svg-icon-danger me-4">
            {!! theme()->getSvgIcon("icons/duotune/general/gen040.svg") !!}
        </span>
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-dark">Error</h4>
            <span>{{ session('error') }}</span>
        </div>
    </div>
@endif

<div class="card mt-2">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                <input type="text" id="search_pembagian" class="form-control form-control-solid w-250px ps-14" placeholder="Cari kelas..." />
            </div>
        </div>
    </div>
    <div class="card-body py-4">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_pembagian">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="min-w-200px">Nama Kelas</th>
                    <th class="min-w-120px">Jumlah Siswa</th>
                    <th class="text-end min-w-70px">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @foreach ($kelas as $item)
                <tr class="cursor-pointer row-clickable" data-href="{{ route('pembagian-kelas.show', $item->id) }}">
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-circle symbol-40px overflow-hidden me-3">
                                <div class="symbol-label fs-4 bg-light-primary text-primary fw-bolder">
                                    {{ substr($item->nama, 0, 1) }}
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <a href="{{ route('pembagian-kelas.show', $item->id) }}" class="text-gray-800 text-hover-primary fw-bolder">{{ $item->nama }}</a>
                                <span class="text-muted fs-7">Tingkat {{ $item->tingkat }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-light-{{ $item->siswas_count > 0 ? 'primary' : 'warning' }} fs-7 fw-bolder">
                            {{ $item->siswas_count }} siswa
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="{{ route('pembagian-kelas.show', $item->id) }}" class="menu-link px-3">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#kt_table_pembagian').DataTable({
        dom:'<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>',
        info: true,
        order: [],
        pageLength: 10,
        lengthChange: true,
        columnDefs: [{orderable: false, targets: [2]}]
    });

    $('#search_pembagian').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Re-init Metronic menu instances on table redraw
    table.on('draw', function() {
        if (window.KTMenu) {
            KTMenu.createInstances();
        }
    });

    // Row click navigation (except action column)
    $(document).on('click', '.row-clickable td:not(:last-child)', function() {
        var href = $(this).closest('tr').data('href');
        if (href) {
            window.location.href = href;
        }
    });
});
</script>

<style>
    .row-clickable {
        transition: background-color 0.15s ease;
    }
    .row-clickable:hover {
        background-color: rgba(var(--bs-component-hover-bg), 1) !important;
        background-color: #f1faff !important;
        cursor: pointer;
    }
</style>
@endsection
</x-base-layout>
