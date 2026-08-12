<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\JadwalPelajaran;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SiswaProfileController extends Controller
{
    /**
     * Show Siswa profile dashboard.
     */
    public function show(Request $request)
    {
        $id = $request->id;
        if (!$id) {
            $siswa = Siswa::with(['kelas.guru', 'user'])->where('user_id', auth()->id())->first();
        } else {
            $siswa = Siswa::with(['kelas.guru', 'user'])->find($id);
        }

        if (!$siswa) {
            abort(404, 'Data siswa tidak ditemukan.');
        }

        $user = $siswa->user;
        $info = $user ? $user->info : null;

        // Statistics (Real data from Kehadiran & Pengaduan)
        $totalEntries = $siswa->kehadirans()->count();
        $totalHadir = $siswa->kehadirans()->whereIn('status', ['hadir', 'terlambat'])->count();
        
        $attendanceRate = $totalEntries > 0 
            ? round(($totalHadir / $totalEntries) * 100) 
            : 0;

        $totalIzin = $siswa->kehadirans()->where('status', 'izin')->count();
        $totalSakit = $siswa->kehadirans()->where('status', 'sakit')->count();
        $totalAlpa = $siswa->kehadirans()->where('status', 'alpha')->count();
        $totalPengaduan = \App\Models\Pengaduan::where('siswa_id', $siswa->id)->count();

        $stats = [
            'attendance_rate' => $attendanceRate . '%',
            'izin'            => $totalIzin,
            'sakit'           => $totalSakit,
            'alpa'            => $totalAlpa,
            'total_pengaduan' => $totalPengaduan,
        ];

        // Active Classes list
        $kelas = Kelas::where('status', 'aktif')->orderBy('tingkat')->get();

        // Semesters & Tahun Ajarans for filter dropdown
        $semesters = Semester::with('tahunAjaran')->get();
        $tahunAjarans = TahunAjaran::get();

        // Student Schedule with dynamic filtering
        $scheduleQuery = JadwalPelajaran::with(['kelas', 'mataPelajaran.guru', 'semester.tahunAjaran'])
            ->where('kelas_id', $siswa->kelas_id);

        if ($request->filled('semester_id')) {
            $scheduleQuery->where('semester_id', $request->semester_id);
        }
        $schedules = $scheduleQuery->get();

        // Role mapping
        $currentUser = auth()->user();
        $userRole = $this->getUserRole($currentUser);

        $completionRate = $this->calculateCompletionRate($siswa, $user);

        // Parent Profile Info (check both orang_tua_user_id and user_id)
        $parentUserIds = array_filter([$siswa->orang_tua_user_id, $siswa->user_id]);
        $parentProfile = \App\Models\ParentProfile::whereIn('parent_user_id', $parentUserIds)->first();

        // Attendance grid data (cloned from SiswaDashboardController)
        $periode = $request->input('periode', date('Ym'));
        $attendanceData = $this->buildAttendanceData($siswa->id, $periode);
        $attendanceRows = $attendanceData['rows'];
        $daysInMonth = $attendanceData['daysInMonth'];

        // Secretary Complaints Laporan Pengaduan
        $pengaduans = \App\Models\Pengaduan::where('siswa_id', $siswa->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('profile.siswa', compact(
            'siswa', 'user', 'info', 'stats', 'kelas',
            'semesters', 'tahunAjarans', 'schedules', 'userRole', 'completionRate',
            'parentProfile', 'periode', 'attendanceRows', 'daysInMonth', 'pengaduans'
        ));
    }

    /**
     * Update Siswa profile.
     */
    public function update(Request $request, Siswa $siswa)
    {
        $currentUser = auth()->user();
        $userRole = $this->getUserRole($currentUser);

        // Normalize phone numbers: strip non-digit characters
        if ($request->filled('no_hp')) {
            $request->merge(['no_hp' => preg_replace('/[^0-9]/', '', $request->no_hp)]);
        } elseif ($request->has('no_hp')) {
            $request->merge(['no_hp' => null]);
        }
        if ($request->filled('no_hp_orang_tua')) {
            $request->merge(['no_hp_orang_tua' => preg_replace('/[^0-9]/', '', $request->no_hp_orang_tua)]);
        } elseif ($request->has('no_hp_orang_tua')) {
            $request->merge(['no_hp_orang_tua' => null]);
        }

        $canStudentEditClass = (\App\Models\Setting::where('key', 'restriksi_kelas')->value('value') ?? 'off') === 'on';

        // Validation Rules (Role Restricted)
        $rules = [
            'nama_orang_tua'  => 'nullable|string|max:150',
            'no_hp'           => 'nullable|regex:/^[0-9]{8,15}$/',
            'no_hp_orang_tua' => 'nullable|regex:/^[0-9]{8,15}$/',
            'alamat'          => 'nullable|string',
            'avatar'          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        if ($userRole === 'admin' || $userRole === 'guru' || $userRole === 'kesiswaan' || $canStudentEditClass) {
            $rules['kelas_id'] = 'nullable|exists:kelas,id';
        }

        $messages = [
            'kelas_id.exists'        => 'Kelas tidak valid.',
            'no_hp.regex'            => 'Nomor HP siswa hanya boleh berisi angka (8-15 digit).',
            'no_hp_orang_tua.regex'  => 'Nomor HP orang tua hanya boleh berisi angka (8-15 digit).',
        ];

        // Admin / Guru / Kesiswaan can update everything
        if ($userRole === 'admin' || $userRole === 'guru' || $userRole === 'kesiswaan') {
            $rules = array_merge($rules, [
                'nama'           => 'required|string|max:150',
                'nisn'           => 'nullable|string|max:20|unique:siswas,nisn,' . $siswa->id,
                'nis'            => 'nullable|string|max:20|unique:siswas,nis,' . $siswa->id,
                'jenis_kelamin'  => 'required|in:L,P',
                'tempat_lahir'   => 'nullable|string|max:100',
                'tanggal_lahir'  => 'nullable|date|before_or_equal:today',
                'status'         => 'nullable|string|max:20',
            ]);

            $messages = array_merge($messages, [
                'nama.required'                 => 'Nama siswa wajib diisi.',
                'nisn.unique'                   => 'NISN sudah terdaftar.',
                'nis.unique'                    => 'NIS sudah terdaftar.',
                'jenis_kelamin.required'        => 'Jenis kelamin wajib dipilih.',
                'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh melebihi hari ini.',
            ]);
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Apply role restrictions on update fields
        if ($userRole === 'siswa' || $userRole === 'orang_tua') {
            $allowedFields = ['nama_orang_tua', 'no_hp', 'no_hp_orang_tua', 'alamat'];
            if ($canStudentEditClass) {
                $allowedFields[] = 'kelas_id';
            }
            $siswa->update($request->only($allowedFields));
        } else {
            $siswa->update($request->only(
                'nama', 'nisn', 'nis', 'kelas_id', 'jenis_kelamin', 'tempat_lahir',
                'tanggal_lahir', 'alamat', 'no_hp', 'no_hp_orang_tua', 'nama_orang_tua',
                'status'
            ));

            // Update associated User name
            if ($siswa->user && $request->has('nama')) {
                $nameParts = explode(' ', trim($request->nama), 2);
                $firstName = $nameParts[0];
                $lastName  = $nameParts[1] ?? $nameParts[0];
                $siswa->user->update([
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                ]);
            }
        }

        // Handle Avatar update for associated User
        if ($siswa->user) {
            $info = $siswa->user->info ?? new UserInfo(['user_id' => $siswa->user_id]);
            $info->user()->associate($siswa->user);
            $info->phone = $request->no_hp;

            if ($request->hasFile('avatar')) {
                if ($info->avatar) {
                    Storage::delete($info->avatar);
                }
                $path = 'avatars/siswa/' . $siswa->id;
                $info->avatar = Storage::disk('public')->putFileAs($path, $request->file('avatar'), 'avatar.jpg', 'public');
            }

            if ($request->boolean('avatar_remove')) {
                if ($info->avatar) {
                    Storage::delete($info->avatar);
                }
                $info->avatar = null;
            }

            $info->save();
        }

        return redirect()->back()->with('success', 'Profil siswa berhasil diperbarui.');
    }

    /**
     * Change student password.
     */
    public function changePassword(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password berhasil diubah.');
    }

    /**
     * Detect user role.
     */
    private function getUserRole($user): string
    {
        if (!$user) return 'guest';
        if ($user->hasRole('admin')) return 'admin';

        $isSiswa = Siswa::where('user_id', $user->id)->exists();
        if ($isSiswa) return 'siswa';

        $isGuru = \App\Models\Guru::where('user_id', $user->id)->exists();
        if ($isGuru) {
            if ($user->hasRole('kesiswaan')) {
                return 'kesiswaan';
            }
            return 'guru';
        }

        return 'admin';
    }

    /**
     * Calculate profile completion rate.
     */
    private function calculateCompletionRate($siswa, $user)
    {
        $fields = [
            $siswa->nama,
            $siswa->nis,
            $siswa->nisn,
            $siswa->no_hp,
            $siswa->alamat,
            $siswa->nama_orang_tua,
            $user->info->avatar ?? null,
        ];

        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($field)) {
                $filled++;
            }
        }

        return round(($filled / count($fields)) * 100);
    }

    /**
     * Helper to build monthly attendance grid (cloned from SiswaDashboardController).
     */
    private function buildAttendanceData($siswaId, $periode)
    {
        $year = substr($periode, 0, 4);
        $month = substr($periode, 4, 2);
        
        $startDate = \Illuminate\Support\Carbon::createFromDate($year, $month, 1)->startOfMonth();
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

        $aturanJam = \App\Models\AturanJam::where('is_aktif', true)->first();

        $kehadirans = \App\Models\Kehadiran::where('siswa_id', $siswa->id)
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($item) {
                return \Illuminate\Support\Carbon::parse($item->tanggal)->format('Y-m-d');
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
                        $row['msk_jam'] = $record->jam_masuk ? \Illuminate\Support\Carbon::parse($record->jam_masuk)->format('H:i:s') : '';

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
