<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\AturanJam;
use App\Models\Kehadiran;
use App\Models\Siswa;
use App\Models\Pengaduan;
use App\Models\ParentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OrangTuaDashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Orang Tua (Rekapitulasi Kehadiran Anak).
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Cari anak yang terhubung ke user orang tua ini
        $siswa = Siswa::where('orang_tua_user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            abort(403, 'Tidak ada data siswa yang terhubung ke akun Anda.');
        }

        // Filter periode bulanan
        $periode = $request->input('periode', date('Ym'));
        $data = $this->buildAttendanceData($siswa->id, $periode);
        
        $attendanceRows = $data['rows'];
        $daysInMonth = $data['daysInMonth'];

        return view('pages.absensi.orangtua-dashboard', compact(
            'user',
            'siswa',
            'periode',
            'attendanceRows',
            'daysInMonth'
        ));
    }

    /**
     * Tampilkan Halaman Pengaduan Orang Tua.
     */
    public function pengaduan(Request $request)
    {
        $user = auth()->user();
        $siswa = Siswa::where('orang_tua_user_id', $user->id)->first();

        if (!$siswa) {
            abort(403, 'Tidak ada data siswa yang terhubung ke akun Anda.');
        }

        $query = Pengaduan::where('siswa_id', $siswa->id);

        if ($request->filled('tanggal_range')) {
            $dates = explode(' hingga ', $request->tanggal_range);
            if (count($dates) === 2) {
                $query->whereBetween('tanggal', [$dates[0], $dates[1]]);
            } else {
                $query->where('tanggal', $dates[0]);
            }
        }

        $records = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->get();

        return view('pages.absensi.orangtua-pengaduan', compact('user', 'siswa', 'records'));
    }

    /**
     * Tampilkan Form Edit Profil Orang Tua.
     */
    public function profil()
    {
        $user = auth()->user();
        $siswa = Siswa::where('orang_tua_user_id', $user->id)->first();

        if (!$siswa) {
            abort(403, 'Tidak ada data siswa yang terhubung ke akun Anda.');
        }

        $profile = ParentProfile::firstOrNew(['parent_user_id' => $user->id]);

        return view('pages.absensi.orangtua-profil', compact('user', 'siswa', 'profile'));
    }

    /**
     * Update Profil Orang Tua.
     */
    public function updateProfil(Request $request)
    {
        $user = auth()->user();
        $siswa = Siswa::where('orang_tua_user_id', $user->id)->first();

        if (!$siswa) {
            abort(403, 'Tidak ada data siswa yang terhubung ke akun Anda.');
        }

        $request->validate([
            // Ayah
            'nik_ayah'            => 'nullable|numeric|digits_between:1,16',
            'nama_ayah'           => 'nullable|string|max:100|regex:/^[a-zA-Z\s\']+$/',
            'pekerjaan_ayah'      => 'nullable|string|max:100',
            'ket_pekerjaan_ayah'  => 'nullable|string|max:200',
            'pendidikan_ayah'     => 'nullable|string|max:100',
            'alamat_ayah'         => 'nullable|string|max:500',
            'no_hp_ayah'          => 'nullable|numeric|digits_between:1,13',
            'penghasilan_ayah'    => 'nullable|string|max:50',

            // Ibu
            'nik_ibu'             => 'nullable|numeric|digits_between:1,16',
            'nama_ibu'            => 'nullable|string|max:100|regex:/^[a-zA-Z\s\']+$/',
            'pekerjaan_ibu'       => 'nullable|string|max:100',
            'ket_pekerjaan_ibu'   => 'nullable|string|max:200',
            'pendidikan_ibu'      => 'nullable|string|max:100',
            'alamat_ibu'          => 'nullable|string|max:500',
            'no_hp_ibu'           => 'nullable|numeric|digits_between:1,13',
            'penghasilan_ibu'     => 'nullable|string|max:50',
        ], [
            'nik_ayah.numeric' => 'NIK Ayah harus berupa angka.',
            'nik_ayah.digits_between' => 'NIK Ayah maksimal 16 digit.',
            'nik_ibu.numeric' => 'NIK Ibu harus berupa angka.',
            'nik_ibu.digits_between' => 'NIK Ibu maksimal 16 digit.',
            
            'nama_ayah.regex' => 'Nama Ayah hanya boleh berisi huruf dan spasi.',
            'nama_ibu.regex' => 'Nama Ibu hanya boleh berisi huruf dan spasi.',

            'no_hp_ayah.numeric' => 'Nomor HP Ayah harus berupa angka.',
            'no_hp_ayah.digits_between' => 'Nomor HP Ayah maksimal 13 digit.',
            'no_hp_ibu.numeric' => 'Nomor HP Ibu harus berupa angka.',
            'no_hp_ibu.digits_between' => 'Nomor HP Ibu maksimal 13 digit.',
            
            'alamat_ayah.max' => 'Alamat Ayah maksimal 500 karakter.',
            'alamat_ibu.max' => 'Alamat Ibu maksimal 500 karakter.',
        ]);

        ParentProfile::updateOrCreate(
            ['parent_user_id' => $user->id],
            $request->only([
                'nik_ayah', 'nama_ayah', 'pekerjaan_ayah', 'ket_pekerjaan_ayah', 'pendidikan_ayah', 'alamat_ayah', 'no_hp_ayah', 'penghasilan_ayah',
                'nik_ibu', 'nama_ibu', 'pekerjaan_ibu', 'ket_pekerjaan_ibu', 'pendidikan_ibu', 'alamat_ibu', 'no_hp_ibu', 'penghasilan_ibu'
            ])
        );

        return redirect()->back()->with('success', 'Profil orang tua berhasil diperbarui.');
    }

    /**
     * Helper to build monthly attendance grid (cloned from SiswaDashboardController).
     */
    private function buildAttendanceData($siswaId, $periode)
    {
        $year = substr($periode, 0, 4);
        $month = substr($periode, 4, 2);
        
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        $siswa = Siswa::with('kelas')->find($siswaId);
        if (!$siswa) {
            return [
                'siswa' => null,
                'rows' => [],
                'daysInMonth' => $daysInMonth
            ];
        }

        $aturanJam = AturanJam::where('is_aktif', true)->first();

        $kehadirans = Kehadiran::where('siswa_id', $siswa->id)
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->tanggal)->format('Y-m-d');
            });

        $rows = [];
        $hariMap = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $startDate->copy()->addDays($day - 1);
            $dateStr = $date->format('Y-m-d');
            $dayOfWeek = $date->dayOfWeek;
            
            $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
            $hariLabel = $hariMap[$dayOfWeek];
            $tanggalLabel = $hariLabel . ', ' . $date->format('d-m-Y');

            $row = [
                'no' => $day,
                'nisn' => $siswa->nisn ?? $siswa->nis ?? '-',
                'nama' => $siswa->nama,
                'tanggal' => $tanggalLabel,
                'tanggal_raw' => $dateStr,
                'is_libur' => $isWeekend,
                'msk_lbr' => '',
                'msk_jam' => '',
                'keterangan' => '',
            ];

            if ($isWeekend) {
                $row['msk_lbr'] = '✗';
                $row['keterangan'] = 'Libur';
            } else {
                $record = $kehadirans->get($dateStr);
                
                if ($record) {
                    $row['status'] = $record->status;
                    
                    if (in_array($record->status, ['hadir', 'terlambat'])) {
                        $row['msk_lbr'] = '✓';
                        $row['msk_jam'] = $record->jam_masuk ? Carbon::parse($record->jam_masuk)->format('H:i:s') : '';

                        if ($record->status === 'terlambat') {
                            $row['keterangan'] = 'Terlambat';
                        } else {
                            $row['keterangan'] = 'Tepat Waktu';
                        }
                    } else if ($record->status === 'sakit') {
                        $row['msk_lbr'] = '✗';
                        $row['keterangan'] = 'Sakit';
                    } else if ($record->status === 'izin') {
                        $row['msk_lbr'] = '✗';
                        $row['keterangan'] = 'Izin';
                    } else {
                        $row['msk_lbr'] = '✗';
                        $row['keterangan'] = '-';
                    }
                } else {
                    $row['msk_lbr'] = '✗';
                    $row['keterangan'] = '-';
                }
            }

            $rows[] = $row;
        }

        return [
            'siswa' => $siswa,
            'rows' => $rows,
            'daysInMonth' => $daysInMonth
        ];
    }
}
