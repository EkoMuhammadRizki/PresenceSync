<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\FingerprintDevice;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SiswaController extends Controller
{
    public function index()
    {
        $siswas = Siswa::with(['kelas', 'user'])->latest()->get();
        $kelas  = Kelas::where('status', 'aktif')->orderBy('tingkat')->get();
        
        // Dapatkan user yang belum dikaitkan dengan data siswa manapun
        $siswaUserIds = Siswa::pluck('user_id')->filter()->toArray();
        $users = User::whereNotIn('id', $siswaUserIds)->orderBy('email')->get();
        
        $unpushedCount = Siswa::where('is_pushed', false)->count();

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

        $siswa->update($request->only(
            'nama', 'nisn', 'nis', 'kelas_id', 'jenis_kelamin', 'tanggal_lahir', 'alamat', 'status'
        ));

        $newFingerprintId = $request->filled('fingerprint_id')
            ? (string) $request->fingerprint_id
            : (empty($siswa->fingerprint_id) ? (string) $siswa->id : (string) $siswa->fingerprint_id);

        // Jika fingerprint_id berubah, hapus PIN lama dari seluruh device aktif
        if (!empty($oldFingerprintId) && $oldFingerprintId !== $newFingerprintId) {
            try {
                $service = app(\App\Services\FingerprintService::class);
                $activeDevices = FingerprintDevice::where('is_aktif', true)->get();
                foreach ($activeDevices as $dev) {
                    $service->deleteUser($dev, $oldFingerprintId);
                    $service->refreshDB($dev);
                }
            } catch (\Throwable $e) {}
        }

        // Tandai is_pushed = false agar ke-flag untuk di-post ulang ke mesin
        $siswa->update([
            'fingerprint_id' => $newFingerprintId,
            'is_pushed'      => false,
        ]);

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
        $oldFingerprintId = (string) $siswa->fingerprint_id;
        $user = $siswa->user;
        $siswa->delete();

        if ($user) {
            $user->delete();
        }

        if (!empty($oldFingerprintId)) {
            try {
                $service = app(\App\Services\FingerprintService::class);
                $activeDevices = FingerprintDevice::where('is_aktif', true)->get();
                foreach ($activeDevices as $dev) {
                    $service->deleteUser($dev, $oldFingerprintId);
                    $service->refreshDB($dev);
                }
            } catch (\Throwable $e) {}
        }

        if (auth()->check()) {
            activity()->causedBy(auth()->user())->log("Menghapus data siswa: {$siswa->nama}");
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
        
        // Headers disamakan dengan urutan tabel web: NIS | NAMA | JENIS KELAMIN | KELAS | FINGERPRINT ID
        $headers = [
            'nis',
            'nama',
            'jenis_kelamin',
            'kelas',
            'id_fingerprint',
            'tempat_lahir',
            'tanggal_lahir',
            'alamat',
            'no_hp',
            'no_hp_orang_tua',
            'status'
        ];
        
        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }
        
        // Populate existing students if requested (default / when not 'empty')
        if (!$request->has('empty')) {
            $siswas = Siswa::with(['kelas'])->orderBy('nama')->get();
            $rowNum = 2;
            foreach ($siswas as $siswa) {
                $sheet->setCellValue('A' . $rowNum, $siswa->nis ?? '');
                $sheet->setCellValue('B' . $rowNum, $siswa->nama);
                $sheet->setCellValue('C' . $rowNum, $siswa->jenis_kelamin);
                $sheet->setCellValue('D' . $rowNum, $siswa->kelas->nama ?? '');
                $sheet->setCellValue('E' . $rowNum, $siswa->fingerprint_id ?? '');
                $sheet->setCellValue('F' . $rowNum, $siswa->tempat_lahir ?? '');
                $sheet->setCellValue('G' . $rowNum, $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('Y-m-d') : '');
                $sheet->setCellValue('H' . $rowNum, $siswa->alamat ?? '');
                $sheet->setCellValue('I' . $rowNum, $siswa->no_hp ?? '');
                $sheet->setCellValue('J' . $rowNum, $siswa->no_hp_orang_tua ?? '');
                $sheet->setCellValue('K' . $rowNum, $siswa->status ?? 'aktif');
                $rowNum++;
            }
        }
        
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        
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
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes'    => 'Format file harus berupa .xlsx atau .xls.',
        ]);

        $file = $request->file('file');
        
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal membaca file Excel: ' . $e->getMessage()]);
        }

        if (count($rows) <= 1) {
            return redirect()->back()->withErrors(['error' => 'File Excel kosong atau hanya berisi header.']);
        }
        
        $header = array_shift($rows);

        // Validasi format template agar guru tidak diimport ke siswa
        $column1 = isset($header[0]) ? strtolower(trim($header[0])) : '';
        if ($column1 === 'nip' || $worksheet->getTitle() === 'Template Guru') {
            return redirect()->back()->withErrors(['error' => 'File Excel yang diunggah adalah template Guru. Silakan unggah file template Siswa yang benar.']);
        }
        if ($column1 !== 'nis') {
            return redirect()->back()->withErrors(['error' => 'Format template tidak sesuai. Pastikan Anda menggunakan file template Siswa yang diunduh dari sistem.']);
        }

        $successCount = 0;
        $skipCount = 0;

        foreach ($rows as $row) {
            // Struktur kolom baru (sesuai urutan tabel web): nis(0) | nama(1) | jenis_kelamin(2) | kelas(3) | id_fingerprint(4) | tempat_lahir(5) | tanggal_lahir(6) | alamat(7) | no_hp(8) | no_hp_orang_tua(9) | status(10)
            // Skip baris jika nama (kolom B / index 1) kosong
            if (empty($row[1])) {
                continue;
            }

            $nis           = !empty($row[0]) ? trim($row[0]) : null;
            $nama          = trim($row[1]);
            $jk            = !empty($row[2]) ? strtoupper(trim($row[2])) : 'L';
            $kelasName     = !empty($row[3]) ? trim($row[3]) : null;
            $fingerprintId = !empty($row[4]) ? trim($row[4]) : null;
            $tempatLahir   = !empty($row[5]) ? trim($row[5]) : null;
            $tanggalLahirRaw = !empty($row[6]) ? trim($row[6]) : null;
            $alamat        = !empty($row[7]) ? trim($row[7]) : null;
            $noHp          = !empty($row[8]) ? trim($row[8]) : null;
            $noHpOrangTua  = !empty($row[9]) ? trim($row[9]) : null;
            $status        = !empty($row[10]) ? trim($row[10]) : 'aktif';

            // NIS wajib ada
            if (empty($nis)) {
                $skipCount++;
                continue;
            }

            // Normalisasi jenis kelamin
            if ($jk !== 'P') {
                $jk = 'L';
            }

            // Cari kelas berdasarkan nama kelas
            $kelasId = null;
            if ($kelasName) {
                $kelas = Kelas::where('nama', $kelasName)->first();
                if ($kelas) {
                    $kelasId = $kelas->id;
                }
            }

            // Pengecekan duplikat berdasarkan NIS saja
            if (Siswa::where('nis', $nis)->exists()) {
                $skipCount++;
                continue;
            }

            // Generate email internal dari NIS
            $email = $nis . '@siswa.internal';
            $counter = 1;
            while (User::where('email', $email)->exists()) {
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
                        $tanggalLahir = date('Y-m-d', strtotime($tanggalLahirRaw));
                    } catch (\Exception $e) {
                        $tanggalLahir = null;
                    }
                }
            }

            $password = $nis; // Default password disamakan dengan NIS

            $nameParts = explode(' ', trim($nama), 2);
            $firstName = $nameParts[0];
            $lastName  = $nameParts[1] ?? $nameParts[0];

            $user = User::create([
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $email,
                'password'   => Hash::make($password),
            ]);

            $siswa = Siswa::create([
                'user_id'         => $user->id,
                'kelas_id'        => $kelasId,
                'nama'            => $nama,
                'nis'             => $nis,
                'jenis_kelamin'   => $jk,
                'tempat_lahir'    => $tempatLahir,
                'tanggal_lahir'   => $tanggalLahir,
                'alamat'          => $alamat,
                'no_hp'           => $noHp,
                'no_hp_orang_tua' => $noHpOrangTua,
                'status'          => in_array(strtolower($status ?? 'aktif'), ['aktif','lulus','keluar']) ? strtolower($status) : 'aktif',
                'is_pushed'       => false,
                'is_enrolled'     => false,
            ]);

            // Auto-assign ID Fingerprint dari ID Siswa
            $siswa->update(['fingerprint_id' => (string) $siswa->id]);

            $successCount++;
        }

        if (auth()->check() && $successCount > 0) {
            activity()->causedBy(auth()->user())->log("Import data siswa dari Excel ({$successCount} disimpen)");
        }

        return redirect()->route('siswa.index')
            ->with('import_success', [
                'success_count' => $successCount,
                'skip_count' => $skipCount,
            ]);
    }

    /**
     * Post/Push data siswa (seluruhnya atau terpilih) ke semua perangkat mesin fingerprint yang aktif
     */
    public function pushToDevices(Request $request)
    {
        $devices = FingerprintDevice::where('is_aktif', true)->get();

        if ($devices->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada perangkat mesin fingerprint yang aktif saat ini.');
        }

        $selectedIds = $request->input('selected_ids');
        if (is_string($selectedIds) && !empty($selectedIds)) {
            $selectedIds = json_decode($selectedIds, true) ?? explode(',', $selectedIds);
        }

        $query = Siswa::whereNotNull('fingerprint_id')
            ->where('fingerprint_id', '!=', '');

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
        if (empty($siswa->fingerprint_id)) {
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
            'is_pushed'   => false,
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
            'is_pushed'   => false,
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
