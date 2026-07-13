<x-base-layout>
    @include('pages.absensi._partials.toolbar')

    <!--begin::Alerts-->
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center p-5 mb-10">
            <span class="svg-icon svg-icon-2hx svg-icon-success me-4">
                {!! theme()->getSvgIcon("icons/duotune/general/gen048.svg") !!}
            </span>
            <div class="d-flex flex-column">
                <h4 class="mb-1 text-dark">Sukses</h4>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
            <span class="svg-icon svg-icon-2hx svg-icon-danger me-4">
                {!! theme()->getSvgIcon("icons/duotune/general/gen040.svg") !!}
            </span>
            <div class="d-flex flex-column">
                <h4 class="mb-1 text-dark">Error</h4>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif
    <!--end::Alerts-->

    <!--begin::Card-->
    <div class="card mt-2 mt-lg-5 shadow-sm border-0">
        <!--begin::Card Header-->
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center gap-3">
                    <span class="svg-icon svg-icon-1 svg-icon-primary">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen019.svg") !!}
                    </span>
                    <h3 class="fw-bolder m-0">Restriksi Kelas Siswa</h3>
                </div>
            </div>
        </div>
        <!--end::Card Header-->

        <!--begin::Form-->
        <form action="{{ route('pengaturan-restriksi.kelas.update') }}" method="POST" id="form_restriksi_kelas">
            @csrf
            
            <!--begin::Card Body-->
            <div class="card-body fs-6 text-gray-700">
                
                <!--begin::Info Alert / Callout-->
                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-8">
                    <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                        {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg") !!}
                    </span>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-bold">
                            <h4 class="text-gray-900 fw-bolder">Tentang Fitur Ini</h4>
                            <div class="fs-6 text-gray-700">
                                Gunakan pengaturan ini untuk mengontrol apakah siswa memiliki hak akses untuk mengubah kelas mereka sendiri di halaman edit profil mereka.
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Info Alert / Callout-->

                <!--begin::Setting Row-->
                <div class="row align-items-center border-bottom pb-8 mb-8">
                    <div class="col-md-8 mb-4 mb-md-0">
                        <h5 class="text-gray-900 fw-bold fs-5 mb-1">Izinkan Siswa Mengubah Kelas Mandiri</h5>
                        <p class="text-muted fs-7 mb-0">
                            Ketika diaktifkan, input kelas pada profil siswa menjadi dropdown pilihan kelas yang aktif. Ketika dinonaktifkan, input tersebut akan dikunci menjadi read-only.
                        </p>
                    </div>
                    <div class="col-md-4 d-flex justify-content-md-end justify-content-start align-items-center gap-4">
                        <!-- Dynamic Status Badge -->
                        <span id="toggle_status_badge" class="badge {{ $restriksiKelas === 'on' ? 'badge-light-success text-success' : 'badge-light-danger text-danger' }} fw-bolder px-4 py-3 fs-7">
                            {{ $restriksiKelas === 'on' ? 'AKTIF (Siswa Bisa Edit)' : 'NONAKTIF (Read-Only)' }}
                        </span>

                        <!-- Switch Toggle -->
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input w-50px h-30px cursor-pointer" type="checkbox" name="restriksi_kelas" value="on" id="restriksi_kelas_toggle" {{ $restriksiKelas === 'on' ? 'checked' : '' }} />
                        </div>
                    </div>
                </div>
                <!--end::Setting Row-->

                <!--begin::Detailed explanation / comparison-->
                <div class="row g-5">
                    <div class="col-lg-6">
                        <div class="card bg-light-success border border-success border-dashed p-6 h-100">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="svg-icon svg-icon-2 svg-icon-success">
                                    {!! theme()->getSvgIcon("icons/duotune/general/gen043.svg") !!}
                                </span>
                                <h6 class="text-success fw-bolder m-0">Kondisi AKTIF (ON)</h6>
                            </div>
                            <ul class="text-gray-700 fs-7 mb-0 ps-5">
                                <li class="mb-2">Siswa dapat memperbarui data kelas secara mandiri tanpa bantuan Admin.</li>
                                <li class="mb-2">Cocok digunakan selama periode kenaikan kelas atau awal tahun ajaran baru.</li>
                                <li>Mengurangi beban kerja administrasi Admin dalam mengedit satu per satu data kelas siswa.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card bg-light-danger border border-danger border-dashed p-6 h-100">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="svg-icon svg-icon-2 svg-icon-danger">
                                    {!! theme()->getSvgIcon("icons/duotune/general/gen050.svg") !!}
                                </span>
                                <h6 class="text-danger fw-bolder m-0">Kondisi NONAKTIF (OFF)</h6>
                            </div>
                            <ul class="text-gray-700 fs-7 mb-0 ps-5">
                                <li class="mb-2">Siswa tidak dapat mengubah kelasnya sendiri di halaman edit profil.</li>
                                <li class="mb-2">Mencegah kesalahan input atau perubahan kelas yang tidak disengaja oleh siswa selama masa kegiatan belajar mengajar berjalan.</li>
                                <li>Perubahan data kelas hanya bisa dilakukan oleh Administrator melalui menu data master siswa.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!--end::Detailed explanation / comparison-->

            </div>
            <!--end::Card Body-->

            <!--begin::Card Footer-->
            <div class="card-footer d-flex justify-content-end py-6 px-9 border-0">
                <button type="submit" class="btn btn-primary" id="btn_save_settings">
                    <span class="indicator-label">Simpan Pengaturan</span>
                </button>
            </div>
            <!--end::Card Footer-->
        </form>
        <!--end::Form-->
    </div>
    <!--end::Card-->

    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toggle = document.getElementById('restriksi_kelas_toggle');
                const badge = document.getElementById('toggle_status_badge');
                const form = document.getElementById('form_restriksi_kelas');

                if (toggle && badge) {
                    toggle.addEventListener('change', function() {
                        if (this.checked) {
                            badge.className = 'badge badge-light-success text-success fw-bolder px-4 py-3 fs-7';
                            badge.textContent = 'AKTIF (Siswa Bisa Edit)';
                        } else {
                            badge.className = 'badge badge-light-danger text-danger fw-bolder px-4 py-3 fs-7';
                            badge.textContent = 'NONAKTIF (Read-Only)';
                        }
                    });
                }

                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        Swal.fire({
                            icon: 'question',
                            title: 'Simpan Perubahan?',
                            text: 'Apakah Anda yakin ingin memperbarui pengaturan restriksi kelas siswa?',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Simpan',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#009EF7',
                            cancelButtonColor: '#7E8299'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                }
            });
        </script>
    @endsection
</x-base-layout>
