# PresenceSync — Panduan Integrasi Perangkat Fingerprint

## 1. Bagaimana Sistem PresenceSync Mendukung Perangkat Fingerprint?

Sistem PresenceSync menggunakan kolom `fingerprint_id` (sebagai User ID / PIN / Enrollment ID) yang unik untuk setiap siswa/guru di database.

Dengan prinsip pemetaan ID ini, sistem PresenceSync bersifat **universal**. Artinya, sistem ini mendukung perangkat fingerprint merk apa pun, asalkan data yang dikirim atau ditarik dari perangkat tersebut memiliki kecocokan ID antara mesin dan database Laravel.

## 2. Tiga Pilihan Arsitektur Integrasi Teknis

Secara umum, integrasi dari mesin sidik jari di sekolah ke server Laravel/PresenceSync dapat dilakukan melalui 3 metode utama:

### Metode A: Cloud Push / Protokol ADMS (Otomatis & Real-Time) — Sangat Direkomendasikan

Metode ini digunakan jika server sekolah di-hosting secara online (Cloud VPS) atau IP Public.

- **Cara Kerja:** Mesin fingerprint memiliki fitur ADMS (Automatic Data Master Server) / Web Server. Anda cukup memasukkan URL/IP domain server PresenceSync ke menu ADMS di mesin. Setiap kali siswa menempelkan sidik jari, mesin secara otomatis mengirimkan data absensi (HTTP POST) langsung ke endpoint API di server.
- **Kelebihan:** Real-time, otomatis tanpa perlu komputer perantara yang menyala terus di sekolah.

### Metode B: Local Middleware / Pull SDK (Otomatis Jaringan Lokal)

Metode ini digunakan jika server berada di jaringan lokal sekolah (Local Server) atau jika mesin tidak mendukung ADMS.

- **Cara Kerja:** Sebuah komputer/PC lokal di sekolah menjalankan program kecil (middleware/script helper menggunakan Python atau PHP CLI). Script ini akan terkoneksi ke mesin fingerprint lewat kabel LAN (port TCP/IP 4370) menggunakan protokol SDK ZKTeco. Script ini akan menarik data log absensi setiap beberapa menit, lalu mengirimkannya (PUSH) via API ke server PresenceSync.
- **Kelebihan:** Aman, tidak membutuhkan fitur ADMS berbayar di mesin.

### Metode C: Eksport-Import via USB Flashdisk (Manual/Offline)

- **Cara Kerja:** Administrator mengunduh log kehadiran dari mesin menggunakan USB Flashdisk (menghasilkan file `.txt` / Excel `.xls`). File tersebut kemudian diunggah secara manual melalui halaman dashboard PresenceSync.
- **Kelebihan:** Cocok sebagai backup jika internet sekolah mati atau server sedang offline.

## 3. Rekomendasi Device Fingerprint Spesifik (Budget < Rp 1.500.000)

Di Indonesia, merk terpopuler untuk fingerprint sekolah dengan dukungan purna jual yang baik adalah **Solution** dan **Fingerspot** (keduanya menggunakan mainboard/sensor berbasis ZKTeco yang sangat andal).

Berikut adalah tipe spesifik di bawah Rp 1,5 juta yang sangat cocok untuk PresenceSync:

| Tipe Perangkat | Estimasi Harga | Koneksi | Kapasitas Sidik Jari | Fitur Unggulan |
|---|---|---|---|---|
| **Solution X100-C** | Rp 1.100.000 – Rp 1.250.000 | TCP/IP (LAN), USB Cable, USB Flashdisk | 10.000 user | Sangat Direkomendasikan. Mendukung ADMS (Cloud Push), memori log besar (200.000 transaksi), dan casing kokoh. Cocok ditaruh di gerbang sekolah. |
| **Solution X302-S** | Rp 1.250.000 – Rp 1.350.000 | TCP/IP (LAN), USB Flashdisk | 3.000 user | Mendukung Web Server/ADMS, dan memiliki fitur Self-Service Query untuk cetak laporan langsung jika dibutuhkan secara offline. |
| **Fingerspot Revo W202BNC** | Rp 1.200.000 – Rp 1.400.000 | WiFi, TCP/IP (LAN), USB Flashdisk | 3.000 user | Sangat cocok jika penarikan kabel LAN sulit, karena sudah mendukung koneksi WiFi lokal. Mendukung API Fingerspot Cloud (opsional). |
| **ZKTeco K40** | Rp 850.000 – Rp 1.000.000 | TCP/IP (LAN), USB Flashdisk | 1.000 - 3.000 user | Opsi paling ekonomis. Desain minimalis, sudah memiliki baterai cadangan internal (battery backup) sehingga mesin tetap menyala saat mati lampu listrik sesaat. |

## 4. Alur Integrasi Operasional Harian

Untuk mengintegrasikan device ke sistem PresenceSync di sekolah, ikuti langkah praktis berikut:

**Langkah 1 — Pendaftaran (Enrollment)**
Operator sekolah mendaftarkan sidik jari siswa langsung di mesin. Mesin akan memberikan nomor PIN (contoh: `1002`).

**Langkah 2 — Sinkronisasi ID**
Di dashboard admin PresenceSync, ubah data siswa tersebut dan masukkan nilai `1002` ke kolom `fingerprint_id`.

**Langkah 3 — Proses Presensi**
Saat siswa melakukan scan sidik jari saat datang/pulang sekolah, data log timestamp otomatis masuk ke sistem, dicocokkan dengan aturan jam masuk sekolah, dan langsung memperbarui status kehadiran siswa di database secara real-time.

### Alur Singkat

1. Daftarkan sidik jari siswa di mesin → mesin memberi nomor PIN (misal: `105`)
2. Masukkan PIN `105` pada kolom `fingerprint_id` siswa di Laravel
3. Siswa tap jari → data dikirim via ADMS / SDK Middleware / Flashdisk
4. Sistem mencocokkan PIN `105` & mencatat status Hadir/Terlambat ke database
