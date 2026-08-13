# 🚨 PANDUAN DARURAT — Sistem Absensi Error

**Dokumen ini untuk:** Guru / Staff TU  
**Tujuan:** Langkah-langkah yang harus dilakukan jika sistem absensi online tidak bisa diakses

---

> ✉️ **Jika bingung atau tidak berhasil, hubungi developer:**  
> **Eko Muhammad Rizki** — *(isi nomor HP/WA)*

---

## 🔴 TANDA-TANDA SISTEM UTAMA ERROR

Sistem utama (hosting) bermasalah jika:
- Fingerprint muncul error / data tidak masuk
- Buka website absensi muncul tulisan error / halaman putih
- Website tidak bisa dibuka sama sekali

---

## LANGKAH 1 — Pastikan Ini Bukan Masalah Internet

Sebelum beralih ke backup, cek dulu:

☐ Coba buka website lain (contoh: google.com) — apakah bisa?  
- **Bisa buka google.com** → masalah di sistem absensi, lanjut ke Langkah 2  
- **Tidak bisa buka google.com** → masalah internet sekolah, hubungi admin jaringan

---

## LANGKAH 2 — Nyalakan Server Sekolah (Backup)

1. Cari komputer server sekolah
2. Nyalakan komputer (jika belum menyala)
3. Tunggu Windows selesai loading
4. Laragon akan otomatis jalan di background *(ikon Laragon muncul di pojok kanan bawah taskbar)*

   **Jika ikon Laragon tidak muncul:**
   - Cari file Laragon di Desktop atau Start Menu
   - Klik dua kali untuk buka
   - Klik tombol **"Start All"**
   - Pastikan tulisan **Apache** dan **MySQL** berwarna **hijau**

5. Klik dua kali file **`switch_backup.bat`** di folder presencesync
6. Pilih **[1] Aktifkan Server Backup**
7. Script akan otomatis konfigurasi sistem

---

## LANGKAH 3 — Pindahkan Mesin Fingerprint ke Server Backup

> ⚠️ Langkah ini perlu dilakukan di **setiap mesin fingerprint** yang ada.

### Untuk Mesin ZKTeco / X100C Solution:

1. Tekan tombol **Menu** di mesin fingerprint
2. Masuk ke **Comm** → **ADMS**  
   *(atau: Settings → Network → Server Address)*
3. Ubah **Server Address** dari:
   ```
   presencesync.domain.com   (domain lama)
   ```
   Menjadi:
   ```
   [IP-SERVER-SEKOLAH]        (contoh: 192.168.1.100)
   ```
4. **Port**: pastikan isi **80**
5. Simpan → **OK**
6. Restart mesin fingerprint (cabut colokan listrik, tunggu 5 detik, pasang lagi)

### Verifikasi:
- Coba tap fingerprint → lihat apakah data masuk ke sistem backup
- Buka browser di komputer mana saja → ketik: `http://[IP-SERVER]/presencesync/public`
- Jika muncul halaman login → ✅ Backup aktif!

---

## LANGKAH 4 — Gunakan Sistem Backup

Sistem backup siap digunakan. Data absensi akan tersimpan di **komputer server sekolah**.

> ⚠️ **Penting:** Data di server backup **tidak otomatis masuk** ke sistem utama (hosting).  
> Catat tanggal mulai menggunakan backup, nanti developer akan bantu sinkronisasi data.

---

## LANGKAH 5 — Kembali ke Sistem Utama (Setelah Hosting Pulih)

Jika sistem utama sudah normal kembali:

1. Klik dua kali **`switch_backup.bat`**
2. Pilih **[3] Backup Data & Kembali ke Hosting Utama**
3. Script akan otomatis:
   - Export data dari server backup ke file
   - Tunjukkan langkah selanjutnya
4. Kirim file export ke developer untuk dimasukkan ke hosting utama
5. Pindahkan mesin fingerprint kembali ke domain hosting:
   - Masuk pengaturan ADMS mesin fingerprint
   - Ubah Server Address kembali ke: `presencesync.domain.com`
   - Restart mesin

---

## 📋 Catatan Penting

Setiap kali menggunakan backup, **catat di sini:**

| Tanggal Mulai | Tanggal Selesai | Penyebab | Keterangan |
|---|---|---|---|
| | | | |
| | | | |
| | | | |

*Berikan catatan ini ke developer saat proses sinkronisasi data.*

---

*Dokumen ini dibuat oleh: **Eko Muhammad Rizki***  
*Versi: 1.0 — Agustus 2026*
