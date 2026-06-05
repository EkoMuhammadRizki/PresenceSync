<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;
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
            'nama'   => 'required|string|max:150',
            'nip'    => 'nullable|string|max:30|unique:gurus,nip',
            'email'  => 'nullable|email|max:150|unique:gurus,email',
            'no_hp'  => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ], [
            'nama.required' => 'Nama guru wajib diisi.',
            'nip.unique'    => 'NIP sudah terdaftar.',
            'email.unique'  => 'Email sudah terdaftar.',
            'email.email'   => 'Format email tidak valid.',
        ]);

        Guru::create($request->only('nama', 'nip', 'email', 'no_hp', 'alamat'));

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nama'   => 'required|string|max:150',
            'nip'    => 'nullable|string|max:30|unique:gurus,nip,' . $guru->id,
            'email'  => 'nullable|email|max:150|unique:gurus,email,' . $guru->id,
            'no_hp'  => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ], [
            'nama.required' => 'Nama guru wajib diisi.',
            'nip.unique'    => 'NIP sudah terdaftar.',
            'email.unique'  => 'Email sudah terdaftar.',
        ]);

        $guru->update($request->only('nama', 'nip', 'email', 'no_hp', 'alamat'));

        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Guru $guru)
    {
        if ($guru->kelas()->exists() || $guru->mataPelajarans()->exists()) {
            return redirect()->route('guru.index')
                ->with('error', 'Guru tidak dapat dihapus karena masih memiliki data kelas atau mata pelajaran.');
        }

        $guru->delete();
        return redirect()->route('guru.index')
            ->with('success', 'Data guru berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Guru');

        $headers = [
            'Nama',
            'NIP',
            'Email',
            'No HP',
            'Alamat',
        ];

        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }

        // Sample Row
        $sheet->setCellValue('A2', 'Drs. Budi Santoso, M.Pd.');
        $sheet->setCellValue('B2', '196504121990031012');
        $sheet->setCellValue('C2', 'budi.santoso@sekolah.sch.id');
        $sheet->setCellValue('D2', '081234567890');
        $sheet->setCellValue('E2', 'Jl. Merdeka No. 5, Jakarta');

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

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
            // Skip baris kosong
            if (empty($row[0])) {
                continue;
            }

            $nama  = trim($row[0]);
            $nip   = !empty($row[1]) ? trim($row[1]) : null;
            $email = !empty($row[2]) ? strtolower(trim($row[2])) : null;
            $noHp  = !empty($row[3]) ? trim($row[3]) : null;
            $alamat = !empty($row[4]) ? trim($row[4]) : null;

            // Pengecekan duplikat berdasarkan NIP atau email
            $exists = false;
            if ($nip) {
                $exists = Guru::where('nip', $nip)->exists();
            }
            if (!$exists && $email) {
                $exists = Guru::where('email', $email)->exists();
            }

            if ($exists) {
                $skipCount++;
                continue;
            }

            Guru::create([
                'nama'   => $nama,
                'nip'    => $nip,
                'email'  => $email,
                'no_hp'  => $noHp,
                'alamat' => $alamat,
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
