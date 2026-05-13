# DESIGN.md — Metronic Design System Guide
> **Panduan referensi desain untuk project PresenceSync.**
> Semua nilai diambil langsung dari source SCSS Metronic (`resources/assets/core/sass/components/_variables.scss`).
> Gunakan file ini sebagai acuan agar tampilan konsisten di seluruh halaman.

---

## Daftar Isi
1. [Filosofi Desain](#1-filosofi-desain)
2. [Palet Warna](#2-palet-warna)
3. [Tipografi](#3-tipografi)
4. [Spacing & Sizing](#4-spacing--sizing)
5. [Border & Radius](#5-border--radius)
6. [Shadow (Box Shadow)](#6-shadow-box-shadow)
7. [Breakpoints & Grid](#7-breakpoints--grid)
8. [Komponen UI](#8-komponen-ui)
9. [Layout (Aside / Header / Toolbar)](#9-layout-aside--header--toolbar)
10. [Dark Mode](#10-dark-mode)
11. [Panduan Penggunaan Kelas Bootstrap](#11-panduan-penggunaan-kelas-bootstrap)
12. [Do's & Don'ts](#12-dos--donts)

---

## 1. Filosofi Desain

Metronic mengikuti pendekatan **Bootstrap 5 yang dikustomisasi** dengan tambahan variabel dan komponen custom. Prinsip utama:

- **Konsistensi**: Selalu gunakan variabel/kelas yang sudah ada, jangan hardcode warna atau ukuran baru.
- **Semantik**: Gunakan nama warna semantik (`primary`, `success`, `danger`) bukan warna mentah (`blue`, `red`).
- **Responsif**: Desain menggunakan breakpoint Mobile-first, dengan penyesuaian untuk `sm`, `md`, `lg`, `xl`, `xxl`.
- **Dark Mode Ready**: Semua komponen mendukung dark mode secara otomatis via `isDarkMode()` SASS function.

---

## 2. Palet Warna

### 2.1 Warna Semantik (Theme Colors)

Ini adalah warna utama yang harus digunakan untuk semua elemen UI.

| Token | Hex (Light Mode) | Hex Active | Hex Light (bg) | Deskripsi |
|---|---|---|---|---|
| `primary` | `#3699FF` | `#187DE4` | `#F1FAFF` | Aksi utama, link, tombol CTA |
| `success` | `#1BC5BD` | `#0BB7AF` | `#C9F7F5` | Status berhasil, konfirmasi |
| `info` | `#8950FC` | `#7337EE` | `#EEE5FF` | Informasi, highlight |
| `warning` | `#FFA800` | `#EE9D01` | `#FFF4DE` | Peringatan, perhatian |
| `danger` | `#F64E60` | `#EE2D41` | `#FFE2E5` | Error, destruktif, hapus |
| `dark` | `#181C32` | `#131628` | `#EFF2F5` | Teks utama, elemen gelap |
| `secondary` | `#E4E6EF` | `#B5B5C3` | `#F5F8FA` | Netral, tidak aktif |
| `light` | `#F5F8FA` | `#EFF2F5` | — | Background ringan |

**Penggunaan di HTML:**
```html
<!-- Background -->
<div class="bg-primary">...</div>
<div class="bg-light-success">...</div>  <!-- versi muted/light -->

<!-- Text -->
<span class="text-primary">...</span>
<span class="text-muted">...</span>

<!-- Button -->
<button class="btn btn-primary">Simpan</button>
<button class="btn btn-light-danger">Hapus</button>
```

---

### 2.2 Skala Abu-abu (Gray Scale)

Digunakan untuk teks, border, dan background netral.

| Token | Light Mode | Dark Mode | Kegunaan Umum |
|---|---|---|---|
| `gray-100` | `#F5F8FA` | `#1b1b29` | Background halaman / card ringan |
| `gray-200` | `#EFF2F5` | `#2B2B40` | Border, input background |
| `gray-300` | `#E4E6EF` | `#323248` | Border dashed, separator |
| `gray-400` | `#B5B5C3` | `#474761` | Placeholder, ikon non-aktif |
| `gray-500` | `#A1A5B7` | `#565674` | Teks muted |
| `gray-600` | `#7E8299` | `#6D6D80` | Teks sekunder |
| `gray-700` | `#5E6278` | `#92929F` | Teks body |
| `gray-800` | `#3F4254` | `#CDCDDE` | Label form, heading kecil |
| `gray-900` | `#181C32` | `#FFFFFF` | Heading utama, teks bold |

---

### 2.3 Warna Sosial Media

| Platform | Warna |
|---|---|
| Facebook | `#3b5998` |
| Google | `#dd4b39` |
| Twitter | `#1da1f2` |
| Instagram | `#e1306c` |
| YouTube | `#ff0000` |
| LinkedIn | `#0077b5` |

---

## 3. Tipografi

### 3.1 Font Family
```
Font Utama: Poppins, Helvetica, sans-serif
```
> Poppins digunakan untuk seluruh teks, heading, dan UI. **Jangan ganti ke font lain** untuk menjaga konsistensi.

### 3.2 Ukuran Font

| Nama | Nilai (relatif) | Pixel (basis 13px) |
|---|---|---|
| `fs-base` | `1rem` | 13px |
| `fs-sm` | `0.925rem` | 12px |
| `fs-lg` | `1.075rem` | 14px |
| `fs-1` / `h1` | `1.75rem` | ~22.75px |
| `fs-2` / `h2` | `1.5rem` | ~19.5px |
| `fs-3` / `h3` | `1.35rem` | ~17.55px |
| `fs-4` / `h4` | `1.25rem` | ~16.25px |
| `fs-5` / `h5` | `1.15rem` | ~14.95px |
| `fs-6` / `h6` | `1.075rem` | ~13.97px |
| `fs-7` | `0.95rem` | ~12.35px |
| `fs-8` | `0.85rem` | ~11.05px |

> **Root font size:** `13px` di desktop, `12px` di mobile.

### 3.3 Font Weight

| Kelas | Nilai | Kegunaan |
|---|---|---|
| `fw-lighter` | lighter | Jarang dipakai |
| `fw-light` | 300 | Subtitle, caption ringan |
| `fw-normal` | 400 | Body teks standar |
| `fw-bold` | 500 | Label, teks penting |
| `fw-bolder` | 600 | Heading, judul section |
| `fw-boldest` | 700 | Judul utama, aksen kuat |

### 3.4 Line Height

| Variable | Nilai |
|---|---|
| Default | `1.5` |
| Small | `1.25` |
| Large | `1.75` |

---

## 4. Spacing & Sizing

### 4.1 Spacer System

Basis spacer: **`1rem` = 14px** (karena root font 13px → 1rem ≈ 13px, tapi spacer base = 14px)

| Kelas | Nilai | Pixel |
|---|---|---|
| `p-1`, `m-1` | `0.25rem` | ~3.5px |
| `p-2`, `m-2` | `0.5rem` | ~7px |
| `p-3`, `m-3` | `0.75rem` | ~10.5px |
| `p-4`, `m-4` | `1rem` | ~14px |
| `p-5`, `m-5` | `1.25rem` | ~17.5px |
| `p-6`, `m-6` | `1.5rem` | ~21px |
| `p-7`, `m-7` | `1.75rem` | ~24.5px |
| `p-8`, `m-8` | `2rem` | ~28px |
| `p-10`, `m-10` | `2.5rem` | ~35px |
| `p-12`, `m-12` | `3rem` | ~42px |
| `p-15`, `m-15` | `3.75rem` | ~52.5px |
| `p-20`, `m-20` | `5rem` | ~70px |

### 4.2 Ukuran Komponen (Custom Sizes)

Metronic menyediakan kelas `w-{size}` dan `h-{size}` untuk ukuran fixed:
```
10px, 15px, 20px, 25px, 30px, 35px, 40px, 45px, 50px, 55px, 60px, 65px, 70px, 75px, 80px, 85px, 90px, 95px, 100px, 125px, 150px, 175px, 200px, 250px, 300px, 350px, 400px, 450px, 500px, 550px, 600px, 700px, 800px, 900px, 1000px
```

Contoh:
```html
<div class="w-100px h-50px">...</div>
<img class="w-40px h-40px rounded-circle">
```

### 4.3 Symbol Sizes (Avatar / Icon Wrapper)

| Kelas | Ukuran |
|---|---|
| `symbol symbol-20px` | 20×20px |
| `symbol symbol-35px` | 35×35px |
| `symbol symbol-40px` | 40×40px |
| `symbol` (default) | 50×50px |
| `symbol symbol-75px` | 75×75px |
| `symbol symbol-100px` | 100×100px |

---

## 5. Border & Radius

### 5.1 Border Radius

| Variable | Nilai | Kegunaan |
|---|---|---|
| `rounded-sm` | `0.275rem` | Radius kecil |
| `rounded` (default) | `0.475rem` | Komponen umum (card, button, input) |
| `rounded-lg` | `0.775rem` | Modal, dropdown besar |
| `rounded-xl` | `1rem` | Kartu hero, banner |
| `rounded-pill` | `50rem` | Badge pill, tombol pill |
| `rounded-circle` | `50%` | Avatar, ikon bulat |

### 5.2 Border Color

| Penggunaan | Color Token |
|---|---|
| Border standar | `gray-200` → `#EFF2F5` |
| Border dashed | `gray-300` → `#E4E6EF` |
| Input border | `gray-300` |
| Input focus | `gray-400` |

Kelas utility:
```html
<div class="border border-gray-300">...</div>
<div class="border border-dashed border-primary">...</div>
```

---

## 6. Shadow (Box Shadow)

| Nama | Nilai | Kegunaan |
|---|---|---|
| `shadow-xs` | `0 .1rem 0.75rem 0.25rem rgba(0,0,0,.05)` | Hover card ringan |
| `shadow-sm` | `0 .1rem 1rem 0.25rem rgba(0,0,0,.05)` | Card default |
| `shadow` | `0 .5rem 1.5rem 0.5rem rgba(0,0,0,.075)` | Dropdown, modal |
| `shadow-lg` | `0 1rem 2rem 1rem rgba(0,0,0,.1)` | Elemen floating |

Kelas utility:
```html
<div class="shadow-sm">...</div>
<div class="shadow">...</div>
```

---

## 7. Breakpoints & Grid

### 7.1 Breakpoints

| Nama | Min-width | Deskripsi |
|---|---|---|
| `xs` | 0px | Mobile kecil |
| `sm` | 576px | Mobile landscape |
| `md` | 768px | Tablet |
| `lg` | 992px | Laptop |
| `xl` | 1200px | Desktop |
| `xxl` | 1400px | Large desktop |

### 7.2 Grid

- Kolom: **12 kolom**
- Gutter default: `1.5rem` (24px)
- Container max-width: `1320px` di `xxl`

```html
<div class="row g-5">
  <div class="col-md-6 col-xl-4">...</div>
  <div class="col-md-6 col-xl-8">...</div>
</div>
```

---

## 8. Komponen UI

### 8.1 Card

```html
<!-- Card standar -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Judul</h3>
    <div class="card-toolbar">...</div>
  </div>
  <div class="card-body">
    ...konten...
  </div>
</div>

<!-- Card dengan border dashed -->
<div class="card border border-dashed border-gray-300">...</div>
```

**Spesifikasi Card:**
- Padding vertikal: `2rem`
- Padding horizontal: `2.25rem`
- Header height: `70px`
- Background: `body-bg` (putih di light, `#1E1E2D` di dark)
- Shadow: `0px 0px 20px 0px rgba(76,87,125,0.02)` (light mode)

---

### 8.2 Button

**Varian utama:**
```html
<button class="btn btn-primary">Primary</button>
<button class="btn btn-success">Success</button>
<button class="btn btn-danger">Danger</button>
<button class="btn btn-warning">Warning</button>
<button class="btn btn-info">Info</button>
<button class="btn btn-dark">Dark</button>
<button class="btn btn-secondary">Secondary</button>
<button class="btn btn-light">Light</button>
```

**Varian Light (background muted):**
```html
<button class="btn btn-light-primary">Primary Light</button>
<button class="btn btn-light-success">Success Light</button>
<button class="btn btn-light-danger">Danger Light</button>
```

**Ukuran:**
```html
<button class="btn btn-primary btn-sm">Small</button>
<button class="btn btn-primary">Default</button>
<button class="btn btn-primary btn-lg">Large</button>
```

**Spesifikasi Button:**
- Font weight: `500` (bold)
- Padding default: `0.75rem 1.5rem`
- Border radius: `0.475rem`
- Transition: `0.15s ease-in-out`

---

### 8.3 Badge

```html
<!-- Badge standar -->
<span class="badge badge-primary">New</span>
<span class="badge badge-success">Active</span>
<span class="badge badge-danger">Error</span>

<!-- Badge light -->
<span class="badge badge-light-warning">Pending</span>

<!-- Badge pill -->
<span class="badge badge-primary rounded-pill">99+</span>
```

**Spesifikasi Badge:**
- Font size: `0.85rem`
- Font weight: `600`
- Padding: `0.5em 0.85em`
- Border radius: `0.475rem`

---

### 8.4 Form & Input

```html
<!-- Input standar -->
<div class="mb-10">
  <label class="form-label required">Email</label>
  <input type="email" class="form-control" placeholder="Masukkan email">
  <div class="form-text text-muted">Teks bantuan</div>
</div>

<!-- Input solid (background abu) -->
<input type="text" class="form-control form-control-solid" placeholder="...">

<!-- Input ukuran -->
<input class="form-control form-control-sm">
<input class="form-control">
<input class="form-control form-control-lg">
```

**Spesifikasi Input:**
- Padding: `0.75rem 1rem`
- Font weight: `500`
- Border color: `gray-300` → focus: `gray-400`
- Solid bg: `gray-100` (`#F5F8FA`)
- Placeholder color: `gray-500`
- Border radius: `0.475rem`

---

### 8.5 Table

```html
<table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
  <thead>
    <tr class="fw-bolder text-muted">
      <th>Nama</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>...</td>
      <td>...</td>
    </tr>
  </tbody>
</table>
```

**Kelas tabel umum:**
- `table-row-dashed` — border row dashed
- `table-row-gray-300` — warna border row
- `gs-0 gy-4` — gutter horizontal 0, vertikal spacing 4
- `align-middle` — vertikal center semua cell

---

### 8.6 Modal

```html
<div class="modal fade" id="myModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bolder">Judul Modal</h5>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
          <span class="svg-icon svg-icon-2x">...</span>
        </div>
      </div>
      <div class="modal-body">
        ...konten...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary">Simpan</button>
      </div>
    </div>
  </div>
</div>
```

**Ukuran Modal:**
- `modal-sm` → 300px
- `modal-md` (default) → 500px
- `modal-lg` → 800px
- `modal-xl` → 1140px

---

### 8.7 Symbol (Avatar)

```html
<!-- Avatar dengan inisial -->
<div class="symbol symbol-50px">
  <span class="symbol-label bg-light-primary text-primary fw-bolder fs-6">AB</span>
</div>

<!-- Avatar dengan gambar -->
<div class="symbol symbol-45px">
  <img src="avatar.jpg" alt="User">
</div>

<!-- Avatar dengan status badge -->
<div class="symbol symbol-45px">
  <img src="avatar.jpg">
  <div class="symbol-badge bg-success start-100 top-100 border-4 h-15px w-15px ms-n2 mt-n2"></div>
</div>
```

---

### 8.8 Alert / Notice

```html
<!-- Alert standar -->
<div class="alert alert-primary d-flex align-items-center p-5 mb-10">
  <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">...</span>
  <div class="d-flex flex-column">
    <h4 class="mb-1 text-dark">Info Penting</h4>
    <span>Detail pesan di sini.</span>
  </div>
</div>

<!-- Notice/highlight box -->
<div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
  ...
</div>
```

---

### 8.9 Separator

```html
<!-- Garis pembagi -->
<div class="separator separator-dashed my-5"></div>
<div class="separator border-gray-200 my-4"></div>
<div class="separator separator-content my-5">
  <span class="w-125px text-gray-500 fw-semibold fs-7">atau</span>
</div>
```

---

### 8.10 Stepper

```html
<div class="stepper stepper-pills" id="kt_stepper_example">
  <div class="stepper-nav">
    <div class="stepper-item current" data-kt-stepper-element="nav">
      <div class="stepper-icon w-40px h-40px">
        <i class="stepper-check fas fa-check"></i>
        <span class="stepper-number">1</span>
      </div>
      <div class="stepper-label">
        <h3 class="stepper-title">Step Pertama</h3>
        <div class="stepper-desc fw-semibold">Deskripsi singkat</div>
      </div>
    </div>
  </div>
</div>
```

---

## 9. Layout (Aside / Header / Toolbar)

### 9.1 Header

| Property | Desktop | Mobile |
|---|---|---|
| Height | `65px` | `55px` |
| Background | White (light) / `#1E1E2D` (dark) |
| Box Shadow | `0px 10px 30px 0px rgba(82,63,105,0.05)` |

### 9.2 Toolbar (Sub-header)

| Property | Nilai |
|---|---|
| Height desktop | `75px` |
| Height mobile | `60px` |
| Background | White (light) / `darken(#1e1e2d, 2%)` (dark) |
| Border top | `1px solid border-color` (light only) |

### 9.3 Aside / Sidebar

| Property | Nilai |
|---|---|
| Width | `265px` |
| Minimized width | `75px` |
| Background | `#1e1e2d` (selalu gelap, baik light maupun dark mode) |
| Logo bg | `darken(#1e1e2d, 2%)` |
| Padding X | `25px` |

> **Catatan penting**: Aside/sidebar Metronic selalu menggunakan tema gelap (`#1e1e2d`), bahkan di light mode. Ini adalah design decision dari Metronic.

### 9.4 Content Area

| Property | Nilai |
|---|---|
| Padding desktop | `30px` |
| Padding mobile | `15px` |
| Content border radius | `1.5rem` |
| Page background | `gray-100` → `#F5F8FA` |

---

## 10. Dark Mode

Metronic mendukung dark mode penuh. Nilai-nilai yang berubah di dark mode:

| Elemen | Light Mode | Dark Mode |
|---|---|---|
| Body background | `#FFFFFF` | `#1E1E2D` |
| Page background | `#F5F8FA` | `#151521` |
| Card background | `#FFFFFF` | `#1E1E2D` |
| Header background | `#FFFFFF` | `#1E1E2D` |
| `gray-100` | `#F5F8FA` | `#1b1b29` |
| `gray-900` (teks) | `#181C32` | `#FFFFFF` |
| Border | `#EFF2F5` | `#2B2B40` |

Untuk mengaktifkan dark mode, tambahkan atribut `data-theme="dark"` pada tag `<html>`:
```html
<html lang="id" data-theme="dark">
```

---

## 11. Panduan Penggunaan Kelas Bootstrap

### Text & Color Utilities

```html
<!-- Warna teks -->
<span class="text-primary">...</span>
<span class="text-success">...</span>
<span class="text-danger">...</span>
<span class="text-muted">...</span>      <!-- gray-500 -->
<span class="text-gray-700">...</span>   <!-- spesifik gray scale -->

<!-- Background -->
<div class="bg-primary">...</div>
<div class="bg-light-primary">...</div>  <!-- versi muted -->
<div class="bg-gray-100">...</div>
```

### Opacity

```html
<div class="opacity-25">25% opacity</div>
<div class="opacity-50">50% opacity</div>
<div class="opacity-75">75% opacity</div>
```

### Z-Index

```html
<div class="z-index-1">z-index: 1</div>
<div class="z-index-2">z-index: 2</div>
<div class="z-index-3">z-index: 3</div>
<div class="z-index-n1">z-index: -1</div>
```

### Transition

- Seluruh elemen interaktif menggunakan `transition: all .2s ease` secara default.
- Jangan override transition ke nilai yang lebih lambat dari `0.3s` agar terasa responsif.

---

## 12. Do's & Don'ts

### ✅ DO (Lakukan)

- Gunakan kelas utility Metronic/Bootstrap (`text-primary`, `bg-light-danger`, dll)
- Gunakan skala spacing yang ada (`p-5`, `m-3`, `gap-4`, dll)
- Gunakan font Poppins dengan weight yang sudah terdefinisi
- Ikuti pola card-header, card-body, card-footer
- Gunakan `symbol` untuk avatar/inisial user
- Gunakan `badge` untuk status label
- Gunakan `separator` untuk pemisah konten
- Gunakan color token semantik, bukan warna hardcode

### ❌ DON'T (Jangan)

- ❌ Jangan hardcode warna: `color: #3699FF` → gunakan `class="text-primary"`
- ❌ Jangan gunakan font selain Poppins
- ❌ Jangan buat border radius sendiri yang berbeda dari sistem (misal `border-radius: 20px`)
- ❌ Jangan override spacing dengan nilai sembarangan
- ❌ Jangan gunakan `!important` kecuali benar-benar terpaksa
- ❌ Jangan membuat komponen baru yang sudah ada padanannya di Metronic
- ❌ Jangan mengubah warna aside/sidebar (selalu `#1e1e2d`)

---

## Referensi

- **Dokumentasi Metronic**: http://localhost:8000/documentation/getting-started/build
- **Source SCSS Utama**: `resources/assets/core/sass/components/_variables.scss`
- **Layout Variables**: `resources/assets/demo1/sass/layout/_variables.scss`
- **Bootstrap 5 Docs**: https://getbootstrap.com/docs/5.1/
