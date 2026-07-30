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
            'nip'      => 'required|string|max:30|unique:gurus,nip',
            'password' => 'required|string|min:6',
            'nama'     => 'required|string|max:150',
            'email'    => 'nullable|email|max:150|unique:users,email|unique:gurus,email',
            'no_hp'    => 'nullable|string|max:20',
            'alamat'   => 'nullable|string',
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
            'user_id' => $user->id,
            'nama'    => $request->nama,
            'nip'     => $request->nip,
            'email'   => $request->email,
            'no_hp'   => $request->no_hp,
            'alamat'  => $request->alamat,
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

        // Headers (NIP sebagai kolom pertama, tanpa kolom email & password)
        $headers = [
            'nip',
            'nama',
            'no_hp',
            'alamat',
            'status',
        ];

        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }

        // Populate existing teachers if requested (default / when not 'empty')
        if (!$request->has('empty')) {
            $gurus = Guru::orderBy('nama')->get();
            $rowNum = 2;
            foreach ($gurus as $guru) {
                $sheet->setCellValue('A' . $rowNum, $guru->nip ?? '');
                $sheet->setCellValue('B' . $rowNum, $guru->nama);
                $sheet->setCellValue('C' . $rowNum, $guru->no_hp ?? '');
                $sheet->setCellValue('D' . $rowNum, $guru->alamat ?? '');
                $sheet->setCellValue('E' . $rowNum, 'aktif');
                $rowNum++;
            }
        }

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        // Note: NIP adalah kolom wajib
        $sheet->getComment('A1')->getText()->createTextRun('Wajib diisi. Digunakan untuk login guru.');

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
            return redirect()->back()->withErrors(['error' => 'File Excel yang diunggah adalah template Siswa. Silakan unggah file template Guru yang benar.']);
        }

        $header = array_shift($rows); // Buang header

        // Validasi format template agar siswa tidak diimport ke guru
        $column1 = isset($header[0]) ? strtolower(trim($header[0])) : '';
        if ($column1 === 'nis' || $worksheet->getTitle() === 'Template Siswa') {
            return redirect()->back()->withErrors(['error' => 'File Excel yang diunggah adalah template Siswa. Silakan unggah file template Guru yang benar.']);
        }
        if ($column1 !== 'nip') {
            return redirect()->back()->withErrors(['error' => 'Format template tidak sesuai. Pastikan Anda menggunakan file template Guru yang diunduh dari sistem.']);
        }

        $successCount = 0;
        $skipCount    = 0;

        foreach ($rows as $row) {
            // Struktur kolom baru: nip(0) | nama(1) | no_hp(2) | alamat(3) | status(4)
            // Skip baris jika nama (kolom B / index 1) kosong
            if (empty($row[1])) {
                continue;
            }

            $nip    = !empty($row[0]) ? trim($row[0]) : null;
            $nama   = trim($row[1]);
            $noHp   = !empty($row[2]) ? trim($row[2]) : null;
            $alamat = !empty($row[3]) ? trim($row[3]) : null;

            // NIP wajib ada
            if (empty($nip)) {
                $skipCount++;
                continue;
            }

            // Pengecekan duplikat berdasarkan NIP saja
            if (Guru::where('nip', $nip)->exists()) {
                $skipCount++;
                continue;
            }

            // Generate email internal dari NIP
            $email = $nip . '@guru.internal';
            $counter = 1;
            while (User::where('email', $email)->exists()) {
                $email = $nip . $counter . '@guru.internal';
                $counter++;
            }

            $password = $nip; // Default password disamakan dengan NIP

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

        if (auth()->check() && $successCount > 0) {
            activity()->causedBy(auth()->user())->log("Import data guru dari Excel ({$successCount} disimpen)");
        }

        return redirect()->route('guru.index')
            ->with('import_success', [
                'success_count' => $successCount,
                'skip_count'    => $skipCount,
            ]);
    }
}
