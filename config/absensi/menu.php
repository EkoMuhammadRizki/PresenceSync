<?php

return array(
    // Main menu
    'main' => array(
        // Dashboard
        array(
            'title' => 'Dashboard',
            'path'  => 'absensi/dashboard',
            'icon'  => theme()->getSvgIcon("demo1/media/icons/duotune/art/art002.svg", "svg-icon-2"),
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
            'icon'       => theme()->getSvgIcon("demo1/media/icons/duotune/general/gen025.svg", "svg-icon-2"),
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
                    array(
                        'title'  => 'Jurusan',
                        'path'   => 'absensi/master/jurusan',
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
                    array(
                        'title'  => 'Jadwal Pelajaran',
                        'path'   => 'absensi/master/jadwal-pelajaran',
                        'bullet' => '<span class="bullet bullet-dot"></span>',
                    ),
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
                                    'path'   => 'absensi/master/fingerprint/data',
                                    'bullet' => '<span class="bullet bullet-dot"></span>',
                                ),
                                array(
                                    'title'  => 'Log Scan Fingerprint',
                                    'path'   => 'absensi/master/fingerprint/log',
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

        // Laporan
        array(
            'title' => 'Laporan',
            'path'  => 'absensi/laporan',
            'icon'  => theme()->getSvgIcon("demo1/media/icons/duotune/general/gen005.svg", "svg-icon-2"),
        ),

        // Akun Saya
        array(
            'title' => 'Akun Saya',
            'path'  => 'absensi/akun',
            'icon'  => theme()->getSvgIcon("demo1/media/icons/duotune/communication/com013.svg", "svg-icon-2"),
        ),

        // Logout
        array(
            'title' => 'Logout',
            'path'  => 'logout',
            'icon'  => theme()->getSvgIcon("demo1/media/icons/duotune/arrows/arr076.svg", "svg-icon-2"),
        ),
    ),
);
