<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\Pengaduan;
use App\Models\Semester;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Mpdf\Mpdf;

class LaporanController extends Controller
{
    // ─────────────────────────────────────────────────
    //  LAPORAN SISWA
    // ─────────────────────────────────────────────────

    public function siswa(Request $request)
    {
        $kelas  = Kelas::where('status', 'aktif')->orderBy('tingkat')->orderBy('nama')->get();
        $query  = Siswa::with(['kelas'])->orderBy('nama');

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('nama', 'like', "%{$q}%")
                   ->orWhere('nis', 'like', "%{$q}%")
                   ->orWhere('nisn', 'like', "%{$q}%");
            });
        }

        $siswas = $query->get();

        // Rekap per status
        $rekap = [
            'total'  => $siswas->count(),
            'aktif'  => $siswas->where('status', 'aktif')->count(),
            'lulus'  => $siswas->where('status', 'lulus')->count(),
            'keluar' => $siswas->where('status', 'keluar')->count(),
            'L'      => $siswas->where('jenis_kelamin', 'L')->count(),
            'P'      => $siswas->where('jenis_kelamin', 'P')->count(),
        ];

        return view('pages.absensi.laporan-siswa', compact('siswas', 'kelas', 'rekap'));
    }

    public function exportSiswaPdf(Request $request)
    {
        $query = Siswa::with(['kelas'])->orderBy('nama');

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('nama', 'like', "%{$q}%")
                   ->orWhere('nis', 'like', "%{$q}%")
                   ->orWhere('nisn', 'like', "%{$q}%");
            });
        }

        $siswas = $query->get();
        $filters = $request->only(['kelas_id', 'status', 'search']);
        $kelasFilter = $request->filled('kelas_id')
            ? Kelas::find($request->kelas_id)?->nama
            : 'Semua Kelas';

        $logoPath = public_path('absensi/media/logos/logo-sekolah.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';

        return view('pages.absensi.laporan-siswa-pdf', compact('siswas', 'filters', 'kelasFilter', 'logoBase64'));
    }

    public function exportSiswaExcel(Request $request)
    {
        $query = Siswa::with(['kelas'])->orderBy('nama');

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('nama', 'like', "%{$q}%")
                   ->orWhere('nis', 'like', "%{$q}%")
                   ->orWhere('nisn', 'like', "%{$q}%");
            });
        }

        $siswas = $query->get();
        $kelasFilter = $request->filled('kelas_id')
            ? Kelas::find($request->kelas_id)?->nama ?? 'Semua Kelas'
            : 'Semua Kelas';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Siswa');

        // Add Logo
        $logoPath = public_path('absensi/media/logos/logo-sekolah.png');
        if (file_exists($logoPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Logo Sekolah');
            $drawing->setPath($logoPath);
            $drawing->setHeight(65);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(10);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
        }

        // Kop Surat Headers
        $sheet->setCellValue('C1', 'PEMERINTAH PROVINSI JAWA BARAT');
        $sheet->setCellValue('C2', 'DINAS PENDIDIKAN');
        $sheet->setCellValue('C3', 'CABANG DINAS PENDIDIKAN WILAYAH VIII');
        $sheet->setCellValue('C4', 'SEKOLAH MENENGAH ATAS NEGERI 1 CIPARAY');
        $sheet->setCellValue('C5', 'Jl. Raya Pacet Nomor 188 Telepon (022) 5950861');
        $sheet->setCellValue('C6', 'Fax. (022) 5955862 Website: www.sman1ciparay.sch.id e-mail: smansatoeciparay@gmail.com');
        $sheet->setCellValue('C7', 'Ciparay Kabupaten Bandung - 40381');

        $sheet->mergeCells('C1:K1');
        $sheet->mergeCells('C2:K2');
        $sheet->mergeCells('C3:K3');
        $sheet->mergeCells('C4:K4');
        $sheet->mergeCells('C5:K5');
        $sheet->mergeCells('C6:K6');
        $sheet->mergeCells('C7:K7');

        $sheet->getStyle('C1:K3')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('C4:K4')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('C5:K6')->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle('C7:K7')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('C1:K7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Double Border
        $sheet->getStyle('A7:K7')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);

        // Title & Filter Meta
        $sheet->setCellValue('A9', 'LAPORAN DATA SISWA');
        $sheet->mergeCells('A9:K9');
        $sheet->getStyle('A9')->getFont()->setBold(true)->setSize(13)->setUnderline(true);
        $sheet->getStyle('A9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A11', 'Kelas: ' . $kelasFilter . '  |  Total Siswa: ' . $siswas->count() . ' Orang  |  Dicetak: ' . now()->translatedFormat('d F Y, H:i'));
        $sheet->mergeCells('A11:K11');
        $sheet->getStyle('A11')->getFont()->setBold(true)->setSize(10);

        // Table Header
        $headers = ['No', 'NIS', 'NISN', 'Nama Siswa', 'Jenis Kelamin', 'Kelas', 'Status', 'Tempat Lahir', 'Tanggal Lahir', 'No. HP', 'Alamat'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '13', $header);
            $col++;
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '009EF7']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];
        $sheet->getStyle('A13:K13')->applyFromArray($headerStyle);
        $sheet->getRowDimension(13)->setRowHeight(25);

        $row = 14;
        foreach ($siswas as $i => $siswa) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $siswa->nis ?? '-');
            $sheet->setCellValue('C' . $row, $siswa->nisn ?? '-');
            $sheet->setCellValue('D' . $row, $siswa->nama);
            $sheet->setCellValue('E' . $row, $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan');
            $sheet->setCellValue('F' . $row, $siswa->kelas?->nama ?? '-');
            $sheet->setCellValue('G' . $row, ucfirst($siswa->status ?? '-'));
            $sheet->setCellValue('H' . $row, $siswa->tempat_lahir ?? '-');
            $sheet->setCellValue('I' . $row, $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d/m/Y') : '-');
            $sheet->setCellValue('J' . $row, $siswa->no_hp ?? '-');
            $sheet->setCellValue('K' . $row, $siswa->alamat ?? '-');
            $sheet->getStyle('A' . $row . ':K' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Laporan_Siswa_' . now()->format('Ymd_His') . '.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    // ─────────────────────────────────────────────────
    //  LAPORAN GURU
    // ─────────────────────────────────────────────────

    public function guru(Request $request)
    {
        $query = Guru::with(['kelas'])->orderBy('nama');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('nama', 'like', "%{$q}%")
                   ->orWhere('nip', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $gurus = $query->get();

        // Rekap kehadiran per guru
        $kehadiranRekap = Kehadiran::whereNotNull('guru_id')
            ->selectRaw('guru_id,
                count(*) as total,
                sum(case when status in ("hadir","terlambat") then 1 else 0 end) as hadir,
                sum(case when status = "terlambat" then 1 else 0 end) as terlambat,
                sum(case when status = "sakit" then 1 else 0 end) as sakit,
                sum(case when status = "izin" then 1 else 0 end) as izin,
                sum(case when status = "alpha" then 1 else 0 end) as alpha')
            ->groupBy('guru_id')
            ->get()
            ->keyBy('guru_id');

        $rekap = [
            'total' => $gurus->count(),
        ];

        return view('pages.absensi.laporan-guru', compact('gurus', 'kehadiranRekap', 'rekap'));
    }

    public function exportGuruPdf(Request $request)
    {
        $query = Guru::with(['kelas'])->orderBy('nama');
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('nama', 'like', "%{$q}%")
                   ->orWhere('nip', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%");
            });
        }
        $gurus = $query->get();

        $kehadiranRekap = Kehadiran::whereNotNull('guru_id')
            ->selectRaw('guru_id, count(*) as total, sum(case when status in ("hadir","terlambat") then 1 else 0 end) as hadir, sum(case when status = "terlambat" then 1 else 0 end) as terlambat')
            ->groupBy('guru_id')
            ->get()
            ->keyBy('guru_id');

        $logoPath = public_path('absensi/media/logos/logo-sekolah.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';

        return view('pages.absensi.laporan-guru-pdf', compact('gurus', 'kehadiranRekap', 'logoBase64'));
    }

    public function exportGuruExcel(Request $request)
    {
        $query = Guru::with(['kelas'])->orderBy('nama');
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('nama', 'like', "%{$q}%")
                   ->orWhere('nip', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%");
            });
        }
        $gurus = $query->get();

        $kehadiranRekap = Kehadiran::whereNotNull('guru_id')
            ->selectRaw('guru_id, count(*) as total, sum(case when status in ("hadir","terlambat") then 1 else 0 end) as hadir, sum(case when status = "terlambat" then 1 else 0 end) as terlambat, sum(case when status = "sakit" then 1 else 0 end) as sakit, sum(case when status = "izin" then 1 else 0 end) as izin, sum(case when status = "alpha" then 1 else 0 end) as alpha')
            ->groupBy('guru_id')
            ->get()
            ->keyBy('guru_id');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Guru');

        // Add Logo
        $logoPath = public_path('absensi/media/logos/logo-sekolah.png');
        if (file_exists($logoPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Logo Sekolah');
            $drawing->setPath($logoPath);
            $drawing->setHeight(65);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(10);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
        }

        // Kop Surat Headers
        $sheet->setCellValue('C1', 'PEMERINTAH PROVINSI JAWA BARAT');
        $sheet->setCellValue('C2', 'DINAS PENDIDIKAN');
        $sheet->setCellValue('C3', 'CABANG DINAS PENDIDIKAN WILAYAH VIII');
        $sheet->setCellValue('C4', 'SEKOLAH MENENGAH ATAS NEGERI 1 CIPARAY');
        $sheet->setCellValue('C5', 'Jl. Raya Pacet Nomor 188 Telepon (022) 5950861');
        $sheet->setCellValue('C6', 'Fax. (022) 5955862 Website: www.sman1ciparay.sch.id e-mail: smansatoeciparay@gmail.com');
        $sheet->setCellValue('C7', 'Ciparay Kabupaten Bandung - 40381');

        $sheet->mergeCells('C1:L1');
        $sheet->mergeCells('C2:L2');
        $sheet->mergeCells('C3:L3');
        $sheet->mergeCells('C4:L4');
        $sheet->mergeCells('C5:L5');
        $sheet->mergeCells('C6:L6');
        $sheet->mergeCells('C7:L7');

        $sheet->getStyle('C1:L3')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('C4:L4')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('C5:L6')->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle('C7:L7')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('C1:L7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Double Border
        $sheet->getStyle('A7:L7')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);

        // Title & Filter Meta
        $sheet->setCellValue('A9', 'LAPORAN DATA GURU');
        $sheet->mergeCells('A9:L9');
        $sheet->getStyle('A9')->getFont()->setBold(true)->setSize(13)->setUnderline(true);
        $sheet->getStyle('A9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A11', 'Total Guru: ' . $gurus->count() . ' Orang  |  Dicetak: ' . now()->translatedFormat('d F Y, H:i'));
        $sheet->mergeCells('A11:L11');
        $sheet->getStyle('A11')->getFont()->setBold(true)->setSize(10);

        // Table Header
        $headers = ['No', 'Nama Guru', 'NIP', 'Email', 'No. HP', 'Kelas Wali', 'Total Kehadiran', 'Hadir', 'Terlambat', 'Sakit', 'Izin', 'Alpha'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '13', $header);
            $col++;
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7239EA']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];
        $sheet->getStyle('A13:L13')->applyFromArray($headerStyle);
        $sheet->getRowDimension(13)->setRowHeight(25);

        $row = 14;
        foreach ($gurus as $i => $guru) {
            $rekap = $kehadiranRekap->get($guru->id);
            $kelasWali = $guru->kelas->first();
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $guru->nama);
            $sheet->setCellValue('C' . $row, $guru->nip ?? '-');
            $sheet->setCellValue('D' . $row, $guru->email ?? '-');
            $sheet->setCellValue('E' . $row, $guru->no_hp ?? '-');
            $sheet->setCellValue('F' . $row, $kelasWali?->nama ?? '-');
            $sheet->setCellValue('G' . $row, $rekap?->total ?? 0);
            $sheet->setCellValue('H' . $row, $rekap?->hadir ?? 0);
            $sheet->setCellValue('I' . $row, $rekap?->terlambat ?? 0);
            $sheet->setCellValue('J' . $row, $rekap?->sakit ?? 0);
            $sheet->setCellValue('K' . $row, $rekap?->izin ?? 0);
            $sheet->setCellValue('L' . $row, $rekap?->alpha ?? 0);
            $sheet->getStyle('A' . $row . ':L' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            foreach (['G', 'H', 'I', 'J', 'K', 'L'] as $c) {
                $sheet->getStyle($c . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            $row++;
        }

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Laporan_Guru_' . now()->format('Ymd_His') . '.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    // ─────────────────────────────────────────────────
    //  LAPORAN KEHADIRAN
    // ─────────────────────────────────────────────────

    public function kehadiran(Request $request)
    {
        $kelas     = Kelas::where('status', 'aktif')->orderBy('tingkat')->orderBy('nama')->get();
        $semesters = Semester::with('tahunAjaran')->orderByDesc('id')->get();
        $today     = Carbon::today()->toDateString();

        $startDate = $request->input('start_date', $today);
        $endDate   = $request->input('end_date', $today);

        $query = Kehadiran::with(['siswa.kelas'])
            ->whereNotNull('siswa_id')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_masuk', 'desc');

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', fn($q) => $q->where('kelas_id', $request->kelas_id));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $kehadirans = $query->get();

        $rekap = [
            'total'      => $kehadirans->count(),
            'hadir'      => $kehadirans->whereIn('status', ['hadir', 'terlambat'])->count(),
            'terlambat'  => $kehadirans->where('status', 'terlambat')->count(),
            'sakit'      => $kehadirans->where('status', 'sakit')->count(),
            'izin'       => $kehadirans->where('status', 'izin')->count(),
            'alpha'      => $kehadirans->where('status', 'alpha')->count(),
        ];

        return view('pages.absensi.laporan-kehadiran', compact('kehadirans', 'kelas', 'semesters', 'rekap', 'startDate', 'endDate'));
    }

    public function exportKehadiranPdf(Request $request)
    {
        $today     = Carbon::today()->toDateString();
        $startDate = $request->input('start_date', $today);
        $endDate   = $request->input('end_date', $today);

        $query = Kehadiran::with(['siswa.kelas'])
            ->whereNotNull('siswa_id')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc');

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', fn($q) => $q->where('kelas_id', $request->kelas_id));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $kehadirans = $query->get();
        $kelasFilter = $request->filled('kelas_id')
            ? Kelas::find($request->kelas_id)?->nama ?? 'Semua Kelas'
            : 'Semua Kelas';
        $statusFilter = $request->filled('status') ? ucfirst($request->status) : 'Semua Status';

        $rekap = [
            'total'     => $kehadirans->count(),
            'hadir'     => $kehadirans->whereIn('status', ['hadir', 'terlambat'])->count(),
            'terlambat' => $kehadirans->where('status', 'terlambat')->count(),
            'sakit'     => $kehadirans->where('status', 'sakit')->count(),
            'izin'      => $kehadirans->where('status', 'izin')->count(),
            'alpha'     => $kehadirans->where('status', 'alpha')->count(),
        ];

        $logoPath = public_path('absensi/media/logos/logo-sekolah.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';

        return view('pages.absensi.laporan-kehadiran-pdf', compact(
            'kehadirans', 'startDate', 'endDate', 'kelasFilter', 'statusFilter', 'rekap', 'logoBase64'
        ));
    }

    public function exportKehadiranExcel(Request $request)
    {
        $today     = Carbon::today()->toDateString();
        $startDate = $request->input('start_date', $today);
        $endDate   = $request->input('end_date', $today);

        $query = Kehadiran::with(['siswa.kelas'])
            ->whereNotNull('siswa_id')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc');

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', fn($q) => $q->where('kelas_id', $request->kelas_id));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $kehadirans = $query->get();
        $kelasFilter = $request->filled('kelas_id')
            ? Kelas::find($request->kelas_id)?->nama ?? 'Semua Kelas'
            : 'Semua Kelas';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Kehadiran');

        // Add Logo
        $logoPath = public_path('absensi/media/logos/logo-sekolah.png');
        if (file_exists($logoPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Logo Sekolah');
            $drawing->setPath($logoPath);
            $drawing->setHeight(65);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(10);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
        }

        // Kop Surat Headers
        $sheet->setCellValue('C1', 'PEMERINTAH PROVINSI JAWA BARAT');
        $sheet->setCellValue('C2', 'DINAS PENDIDIKAN');
        $sheet->setCellValue('C3', 'CABANG DINAS PENDIDIKAN WILAYAH VIII');
        $sheet->setCellValue('C4', 'SEKOLAH MENENGAH ATAS NEGERI 1 CIPARAY');
        $sheet->setCellValue('C5', 'Jl. Raya Pacet Nomor 188 Telepon (022) 5950861');
        $sheet->setCellValue('C6', 'Fax. (022) 5955862 Website: www.sman1ciparay.sch.id e-mail: smansatoeciparay@gmail.com');
        $sheet->setCellValue('C7', 'Ciparay Kabupaten Bandung - 40381');

        $sheet->mergeCells('C1:I1');
        $sheet->mergeCells('C2:I2');
        $sheet->mergeCells('C3:I3');
        $sheet->mergeCells('C4:I4');
        $sheet->mergeCells('C5:I5');
        $sheet->mergeCells('C6:I6');
        $sheet->mergeCells('C7:I7');

        $sheet->getStyle('C1:I3')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('C4:I4')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('C5:I6')->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle('C7:I7')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('C1:I7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Double Border
        $sheet->getStyle('A7:I7')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);

        // Title & Filter Meta
        $sheet->setCellValue('A9', 'LAPORAN KEHADIRAN SISWA');
        $sheet->mergeCells('A9:I9');
        $sheet->getStyle('A9')->getFont()->setBold(true)->setSize(13)->setUnderline(true);
        $sheet->getStyle('A9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A11', 'Periode: ' . Carbon::parse($startDate)->translatedFormat('d F Y') . ' s.d. ' . Carbon::parse($endDate)->translatedFormat('d F Y') . '  |  Kelas: ' . $kelasFilter . '  |  Dicetak: ' . now()->translatedFormat('d F Y, H:i'));
        $sheet->mergeCells('A11:I11');
        $sheet->getStyle('A11')->getFont()->setBold(true)->setSize(10);

        // Table Header
        $headers = ['No', 'Tanggal', 'Nama Siswa', 'NIS', 'Kelas', 'Status', 'Jam Masuk', 'Jam Pulang', 'Keterangan'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '13', $header);
            $col++;
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '50CD89']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];
        $sheet->getStyle('A13:I13')->applyFromArray($headerStyle);
        $sheet->getRowDimension(13)->setRowHeight(25);

        $statusLabels = [
            'hadir' => 'Hadir', 'terlambat' => 'Terlambat',
            'sakit' => 'Sakit', 'izin' => 'Izin', 'alpha' => 'Alpha',
        ];

        $row = 14;
        foreach ($kehadirans as $i => $k) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $k->tanggal ? Carbon::parse($k->tanggal)->format('d/m/Y') : '-');
            $sheet->setCellValue('C' . $row, $k->siswa?->nama ?? '-');
            $sheet->setCellValue('D' . $row, $k->siswa?->nis ?? '-');
            $sheet->setCellValue('E' . $row, $k->siswa?->kelas?->nama ?? '-');
            $sheet->setCellValue('F' . $row, $statusLabels[$k->status] ?? ucfirst($k->status));
            $sheet->setCellValue('G' . $row, $k->jam_masuk ? Carbon::parse($k->jam_masuk)->format('H:i') : '-');
            $sheet->setCellValue('H' . $row, $k->jam_pulang ? Carbon::parse($k->jam_pulang)->format('H:i') : '-');
            $sheet->setCellValue('I' . $row, $k->keterangan ?? '-');
            $sheet->getStyle('A' . $row . ':I' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Laporan_Kehadiran_' . now()->format('Ymd_His') . '.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    // ─────────────────────────────────────────────────
    //  LAPORAN PENGADUAN
    // ─────────────────────────────────────────────────

    public function pengaduan(Request $request)
    {
        $kelas = Kelas::where('status', 'aktif')->orderBy('tingkat')->orderBy('nama')->get();
        $today = Carbon::today()->toDateString();
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', $today);

        $query = Pengaduan::with(['siswa.kelas'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc');

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', fn($q) => $q->where('kelas_id', $request->kelas_id));
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->whereHas('siswa', fn($sq) => $sq->where('nama', 'like', "%{$q}%"));
        }

        $pengaduans = $query->get();

        $rekap = [
            'total'  => $pengaduans->count(),
        ];

        return view('pages.absensi.laporan-pengaduan', compact('pengaduans', 'kelas', 'rekap', 'startDate', 'endDate'));
    }

    public function exportPengaduanPdf(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', $today);

        $query = Pengaduan::with(['siswa.kelas'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc');

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', fn($q) => $q->where('kelas_id', $request->kelas_id));
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->whereHas('siswa', fn($sq) => $sq->where('nama', 'like', "%{$q}%"));
        }

        $pengaduans = $query->get();
        $kelasFilter = $request->filled('kelas_id')
            ? Kelas::find($request->kelas_id)?->nama ?? 'Semua Kelas'
            : 'Semua Kelas';

        $logoPath = public_path('absensi/media/logos/logo-sekolah.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';

        return view('pages.absensi.laporan-pengaduan-pdf', compact('pengaduans', 'startDate', 'endDate', 'kelasFilter', 'logoBase64'));
    }

    public function exportPengaduanExcel(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', $today);

        $query = Pengaduan::with(['siswa.kelas'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc');

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', fn($q) => $q->where('kelas_id', $request->kelas_id));
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->whereHas('siswa', fn($sq) => $sq->where('nama', 'like', "%{$q}%"));
        }

        $pengaduans = $query->get();
        $kelasFilter = $request->filled('kelas_id')
            ? Kelas::find($request->kelas_id)?->nama ?? 'Semua Kelas'
            : 'Semua Kelas';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pengaduan');

        // Add Logo
        $logoPath = public_path('absensi/media/logos/logo-sekolah.png');
        if (file_exists($logoPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Logo Sekolah');
            $drawing->setPath($logoPath);
            $drawing->setHeight(65);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(10);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
        }

        // Kop Surat Headers
        $sheet->setCellValue('C1', 'PEMERINTAH PROVINSI JAWA BARAT');
        $sheet->setCellValue('C2', 'DINAS PENDIDIKAN');
        $sheet->setCellValue('C3', 'CABANG DINAS PENDIDIKAN WILAYAH VIII');
        $sheet->setCellValue('C4', 'SEKOLAH MENENGAH ATAS NEGERI 1 CIPARAY');
        $sheet->setCellValue('C5', 'Jl. Raya Pacet Nomor 188 Telepon (022) 5950861');
        $sheet->setCellValue('C6', 'Fax. (022) 5955862 Website: www.sman1ciparay.sch.id e-mail: smansatoeciparay@gmail.com');
        $sheet->setCellValue('C7', 'Ciparay Kabupaten Bandung - 40381');

        $sheet->mergeCells('C1:F1');
        $sheet->mergeCells('C2:F2');
        $sheet->mergeCells('C3:F3');
        $sheet->mergeCells('C4:F4');
        $sheet->mergeCells('C5:F5');
        $sheet->mergeCells('C6:F6');
        $sheet->mergeCells('C7:F7');

        $sheet->getStyle('C1:F3')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('C4:F4')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('C5:F6')->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle('C7:F7')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('C1:F7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Double Border
        $sheet->getStyle('A7:F7')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);

        // Title & Filter Meta
        $sheet->setCellValue('A9', 'LAPORAN PENGADUAN SISWA');
        $sheet->mergeCells('A9:F9');
        $sheet->getStyle('A9')->getFont()->setBold(true)->setSize(13)->setUnderline(true);
        $sheet->getStyle('A9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A11', 'Periode: ' . Carbon::parse($startDate)->translatedFormat('d F Y') . ' s.d. ' . Carbon::parse($endDate)->translatedFormat('d F Y') . '  |  Kelas: ' . $kelasFilter . '  |  Dicetak: ' . now()->translatedFormat('d F Y, H:i'));
        $sheet->mergeCells('A11:F11');
        $sheet->getStyle('A11')->getFont()->setBold(true)->setSize(10);

        // Table Header
        $headers = ['No', 'Tanggal', 'Nama Siswa', 'NIS', 'Kelas', 'Deskripsi Pengaduan'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '13', $header);
            $col++;
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1416C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];
        $sheet->getStyle('A13:F13')->applyFromArray($headerStyle);
        $sheet->getRowDimension(13)->setRowHeight(25);

        $row = 14;
        foreach ($pengaduans as $i => $p) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $p->tanggal ? Carbon::parse($p->tanggal)->translatedFormat('d F Y') : '-');
            $sheet->setCellValue('C' . $row, $p->siswa?->nama ?? '-');
            $sheet->setCellValue('D' . $row, $p->siswa?->nis ?? '-');
            $sheet->setCellValue('E' . $row, $p->siswa?->kelas?->nama ?? '-');
            $sheet->setCellValue('F' . $row, $p->deskripsi ?? '-');
            $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setWrapText(true);
            $row++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('F')->setWidth(50);

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Laporan_Pengaduan_' . now()->format('Ymd_His') . '.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
