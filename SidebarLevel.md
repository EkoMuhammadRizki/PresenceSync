# Dokumentasi Kustomisasi Sidebar Leveling - PresenceSync

Dokumen ini menjelaskan langkah-langkah teknis untuk mengubah template standar Metronic menjadi sistem navigasi sidebar dengan dukungan leveling (multi-level menu) dan routing dinamis.

## 1. Struktur Folder Utama

| Fitur | Lokasi File / Folder | Deskripsi |
| :--- | :--- | :--- |
| **Layout Files** | `resources/views/layout/absensi/` | Folder layout kustom untuk prototipe. |
| **Menu Config** | `config/absensi/menu.php` | Definisi struktur menu (Judul, Icon, Path, Sub-menu). |
| **Page Mapping** | `config/absensi/pages.php` | Pemetaan path menu ke file view Blade. |
| **Bootstrap Logic** | `app/Core/Bootstraps/BootstrapAbsensi.php` | Menangani inisialisasi menu dan layout saat aplikasi dimuat. |
| **Routing** | `routes/web.php` | Registrasi rute secara otomatis berdasarkan file menu. |

---

## 2. Definisi Menu (Leveling)

Struktur menu diatur di `config/absensi/menu.php`. Anda bisa membuat menu satu tingkat atau bersarang (nested).

### Contoh Menu Single Level
```php
array(
    'title' => 'Dashboard',
    'path'  => 'absensi/dashboard',
    'icon'  => theme()->getSvgIcon("media/icons/duotune/art/art002.svg", "svg-icon-2"),
),
```

### Contoh Menu Multi-Level (Accordion)
```php
array(
    'title'      => 'Master Data',
    'classes'    => array('item' => 'menu-accordion'),
    'attributes' => array('data-kt-menu-trigger' => 'click'),
    'sub'        => array(
        'class' => 'menu-sub-accordion',
        'items' => array(
            array(
                'title'  => 'Siswa',
                'path'   => 'absensi/master/siswa',
                'bullet' => '<span class="bullet bullet-dot"></span>',
            ),
        ),
    ),
),
```

---

## 3. Pemetaan Halaman (Page Mapping)

Setiap `path` yang didefinisikan di menu harus didaftarkan di `config/absensi/pages.php` agar sistem tahu file view mana yang harus ditampilkan.

```php
'absensi' => array(
    'dashboard' => array(
        'title' => 'Dashboard',
        'view'  => 'absensi/dashboard_konten',
    ),
    'kehadiran' => array(
        'title' => 'Kehadiran Siswa',
        'view'  => 'absensi/kehadiran',
    ),
),
```

---

## 4. Logika Inisialisasi (AppServiceProvider)

Untuk menghindari "polusi menu" (menu bawaan Metronic yang tercampur), kita harus membersihkan konfigurasi global sebelum inisialisasi layout di `app/Providers/AppServiceProvider.php`.

```php
public function boot()
{
    $theme = theme();
    $demo = request()->input('demo', 'absensi');
    $theme->setDemo($demo);

    // Mencegah penggabungan menu global jika menggunakan demo absensi
    if ($demo === 'absensi') {
        config(['global.menu' => array()]);
    }

    $theme->initConfig();
    bootstrap()->run();
}
```

---

## 5. Routing Dinamis

Rute didaftarkan secara otomatis di `routes/web.php` dengan melakukan iterasi pada array menu.

```php
$menu = theme()->getMenu();
array_walk($menu, function ($val) {
    if (isset($val['path']) && $val['path'] !== 'logout') {
        Route::get($val['path'], [PagesController::class, 'index']);
    }
});
```

---

## 6. Penanganan View (PagesController)

`PagesController@index` akan mengambil path URL saat ini dan mencocokkannya dengan konfigurasi `pages.php` untuk merender view yang tepat.

```php
public function index()
{
    $view = theme()->getOption('page', 'view'); // Mengambil 'view' dari pages.php

    if (view()->exists('pages.'.$view)) {
        return view('pages.'.$view);
    }

    return redirect('/'); // Fallback jika view tidak ditemukan
}
```

---

## Tips Kustomisasi
- **Ikon**: Gunakan `theme()->getSvgIcon("path/to/svg", "class")` untuk ikon yang konsisten.
- **Accordion**: Pastikan menambahkan `'classes' => array('item' => 'menu-accordion')` pada item induk jika ingin memiliki sub-menu.
- **Breadcrumb**: Breadcrumb akan dihasilkan secara otomatis oleh `BootstrapAbsensi` berdasarkan struktur hirarki di `menu.php`.
