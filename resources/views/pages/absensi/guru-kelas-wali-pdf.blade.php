<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Siswa Kelas Wali</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            line-height: 1.4;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            margin-bottom: 25px;
            border-bottom: 3px solid #D9214E; /* Merah theme color */
            padding-bottom: 10px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .header td {
            vertical-align: middle;
        }
        .title-section h2 {
            margin: 0 0 5px 0;
            color: #D9214E;
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .title-section p {
            margin: 0;
            color: #666666;
            font-size: 12px;
        }
        .meta-info {
            margin-bottom: 20px;
            background-color: #F9F9F9;
            border-left: 4px solid #D9214E;
            padding: 10px 15px;
        }
        .meta-info table {
            width: 100%;
        }
        .meta-info td {
            padding: 3px 0;
            font-size: 11px;
        }
        .meta-info td.label {
            font-weight: bold;
            color: #555555;
            width: 100px;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .content-table th {
            background-color: #D9214E; /* Merah theme color */
            color: #FFFFFF;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            font-size: 11px;
            text-transform: uppercase;
            border: 1px solid #D9214E;
        }
        .content-table td {
            padding: 9px 10px;
            border: 1px solid #E5E5E5;
            font-size: 11px;
        }
        .content-table tr:nth-child(even) {
            background-color: #FDFDFD;
        }
        .content-table tr:hover {
            background-color: #F5F5F5;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-weight: bold;
            border-radius: 3px;
            font-size: 10px;
        }
        .badge-success {
            background-color: #E8FFF3;
            color: #50CD89;
        }
        .badge-warning {
            background-color: #FFF8DD;
            color: #F1BC00;
        }
        .badge-danger {
            background-color: #FFF5F8;
            color: #F1416C;
        }
        .footer {
            margin-top: 40px;
            width: 100%;
        }
        .footer table {
            width: 100%;
        }
        .footer td {
            font-size: 11px;
        }
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

    <div class="header">
        <table>
            <tr>
                <td style="width: 60px; padding-right: 15px; vertical-align: middle;">
                    <img src="{{ asset('absensi/media/logos/logo-sekolah.png') }}" style="width: 55px; height: auto;" />
                </td>
                <td class="title-section" style="vertical-align: middle;">
                    <h2>Laporan Data Siswa Kelas Wali</h2>
                    <p>Sistem Informasi Kehadiran - PresenceSync</p>
                </td>
                <td style="text-align: right; color: #888888; font-size: 10px; vertical-align: middle;">
                    Tanggal Cetak: {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y, H:i') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="meta-info">
        <table>
            <tr>
                <td class="label">Kelas Wali</td>
                <td>: {{ $kelas->nama_lengkap }} ({{ $kelas->tingkat }} - {{ $kelas->nama }})</td>
                <td class="label">Wali Kelas</td>
                <td>: {{ $guru->nama }}</td>
            </tr>
            <tr>
                <td class="label">Tahun Ajaran</td>
                @php
                    $activeSemesterForPdf = \App\Models\Semester::with('tahunAjaran')->where('status', 'aktif')->first();
                @endphp
                <td>: {{ $activeSemesterForPdf?->tahunAjaran?->nama ?? '-' }} (Semester: {{ $activeSemesterForPdf ? ucfirst($activeSemesterForPdf->jenis) : '-' }})</td>
                <td class="label">Total Siswa</td>
                <td>: {{ $siswas->count() }} Orang</td>
            </tr>
        </table>
    </div>

    <table class="content-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th style="width: 100px;">NIS</th>
                <th>Nama Lengkap</th>
                <th class="text-center" style="width: 100px;">Jenis Kelamin</th>
                <th class="text-center" style="width: 80px;">Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($siswas as $i => $siswa)
            @php
                $pct = $rekap[$siswa->id]['persentase'] ?? 0;
                $badgeClass = $pct >= 85 ? 'badge-success' : ($pct >= 70 ? 'badge-warning' : 'badge-danger');
            @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $siswa->nis ?? '-' }}</td>
                <td><strong>{{ $siswa->nama }}</strong></td>
                <td class="text-center">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td>
                <td class="text-center">
                    <span class="badge {{ $badgeClass }}">{{ $pct }}%</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <table style="margin-top: 50px;">
            <tr>
                <td style="width: 65%;"></td>
                <td class="text-center" style="width: 35%; border-top: 1px solid #DDDDDD; padding-top: 10px;">
                    <p style="margin: 0 0 50px 0;">Mengetahui,<br>Wali Kelas</p>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">{{ $guru->nama }}</p>
                    <p style="margin: 3px 0 0 0; color: #666666; font-size: 10px;">NIP. {{ $guru->nip ?? '-' }}</p>
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
