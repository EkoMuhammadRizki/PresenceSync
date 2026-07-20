<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Pengaduan;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GuruDashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Guru (Wali Kelas).
     */
    public function index()
    {
        $user = auth()->user();
        $guru = Guru::where('user_id', $user->id)->first();

        if (!$guru) {
            abort(403, 'Anda tidak terdaftar sebagai guru.');
        }

        $today = Carbon::today()->toDateString();

        // Kelas yang diampu (wali kelas)
        $kelasWali = Kelas::where('guru_id', $guru->id)->where('status', 'aktif')->get();

        // Statistik hari ini untuk semua kelas wali
        $kelasIds = $kelasWali->pluck('id');
        $siswaIds = Siswa::whereIn('kelas_id', $kelasIds)->pluck('id');
        $totalSiswa = $siswaIds->count();

        $kehadiranHariIni = Kehadiran::whereIn('siswa_id', $siswaIds)
            ->whereDate('tanggal', $today)
            ->get();

        $hadirHariIni = $kehadiranHariIni->whereIn('status', ['hadir', 'terlambat'])->count();
        $izinSakitHariIni = $kehadiranHariIni->whereIn('status', ['izin', 'sakit'])->count();
        $alphaHariIni = $totalSiswa - $hadirHariIni - $izinSakitHariIni;

        // Detail kehadiran per kelas
        $kelasDetail = [];
        foreach ($kelasWali as $kelas) {
            $siswaDiKelas = Siswa::where('kelas_id', $kelas->id)->get();
            $siswaIdKelas = $siswaDiKelas->pluck('id');

            $kehadiranKelas = Kehadiran::with('siswa')
                ->whereIn('siswa_id', $siswaIdKelas)
                ->whereDate('tanggal', $today)
                ->get()
                ->keyBy('siswa_id');

            $dataSiswa = [];
            foreach ($siswaDiKelas as $siswa) {
                $kh = $kehadiranKelas->get($siswa->id);
                $dataSiswa[] = [
                    'siswa'  => $siswa,
                    'status' => $kh ? $kh->status : 'belum_absen',
                    'jam_masuk' => $kh ? $kh->jam_masuk : null,
                    'keterangan' => $kh ? $kh->keterangan : null,
                ];
            }

            $kelasDetail[] = [
                'kelas' => $kelas,
                'siswa' => $dataSiswa,
                'total' => count($siswaDiKelas),
                'hadir' => collect($dataSiswa)->whereIn('status', ['hadir', 'terlambat'])->count(),
            ];
        }

        return view('pages.absensi.guru-dashboard', compact(
            'guru', 'totalSiswa', 'hadirHariIni', 'izinSakitHariIni', 'alphaHariIni', 'kelasDetail'
        ));
    }

    /**
     * Tampilkan Halaman Pengaduan Siswa Kelas Wali (untuk Guru).
     */
    public function pengaduan(Request $request)
    {
        $user = auth()->user();
        $guru = Guru::where('user_id', $user->id)->first();

        if (!$guru) {
            abort(403, 'Anda tidak terdaftar sebagai guru.');
        }

        $kelas = Kelas::where('guru_id', $guru->id)->where('status', 'aktif')->first();

        if (!$kelas) {
            $records = collect();
        } else {
            $siswaIds = Siswa::where('kelas_id', $kelas->id)->pluck('id');
            $query = Pengaduan::with('siswa')->whereIn('siswa_id', $siswaIds);

            if ($request->filled('tanggal_range')) {
                $dates = explode(' hingga ', $request->tanggal_range);
                if (count($dates) === 2) {
                    $query->whereBetween('tanggal', [$dates[0], $dates[1]]);
                } else {
                    $query->where('tanggal', $dates[0]);
                }
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('siswa', function($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%');
                });
            }

            $records = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->get();
        }

        return view('pages.absensi.guru-pengaduan', compact('guru', 'kelas', 'records'));
    }

    /**
     * Ekspor Laporan Pengaduan Siswa ke Excel.
     */
    public function exportPengaduan(Request $request)
    {
        $user = auth()->user();
        $guru = Guru::where('user_id', $user->id)->first();

        if (!$guru) {
            abort(403, 'Anda tidak terdaftar sebagai guru.');
        }

        $kelas = Kelas::where('guru_id', $guru->id)->where('status', 'aktif')->first();

        if (!$kelas) {
            abort(404, 'Anda tidak memiliki kelas wali aktif.');
        }

        $siswaIds = Siswa::where('kelas_id', $kelas->id)->pluck('id');
        $query = Pengaduan::with('siswa')->whereIn('siswa_id', $siswaIds);

        if ($request->filled('tanggal_range')) {
            $dates = explode(' hingga ', $request->tanggal_range);
            if (count($dates) === 2) {
                $query->whereBetween('tanggal', [$dates[0], $dates[1]]);
            } else {
                $query->where('tanggal', $dates[0]);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            });
        }

        $records = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pengaduan Siswa Kelas ' . $kelas->tingkat . ' ' . $kelas->nama);

        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'NIS');
        $sheet->setCellValue('C1', 'Nama Siswa');
        $sheet->setCellValue('D1', 'Deskripsi Pengaduan');
        $sheet->setCellValue('E1', 'Tanggal');
        $sheet->setCellValue('F1', 'Tanggal Submit');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3E97FF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]
            ]
        ];

        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Data
        $rowNum = 2;
        foreach ($records as $index => $row) {
            $sheet->setCellValue('A' . $rowNum, $index + 1);
            $sheet->setCellValue('B' . $rowNum, $row->siswa->nis ?? '-');
            $sheet->setCellValue('C' . $rowNum, $row->siswa->nama ?? '-');
            $sheet->setCellValue('D' . $rowNum, $row->deskripsi);
            $sheet->setCellValue('E' . $rowNum, $row->tanggal->format('d-m-Y'));
            $sheet->setCellValue('F' . $rowNum, $row->created_at->format('d-m-Y H:i:s'));

            $sheet->getStyle('A' . $rowNum . ':F' . $rowNum)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('DDDDDD');
            $rowNum++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $filename = 'Laporan_Pengaduan_Kelas_' . str_replace(' ', '_', $kelas->tingkat . '_' . $kelas->nama) . '.xlsx';
        
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
