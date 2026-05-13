# Requirements Document

## Introduction

Fitur ini adalah **clickable prototype UI-only** untuk dashboard admin sistem manajemen absensi sekolah berbasis fingerprint. Prototype ini dibangun di atas project Laravel 9 yang sudah ada (Metronic theme, Blade templating) dan mencakup sidebar navigasi berlevel hierarki (3 level), halaman placeholder untuk setiap menu item, serta perilaku expand/collapse untuk menu yang memiliki sub-menu. Tidak ada integrasi backend atau logika bisnis nyata pada tahap ini — seluruh konten bersifat statis/placeholder.

---

## Glossary

- **Sidebar**: Panel navigasi vertikal di sisi kiri layar yang menampilkan struktur menu hierarki.
- **Menu_Item**: Satu entri navigasi dalam Sidebar yang dapat berupa tautan langsung atau induk dari sub-menu.
- **Level_1**: Lapisan menu paling atas (root) dalam hierarki Sidebar — 7 item utama.
- **Level_2**: Lapisan menu kedua, merupakan anak langsung dari Level_1 yang memiliki sub-menu.
- **Level_3**: Lapisan menu ketiga, merupakan anak langsung dari Level_2 yang memiliki sub-menu.
- **Accordion**: Mekanisme expand/collapse di mana hanya satu grup sub-menu yang terbuka pada satu waktu dalam satu level.
- **Active_State**: Kondisi visual yang menandai Menu_Item yang sedang aktif/dipilih.
- **Placeholder_Page**: Halaman Blade statis yang menampilkan judul menu dan pesan "Halaman dalam pengembangan" tanpa data nyata.
- **Prototype**: Implementasi UI-only tanpa koneksi ke database atau logika bisnis.
- **Admin**: Pengguna yang mengakses dashboard dengan hak akses penuh.
- **Metronic_Theme**: Template admin berbasis Bootstrap yang digunakan dalam project ini.
- **Blade**: Templating engine Laravel yang digunakan untuk merender tampilan.

---

## Requirements

### Requirement 1: Struktur Sidebar Berlevel Hierarki

**User Story:** Sebagai Admin, saya ingin melihat sidebar navigasi berlevel hierarki, sehingga saya dapat memahami struktur sistem dan berpindah antar modul dengan mudah.

#### Acceptance Criteria

1. THE Sidebar SHALL menampilkan 7 Menu_Item Level_1 secara berurutan: Dashboard, Manajemen Pengguna & Peran, Master Data, Kehadiran Siswa, Laporan, Akun Saya, dan Logout.
2. WHEN Admin mengklik Menu_Item Level_1 yang memiliki sub-menu (Manajemen Pengguna & Peran, Master Data), THE Sidebar SHALL menampilkan daftar Menu_Item Level_2 yang terkait di bawahnya.
3. WHEN Admin mengklik Menu_Item Level_2 yang memiliki sub-menu (Kelas, Perangkat Fingerprint), THE Sidebar SHALL menampilkan daftar Menu_Item Level_3 yang terkait di bawahnya.
4. THE Sidebar SHALL menampilkan 2 Menu_Item Level_2 di bawah "Manajemen Pengguna & Peran": Data Pengguna & Hak Akses, dan Log Aktivitas.
5. THE Sidebar SHALL menampilkan 9 Menu_Item Level_2 di bawah "Master Data": Siswa, Guru, Jurusan, Kelas, Tahun Ajaran, Mata Pelajaran, Jadwal Pelajaran, Aturan Jam Sekolah, dan Perangkat Fingerprint.
6. THE Sidebar SHALL menampilkan 2 Menu_Item Level_3 di bawah "Kelas": Data Kelas, dan Pembagian Kelas.
7. THE Sidebar SHALL menampilkan 2 Menu_Item Level_3 di bawah "Perangkat Fingerprint": Data Perangkat, dan Log Scan Fingerprint.

---

### Requirement 2: Perilaku Expand/Collapse Sidebar

**User Story:** Sebagai Admin, saya ingin menu yang memiliki sub-item dapat dibuka dan ditutup, sehingga sidebar tidak terasa penuh dan saya dapat fokus pada modul yang sedang digunakan.

#### Acceptance Criteria

