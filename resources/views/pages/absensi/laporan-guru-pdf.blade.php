<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Guru</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; color: #000; line-height: 1.3; font-size: 10px; margin: 0; padding: 20px; }
        .kop-surat { width: 100%; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 12px; text-align: center; }
        .kop-surat table { width: 100%; border-collapse: collapse; }
        .kop-surat td { vertical-align: middle; }
        .kop-title-1 { font-size: 13px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .kop-title-2 { font-size: 14px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .kop-title-3 { font-size: 13px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .kop-main-title { font-size: 16px; font-weight: bold; text-transform: uppercase; margin: 2px 0; }
        .kop-address { font-size: 10px; font-style: italic; margin: 1px 0; }
        .kop-contact { font-size: 10px; font-style: italic; margin: 1px 0; }

        .doc-title { text-align: center; margin: 10px 0 10px 0; text-transform: uppercase; }
        .doc-title h3 { margin: 0; font-size: 13px; text-decoration: underline; }
        .doc-title p { margin: 2px 0 0 0; font-size: 9px; color: #555; }

        .meta-info { margin-bottom: 12px; background: #f9f9f9; border: 1px solid #ddd; padding: 6px 10px; }
        .meta-info td { padding: 2px 0; font-size: 9px; }
        .meta-info td.label { font-weight: bold; color: #333; width: 90px; }
        
        .content-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .content-table th { background-color: #f2f2f2; color: #000; font-weight: bold; text-align: center; padding: 6px 5px; font-size: 9px; text-transform: uppercase; border: 1px solid #000; }
        .content-table td { padding: 5px; border: 1px solid #000; font-size: 9px; }
        .text-center { text-align: center; }
        .footer { margin-top: 20px; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="background: #2aa8d8; color: #fff; padding: 12px 24px; display: flex; align-items: center; justify-content: space-between; font-family: sans-serif; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 20px; border-radius: 8px;">
        <div>
            <h4 style="margin: 0; font-size: 14px; font-weight: bold;">Laporan Siap Dicetak</h4>
            <p style="margin: 2px 0 0 0; font-size: 11px; opacity: 0.85;">Dialog cetak browser akan terbuka otomatis. Pilih opsi "Simpan sebagai PDF" / "Save as PDF" untuk mengunduh berkas.</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" style="background: #fff; color: #1a4fa0; border: none; padding: 6px 16px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer;">Cetak / Simpan PDF</button>
            <button onclick="window.close()" style="background: rgba(255,255,255,0.2); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; cursor: pointer;">Tutup</button>
        </div>
    </div>

    <div class="kop-surat">
        <table>
            <tr>
                <td style="width: 75px; text-align: center; vertical-align: middle;">
                    <img src="{{ asset('absensi/media/logos/logo-sekolah.png') }}" style="width: 70px; height: auto;" />
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    <div class="kop-title-1">PEMERINTAH PROVINSI JAWA BARAT</div>
                    <div class="kop-title-2">DINAS PENDIDIKAN</div>
                    <div class="kop-title-3">CABANG DINAS PENDIDIKAN WILAYAH VIII</div>
                    <div class="kop-main-title">SEKOLAH MENENGAH ATAS NEGERI 1 CIPARAY</div>
                    <div class="kop-address">Jl. Raya Pacet Nomor 188 Telepon (022) 5950861</div>
                    <div class="kop-contact">Fax. (022) 5955862 Website: www.sman1ciparay.sch.id e-mail: smansatoeciparay@gmail.com</div>
                    <div class="kop-address" style="font-weight: bold; font-style: normal;">Ciparay Kabupaten Bandung - 40381</div>
                </td>
                <td style="width: 75px;"></td>
            </tr>
        </table>
    </div>

    <div class="doc-title">
        <h3>LAPORAN DATA GURU</h3>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}</p>
    </div>

    <div class="meta-info">
        <table>
            <tr>
                <td class="label">Total Guru</td>
                <td>: {{ $gurus->count() }} Orang</td>
                <td class="label" style="width:100px;">Total Presensi</td>
                <td>: {{ $kehadiranRekap->sum('total') }} Record</td>
            </tr>
        </table>
    </div>

    <table class="content-table">
        <thead>
            <tr>
                <th class="text-center" style="width:25px;">No</th>
                <th>Nama Guru</th>
                <th style="width:80px;">NIP</th>
                <th style="width:120px;">Email</th>
                <th style="width:70px;">No. HP</th>
                <th style="width:55px;">Kelas Wali</th>
                <th class="text-center" style="width:45px;">Total</th>
                <th class="text-center" style="width:40px;">Hadir</th>
                <th class="text-center" style="width:52px;">Terlambat</th>
                <th class="text-center" style="width:38px;">Sakit</th>
                <th class="text-center" style="width:35px;">Izin</th>
                <th class="text-center" style="width:40px;">Alpha</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($gurus as $i => $guru)
            @php
                $rekap = $kehadiranRekap->get($guru->id);
                $total     = $rekap?->total ?? 0;
                $hadir     = $rekap?->hadir ?? 0;
                $terlambat = $rekap?->terlambat ?? 0;
                $sakit     = $rekap?->sakit ?? 0;
                $izin      = $rekap?->izin ?? 0;
                $alpha     = $rekap?->alpha ?? 0;
                $kelasWali = $guru->kelas->first();
            @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td><strong>{{ $guru->nama }}</strong></td>
                <td>{{ $guru->nip ?? '-' }}</td>
                <td>{{ $guru->email ?? '-' }}</td>
                <td>{{ $guru->no_hp ?? '-' }}</td>
                <td>{{ $kelasWali?->nama ?? '-' }}</td>
                <td class="text-center">{{ $total }}</td>
                <td class="text-center" style="color:#50cd89; font-weight:bold;">{{ $hadir }}</td>
                <td class="text-center" style="color:#f6c000; font-weight:bold;">{{ $terlambat }}</td>
                <td class="text-center" style="color:#009ef7;">{{ $sakit }}</td>
                <td class="text-center" style="color:#7239ea;">{{ $izin }}</td>
                <td class="text-center" style="color:#f1416c;">{{ $alpha }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <table style="margin-top:28px; width:100%;">
            <tr>
                <td style="width:65%;"></td>
                <td class="text-center" style="border-top:1px solid #ddd; padding-top:8px; font-size:10px;">
                    <p style="margin:0 0 40px 0;">Mengetahui,<br>Kepala Sekolah</p>
                    <p style="margin:0; font-weight:bold; text-decoration:underline;">___________________</p>
                    <p style="margin:3px 0 0 0; font-size:9px; color:#999;">NIP. -</p>
                </td>
            </tr>
        </table>
    </div>
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
