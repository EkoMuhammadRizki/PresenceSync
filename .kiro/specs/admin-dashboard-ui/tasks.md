# Implementation Plan: Admin Dashboard UI

## Overview

Membangun clickable prototype UI-only untuk dashboard admin sistem manajemen absensi sekolah berbasis fingerprint. Implementasi mengikuti pola yang sudah ada di project: config PHP array → Bootstrap class → Layout Blade views → Placeholder page. Tidak ada backend logic atau database query — seluruh konten statis/placeholder.

Bahasa implementasi: **PHP (Laravel 9)** dengan **Blade templating**.

## Tasks

- [x] 1. Buat konfigurasi demo `absensi` (config layer)
  - [x] 1.1 Buat file `config/absensi/general.php`
    - Salin struktur dari `config/demo1/general.php`
    - Set `layout/aside/display` → `true`, `layout/aside/theme` → `'dark'`, `layout/aside/menu` → `'main'`
    - Set `layout/aside/minimize` → `true`, `layout/aside/hoverable` → `true`
    - Set `layout/toolbar/display` → `false` (disederhanakan untuk prototype)
    - _Requirements: 7.4_

  - [x] 1.2 Buat file `config/absensi/menu.php` dengan struktur hierarki 3 level lengkap
    - Definisikan 7 item Level_1: Dashboard, Manajemen Pengguna & Peran, Master Data, Kehadiran Siswa, Laporan, Akun Saya, Logout
    - Tambahkan `'classes' => ['item' => 'menu-accordion']` dan `'attributes' => ['data-kt-menu-trigger' => 'click']` pada setiap item yang memiliki sub-menu
    - Definisikan 2 item Level_2 di bawah "Manajemen Pengguna & Peran": Data Pengguna & Hak Akses (`absensi/pengguna/data`), Log Aktivitas (`absensi/pengguna/log-aktivitas`)
    - Definisikan 9 item Level_2 di bawah "Master Data": Siswa, Guru, Jurusan, Kelas (parent), Tahun Ajaran, Mata Pelajaran, Jadwal Pelajaran, Aturan Jam Sekolah, Perangkat Fingerprint (parent)
    - Definisikan 2 item Level_3 di bawah "Kelas": Data Kelas (`absensi/master/kelas/data`), Pembagian Kelas (`absensi/master/kelas/pembagian`)
    - Definisikan 2 item Level_3 di bawah "Perangkat Fingerprint": Data Perangkat (`absensi/master/fingerprint/data`), Log Scan Fingerprint (`absensi/master/fingerprint/log`)
    - Gunakan `'bullet' => '<span class="bullet bullet-dot"></span>'` untuk item Level_2 dan Level_3 tanpa icon
    - Gunakan `'sub' => ['class' => 'menu-sub-accordion menu-active-bg', 'items' => [...]]` untuk Level_1 parent
    - Gunakan `'sub' => ['class' => 'menu-sub-accordion', 'items' => [...]]` untuk Level_2 parent
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 1.7, 6.1, 6.2, 6.4_

  - [ ]* 1.3 Tulis property test untuk Property 1: Item dengan sub-menu merender chevron arrow
    - **Property 1: Item dengan sub-menu merender chevron arrow**
    - Iterasi semua item menu config yang memiliki key `'sub'`, verifikasi output `Menu::build()` mengandung `menu-arrow`
    - **Validates: Requirements 2.6**

  - [ ]* 1.4 Tulis property test untuk Property 2: Setiap path di menu config memiliki route yang valid
    - **Property 2: Setiap path di menu config memiliki route yang valid**
    - Iterasi semua item dengan key `'path'` non-empty dan bukan `'#'`, verifikasi route terdaftar di aplikasi
    - **Validates: Requirements 3.1, 6.3**

  - [x] 1.5 Buat file `config/absensi/pages.php`
    - Definisikan entry untuk setiap path yang ada di menu config, semua menggunakan `'view' => 'absensi/placeholder'`
    - Sertakan `'title'` yang sesuai dengan nama menu item untuk setiap path
    - Struktur nested mengikuti segmen URL: `absensi.dashboard`, `absensi.pengguna.data`, `absensi.master.siswa`, dst.
    - _Requirements: 3.3–3.19, 5.1_

