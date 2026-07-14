<x-base-layout>
@include('pages.absensi._partials.toolbar')


<!--begin::Placeholder Content-->
<div class="card mt-2">
    <div class="card-body">
        <div class="text-center py-10">
            <div class="mb-5">
                {!! theme()->getSvgIcon("icons/duotune/general/gen025.svg", "svg-icon-5x text-primary") !!}
            </div>
            <h3 class="text-dark fw-bolder mb-3">Selamat Datang di PresenceSync</h3>
            <div class="text-muted fw-bold fs-5">
                Gunakan menu di sebelah kiri untuk mengakses fitur sistem absensi.
            </div>
        </div>
    </div>
</div>
<!--end::Placeholder Content-->

@php $showPanduan = !session()->get('panduan_singkat_shown', false); @endphp

<!-- Modal Panduan Singkat -->
<div class="modal fade" id="modal_panduan_singkat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-550px">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0 justify-content-end">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body scroll-y mx-5 mx-xl-10 pt-0 pb-8">
                <div class="text-center mb-6">
                    <span class="svg-icon svg-icon-2hx svg-icon-primary mb-2 d-block text-center justify-content-center">
                        {!! theme()->getSvgIcon("demo1/media/icons/duotune/general/book-icon.svg", "svg-icon-2hx svg-icon-primary") !!}
                    </span>
                    <h2 class="mb-1 text-gray-900 fs-2">Panduan Singkat Penggunaan</h2>
                    <div class="text-muted fw-bold fs-6">Langkah cepat mengonfigurasi sistem Presence Sync</div>
                </div>
                
                <div class="mb-8">
                    <!-- Flow steps -->
                    <div class="d-flex align-items-center mb-4">
                        <span class="badge badge-circle badge-light-primary fw-bolder fs-6 me-3 w-30px h-30px d-flex align-items-center justify-content-center">1</span>
                        <div class="flex-grow-1">
                            <h5 class="text-gray-800 fw-bolder mb-0 fs-6">Setup Tahun Ajaran & Aturan Jam</h5>
                            <span class="text-muted fw-bold fs-7">Aktifkan Tahun Ajaran saat ini dan atur jam masuk/pulang sekolah.</span>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-4">
                        <span class="badge badge-circle badge-light-primary fw-bolder fs-6 me-3 w-30px h-30px d-flex align-items-center justify-content-center">2</span>
                        <div class="flex-grow-1">
                            <h5 class="text-gray-800 fw-bolder mb-0 fs-6">Input Data Guru & Kelas</h5>
                            <span class="text-muted fw-bold fs-7">Daftarkan Guru dan buat data Kelas dengan Wali Kelas terpilih.</span>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-4">
                        <span class="badge badge-circle badge-light-primary fw-bolder fs-6 me-3 w-30px h-30px d-flex align-items-center justify-content-center">3</span>
                        <div class="flex-grow-1">
                            <h5 class="text-gray-800 fw-bolder mb-0 fs-6">Input Siswa & Pembagian Kelas</h5>
                            <span class="text-muted fw-bold fs-7">Import data Siswa lalu masukkan mereka ke dalam kelas masing-masing.</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-4">
                        <span class="badge badge-circle badge-light-primary fw-bolder fs-6 me-3 w-30px h-30px d-flex align-items-center justify-content-center">4</span>
                        <div class="flex-grow-1">
                            <h5 class="text-gray-800 fw-bolder mb-0 fs-6">Mata Pelajaran & Aturan Jam</h5>
                            <span class="text-muted fw-bold fs-7">Daftarkan Mata Pelajaran beserta Guru Pengampu.</span>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-4">
                        <span class="badge badge-circle badge-light-primary fw-bolder fs-6 me-3 w-30px h-30px d-flex align-items-center justify-content-center">5</span>
                        <div class="flex-grow-1">
                            <h5 class="text-gray-800 fw-bolder mb-0 fs-6">Sinkronisasi Fingerprint & Laporan</h5>
                            <span class="text-muted fw-bold fs-7">Pantau presensi otomatis dari sidik jari dan cetak rekapitulasi laporan.</span>
                        </div>
                    </div>
                </div>

                <div class="text-center pt-2">
                    <button type="button" class="btn btn-light btn-sm me-3" data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ route('panduan.index') }}" class="btn btn-primary btn-sm">Buka Panduan Lengkap</a>
                </div>
            </div>
        </div>
    </div>
</div>

@if($showPanduan)
    @php session()->put('panduan_singkat_shown', true); @endphp
    @push('scripts')
    <script>
        $(document).ready(function() {
            var modal = new bootstrap.Modal(document.getElementById('modal_panduan_singkat'));
            modal.show();
        });
    </script>
    @endpush
@endif

</x-base-layout>
