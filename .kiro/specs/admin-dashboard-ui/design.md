# Design Document: Admin Dashboard UI

## Overview

Fitur ini adalah **clickable prototype UI-only** untuk dashboard admin sistem manajemen absensi sekolah berbasis fingerprint. Prototype dibangun di atas project Laravel 9 yang sudah ada dengan Metronic theme (Bootstrap-based) dan Blade templating engine.

Pendekatan desain mengikuti pola yang sudah ada di project: menu didefinisikan sebagai PHP array di file konfigurasi, dirender oleh `App\Core\Menu` (via `App\Core\Adapters\Menu`), dan setiap halaman dilayani oleh `PagesController` yang membaca view berdasarkan konfigurasi `pages.php`. Tidak ada backend logic, database query, atau autentikasi nyata — seluruh konten bersifat statis/placeholder.

### Keputusan Desain Utama

1. **Menggunakan demo1 sebagai basis** — Demo1 memiliki layout aside (sidebar) yang paling sesuai dengan kebutuhan navigasi hierarki. Layout ini sudah mendukung expand/collapse accordion, active state, dan responsivitas mobile via Metronic JS.

2. **Satu file config menu baru** — Dibuat `config/absensi/menu.php` (dan `general.php`, `pages.php`) sebagai "demo" baru bernama `absensi`, mengikuti pola `config/demo*/` yang sudah ada. Ini memisahkan konfigurasi prototype dari demo bawaan.

3. **Satu Blade view placeholder yang reusable** — Semua halaman placeholder menggunakan satu view `resources/views/pages/absensi/placeholder.blade.php` yang membaca judul dari konfigurasi `pages.php`, bukan membuat file terpisah per halaman.

4. **Tidak ada controller baru** — `PagesController::index()` sudah cukup: ia membaca `theme()->getOption('page', 'view')` untuk menentukan view yang dirender. Kita hanya perlu mendaftarkan route dan konfigurasi pages.

---

## Architecture

### Alur Request

```
Browser Request (GET /absensi/dashboard)
    │
    ▼
routes/web.php
    │  (route didaftarkan otomatis dari menu config via theme()->getMenu())
    ▼
PagesController::index()
    │  (membaca theme()->getOption('page', 'view') → 'absensi/placeholder')
    ▼
resources/views/pages/absensi/placeholder.blade.php
    │  (extends x-base-layout → layout/absensi/master.blade.php)
    ▼
layout/absensi/master.blade.php
    │  (memanggil layout/aside/_base → layout/aside/_menu)
    ▼
App\Core\Adapters\Menu::build()
    │  (membaca config/absensi/menu.php, merender HTML sidebar)
    ▼
HTML Response
```

### Komponen Sistem

```
┌─────────────────────────────────────────────────────────────┐
│                    Config Layer                              │
│  config/absensi/                                            │
│  ├── general.php   (layout options, assets)                 │
│  ├── menu.php      (struktur menu hierarki 3 level)         │
│  └── pages.php     (judul & view per path)                  │
└─────────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│                   Bootstrap Layer                            │
│  app/Core/Bootstraps/BootstrapAbsensi.php                   │
│  (extends BootstrapBase, inisialisasi aside menu)           │
└─────────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│                    View Layer                                │
│  resources/views/layout/absensi/                            │
│  ├── master.blade.php                                       │
│  ├── aside/_base.blade.php                                  │
│  ├── aside/_menu.blade.php                                  │
│  ├── header/_base.blade.php                                 │
│  ├── _content.blade.php                                     │
│  └── _footer.blade.php                                      │
│                                                             │
│  resources/views/pages/absensi/                             │
│  └── placeholder.blade.php  (satu view untuk semua halaman) │
└─────────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────┐
│                   Routing Layer                              │
│  routes/web.php                                             │
│  (route didaftarkan otomatis dari menu config)              │
│  + route manual untuk logout → redirect login               │
└─────────────────────────────────────────────────────────────┘
```