1. WHEN Admin mengklik Menu_Item Level_1 yang memiliki sub-menu dan sub-menu tersebut sedang tertutup, THE Sidebar SHALL membuka (expand) sub-menu tersebut dan menampilkan seluruh Menu_Item Level_2 di dalamnya.
2. WHEN Admin mengklik Menu_Item Level_1 yang memiliki sub-menu dan sub-menu tersebut sedang terbuka, THE Sidebar SHALL menutup (collapse) sub-menu tersebut.
3. WHEN Admin membuka sub-menu Level_1 yang baru, THE Sidebar SHALL menutup sub-menu Level_1 lain yang sebelumnya terbuka (Accordion behavior).
4. WHEN Admin mengklik Menu_Item Level_2 yang memiliki sub-menu dan sub-menu tersebut sedang tertutup, THE Sidebar SHALL membuka sub-menu Level_3 yang terkait tanpa menutup sub-menu Level_1 induknya.
5. WHEN Admin mengklik Menu_Item Level_2 yang memiliki sub-menu dan sub-menu tersebut sedang terbuka, THE Sidebar SHALL menutup sub-menu Level_3 tersebut.
6. THE Sidebar SHALL menampilkan indikator visual (ikon panah/chevron) pada setiap Menu_Item yang memiliki sub-menu untuk membedakannya dari Menu_Item tanpa sub-menu.
7. WHEN sub-menu sedang dalam kondisi expand, THE Sidebar SHALL mengubah arah indikator visual (ikon panah/chevron) untuk mencerminkan kondisi terbuka.

---

### Requirement 3: Navigasi Antar Halaman

**User Story:** Sebagai Admin, saya ingin setiap Menu_Item dapat diklik dan membawa saya ke halaman yang sesuai, sehingga saya dapat menjelajahi seluruh area sistem.

#### Acceptance Criteria

1. WHEN Admin mengklik Menu_Item yang tidak memiliki sub-menu, THE Prototype SHALL menavigasi Admin ke Placeholder_Page yang sesuai dengan menu tersebut.
2. WHEN Admin mengklik Menu_Item yang memiliki sub-menu, THE Prototype SHALL melakukan expand/collapse sub-menu tanpa melakukan navigasi halaman.
3. WHEN Admin mengklik "Dashboard" (Level_1), THE Prototype SHALL menampilkan Placeholder_Page dengan judul "Dashboard".
4. WHEN Admin mengklik "Data Pengguna & Hak Akses" (Level_2), THE Prototype SHALL menampilkan Placeholder_Page dengan judul "Data Pengguna & Hak Akses".
5. WHEN Admin mengklik "Log Aktivitas" (Level_2), THE Prototype SHALL menampilkan Placeholder_Page dengan judul "Log Aktivitas".
6. WHEN Admin mengklik "Siswa" (Level_2), THE Prototype SHALL menampilkan Placeholder_Page dengan judul "Siswa".
7. WHEN Admin mengklik "Guru" (Level_2), THE Prototype SHALL menampilkan Placeholder_Page dengan judul "Guru".
8. WHEN Admin mengklik "Jurusan" (Level_2), THE Prototype SHALL menampilkan Placeholder_Page dengan judul "Jurusan".
9. WHEN Admin mengklik "Tahun Ajaran" (Level_2), THE Prototype SHALL menampilkan Placeholder_Page dengan judul "Tahun Ajaran".
10. WHEN Admin mengklik "Mata Pelajaran" (Level_2), THE Prototype SHALL menampilkan Placeholder_Page dengan judul "Mata Pelajaran".
11. WHEN Admin mengklik "Jadwal Pelajaran" (Level_2), THE Prototype SHALL menampilkan Placeholder_Page dengan judul "Jadwal Pelajaran".
12. WHEN Admin mengklik "Aturan Jam Sekolah" (Level_2), THE Prototype SHALL menampilkan Placeholder_Page dengan judul "Aturan Jam Sekolah".
13. WHEN Admin mengklik "Data Kelas" (Level_3), THE Prototype SHALL menampilkan Placeholder_Page dengan judul "Data Kelas".
14. WHEN Admin mengklik "Pembagian Kelas" (Level_3), THE Prototype SHALL menampilkan Placeholder_Page dengan judul "Pembagian Kelas".
15. WHEN Admin mengklik "Data Perangkat" (Level_3), THE Prototype SHALL menampilkan Placeholder_Page dengan judul "Data Perangkat".
16. WHEN Admin mengklik "Log Scan Fingerprint" (Level_3), THE Prototype SHALL menampilkan Placeholder_Page dengan judul "Log Scan Fingerprint".
17. WHEN Admin mengklik "Kehadiran Siswa" (Level_1), THE Prototype SHALL menampilkan Placeholder_Page dengan judul "Kehadiran Siswa".
18. WHEN Admin mengklik "Laporan" (Level_1), THE Prototype SHALL menampilkan Placeholder_Page dengan judul "Laporan".
19. WHEN Admin mengklik "Akun Saya" (Level_1), THE Prototype SHALL menampilkan Placeholder_Page dengan judul "Akun Saya".
20. WHEN Admin mengklik "Logout" (Level_1), THE Prototype SHALL mengarahkan Admin ke halaman login.

