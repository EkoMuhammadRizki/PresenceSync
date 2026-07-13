<x-base-layout>
    <!--begin::Card-->
    <div class="card shadow-sm mb-10">
        <!--begin::Card Header-->
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center gap-3">
                    <span class="svg-icon svg-icon-1 svg-icon-primary">
                        {!! theme()->getSvgIcon("demo1/media/icons/duotune/general/gen008.svg", "svg-icon-1") !!}
                    </span>
                    <h3 class="fw-bolder m-0">Panduan Lengkap Penggunaan Sistem Presence Sync</h3>
                </div>
            </div>
        </div>
        <!--end::Card Header-->

        <!--begin::Card Body-->
        <div class="card-body fs-6 text-gray-700">
            <!--begin::Overview Notice-->
            <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-8">
                <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg") !!}
                </span>
                <div class="d-flex flex-stack flex-grow-1">
                    <div class="fw-bold">
                        <h4 class="text-gray-900 fw-bolder">Selamat Datang di Portal Panduan</h4>
                        <div class="fs-6 text-gray-700">
                            Halaman ini memberikan instruksi terperinci tentang alur kerja aplikasi Presence Sync agar proses manajemen data sekolah, kelas, siswa, guru, jadwal pelajaran, serta pencatatan presensi terintegrasi berjalan dengan lancar dan bebas dari kesalahan relasi database.
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Overview Notice-->

            <!--begin::Accordion-->
            <div class="accordion accordion-icon-toggle" id="kt_accordion_panduan">
                
                <!--begin::Accordion Item 1-->
                <div class="mb-5">
                    <div class="accordion-header py-3 d-flex" data-bs-toggle="collapse" data-bs-target="#kt_accordion_panduan_body_1">
                        <span class="accordion-icon"><i class="bi bi-chevron-right fs-4"></i></span>
                        <h3 class="fs-4 fw-bolder text-gray-800 px-3 mb-0">Langkah 1: Setup Tahun Ajaran & Aturan Jam Sekolah</h3>
                    </div>
                    <div id="kt_accordion_panduan_body_1" class="fs-6 collapse show ps-10" data-bs-parent="#kt_accordion_panduan">
                        <p class="mb-3">Sebelum mengelola data kelas atau siswa, Anda wajib memastikan <strong>Tahun Ajaran Aktif</strong> dan <strong>Aturan Jam Sekolah</strong> sudah terkonfigurasi dengan benar.</p>
                        <ul class="ps-5 mb-4">
                            <li class="mb-2"><strong>Tahun Ajaran</strong>: Akses menu <span class="badge badge-light-primary fs-7">Master Data > Tahun Ajaran</span>. Tambahkan tahun ajaran baru dan aktifkan satu tahun ajaran. Sistem membatasi hanya boleh ada satu Tahun Ajaran yang berstatus <strong>Aktif</strong> dalam satu waktu. Jika ingin membuat yang baru aktif, ubah status tahun ajaran sebelumnya ke <strong>Selesai</strong>.</li>
                            <li class="mb-2"><strong>Aturan Jam Sekolah</strong>: Akses menu <span class="badge badge-light-primary fs-7">Master Data > Aturan Jam Sekolah</span>. Konfigurasikan jam masuk, batas keterlambatan, jam pulang, serta hari kerja (Senin s.d. Minggu). Konfigurasi ini digunakan sebagai acuan pencatatan presensi siswa.</li>
                        </ul>
                    </div>
                </div>
                <!--end::Accordion Item 1-->

                <!--begin::Accordion Item 2-->
                <div class="mb-5">
                    <div class="accordion-header py-3 d-flex collapsed" data-bs-toggle="collapse" data-bs-target="#kt_accordion_panduan_body_2">
                        <span class="accordion-icon"><i class="bi bi-chevron-right fs-4"></i></span>
                        <h3 class="fs-4 fw-bolder text-gray-800 px-3 mb-0">Langkah 2: Mengelola Data Guru & Wali Kelas</h3>
                    </div>
                    <div id="kt_accordion_panduan_body_2" class="fs-6 collapse ps-10" data-bs-parent="#kt_accordion_panduan">
                        <p class="mb-3">Langkah selanjutnya adalah mengisi data Guru yang akan mengajar atau bertindak sebagai Wali Kelas.</p>
                        <ul class="ps-5 mb-4">
                            <li class="mb-2">Akses menu <span class="badge badge-light-primary fs-7">Master Data > Guru</span>.</li>
                            <li class="mb-2">Anda dapat menambahkan Guru secara manual melalui tombol <span class="text-primary fw-bold">Tambah Guru</span> atau melakukan import masal dengan format Excel.</li>
                            <li class="mb-2"><strong>Aturan Wali Kelas</strong>: Setiap Wali Kelas hanya boleh didelegasikan pada satu kelas saja. Guru yang sudah terpilih menjadi Wali Kelas di suatu kelas akan otomatis terkunci (disabled) dan tidak dapat dipilih lagi di kelas lain untuk menjaga keunikan data.</li>
                        </ul>
                        <div class="alert alert-light-primary border border-primary d-flex align-items-center p-5 mb-4">
                            <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
                                {!! theme()->getSvgIcon("icons/duotune/general/gen008.svg") !!}
                            </span>
                            <div class="d-flex flex-column">
                                <h5 class="fw-bolder text-primary">Informasi Import Guru</h5>
                                <span>Saat mengunduh template import lewat modal, Anda akan mendapatkan template kosong. Sedangkan tombol ekspor/download template hijau di halaman utama akan menyertakan data guru yang sudah terdaftar.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Accordion Item 2-->

                <!--begin::Accordion Item 3-->
                <div class="mb-5">
                    <div class="accordion-header py-3 d-flex collapsed" data-bs-toggle="collapse" data-bs-target="#kt_accordion_panduan_body_3">
                        <span class="accordion-icon"><i class="bi bi-chevron-right fs-4"></i></span>
                        <h3 class="fs-4 fw-bolder text-gray-800 px-3 mb-0">Langkah 3: Mengelola Data Kelas & Pembagian Kelas</h3>
                    </div>
                    <div id="kt_accordion_panduan_body_3" class="fs-6 collapse ps-10" data-bs-parent="#kt_accordion_panduan">
                        <p class="mb-3">Setelah data Guru dan Tahun Ajaran siap, Anda dapat membuat Kelas dan mendaftarkan siswa ke kelas masing-masing.</p>
                        <ul class="ps-5 mb-4">
                            <li class="mb-2"><strong>Data Kelas</strong>: Akses menu <span class="badge badge-light-primary fs-7">Master Data > Kelas > Data Kelas</span>. Buat tingkat kelas (contoh: X - 1) dan pilih Wali Kelas dari daftar guru yang tersedia.</li>
                            <li class="mb-2"><strong>Pembagian Kelas</strong>: Akses menu <span class="badge badge-light-primary fs-7">Master Data > Kelas > Pembagian Kelas</span>. Di sini Anda dapat memetakan Siswa ke dalam kelas yang telah dibuat. Klik tombol <span class="text-primary fw-bold">Detail</span> pada kelas target, lalu gunakan tombol aksi untuk menambahkan atau mengeluarkan siswa dari kelas tersebut.</li>
                        </ul>
                    </div>
                </div>
                <!--end::Accordion Item 3-->

                <!--begin::Accordion Item 4-->
                <div class="mb-5">
                    <div class="accordion-header py-3 d-flex collapsed" data-bs-toggle="collapse" data-bs-target="#kt_accordion_panduan_body_4">
                        <span class="accordion-icon"><i class="bi bi-chevron-right fs-4"></i></span>
                        <h3 class="fs-4 fw-bolder text-gray-800 px-3 mb-0">Langkah 4: Mengelola Data Siswa & Akun Pengguna</h3>
                    </div>
                    <div id="kt_accordion_panduan_body_4" class="fs-6 collapse ps-10" data-bs-parent="#kt_accordion_panduan">
                        <p class="mb-3">Siswa didefinisikan secara terpusat di modul Siswa.</p>
                        <ul class="ps-5 mb-4">
                            <li class="mb-2">Akses menu <span class="badge badge-light-primary fs-7">Master Data > Siswa</span>.</li>
                            <li class="mb-2">Menambahkan data siswa otomatis akan membuat akun login user untuk siswa tersebut dengan hak akses (role) sebagai **Siswa** secara instan.</li>
                            <li class="mb-2">Import siswa secara masal dapat dilakukan menggunakan file Excel dengan mengunduh **Template Excel Kosong** di dalam modal import siswa. Pastikan format kolom (**Nama, NIS, Email, Kelas, Jenis Kelamin, Alamat**) terisi dengan benar.</li>
                        </ul>
                    </div>
                </div>
                <!--end::Accordion Item 4-->

                <!--begin::Accordion Item 5-->
                <div class="mb-5">
                    <div class="accordion-header py-3 d-flex collapsed" data-bs-toggle="collapse" data-bs-target="#kt_accordion_panduan_body_5">
                        <span class="accordion-icon"><i class="bi bi-chevron-right fs-4"></i></span>
                        <h3 class="fs-4 fw-bolder text-gray-800 px-3 mb-0">Langkah 5: Mata Pelajaran & Jadwal Pelajaran</h3>
                    </div>
                    <div id="kt_accordion_panduan_body_5" class="fs-6 collapse ps-10" data-bs-parent="#kt_accordion_panduan">
                        <p class="mb-3">Konfigurasi materi pelajaran dan jadwal harian sekolah:</p>
                        <ul class="ps-5 mb-4">
                            <li class="mb-2"><strong>Mata Pelajaran</strong>: Akses menu <span class="badge badge-light-primary fs-7">Master Data > Mata Pelajaran</span>. Masukkan nama mapel, kode mapel unik, dan tentukan Guru Pengampunya.</li>
                            <li class="mb-2"><strong>Jadwal Pelajaran</strong>: Akses menu <span class="badge badge-light-primary fs-7">Master Data > Jadwal Pelajaran</span> (atau konfigurasi jadwal terkait). Hubungkan mata pelajaran dengan kelas, ruangan, hari, serta jam pelajaran tertentu.</li>
                        </ul>
                    </div>
                </div>
                <!--end::Accordion Item 5-->

                <!--begin::Accordion Item 6-->
                <div class="mb-5">
                    <div class="accordion-header py-3 d-flex collapsed" data-bs-toggle="collapse" data-bs-target="#kt_accordion_panduan_body_6">
                        <span class="accordion-icon"><i class="bi bi-chevron-right fs-4"></i></span>
                        <h3 class="fs-4 fw-bolder text-gray-800 px-3 mb-0">Langkah 6: Sinkronisasi Fingerprint & Manajemen Presensi</h3>
                    </div>
                    <div id="kt_accordion_panduan_body_6" class="fs-6 collapse ps-10" data-bs-parent="#kt_accordion_panduan">
                        <p class="mb-3">Presence Sync mendukung pencatatan kehadiran otomatis lewat integrasi mesin sidik jari.</p>
                        <ul class="ps-5 mb-4">
                            <li class="mb-2"><strong>Data Perangkat</strong>: Daftarkan mesin fingerprint di menu <span class="badge badge-light-primary fs-7">Master Data > Perangkat Fingerprint > Data Perangkat</span>.</li>
                            <li class="mb-2"><strong>Log Scan Fingerprint</strong>: Log mentah hasil scan sidik jari dapat dipantau di menu <span class="badge badge-light-primary fs-7">Master Data > Perangkat Fingerprint > Log Scan Fingerprint</span>.</li>
                            <li class="mb-2"><strong>Kehadiran Siswa</strong>: Hasil sinkronisasi sidik jari dikompilasi secara real-time ke dalam tabel <span class="badge badge-light-primary fs-7">Kehadiran Siswa</span>, yang menampilkan status hadir, terlambat, izin/sakit, atau alpa.</li>
                        </ul>
                    </div>
                </div>
                <!--end::Accordion Item 6-->

                <!--begin::Accordion Item 7-->
                <div class="mb-5">
                    <div class="accordion-header py-3 d-flex collapsed" data-bs-toggle="collapse" data-bs-target="#kt_accordion_panduan_body_7">
                        <span class="accordion-icon"><i class="bi bi-chevron-right fs-4"></i></span>
                        <h3 class="fs-4 fw-bolder text-danger px-3 mb-0">Aturan Khusus: Keamanan Relasi Data & Penghapusan</h3>
                    </div>
                    <div id="kt_accordion_panduan_body_7" class="fs-6 collapse ps-10" data-bs-parent="#kt_accordion_panduan">
                        <p class="mb-3">Aplikasi ini didesain dengan proteksi data yang ketat untuk mencegah terhapusnya riwayat presensi secara tidak sengaja.</p>
                        <div class="alert alert-light-danger border border-danger d-flex align-items-center p-5 mb-4">
                            <span class="svg-icon svg-icon-2hx svg-icon-danger me-4">
                                {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg") !!}
                            </span>
                            <div class="d-flex flex-column">
                                <h5 class="fw-bolder text-danger">Peringatan Penghapusan Data Terkait</h5>
                                <span>Apabila Anda mencoba menghapus data (misalnya Guru atau Kelas) yang masih terikat dengan data lain (seperti Mata Pelajaran, Siswa, atau Jadwal Pelajaran), sistem akan memunculkan pop-up SweetAlert2 berisi tautan navigasi langsung ke tabel relasi bersangkutan. Anda harus menghapus atau mengubah relasi tersebut terlebih dahulu sebelum dapat menghapus data utama.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Accordion Item 7-->

            </div>
            <!--end::Accordion-->
        </div>
        <!--end::Card Body-->
    </div>
    <!--end::Card-->
</x-base-layout>
