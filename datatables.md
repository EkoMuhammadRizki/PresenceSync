# Dokumentasi Implementasi DataTables & Header Dinamis - PresenceSync

## 1. Implementasi DataTables (11 Modul Sidebar)

Semua tabel di dalam sistem telah diseragamkan menggunakan framework **DataTables.net** dengan styling **Metronic 8**.

### A. Struktur File Blade
Setiap halaman tabel (contoh: `siswa.blade.php`, `guru.blade.php`, dll) mengikuti struktur standar berikut:

```blade
<x-base-layout>
    {{-- 1. Memanggil Toolbar & Breadcrumb --}}
    @include('pages.absensi._partials.toolbar')

    {{-- 2. Container Tabel --}}
    <div class="card mt-2">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                {{-- Search Box Custom --}}
                <div class="d-flex align-items-center position-relative my-1">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                    <input type="text" id="search_custom" class="form-control form-control-solid w-250px ps-14" placeholder="Cari..." />
                </div>
            </div>
            {{-- Toolbar Aksi (Tambah Data) --}}
            <div class="card-toolbar">
                <button class="btn btn-primary">Tambah Data</button>
            </div>
        </div>
        <div class="card-body py-4">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_data">
                <thead>...</thead>
                <tbody>...</tbody>
            </table>
        </div>
    </div>

    {{-- 3. Script Inisialisasi --}}
    @section('scripts')
    <script>
    $(document).ready(function() {
        // Inisialisasi DataTable
        var table = $('#kt_table_data').DataTable({
            dom: "<'table-responsive'tr><'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'li><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>",
            info: false,
            order: [],
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: [0, -1] } // Nonaktifkan sorting pada kolom No dan Aksi
            ]
        });

        // Event Listener Search Box Custom
        $('#search_custom').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
    </script>
    @endsection
</x-base-layout>
```

### B. Konfigurasi Assets (`config/absensi/pages.php`)
Agar library DataTables dimuat secara otomatis, setiap path halaman didaftarkan dalam config assets:

```php
'master' => array(
    'siswa' => array(
        'title' => 'Data Siswa',
        'view'  => 'absensi/siswa',
        'assets' => array(
            'custom' => array(
                'js' => array('plugins/custom/datatables/datatables.bundle.js'),
                'css' => array('plugins/custom/datatables/datatables.bundle.css'),
            ),
        ),
    ),
    // ... modul lainnya
),
```

---

## 2. Header Dinamis (Judul & Tanggal Hari)

Teks "Dashboard Admin" dan tanggal hari ini dipasang pada level **Layout Global** agar konsisten di semua halaman.

### A. Lokasi File
Modifikasi dilakukan pada file: `resources/views/layout/absensi/header/_base.blade.php`

### B. Logika Tanggal Indonesia (PHP/Laravel)
Untuk menampilkan hari dan bulan dalam Bahasa Indonesia, kita menggunakan array mapping di dalam blok `@php`:

```php
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
```

### C. Penempatan di Header
Komponen teks dimasukkan ke dalam wrapper header (`kt_header`) di sisi kiri:

```html
<div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
    <!-- Konten Baru -->
    <div class="d-flex flex-column justify-content-center ms-5">
        <h1 class="text-dark fw-bolder fs-3 mb-0">Dashboard Admin</h1>
        <span class="text-gray-400 fs-7 fw-bold">{{ $tanggalStr }}</span>
    </div>
    
    <!-- Topbar (Search, Profile, etc) tetap di kanan -->
    <div class="d-flex align-items-stretch flex-shrink-0">
        {{ theme()->getView('layout/header/__topbar') }}
    </div>
</div>
```

---

## 3. Daftar Modul yang Diperbarui
Berikut adalah 11 modul yang telah menggunakan standar DataTables ini:
1.  **Manajemen Pengguna & Peran**
2.  **Log Aktivitas**
3.  **Data Siswa**
4.  **Data Guru**
5.  **Data Jurusan**
6.  **Data Kelas**
7.  **Tahun Ajaran**
8.  **Mata Pelajaran**
9.  **Jadwal Pelajaran**
10. **Aturan Jam Sekolah**
11. **Perangkat Fingerprint**

---

## 4. Keunggulan Implementasi Ini
*   **Zero PHP Error**: Semua tag breadcrumb dan toolbar yang rusak telah dibersihkan.
*   **Server-Side Ready**: Struktur tabel sudah siap untuk dihubungkan ke Controller Laravel/Yajra DataTables.
*   **Clean UI**: Tidak ada duplikasi judul halaman karena semua dipusatkan di Header.
*   **Lokalitas**: Tanggal otomatis menyesuaikan dengan waktu server dan menggunakan format Indonesia.