---

### Requirement 4: Active State pada Sidebar

**User Story:** Sebagai Admin, saya ingin menu yang sedang aktif ditandai secara visual, sehingga saya selalu tahu posisi saya dalam sistem.

#### Acceptance Criteria

1. WHEN Admin berada pada suatu Placeholder_Page, THE Sidebar SHALL menampilkan Active_State pada Menu_Item yang sesuai dengan halaman tersebut.
2. WHEN Menu_Item Level_2 atau Level_3 sedang dalam Active_State, THE Sidebar SHALL secara otomatis membuka (expand) seluruh sub-menu induk yang melingkupinya.
3. WHEN Menu_Item Level_3 sedang dalam Active_State, THE Sidebar SHALL menampilkan Active_State pada Menu_Item Level_2 induknya sebagai penanda jalur aktif (breadcrumb visual).
4. THE Sidebar SHALL menampilkan Active_State hanya pada satu Menu_Item dalam satu waktu.

---

### Requirement 5: Placeholder Page

**User Story:** Sebagai Admin, saya ingin setiap halaman menampilkan konten placeholder yang informatif, sehingga saya dapat memahami tujuan halaman tersebut dalam prototype.

#### Acceptance Criteria

1. THE Placeholder_Page SHALL menampilkan judul halaman yang sesuai dengan nama Menu_Item yang diklik.
2. THE Placeholder_Page SHALL menampilkan pesan teks "Halaman ini sedang dalam pengembangan." di bawah judul.
3. THE Placeholder_Page SHALL menggunakan layout utama (master layout) yang sama dengan seluruh halaman lain, termasuk Sidebar dan header.
4. THE Placeholder_Page SHALL menampilkan breadcrumb navigasi yang mencerminkan jalur hierarki menu dari Level_1 hingga halaman aktif.
5. WHERE halaman memiliki induk Level_2, THE Placeholder_Page SHALL menyertakan nama Level_2 dalam breadcrumb.
6. WHERE halaman memiliki induk Level_3, THE Placeholder_Page SHALL menyertakan nama Level_2 dan Level_3 dalam breadcrumb.

---

### Requirement 6: Konfigurasi Menu Berbasis Config PHP

**User Story:** Sebagai Developer, saya ingin struktur menu didefinisikan dalam file konfigurasi PHP array, sehingga mudah dimodifikasi tanpa mengubah kode Blade atau controller.

#### Acceptance Criteria

1. THE Prototype SHALL mendefinisikan seluruh struktur menu hierarki dalam satu file konfigurasi PHP array yang mengikuti pola yang sudah ada di project (`config/*/menu.php`).
2. THE Prototype SHALL menggunakan key `'sub'` dengan nested `'items'` array untuk mendefinisikan Menu_Item Level_2 dan Level_3, konsisten dengan pola Metronic_Theme yang sudah ada.
3. WHEN file konfigurasi menu diubah (item ditambah, dihapus, atau diurutkan ulang), THE Sidebar SHALL mencerminkan perubahan tersebut tanpa modifikasi pada file Blade atau controller.
4. THE Prototype SHALL mendefinisikan atribut `data-kt-menu-trigger` dan class `menu-accordion` pada setiap Menu_Item yang memiliki sub-menu, sesuai dengan konvensi Metronic_Theme.

---

### Requirement 7: Responsivitas dan Tampilan

**User Story:** Sebagai Admin, saya ingin dashboard dapat digunakan dengan nyaman di berbagai ukuran layar, sehingga saya tidak terbatas pada perangkat tertentu.

#### Acceptance Criteria

1. THE Sidebar SHALL dapat disembunyikan (collapsed) pada layar dengan lebar kurang dari 992px (breakpoint `lg` Bootstrap).
2. WHEN layar berukuran kurang dari 992px, THE Prototype SHALL menampilkan tombol toggle untuk membuka dan menutup Sidebar.
3. WHEN Sidebar dalam kondisi tersembunyi pada layar kecil dan Admin mengklik tombol toggle, THE Sidebar SHALL muncul sebagai overlay di atas konten utama.
4. THE Prototype SHALL menggunakan komponen dan class CSS dari Metronic_Theme yang sudah ada tanpa menambahkan dependensi CSS/JS baru.
