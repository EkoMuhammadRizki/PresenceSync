<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MataPelajaranController extends Controller
{
    public function index()
    {
        $mataPelajarans = MataPelajaran::with('guru')->orderBy('nama')->get();
        $gurus          = Guru::orderBy('nama')->get();
        return view('pages.absensi.mata-pelajaran', compact('mataPelajarans', 'gurus'));
    }

    public function show(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->load('guru', 'jadwalPelajarans.kelas');
        return view('pages.absensi.profil-mata-pelajaran', compact('mataPelajaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'    => 'required|string|max:150',
            'guru_id' => 'nullable|exists:gurus,id',
            'kode'    => 'nullable|string|max:50',
        ], [
            'nama.required' => 'Nama mata pelajaran wajib diisi.',
        ]);

        $kode = !empty($request->kode) 
            ? strtoupper(trim($request->kode)) 
            : $this->generateCleanKode($request->nama);

        MataPelajaran::create([
            'nama'    => $request->nama,
            'kode'    => $kode,
            'guru_id' => $request->guru_id,
        ]);

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $request->validate([
            'nama'    => 'required|string|max:150',
            'guru_id' => 'nullable|exists:gurus,id',
            'kode'    => 'nullable|string|max:50',
        ], [
            'nama.required' => 'Nama mata pelajaran wajib diisi.',
        ]);

        $kode = !empty($request->kode) 
            ? strtoupper(trim($request->kode)) 
            : $this->generateCleanKode($request->nama);

        $mataPelajaran->update([
            'nama'    => $request->nama,
            'kode'    => $kode,
            'guru_id' => $request->guru_id,
        ]);

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->delete();
        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    /**
     * Download Excel template for importing mata pelajaran (Template Kosong)
     */
    public function downloadTemplate(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Matpel');

        // Headers: id_matpel, kd_matpel, nama_matpel, nama_guru
        $headers = ['id_matpel', 'kd_matpel', 'nama_matpel', 'nama_guru'];
        $colIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($colIndex . '1', $header);
            $colIndex++;
        }

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '009EF7']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ]
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Template kosong tanpa data baris contoh
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_matpel.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Import Mata Pelajaran from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes'    => 'Format file harus berupa .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal adalah 20MB.',
        ]);

        $file = $request->file('file');

        try {
            $filePath = $file->getRealPath();
            if (empty($filePath) || !file_exists($filePath)) {
                $filePath = $file->getPathname();
            }
            if (empty($filePath) || !file_exists($filePath)) {
                $tempName = 'temp_import_matpel_' . uniqid() . '.' . ($file->getClientOriginalExtension() ?: 'xlsx');
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

        // Smart Header Detection
        $headerRowIndex = 0;
        $maxHeaderCheck = min(20, count($rows));
        for ($i = 0; $i < $maxHeaderCheck; $i++) {
            $r = $rows[$i];
            $matchCount = 0;
            foreach ($r as $cell) {
                if ($cell === null || trim((string)$cell) === '') continue;
                $clean = strtolower(trim((string)$cell));
                $clean = preg_replace('/[^a-z0-9]/', '', $clean);
                if (in_array($clean, [
                    'idmatpel', 'kdmatpel', 'namamatpel', 'kode', 'nama', 'kodematpel',
                    'kodemapel', 'namamapel', 'matapelajarannama', 'matapelajaran', 'mapel',
                    'guru', 'namaguru', 'gurupengampu', 'pengampu', 'no'
                ])) {
                    $matchCount++;
                }
            }
            if ($matchCount >= 2) {
                $headerRowIndex = $i;
                break;
            }
        }

        $rawHeader = $rows[$headerRowIndex];
        $dataRows = array_slice($rows, $headerRowIndex + 1);

        $headerMap = [];
        foreach ($rawHeader as $index => $colName) {
            if ($colName !== null && trim((string)$colName) !== '') {
                $cleanName = strtolower(trim((string)$colName));
                $cleanName = preg_replace('/[^a-z0-9_]/', '_', str_replace([' ', '-', '.', '/'], '_', $cleanName));
                $cleanName = preg_replace('/_+/', '_', $cleanName);
                $cleanName = trim($cleanName, '_');
                $headerMap[$cleanName] = $index;
            }
        }

        $getVal = function ($row, ...$aliases) use ($headerMap) {
            foreach ($aliases as $alias) {
                $cleanAlias = strtolower(trim($alias));
                $cleanAlias = preg_replace('/[^a-z0-9_]/', '_', str_replace([' ', '-', '.', '/'], '_', $cleanAlias));
                $cleanAlias = preg_replace('/_+/', '_', $cleanAlias);
                $cleanAlias = trim($cleanAlias, '_');
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

        // Cache all gurus for fast matching
        $allGurus = Guru::all();
        $findGuruId = function ($rawGuru) use ($allGurus) {
            if (empty($rawGuru)) return null;
            $clean = strtolower(trim(preg_replace('/[^a-zA-Z0-9\s]/', '', $rawGuru)));
            if (empty($clean)) return null;

            // 1. Exact match
            foreach ($allGurus as $g) {
                $gClean = strtolower(trim(preg_replace('/[^a-zA-Z0-9\s]/', '', $g->nama)));
                if ($gClean === $clean) return $g->id;
            }
            // 2. Partial match
            foreach ($allGurus as $g) {
                $gClean = strtolower(trim(preg_replace('/[^a-zA-Z0-9\s]/', '', $g->nama)));
                if (str_contains($gClean, $clean) || str_contains($clean, $gClean)) return $g->id;
            }
            return null;
        };

        $successCount = 0;
        $skipCount = 0;
        $importedNames = [];
        $skippedNames = [];

        foreach ($dataRows as $row) {
            $rawKode = $getVal($row, 'kd_matpel', 'kd_mapel', 'kode', 'kode_matpel', 'kode_mapel', 'kd_mata_pelajaran');
            $nama    = $getVal($row, 'nama_matpel', 'nama_mapel', 'nama', 'nama_mata_pelajaran', 'mata_pelajaran', 'matpel', 'mapel');
            $rawGuru = $getVal($row, 'nama_guru', 'guru', 'guru_pengampu', 'pengampu', 'nama_guru_pengampu');

            if (empty($nama)) {
                $skipCount++;
                if (count($skippedNames) < 100) {
                    $skippedNames[] = [
                        'nama'   => '(Nama Kosong)',
                        'kode'   => $rawKode ?: '-',
                        'alasan' => 'Kolom nama mata pelajaran kosong',
                    ];
                }
                continue;
            }

            $kode = !empty($rawKode) 
                ? strtoupper(trim($rawKode)) 
                : $this->generateCleanKode($nama);

            $guruId = $findGuruId($rawGuru);

            // Cek apakah data mapel dengan nama dan guru ini sudah ada
            $existing = MataPelajaran::where('nama', $nama)
                ->when($guruId, fn($q) => $q->where('guru_id', $guruId), fn($q) => $q->whereNull('guru_id'))
                ->first();

            if ($existing) {
                $existing->update([
                    'kode'    => $kode,
                    'guru_id' => $guruId ?: $existing->guru_id,
                ]);
            } else {
                MataPelajaran::create([
                    'kode'    => $kode,
                    'nama'    => $nama,
                    'guru_id' => $guruId,
                ]);
            }

            $successCount++;
            if (count($importedNames) < 100) {
                $guruName = $rawGuru ?: ($guruId ? Guru::find($guruId)?->nama : '-');
                $importedNames[] = [
                    'kode' => $kode,
                    'nama' => $nama,
                    'guru' => $guruName ?: '-',
                ];
            }
        }

        return redirect()->route('mata-pelajaran.index')
            ->with('import_success', [
                'success_count'  => $successCount,
                'skip_count'     => $skipCount,
                'imported_names' => $importedNames,
                'skipped_names'  => $skippedNames,
            ]);
    }

    /**
     * Helper to generate clean, short subject code (e.g. BIO, KIM, MAT, PAI, PJOK, PPKN, BIN, BIG)
     */
    private function generateCleanKode(string $nama): string
    {
        $clean = trim($nama);
        $lower = strtolower($clean);

        // Standard predefined map
        $map = [
            'biologi'                                      => 'BIO',
            'kimia'                                        => 'KIM',
            'matematika'                                   => 'MAT',
            'fisika'                                       => 'FIS',
            'informatika'                                  => 'INF',
            'geografi'                                     => 'GEO',
            'sosiologi'                                    => 'SOS',
            'sejarah'                                      => 'SEJ',
            'ekonomi'                                      => 'EKO',
            'bahasa indonesia'                             => 'BIN',
            'bahasa inggris'                               => 'BIG',
            'bahasa sunda'                                 => 'BSN',
            'pendidikan agama islam dan budi pekerti'      => 'PAI',
            'pendidikan agama dan budi pekerti'            => 'PAB',
            'pendidikan jasmani, olahraga, dan kesehatan'  => 'PJOK',
            'pendidikan pancasila'                         => 'PPKN',
            'pendidikan kewarganegaraan'                   => 'PKN',
            'prakarya dan kewirausahaan'                   => 'PKWU',
            'seni budaya'                                  => 'SBD',
            'bimbingan konseling'                          => 'BK',
            'antropologi'                                  => 'ANT',
        ];

        foreach ($map as $key => $code) {
            if ($lower === $key || str_starts_with($lower, $key)) {
                return $code;
            }
        }

        // Generic fallback: take first letter of each word if multiple words, or first 3 letters
        $words = preg_split('/\s+/', $clean);
        if (count($words) >= 2) {
            $prefix = '';
            foreach ($words as $w) {
                $prefix .= strtoupper(substr($w, 0, 1));
            }
            if (strlen($prefix) > 4) {
                $prefix = substr($prefix, 0, 4);
            }
        } else {
            $prefix = strtoupper(substr($words[0], 0, 3));
        }

        return preg_replace('/[^A-Z0-9]/', '', $prefix) ?: 'MPL';
    }
}
