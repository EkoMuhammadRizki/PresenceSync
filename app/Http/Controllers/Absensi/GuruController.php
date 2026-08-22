<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::with(['kelas', 'user.info'])->withCount(['kelas', 'mataPelajarans'])->orderBy('nama', 'asc')->get();
        return view('pages.absensi.guru', compact('gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip'                => 'required|string|max:30|unique:gurus,nip',
            'password'           => 'required|string|min:6',
            'nama'               => 'required|string|max:150',
            'email'              => 'nullable|email|max:150|unique:users,email|unique:gurus,email',
            'jenis_kelamin'      => 'nullable|in:L,P',
            'tempat_lahir'       => 'nullable|string|max:100',
            'tanggal_lahir'      => 'nullable|date',
            'agama'              => 'nullable|string|max:50',
            'no_hp'              => 'nullable|string|max:20',
            'alamat'             => 'nullable|string',
            'status'             => 'nullable|string|max:50',
            'nik'                => 'nullable|string|max:20',
            'npwp'               => 'nullable|string|max:30',
            'nuptk'              => 'nullable|string|max:30',
            'status_kepegawaian' => 'nullable|string|max:100',
            'tugas_tambahan'     => 'nullable|string|max:150',
            'sk_cpns'            => 'nullable|string|max:100',
            'tanggal_cpns'       => 'nullable|date',
            'sk_pengangkatan'    => 'nullable|string|max:100',
            'tmt_pengangkatan'   => 'nullable|date',
            'lembaga_pengangkatan' => 'nullable|string|max:150',
            'pangkat_golongan'   => 'nullable|string|max:50',
        ], [
            'nip.required'      => 'NIP wajib diisi karena digunakan untuk login.',
            'nip.unique'        => 'NIP sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal harus 6 karakter.',
            'nama.required'     => 'Nama guru wajib diisi.',
            'email.unique'      => 'Email sudah terdaftar.',
            'email.email'       => 'Format email tidak valid.',
        ]);

        $nameParts = explode(' ', trim($request->nama), 2);
        $firstName = $nameParts[0];
        $lastName  = $nameParts[1] ?? $nameParts[0];

        // Generate email internal dari NIP jika tidak diinput
        $email = $request->email;
        if (empty($email)) {
            $email = $request->nip . '@guru.internal';
            $counter = 1;
            while (User::where('email', $email)->exists()) {
                $email = $request->nip . $counter . '@guru.internal';
                $counter++;
            }
        }

        $user = User::create([
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $email,
            'password'   => Hash::make($request->password),
        ]);

        Guru::create([
            'user_id'              => $user->id,
            'nama'                 => $request->nama,
            'nip'                  => $request->nip,
            'email'                => $request->email,
            'jenis_kelamin'        => $request->jenis_kelamin ?? 'L',
            'tempat_lahir'         => $request->tempat_lahir,
            'tanggal_lahir'        => $request->tanggal_lahir,
            'agama'                => $request->agama,
            'no_hp'                => $request->no_hp,
            'alamat'               => $request->alamat,
            'status'               => $request->status ?? 'aktif',
            'nik'                  => $request->nik,
            'npwp'                 => $request->npwp,
            'nuptk'                => $request->nuptk,
            'status_kepegawaian'   => $request->status_kepegawaian,
            'tugas_tambahan'       => $request->tugas_tambahan,
            'sk_cpns'              => $request->sk_cpns,
            'tanggal_cpns'         => $request->tanggal_cpns,
            'sk_pengangkatan'      => $request->sk_pengangkatan,
            'tmt_pengangkatan'     => $request->tmt_pengangkatan,
            'lembaga_pengangkatan' => $request->lembaga_pengangkatan,
            'pangkat_golongan'     => $request->pangkat_golongan,
        ]);

        if (auth()->check()) {
            activity()->causedBy(auth()->user())->log("Menambah data guru baru: {$request->nama}");
        }

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nama'               => 'required|string|max:150',
            'nip'                => 'nullable|string|max:30|unique:gurus,nip,' . $guru->id,
            'email'              => 'nullable|email|max:150|unique:gurus,email,' . $guru->id . '|unique:users,email,' . ($guru->user_id ?? 0),
            'jenis_kelamin'      => 'nullable|in:L,P',
            'tempat_lahir'       => 'nullable|string|max:100',
            'tanggal_lahir'      => 'nullable|date',
            'agama'              => 'nullable|string|max:50',
            'no_hp'              => 'nullable|string|max:20',
            'alamat'             => 'nullable|string',
            'status'             => 'nullable|string|max:50',
            'nik'                => 'nullable|string|max:20',
            'npwp'               => 'nullable|string|max:30',
            'nuptk'              => 'nullable|string|max:30',
            'status_kepegawaian' => 'nullable|string|max:100',
            'tugas_tambahan'     => 'nullable|string|max:150',
            'sk_cpns'            => 'nullable|string|max:100',
            'tanggal_cpns'       => 'nullable|date',
            'sk_pengangkatan'    => 'nullable|string|max:100',
            'tmt_pengangkatan'   => 'nullable|date',
            'lembaga_pengangkatan' => 'nullable|string|max:150',
            'pangkat_golongan'   => 'nullable|string|max:50',
        ], [
            'nama.required' => 'Nama guru wajib diisi.',
            'nip.unique'    => 'NIP sudah terdaftar.',
            'email.unique'  => 'Email sudah terdaftar.',
            'email.email'   => 'Format email tidak valid.',
        ]);

        $guru->update($request->only([
            'nama', 'nip', 'email', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
            'agama', 'no_hp', 'alamat', 'status', 'nik', 'npwp', 'nuptk',
            'status_kepegawaian', 'tugas_tambahan', 'sk_cpns', 'tanggal_cpns',
            'sk_pengangkatan', 'tmt_pengangkatan', 'lembaga_pengangkatan', 'pangkat_golongan'
        ]));

        if ($guru->user) {
            $nameParts = explode(' ', trim($request->nama), 2);
            $firstName = $nameParts[0];
            $lastName  = $nameParts[1] ?? $nameParts[0];
            
            $userData = [
                'first_name' => $firstName,
                'last_name'  => $lastName,
            ];
            
            if ($request->email) {
                $userData['email'] = $request->email;
            }
            
            $guru->user->update($userData);
        }

        if (auth()->check()) {
            activity()->causedBy(auth()->user())->log("Mengubah data guru: {$guru->nama}");
        }

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Guru $guru)
    {
        $user = $guru->user;
        $guru->delete();

        if ($user) {
            $user->delete();
        }

        if (auth()->check()) {
            activity()->causedBy(auth()->user())->log("Menghapus data guru: {$guru->nama}");
        }

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil dihapus.');
    }

    public function downloadTemplate(Request $request)
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Guru');

        // Headers (21 kolom sesuai template guru resmi)
        $headers = [
            'nip',
            'nama',
            'jenis_kelamin',
            'tempat_lahir',
            'tanggal_lahir',
            'agama',
            'alamat',
            'no_telepon',
            'email',
            'status',
            'nik',
            'npwp',
            'nuptk',
            'status_kepegawaian',
            'tugas_tambahan',
            'sk_cpns',
            'tanggal_cpns',
            'sk_pengangkatan',
            'tmt_pengangkatan',
            'lembaga_pengangkatan',
            'pangkat_golongan',
        ];

        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }

        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));

        // Style header: bold & background color
        $sheet->getStyle('A1:' . $lastColLetter . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $lastColLetter . '1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDEEFF');

        // Note: NIP adalah kolom wajib
        $sheet->getComment('A1')->getText()->createTextRun('Wajib diisi. Digunakan untuk login guru.');

        // Populate existing teachers if requested (default / when not 'empty')
        if (!$request->has('empty')) {
            $gurus = Guru::orderBy('nama')->get();
            $rowNum = 2;
            foreach ($gurus as $guru) {
                $sheet->setCellValue('A' . $rowNum, $guru->nip ?? '');
                $sheet->setCellValue('B' . $rowNum, $guru->nama);
                $sheet->setCellValue('C' . $rowNum, $guru->jenis_kelamin ?? '');
                $sheet->setCellValue('D' . $rowNum, $guru->tempat_lahir ?? '');
                $sheet->setCellValue('E' . $rowNum, $guru->tanggal_lahir ? $guru->tanggal_lahir->format('Y-m-d') : '');
                $sheet->setCellValue('F' . $rowNum, $guru->agama ?? '');
                $sheet->setCellValue('G' . $rowNum, $guru->alamat ?? '');
                $sheet->setCellValue('H' . $rowNum, $guru->no_hp ?? '');
                $sheet->setCellValue('I' . $rowNum, $guru->email ?? '');
                $sheet->setCellValue('J' . $rowNum, $guru->status ?? 'aktif');
                $sheet->setCellValue('K' . $rowNum, $guru->nik ?? '');
                $sheet->setCellValue('L' . $rowNum, $guru->npwp ?? '');
                $sheet->setCellValue('M' . $rowNum, $guru->nuptk ?? '');
                $sheet->setCellValue('N' . $rowNum, $guru->status_kepegawaian ?? '');
                $sheet->setCellValue('O' . $rowNum, $guru->tugas_tambahan ?? '');
                $sheet->setCellValue('P' . $rowNum, $guru->sk_cpns ?? '');
                $sheet->setCellValue('Q' . $rowNum, $guru->tanggal_cpns ? $guru->tanggal_cpns->format('Y-m-d') : '');
                $sheet->setCellValue('R' . $rowNum, $guru->sk_pengangkatan ?? '');
                $sheet->setCellValue('S' . $rowNum, $guru->tmt_pengangkatan ? $guru->tmt_pengangkatan->format('Y-m-d') : '');
                $sheet->setCellValue('T' . $rowNum, $guru->lembaga_pengangkatan ?? '');
                $sheet->setCellValue('U' . $rowNum, $guru->pangkat_golongan ?? '');
                $rowNum++;
            }
        }

        // Autofit columns
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="template_import_guru.xlsx"');
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
                $tempName = 'temp_import_guru_' . uniqid() . '.' . ($file->getClientOriginalExtension() ?: 'xlsx');
                $tempPath = storage_path('app/' . $tempName);
                $file->move(storage_path('app'), $tempName);
                $filePath = $tempPath;
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $worksheet   = $spreadsheet->getActiveSheet();
            $rows        = $worksheet->toArray();

            if (isset($tempPath) && file_exists($tempPath)) {
                @unlink($tempPath);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal membaca file Excel: ' . $e->getMessage()]);
        }

        if (count($rows) <= 1) {
            return redirect()->back()->withErrors(['error' => 'File Excel kosong atau hanya berisi header.']);
        }

        $rawHeader = array_shift($rows); // Buang header

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

        // Validasi format template agar siswa tidak diimport ke guru
        if (isset($headerMap['nis']) || $worksheet->getTitle() === 'Template Siswa') {
            return redirect()->back()->withErrors(['error' => 'File Excel yang diunggah adalah template Siswa. Silakan unggah file template Guru yang benar.']);
        }
        if (!isset($headerMap['nip']) && !isset($headerMap['nama'])) {
            return redirect()->back()->withErrors(['error' => 'Format template tidak sesuai. Kolom "nip" dan "nama" tidak ditemukan dalam file Excel.']);
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

        // Helper untuk parsing tanggal (Excel date number maupun string)
        $parseDate = function ($dateRaw) {
            if (empty($dateRaw)) {
                return null;
            }
            if (is_numeric($dateRaw) && $dateRaw > 20000) {
                try {
                    return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateRaw)->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            } else {
                try {
                    $parsed = date('Y-m-d', strtotime(str_replace('/', '-', $dateRaw)));
                    if ($parsed && $parsed !== '1970-01-01') {
                        return $parsed;
                    }
                } catch (\Exception $e) {
                    return null;
                }
            }
            return null;
        };

        $successCount  = 0;
        $skipCount     = 0;
        $importedNames = [];
        $skippedNames  = [];

        $existingNip = Guru::pluck('nip', 'nip')->all();
        $existingEmails = User::pluck('email', 'email')->all();

        $batchSize = 100;
        $currentBatch = 0;

        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                // Ambil nama
                $nama = $getVal($row, 'nama');
                if (empty($nama)) {
                    continue;
                }

                $nip                 = $getVal($row, 'nip');
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

                $tempatLahir         = $getVal($row, 'tempat_lahir');
                $tanggalLahirRaw     = $getVal($row, 'tanggal_lahir', 'tgl_lahir');
                $agama               = $getVal($row, 'agama');
                $alamat              = $getVal($row, 'alamat');
                $noHp                = substr($getVal($row, 'no_telepon', 'no_hp', 'telepon', 'nohp') ?? '', 0, 30) ?: null;
                $emailInput          = $getVal($row, 'email');
                $status              = strtolower($getVal($row, 'status') ?? 'aktif');
                $nik                 = substr($getVal($row, 'nik') ?? '', 0, 20) ?: null;
                $npwp                = substr($getVal($row, 'npwp') ?? '', 0, 30) ?: null;
                $nuptk               = substr($getVal($row, 'nuptk') ?? '', 0, 30) ?: null;
                $statusKepegawaian   = substr($getVal($row, 'status_kepegawaian') ?? '', 0, 100) ?: null;
                $tugasTambahan       = substr($getVal($row, 'tugas_tambahan') ?? '', 0, 150) ?: null;
                $skCpns              = substr($getVal($row, 'sk_cpns') ?? '', 0, 100) ?: null;
                $tanggalCpnsRaw      = $getVal($row, 'tanggal_cpns', 'tgl_cpns');
                $skPengangkatan      = substr($getVal($row, 'sk_pengangkatan') ?? '', 0, 100) ?: null;
                $tmtPengangkatanRaw  = $getVal($row, 'tmt_pengangkatan', 'tmt');
                $lembagaPengangkatan = substr($getVal($row, 'lembaga_pengangkatan') ?? '', 0, 150) ?: null;
                $pangkatGolongan     = substr($getVal($row, 'pangkat_golongan', 'pangkat', 'golongan') ?? '', 0, 50) ?: null;

                // NIP wajib ada
                if (empty($nip)) {
                    $skipCount++;
                    if (count($skippedNames) < 100) {
                        $skippedNames[] = [
                            'nama'   => $nama,
                            'nip'    => '-',
                            'alasan' => 'Kolom NIP kosong',
                        ];
                    }
                    continue;
                }

                // Jika NIP sudah ada di sistem, update datanya agar Jenis Kelamin tersinkron
                if (isset($existingNip[$nip])) {
                    $existingGuru = Guru::where('nip', $nip)->first();
                    if ($existingGuru) {
                        $updateData = ['jenis_kelamin' => $jk];
                        if ($nama) $updateData['nama'] = $nama;
                        if ($noHp) $updateData['no_hp'] = $noHp;
                        if ($emailInput) $updateData['email'] = $emailInput;
                        if ($nik) $updateData['nik'] = $nik;
                        if ($nuptk) $updateData['nuptk'] = $nuptk;
                        if ($npwp) $updateData['npwp'] = $npwp;
                        if ($tempatLahir) $updateData['tempat_lahir'] = $tempatLahir;
                        if ($agama) $updateData['agama'] = $agama;
                        if ($alamat) $updateData['alamat'] = $alamat;
                        if ($statusKepegawaian) $updateData['status_kepegawaian'] = $statusKepegawaian;
                        if ($pangkatGolongan) $updateData['pangkat_golongan'] = $pangkatGolongan;
                        $existingGuru->update($updateData);
                        $successCount++;
                        if (count($importedNames) < 100) {
                            $importedNames[] = [
                                'nama' => $nama,
                                'nip'  => $nip,
                            ];
                        }
                    }
                    continue;
                }

                // Generate email jika tidak diinput
                $email = $emailInput;
                if (empty($email)) {
                    $email = $nip . '@guru.internal';
                    $counter = 1;
                    while (isset($existingEmails[$email])) {
                        $email = $nip . $counter . '@guru.internal';
                        $counter++;
                    }
                }

                $password = $nip; // Default password disamakan dengan NIP

                $nameParts = explode(' ', trim($nama), 2);
                $firstName = $nameParts[0];
                $lastName  = $nameParts[1] ?? $nameParts[0];

                $user = User::create([
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'email'      => $email,
                    'password'   => Hash::make($password, ['rounds' => 6]),
                ]);

                $existingEmails[$email] = $email;

                Guru::create([
                    'user_id'              => $user->id,
                    'nama'                 => $nama,
                    'nip'                  => $nip,
                    'jenis_kelamin'        => $jk,
                    'tempat_lahir'         => $tempatLahir,
                    'tanggal_lahir'        => $parseDate($tanggalLahirRaw),
                    'agama'                => $agama,
                    'alamat'               => $alamat,
                    'no_hp'                => $noHp,
                    'email'                => $email,
                    'status'               => in_array($status, ['aktif', 'nonaktif', 'cuti']) ? $status : 'aktif',
                    'nik'                  => $nik,
                    'npwp'                 => $npwp,
                    'nuptk'                => $nuptk,
                    'status_kepegawaian'   => $statusKepegawaian,
                    'tugas_tambahan'       => $tugasTambahan,
                    'sk_cpns'              => $skCpns,
                    'tanggal_cpns'         => $parseDate($tanggalCpnsRaw),
                    'sk_pengangkatan'      => $skPengangkatan,
                    'tmt_pengangkatan'     => $parseDate($tmtPengangkatanRaw),
                    'lembaga_pengangkatan' => $lembagaPengangkatan,
                    'pangkat_golongan'     => $pangkatGolongan,
                ]);

                $existingNip[$nip] = $nip;

                if (count($importedNames) < 100) {
                    $importedNames[] = [
                        'nama' => $nama,
                        'nip'  => $nip,
                    ];
                }
                $successCount++;
                $currentBatch++;

                if ($currentBatch >= $batchSize) {
                    DB::commit();
                    DB::beginTransaction();
                    $currentBatch = 0;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat memproses data guru: ' . $e->getMessage()]);
        }

        if (auth()->check() && $successCount > 0) {
            activity()->causedBy(auth()->user())->log("Import data guru dari Excel ({$successCount} disimpan)");
        }

        return redirect()->route('guru.index')
            ->with('import_success', [
                'success_count'  => $successCount,
                'skip_count'     => $skipCount,
                'imported_names' => $importedNames,
                'skipped_names'  => $skippedNames,
            ]);
    }
}
