<x-base-layout>
@include('pages.absensi._partials.toolbar')

<!--begin::Card-->
<div class="card mt-2">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                <input type="text" id="search_log" class="form-control form-control-solid w-250px ps-14" placeholder="Cari log aktivitas..." />
            </div>
        </div>
        <div class="card-toolbar">
            <div class="d-flex align-items-center position-relative my-1 me-3">
                {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-1 position-absolute ms-4") !!}
                <input type="text" id="filter_tanggal" class="form-control form-control-solid w-275px ps-12" placeholder="Pilih Rentang Tanggal" readonly="readonly" />
            </div>
            <button type="button" id="reset_filter_tanggal" class="btn btn-light-danger btn-sm my-1" style="display: none;">
                Reset
            </button>
        </div>
    </div>
    <div class="card-body py-4">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_log">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-30px">No</th>
                    <th class="min-w-100px">Username</th>
                    <th class="min-w-120px">Nama</th>
                    <th class="min-w-200px">Aktivitas</th>
                    <th class="min-w-80px">Metode</th>
                    <th class="min-w-150px">Waktu</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                <tr class="row-clickable" data-username="admin.sekolah" data-nama="Administrator" data-aktivitas="Login ke sistem" data-metode="POST" data-waktu="15 Mei 2026, 08:30">
                    <td>1</td><td>admin.sekolah</td><td>Administrator</td>
                    <td>Login ke sistem</td>
                    <td><span class="badge badge-light-success fw-bolder">POST</span></td>
                    <td>15 Mei 2026, 08:30</td>
                </tr>
                <tr class="row-clickable" data-username="guru.budi" data-nama="Budi Santoso" data-aktivitas="Melihat data kehadiran kelas X-1" data-metode="GET" data-waktu="15 Mei 2026, 09:15">
                    <td>2</td><td>guru.budi</td><td>Budi Santoso</td>
                    <td>Melihat data kehadiran kelas X-1</td>
                    <td><span class="badge badge-light-primary fw-bolder">GET</span></td>
                    <td>15 Mei 2026, 09:15</td>
                </tr>
                <tr class="row-clickable" data-username="admin.sekolah" data-nama="Administrator" data-aktivitas="Menambah data siswa baru" data-metode="POST" data-waktu="15 Mei 2026, 10:00">
                    <td>3</td><td>admin.sekolah</td><td>Administrator</td>
                    <td>Menambah data siswa baru</td>
                    <td><span class="badge badge-light-success fw-bolder">POST</span></td>
                    <td>15 Mei 2026, 10:00</td>
                </tr>
                <tr class="row-clickable" data-username="guru.siti" data-nama="Siti Rahayu" data-aktivitas="Mengubah jadwal pelajaran Matematika" data-metode="PUT" data-waktu="15 Mei 2026, 11:30">
                    <td>4</td><td>guru.siti</td><td>Siti Rahayu</td>
                    <td>Mengubah jadwal pelajaran Matematika</td>
                    <td><span class="badge badge-light-warning fw-bolder">PUT</span></td>
                    <td>15 Mei 2026, 11:30</td>
                </tr>
                <tr class="row-clickable" data-username="siswa.ahmad" data-nama="Ahmad Subarjo" data-aktivitas="Melakukan presensi masuk (fingerprint)" data-metode="POST" data-waktu="15 Mei 2026, 06:45">
                    <td>5</td><td>siswa.ahmad</td><td>Ahmad Subarjo</td>
                    <td>Melakukan presensi masuk (fingerprint)</td>
                    <td><span class="badge badge-light-success fw-bolder">POST</span></td>
                    <td>15 Mei 2026, 06:45</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<style>
    .row-clickable {
        transition: background-color 0.15s ease;
    }
    .row-clickable:hover {
        background-color: var(--bs-table-hover-bg) !important;
        cursor: pointer;
    }
</style>

@section('scripts')
<script src="{{ asset(theme()->getDemo() . '/plugins/custom/flatpickr/flatpickr.bundle.js') }}"></script>
<script>
$(document).ready(function() {
    var table = $('#kt_table_log').DataTable({ 
        dom: '<\'table-responsive\'tr><\'row\'<\'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start\'li><\'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end\'p>>', 
        info: true, 
        order: [[5, 'desc']], 
        pageLength: 5, 
        lengthChange: true, 
        columnDefs: [{orderable: false, targets: 0}] 
    });

    $(document).on('click', '.row-clickable', function() {
        var el = $(this);
        var metode = el.data('metode');
        var metodeClass = metode === 'POST' ? 'success' : (metode === 'PUT' ? 'warning' : 'primary');
        Swal.fire({
            title: 'Detail Log Aktivitas',
            html: '<div class="text-start">' +
                '<div class="mb-3"><strong>Username:</strong> ' + el.data('username') + '</div>' +
                '<div class="mb-3"><strong>Nama:</strong> ' + el.data('nama') + '</div>' +
                '<div class="mb-3"><strong>Aktivitas:</strong> ' + el.data('aktivitas') + '</div>' +
                '<div class="mb-3"><strong>Metode:</strong> <span class="badge badge-light-' + metodeClass + ' fw-bolder">' + metode + '</span></div>' +
                '<div class="mb-3"><strong>Waktu:</strong> ' + el.data('waktu') + '</div>' +
                '</div>',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#009EF7',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-primary'
            }
        });
    });

    $('#search_log').on('keyup', function() { 
        table.search(this.value).draw(); 
    });

    // Lokalisasi Bahasa Indonesia untuk Flatpickr
    var indonesianLocale = {
        firstDayOfWeek: 1,
        weekdays: {
            shorthand: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
            longhand: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"]
        },
        months: {
            shorthand: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agt", "Sep", "Okt", "Nov", "Des"],
            longhand: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"]
        },
        rangeSeparator: " hingga "
    };

    // Parser tanggal format Indonesia (e.g. "15 Mei 2026, 08:30")
    function parseIndonesianDate(dateStr) {
        if (!dateStr) return null;
        var cleanStr = dateStr.trim();
        var parts = cleanStr.split(',')[0].split(' ');
        if (parts.length < 3) return null;
        
        var day = parseInt(parts[0], 10);
        var monthStr = parts[1].toLowerCase();
        var year = parseInt(parts[2], 10);
        
        var months = {
            'januari': 0, 'februari': 1, 'maret': 2, 'april': 3, 'mei': 4, 'juni': 5,
            'juli': 6, 'agustus': 7, 'september': 8, 'oktober': 9, 'november': 10, 'desember': 11,
            'jan': 0, 'feb': 1, 'mar': 2, 'apr': 3, 'mei': 4, 'jun': 5, 'jul': 6, 'agt': 7, 'sep': 8, 'okt': 9, 'nov': 10, 'des': 11
        };
        
        var month = months[monthStr] !== undefined ? months[monthStr] : -1;
        if (month === -1) return null;
        
        var hours = 0;
        var minutes = 0;
        var timePart = cleanStr.split(',')[1];
        if (timePart) {
            var timeParts = timePart.trim().split(':');
            if (timeParts.length >= 2) {
                hours = parseInt(timeParts[0], 10);
                minutes = parseInt(timeParts[1], 10);
            }
        }
        
        return new Date(year, month, day, hours, minutes);
    }

    // Daftarkan kustom filter pencarian tanggal ke DataTables
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'kt_table_log') {
                return true;
            }
            
            var dateVal = $('#filter_tanggal').val();
            if (!dateVal) {
                return true;
            }
            
            var fp = document.getElementById("filter_tanggal")._flatpickr;
            if (!fp || fp.selectedDates.length === 0) {
                return true;
            }
            
            var startDate = fp.selectedDates[0];
            var endDate = fp.selectedDates[1] || startDate;
            
            var minDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate(), 0, 0, 0);
            var maxDate = new Date(endDate.getFullYear(), endDate.getMonth(), endDate.getDate(), 23, 59, 59);
            
            var rowDateStr = data[5]; // Kolom Waktu
            var rowDate = parseIndonesianDate(rowDateStr);
            
            if (!rowDate) {
                return true;
            }
            
            return rowDate >= minDate && rowDate <= maxDate;
        }
    );

    // Inisialisasi Flatpickr
    var fp = $("#filter_tanggal").flatpickr({
        mode: "range",
        dateFormat: "Y-m-d",
        locale: indonesianLocale,
        onClose: function(selectedDates, dateStr, instance) {
            if (selectedDates.length > 0) {
                $('#reset_filter_tanggal').show();
            } else {
                $('#reset_filter_tanggal').hide();
            }
            table.draw();
        }
    });

    // Reset filter klik
    $('#reset_filter_tanggal').on('click', function() {
        if (fp) {
            fp.clear();
        }
        $(this).hide();
        table.draw();
    });
});
</script>
@endsection
</x-base-layout>
