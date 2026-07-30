# Instalasi & Integrasi Device Fingerprint Solution X100-C
### Sistem Absensi Siswa SMA — Laravel 13 + Laragon (MySQL)

Dokumen ini adalah panduan lengkap (hardware → jaringan → konfigurasi device → integrasi software) untuk menghubungkan mesin fingerprint **Solution X100-C** ke sistem absensi berbasis Laravel yang berjalan di komputer server sekolah.

Referensi: *Instalasi Device Fingerprint X100C Solution* (manual bawaan alat).

---

## 1. Ringkasan Perangkat

| Spesifikasi | Nilai |
|---|---|
| Model | Solution X100-C |
| Power | DC 5V / 800mA (adaptor AC bawaan) |
| Suhu operasi | 0°C – 45°C |
| Kelembapan operasi | 20% – 80% |
| Konektivitas | Ethernet (TCP/IP), RS232, RS485, USB (mini) |
| Tinggi pemasangan disarankan | 1.4 – 1.5 meter dari lantai |
| IP default (TCP/IP) | `192.168.1.201` / Netmask `255.255.255.0` |
| Port komunikasi default (TCP/IP) | `4370` |
| Baud rate RS232 (rekomendasi) | `115200` |
| Baud rate RS485 (rekomendasi) | `9600` / `38400` |

> ⚠️ IP `192.168.1.201` dan port `4370` adalah nilai default khas keluarga protokol **ZK-compatible** (banyak dipakai vendor OEM seperti "Solution", ZKTeco, dsb). Ini penting untuk menentukan pendekatan integrasi di bagian 6.

---

## 2. Kebutuhan Hardware (Alat & Bahan)

### 2.1 Sudah termasuk dalam paket (sesuai manual)
- [ ] 1x Mesin fingerprint X100-C
- [ ] 1x Mounting template (untuk marking lubang baut di tembok)
- [ ] 1x Kabel RS232
- [ ] 1x RS485 converter
- [ ] 1x Kabel ekstensi RS485
- [ ] 1x Wireless doorbell (opsional, hanya untuk notifikasi bel — tidak wajib untuk absensi)
- [ ] 3x Baterai (untuk doorbell wireless)
- [ ] 1x USB flashdisk bawaan (berisi software & manual)
- [ ] 1x USB converter (mini USB → USB standar)
- [ ] Adaptor power 220V → 5V DC
- [ ] Baut pemasangan (installing bolt) x2

### 2.2 Wajib disiapkan sendiri (tidak termasuk paket)
- [ ] **1x Komputer/PC Server sekolah** — spek minimum:
  - Windows/Linux, RAM ≥ 8GB (untuk menjalankan Laragon + Laravel + MySQL nyaman)
  - PHP ≥ 8.2 (syarat Laravel 13), Composer, Node.js (untuk build asset)
  - Port ethernet aktif (LAN) — **tidak disarankan pakai WiFi** untuk device, gunakan kabel agar koneksi stabil
- [ ] **1x Kabel LAN (RJ45) straight-through** — jika device dan PC dihubungkan lewat switch/router sekolah
- [ ] **1x Kabel LAN cross-over** — jika device dihubungkan **langsung** ke PC tanpa switch (opsional, kebanyakan NIC modern sudah auto-MDIX sehingga kabel straight pun tetap jalan)
- [ ] **1x Switch/Router jaringan sekolah** (jika device digabung ke jaringan LAN sekolah, bukan koneksi point-to-point)
- [ ] Stop kontak/UPS di dekat lokasi pemasangan device (device tidak boleh mati mendadak saat proses enroll/scan — bisa merusak data)
- [ ] Obeng + fischer/baut tembok untuk mounting (bila dipasang permanen di dinding)
- [ ] (Opsional, jika mau kontrol pintu otomatis) Controller box C1, door lock (magnetic/drop bolt), release door button, kabel power terpisah — **tidak wajib** untuk kebutuhan absensi murni