---

## Components and Interfaces

### 1. Menu Config (`config/absensi/menu.php`)

File ini mendefinisikan seluruh hierarki menu sebagai PHP array. Mengikuti format yang sudah ada di `config/global/menu.php`.

**Format item tanpa sub-menu:**
```php
array(
    'title' => 'Dashboard',
    'path'  => 'absensi/dashboard',
    'icon'  => theme()->getSvgIcon("demo1/media/icons/duotune/art/art002.svg", "svg-icon-2"),
)
```

**Format item dengan sub-menu (Level_1 → Level_2):**
```php
array(
    'title'      => 'Master Data',
    'icon'       => theme()->getSvgIcon("demo1/media/icons/duotune/general/gen025.svg", "svg-icon-2"),
    'classes'    => array('item' => 'menu-accordion'),
    'attributes' => array('data-kt-menu-trigger' => 'click'),
    'sub'        => array(
        'class' => 'menu-sub-accordion menu-active-bg',
        'items' => array(
            // Level_2 items...
        ),
    ),
)
```

**Format item Level_2 dengan sub-menu (Level_2 → Level_3):**
```php
array(
    'title'      => 'Kelas',
    'classes'    => array('item' => 'menu-accordion'),
    'attributes' => array('data-kt-menu-trigger' => 'click'),
    'bullet'     => '<span class="bullet bullet-dot"></span>',
    'sub'        => array(
        'class' => 'menu-sub-accordion',
        'items' => array(
            // Level_3 items...
        ),
    ),
)
```

**Struktur menu lengkap:**

| Level | Item | Path | Tipe |
|-------|------|------|------|
| L1 | Dashboard | `absensi/dashboard` | Link |
| L1 | Manajemen Pengguna & Peran | — | Parent |
| L2 | ↳ Data Pengguna & Hak Akses | `absensi/pengguna/data` | Link |
| L2 | ↳ Log Aktivitas | `absensi/pengguna/log-aktivitas` | Link |
| L1 | Master Data | — | Parent |
| L2 | ↳ Siswa | `absensi/master/siswa` | Link |
| L2 | ↳ Guru | `absensi/master/guru` | Link |
| L2 | ↳ Jurusan | `absensi/master/jurusan` | Link |
| L2 | ↳ Kelas | — | Parent |
| L3 | ↳↳ Data Kelas | `absensi/master/kelas/data` | Link |
| L3 | ↳↳ Pembagian Kelas | `absensi/master/kelas/pembagian` | Link |
| L2 | ↳ Tahun Ajaran | `absensi/master/tahun-ajaran` | Link |
| L2 | ↳ Mata Pelajaran | `absensi/master/mata-pelajaran` | Link |
| L2 | ↳ Jadwal Pelajaran | `absensi/master/jadwal-pelajaran` | Link |
| L2 | ↳ Aturan Jam Sekolah | `absensi/master/aturan-jam` | Link |
| L2 | ↳ Perangkat Fingerprint | — | Parent |
| L3 | ↳↳ Data Perangkat | `absensi/master/fingerprint/data` | Link |
| L3 | ↳↳ Log Scan Fingerprint | `absensi/master/fingerprint/log` | Link |
| L1 | Kehadiran Siswa | `absensi/kehadiran` | Link |
| L1 | Laporan | `absensi/laporan` | Link |
| L1 | Akun Saya | `absensi/akun` | Link |
| L1 | Logout | `logout` | Link (redirect) |

### 2. General Config (`config/absensi/general.php`)

Mengikuti pola `config/demo1/general.php`. Konfigurasi utama yang perlu disesuaikan:
- `layout/aside/display`: `true`
- `layout/aside/theme`: `'dark'`
- `layout/aside/menu`: `'main'`
- `layout/aside/minimize`: `true`
- `layout/aside/hoverable`: `true`
- `layout/toolbar/display`: `false` (disederhanakan untuk prototype)

