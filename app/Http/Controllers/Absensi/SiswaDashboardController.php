<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\AturanJam;
use App\Models\JadwalPelajaran;
use App\Models\Kehadiran;
use App\Models\KehadiranMataPelajaran;
use App\Models\KehadiranMataPelajaranDetail;
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

        if ($request->is('absensi/siswa/kehadiran')) {
            return view('pages.absensi.siswa-kehadiran', compact(
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

        if ($request->is('absensi/siswa/profil')) {
            return $this->profil();
        }

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
     * Tampilkan Form Edit Profil Orang Tua (untuk Siswa).
     */
    public function profil()
    {
        $user = auth()->user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            abort(403, 'Anda tidak terdaftar sebagai siswa.');
        }

        $parentId = $siswa->orang_tua_user_id ?? $user->id;
        $profile = \App\Models\ParentProfile::firstOrNew(['parent_user_id' => $parentId]);

        return view('pages.absensi.siswa-profil', compact('user', 'siswa', 'profile'));
    }

    /**
     * Update Profil Orang Tua (dari Siswa).
     */
    public function updateProfil(Request $request)
    {
        $user = auth()->user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            abort(403, 'Anda tidak terdaftar sebagai siswa.');
        }

        $request->validate([
            'nik_ayah'       => 'nullable|regex:/^[0-9]+$/|max:16',
            'nama_ayah'      => 'nullable|string|max:255',
            'pekerjaan_ayah' => 'nullable|string|max:100',
            'pendidikan_ayah'=> 'nullable|string|max:50',
            'no_hp_ayah'     => 'nullable|string|max:20',

            'nik_ibu'        => 'nullable|regex:/^[0-9]+$/|max:16',
            'nama_ibu'       => 'nullable|string|max:255',
            'pekerjaan_ibu'  => 'nullable|string|max:100',
            'pendidikan_ibu' => 'nullable|string|max:50',
            'no_hp_ibu'      => 'nullable|string|max:20',
        ], [
            'nik_ayah.regex' => 'NIK Ayah hanya boleh berisi angka.',
            'nik_ayah.max'   => 'NIK Ayah maksimal 16 digit.',
            'nik_ibu.regex'  => 'NIK Ibu hanya boleh berisi angka.',
            'nik_ibu.max'    => 'NIK Ibu maksimal 16 digit.',
        ]);

        $parentId = $siswa->orang_tua_user_id ?? $user->id;
        $profile = \App\Models\ParentProfile::firstOrNew(['parent_user_id' => $parentId]);
        $profile->fill($request->only([
            'nik_ayah', 'nama_ayah', 'pekerjaan_ayah', 'ket_pekerjaan_ayah',
            'pendidikan_ayah', 'alamat_ayah', 'no_hp_ayah', 'penghasilan_ayah',
            'nik_ibu', 'nama_ibu', 'pekerjaan_ibu', 'ket_pekerjaan_ibu',
            'pendidikan_ibu', 'alamat_ibu', 'no_hp_ibu', 'penghasilan_ibu',
        ]));
        $profile->save();

        // Sync nama_orang_tua & no_hp_orang_tua ke tabel siswas
        $namaOrtu = $request->nama_ayah ?: $request->nama_ibu;
        $noHpOrtu = $request->no_hp_ayah ?: $request->no_hp_ibu;
        $siswaUpdates = [];
        if ($namaOrtu) {
            $siswaUpdates['nama_orang_tua'] = $namaOrtu;
        }
        if ($noHpOrtu) {
            $siswaUpdates['no_hp_orang_tua'] = $noHpOrtu;
        }
        if (!empty($siswaUpdates)) {
            $siswa->update($siswaUpdates);
        }

        return redirect()->back()->with('success', 'Profil Orang Tua berhasil diperbarui.');
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
        if (auth()->user()->hasAnyRole(['admin', 'guru', 'kesiswaan']) && request()->filled('siswa_id')) {
            $siswa = Siswa::with('kelas')->find(request('siswa_id'));
        } else {
            $siswa = Siswa::with('kelas')->where('user_id', $user->id)->first();
        }

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

    // ===== SEKRETARIS: Kehadiran Mata Pelajaran =====

    /**
     * Halaman Kehadiran Mata Pelajaran (hanya untuk sekretaris).
     */
    public function kehadiranMataPelajaran(Request $request)
    {
        $siswa = $this->getSiswaAuth();
        if (!$siswa || !$siswa->is_sekretaris || !$siswa->kelas) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $activeSemester = Semester::where('status', 'aktif')->first() ?? Semester::latest()->first();

        $periode = $request->input('periode', date('Ym'));
        $year = substr($periode, 0, 4);
        $month = substr($periode, 4, 2);

        $groupedRecords = KehadiranMataPelajaran::with(['mataPelajaran.guru', 'creator', 'details'])
            ->where('kelas_id', $siswa->kelas_id)
            ->when($activeSemester, fn($q) => $q->where('semester_id', $activeSemester->id))
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->get()
            ->groupBy(fn($item) => $item->tanggal->format('Y-m-d'));

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        $records = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $startDate->copy()->addDays($day - 1);
            $dateStr = $date->format('Y-m-d');
            $dayRecords = $groupedRecords->get($dateStr);
            $records[] = [
                'tanggal'    => $dateStr,
                'tanggal_label' => $date->isoFormat('dddd, D MMMM Y'),
                'count'      => $dayRecords?->count() ?? 0,
                'is_future'  => $date->isFuture(),
				'mapel_list' => $dayRecords ? $dayRecords->pluck('mataPelajaran.nama')->implode('. ') : '',
            ];
        }

        $siswaKelas = Siswa::where('kelas_id', $siswa->kelas_id)
            ->orderBy('nama')
            ->get();

        return view('pages.absensi.kehadiran-mata-pelajaran', compact(
            'siswa',
            'records',
            'daysInMonth',
            'siswaKelas',
            'activeSemester',
            'periode'
        ));
    }

    /**
     * Halaman Profiling Kehadiran Mata Pelajaran per Tanggal.
     */
    public function profilingKehadiranMp($tanggal)
    {
        $siswa = $this->getSiswaAuth();
        if (!$siswa || !$siswa->is_sekretaris || !$siswa->kelas) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $activeSemester = Semester::where('status', 'aktif')->first() ?? Semester::latest()->first();

        $records = KehadiranMataPelajaran::with(['mataPelajaran.guru', 'creator', 'details'])
            ->where('kelas_id', $siswa->kelas_id)
            ->where('tanggal', $tanggal)
            ->when($activeSemester, fn($q) => $q->where('semester_id', $activeSemester->id))
            ->orderBy('jam_mulai', 'asc')
            ->get();

        $siswaKelas = Siswa::where('kelas_id', $siswa->kelas_id)
            ->orderBy('nama')
            ->get();

        return view('pages.absensi.kehadiran-mp-profiling', compact(
            'siswa',
            'records',
            'siswaKelas',
            'activeSemester',
            'tanggal'
        ));
    }

    /**
     * Export Rekap Kehadiran Mata Pelajaran ke Excel.
     */
    public function exportKehadiranMp(Request $request)
    {
        $siswa = $this->getSiswaAuth();
        if (!$siswa || !$siswa->is_sekretaris || !$siswa->kelas) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $activeSemester = Semester::where('status', 'aktif')->first() ?? Semester::latest()->first();

        $periode = $request->input('periode', date('Ym'));
        $year = substr($periode, 0, 4);
        $month = substr($periode, 4, 2);

        $groupedRecords = KehadiranMataPelajaran::with(['mataPelajaran.guru', 'creator', 'details'])
            ->where('kelas_id', $siswa->kelas_id)
            ->when($activeSemester, fn($q) => $q->where('semester_id', $activeSemester->id))
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->get()
            ->groupBy(fn($item) => $item->tanggal->format('Y-m-d'));

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Kehadiran MP');

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'Jumlah Mapel');
        $sheet->setCellValue('D1', 'Nama Mata Pelajaran');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3E97FF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        $rowNum = 2;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $startDate->copy()->addDays($day - 1);
            $dateStr = $date->format('Y-m-d');
            $dayRecords = $groupedRecords->get($dateStr);
            $count = $dayRecords?->count() ?? 0;
            $mapelList = $dayRecords ? $dayRecords->pluck('mataPelajaran.nama')->implode(', ') : '';
            $sheet->setCellValue('A' . $rowNum, $day);
            $sheet->setCellValue('B' . $rowNum, $date->isoFormat('dddd, D MMMM Y'));
            $sheet->setCellValue('C' . $rowNum, $count);
            $sheet->setCellValue('D' . $rowNum, $mapelList);
            $sheet->getStyle('A' . $rowNum . ':D' . $rowNum)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('DDDDDD');
            $rowNum++;
        }

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $filename = 'Rekap_Kehadiran_MP_' . str_replace(' ', '_', $siswa->kelas->nama) . '_' . $periode . '.xlsx';
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    public function getJadwalByTanggal(Request $request)
    {
        $siswa = $this->getSiswaAuth();
        if (!$siswa || !$siswa->is_sekretaris || !$siswa->kelas) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $tanggal = $request->input('tanggal');
        if (!$tanggal) {
            return response()->json(['error' => 'Parameter tanggal wajib diisi'], 400);
        }

        $mataPelajarans = \App\Models\MataPelajaran::with('guru')->orderBy('nama')->get();

        return response()->json($mataPelajarans->map(function ($mp) {
            return [
                'id'                     => $mp->id,
                'mata_pelajaran_id'      => $mp->id,
                'mata_pelajaran'         => $mp->nama,
                'guru'                   => $mp->guru->nama ?? '-',
                'jam_mulai'              => null,
                'jam_selesai'            => null,
            ];
        }));
    }

    /**
     * Store a new subject attendance header record.
     */
    public function storeKehadiranMataPelajaran(Request $request)
    {
        $siswa = $this->getSiswaAuth();
        if (!$siswa || !$siswa->is_sekretaris || !$siswa->kelas) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($request->has('jam_mulai')) {
            $request->merge(['jam_mulai' => substr($request->jam_mulai, 0, 5)]);
        }
        if ($request->has('jam_selesai')) {
            $request->merge(['jam_selesai' => substr($request->jam_selesai, 0, 5)]);
        }

        $request->validate([
            'tanggal'            => 'required|date',
            'mata_pelajaran_id'  => 'required|exists:mata_pelajarans,id',
            'jam_mulai'          => 'required|date_format:H:i',
            'jam_selesai'        => 'required|date_format:H:i|after:jam_mulai',
        ], [
            'tanggal.required'            => 'Tanggal wajib diisi',
            'mata_pelajaran_id.required'  => 'Mata pelajaran wajib dipilih',
            'jam_mulai.required'          => 'Jam mulai wajib dipilih',
            'jam_selesai.required'        => 'Jam selesai wajib dipilih',
            'jam_selesai.after'           => 'Jam selesai harus setelah jam mulai',
        ]);

        $activeSemester = Semester::where('status', 'aktif')->first();
        if (!$activeSemester) {
            return response()->json(['error' => 'Semester aktif tidak ditemukan'], 400);
        }

        // Check duplikasi
        $exists = KehadiranMataPelajaran::where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->where('tanggal', $request->tanggal)
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Data kehadiran untuk mata pelajaran ini pada tanggal tersebut sudah ada'], 409);
        }

        $record = KehadiranMataPelajaran::create([
            'kelas_id'           => $siswa->kelas_id,
            'semester_id'        => $activeSemester->id,
            'mata_pelajaran_id'  => $request->mata_pelajaran_id,
            'tanggal'            => $request->tanggal,
            'jam_mulai'          => $request->jam_mulai,
            'jam_selesai'        => $request->jam_selesai,
            'created_by'         => $siswa->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data kehadiran mata pelajaran berhasil ditambahkan',
            'data'    => $record->load(['mataPelajaran.guru', 'creator']),
        ]);
    }

    /**
     * AJAX: Get list of subject attendance records for sekretaris class.
     */
    public function getKehadiranMataPelajaranData(Request $request)
    {
        $siswa = $this->getSiswaAuth();
        if (!$siswa || !$siswa->is_sekretaris || !$siswa->kelas) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $activeSemester = Semester::where('status', 'aktif')->first();

        $records = KehadiranMataPelajaran::with(['mataPelajaran', 'creator', 'details'])
            ->where('kelas_id', $siswa->kelas_id)
            ->when($activeSemester, fn($q) => $q->where('semester_id', $activeSemester->id))
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($r) {
                $totalSiswa = Siswa::where('kelas_id', $r->kelas_id)->count();
                $hadirCount = $r->details->where('status', true)->count();
                return [
                    'id'              => $r->id,
                    'mata_pelajaran'  => $r->mataPelajaran?->nama ?? '-',
                    'tanggal'         => $r->tanggal->format('Y-m-d'),
                    'tanggal_label'   => $r->tanggal->isoFormat('dddd, D MMMM Y'),
                    'created_by'      => $r->creator?->nama ?? '-',
                    'total_siswa'     => $totalSiswa,
                    'hadir_count'     => $hadirCount,
                    'created_at'      => $r->created_at->format('d-m-Y H:i'),
                ];
            });

        return response()->json($records);
    }

    /**
     * Halaman Daftar Kehadiran Mata Pelajaran (Detail Page).
     */
    public function kehadiranMataPelajaranDetailPage($id)
    {
        $siswa = $this->getSiswaAuth();
        if (!$siswa || !$siswa->is_sekretaris || !$siswa->kelas) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $record = KehadiranMataPelajaran::with(['mataPelajaran.guru', 'details.siswa'])
            ->where('id', $id)
            ->where('kelas_id', $siswa->kelas_id)
            ->first();

        if (!$record) {
            abort(404, 'Data tidak ditemukan');
        }

        $siswaKelas = Siswa::where('kelas_id', $siswa->kelas_id)
            ->orderBy('nis', 'asc')
            ->get()
            ->map(function ($s) use ($record) {
                $detail = $record->details->firstWhere('siswa_id', $s->id);
                return [
                    'siswa_id'    => $s->id,
                    'nama'        => $s->nama,
                    'nis'         => $s->nis ?? '-',
                    'nisn'        => $s->nisn ?? '-',
                    'status'      => $detail ? $detail->status : true,
                    'keterangan'  => $detail ? $detail->keterangan : '',
                    'detail_id'   => $detail ? $detail->id : null,
                ];
            });

        return view('pages.absensi.kehadiran-mp-detail-page', compact(
            'siswa',
            'record',
            'siswaKelas'
        ));
    }

    /**
     * AJAX: Get detail of a subject attendance record with all student statuses.
     */
    public function getKehadiranMataPelajaranDetail($id)
    {
        $siswa = $this->getSiswaAuth();
        if (!$siswa || !$siswa->is_sekretaris || !$siswa->kelas) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $record = KehadiranMataPelajaran::with(['mataPelajaran', 'details.siswa'])
            ->where('id', $id)
            ->where('kelas_id', $siswa->kelas_id)
            ->first();

        if (!$record) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $siswaKelas = Siswa::where('kelas_id', $siswa->kelas_id)
            ->orderBy('nama')
            ->get()
            ->map(function ($s) use ($record) {
                $detail = $record->details->firstWhere('siswa_id', $s->id);
                return [
                    'siswa_id'    => $s->id,
                    'nama'        => $s->nama,
                    'nisn'        => $s->nisn ?? $s->nis ?? '-',
                    'status'      => $detail ? $detail->status : true,
                    'keterangan'  => $detail ? $detail->keterangan : '',
                    'detail_id'   => $detail ? $detail->id : null,
                ];
            });

        return response()->json([
            'record' => [
                'id'             => $record->id,
                'mata_pelajaran' => $record->mataPelajaran?->nama ?? '-',
                'tanggal'        => $record->tanggal->format('Y-m-d'),
                'tanggal_label'  => $record->tanggal->isoFormat('dddd, D MMMM Y'),
                'hari'           => $record->tanggal->isoFormat('dddd'),
            ],
            'siswa' => $siswaKelas,
        ]);
    }

    /**
     * Save student attendance statuses for a subject.
     */
    public function saveKehadiranMataPelajaranDetail(Request $request, $id)
    {
        $siswa = $this->getSiswaAuth();
        if (!$siswa || !$siswa->is_sekretaris || !$siswa->kelas) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $record = KehadiranMataPelajaran::where('id', $id)
            ->where('kelas_id', $siswa->kelas_id)
            ->first();

        if (!$record) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $request->validate([
            'siswa'              => 'required|array',
            'siswa.*.siswa_id'   => 'required|exists:siswas,id',
            'siswa.*.status'     => 'required|boolean',
            'siswa.*.keterangan' => 'nullable|string|max:500',
        ]);

        // Backend validation: Keterangan is required if status is false (absent)
        foreach ($request->siswa as $item) {
            if (!$item['status'] && empty(trim($item['keterangan'] ?? ''))) {
                $siswaNama = Siswa::find($item['siswa_id'])->nama ?? 'Siswa';
                return response()->json([
                    'error' => "Kolom keterangan wajib diisi untuk siswa \"{$siswaNama}\" yang tidak hadir."
                ], 422);
            }
        }

        foreach ($request->siswa as $item) {
            KehadiranMataPelajaranDetail::updateOrCreate(
                [
                    'kehadiran_mata_pelajaran_id' => $record->id,
                    'siswa_id'                    => $item['siswa_id'],
                ],
                [
                    'status'     => $item['status'],
                    'keterangan' => $item['keterangan'] ?? null,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Data kehadiran berhasil disimpan',
        ]);
    }

    /**
     * Delete a subject attendance record.
     */
    public function destroyKehadiranMataPelajaran($id)
    {
        $siswa = $this->getSiswaAuth();
        if (!$siswa || !$siswa->is_sekretaris || !$siswa->kelas) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $record = KehadiranMataPelajaran::where('id', $id)
            ->where('kelas_id', $siswa->kelas_id)
            ->first();

        if (!$record) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kehadiran berhasil dihapus',
        ]);
    }

    /**
     * Halaman Pengaduan (hanya untuk sekretaris).
     */
    public function pengaduan(Request $request)
    {
        $siswa = $this->getSiswaAuth();
        if (!$siswa || !$siswa->is_sekretaris || !$siswa->kelas) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $query = \App\Models\Pengaduan::where('siswa_id', $siswa->id);

        if ($request->filled('tanggal_range')) {
            $dates = explode(' hingga ', $request->tanggal_range);
            if (count($dates) === 2) {
                $query->whereBetween('tanggal', [$dates[0], $dates[1]]);
            } else {
                $query->where('tanggal', $dates[0]);
            }
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        $records = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('pages.absensi.pengaduan', compact('siswa', 'records'));
    }

    /**
     * POST: Konfirmasi Kehadiran Guru oleh Sekretaris Kelas.
     */
    public function konfirmasiGuru(Request $request, $id)
    {
        $siswa = $this->getSiswaAuth();
        if (!$siswa || !$siswa->is_sekretaris || !$siswa->kelas) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $record = KehadiranMataPelajaran::where('id', $id)
            ->where('kelas_id', $siswa->kelas_id)
            ->first();

        if (!$record) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $request->validate([
            'is_guru_hadir'       => 'required|boolean',
            'ada_konfirmasi_guru' => 'required|boolean',
        ]);

        $record->update([
            'is_guru_hadir'       => $request->is_guru_hadir,
            'ada_konfirmasi_guru' => $request->ada_konfirmasi_guru,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Konfirmasi kehadiran guru berhasil disimpan',
            'is_guru_hadir' => (bool) $record->is_guru_hadir,
            'ada_konfirmasi_guru' => (bool) $record->ada_konfirmasi_guru,
        ]);
    }

    /**
     * Helper: Get authenticated student.
     */
    private function getSiswaAuth()
    {
        $user = auth()->user();
        return Siswa::with('kelas')->where('user_id', $user->id)->first();
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
                        $row['keterangan'] = '-';
                    }
                } else {
                    $row['msk_lbr'] = '✗';
                    $row['keterangan'] = '-';
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

    /**
     * Simpan Pengaduan Baru.
     */
    public function storePengaduan(Request $request)
    {
        $siswa = $this->getSiswaAuth();
        if (!$siswa || !$siswa->is_sekretaris || !$siswa->kelas) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'tanggal'   => 'required|date',
            'deskripsi' => 'required|string|max:1000',
            'bukti'     => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $file = $request->file('bukti');
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = 'bukti_' . uniqid() . '.jpg';
        
        $tempPath = $file->getRealPath();
        
        // Load image resource
        $image = null;
        if (function_exists('imagecreatefromjpeg')) {
            if ($extension === 'png' && function_exists('imagecreatefrompng')) {
                $image = @\imagecreatefrompng($tempPath);
            } elseif ($extension === 'gif' && function_exists('imagecreatefromgif')) {
                $image = @\imagecreatefromgif($tempPath);
            } else {
                $image = @\imagecreatefromjpeg($tempPath);
            }
        }

        if ($image) {
            // Compress by encoding as JPEG with quality = 30-40 (gives around 10-30kb size for photos)
            // Scale width to max 1000px to conserve memory/storage
            $width = \imagesx($image);
            $height = \imagesy($image);
            if ($width > 1000) {
                $newWidth = 1000;
                $newHeight = intval(($height / $width) * 1000);
                $resized = \imagecreatetruecolor($newWidth, $newHeight);
                \imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                \imagedestroy($image);
                $image = $resized;
            }
            
            $tempFile = tempnam(sys_get_temp_dir(), 'pengaduan');
            \imagejpeg($image, $tempFile, 35);
            \imagedestroy($image);

            $buktiPath = \Illuminate\Support\Facades\Storage::disk('public')->putFileAs('pengaduan', new \Illuminate\Http\File($tempFile), $filename);
            @unlink($tempFile);
        } else {
            $buktiPath = \Illuminate\Support\Facades\Storage::disk('public')->putFileAs('pengaduan', $file, $filename);
        }

        $buktiPath = 'pengaduan/' . $filename;

        \App\Models\Pengaduan::create([
            'siswa_id'  => $siswa->id,
            'tanggal'   => $request->tanggal,
            'deskripsi' => $request->deskripsi,
            'bukti'     => $buktiPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengaduan berhasil ditambahkan',
        ]);
    }

}
