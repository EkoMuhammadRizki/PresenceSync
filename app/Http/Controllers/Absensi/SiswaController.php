<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\FingerprintDevice;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SiswaController extends Controller
{
    public function index()
    {
        $siswas = Siswa::with(['kelas', 'user.info'])->orderBy('nama', 'asc')->get();
        $kelas  = Kelas::where('status', 'aktif')->orderBy('tingkat')->get();
        
        // Dapatkan user yang belum dikaitkan dengan data siswa manapun
        $siswaUserIds = Siswa::pluck('user_id')->filter()->toArray();
        $users = User::whereNotIn('id', $siswaUserIds)->orderBy('email')->get();
        
        $unpushedCount = Siswa::where('is_pushed', false)->where('status', 'aktif')->count();

        return view('pages.absensi.siswa', compact('siswas', 'kelas', 'users', 'unpushedCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis'           => 'required|regex:/^[0-9]+$/|max:20|unique:siswas,nis',
            'password'      => 'required|string|min:6',
            'nama'          => 'required|string|max:150',
            'kelas_id'      => 'nullable|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date|before_or_equal:today',
            'alamat'        => 'nullable|string',
        ], [
            'nis.required'                 => 'NIS wajib diisi karena digunakan untuk login.',
            'nis.regex'                    => 'NIS hanya boleh berisi angka.',
            'nis.max'                      => 'NIS maksimal 20 digit.',
            'nis.unique'                   => 'NIS sudah terdaftar.',
            'password.required'            => 'Password wajib diisi.',
            'password.min'                 => 'Password minimal harus 6 karakter.',
            'nama.required'                => 'Nama siswa wajib diisi.',
            'jenis_kelamin.required'       => 'Jenis kelamin wajib dipilih.',
            'tanggal_lahir.before_or_equal'=> 'Tanggal lahir tidak boleh melebihi hari ini.',
        ]);

        $nameParts = explode(' ', trim($request->nama), 2);
        $firstName = $nameParts[0];
        $lastName  = $nameParts[1] ?? $nameParts[0];

        // Generate email internal dari NIS (tidak perlu diinput user)
        $email = $request->nis . '@siswa.internal';
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = $request->nis . $counter . '@siswa.internal';
            $counter++;
        }

        $user = User::create([
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $email,
            'password'   => Hash::make($request->password),
        ]);

        $siswa = Siswa::create([
            'user_id'       => $user->id,
            'nama'          => $request->nama,
            'nis'           => $request->nis,
            'kelas_id'      => $request->kelas_id,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat'        => $request->alamat,
            'status'        => 'aktif',
            'is_pushed'     => false,
            'is_enrolled'   => false,
        ]);

        // Auto-assign ID Fingerprint dari ID Siswa (database auto-increment ID)
        $siswa->update(['fingerprint_id' => (string) $siswa->id]);

        if (auth()->check()) {
            activity()->causedBy(auth()->user())->performedOn($siswa)->log("Menambah data siswa baru: {$siswa->nama}");
        }

        return redirect()->route('siswa.index')
            ->with('success', 'Siswa berhasil ditambahkan. Silakan klik Post ke Mesin untuk sinkronisasi ke perangkat.');
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama'          => 'required|string|max:150',
            'nisn'          => 'nullable|string|max:20|unique:siswas,nisn,' . $siswa->id,
            'nis'           => 'nullable|regex:/^[0-9]+$/|max:20|unique:siswas,nis,' . $siswa->id,
            'kelas_id'      => 'nullable|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date|before_or_equal:today',
            'alamat'        => 'nullable|string',
            'fingerprint_id'=> 'nullable|string|max:50',
            'status'        => 'nullable|in:aktif,lulus,keluar',
        ], [
            'nisn.unique'                  => 'NISN sudah terdaftar.',
            'nis.unique'                   => 'NIS sudah terdaftar.',
            'nis.regex'                    => 'NIS hanya boleh berisi angka.',
            'nis.max'                      => 'NIS maksimal 20 digit.',
            'tanggal_lahir.before_or_equal'=> 'Tanggal lahir tidak boleh melebihi hari ini.',
        ]);

        $oldFingerprintId = (string) $siswa->fingerprint_id;
        $oldStatus = $siswa->status;

        $siswa->update($request->only(
            'nama', 'nisn', 'nis', 'kelas_id', 'jenis_kelamin', 'tanggal_lahir', 'alamat', 'status'
        ));

        $newFingerprintId = $request->filled('fingerprint_id')
            ? (string) $request->fingerprint_id
            : (empty($siswa->fingerprint_id) ? (string) $siswa->id : (string) $siswa->fingerprint_id);

        $newStatus = $siswa->status;

        // Kondisi 1: Status baru adalah 'lulus' atau 'keluar' (wajib dihapus dari mesin)
        if ($newStatus === 'lulus' || $newStatus === 'keluar') {
            $siswa->update([
                'fingerprint_id' => $newFingerprintId,
                'is_enrolled'    => false,
                'is_pushed'      => true, // Ditandai true agar tidak dianggap "Perlu Post" di UI
            ]);

            // Queue perintah ADMS untuk menghapus user
            if (!empty($oldFingerprintId)) {
                $admsCmd = "DATA DELETE USER PIN={$oldFingerprintId}";
                \App\Http\Controllers\Absensi\AdmsController::queueCommand($admsCmd);
            }

            // Hapus dari perangkat aktif via SDK
            try {
                $service = app(\App\Services\FingerprintService::class);
                $activeDevices = FingerprintDevice::where('is_aktif', true)->get();
                foreach ($activeDevices as $dev) {
                    if (!empty($oldFingerprintId)) {
                        $service->deleteUser($dev, $oldFingerprintId);
                    }
                    $service->refreshDB($dev);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Edit siswa (lulus/keluar) delete device error: " . $e->getMessage());
            }
        }
        // Kondisi 2: Status berubah dari 'lulus'/'keluar' ke 'aktif'
        elseif (($oldStatus === 'lulus' || $oldStatus === 'keluar') && $newStatus === 'aktif') {
            $siswa->update([
                'fingerprint_id' => $newFingerprintId,
                'is_enrolled'    => false,
                'is_pushed'      => false,
            ]);

            // Otomatis push ulang ke perangkat
            $this->autoPushFingerprintToDevices($siswa);
        }
        // Kondisi 3: Status tetap aktif, tapi fingerprint_id berubah
        elseif ($oldFingerprintId !== $newFingerprintId && !empty($oldFingerprintId) && $newStatus === 'aktif') {
            $siswa->update([
                'fingerprint_id' => $newFingerprintId,
                'is_enrolled'    => false,
                'is_pushed'      => false,
            ]);

            // Hapus fingerprint_id lama dari device
            try {
                $service = app(\App\Services\FingerprintService::class);
                $activeDevices = FingerprintDevice::where('is_aktif', true)->get();
                foreach ($activeDevices as $dev) {
                    $service->deleteUser($dev, $oldFingerprintId);
                    $service->refreshDB($dev);
                }
            } catch (\Throwable $e) {}

            // Auto push ID baru
            $this->autoPushFingerprintToDevices($siswa);
        }
        // Kondisi 4: Update data biasa (tetap aktif, detail berubah)
        else {
            $siswa->update([
                'fingerprint_id' => $newFingerprintId,
                'is_pushed'      => false,
            ]);
        }


        // Update nama user terkait
        if ($siswa->user) {
            $nameParts = explode(' ', trim($request->nama), 2);
            $firstName = $nameParts[0];
            $lastName  = $nameParts[1] ?? $nameParts[0];
            $siswa->user->update([
                'first_name' => $firstName,
                'last_name'  => $lastName,
            ]);
        }

        if (auth()->check()) {
            activity()->causedBy(auth()->user())->performedOn($siswa)->log("Mengubah data siswa: {$siswa->nama}");
        }

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui (Status: Perlu Post). Silakan klik tombol Post ke Mesin saat siap dikirim.');
    }

    public function destroy(Siswa $siswa)
    {
        set_time_limit(60); // Beri waktu cukup untuk komunikasi ke fingerprint device

        $oldFingerprintId = (string) $siswa->fingerprint_id;
        $namaSiswa = $siswa->nama;
        $user = $siswa->user;

        // Hapus data siswa & user dari database terlebih dahulu
        $siswa->delete();
        if ($user) {
            $user->delete();
        }

        // Coba hapus dari perangkat fingerprint & daftarkan ke antrean ADMS
        if (!empty($oldFingerprintId)) {
            // 1. Masukkan ke antrean ADMS (jika mesin offline, saat mesin online/poll lagi akan otomatis terhapus)
            try {
                $admsCmd = "DATA DELETE USER PIN={$oldFingerprintId}";
                \App\Http\Controllers\Absensi\AdmsController::queueCommand($admsCmd);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("destroy siswa ADMS queue error: " . $e->getMessage());
            }

            // 2. Coba hapus langsung via SDK/SOAP jika mesin sedang online di LAN saat ini
            try {
                $service = app(\App\Services\FingerprintService::class);
                $activeDevices = FingerprintDevice::where('is_aktif', true)->get();
                foreach ($activeDevices as $dev) {
                    $service->deleteUser($dev, $oldFingerprintId);
                    $service->refreshDB($dev);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("destroy siswa: gagal hapus langsung dari fingerprint device via LAN. " . $e->getMessage());
            }
        }

        if (auth()->check()) {
            activity()->causedBy(auth()->user())->log("Menghapus data siswa: {$namaSiswa}");
        }

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }

    public function downloadTemplate(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        
        // Sheet 1: Template Siswa
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Siswa');
        
        // Headers: Data Siswa (A-M) | Data Ayah (N-V) | Data Ibu (W-AE)
        $headers = [
            // Data Siswa (A-M)
            'nis',
            'nama',
            'jenis_kelamin',
            'kelas',
            'id_fingerprint',
            'nik',
            'tempat_lahir',
            'tanggal_lahir',
            'agama',
            'alamat',
            'no_hp',
            'status',
            'asal_sekolah',
            // Data Ayah (N-V)
            'nik_ayah',
            'nama_ayah',
            'tahun_lahir_ayah',
            'pekerjaan_ayah',
            'ket_pekerjaan_ayah',
            'pendidikan_ayah',
            'alamat_ayah',
            'no_hp_ayah',
            'penghasilan_ayah',
            // Data Ibu (W-AE)
            'nik_ibu',
            'nama_ibu',
            'tahun_lahir_ibu',
            'pekerjaan_ibu',
            'ket_pekerjaan_ibu',
            'pendidikan_ibu',
            'alamat_ibu',
            'no_hp_ibu',
            'penghasilan_ibu',
        ];
        
        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }

        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));

        // Style header: bold
        $sheet->getStyle('A1:' . $lastColLetter . '1')->getFont()->setBold(true);
        // Warna pemisah: Data Siswa (Biru muda), Data Ayah (Kuning muda), Data Ibu (Merah muda)
        $sheet->getStyle('A1:M1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDEEFF');
        $sheet->getStyle('N1:V1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFE8CC');
        $sheet->getStyle('W1:AE1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFDDEE');
        
        // Populate existing students if requested (default / when not 'empty')
        if (!$request->has('empty')) {
            $siswas = Siswa::with(['kelas'])->orderBy('nama')->get();
            $rowNum = 2;
            foreach ($siswas as $siswa) {
                // Data Siswa
                $sheet->setCellValue('A' . $rowNum, $siswa->nis ?? '');
                $sheet->setCellValue('B' . $rowNum, $siswa->nama);
                $sheet->setCellValue('C' . $rowNum, $siswa->jenis_kelamin);
                $sheet->setCellValue('D' . $rowNum, $siswa->kelas->nama ?? '');
                $sheet->setCellValue('E' . $rowNum, $siswa->fingerprint_id ?? '');
                $sheet->setCellValue('F' . $rowNum, $siswa->nik ?? '');
                $sheet->setCellValue('G' . $rowNum, $siswa->tempat_lahir ?? '');
                $sheet->setCellValue('H' . $rowNum, $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('Y-m-d') : '');
                $sheet->setCellValue('I' . $rowNum, $siswa->agama ?? '');
                $sheet->setCellValue('J' . $rowNum, $siswa->alamat ?? '');
                $sheet->setCellValue('K' . $rowNum, $siswa->no_hp ?? '');
                $sheet->setCellValue('L' . $rowNum, $siswa->status ?? 'aktif');
                $sheet->setCellValue('M' . $rowNum, $siswa->asal_sekolah ?? '');

                // Data Orang Tua — ambil dari parent_profiles via user siswa
                $parentUserId = $siswa->orang_tua_user_id ?? $siswa->user_id;
                $parentProfile = null;
                if ($parentUserId) {
                    $parentProfile = \App\Models\ParentProfile::where('parent_user_id', $parentUserId)->first();
                }
                // Ayah
                $sheet->setCellValue('N' . $rowNum, $parentProfile->nik_ayah ?? '');
                $sheet->setCellValue('O' . $rowNum, $parentProfile->nama_ayah ?? '');
                $sheet->setCellValue('P' . $rowNum, $parentProfile->tahun_lahir_ayah ?? '');
                $sheet->setCellValue('Q' . $rowNum, $parentProfile->pekerjaan_ayah ?? '');
                $sheet->setCellValue('R' . $rowNum, $parentProfile->ket_pekerjaan_ayah ?? '');
                $sheet->setCellValue('S' . $rowNum, $parentProfile->pendidikan_ayah ?? '');
                $sheet->setCellValue('T' . $rowNum, $parentProfile->alamat_ayah ?? '');
                $sheet->setCellValue('U' . $rowNum, $parentProfile->no_hp_ayah ?? '');
                $sheet->setCellValue('V' . $rowNum, $parentProfile->penghasilan_ayah ?? '');
                // Ibu
                $sheet->setCellValue('W' . $rowNum, $parentProfile->nik_ibu ?? '');
                $sheet->setCellValue('X' . $rowNum, $parentProfile->nama_ibu ?? '');
                $sheet->setCellValue('Y' . $rowNum, $parentProfile->tahun_lahir_ibu ?? '');
                $sheet->setCellValue('Z' . $rowNum, $parentProfile->pekerjaan_ibu ?? '');
                $sheet->setCellValue('AA' . $rowNum, $parentProfile->ket_pekerjaan_ibu ?? '');
                $sheet->setCellValue('AB' . $rowNum, $parentProfile->pendidikan_ibu ?? '');
                $sheet->setCellValue('AC' . $rowNum, $parentProfile->alamat_ibu ?? '');
                $sheet->setCellValue('AD' . $rowNum, $parentProfile->no_hp_ibu ?? '');
                $sheet->setCellValue('AE' . $rowNum, $parentProfile->penghasilan_ibu ?? '');

                $rowNum++;
            }
        }
        
        // Sheet 2: Daftar Kelas (untuk referensi)
        $kelasSheet = $spreadsheet->createSheet();
        $kelasSheet->setTitle('Daftar Kelas');
        $kelasSheet->setCellValue('A1', 'Nama Kelas');
        $kelasSheet->setCellValue('B1', 'Tingkat');
        $kelasSheet->getStyle('A1:B1')->getFont()->setBold(true);
        
        $kelas = Kelas::where('status', 'aktif')->orderBy('tingkat')->get();
        $row = 2;
        foreach ($kelas as $k) {
            $kelasSheet->setCellValue('A' . $row, $k->nama);
            $kelasSheet->setCellValue('B' . $row, $k->tingkat);
            $row++;
        }
        
        // Autofit columns for both sheets
        foreach ($spreadsheet->getAllSheets() as $currentSheet) {
            foreach ($currentSheet->getColumnIterator() as $column) {
                $currentSheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        
        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="template_import_siswa.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');
        
        return $response;
    }

    public function import(Request $request)
    {
        @ini_set('max_execution_time', '600');
        @ini_set('memory_limit', '512M');
        @set_time_limit(600);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes'    => 'Format file harus berupa .xlsx atau .xls.',
        ]);

        $file = $request->file('file');
        
        try {
            $filePath = $file->getRealPath();
            if (empty($filePath) || !file_exists($filePath)) {
                $filePath = $file->getPathname();
            }
            if (empty($filePath) || !file_exists($filePath)) {
                $tempName = 'temp_import_siswa_' . uniqid() . '.' . ($file->getClientOriginalExtension() ?: 'xlsx');
                $tempPath = storage_path('app/' . $tempName);
                $file->move(storage_path('app'), $tempName);
                $filePath = $tempPath;
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (isset($tempPath) && file_exists($tempPath)) {
                @unlink($tempPath);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal membaca file Excel: ' . $e->getMessage()]);
        }

        if (count($rows) <= 1) {
            return redirect()->back()->withErrors(['error' => 'File Excel kosong atau hanya berisi header.']);
        }
        
        $rawHeader = array_shift($rows);

        // Buat Header Map dinamis: lowercase, trim, ganti spasi/dash dengan underscore
        $headerMap = [];
        foreach ($rawHeader as $index => $colName) {
            if ($colName !== null && trim($colName) !== '') {
                $cleanName = strtolower(trim((string)$colName));
                $cleanName = preg_replace('/[^a-z0-9_]/', '_', str_replace([' ', '-', '.'], '_', $cleanName));
                $cleanName = preg_replace('/_+/', '_', $cleanName);
                $headerMap[$cleanName] = $index;
            }
        }

        // Validasi format template
        if (isset($headerMap['nip']) || $worksheet->getTitle() === 'Template Guru') {
            return redirect()->back()->withErrors(['error' => 'File Excel yang diunggah adalah template Guru. Silakan unggah file template Siswa yang benar.']);
        }
        if (!isset($headerMap['nis']) && !isset($headerMap['nama'])) {
            return redirect()->back()->withErrors(['error' => 'Format template tidak sesuai. Kolom "nis" dan "nama" tidak ditemukan dalam file Excel.']);
        }

        // Helper untuk mengambil nilai kolom berdasarkan alias header
        $getVal = function ($row, ...$aliases) use ($headerMap) {
            foreach ($aliases as $alias) {
                $cleanAlias = strtolower(trim($alias));
                $cleanAlias = preg_replace('/[^a-z0-9_]/', '_', str_replace([' ', '-', '.'], '_', $cleanAlias));
                $cleanAlias = preg_replace('/_+/', '_', $cleanAlias);
                if (isset($headerMap[$cleanAlias])) {
                    $idx = $headerMap[$cleanAlias];
                    if (isset($row[$idx]) && $row[$idx] !== null) {
                        $val = trim((string)$row[$idx]);
                        if ($val !== '') {
                            return $val;
                        }
                    }
                }
            }
            return null;
        };

        $successCount  = 0;
        $skipCount     = 0;
        $importedNames = [];
        $skippedNames  = [];

        // Preload cache di memori untuk performa tinggi O(1)
        $kelasMap = [];
        foreach (Kelas::all() as $k) {
            $kelasMap[trim(strtolower($k->nama))] = $k->id;
            $kelasMap[trim($k->nama)] = $k->id;
        }

        $existingNis = Siswa::pluck('nis', 'nis')->all();
        $existingEmails = User::pluck('email', 'email')->all();
        $existingFingerprints = Siswa::whereNotNull('fingerprint_id')->pluck('fingerprint_id', 'fingerprint_id')->all();

        // Gunakan batch transaction per 100 baris agar commit cepat dan tidak membebani memori
        $batchSize = 100;
        $currentBatch = 0;

        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                // Ambil data siswa
                $nama = $getVal($row, 'nama');
                if (empty($nama)) {
                    continue;
                }

                // Ambil & Normalisasi Jenis Kelamin secara komprehensif
                $rawJk = $getVal($row, 'jenis_kelamin', 'jk', 'l_p', 'lp', 'gender', 'jeniskelamin', 'sex', 'j_k', 'l_perempuan', 'l_p_');
                $jk = 'L';
                if ($rawJk !== null && trim((string)$rawJk) !== '') {
                    $cleanJk = strtoupper(trim((string)$rawJk));
                    if (
                        $cleanJk === 'P' ||
                        $cleanJk === 'PEREMPUAN' ||
                        $cleanJk === 'WANITA' ||
                        $cleanJk === 'FEMALE' ||
                        $cleanJk === 'F' ||
                        $cleanJk === '2' ||
                        str_starts_with($cleanJk, 'PEREMP') ||
                        str_starts_with($cleanJk, 'WANIT') ||
                        str_starts_with($cleanJk, 'FEM') ||
                        str_contains($cleanJk, 'PEREMPUAN') ||
                        str_contains($cleanJk, 'WANITA')
                    ) {
                        $jk = 'P';
                    } elseif (
                        $cleanJk === 'PRIA' ||
                        $cleanJk === 'L' ||
                        $cleanJk === 'LAKI-LAKI' ||
                        $cleanJk === 'LAKI - LAKI' ||
                        $cleanJk === 'LAKI_LAKI' ||
                        $cleanJk === 'LAKI' ||
                        $cleanJk === 'MALE' ||
                        $cleanJk === 'M' ||
                        $cleanJk === '1' ||
                        str_starts_with($cleanJk, 'LAKI')
                    ) {
                        $jk = 'L';
                    }
                }

                $kelasName    = $getVal($row, 'kelas', 'nama_kelas');
                $fingerprintId = substr($getVal($row, 'id_fingerprint', 'fingerprint_id', 'id_finger', 'fingerprint', 'pin') ?? '', 0, 50) ?: null;
                $nik          = substr($getVal($row, 'nik', 'nik_siswa') ?? '', 0, 20) ?: null;
                $tempatLahir  = $getVal($row, 'tempat_lahir');
                $tanggalLahirRaw = $getVal($row, 'tanggal_lahir', 'tgl_lahir');
                $agama        = $getVal($row, 'agama');
                $alamat       = $getVal($row, 'alamat', 'alamat_siswa');
                $noHp         = substr($getVal($row, 'no_hp', 'no_telepon', 'nohp') ?? '', 0, 30) ?: null;
                $status       = strtolower($getVal($row, 'status') ?? 'aktif');
                $asalSekolah  = $getVal($row, 'asal_sekolah', 'sekolah_asal');

                // Ambil data Ayah
                $nikAyah          = substr($getVal($row, 'nik_ayah', 'nik_bapak') ?? '', 0, 20) ?: null;
                $namaAyah         = substr($getVal($row, 'nama_ayah', 'nama_lengkap_ayah', 'nama_bapak') ?? '', 0, 255) ?: null;
                $tahunLahirAyah   = substr($getVal($row, 'tahun_lahir_ayah', 'thn_lahir_ayah', 'tahun_ayah') ?? '', 0, 20) ?: null;
                $pekerjaanAyah    = substr($getVal($row, 'pekerjaan_ayah', 'pekerjaan_bapak') ?? '', 0, 100) ?: null;
                $ketPekerjaanAyah = substr($getVal($row, 'ket_pekerjaan_ayah', 'keterangan_pekerjaan_ayah', 'ket_pekerjaan', 'keterangan_pekerjaan') ?? '', 0, 255) ?: null;
                $pendidikanAyah   = substr($getVal($row, 'pendidikan_ayah', 'pendidikan_terakhir_ayah', 'pendidikan_bapak') ?? '', 0, 100) ?: null;
                $alamatAyah       = $getVal($row, 'alamat_ayah', 'alamat_tinggal_ayah', 'alamat_bapak');
                $noHpAyah         = substr($getVal($row, 'no_hp_ayah', 'nomor_hp_ayah', 'no_telepon_ayah', 'nohp_ayah', 'hp_ayah') ?? '', 0, 30) ?: null;
                $penghasilanAyah  = substr($getVal($row, 'penghasilan_ayah', 'penghasilan_per_bulan_ayah', 'gaji_ayah') ?? '', 0, 100) ?: null;

                // Ambil data Ibu
                $nikIbu           = substr($getVal($row, 'nik_ibu') ?? '', 0, 20) ?: null;
                $namaIbu          = substr($getVal($row, 'nama_ibu', 'nama_lengkap_ibu') ?? '', 0, 255) ?: null;
                $tahunLahirIbu    = substr($getVal($row, 'tahun_lahir_ibu', 'thn_lahir_ibu', 'tahun_ibu') ?? '', 0, 20) ?: null;
                $pekerjaanIbu     = substr($getVal($row, 'pekerjaan_ibu') ?? '', 0, 100) ?: null;
                $ketPekerjaanIbu  = substr($getVal($row, 'ket_pekerjaan_ibu', 'keterangan_pekerjaan_ibu') ?? '', 0, 255) ?: null;
                $pendidikanIbu    = substr($getVal($row, 'pendidikan_ibu', 'pendidikan_terakhir_ibu') ?? '', 0, 100) ?: null;
                $alamatIbu        = $getVal($row, 'alamat_ibu', 'alamat_tinggal_ibu');
                $noHpIbu          = substr($getVal($row, 'no_hp_ibu', 'nomor_hp_ibu', 'no_telepon_ibu', 'nohp_ibu', 'hp_ibu') ?? '', 0, 30) ?: null;
                $penghasilanIbu   = substr($getVal($row, 'penghasilan_ibu', 'penghasilan_per_bulan_ibu', 'gaji_ibu') ?? '', 0, 100) ?: null;

                // NIS wajib ada
                if (empty($nis)) {
                    $skipCount++;
                    if (count($skippedNames) < 100) {
                        $skippedNames[] = [
                            'nama'   => $nama,
                            'nis'    => '-',
                            'alasan' => 'Kolom NIS kosong',
                        ];
                    }
                    continue;
                }

                // Cari kelas berdasarkan lookup array (atau auto-create jika belum ada)
                $kelasId = null;
                if ($kelasName) {
                    $cleanKelasName = trim($kelasName);
                    $kKey = strtolower($cleanKelasName);
                    if (isset($kelasMap[$kKey])) {
                        $kelasId = $kelasMap[$kKey];
                    } elseif (isset($kelasMap[$cleanKelasName])) {
                        $kelasId = $kelasMap[$cleanKelasName];
                    } else {
                        // Tentukan tingkat kelas otomatis (10, 11, 12)
                        $tingkat = '10';
                        if (preg_match('/^(xii|12)/i', $cleanKelasName)) {
                            $tingkat = '12';
                        } elseif (preg_match('/^(xi|11)/i', $cleanKelasName)) {
                            $tingkat = '11';
                        }
                        $newKelas = Kelas::create([
                            'nama'    => $cleanKelasName,
                            'tingkat' => $tingkat,
                            'status'  => 'aktif',
                        ]);
                        $kelasId = $newKelas->id;
                        $kelasMap[$kKey] = $kelasId;
                        $kelasMap[$cleanKelasName] = $kelasId;
                    }
                }

                // Jika NIS sudah ada di sistem, update datanya agar Jenis Kelamin & Kelas tersinkron
                if (isset($existingNis[$nis])) {
                    $existingSiswa = Siswa::where('nis', $nis)->first();
                    if ($existingSiswa) {
                        $updateData = ['jenis_kelamin' => $jk];
                        if ($kelasId) $updateData['kelas_id'] = $kelasId;
                        if ($nama) $updateData['nama'] = $nama;
                        if ($noHp) $updateData['no_hp'] = $noHp;
                        if ($nik) $updateData['nik'] = $nik;
                        if ($tempatLahir) $updateData['tempat_lahir'] = $tempatLahir;
                        if ($agama) $updateData['agama'] = $agama;
                        if ($alamat) $updateData['alamat'] = $alamat;
                        $existingSiswa->update($updateData);
                        $successCount++;
                        if (count($importedNames) < 100) {
                            $importedNames[] = [
                                'nama' => $nama,
                                'nis'  => $nis,
                                'kelas'=> $kelasName ?? '-',
                            ];
                        }
                    }
                    continue;
                }

                // Generate email internal dari NIS
                $email = $nis . '@siswa.internal';
                $counter = 1;
                while (isset($existingEmails[$email])) {
                    $email = $nis . $counter . '@siswa.internal';
                    $counter++;
                }

                // Parse tanggal lahir
                $tanggalLahir = null;
                if ($tanggalLahirRaw) {
                    if (is_numeric($tanggalLahirRaw) && $tanggalLahirRaw > 20000) {
                        try {
                            $tanggalLahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalLahirRaw)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $tanggalLahir = null;
                        }
                    } else {
                        try {
                            $parsedDate = date('Y-m-d', strtotime(str_replace('/', '-', $tanggalLahirRaw)));
                            if ($parsedDate && $parsedDate !== '1970-01-01') {
                                $tanggalLahir = $parsedDate;
                            }
                        } catch (\Exception $e) {
                            $tanggalLahir = null;
                        }
                    }
                }

                $password = $nis; // Default password = NIS

                $nameParts = explode(' ', trim($nama), 2);
                $firstName = $nameParts[0];
                $lastName  = $nameParts[1] ?? $nameParts[0];

                $user = User::create([
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'email'      => $email,
                    'password'   => Hash::make($password, ['rounds' => 6]),
                ]);

                // Track email baru di cache
                $existingEmails[$email] = $email;

                // Tentukan fingerprint_id
                $finalFingerprintId = $fingerprintId;
                if (!empty($finalFingerprintId) && isset($existingFingerprints[$finalFingerprintId])) {
                    $finalFingerprintId = (string) $user->id;
                } elseif (empty($finalFingerprintId)) {
                    $finalFingerprintId = (string) $user->id;
                }

                $siswa = Siswa::create([
                    'user_id'        => $user->id,
                    'kelas_id'       => $kelasId,
                    'nama'           => $nama,
                    'nis'            => $nis,
                    'nik'            => $nik,
                    'jenis_kelamin'  => $jk,
                    'tempat_lahir'   => $tempatLahir,
                    'tanggal_lahir'  => $tanggalLahir,
                    'agama'          => $agama,
                    'alamat'         => $alamat,
                    'no_hp'          => $noHp,
                    'status'         => in_array($status, ['aktif', 'lulus', 'keluar']) ? $status : 'aktif',
                    'asal_sekolah'   => $asalSekolah,
                    'fingerprint_id' => $finalFingerprintId,
                    'is_pushed'      => false,
                    'is_enrolled'    => false,
                ]);

                // Track NIS dan fingerprint baru di cache
                $existingNis[$nis] = $nis;
                $existingFingerprints[$finalFingerprintId] = $finalFingerprintId;

                // Simpan data orang tua ke parent_profiles jika ada isinya
                $adaDataOrtu = $nikAyah || $namaAyah || $tahunLahirAyah || $pekerjaanAyah
                            || $ketPekerjaanAyah || $pendidikanAyah || $alamatAyah || $noHpAyah || $penghasilanAyah
                            || $nikIbu || $namaIbu || $tahunLahirIbu || $pekerjaanIbu
                            || $ketPekerjaanIbu || $pendidikanIbu || $alamatIbu || $noHpIbu || $penghasilanIbu;

                if ($adaDataOrtu) {
                    \App\Models\ParentProfile::updateOrCreate(
                        ['parent_user_id' => $user->id],
                        [
                            'nik_ayah'           => $nikAyah,
                            'nama_ayah'          => $namaAyah,
                            'tahun_lahir_ayah'   => $tahunLahirAyah,
                            'pekerjaan_ayah'     => $pekerjaanAyah,
                            'ket_pekerjaan_ayah' => $ketPekerjaanAyah,
                            'pendidikan_ayah'    => $pendidikanAyah,
                            'alamat_ayah'        => $alamatAyah,
                            'no_hp_ayah'         => $noHpAyah,
                            'penghasilan_ayah'   => $penghasilanAyah,

                            'nik_ibu'            => $nikIbu,
                            'nama_ibu'           => $namaIbu,
                            'tahun_lahir_ibu'    => $tahunLahirIbu,
                            'pekerjaan_ibu'      => $pekerjaanIbu,
                            'ket_pekerjaan_ibu'  => $ketPekerjaanIbu,
                            'pendidikan_ibu'     => $pendidikanIbu,
                            'alamat_ibu'         => $alamatIbu,
                            'no_hp_ibu'          => $noHpIbu,
                            'penghasilan_ibu'    => $penghasilanIbu,
                        ]
                    );

                    $namaOrtu = $namaAyah ?: $namaIbu;
                    $noHpOrtu = $noHpAyah ?: $noHpIbu;
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
                }

                if (count($importedNames) < 100) {
                    $importedNames[] = [
                        'nama'  => $nama,
                        'nis'   => $nis,
                        'kelas' => $kelasName ?? '-',
                    ];
                }
                $successCount++;
                $currentBatch++;

                // Commit parsial per batch agar transaksi database cepat
                if ($currentBatch >= $batchSize) {
                    DB::commit();
                    DB::beginTransaction();
                    $currentBatch = 0;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat memproses data siswa: ' . $e->getMessage()]);
        }

        if (auth()->check() && $successCount > 0) {
            activity()->causedBy(auth()->user())->log("Import data siswa dari Excel ({$successCount} disimpan)");
        }

        return redirect()->route('siswa.index')
            ->with('import_success', [
                'success_count'  => $successCount,
                'skip_count'     => $skipCount,
                'imported_names' => $importedNames,
                'skipped_names'  => $skippedNames,
            ]);
    }

    /**
     * Post/Push data siswa (seluruhnya atau terpilih) ke semua perangkat mesin fingerprint yang aktif
     */
    public function pushToDevices(Request $request)
    {
        set_time_limit(0); // Prevent timeout when pushing to multiple devices

        $devices = FingerprintDevice::where('is_aktif', true)->get();

        if ($devices->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada perangkat mesin fingerprint yang aktif saat ini.');
        }

        $selectedIds = $request->input('selected_ids');
        if (is_string($selectedIds) && !empty($selectedIds)) {
            $selectedIds = json_decode($selectedIds, true) ?? explode(',', $selectedIds);
        }

        $query = Siswa::whereNotNull('fingerprint_id')
            ->where('fingerprint_id', '!=', '')
            ->where('status', 'aktif');

        if (!empty($selectedIds) && is_array($selectedIds)) {
            $query->whereIn('id', $selectedIds);
        }

        $siswas = $query->get();

        if ($siswas->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data siswa terpilih yang memiliki ID Fingerprint untuk dikirim ke mesin.');
        }

        $service = app(\App\Services\FingerprintService::class);
        $totalPushed = 0;
        $deviceMsg = [];
        $pushedSiswaIds = [];

        foreach ($devices as $device) {
            $count = 0;
            foreach ($siswas as $siswa) {
                try {
                    $service->uploadUserName($device, (string) $siswa->fingerprint_id, $siswa->nama);
                } catch (\Throwable $e) {}

                // Queue perintah ADMS agar mesin fisik mengunduh nama siswa via HTTPS ADMS polling
                if (!empty($siswa->fingerprint_id)) {
                    $admsCmd = "DATA USER PIN={$siswa->fingerprint_id}\tName={$siswa->nama}\tPri=0\tPass=\tCard=\tGrp=1\tTZ=0000000100000000";
                    \App\Http\Controllers\Absensi\AdmsController::queueCommand($admsCmd);
                }

                $count++;
                $totalPushed++;
                $pushedSiswaIds[] = $siswa->id;
            }
            // Kirim RefreshDB jika device dapat dijangkau
            try {
                $service->refreshDB($device);
            } catch (\Throwable $e) {}
            $deviceMsg[] = "{$device->nama}: {$count} siswa";
        }

        if (!empty($pushedSiswaIds)) {
            Siswa::whereIn('id', array_unique($pushedSiswaIds))->update([
                'is_pushed'    => true,
                'is_enrolled'  => true,
            ]);
        }

        $summary = implode(', ', $deviceMsg);
        $siswaCountText = count($siswas) . " siswa";

        if (auth()->check()) {
            activity()->causedBy(auth()->user())->log("POST & Sinkronisasi {$siswaCountText} ke mesin fingerprint");
        }

        return redirect()->back()->with('success', "Berhasil POST & Sinkronisasi {$siswaCountText} ke mesin fingerprint! ({$summary})");
    }

    /**
     * Otomatis kirim nama dan fingerprint ID siswa ke seluruh perangkat fingerprint aktif
     */
    protected function autoPushFingerprintToDevices(Siswa $siswa)
    {
        if (empty($siswa->fingerprint_id) || $siswa->status !== 'aktif') {
            return;
        }

        try {
            $service = app(\App\Services\FingerprintService::class);
            $devices = FingerprintDevice::where('is_aktif', true)->get();
            $successAny = false;

            foreach ($devices as $device) {
                $res = $service->uploadUserName($device, (string) $siswa->fingerprint_id, $siswa->nama);
                if ($res['success']) {
                    $service->refreshDB($device);
                    $successAny = true;
                }
            }

            if ($successAny) {
                $siswa->update(['is_pushed' => true]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Auto push fingerprint error: " . $e->getMessage());
        }
    }

    /**
     * Tandai siswa terpilih sebagai Lulus + hapus dari semua perangkat mesin fingerprint aktif
     */
    public function markLulus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer',
        ]);

        $ids = $request->input('ids');
        $siswas = Siswa::whereIn('id', $ids)->get();

        if ($siswas->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada siswa yang dipilih.');
        }

        // Update status menjadi lulus + reset enrollment (tidak aktif di mesin)
        Siswa::whereIn('id', $ids)->update([
            'status'      => 'lulus',
            'is_enrolled' => false,
            'is_pushed'   => true, // Tandai true agar tidak muncul di daftar unpushed/Perlu Post
        ]);

        // Hapus dari seluruh perangkat mesin fingerprint yang aktif & queue ADMS delete
        foreach ($siswas as $siswa) {
            if (!empty($siswa->fingerprint_id)) {
                $admsCmd = "DATA DELETE USER PIN={$siswa->fingerprint_id}";
                \App\Http\Controllers\Absensi\AdmsController::queueCommand($admsCmd);
            }
        }

        $activeDevices = FingerprintDevice::where('is_aktif', true)->get();
        if ($activeDevices->isNotEmpty()) {
            try {
                $service = app(\App\Services\FingerprintService::class);
                foreach ($activeDevices as $dev) {
                    foreach ($siswas as $siswa) {
                        if (!empty($siswa->fingerprint_id)) {
                            $service->deleteUser($dev, (string) $siswa->fingerprint_id);
                        }
                    }
                    $service->refreshDB($dev);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("markLulus delete from device error: " . $e->getMessage());
            }
        }

        $count = $siswas->count();
        if (auth()->check()) {
            activity()->causedBy(auth()->user())->log("Menandai {$count} siswa sebagai Lulus dan menghapus dari mesin fingerprint.");
        }

        return redirect()->route('siswa.index')
            ->with('success', "{$count} siswa berhasil ditandai sebagai Lulus dan telah dihapus dari perangkat mesin fingerprint.");
    }

    /**
     * Tandai satu siswa sebagai Keluar + hapus dari semua perangkat mesin fingerprint aktif
     */
    public function markKeluar(Siswa $siswa)
    {
        $siswa->update([
            'status'      => 'keluar',
            'is_enrolled' => false,
            'is_pushed'   => true, // Tandai true agar tidak muncul di daftar unpushed/Perlu Post
        ]);

        if (!empty($siswa->fingerprint_id)) {
            $admsCmd = "DATA DELETE USER PIN={$siswa->fingerprint_id}";
            \App\Http\Controllers\Absensi\AdmsController::queueCommand($admsCmd);

            $activeDevices = FingerprintDevice::where('is_aktif', true)->get();
            try {
                $service = app(\App\Services\FingerprintService::class);
                foreach ($activeDevices as $dev) {
                    $service->deleteUser($dev, (string) $siswa->fingerprint_id);
                    $service->refreshDB($dev);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("markKeluar delete from device error: " . $e->getMessage());
            }
        }

        if (auth()->check()) {
            activity()->causedBy(auth()->user())->performedOn($siswa)->log("Menandai siswa sebagai Keluar: {$siswa->nama}");
        }

        return redirect()->route('siswa.index')
            ->with('success', "Siswa {$siswa->nama} berhasil ditandai sebagai Keluar dan telah dihapus dari perangkat mesin fingerprint.");
    }

    /**
     * Tandai siswa sebagai Aktif kembali + otomatis push ke mesin fingerprint aktif
     */
    public function markAktif(Siswa $siswa)
    {
        $siswa->update([
            'status'      => 'aktif',
            'is_pushed'   => false,
            'is_enrolled' => false, // Perlu di-push ulang ke mesin agar enrolled kembali
        ]);

        // Auto push ke mesin fingerprint jika ada ID Fingerprint
        $this->autoPushFingerprintToDevices($siswa);

        if (auth()->check()) {
            activity()->causedBy(auth()->user())->performedOn($siswa)->log("Menandai siswa sebagai Aktif kembali: {$siswa->nama}");
        }

        return redirect()->route('siswa.index')
            ->with('success', "Siswa {$siswa->nama} berhasil diaktifkan kembali. Data siswa telah di-POST ulang ke mesin fingerprint.");
    }
}