### 2.3 Software yang wajib diinstal di server
| Software | Fungsi |
|---|---|
| Laragon (Full version) | Menyediakan Apache/Nginx, PHP, MySQL sekaligus |
| PHP ≥ 8.2 | Requirement Laravel 13 |
| Composer | Dependency manager PHP |
| Node.js + NPM | Build asset frontend (Vite) |
| Laravel 13 | Framework aplikasi absensi |
| MySQL (via Laragon) | Database absensi |
| Git (opsional) | Version control |
| Python 3.x (opsional, lihat opsi integrasi di bagian 6.2) | Untuk bridging protokol device ke Laravel |

---

## 3. Topologi Jaringan yang Disarankan

```
[ Mesin Fingerprint X100-C ]
        │  (kabel LAN)
        ▼
[ Switch/Router Sekolah ] ── [ PC Server (Laragon + Laravel) ]
        │
        ▼
[ Client lain: PC admin TU, dsb (opsional, akses via browser) ]
```

- **Rekomendasi**: satukan device dan server dalam **1 subnet/VLAN yang sama**, idealnya jaringan lokal terpisah dari jaringan WiFi umum sekolah (untuk keamanan data biometrik siswa).
- Jika server juga perlu diakses dari ruang TU/guru piket, pastikan server punya IP statis di LAN sekolah.
- Berikan **IP statis** ke device (jangan DHCP), agar Laravel selalu tahu alamat device untuk polling data.

---

## 4. Konfigurasi Device (Langkah di Mesin X100-C)

### 4.1 Pasang device
1. Tentukan lokasi dinding, tinggi ± 1400mm dari lantai (sesuai manual bagian 3.1).
2. Tempel mounting template, bor 2 lubang sejajar horizontal.
3. Pasang mounting plate → gantung device di plate tersebut.
4. Pastikan device terpasang kencang, tidak goyang.

### 4.2 Sambungkan power & jaringan
1. **Pastikan power OFF/adaptor belum ditancap** sebelum memasang kabel apapun (sesuai peringatan manual — salah wiring saat power ON bisa merusak board).
2. Sambungkan kabel LAN dari port Ethernet device ke switch sekolah (atau langsung ke NIC PC server).
3. Baru setelah semua kabel terpasang benar, tancapkan adaptor 5V ke device.

### 4.3 Setting IP di device (menu device)
Masuk ke: **Menu → Option → Comm Opt (Communication Option)**

Set parameter berikut:
| Parameter | Nilai yang disarankan |
|---|---|
| IP address | `192.168.1.201` (atau sesuaikan subnet LAN sekolah, contoh `192.168.10.50`) |
| Net Mask | `255.255.255.0` |
| Gateway | sesuai gateway LAN sekolah (contoh `192.168.10.1`) |
| Net Speed | `AUTO` |
| Dev Num | `1` (jika hanya 1 device; naikkan jika ada beberapa unit) |
| Link code | kosongkan dulu (`0`) — isi hanya jika ingin proteksi password koneksi |
| RS232 | `N` (matikan, karena pakai TCP/IP) |
| RS485 | `N` (matikan, karena pakai TCP/IP) |

> ⚠️ **Wajib restart device** setelah menyimpan (tekan **ESC → OK(Save)**), setting baru tidak aktif sebelum restart.

### 4.4 Test koneksi awal
1. Dari PC server, buka Command Prompt / terminal:
   ```
   ping 192.168.1.201
   ```
2. Jika reply sukses → device sudah bisa diakses di jaringan.
3. (Opsional) gunakan software bawaan CD ("Attendance Management Program") di PC Windows untuk tes awal koneksi via menu **External Program → Standalone Fingerprint Machine Communication → Connect**, pilih tipe **Ethernet**, isi IP `192.168.1.201`, Port `4370`, klik **Connect**. Jika muncul **"Product activate succeed"**, device dan protokolnya terkonfirmasi jalan normal.

### 4.5 Enroll fingerprint (percobaan)
Menu di device: **Menu → User Manage → User Enroll → Fingerprint Enroll** → tempel jari 3x sampai sukses → **OK**.

Lakukan **Menu → Option → Auto-test** untuk memastikan sensor & sistem berfungsi normal sebelum dipakai produksi.

---

## 5. Struktur Database Laravel (Usulan Skema)

Berikut skema minimum yang dibutuhkan agar data dari fingerprint bisa dipetakan ke siswa dan dicatat sebagai absensi.