### 3. Pages Config (`config/absensi/pages.php`)

Mendefinisikan judul dan view untuk setiap path. Semua halaman placeholder menggunakan view yang sama:

```php
return array(
    'absensi' => array(
        'dashboard' => array(
            'title' => 'Dashboard',
            'view'  => 'absensi/placeholder',
        ),
        'pengguna' => array(
            'data' => array(
                'title' => 'Data Pengguna & Hak Akses',
                'view'  => 'absensi/placeholder',
            ),
            // ...
        ),
        // ...
    ),
);
```

### 4. Bootstrap Class (`app/Core/Bootstraps/BootstrapAbsensi.php`)

Extends `BootstrapBase` mengikuti pola `BootstrapDemo1`. Bertanggung jawab menginisialisasi aside menu dari config `absensi`.

```php
class BootstrapAbsensi extends BootstrapBase {
    private static $asideMenu;

    private static function initAsideMenu() {
        self::$asideMenu = new Menu(
            Theme::getOption('menu', 'main'),
            Theme::getPagePath()
        );
        self::$asideMenu->setIconType(Theme::getOption('layout', 'aside/menu-icon'));
    }

    public static function initLayout() { /* ... */ }
    public static function getAsideMenu() { return self::$asideMenu; }
    public static function getBreadcrumb() { /* ... */ }
}
```

### 5. Layout Views (`resources/views/layout/absensi/`)

Disalin dan disesuaikan dari `layout/demo1/`. Perubahan utama:
- `aside/_menu.blade.php`: memanggil `bootstrap()->getAsideMenu()` (sudah sama)
- `master.blade.php`: struktur identik dengan demo1

### 6. Placeholder View (`resources/views/pages/absensi/placeholder.blade.php`)

Satu view yang digunakan oleh semua halaman placeholder:

```blade
<x-base-layout>
    <div class="card">
        <div class="card-body">
            <h1 class="card-title">{{ theme()->getPageTitle() }}</h1>
            <p class="text-muted">Halaman ini sedang dalam pengembangan.</p>
        </div>
    </div>
</x-base-layout>
```

### 7. Route Registration

Route didaftarkan di `routes/web.php` mengikuti pola yang sudah ada — `theme()->getMenu()` mengiterasi semua item dengan `path` dan mendaftarkan route GET. Untuk demo `absensi`, route logout perlu ditangani secara khusus (redirect ke halaman login).

---

## Data Models

Prototype ini tidak menggunakan database atau model Eloquent. Satu-satunya "data" yang digunakan adalah:

### Menu Item Structure (PHP Array)

```php
[
    'title'      => string,           // Teks label menu
    'path'       => string,           // URL path (tanpa leading slash)
    'icon'       => string|array,     // HTML SVG icon (opsional)
    'bullet'     => string,           // HTML bullet (untuk Level_2/3 tanpa icon)
    'classes'    => [
        'item'   => string,           // CSS class pada <div class="menu-item">
        'link'   => string,           // CSS class pada <a class="menu-link">
    ],
    'attributes' => [
        'data-kt-menu-trigger' => 'click',  // Untuk item dengan sub-menu
    ],
    'sub'        => [
        'class'  => string,           // CSS class pada <div class="menu-sub">
        'items'  => array,            // Array of child menu items
    ],
    'hide'       => bool|callable,    // Sembunyikan item (opsional)
]
```

### Page Config Structure (PHP Array)

```php
[
    'title' => string,   // Judul halaman (ditampilkan di toolbar & breadcrumb)
    'view'  => string,   // Nama view Blade (relatif ke resources/views/pages/)
]
```

---

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system — essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

Berdasarkan prework analysis, fitur ini adalah **UI prototype berbasis konfigurasi**. Sebagian besar perilaku (expand/collapse, responsivitas) dihandle oleh Metronic JS framework dan tidak memiliki logika PHP yang bisa ditest sebagai property. Namun, ada beberapa property yang bermakna pada lapisan PHP:

