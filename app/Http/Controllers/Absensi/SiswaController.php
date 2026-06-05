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
        $siswas = Siswa::with(['kelas.jurusan', 'user'])->latest()->get();
        $kelas  = Kelas::with('jurusan')->where('status', 'aktif')->orderBy('tingkat')->get();
        
        // Dapatkan user yang belum dikaitkan dengan data siswa manapun
        $siswaUserIds = Siswa::pluck('user_id')->filter()->toArray();
        $users = User::whereNotIn('id', $siswaUserIds)->orderBy('email')->get();
        
        return view('pages.absensi.siswa', compact('siswas', 'kelas', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id|unique:siswas,user_id',
            'nama'          => 'required|string|max:150',
            'nisn'          => 'nullable|string|max:20|unique:siswas,nisn',
            'nis'           => 'nullable|string|max:20|unique:siswas,nis',
            'kelas_id'      => 'nullable|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat'        => 'nullable|string',
        ], [
            'user_id.required'       => 'Username/Akun wajib dipilih.',
            'user_id.unique'         => 'Akun ini sudah dikaitkan dengan siswa lain.',
            'nama.required'          => 'Nama siswa wajib diisi.',
            'nisn.unique'            => 'NISN sudah terdaftar.',
            'nis.unique'             => 'NIS sudah terdaftar.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
        ]);

        Siswa::create($request->only('user_id', 'nama', 'nisn', 'nis', 'kelas_id', 'jenis_kelamin', 'tanggal_lahir', 'alamat'));

        return redirect()->route('siswa.index')
            ->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama'          => 'required|string|max:150',
            'nisn'          => 'nullable|string|max:20|unique:siswas,nisn,' . $siswa->id,
            'nis'           => 'nullable|string|max:20|unique:siswas,nis,' . $siswa->id,
            'kelas_id'      => 'nullable|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat'        => 'nullable|string',
            'fingerprint_id'=> 'nullable|string|max:50|unique:siswas,fingerprint_id,' . $siswa->id,
        ], [
            'nisn.unique'           => 'NISN sudah terdaftar.',
            'nis.unique'            => 'NIS sudah terdaftar.',
            'fingerprint_id.unique' => 'ID fingerprint sudah terdaftar pada siswa lain.',
        ]);

        $siswa->update($request->only(
            'nama', 'nisn', 'nis', 'kelas_id', 'jenis_kelamin', 'tanggal_lahir', 'alamat', 'fingerprint_id'
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

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        
        // Sheet 1: Template Siswa
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Siswa');
        
        // Headers
        $headers = [
            'Nama',
            'NISN',
            'NIS',
            'Kelas',
            'Jenis Kelamin (L/P)',
            'Tanggal Lahir (YYYY-MM-DD)',
            'Alamat'
        ];
        
        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }
        
        // Sample Row
        $sheet->setCellValue('A2', 'Ahmad Subarjo');
        $sheet->setCellValue('B2', '0054321098');
        $sheet->setCellValue('C2', '10201');
        $sheet->setCellValue('D2', 'X-1');
        $sheet->setCellValue('E2', 'L');
        $sheet->setCellValue('F2', '2009-08-15');
        $sheet->setCellValue('G2', 'Jl. Sukarno Hatta No. 12');
        
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        
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

        $successCount = 0;
        $skipCount = 0;

        foreach ($rows as $row) {
            // Skip baris kosong
            if (empty($row[0])) {
                continue;
            }

            $nama = trim($row[0]);
            $nisn = !empty($row[1]) ? trim($row[1]) : null;
            $nis = !empty($row[2]) ? trim($row[2]) : null;
            $kelasName = !empty($row[3]) ? trim($row[3]) : null;
            $jk = !empty($row[4]) ? strtoupper(trim($row[4])) : 'L';
            $tanggalLahirRaw = !empty($row[5]) ? trim($row[5]) : null;
            $alamat = !empty($row[6]) ? trim($row[6]) : null;

            // Normalisasi jenis kelamin
            if ($jk !== 'P') {
                $jk = 'L';
            }

            // Pengecekan apakah siswa sudah ada di database (berdasarkan NISN atau NIS)
            $exists = false;
            if ($nisn) {
                $exists = Siswa::where('nisn', $nisn)->exists();
            }
            if (!$exists && $nis) {
                $exists = Siswa::where('nis', $nis)->exists();
            }

            if ($exists) {
                $skipCount++;
                continue;
            }

            // Cari kelas berdasarkan nama kelas
            $kelasId = null;
            if ($kelasName) {
                $kelas = Kelas::where('nama', $kelasName)->first();
                if ($kelas) {
                    $kelasId = $kelas->id;
                }
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

            // Buat akun user secara otomatis untuk siswa
            $baseEmail = Str::slug($nama, '.') . '@siswa.presencesync.sch.id';
            $email = $baseEmail;
            $counter = 1;
            while (User::where('email', $email)->exists()) {
                $email = Str::slug($nama, '.') . $counter . '@siswa.presencesync.sch.id';
                $counter++;
            }

            $password = $nisn ?? $nis ?? 'password123';

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
                'user_id'       => $user->id,
                'kelas_id'      => $kelasId,
                'nama'          => $nama,
                'nisn'          => $nisn,
                'nis'           => $nis,
                'jenis_kelamin' => $jk,
                'tanggal_lahir' => $tanggalLahir,
                'alamat'        => $alamat,
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