```php
// database/migrations/xxxx_create_students_table.php
Schema::create('students', function (Blueprint $table) {
    $table->id();
    $table->string('nisn')->unique();
    $table->string('name');
    $table->string('class'); // kelas, contoh: XII IPA 1
    $table->unsignedInteger('fingerprint_uid')->unique()->nullable(); // User ID di mesin fingerprint
    $table->timestamps();
});

// database/migrations/xxxx_create_attendance_logs_table.php
Schema::create('attendance_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->constrained('students');
    $table->dateTime('scan_time');
    $table->enum('type', ['in', 'out'])->nullable(); // masuk / pulang
    $table->unsignedTinyInteger('verify_mode')->nullable(); // 1=fingerprint, dll (dari device)
    $table->string('device_serial')->nullable(); // SN mesin, contoh: NJF7261700356
    $table->timestamps();
});

// database/migrations/xxxx_create_fingerprint_devices_table.php
Schema::create('fingerprint_devices', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // contoh: "Gerbang Utama"
    $table->string('ip_address');
    $table->unsignedInteger('port')->default(4370);
    $table->string('serial_number')->nullable();
    $table->timestamp('last_synced_at')->nullable();
    $table->timestamps();
});
```

> Catatan penting: **`fingerprint_uid`** di tabel `students` HARUS sama persis dengan **User No.** yang di-set saat enroll di device (lihat manual bagian 6.2 poin 2: *"forward part of user No. can not be 0"*). Jadi saat enroll siswa baru di mesin, gunakan User ID yang sudah dipetakan lebih dulu ke NISN di sistem Laravel.

---

## 6. Strategi Integrasi ke Laravel

Device ini berkomunikasi lewat **TCP/IP port 4370** dengan pola protokol khas ZK-compatible. Laravel/PHP **tidak punya driver bawaan** untuk bicara langsung ke fingerprint device, jadi ada 3 opsi realistis, dari yang paling direkomendasikan:

### 🥇 Opsi A — Bridge service Python (paling stabil, direkomendasikan)
Gunakan library Python **`pyzk`** (mature, banyak dipakai untuk device ZK-compatible) sebagai jembatan, lalu kirim data ke Laravel via HTTP API.

**Alur:**
```
[X100-C] <--TCP:4370--> [Script Python (pyzk)] --HTTP POST--> [Laravel API endpoint] --> [MySQL]
```

**Langkah:**
1. Install python & pyzk di server:
   ```bash
   pip install pyzk requests
   ```
2. Contoh script `sync_attendance.py`:
   ```python
   from zk import ZK
   import requests

   conn = None
   zk = ZK('192.168.1.201', port=4370, timeout=5)
   try:
       conn = zk.connect()
       conn.disable_device()

       attendances = conn.get_attendance()
       payload = [
           {
               "fingerprint_uid": a.user_id,
               "scan_time": a.timestamp.strftime("%Y-%m-%d %H:%M:%S"),
               "status": a.status,
           }
           for a in attendances
       ]

       requests.post(
           "http://localhost/api/attendance/sync",
           json={"device_ip": "192.168.1.201", "logs": payload},
           headers={"X-API-KEY": "GANTI_DENGAN_TOKEN_RAHASIA"}
       )

       # opsional: hapus log di device setelah berhasil sync
       # conn.clear_attendance()

   finally:
       if conn:
           conn.enable_device()
           conn.disconnect()
   ```
3. Jadwalkan script ini via **Windows Task Scheduler** (jika server Windows) atau **cron** (jika Linux), misalnya setiap 1–5 menit.
4. Buat endpoint penerima di Laravel:
   ```php
   // routes/api.php
   Route::post('/attendance/sync', [AttendanceSyncController::class, 'store'])
       ->middleware('verify.device.key');
   ```
   ```php
   // app/Http/Controllers/AttendanceSyncController.php
   public function store(Request $request)
   {
       $validated = $request->validate([
           'device_ip' => 'required|string',
           'logs' => 'required|array',
           'logs.*.fingerprint_uid' => 'required|integer',
           'logs.*.scan_time' => 'required|date',
       ]);

       foreach ($validated['logs'] as $log) {
           $student = Student::where('fingerprint_uid', $log['fingerprint_uid'])->first();
           if (!$student) continue;

           AttendanceLog::firstOrCreate([
               'student_id'   => $student->id,
               'scan_time'    => $log['scan_time'],
           ], [
               'device_serial' => $validated['device_ip'],
           ]);
       }

       return response()->json(['status' => 'ok']);
   }
   ```

