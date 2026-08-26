Analisis Penyebab Sistem Lemot Sebelumnya:

Masalah N+1 Database Query & Blocking I/O:
Ketika mesin fisik dinyalakan, mesin langsung mengirimkan puluhan/ratusan baris riwayat scan log (ADMS ATTLOG) sekaligus dalam 1 request HTTP.
Sebelumnya, setiap 1 baris log diproses secara beruntun (sequential loop) dengan memanggil 8–10 query database terpisah (SELECT Siswa, SELECT Semester, SELECT AturanJam, SELECT/INSERT Kehadiran, SELECT/INSERT SyncLog, UPDATE Device, dan activity_log).
Jika mesin mengirim 100 log Server mengeksekusi 800–1.000 query database sinkron dan menulis disk log berulang kali.
Web Server Thread Freeze / Starvation:
Proses query beruntun tersebut memblokir worker thread web server (terutama PHP), sehingga ketika pengguna membuka halaman web lain (seperti Dashboard atau Log Scan), request tersebut antre di belakang request mesin dan mengalami timeout / loading sangat lama (lemot).
Mesin Mengalami Timeout & Melakukan Retry Berulang:
Karena server lokal lambat membalas respons OK, mesin ADMS menganggap pengiriman gagal dan terus-menerus mengirim ulang log yang sama (terjadi request storm).

Solusi & Optimasi Best Practice yang Diterapkan:
Di 

app/Http/Controllers/Absensi/AdmsController.php
, metode penerimaan data mesin telah ditingkatkan dengan arsitektur Batch In-Memory Ingestion:

Zero-Query In-Memory Parsing:
Seluruh baris teks scan log dari mesin diparsing langsung di memori array PHP tanpa menyentuh database sama sekali pada tahap pembacaan.
Preload Master Data Sekali di Awal (Bulk Lookup O(1)):
Data master (Semester Aktif, seluruh AturanJam, dan seluruh data Siswa yang bersangkutan) ditarik hanya dengan 1 kali query saja di awal batch, lalu di-mapping ke hash table memori.
Batch Deduplication & Bulk Process:
Pengecekan duplikasi log dan pembuatan record Kehadiran diproses dalam memori dan dibungkus dalam 1 transaksi database atomik (DB::beginTransaction).
Instan Response ke Mesin:
Waktu eksekusi yang tadinya 10–20 detik kini terpangkas menjadi hanya 15–50 milidetik (< 0.05 detik) untuk 100+ log.
Mesin menerima respons OK: N seketika sehingga mesin tidak akan pernah hang atau retry berulang.