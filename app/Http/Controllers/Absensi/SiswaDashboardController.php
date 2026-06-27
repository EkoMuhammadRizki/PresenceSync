<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\AturanJam;
use App\Models\Kehadiran;
use App\Models\Semester;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SiswaDashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Kehadiran Siswa.
     */
    public function index()
    {
        $user = auth()->user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            abort(403, 'Anda tidak terdaftar sebagai siswa.');
        }

        // Ambil riwayat kehadiran siswa ini
        $kehadirans = Kehadiran::with('aturanJam')
            ->where('siswa_id', $siswa->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Cek apakah siswa sudah melakukan presensi hari ini
        $today = Carbon::today()->toDateString();
        $hasCheckedInToday = Kehadiran::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $today)
            ->exists();

        // Dapatkan data kehadiran hari ini jika ada
        $kehadiranHariIni = Kehadiran::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $today)
            ->first();

        return view('pages.absensi.siswa-dashboard', compact('siswa', 'kehadirans', 'hasCheckedInToday', 'kehadiranHariIni'));
    }

    /**
     * Proses Presensi (Hadir/Masuk).
     */
    public function presensi(Request $request)
    {
        $user = auth()->user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            abort(403, 'Anda tidak terdaftar sebagai siswa.');
        }

        $today = Carbon::today()->toDateString();

        // Cek double submit
        $exists = Kehadiran::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $today)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Anda sudah melakukan presensi hari ini.');
        }

        // Validasi foto dan koordinat wajib ada
        $request->validate([
            'foto_base64' => 'required|string',
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
        ], [
            'foto_base64.required' => 'Foto kehadiran wajib diambil.',
            'latitude.required'    => 'Lokasi GPS wajib diaktifkan.',
        ]);

        $dayOfWeek = now()->format('l');
        $daysMap = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        $hariIni = $daysMap[$dayOfWeek] ?? 'Senin';

        // Cari Aturan Jam
        $aturan = AturanJam::where('hari', $hariIni)->where('is_aktif', true)->first()
            ?? AturanJam::where('is_aktif', true)->first();

        if (!$aturan) {
            return redirect()->back()->with('error', 'Aturan jam masuk sekolah aktif tidak ditemukan.');
        }

        // Cari Semester Aktif
        $activeSemester = Semester::where('status', 'aktif')->first();
        if (!$activeSemester) {
            return redirect()->back()->with('error', 'Semester aktif tidak ditemukan.');
        }

        // Hitung status: tepat/terlambat
        $limitTime = Carbon::createFromFormat('H:i:s', $aturan->jam_masuk)
            ->addMinutes($aturan->toleransi_keterlambatan);

        $now = Carbon::now();
        $currentTimeString = $now->toTimeString();

        $status = 'hadir';
        if ($now->format('H:i:s') > $limitTime->format('H:i:s')) {
            $status = 'terlambat';
        }

        // Simpan foto base64 sebagai file jika kolom tersedia
        $fotoPath = null;
        $fotoBase64 = $request->input('foto_base64');
        if ($fotoBase64 && str_starts_with($fotoBase64, 'data:image')) {
            $imageData = explode(',', $fotoBase64);
            $image = base64_decode($imageData[1] ?? '');
            if ($image) {
                $filename = 'presensi_' . $siswa->id . '_' . $today . '.jpg';
                $path = storage_path('app/public/presensi/' . $filename);
                if (!file_exists(dirname($path))) {
                    mkdir(dirname($path), 0755, true);
                }
                file_put_contents($path, $image);
                $fotoPath = 'presensi/' . $filename;
            }
        }

        $koordinat = $request->latitude . ',' . $request->longitude;

        Kehadiran::create([
            'siswa_id'      => $siswa->id,
            'semester_id'   => $activeSemester->id,
            'aturan_jam_id' => $aturan->id,
            'tanggal'       => $today,
            'jam_masuk'     => $currentTimeString,
            'status'        => $status,
            'foto'          => $fotoPath,
            'koordinat'     => $koordinat,
        ]);

        $message = $status === 'hadir' ? 'Presensi berhasil! Anda masuk tepat waktu.' : 'Presensi berhasil! Anda tercatat terlambat.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Proses Pengajuan Izin / Sakit.
     */
    public function izin(Request $request)
    {
        $request->validate([
            'status'      => 'required|in:izin,sakit',
            'keterangan'  => 'required|string|min:500|max:5000',
            'foto_base64' => 'required|string',
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
        ], [
            'status.required'     => 'Jenis izin wajib dipilih.',
            'keterangan.required' => 'Alasan izin wajib diisi.',
            'keterangan.min'      => 'Alasan izin minimal 500 karakter.',
            'foto_base64.required' => 'Foto bukti wajib diambil.',
            'latitude.required'    => 'Lokasi GPS wajib diaktifkan.',
        ]);

        $user = auth()->user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            abort(403, 'Anda tidak terdaftar sebagai siswa.');
        }

        $today = Carbon::today()->toDateString();

        // Cek double submit
        $exists = Kehadiran::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $today)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Anda sudah mengisi kehadiran/izin hari ini.');
        }

        $dayOfWeek = now()->format('l');
        $daysMap = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        $hariIni = $daysMap[$dayOfWeek] ?? 'Senin';

        // Cari Aturan Jam
        $aturan = AturanJam::where('hari', $hariIni)->where('is_aktif', true)->first()
            ?? AturanJam::where('is_aktif', true)->first();

        // Cari Semester Aktif
        $activeSemester = Semester::where('status', 'aktif')->first();
        if (!$activeSemester) {
            return redirect()->back()->with('error', 'Semester aktif tidak ditemukan.');
        }

        // Simpan foto base64 sebagai file
        $fotoPath = null;
        $fotoBase64 = $request->input('foto_base64');
        if ($fotoBase64 && str_starts_with($fotoBase64, 'data:image')) {
            $imageData = explode(',', $fotoBase64);
            $image = base64_decode($imageData[1] ?? '');
            if ($image) {
                $filename = 'izin_' . $siswa->id . '_' . $today . '.jpg';
                $path = storage_path('app/public/presensi/' . $filename);
                if (!file_exists(dirname($path))) {
                    mkdir(dirname($path), 0755, true);
                }
                file_put_contents($path, $image);
                $fotoPath = 'presensi/' . $filename;
            }
        }

        $koordinat = $request->latitude . ',' . $request->longitude;

        Kehadiran::create([
            'siswa_id'      => $siswa->id,
            'semester_id'   => $activeSemester->id,
            'aturan_jam_id' => $aturan ? $aturan->id : null,
            'tanggal'       => $today,
            'status'        => $request->status,
            'keterangan'    => $request->keterangan,
            'foto'          => $fotoPath,
            'koordinat'     => $koordinat,
        ]);

        $statusText = $request->status === 'sakit' ? 'Sakit' : 'Izin';

        return redirect()->back()->with('success', "Pengajuan {$statusText} berhasil disimpan.");
    }
}
