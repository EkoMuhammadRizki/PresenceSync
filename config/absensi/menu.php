<?php

return array(
    // Main menu
    'main' => array(
        // Dashboard Admin & Kesiswaan
        array(
            'title' => 'Dashboard',
            'path'  => 'absensi/dashboard',
            'icon'  => theme()->getSvgIcon("demo1/media/icons/duotune/art/art002.svg", "svg-icon-2"),
            'role'  => ['admin', 'kesiswaan'],
        ),

        // Dashboard Guru
        array(
            'title' => 'Dashboard',
            'path'  => 'absensi/guru/dashboard',
            'icon'  => theme()->getSvgIcon("demo1/media/icons/duotune/art/art002.svg", "svg-icon-2"),
            'role'  => ['guru'],
        ),

        // Dashboard Siswa
        array(
            'title' => 'Dashboard',
            'path'  => 'absensi/siswa/dashboard',
            'icon'  => theme()->getSvgIcon("demo1/media/icons/duotune/art/art002.svg", "svg-icon-2"),
            'role'  => ['siswa'],
        ),

        // Data Siswa (Single Level, Kesiswaan Only)
        array(
            'title' => 'Data Siswa',
            'path'  => 'absensi/master/siswa',
            'icon'  => theme()->getSvgIcon("demo1/media/icons/duotune/communication/com006.svg", "svg-icon-2"),
            'role'  => ['kesiswaan'],
        ),

        // Kelas (Multi Level, Kesiswaan Only)
        array(
            'title'      => 'Kelas',
            'icon'       => theme()->getSvgIcon("demo1/media/icons/duotune/general/gen025.svg", "svg-icon-2"),
            'classes'    => array('item' => 'menu-accordion'),
            'attributes' => array('data-kt-menu-trigger' => 'click'),
            'role'       => ['kesiswaan'],
            'sub'        => array(
                'class' => 'menu-sub-accordion menu-active-bg',
                'items' => array(
                    array(
                        'title'  => 'Data Kelas',
                        'path'   => 'absensi/master/kelas/data',
                        'bullet' => '<span class="bullet bullet-dot"></span>',
                    ),
                    array(
                        'title'  => 'Pembagian Kelas',
                        'path'   => 'absensi/master/kelas/pembagian',
                        'bullet' => '<span class="bullet bullet-dot"></span>',
                    ),
                ),
            ),
        ),

        // Data Kelas Wali (Only visible for Guru)
        array(
            'title' => 'Data Kelas Wali',
            'path'  => 'absensi/guru/kelas-wali',
            'icon'  => theme()->getSvgIcon("demo1/media/icons/duotune/communication/com014.svg", "svg-icon-2"),
            'role'  => ['guru'],
        ),

        // Pengaduan Wali Kelas (Only visible for Guru)
        array(
            'title' => 'Pengaduan',
            'path'  => 'absensi/guru/pengaduan',
            'icon'  => theme()->getSvgIcon("demo1/media/icons/duotune/general/gen044.svg", "svg-icon-2"),
            'role'  => ['guru'],
        ),

        // Kehadiran Siswa (Only visible for Siswa)
        array(
            'title' => 'Kehadiran',
            'path'  => 'absensi/siswa/kehadiran',
            'icon'  => theme()->getSvgIcon("demo1/media/icons/duotune/general/gen014.svg", "svg-icon-2"),
            'role'  => ['siswa'],
        ),

        // Kehadiran Mata Pelajaran (Only visible for Siswa Sekretaris)
        array(
            'title' => 'Kehadiran Mata Pelajaran',
            'path'  => 'absensi/siswa/kehadiran-mp',
            'icon'  => theme()->getSvgIcon("demo1/media/icons/duotune/general/gen014.svg", "svg-icon-2"),
            'role'  => ['siswa'],
        ),

        // Pengaduan (Only visible for Siswa)
        array(
            'title' => 'Pengaduan',
            'path'  => 'absensi/siswa/pengaduan',
            'icon'  => theme()->getSvgIcon("demo1/media/icons/duotune/general/gen044.svg", "svg-icon-2"),
            'role'  => ['siswa'],
        ),

        // Manajemen Pengguna & Peran
        array(
            'title'      => 'Manajemen Pengguna & Peran',
            'icon'       => theme()->getSvgIcon("demo1/media/icons/duotune/communication/com006.svg", "svg-icon-2"),
            'classes'    => array('item' => 'menu-accordion'),
            'attributes' => array('data-kt-menu-trigger' => 'click'),
            'sub'        => array(
                'class' => 'menu-sub-accordion menu-active-bg',
                'items' => array(
                    array(
                        'title'  => 'Data Pengguna & Hak Akses',
                        'path'   => 'absensi/pengguna/data',
                        'bullet' => '<span class="bullet bullet-dot"></span>',
                    ),
                    array(
                        'title'  => 'Log Aktivitas',
                        'path'   => 'absensi/pengguna/log-aktivitas',
                        'bullet' => '<span class="bullet bullet-dot"></span>',
                    ),
                ),
            ),
        ),

        // Master Data
        array(
            'title'      => 'Master Data',
            'icon'       => theme()->getSvgIcon("demo1/media/icons/duotune/abstract/abs027.svg", "svg-icon-2"),
            'classes'    => array('item' => 'menu-accordion'),
            'attributes' => array('data-kt-menu-trigger' => 'click'),
            'sub'        => array(
                'class' => 'menu-sub-accordion menu-active-bg',
                'items' => array(
                    array(
                        'title'  => 'Siswa',
                        'path'   => 'absensi/master/siswa',
                        'bullet' => '<span class="bullet bullet-dot"></span>',
                    ),
                    array(
                        'title'  => 'Guru',
                        'path'   => 'absensi/master/guru',
                        'bullet' => '<span class="bullet bullet-dot"></span>',
                    ),

                    // Kelas (parent with Level_3 sub-menu)
                    array(
                        'title'      => 'Kelas',
                        'classes'    => array('item' => 'menu-accordion'),
                        'attributes' => array('data-kt-menu-trigger' => 'click'),
                        'bullet'     => '<span class="bullet bullet-dot"></span>',
                        'sub'        => array(
                            'class' => 'menu-sub-accordion',
                            'items' => array(
                                array(
                                    'title'  => 'Data Kelas',
                                    'path'   => 'absensi/master/kelas/data',
                                    'bullet' => '<span class="bullet bullet-dot"></span>',
                                ),
                                array(
                                    'title'  => 'Pembagian Kelas',
                                    'path'   => 'absensi/master/kelas/pembagian',
                                    'bullet' => '<span class="bullet bullet-dot"></span>',
                                ),
                            ),
                        ),
                    ),
                    array(
                        'title'  => 'Tahun Ajaran',
                        'path'   => 'absensi/master/tahun-ajaran',
                        'bullet' => '<span class="bullet bullet-dot"></span>',
                    ),
                    array(
                        'title'  => 'Mata Pelajaran',
                        'path'   => 'absensi/master/mata-pelajaran',
                        'bullet' => '<span class="bullet bullet-dot"></span>',
                    ),
                    /*
                    array(
                        'title'  => 'Jadwal Pelajaran',
                        'path'   => 'absensi/master/jadwal-pelajaran',
                        'bullet' => '<span class="bullet bullet-dot"></span>',
                    ),
                    */
                    array(
                        'title'  => 'Aturan Jam Sekolah',
                        'path'   => 'absensi/master/aturan-jam',
                        'bullet' => '<span class="bullet bullet-dot"></span>',
                    ),
                    // Perangkat Fingerprint (parent with Level_3 sub-menu)
                    array(
                        'title'      => 'Perangkat Fingerprint',
                        'classes'    => array('item' => 'menu-accordion'),
                        'attributes' => array('data-kt-menu-trigger' => 'click'),
                        'bullet'     => '<span class="bullet bullet-dot"></span>',
                        'sub'        => array(
                            'class' => 'menu-sub-accordion',
                            'items' => array(
                                array(
                                    'title'  => 'Data Perangkat',
                                    'path'   => 'absensi/fingerprint',
                                    'bullet' => '<span class="bullet bullet-dot"></span>',
                                ),
                                array(
                                    'title'  => 'Log Scan Fingerprint',
                                    'path'   => 'absensi/fingerprint/logs-view',
                                    'bullet' => '<span class="bullet bullet-dot"></span>',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),

        // Kehadiran Siswa
        array(
            'title' => 'Kehadiran Siswa',
            'path'  => 'absensi/kehadiran',
            'icon'  => theme()->getSvgIcon("demo1/media/icons/duotune/general/gen014.svg", "svg-icon-2"),
        ),

        // Laporan (Multi Level)
        array(
            'title'      => 'Laporan',
            'icon'       => theme()->getSvgIcon("demo1/media/icons/duotune/general/gen005.svg", "svg-icon-2"),
            'classes'    => array('item' => 'menu-accordion'),
            'attributes' => array('data-kt-menu-trigger' => 'click'),
            'sub'        => array(
                'class' => 'menu-sub-accordion menu-active-bg',
                'items' => array(
                    array(
                        'title'  => 'Siswa',
                        'path'   => 'absensi/laporan/siswa',
                        'bullet' => '<span class="bullet bullet-dot"></span>',
                    ),
                    array(
                        'title'  => 'Guru',
                        'path'   => 'absensi/laporan/guru',
                        'bullet' => '<span class="bullet bullet-dot"></span>',
                    ),
                    array(
                        'title'  => 'Kehadiran',
                        'path'   => 'absensi/laporan/kehadiran',
                        'bullet' => '<span class="bullet bullet-dot"></span>',
                    ),
                    array(
                        'title'  => 'Pengaduan',
                        'path'   => 'absensi/laporan/pengaduan',
                        'bullet' => '<span class="bullet bullet-dot"></span>',
                    ),
                ),
            ),
        ),

        // Arsip Tahun Ajaran & Semester
        array(
            'title' => 'Arsip',
            'path'  => 'absensi/arsip',
            'icon'  => theme()->getSvgIcon("demo1/media/icons/duotune/files/fil017.svg", "svg-icon-2"),
        ),

        // Pengaturan Restriksi
        array(
            'title'      => 'Pengaturan Restriksi',
            'icon'       => theme()->getSvgIcon("demo1/media/icons/duotune/general/gen019.svg", "svg-icon-2"),
            'classes'    => array('item' => 'menu-accordion'),
            'attributes' => array('data-kt-menu-trigger' => 'click'),
            'sub'        => array(
                'class' => 'menu-sub-accordion menu-active-bg',
                'items' => array(
                    array(
                        'title'  => 'Restriksi Kelas',
                        'path'   => 'absensi/pengaturan-restriksi/kelas',
                        'bullet' => '<span class="bullet bullet-dot"></span>',
                    ),
                ),
            ),
        ),

        // Akun Saya
        array(
            'title' => 'Akun Saya',
            'path'  => 'absensi/akun',
            'icon'  => theme()->getSvgIcon("demo1/media/icons/duotune/communication/com013.svg", "svg-icon-2"),
        ),

        // Panduan
        array(
            'title' => 'Panduan',
            'path'  => 'absensi/panduan',
            'icon'  => theme()->getSvgIcon("demo1/media/icons/duotune/general/book-icon.svg", "svg-icon-2"),
        ),

        // Keluar
        array(
            'title' => 'Keluar',
            'path'  => 'logout',
            'icon'  => theme()->getSvgIcon("demo1/media/icons/duotune/arrows/arr076.svg", "svg-icon-2"),
        ),
    ),
);