- [x] 2. Buat Bootstrap class untuk demo `absensi`
  - [x] 2.1 Buat file `app/Core/Bootstraps/BootstrapAbsensi.php`
    - Extends `BootstrapBase`, ikuti pola `BootstrapDemo1`
    - Implementasikan `initLayout()` yang memanggil: `initBase()`, `initHeader()`, `initPageTitle()`, `initContent()`, `initAside()`, `initFooter()`, `initAsideMenu()`
    - Implementasikan `initAsideMenu()` yang membuat instance `Menu` dari `Theme::getOption('menu', 'main')` dan `Theme::getPagePath()`
    - Implementasikan `getAsideMenu()` dan `getBreadcrumb()` (dengan `'skip-active' => false`)
    - Tidak perlu `initHorizontalMenu()` karena prototype tidak menggunakan horizontal menu
    - _Requirements: 1.1, 4.1, 4.2, 5.4_

  - [ ]* 2.2 Tulis unit test untuk BootstrapAbsensi
    - Verifikasi `BootstrapAbsensi` dapat di-instantiate dan `getAsideMenu()` mengembalikan instance `Menu`
    - Verifikasi `getBreadcrumb()` mengembalikan array
    - _Requirements: 1.1_

- [x] 3. Buat layout Blade views untuk demo `absensi`
  - [x] 3.1 Buat direktori `resources/views/layout/absensi/` dan salin file dari `layout/demo1/`
    - Salin `master.blade.php` — identik dengan demo1, tidak perlu modifikasi
    - Salin `_content.blade.php` — identik dengan demo1
    - Salin `_footer.blade.php` — identik dengan demo1
    - Salin `_scrolltop.blade.php` — identik dengan demo1
    - _Requirements: 5.3, 7.4_

  - [x] 3.2 Buat `resources/views/layout/absensi/aside/_base.blade.php`
    - Salin dari `layout/demo1/aside/_base.blade.php`
    - Hapus bagian "Aside footer" yang berisi link dokumentasi (tidak relevan untuk prototype)
    - Pertahankan struktur drawer, logo, aside toggle, dan aside menu
    - _Requirements: 7.1, 7.2, 7.3_

  - [x] 3.3 Buat `resources/views/layout/absensi/aside/_menu.blade.php`
    - Salin dari `layout/demo1/aside/_menu.blade.php`
    - Konten identik: memanggil `bootstrap()->getAsideMenu()` dan `Menu::filterMenuPermissions()`
    - _Requirements: 1.1, 2.6, 4.1_

  - [x] 3.4 Buat `resources/views/layout/absensi/header/_base.blade.php`
    - Salin dari `layout/demo1/header/_base.blade.php`
    - Konten identik: sudah menggunakan `theme()->getOption()` secara dinamis
    - _Requirements: 5.3, 7.2_