**Property Reflection:**
- Property 4.1 (active state per path) dan Property 4.4 (hanya satu active state) dapat digabung: "untuk setiap path, tepat satu item memiliki class active" sudah mencakup keduanya.
- Property 3.1 (route exists untuk setiap path) dan Property 6.3 (config change tercermin di output) dapat digabung: keduanya menguji bahwa menu config adalah sumber kebenaran tunggal untuk routing.
- Property 4.2 (parent auto-expand) dan Property 5.4 (breadcrumb mencerminkan hierarki) adalah dua property yang berbeda dan tidak redundan.

Setelah reflection, diperoleh 5 property yang unik:

---

### Property 1: Item dengan sub-menu merender chevron arrow

*For any* item menu yang memiliki key `'sub'` dalam konfigurasi, output HTML dari `Menu::build()` harus mengandung elemen `<span class="menu-arrow">` di dalam link item tersebut.

**Validates: Requirements 2.6**

---

### Property 2: Setiap path di menu config memiliki route yang valid

*For any* item menu dengan key `'path'` yang non-empty dan bukan `'#'`, URL yang dihasilkan oleh `Theme::getPageUrl($path)` harus dapat di-resolve sebagai route yang terdaftar di aplikasi.

**Validates: Requirements 3.1, 6.3**

---

### Property 3: Tepat satu item aktif per path

*For any* path halaman yang valid, inisialisasi `Menu` dengan path tersebut dan memanggil `build()` harus menghasilkan HTML di mana tepat satu elemen `menu-link` memiliki class `active`.

**Validates: Requirements 4.1, 4.4**

---

### Property 4: Parent item mendapat class `here show` ketika child aktif

*For any* item menu yang memiliki `'sub'`, jika salah satu descendant-nya (di level manapun) memiliki `'path'` yang cocok dengan path aktif, maka output HTML dari item parent tersebut harus mengandung class `here show` pada elemen `menu-item`-nya.

**Validates: Requirements 4.2, 4.3**

---

### Property 5: Breadcrumb mencerminkan hierarki menu secara lengkap

*For any* item menu dengan `'path'`, memanggil `Menu::getBreadcrumb()` dengan path tersebut harus mengembalikan array yang mengandung semua ancestor item dalam urutan dari root ke leaf, diawali dengan item "Home".

**Validates: Requirements 5.4, 5.5, 5.6**

---

## Error Handling

Karena ini adalah prototype UI-only, error handling difokuskan pada ketahanan rendering:

### Config Tidak Lengkap
- Jika `config/absensi/pages.php` tidak mendefinisikan entry untuk path tertentu, `theme()->getPageTitle()` mengembalikan `null`. View placeholder harus menangani ini dengan graceful fallback (tampilkan string kosong atau judul default).
- `PagesController::index()` sudah memiliki fallback: jika view tidak ditemukan, redirect ke `/`.

### Path Tidak Terdaftar
- Jika Admin mengakses URL yang tidak ada di menu config, Laravel akan mengembalikan 404. Ini adalah perilaku yang diharapkan untuk prototype.

### Item Menu dengan Path `#`
- Item dengan `path => '#'` (placeholder link) dirender sebagai `<a href="#">`. Ini tidak menyebabkan error, hanya tidak melakukan navigasi.

### Logout
- Route `logout` harus diarahkan ke `AuthenticatedSessionController::destroy()` yang sudah ada, bukan ke `PagesController`. Ini perlu didaftarkan secara eksplisit di `routes/web.php`.

---

## Testing Strategy

Fitur ini adalah UI prototype berbasis konfigurasi PHP. Strategi testing menggunakan pendekatan dual:

### Unit Tests (PHPUnit)

Fokus pada lapisan PHP yang memiliki logika: `App\Core\Menu` class dan konfigurasi menu.

