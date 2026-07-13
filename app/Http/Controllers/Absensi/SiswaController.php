<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
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
        
        return view('pages.absensi.siswa', compact('siswas', 'kelas', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email'         => 'required|email|max:255|unique:users,email',
            'password'      => 'required|string|min:6',
            'nama'          => 'required|string|max:150',
            'nis'           => 'nullable|regex:/^[0-9]+$/|max:20|unique:siswas,nis',
            'kelas_id'      => 'nullable|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat'        => 'nullable|string',
        ], [
            'email.required'         => 'Email wajib diisi.',
            'email.email'            => 'Format email tidak valid.',
            'email.unique'           => 'Email sudah digunakan oleh user lain.',
            'password.required'      => 'Password wajib diisi.',
            'password.min'           => 'Password minimal harus 6 karakter.',
            'nama.required'          => 'Nama siswa wajib diisi.',
            'nis.regex'              => 'NIS hanya boleh berisi angka.',
            'nis.max'                => 'NIS maksimal 20 digit.',
            'nis.unique'             => 'NIS sudah terdaftar.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
        ]);

        $nameParts = explode(' ', trim($request->nama), 2);
        $firstName = $nameParts[0];
        $lastName  = $nameParts[1] ?? $nameParts[0];

        $user = User::create([
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
        ]);

        Siswa::create([
            'user_id'       => $user->id,
            'nama'          => $request->nama,
            'nis'           => $request->nis,
            'kelas_id'      => $request->kelas_id,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat'        => $request->alamat,
        ]);

        return redirect()->route('siswa.index')
            ->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama'          => 'required|string|max:150',
            'nisn'          => 'nullable|string|max:20|unique:siswas,nisn,' . $siswa->id,
            'nis'           => 'nullable|regex:/^[0-9]+$/|max:20|unique:siswas,nis,' . $siswa->id,
            'kelas_id'      => 'nullable|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat'        => 'nullable|string',
        ], [
            'nisn.unique'           => 'NISN sudah terdaftar.',
            'nis.unique'            => 'NIS sudah terdaftar.',
            'nis.regex'             => 'NIS hanya boleh berisi angka.',
            'nis.max'               => 'NIS maksimal 20 digit.',
        ]);

        $siswa->update($request->only(
            'nama', 'nisn', 'nis', 'kelas_id', 'jenis_kelamin', 'tanggal_lahir', 'alamat'
        ));

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

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        // Hapus user terkait juga
        $user = $siswa->user;
        $siswa->delete();

        if ($user) {
            $user->delete();
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
        
        // Headers
        $headers = [
            'email',
            'password',
            'kata_sandi',
            'jenis_pengguna',
            'nis',
            'nama',
            'id_fingerprint',
            'jenis_kelamin',
            'tempat_lahir',
            'tanggal_lahir',
            'alamat',
            'no_hp',
            'no_hp_orang_tua',
            'status',
            'kelas'
        ];
        
        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }
        
        // Populate existing students if requested (default / when not 'empty')
        if (!$request->has('empty')) {
            $siswas = Siswa::with(['user', 'kelas'])->orderBy('nama')->get();
            $rowNum = 2;
            foreach ($siswas as $siswa) {
                $sheet->setCellValue('A' . $rowNum, $siswa->user->email ?? '');
                $sheet->setCellValue('B' . $rowNum, 'password123');
                $sheet->setCellValue('C' . $rowNum, 'password123');
                $sheet->setCellValue('D' . $rowNum, 'siswa');
                $sheet->setCellValue('E' . $rowNum, $siswa->nis ?? '');
                $sheet->setCellValue('F' . $rowNum, $siswa->nama);
                $sheet->setCellValue('G' . $rowNum, $siswa->fingerprint_id ?? '');
                $sheet->setCellValue('H' . $rowNum, $siswa->jenis_kelamin);
                $sheet->setCellValue('I' . $rowNum, $siswa->tempat_lahir ?? '');
                $sheet->setCellValue('J' . $rowNum, $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('Y-m-d') : '');
                $sheet->setCellValue('K' . $rowNum, $siswa->alamat ?? '');
                $sheet->setCellValue('L' . $rowNum, $siswa->no_hp ?? '');
                $sheet->setCellValue('M' . $rowNum, $siswa->no_hp_orang_tua ?? '');
                $sheet->setCellValue('N' . $rowNum, $siswa->status ?? 'aktif');
                $sheet->setCellValue('O' . $rowNum, $siswa->kelas->nama ?? '');
                $rowNum++;
            }
        }
        
        $sheet->getStyle('A1:O1')->getFont()->setBold(true);
        
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
        $column5 = isset($header[4]) ? strtolower(trim($header[4])) : '';
        if ($column5 === 'nip' || $worksheet->getTitle() === 'Template Guru') {
            return redirect()->back()->withErrors(['error' => 'File Excel yang diunggah adalah template Guru. Silakan unggah file template Siswa yang benar.']);
        }
        if ($column5 !== 'nis') {
            return redirect()->back()->withErrors(['error' => 'Format template tidak sesuai. Pastikan Anda menggunakan file template Siswa yang diunduh dari sistem.']);
        }

        $successCount = 0;
        $skipCount = 0;

        foreach ($rows as $row) {
            // Skip baris jika nama (kolom F / index 5) kosong
            if (empty($row[5])) {
                continue;
            }

            $inputEmail = !empty($row[0]) ? trim($row[0]) : null;
            $inputPassword = !empty($row[1]) ? trim($row[1]) : (!empty($row[2]) ? trim($row[2]) : null);
            $jenisPengguna = !empty($row[3]) ? trim($row[3]) : 'siswa';
            $nis = !empty($row[4]) ? trim($row[4]) : null;
            $nama = trim($row[5]);
            $jk = !empty($row[7]) ? strtoupper(trim($row[7])) : 'L';
            $tempatLahir = !empty($row[8]) ? trim($row[8]) : null;
            $tanggalLahirRaw = !empty($row[9]) ? trim($row[9]) : null;
            $alamat = !empty($row[10]) ? trim($row[10]) : null;
            $noHp = !empty($row[11]) ? trim($row[11]) : null;
            $noHpOrangTua = !empty($row[12]) ? trim($row[12]) : null;
            $status = !empty($row[13]) ? trim($row[13]) : 'aktif';
            $kelasName = !empty($row[14]) ? trim($row[14]) : null;

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

            // Parse email
            $email = $inputEmail;
            if (empty($email)) {
                $baseEmail = Str::slug($nama, '.') . '@siswa.presencesync.sch.id';
                $email = $baseEmail;
                $counter = 1;
                while (User::where('email', $email)->exists()) {
                    $email = Str::slug($nama, '.') . $counter . '@siswa.presencesync.sch.id';
                    $counter++;
                }
            }

            // Pengecekan apakah siswa sudah ada di database (berdasarkan email atau NIS)
            $exists = false;
            if ($nis) {
                $exists = Siswa::where('nis', $nis)->exists();
            }
            if (!$exists && $email) {
                $exists = User::where('email', $email)->exists();
            }

            if ($exists) {
                $skipCount++;
                continue;
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

            $password = $inputPassword ?? $nis ?? 'password123';

            $nameParts = explode(' ', trim($nama), 2);
            $firstName = $nameParts[0];
            $lastName  = $nameParts[1] ?? $nameParts[0];

            $user = User::create([
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $email,
                'password'   => Hash::make($password),
            ]);

            Siswa::create([
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
                'status'          => $status,
            ]);

            $successCount++;
        }

        return redirect()->route('siswa.index')
            ->with('import_success', [
                'success_count' => $successCount,
                'skip_count' => $skipCount,
            ]);
    }
}
