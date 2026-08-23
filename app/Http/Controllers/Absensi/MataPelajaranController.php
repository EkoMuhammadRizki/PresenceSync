<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index()
    {
        $mataPelajarans = MataPelajaran::with('guru')->orderBy('tingkat')->orderBy('nama')->get();
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
            'nama'          => 'required|string|max:150',
            'kode'          => 'required|string|max:50|unique:mata_pelajarans,kode',
            'tingkat'       => 'nullable|string|max:20',
            'guru_id'       => 'nullable|exists:gurus,id',
        ], [
            'nama.required' => 'Nama mata pelajaran wajib diisi.',
            'kode.required' => 'Kode mata pelajaran wajib diisi.',
            'kode.unique'   => 'Kode mata pelajaran sudah digunakan.',
        ]);

        MataPelajaran::create($request->only('nama', 'kode', 'tingkat', 'guru_id'));

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $request->validate([
            'nama'          => 'required|string|max:150',
            'kode'          => 'required|string|max:50|unique:mata_pelajarans,kode,' . $mataPelajaran->id,
            'tingkat'       => 'nullable|string|max:20',
            'guru_id'       => 'nullable|exists:gurus,id',
        ], [
            'kode.unique' => 'Kode mata pelajaran sudah digunakan.',
        ]);

        $mataPelajaran->update($request->only('nama', 'kode', 'tingkat', 'guru_id'));

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
     * Download Excel template for importing mata pelajaran
     */
    public function downloadTemplate(Request $request)
    {
        $isEmpty = $request->has('empty');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Matpel');

        // Headers exactly as requested
        $headers = ['kd_matpel', 'nama_matpel', 'tingkat'];
        $colIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($colIndex . '1', $header);
            $colIndex++;
        }

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '009EF7']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ];
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        if (!$isEmpty) {
            $existing = MataPelajaran::all();
            if ($existing->isNotEmpty()) {
                $row = 2;
                foreach ($existing as $m) {
                    $sheet->setCellValue('A' . $row, $m->kode);
                    $sheet->setCellValue('B' . $row, $m->nama);
                    $sheet->setCellValue('C' . $row, $m->tingkat ?? '10');
                    $row++;
                }
            } else {
                $samples = [
                    ['BIN-10', 'Bahasa Indonesia', '10'],
                    ['BIN-11', 'Bahasa Indonesia', '11'],
                    ['BIN-12', 'Bahasa Indonesia', '12'],
                    ['BIG-10', 'Bahasa Inggris', '10'],
                    ['BIG-11', 'Bahasa Inggris', '11'],
                    ['BIG-12', 'Bahasa Inggris', '12'],
                    ['MTK-10', 'Matematika', '10'],
                    ['MTK-11', 'Matematika', '11'],
                    ['MTK-12', 'Matematika', '12'],
                    ['FIS-10', 'Fisika', '10'],
                    ['KIM-10', 'Kimia', '10'],
                    ['BIO-10', 'Biologi', '10'],
                ];
                $row = 2;
                foreach ($samples as $s) {
                    $sheet->setCellValue('A' . $row, $s[0]);
                    $sheet->setCellValue('B' . $row, $s[1]);
                    $sheet->setCellValue('C' . $row, $s[2]);
                    $row++;
                }
            }
        }

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'template_import_matpel.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
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
                    'kdmatpel', 'namamatpel', 'tingkat', 'kode', 'nama', 'kodematpel',
                    'kodemapel', 'namamapel', 'matapelajarannama', 'matapelajaran', 'mapel'
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

        // Normalisasi tingkat
        $normalizeTingkat = function ($raw) {
            if (!$raw) return 'Semua';
            $s = strtoupper(trim((string)$raw));
            if (in_array($s, ['10', 'X', 'KELAS 10', 'KELAS X', 'TINGKAT 10', 'SEPULUH'])) return '10';
            if (in_array($s, ['11', 'XI', 'KELAS 11', 'KELAS XI', 'TINGKAT 11', 'SEBELAS'])) return '11';
            if (in_array($s, ['12', 'XII', 'KELAS 12', 'KELAS XII', 'TINGKAT 12', 'DUA BELAS'])) return '12';
            return $raw;
        };

        $successCount = 0;
        $skipCount = 0;
        $importedNames = [];
        $skippedNames = [];

        foreach ($dataRows as $row) {
            $kode = $getVal($row, 'kd_matpel', 'kd_mapel', 'kode', 'kode_matpel', 'kode_mapel', 'kd_mata_pelajaran', 'kode_mata_pelajaran');
            $nama = $getVal($row, 'nama_matpel', 'nama_mapel', 'nama', 'nama_mata_pelajaran', 'mata_pelajaran', 'matpel', 'mapel');
            $rawTingkat = $getVal($row, 'tingkat', 'tingkat_kelas', 'kelas', 'level', 'tingkatan');

            if (empty($kode) || empty($nama)) {
                $skipCount++;
                if (count($skippedNames) < 100) {
                    $skippedNames[] = [
                        'nama'   => $nama ?: '(Nama Kosong)',
                        'kode'   => $kode ?: '(Kode Kosong)',
                        'alasan' => 'Kolom kode atau nama mata pelajaran kosong',
                    ];
                }
                continue;
            }

            $tingkat = $normalizeTingkat($rawTingkat);

            $existing = MataPelajaran::where('kode', $kode)->first();
            if ($existing) {
                $existing->update([
                    'nama'    => $nama,
                    'tingkat' => $tingkat,
                ]);
            } else {
                MataPelajaran::create([
                    'kode'    => $kode,
                    'nama'    => $nama,
                    'tingkat' => $tingkat,
                    'guru_id' => null,
                ]);
            }

            $successCount++;
            if (count($importedNames) < 100) {
                $importedNames[] = [
                    'kode'    => $kode,
                    'nama'    => $nama,
                    'tingkat' => $tingkat,
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
}