- [x] 4. Buat placeholder view dan daftarkan routes
  - [x] 4.1 Buat file `resources/views/pages/absensi/placeholder.blade.php`
    - Gunakan `<x-base-layout>` sebagai wrapper
    - Tampilkan judul dari `theme()->getPageTitle()` dalam elemen heading
    - Tampilkan teks "Halaman ini sedang dalam pengembangan." di bawah judul
    - Tampilkan breadcrumb menggunakan `bootstrap()->getBreadcrumb()` yang mencerminkan hierarki menu
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6_

  - [x] 4.2 Daftarkan route untuk demo `absensi` di `routes/web.php`
    - Tambahkan logika untuk mendeteksi demo `absensi` dan mendaftarkan route dari menu config secara otomatis (mengikuti pola yang sudah ada)
    - Tambahkan route eksplisit untuk `logout` yang mengarahkan ke `AuthenticatedSessionController::destroy()` (POST) atau redirect ke halaman login
    - Pastikan route `absensi/dashboard` sebagai entry point utama
    - _Requirements: 3.1, 3.20_

  - [ ]* 4.3 Tulis property test untuk Property 3: Tepat satu item aktif per path
    - **Property 3: Tepat satu item aktif per path**
    - Iterasi semua leaf path di menu config, inisialisasi `Menu` dengan path tersebut, verifikasi tepat satu `menu-link active` dalam output HTML
    - **Validates: Requirements 4.1, 4.4**

  - [ ]* 4.4 Tulis property test untuk Property 4: Parent item mendapat class `here show` ketika child aktif
    - **Property 4: Parent item mendapat class `here show` ketika child aktif**
    - Iterasi semua pasangan parent-child di menu config, verifikasi output HTML mengandung `here show` pada parent ketika child path aktif
    - **Validates: Requirements 4.2, 4.3**

  - [ ]* 4.5 Tulis property test untuk Property 5: Breadcrumb mencerminkan hierarki menu secara lengkap
    - **Property 5: Breadcrumb mencerminkan hierarki menu secara lengkap**
    - Iterasi semua item dengan `'path'`, verifikasi `getBreadcrumb()` mengembalikan array dengan semua ancestor dalam urutan root ke leaf, diawali "Home"
    - **Validates: Requirements 5.4, 5.5, 5.6**

- [x] 5. Checkpoint — Verifikasi integrasi awal
  - Pastikan semua tests pass, tanya user jika ada pertanyaan.
  - Akses `/?demo=absensi` di browser untuk memverifikasi sidebar muncul dengan struktur menu yang benar.

- [x] 6. Registrasi demo `absensi` di AppServiceProvider dan konfigurasi routing
  - [x] 6.1 Update `app/Providers/AppServiceProvider.php` untuk mendukung demo `absensi`
    - Pastikan `theme()->setDemo()` dapat menerima nilai `'absensi'` dari query param `?demo=absensi`
    - Verifikasi `bootstrap()` helper dapat me-resolve `BootstrapAbsensi` class
    - _Requirements: 6.1, 6.3_

  - [x] 6.2 Tulis unit test untuk config structure validation
    - Verifikasi semua item menu dengan `'sub'` memiliki `data-kt-menu-trigger` di `attributes` dan `menu-accordion` di `classes['item']`
    - Verifikasi semua item menu dengan `'path'` memiliki entry yang sesuai di `pages.php`
    - _Requirements: 6.1, 6.2, 6.4_

  - [ ]* 6.3 Tulis feature test untuk halaman placeholder
    - Verifikasi setiap path yang terdaftar di menu config mengembalikan HTTP 200 dengan demo `absensi`
    - Verifikasi response mengandung judul halaman yang sesuai
    - _Requirements: 3.1–3.19, 5.1, 5.2_

- [x] 7. Final checkpoint — Pastikan semua tests pass
  - Jalankan `php artisan test` dan pastikan semua test hijau.
  - Pastikan semua tests pass, tanya user jika ada pertanyaan.

## Notes

- Tasks bertanda `*` bersifat opsional dan dapat dilewati untuk implementasi MVP yang lebih cepat
- Setiap task mereferensikan requirements spesifik untuk traceability
- Property tests diimplementasikan sebagai parameterized tests yang mengiterasi seluruh item menu config (input space finite), bukan random generation — tidak memerlukan library PBT eksternal
- Smoke tests (expand/collapse accordion, responsivitas mobile, chevron rotation) dilakukan secara manual di browser karena melibatkan Metronic JS behavior
- Seluruh implementasi menggunakan pola yang sudah ada di project — tidak ada dependensi baru yang ditambahkan
