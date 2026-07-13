<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\AturanJam;
use App\Models\Kehadiran;
use App\Models\Semester;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SiswaDashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Kehadiran Siswa.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $siswa = Siswa::with('kelas')->where('user_id', $user->id)->first();

        if (!$siswa) {
            abort(403, 'Anda tidak terdaftar sebagai siswa.');
        }

        // Ambil riwayat kehadiran siswa ini
        $kehadirans = Kehadiran::with('aturanJam')
            ->where('siswa_id', $siswa->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Cek apakah siswa sudah melakukan presensi hari ini
        $today = Carbon::today()->toDateString();
        $hasCheckedInToday = Kehadiran::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $today)
            ->exists();

        // Dapatkan data kehadiran hari ini jika ada
        $kehadiranHariIni = Kehadiran::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $today)
            ->first();

        // SIMPEG Monthly grid logic
        $periode = $request->input('periode', date('Ym'));
        $data = $this->buildAttendanceData($siswa->id, $periode);
        $attendanceRows = $data['rows'];
        $daysInMonth = $data['daysInMonth'];

        // Dropdown data for forms
        $activeSemester = Semester::where('status', 'aktif')->first() ?? Semester::latest()->first();
        $semesters = Semester::with('tahunAjaran')->latest()->get();
        $aturanJams = AturanJam::aktif()->get();

        return view('pages.absensi.siswa-dashboard', compact(
            'siswa',
            'kehadirans',
            'hasCheckedInToday',
            'kehadiranHariIni',
            'periode',
            'attendanceRows',
            'daysInMonth',
            'activeSemester',
            'semesters',
            'aturanJams'
        ));
    }

    /**
     * Proses Presensi (Hadir/Masuk).
     */
    public function presensi(Request $request)
    {
        $user = auth()->user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            abort(403, 'Anda tidak terdaftar sebagai siswa.');
        }

        $today = Carbon::today()->toDateString();

        // Cek double submit
        $exists = Kehadiran::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $today)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Anda sudah melakukan presensi hari ini.');
        }

        // Validasi foto dan koordinat wajib ada
        $request->validate([
            'foto_base64' => 'required|string',
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
        ], [
            'foto_base64.required' => 'Foto kehadiran wajib diambil.',
            'latitude.required'    => 'Lokasi GPS wajib diaktifkan.',
        ]);

        $dayOfWeek = now()->format('l');
        $daysMap = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        $hariIni = $daysMap[$dayOfWeek] ?? 'Senin';

        // Cari Aturan Jam
        $aturan = AturanJam::where('hari', $hariIni)->where('is_aktif', true)->first()
            ?? AturanJam::where('is_aktif', true)->first();

        if (!$aturan) {
            return redirect()->back()->with('error', 'Aturan jam masuk sekolah aktif tidak ditemukan.');
        }

        // Cari Semester Aktif
        $activeSemester = Semester::where('status', 'aktif')->first();
        if (!$activeSemester) {
            return redirect()->back()->with('error', 'Semester aktif tidak ditemukan.');
        }

        // Hitung status: tepat/terlambat
        $limitTime = Carbon::createFromFormat('H:i:s', $aturan->jam_masuk)
            ->addMinutes($aturan->toleransi_keterlambatan);

        $now = Carbon::now();
        $currentTimeString = $now->toTimeString();

        $status = 'hadir';
        if ($now->format('H:i:s') > $limitTime->format('H:i:s')) {
            $status = 'terlambat';
        }

        // Simpan foto base64 sebagai file jika kolom tersedia
        $fotoPath = null;
        $fotoBase64 = $request->input('foto_base64');
        if ($fotoBase64 && str_starts_with($fotoBase64, 'data:image')) {
            $imageData = explode(',', $fotoBase64);
            $image = base64_decode($imageData[1] ?? '');
            if ($image) {
                $filename = 'presensi_' . $siswa->id . '_' . $today . '.jpg';
                $path = storage_path('app/public/presensi/' . $filename);
                if (!file_exists(dirname($path))) {
                    mkdir(dirname($path), 0755, true);
                }
                file_put_contents($path, $image);
                $fotoPath = 'presensi/' . $filename;
            }
        }

        $koordinat = $request->latitude . ',' . $request->longitude;

        Kehadiran::create([
            'siswa_id'      => $siswa->id,
            'semester_id'   => $activeSemester->id,
            'aturan_jam_id' => $aturan->id,
            'tanggal'       => $today,
            'jam_masuk'     => $currentTimeString,
            'status'        => $status,
            'foto'          => $fotoPath,
            'koordinat'     => $koordinat,
        ]);

        $message = $status === 'hadir' ? 'Presensi berhasil! Anda masuk tepat waktu.' : 'Presensi berhasil! Anda tercatat terlambat.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Proses Pengajuan Izin / Sakit.
     */
    public function izin(Request $request)
    {
        $request->validate([
            'status'      => 'required|in:izin,sakit',
            'keterangan'  => 'required|string|max:500',
            'foto_base64' => 'required|string',
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
        ], [
            'status.required'     => 'Jenis izin wajib dipilih.',
            'keterangan.required' => 'Alasan izin wajib diisi.',
            'keterangan.max'      => 'Alasan izin maksimal 500 karakter.',
            'foto_base64.required' => 'Foto bukti wajib diambil.',
            'latitude.required'    => 'Lokasi GPS wajib diaktifkan.',
        ]);

        $user = auth()->user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            abort(403, 'Anda tidak terdaftar sebagai siswa.');
        }

        $today = Carbon::today()->toDateString();

        // Cek double submit
        $exists = Kehadiran::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $today)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Anda sudah mengisi kehadiran/izin hari ini.');
        }

        $dayOfWeek = now()->format('l');
        $daysMap = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];
        $hariIni = $daysMap[$dayOfWeek] ?? 'Senin';

        // Cari Aturan Jam
        $aturan = AturanJam::where('hari', $hariIni)->where('is_aktif', true)->first()
            ?? AturanJam::where('is_aktif', true)->first();

        // Cari Semester Aktif
        $activeSemester = Semester::where('status', 'aktif')->first();
        if (!$activeSemester) {
            return redirect()->back()->with('error', 'Semester aktif tidak ditemukan.');
        }

        // Simpan foto base64 sebagai file
        $fotoPath = null;
        $fotoBase64 = $request->input('foto_base64');
        if ($fotoBase64 && str_starts_with($fotoBase64, 'data:image')) {
            $imageData = explode(',', $fotoBase64);
            $image = base64_decode($imageData[1] ?? '');
            if ($image) {
                $filename = 'izin_' . $siswa->id . '_' . $today . '.jpg';
                $path = storage_path('app/public/presensi/' . $filename);
                if (!file_exists(dirname($path))) {
                    mkdir(dirname($path), 0755, true);
                }
                file_put_contents($path, $image);
                $fotoPath = 'presensi/' . $filename;
            }
        }

        $koordinat = $request->latitude . ',' . $request->longitude;

        Kehadiran::create([
            'siswa_id'      => $siswa->id,
            'semester_id'   => $activeSemester->id,
            'aturan_jam_id' => $aturan ? $aturan->id : null,
            'tanggal'       => $today,
            'status'        => $request->status,
            'keterangan'    => $request->keterangan,
            'foto'          => $fotoPath,
            'koordinat'     => $koordinat,
        ]);

        $statusText = $request->status === 'sakit' ? 'Sakit' : 'Izin';

        return redirect()->back()->with('success', "Pengajuan {$statusText} berhasil disimpan.");
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $siswa = Siswa::with('kelas')->where('user_id', $user->id)->first();

        if (!$siswa) {
            abort(403, 'Anda tidak terdaftar sebagai siswa.');
        }

        $periode = $request->input('periode', date('Ym'));
        $data = $this->buildAttendanceData($siswa->id, $periode);
        $attendanceRows = $data['rows'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Kehadiran');

        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'NISN');
        $sheet->setCellValue('C1', 'Nama');
        $sheet->setCellValue('D1', 'Tanggal');
        $sheet->setCellValue('E1', 'Msk/Lbr');
        $sheet->setCellValue('F1', 'Masuk Jam');
        $sheet->setCellValue('G1', 'Keterangan');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3E97FF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]
            ]
        ];

        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Data Row Rendering
        $rowNum = 2;
        foreach ($attendanceRows as $row) {
            $sheet->setCellValue('A' . $rowNum, $row['no']);
            $sheet->setCellValue('B' . $rowNum, $row['nisn']);
            $sheet->setCellValue('C' . $rowNum, $row['nama']);
            $sheet->setCellValue('D' . $rowNum, $row['tanggal']);
            $sheet->setCellValue('E' . $rowNum, $row['msk_lbr']);
            $sheet->setCellValue('F' . $rowNum, $row['msk_jam']);
            $sheet->setCellValue('G' . $rowNum, $row['keterangan']);

            $sheet->getStyle('A' . $rowNum . ':G' . $rowNum)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('DDDDDD');

            if ($row['is_libur']) {
                $sheet->getStyle('A' . $rowNum . ':G' . $rowNum)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFF0F0');
                $sheet->getStyle('A' . $rowNum . ':G' . $rowNum)->getFont()->getColor()->setRGB('A0A0A0');
            }

            $rowNum++;
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $filename = 'Laporan_Kehadiran_' . str_replace(' ', '_', $siswa->nama) . '_' . $periode . '.xlsx';
        
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    private function buildAttendanceData($siswaId, $periode)
    {
        $year = substr($periode, 0, 4);
        $month = substr($periode, 4, 2);
        
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        $siswa = Siswa::with('kelas')->find($siswaId);
        if (!$siswa) {
            return [
                'siswa' => null,
                'rows' => [],
                'daysInMonth' => $daysInMonth
            ];
        }

        $aturanJam = AturanJam::where('is_aktif', true)->first();

        $kehadirans = Kehadiran::where('siswa_id', $siswa->id)
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->tanggal)->format('Y-m-d');
            });

        $rows = [];
        $hariMap = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $startDate->copy()->addDays($day - 1);
            $dateStr = $date->format('Y-m-d');
            $dayOfWeek = $date->dayOfWeek;
            
            $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
            $hariLabel = $hariMap[$dayOfWeek];
            $tanggalLabel = $hariLabel . ', ' . $date->format('d-m-Y');

            $row = [
                'no' => $day,
                'nisn' => $siswa->nisn ?? $siswa->nis ?? '-',
                'nama' => $siswa->nama,
                'tanggal' => $tanggalLabel,
                'tanggal_raw' => $dateStr,
                'is_libur' => $isWeekend,
                'msk_lbr' => '',
                'msk_jam' => '',
                'keterangan' => '',
                'status' => ''
            ];

            if ($isWeekend) {
                $row['msk_lbr'] = '✗';
                $row['keterangan'] = 'Libur';
            } else {
                $record = $kehadirans->get($dateStr);
                
                if ($record) {
                    $row['status'] = $record->status;
                    
                    if (in_array($record->status, ['hadir', 'terlambat'])) {
                        $row['msk_lbr'] = '✓';
                        $row['msk_jam'] = $record->jam_masuk ? Carbon::parse($record->jam_masuk)->format('H:i:s') : '';

                        if ($record->status === 'terlambat') {
                            $row['keterangan'] = 'Terlambat';
                        } else {
                            $row['keterangan'] = 'Tepat Waktu';
                        }
                    } else if ($record->status === 'sakit') {
                        $row['msk_lbr'] = '✗';
                        $row['keterangan'] = 'Sakit';
                    } else if ($record->status === 'izin') {
                        $row['msk_lbr'] = '✗';
                        $row['keterangan'] = 'Izin';
                    } else {
                        $row['msk_lbr'] = '✗';
                        $row['keterangan'] = 'Alpha';
                    }
                } else {
                    $row['msk_lbr'] = '✗';
                    $row['keterangan'] = 'Alpha';
                }
            }

            $rows[] = $row;
        }

        return [
            'siswa' => $siswa,
            'rows' => $rows,
            'daysInMonth' => $daysInMonth
        ];
    }

}
