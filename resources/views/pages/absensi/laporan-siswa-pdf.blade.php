<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Siswa</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; color: #000; line-height: 1.3; font-size: 11px; margin: 0; padding: 20px; }
        .kop-surat { width: 100%; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 15px; text-align: center; }
        .kop-surat table { width: 100%; border-collapse: collapse; }
        .kop-surat td { vertical-align: middle; }
        .kop-title-1 { font-size: 13px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .kop-title-2 { font-size: 14px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .kop-title-3 { font-size: 13px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .kop-main-title { font-size: 16px; font-weight: bold; text-transform: uppercase; margin: 2px 0; }
        .kop-address { font-size: 10px; font-style: italic; margin: 1px 0; }
        .kop-contact { font-size: 10px; font-style: italic; margin: 1px 0; }
        
        .doc-title { text-align: center; margin: 15px 0 10px 0; text-transform: uppercase; }
        .doc-title h3 { margin: 0; font-size: 14px; text-decoration: underline; }
        .doc-title p { margin: 2px 0 0 0; font-size: 10px; color: #555; }

        .meta-info { margin-bottom: 15px; background: #f9f9f9; border: 1px solid #ddd; padding: 8px 12px; }
        .meta-info table { width: 100%; }
        .meta-info td { padding: 2px 0; font-size: 10px; }
        .meta-info td.label { font-weight: bold; color: #333; width: 100px; }
        
        .summary-boxes { margin-bottom: 15px; }
        .summary-boxes table { width: 100%; border-collapse: collapse; }
        .summary-box { border: 1px solid #ccc; border-radius: 4px; padding: 6px; text-align: center; background: #fdfdfd; }
        .summary-box .val { font-size: 16px; font-weight: 700; }
        .summary-box .lbl { font-size: 9px; text-transform: uppercase; }

        .content-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .content-table th { background-color: #f2f2f2; color: #000; font-weight: bold; text-align: center; padding: 7px 5px; font-size: 10px; text-transform: uppercase; border: 1px solid #000; }
        .content-table td { padding: 6px 5px; border: 1px solid #000; font-size: 10px; }
        .text-center { text-align: center; }
        .footer { margin-top: 25px; }

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
                <td style="width: 80px; text-align: center; vertical-align: middle;">
                    <img src="{{ asset('absensi/media/logos/logo-sekolah.png') }}" style="width: 75px; height: auto;" />
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
                <td style="width: 80px;"></td>
            </tr>
        </table>
    </div>

    <div class="doc-title">
        <h3>LAPORAN DATA SISWA</h3>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}</p>
    </div>

    <div class="meta-info">
        <table>
            <tr>
                <td class="label">Kelas</td>
                <td>: {{ $kelasFilter }}</td>
                <td class="label" style="width:100px;">Total Siswa</td>
                <td>: {{ $siswas->count() }} Orang</td>
            </tr>
        </table>
    </div>

    {{-- Summary Boxes --}}
    <div class="summary-boxes">
        <table>
            <tr>
                <td style="width:16%;">
                    <div class="summary-box" style="background:#e8fff3;">
                        <div class="val" style="color:#50cd89;">{{ $siswas->where('status','aktif')->count() }}</div>
                        <div class="lbl" style="color:#50cd89;">Aktif</div>
                    </div>
                </td>
                <td style="width:16%;">
                    <div class="summary-box" style="background:#f8f5ff;">
                        <div class="val" style="color:#7239ea;">{{ $siswas->where('status','lulus')->count() }}</div>
                        <div class="lbl" style="color:#7239ea;">Lulus</div>
                    </div>
                </td>
                <td style="width:16%;">
                    <div class="summary-box" style="background:#fff5f8;">
                        <div class="val" style="color:#f1416c;">{{ $siswas->where('status','keluar')->count() }}</div>
                        <div class="lbl" style="color:#f1416c;">Keluar</div>
                    </div>
                </td>
                <td style="width:16%;">
                    <div class="summary-box" style="background:#eff8ff;">
                        <div class="val" style="color:#009ef7;">{{ $siswas->where('jenis_kelamin','L')->count() }}</div>
                        <div class="lbl" style="color:#009ef7;">Laki-laki</div>
                    </div>
                </td>
                <td style="width:16%;">
                    <div class="summary-box" style="background:#fff0f6;">
                        <div class="val" style="color:#e91e8c;">{{ $siswas->where('jenis_kelamin','P')->count() }}</div>
                        <div class="lbl" style="color:#e91e8c;">Perempuan</div>
                    </div>
                </td>
                <td style="width:16%;">
                    <div class="summary-box" style="background:#f0f4ff;">
                        <div class="val" style="color:#3B5BDB;">{{ $siswas->count() }}</div>
                        <div class="lbl" style="color:#3B5BDB;">Total</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="content-table">
        <thead>
            <tr>
                <th class="text-center" style="width:28px;">No</th>
                <th style="width:65px;">NIS</th>
                <th>Nama Lengkap</th>
                <th class="text-center" style="width:70px;">JK</th>
                <th style="width:60px;">Kelas</th>
                <th style="width:65px;">Tgl. Lahir</th>
                <th class="text-center" style="width:50px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($siswas as $i => $siswa)
            @php
                $badgeClass = match($siswa->status) {
                    'aktif'  => 'badge-aktif',
                    'lulus'  => 'badge-lulus',
                    'keluar' => 'badge-keluar',
                    default  => '',
                };
            @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $siswa->nis ?? '-' }}</td>
                <td><strong>{{ $siswa->nama }}</strong></td>
                <td class="text-center">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                <td>{{ $siswa->kelas?->nama ?? '-' }}</td>
                <td>{{ $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d/m/Y') : '-' }}</td>
                <td class="text-center">
                    <span class="badge {{ $badgeClass }}">{{ ucfirst($siswa->status ?? '-') }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <table style="margin-top:30px; width:100%;">
            <tr>
                <td style="width:65%;"></td>
                <td class="text-center" style="border-top:1px solid #ddd; padding-top:8px;">
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
