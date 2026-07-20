<?php
 
namespace App\Http\Controllers\Absensi;
 
use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Dompdf\Dompdf;
use Dompdf\Options;
 
class GuruKelasController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $guru = Guru::where('user_id', $user->id)->first();

        if (!$guru) {
            abort(403, 'Anda tidak terdaftar sebagai guru.');
        }

        $kelas = Kelas::with('siswas')
            ->where('guru_id', $guru->id)
            ->where('status', 'aktif')
            ->first();

        if (!$kelas) {
            return view('pages.absensi.guru-kelas-wali', [
                'guru' => $guru,
                'kelas' => null,
                'siswas' => collect(),
                'kehadirans' => collect(),
                'rekap' => [],
            ]);
        }

        $siswas = $kelas->siswas()->orderBy('nama')->get();

        $kehadirans = Kehadiran::with('siswa')
            ->whereIn('siswa_id', $siswas->pluck('id'))
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_masuk', 'desc')
            ->paginate(50);

        $rekap = [];
        $totalHari = Kehadiran::whereIn('siswa_id', $siswas->pluck('id'))
            ->selectRaw('siswa_id, count(*) as total, sum(case when status in ("hadir","terlambat") then 1 else 0 end) as hadir')
            ->groupBy('siswa_id')
            ->get()
            ->keyBy('siswa_id');

        foreach ($siswas as $siswa) {
            $data = $totalHari->get($siswa->id);
            $hadirCount = $data->hadir ?? 0;
            $totalCount = $data->total ?? 0;
            $persentase = $totalCount > 0 ? round(($hadirCount / $totalCount) * 100) : 0;
            $rekap[$siswa->id] = [
                'total' => $totalCount,
                'hadir' => $hadirCount,
                'persentase' => $persentase,
            ];
        }

        return view('pages.absensi.guru-kelas-wali', compact(
            'guru', 'kelas', 'siswas', 'kehadirans', 'rekap'
        ));
    }
 
    public function exportExcel()
    {
        $user = auth()->user();
        $guru = Guru::where('user_id', $user->id)->first();
 
        if (!$guru) {
            abort(403, 'Anda tidak terdaftar sebagai guru.');
        }
 
        $kelas = Kelas::with('siswas')
            ->where('guru_id', $guru->id)
            ->where('status', 'aktif')
            ->first();
 
        if (!$kelas) {
            abort(404, 'Anda tidak memiliki kelas wali aktif.');
        }
 
        $activeSemester = Semester::with('tahunAjaran')->where('status', 'aktif')->first()
            ?? Semester::with('tahunAjaran')->latest()->first();
 
        $siswas = $kelas->siswas()->orderBy('nama')->get();
 
        $rekap = [];
        $totalHari = Kehadiran::whereIn('siswa_id', $siswas->pluck('id'))
            ->selectRaw('siswa_id, count(*) as total, sum(case when status in ("hadir","terlambat") then 1 else 0 end) as hadir')
            ->groupBy('siswa_id')
            ->get()
            ->keyBy('siswa_id');
 
        foreach ($siswas as $siswa) {
            $data = $totalHari->get($siswa->id);
            $hadirCount = $data->hadir ?? 0;
            $totalCount = $data->total ?? 0;
            $persentase = $totalCount > 0 ? round(($hadirCount / $totalCount) * 100) : 0;
            $rekap[$siswa->id] = [
                'total' => $totalCount,
                'hadir' => $hadirCount,
                'persentase' => $persentase,
            ];
        }
 
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa Kelas Wali');
 
        // Header Title block
        $sheet->setCellValue('A1', 'LAPORAN DATA SISWA KELAS WALI');
        $sheet->setCellValue('A2', 'Kelas: ' . $kelas->nama_lengkap . ' (' . $kelas->tingkat . ' ' . $kelas->nama . ')');
        $sheet->setCellValue('A3', 'Wali Kelas: ' . $guru->nama);
        $sheet->setCellValue('A4', 'Tahun Ajaran: ' . ($activeSemester?->tahunAjaran?->nama ?? '-') . ' (Semester: ' . ($activeSemester ? ucfirst($activeSemester->jenis) : '-') . ')');
 
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A4')->getFont()->setBold(true);
 
        // Header table
        $sheet->setCellValue('A6', 'No');
        $sheet->setCellValue('B6', 'NISN');
        $sheet->setCellValue('C6', 'Nama Siswa');
        $sheet->setCellValue('D6', 'Jenis Kelamin');
        $sheet->setCellValue('E6', 'Kehadiran (%)');
 
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '10B981'] // hijau / green
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]
            ]
        ];
 
        $sheet->getStyle('A6:E6')->applyFromArray($headerStyle);
        $sheet->getRowDimension(6)->setRowHeight(25);
 
        // Data rendering
        $rowNum = 7;
        foreach ($siswas as $index => $row) {
            $pct = $rekap[$row->id]['persentase'] ?? 0;
            $sheet->setCellValue('A' . $rowNum, $index + 1);
            $sheet->setCellValue('B' . $rowNum, $row->nisn ?? '-');
            $sheet->setCellValue('C' . $rowNum, $row->nama ?? '-');
            $sheet->setCellValue('D' . $rowNum, $row->jenis_kelamin ?? '-');
            $sheet->setCellValue('E' . $rowNum, $pct . '%');
 
            // Alignment
            $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
 
            $sheet->getStyle('A' . $rowNum . ':E' . $rowNum)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('DDDDDD');
            $rowNum++;
        }
 
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
 
        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
 
        $filename = 'Laporan_Data_Siswa_Kelas_Wali_' . str_replace(' ', '_', $kelas->tingkat . '_' . $kelas->nama) . '.xlsx';
        
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');
 
        return $response;
    }
 
    public function exportPdf()
    {
        $user = auth()->user();
        $guru = Guru::where('user_id', $user->id)->first();
 
        if (!$guru) {
            abort(403, 'Anda tidak terdaftar sebagai guru.');
        }
 
        $kelas = Kelas::with('siswas')
            ->where('guru_id', $guru->id)
            ->where('status', 'aktif')
            ->first();
 
        if (!$kelas) {
            abort(404, 'Anda tidak memiliki kelas wali aktif.');
        }
 
        $siswas = $kelas->siswas()->orderBy('nama')->get();
 
        $rekap = [];
        $totalHari = Kehadiran::whereIn('siswa_id', $siswas->pluck('id'))
            ->selectRaw('siswa_id, count(*) as total, sum(case when status in ("hadir","terlambat") then 1 else 0 end) as hadir')
            ->groupBy('siswa_id')
            ->get()
            ->keyBy('siswa_id');
 
        foreach ($siswas as $siswa) {
            $data = $totalHari->get($siswa->id);
            $hadirCount = $data->hadir ?? 0;
            $totalCount = $data->total ?? 0;
            $persentase = $totalCount > 0 ? round(($hadirCount / $totalCount) * 100) : 0;
            $rekap[$siswa->id] = [
                'total' => $totalCount,
                'hadir' => $hadirCount,
                'persentase' => $persentase,
            ];
        }
 
        $html = view('pages.absensi.guru-kelas-wali-pdf', compact('guru', 'kelas', 'siswas', 'rekap'))->render();
 
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
 
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
 
        $filename = 'Laporan_Data_Siswa_Kelas_Wali_' . str_replace(' ', '_', $kelas->tingkat . '_' . $kelas->nama) . '.pdf';
 
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