### 🥈 Opsi B — Package PHP komunitas untuk protokol ZK
Ada beberapa package komposer open-source yang mencoba mengimplementasikan protokol ZK langsung di PHP (tanpa Python), contoh kata kunci pencarian di Packagist: `zkteco`, `zklib php`. Kualitas dan kompatibilitas bervariasi tergantung firmware device, jadi **wajib diuji coba dulu** terhadap X100-C sebelum dipakai produksi. Kelebihannya: tidak perlu install Python terpisah, semua logic ada di Laravel (via Artisan Command + Scheduler `app/Console/Kernel.php`).

### 🥉 Opsi C — Semi-manual via software bawaan (fallback paling aman, tapi tidak realtime)
1. Gunakan software **Attendance Management Program** dari CD/USB bawaan (Windows only) untuk konek ke device dan **Download Data from Fingerprint Machine**.
2. Export hasilnya ke Excel/CSV.
3. Import file tersebut ke Laravel memakai package `maatwebsite/laravel-excel`.
4. Cocok sebagai **backup/cadangan** kalau opsi A/B bermasalah di jaringan sekolah, tapi kurang cocok untuk kebutuhan real-time.

> **Rekomendasi akhir**: pakai **Opsi A** sebagai jalur utama (paling stabil & banyak dipakai di lapangan untuk device sejenis), dengan **Opsi C** sebagai prosedur darurat manual jika device/jaringan bermasalah.

---

## 7. Checklist Sebelum Go-Live

- [ ] Device terpasang kokoh di dinding, tinggi sesuai standar (1.4–1.5m)
- [ ] Adaptor power device tersambung ke stop kontak yang stabil (idealnya via UPS)
- [ ] Device & server berada di 1 subnet, IP device sudah statis
- [ ] `ping` ke IP device dari server berhasil
- [ ] Test koneksi via software bawaan sukses ("Product activate succeed")
- [ ] Auto-test di device sukses (Menu → Option → Auto-test)
- [ ] Minimal 1 fingerprint percobaan berhasil di-enroll & diverifikasi, lalu dihapus lagi
- [ ] Tabel `students`, `attendance_logs`, `fingerprint_devices` sudah dimigrasi di Laravel
- [ ] Mapping `fingerprint_uid` siswa vs User ID di device sudah konsisten
- [ ] Endpoint sync API di Laravel sudah diuji dengan data dummy
- [ ] Script/scheduler sync (Opsi A/B) sudah berjalan otomatis dan teruji minimal 1 hari penuh
- [ ] Dust-proof film di sensor sidik jari sudah dilepas (sesuai catatan manual bagian 1.1)
- [ ] Backup rencana: prosedur manual (Opsi C) sudah didokumentasikan untuk staf TU

---

## 8. Troubleshooting Cepat (dari manual resmi)

| Masalah | Kemungkinan Penyebab | Solusi |
|---|---|---|
| LED power mati | Tidak ada power / tegangan kurang | Cek sambungan PWR & GND, pastikan tegangan sesuai (5V DC via adaptor bawaan) |
| Device tidak bisa konek ke PC | Masalah koneksi jaringan | Cek kabel LAN/RS232/RS485, pastikan setting IP & port benar dan device sudah di-restart setelah save setting |
| Layar selalu "Please try again" | Sensor kotor/tergores, kabel sensor kendor, chip rusak | Bersihkan sensor dengan scotch tape (untuk kotoran ringan), jika masih gagal hubungi supplier |
| Waktu reset ke "00:00" setelah restart | Baterai RTC device habis | Hubungi reseller untuk ganti baterai |
| Sidik jari kadang gagal terverifikasi | Kualitas enrollment sidik jari kurang baik | Enroll ulang dengan area jari lebih luas, kurang kerutan, disarankan enroll >1 jari cadangan per siswa |

---

## 9. Referensi
- Manual resmi: *Installation Instruction V2.0 — Solution X100-C Fingerprint Device*
- Serial Number unit contoh: `NJF7261700356`
- Software bawaan: *Solution — Software and User Manual* (CD/USB bawaan paket)
