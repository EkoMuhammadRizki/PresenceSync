<?php

$dtAssets = array(
    'custom' => array(
        'css' => array('plugins/custom/datatables/datatables.bundle.css'),
        'js'  => array('plugins/custom/datatables/datatables.bundle.js'),
    ),
);

return array(
    'absensi' => array(
        'dashboard' => array(
            'title' => 'Dashboard',
            'view'  => 'absensi/dashboard',
        ),

        'panduan' => array(
            'title' => 'Panduan',
            'view'  => 'absensi/panduan',
        ),

        'pengguna' => array(
            'data' => array(
                'title'  => 'Data Pengguna & Hak Akses',
                'view'   => 'absensi/pengguna-data',
                'assets' => $dtAssets,
            ),
            'log-aktivitas' => array(
                'title'  => 'Log Aktivitas',
                'view'   => 'absensi/log-aktivitas',
                'assets' => $dtAssets,
            ),
        ),

        'master' => array(
            'siswa' => array(
                'title'  => 'Siswa',
                'view'   => 'absensi/siswa',
                'assets' => $dtAssets,
            ),
            'guru' => array(
                'title'  => 'Guru',
                'view'   => 'absensi/guru',
                'assets' => $dtAssets,
            ),
            'kelas' => array(
                'data' => array(
                    'title'  => 'Data Kelas',
                    'view'   => 'absensi/kelas-data',
                    'assets' => $dtAssets,
                ),
                'pembagian' => array(
                    'title'  => 'Pembagian Kelas',
                    'view'   => 'absensi/pembagian-kelas',
                    'assets' => $dtAssets,
                ),
            ),
            'tahun-ajaran' => array(
                'title'  => 'Tahun Ajaran',
                'view'   => 'absensi/tahun-ajaran',
                'assets' => $dtAssets,
            ),
            'mata-pelajaran' => array(
                'title'  => 'Mata Pelajaran',
                'view'   => 'absensi/mata-pelajaran',
                'assets' => $dtAssets,
            ),
            'jadwal-pelajaran' => array(
                'title'  => 'Jadwal Pelajaran',
                'view'   => 'absensi/jadwal-pelajaran',
                'assets' => $dtAssets,
            ),
            'aturan-jam' => array(
                'title'  => 'Aturan Jam Sekolah',
                'view'   => 'absensi/aturan-jam',
                'assets' => $dtAssets,
            ),
            'fingerprint' => array(
                'data' => array(
                    'title'  => 'Data Perangkat',
                    'view'   => 'absensi/fingerprint-data',
                    'assets' => $dtAssets,
                ),
                'log' => array(
                    'title' => 'Log Scan Fingerprint',
                    'view'  => 'absensi/placeholder',
                ),
            ),
        ),

        'kehadiran' => array(
            'title'  => 'Kehadiran Siswa',
            'view'   => 'absensi/kehadiran',
            'assets' => $dtAssets,
        ),

        'profil-siswa' => array(
            'title'  => 'Profil Siswa',
            'view'   => 'absensi/profil-siswa',
            'assets' => $dtAssets,
        ),

        'profil-guru' => array(
            'title'  => 'Profil Guru',
            'view'   => 'absensi/profil-guru',
            'assets' => $dtAssets,
        ),

        'profil-kelas' => array(
            'title'  => 'Profil Kelas',
            'view'   => 'absensi/profil-kelas',
            'assets' => $dtAssets,
        ),

        'siswa' => array(
            'dashboard' => array(
                'title'  => 'Kehadiran',
                'view'   => 'absensi/siswa-dashboard',
                'assets' => $dtAssets,
            ),
            'kehadiran-mp' => array(
                'title'  => 'Kehadiran Mata Pelajaran',
                'view'   => 'absensi/kehadiran-mata-pelajaran',
                'assets' => $dtAssets,
            ),
            'pengaduan' => array(
                'title'  => 'Pengaduan',
                'view'   => 'absensi/pengaduan',
            ),
        ),

        'guru' => array(
            'kelas-wali' => array(
                'title'  => 'Data Kelas Wali',
                'view'   => 'absensi/guru-kelas-wali',
                'assets' => $dtAssets,
            ),
            'pengaduan' => array(
                'title'  => 'Daftar Pengaduan',
                'view'   => 'absensi/guru-pengaduan',
                'assets' => $dtAssets,
            ),
        ),

        'laporan' => array(
            'title' => 'Laporan',
            'view'  => 'absensi/placeholder',
        ),

        'pengaturan-restriksi' => array(
            'kelas' => array(
                'title'  => 'Restriksi Kelas',
                'view'   => 'absensi/pengaturan-restriksi-kelas',
                'assets' => $dtAssets,
            ),
        ),

        'akun' => array(
            'title' => 'Akun Saya',
            'view'  => 'absensi/placeholder',
        ),

        'orangtua' => array(
            'dashboard' => array(
                'title'  => 'Rekapitulasi Kehadiran Anak',
                'view'   => 'absensi/orangtua-dashboard',
                'assets' => $dtAssets,
            ),
            'pengaduan' => array(
                'title'  => 'Pengaduan',
                'view'   => 'absensi/orangtua-pengaduan',
                'assets' => $dtAssets,
            ),
            'profil' => array(
                'title'  => 'Edit Profil',
                'view'   => 'absensi/orangtua-profil',
                'assets' => $dtAssets,
            ),
        ),
    ),
);
