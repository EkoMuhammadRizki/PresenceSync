<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::withCount(['kelas', 'mataPelajarans'])->latest()->get();
        return view('pages.absensi.guru', compact('gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|max:150|unique:users,email|unique:gurus,email',
            'password' => 'required|string|min:6',
            'nama'     => 'required|string|max:150',
            'nip'      => 'nullable|string|max:30|unique:gurus,nip',
            'no_hp'    => 'nullable|string|max:20',
            'alamat'   => 'nullable|string',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min'   => 'Password minimal harus 6 karakter.',
            'nama.required'  => 'Nama guru wajib diisi.',
            'nip.unique'     => 'NIP sudah terdaftar.',
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

        Guru::create([
            'user_id' => $user->id,
            'nama'    => $request->nama,
            'nip'     => $request->nip,
            'email'   => $request->email,
            'no_hp'   => $request->no_hp,
            'alamat'  => $request->alamat,
        ]);

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nama'   => 'required|string|max:150',
            'nip'    => 'nullable|string|max:30|unique:gurus,nip,' . $guru->id,
            'email'  => 'nullable|email|max:150|unique:gurus,email,' . $guru->id . '|unique:users,email,' . ($guru->user_id ?? 0),
            'no_hp'  => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ], [
            'nama.required' => 'Nama guru wajib diisi.',
            'nip.unique'    => 'NIP sudah terdaftar.',
            'email.unique'  => 'Email sudah terdaftar.',
            'email.email'   => 'Format email tidak valid.',
        ]);

        $guru->update($request->only('nama', 'nip', 'email', 'no_hp', 'alamat'));

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

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Guru $guru)
    {
        if ($guru->kelas()->exists() || $guru->mataPelajarans()->exists()) {
            return redirect()->route('guru.index')
                ->with('error', 'Guru tidak dapat dihapus karena masih memiliki data kelas atau mata pelajaran.');
        }

        $user = $guru->user;
        $guru->delete();

        if ($user) {
            $user->delete();
        }

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Guru');

        $headers = [
            'email',
            'password',
            'kata_sandi',
            'jenis_pengguna',
            'nip',
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

        // Populate existing teachers if any, otherwise write a sample row
        $gurus = Guru::with('user')->orderBy('nama')->get();

        if ($gurus->isEmpty()) {
            $sheet->setCellValue('A2', 'budi.santoso@sekolah.sch.id');
            $sheet->setCellValue('B2', 'password123');
            $sheet->setCellValue('C2', 'password123');
            $sheet->setCellValue('D2', 'guru');
            $sheet->setCellValue('E2', '198105122008011003');
            $sheet->setCellValue('F2', 'Budi Santoso, S.Pd');
            $sheet->setCellValue('G2', '');
            $sheet->setCellValue('H2', 'L');
            $sheet->setCellValue('I2', 'Jakarta');
            $sheet->setCellValue('J2', '1981-05-12');
            $sheet->setCellValue('K2', 'Jl. Sukasenang No. 12');
            $sheet->setCellValue('L2', '081234567890');
            $sheet->setCellValue('M2', '');
            $sheet->setCellValue('N2', 'aktif');
            $sheet->setCellValue('O2', '');
        } else {
            $rowNum = 2;
            foreach ($gurus as $guru) {
                $sheet->setCellValue('A' . $rowNum, $guru->user->email ?? $guru->email ?? '');
                $sheet->setCellValue('B' . $rowNum, 'password123');
                $sheet->setCellValue('C' . $rowNum, 'password123');
                $sheet->setCellValue('D' . $rowNum, 'guru');
                $sheet->setCellValue('E' . $rowNum, $guru->nip ?? '');
                $sheet->setCellValue('F' . $rowNum, $guru->nama);
                $sheet->setCellValue('G' . $rowNum, '');
                $sheet->setCellValue('H' . $rowNum, 'L');
                $sheet->setCellValue('I' . $rowNum, '');
                $sheet->setCellValue('J' . $rowNum, '');
                $sheet->setCellValue('K' . $rowNum, $guru->alamat ?? '');
                $sheet->setCellValue('L' . $rowNum, $guru->no_hp ?? '');
                $sheet->setCellValue('M' . $rowNum, '');
                $sheet->setCellValue('N' . $rowNum, 'aktif');
                $sheet->setCellValue('O' . $rowNum, '');
                $rowNum++;
            }
        }

        $sheet->getStyle('A1:O1')->getFont()->setBold(true);

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
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes'    => 'Format file harus berupa .xlsx atau .xls.',
        ]);

        $file = $request->file('file');

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet   = $spreadsheet->getActiveSheet();
            $rows        = $worksheet->toArray();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal membaca file Excel: ' . $e->getMessage()]);
        }

        if (count($rows) <= 1) {
            return redirect()->back()->withErrors(['error' => 'File Excel kosong atau hanya berisi header.']);
        }

        array_shift($rows); // Buang header

        $successCount = 0;
        $skipCount    = 0;

        foreach ($rows as $row) {
            // Skip baris jika nama (kolom F / index 5) kosong
            if (empty($row[5])) {
                continue;
            }

            $inputEmail = !empty($row[0]) ? trim($row[0]) : null;
            $inputPassword = !empty($row[1]) ? trim($row[1]) : (!empty($row[2]) ? trim($row[2]) : null);
            $jenisPengguna = !empty($row[3]) ? trim($row[3]) : 'guru';
            $nip   = !empty($row[4]) ? trim($row[4]) : null;
            $nama  = trim($row[5]);
            $alamat = !empty($row[10]) ? trim($row[10]) : null;
            $noHp  = !empty($row[11]) ? trim($row[11]) : null;

            // Parse email
            $email = $inputEmail;
            if (empty($email)) {
                $baseEmail = Str::slug($nama, '.') . '@sekolah.sch.id';
                $email = $baseEmail;
                $counter = 1;
                while (User::where('email', $email)->exists()) {
                    $email = Str::slug($nama, '.') . $counter . '@sekolah.sch.id';
                    $counter++;
                }
            }

            // Pengecekan duplikat berdasarkan NIP atau email
            $exists = false;
            if ($nip) {
                $exists = Guru::where('nip', $nip)->exists();
            }
            if (!$exists && $email) {
                $exists = User::where('email', $email)->exists();
            }

            if ($exists) {
                $skipCount++;
                continue;
            }

            $password = $inputPassword ?? $nip ?? 'password123';

            $nameParts = explode(' ', trim($nama), 2);
            $firstName = $nameParts[0];
            $lastName  = $nameParts[1] ?? $nameParts[0];

            $user = User::create([
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $email,
                'password'   => Hash::make($password),
            ]);

            Guru::create([
                'user_id' => $user->id,
                'nama'    => $nama,
                'nip'     => $nip,
                'email'   => $email,
                'no_hp'   => $noHp,
                'alamat'  => $alamat,
            ]);

            $successCount++;
        }

        return redirect()->route('guru.index')
            ->with('import_success', [
                'success_count' => $successCount,
                'skip_count'    => $skipCount,
            ]);
    }
}
