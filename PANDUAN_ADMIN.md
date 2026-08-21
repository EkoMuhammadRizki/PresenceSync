# Panduan Admin PresenceSync

## Cara Buka Sistem

**Cukup buka browser dan ketik:**

```
https://presencesync.lokal
```

Atau double-klik shortcut **"PresenceSync"** di Desktop.

> **Tidak perlu buka terminal. Tidak perlu ketik npm run dev atau php artisan serve.**

---

## Syarat Sistem Bisa Diakses

✅ Komputer **dinyalakan** (Laragon akan otomatis jalan)
✅ **Laragon** berwarna hijau (Apache + MySQL)
✅ Buka browser → ketik `https://presencesync.lokal`

Jika Laragon belum hijau, buka Laragon → klik **"Start All"**.

---

## Cara Install Pertama Kali

1. Pastikan **Laragon** sudah terinstall  
   Download: https://laragon.org/download/ (pilih Full)

2. Double-klik file **`INSTALL_LARAGON.bat`**  
   → Klik kanan → **"Run as administrator"**

3. Ikuti instruksi di layar

4. Setelah selesai, buka browser → `https://presencesync.lokal`

**Login default:**
- Username: `admin`
- Password: `password`

---

## Cara Update Sistem

Ketika developer mengirim update:

1. Pastikan Laragon jalan (Apache + MySQL hijau)
2. Double-klik **`update.bat`**
3. Tunggu proses selesai (2-5 menit)
4. Sistem otomatis kembali online

---

## Troubleshooting

### Browser menampilkan "Not Secure" / peringatan SSL

Ini normal untuk jaringan lokal. Cara bypass:
1. Klik **"Advanced"** (atau "Lanjutan")
2. Klik **"Proceed to presencesync.lokal (unsafe)"**

Ini hanya perlu dilakukan **sekali** di browser baru.

---

### Sistem tidak bisa dibuka (browser error)

1. Buka **Laragon**
2. Pastikan **Apache** dan **MySQL** berwarna **hijau**
3. Kalau merah, klik **"Start All"**
4. Coba buka lagi di browser

---

### Lupa password

Hubungi developer atau jalankan di terminal Laragon:
```
php artisan tinker
App\Models\User::find(1)->update(['password' => bcrypt('password_baru')]);
```

---

## Informasi Sistem

| Item | Detail |
|------|--------|
| Alamat sistem | https://presencesync.lokal |
| Database | presencesync (MySQL Laragon) |
| Server | Apache Laragon |
| Platform | Laravel + Laragon |
| Auto-start | Ya, otomatis saat Windows boot |

---

*Panduan ini dibuat untuk admin non-teknis. Hubungi developer jika ada masalah di luar panduan ini.*