**Test cases:**
1. **Menu rendering — item tanpa sub-menu** (Example): Verifikasi item tanpa `'sub'` dirender sebagai `<a>` tag dengan href yang benar.
2. **Menu rendering — item dengan sub-menu** (Example): Verifikasi item dengan `'sub'` dirender sebagai `<span>` (bukan `<a>`) dan mengandung `menu-sub`.
3. **Config structure validation** (Example): Verifikasi semua item dengan `'sub'` memiliki `data-kt-menu-trigger` di `attributes` dan `menu-accordion` di `classes['item']`.
4. **Breadcrumb untuk Level_3** (Example): Verifikasi `getBreadcrumb()` untuk path Level_3 mengembalikan array dengan 4 elemen (Home + L1 + L2 + L3).

### Property-Based Tests

Menggunakan library **[eris/eris](https://github.com/giorgiosironi/eris)** (PHP property-based testing library) atau implementasi sederhana dengan loop generator, karena input space-nya terbatas (array config yang sudah diketahui).

> **Catatan**: Karena input space adalah array config PHP yang finite dan sudah diketahui, property tests di sini diimplementasikan sebagai parameterized tests yang mengiterasi seluruh item menu — bukan random generation. Ini tetap memverifikasi property universal "untuk setiap item" tanpa memerlukan library PBT eksternal.

**Property Test 1 — Chevron arrow pada item dengan sub-menu:**
```php
// Feature: admin-dashboard-ui, Property 1: Item dengan sub-menu merender chevron arrow
foreach (getAllMenuItemsWithSub($menuConfig) as $item) {
    $html = buildMenuHtml($item);
    assertStringContainsString('menu-arrow', $html);
}
```

**Property Test 2 — Route valid untuk setiap path:**
```php
// Feature: admin-dashboard-ui, Property 2: Setiap path di menu config memiliki route valid
foreach (getAllMenuItemsWithPath($menuConfig) as $item) {
    $url = Theme::getPageUrl($item['path']);
    assertNotEmpty($url);
    // Verifikasi route terdaftar
    assertRouteExists($item['path']);
}
```

**Property Test 3 — Tepat satu active state per path:**
```php
// Feature: admin-dashboard-ui, Property 3: Tepat satu item aktif per path
foreach (getAllLeafPaths($menuConfig) as $path) {
    $menu = new Menu($menuConfig, $path);
    $html = $menu->build();
    $activeCount = substr_count($html, 'menu-link active');
    assertEquals(1, $activeCount);
}
```

**Property Test 4 — Parent mendapat `here show` ketika child aktif:**
```php
// Feature: admin-dashboard-ui, Property 4: Parent item mendapat class here show ketika child aktif
foreach (getAllParentChildPairs($menuConfig) as [$parent, $childPath]) {
    $menu = new Menu($menuConfig, $childPath);
    $html = $menu->build();
    assertStringContainsString('here show', $html);
}
```

**Property Test 5 — Breadcrumb mencerminkan hierarki:**
```php
// Feature: admin-dashboard-ui, Property 5: Breadcrumb mencerminkan hierarki menu
foreach (getAllMenuItemsWithPath($menuConfig) as ['path' => $path, 'depth' => $depth]) {
    $menu = new Menu($menuConfig, $path);
    $breadcrumb = $menu->getBreadcrumb();
    // Home + semua ancestor + item aktif
    assertCount($depth + 1, $breadcrumb); // +1 untuk Home
    assertEquals($path, last($breadcrumb)['path']);
}
```

### Smoke Tests (Manual / Browser)

Dilakukan secara manual karena melibatkan Metronic JS behavior:
1. Expand/collapse accordion bekerja di browser
2. Sidebar collapse pada layar < 992px
3. Toggle button muncul di mobile
4. Chevron berputar saat expand
5. Dark mode sidebar tampil dengan benar
