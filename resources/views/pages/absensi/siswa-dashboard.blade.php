<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => ''
])

<!--begin::Row 1: 5 Metric Cards Personal Siswa (SAMA PERSIS GAYA UI DASHBOARD ADMIN)-->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-5 mb-8">
    <!-- Card 1: Profil Siswa -->
    <div class="col">
        <div class="card flex-center h-100 min-w-100px p-6 bg-light-primary border border-primary text-center">
            <span class="svg-icon svg-icon-3x svg-icon-primary mb-2">
                {!! theme()->getSvgIcon("icons/duotune/communication/com006.svg", "svg-icon-3x svg-icon-primary") !!}
            </span>
            <span class="fs-6 fw-bold text-gray-700 pb-1">Profil Siswa</span>
            <span class="fs-3 fw-bolder text-primary text-truncate w-100" title="{{ $siswa->nama }}">{{ $siswa->nama }}</span>
            <span class="fs-8 fw-bold text-gray-500">NIS: {{ $siswa->nis }}</span>
            <span class="fs-8 fw-bold text-gray-500">Kelas {{ $siswa->kelas ? $siswa->kelas->tingkat . ' ' . $siswa->kelas->nama : '-' }}</span>
        </div>
    </div>

    <!-- Card 2: Status Absen Hari Ini -->
    <div class="col">
        @php
            $bgStatus = 'bg-light-danger border-danger';
            $textStatus = 'text-danger';
            $svgColor = 'svg-icon-danger';
            if ($hasCheckedInToday && $kehadiranHariIni) {
                if ($kehadiranHariIni->status === 'hadir') {
                    $bgStatus = 'bg-light-success border-success';
                    $textStatus = 'text-success';
                    $svgColor = 'svg-icon-success';
                } elseif ($kehadiranHariIni->status === 'terlambat') {
                    $bgStatus = 'bg-light-warning border-warning';
                    $textStatus = 'text-warning';
                    $svgColor = 'svg-icon-warning';
                } elseif (in_array($kehadiranHariIni->status, ['sakit', 'izin'])) {
                    $bgStatus = 'bg-light-info border-info';
                    $textStatus = 'text-info';
                    $svgColor = 'svg-icon-info';
                }
            }
        @endphp
        <div class="card flex-center h-100 min-w-100px p-6 {{ $bgStatus }} border text-center">
            <span class="svg-icon svg-icon-3x {{ $svgColor }} mb-2">
                {!! theme()->getSvgIcon("icons/duotune/general/gen014.svg", "svg-icon-3x " . $svgColor) !!}
            </span>
            <span class="fs-6 fw-bold text-gray-700 pb-1">Status Hari Ini</span>
            @if ($hasCheckedInToday && $kehadiranHariIni)
                <span class="fs-2hx fw-bolder {{ $textStatus }}">{{ ucfirst($kehadiranHariIni->status) }}</span>
                <span class="fs-8 fw-bold text-gray-500">Masuk: {{ $kehadiranHariIni->jam_masuk ?? '-' }}</span>
            @else
                <span class="fs-2hx fw-bolder text-danger">Belum Absen</span>
                <span class="fs-8 fw-bold text-gray-500">Silakan Presensi</span>
            @endif
        </div>
    </div>

    <!-- Card 3: Total Hadir -->
    <div class="col">
        <div class="card flex-center h-100 min-w-100px p-6 bg-light-info border border-info text-center">
            <span class="svg-icon svg-icon-3x svg-icon-info mb-2">
                {!! theme()->getSvgIcon("icons/duotune/communication/com013.svg", "svg-icon-3x svg-icon-info") !!}
            </span>
            <span class="fs-6 fw-bold text-gray-700 pb-1">Total Hadir</span>
            <span class="fs-2hx fw-bolder text-info">{{ $kehadirans->whereIn('status', ['hadir', 'terlambat'])->count() }}</span>
            <span class="fs-8 fw-bold text-gray-500">Hari Hadir</span>
        </div>
    </div>

    <!-- Card 4: Sakit & Izin -->
    <div class="col">
        <div class="card flex-center h-100 min-w-100px p-6 bg-light-warning border border-warning text-center">
            <span class="svg-icon svg-icon-3x svg-icon-warning mb-2">
                {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg", "svg-icon-3x svg-icon-warning") !!}
            </span>
            <span class="fs-6 fw-bold text-gray-700 pb-1">Sakit & Izin</span>
            <span class="fs-2hx fw-bolder text-warning">{{ $kehadirans->whereIn('status', ['sakit', 'izin'])->count() }}</span>
            <span class="fs-8 fw-bold text-gray-500">Permohonan Izin</span>
        </div>
    </div>

    <!-- Card 5: Tanpa Keterangan / Alpha -->
    <div class="col">
        <div class="card flex-center h-100 min-w-100px p-6 bg-light-danger border border-danger text-center">
            <span class="svg-icon svg-icon-3x svg-icon-danger mb-2">
                {!! theme()->getSvgIcon("icons/duotune/general/gen050.svg", "svg-icon-3x svg-icon-danger") !!}
            </span>
            <span class="fs-6 fw-bold text-gray-700 pb-1">Tanpa Keterangan</span>
            <span class="fs-2hx fw-bolder text-danger">{{ $kehadirans->where('status', 'alpha')->count() }}</span>
            <span class="fs-8 fw-bold text-gray-500">Hari Alpha</span>
        </div>
    </div>
</div>
<!--end::Row 1-->

<!--begin::Row 2: Tren Kehadiran & Aktivitas Terakhir-->
<div class="row g-5 g-xl-8 mb-8">
    <!-- Kolom Kiri: Tren & Aksi Presensi -->
    <div class="col-xl-7">
        <div class="card card-flush h-xl-100 shadow-sm">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-boldest text-gray-800 fs-4">Ringkasan Kehadiran Saya</span>
                    <span class="text-gray-400 mt-1 fw-bold fs-7">Analisis data kehadiran personal siswa</span>
                </h3>
                <div class="card-toolbar">
                    @if (!$hasCheckedInToday)
                        <button type="button" class="btn btn-warning btn-sm me-2" onclick="openModalSafe('modal_izin')">
                            <i class="bi bi-file-earmark-text me-1"></i> Ajukan Izin
                        </button>
                        <button type="button" class="btn btn-success btn-sm" onclick="openModalSafe('modal_presensi')">
                            <i class="bi bi-camera me-1"></i> Presensi Sekarang
                        </button>
                    @else
                        <span class="badge badge-light-success p-3 fw-bold fs-7">
                            <i class="bi bi-check-circle-fill text-success me-1"></i> Anda Sudah Absen Hari Ini
                        </span>
                    @endif
                </div>
            </div>
            <div class="card-body pt-5">
                <!-- Stat Badges Breakdown -->
                <div class="d-flex flex-wrap gap-4 mb-6">
                    <div class="border border-dashed border-gray-300 rounded p-4 flex-grow-1 text-center bg-light">
                        <div class="fs-7 text-gray-400 fw-bold">Hadir Tepat Waktu</div>
                        <div class="fs-2 fw-boldest text-success">{{ $kehadirans->where('status', 'hadir')->count() }}</div>
                    </div>
                    <div class="border border-dashed border-gray-300 rounded p-4 flex-grow-1 text-center bg-light">
                        <div class="fs-7 text-gray-400 fw-bold">Terlambat</div>
                        <div class="fs-2 fw-boldest text-warning">{{ $kehadirans->where('status', 'terlambat')->count() }}</div>
                    </div>
                    <div class="border border-dashed border-gray-300 rounded p-4 flex-grow-1 text-center bg-light">
                        <div class="fs-7 text-gray-400 fw-bold">Sakit</div>
                        <div class="fs-2 fw-boldest text-primary">{{ $kehadirans->where('status', 'sakit')->count() }}</div>
                    </div>
                    <div class="border border-dashed border-gray-300 rounded p-4 flex-grow-1 text-center bg-light">
                        <div class="fs-7 text-gray-400 fw-bold">Izin</div>
                        <div class="fs-2 fw-boldest text-info">{{ $kehadirans->where('status', 'izin')->count() }}</div>
                    </div>
                </div>

                <!-- Link ke Tabel Lengkap -->
                <div class="d-flex align-items-center justify-content-between p-5 rounded bg-light-primary border border-primary border-opacity-25">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-table fs-1 text-primary me-3"></i>
                        <div>
                            <div class="fw-boldest text-gray-800 fs-6">Tabel Kehadiran Lengkap</div>
                            <div class="fs-7 text-gray-600">Lihat seluruh riwayat presensi, filter bulanan, dan cetak laporan</div>
                        </div>
                    </div>
                    <a href="{{ url('/absensi/siswa/kehadiran') }}" class="btn btn-primary btn-sm fw-bold">
                        Buka Tabel <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Aktivitas Presensi Terbaru -->
    <div class="col-xl-5">
        <div class="card card-flush h-xl-100 shadow-sm">
            <div class="card-header pt-7 mb-3">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-boldest text-gray-800 fs-4">Riwayat Terakhir Saya</span>
                    <span class="text-gray-400 mt-1 fw-bold fs-7">5 log presensi paling baru</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                <div class="timeline-label">
                    @forelse($kehadirans->take(5) as $kh)
                        <div class="timeline-item">
                            <div class="timeline-label fw-boldest text-gray-800 fs-7" style="width: 70px;">
                                {{ \Carbon\Carbon::parse($kh->tanggal)->format('d/m') }}
                            </div>
                            <div class="timeline-badge">
                                @if($kh->status === 'hadir')
                                    <i class="fa fa-genderless text-success fs-1"></i>
                                @elseif($kh->status === 'terlambat')
                                    <i class="fa fa-genderless text-warning fs-1"></i>
                                @elseif($kh->status === 'sakit')
                                    <i class="fa fa-genderless text-primary fs-1"></i>
                                @elseif($kh->status === 'izin')
                                    <i class="fa fa-genderless text-info fs-1"></i>
                                @else
                                    <i class="fa fa-genderless text-danger fs-1"></i>
                                @endif
                            </div>
                            <div class="timeline-content fw-bold text-gray-800 ps-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-7 fw-boldest">
                                        @if($kh->status === 'hadir')
                                            <span class="badge badge-light-success fs-8">Hadir</span>
                                        @elseif($kh->status === 'terlambat')
                                            <span class="badge badge-light-warning fs-8">Terlambat</span>
                                        @elseif($kh->status === 'sakit')
                                            <span class="badge badge-light-primary fs-8">Sakit</span>
                                        @elseif($kh->status === 'izin')
                                            <span class="badge badge-light-info fs-8">Izin</span>
                                        @else
                                            <span class="badge badge-light-danger fs-8">Alpha</span>
                                        @endif
                                    </span>
                                    <span class="text-gray-400 fs-8 fw-semibold">{{ $kh->jam_masuk ?? '-' }}</span>
                                </div>
                                <div class="text-gray-500 fs-8 fw-normal mt-1">
                                    {{ $kh->keterangan ?? ($kh->jam_masuk ? 'Masuk pukul ' . $kh->jam_masuk : 'Tidak ada keterangan') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-8">
                            Belum ada catatan presensi.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Row 2-->

<!--begin::Modal Presensi Sekarang-->
<div class="modal fade" id="modal_presensi" tabindex="-1" aria-labelledby="modal_presensi_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('siswa.presensi') }}" id="form_presensi">
                @csrf
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white fw-bolder" id="modal_presensi_label">
                        <i class="bi bi-camera text-white me-2"></i> Presensi Sekarang
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info d-flex align-items-center p-5 mb-6">
                        <i class="bi bi-info-circle-fill text-info fs-3 me-3"></i>
                        <div class="fs-7">
                            Pastikan <strong>kamera</strong> dan <strong>GPS</strong> pada perangkat Anda telah diaktifkan. Foto wajah dan lokasi Anda akan direkam sebagai bukti kehadiran.
                        </div>
                    </div>

                    {{-- Kamera --}}
                    <div class="mb-6 text-center">
                        <label class="form-label fw-bold fs-6 mb-3">Ambil Foto Kehadiran</label>
                        <div class="position-relative mx-auto" style="max-width: 480px;">
                            <video id="presensi_video" class="w-100 rounded border" autoplay playsinline style="display:none;"></video>
                            <canvas id="presensi_canvas" class="w-100 rounded border" style="display:none;"></canvas>
                            <img id="presensi_preview" class="w-100 rounded border" style="display:none;" alt="Preview Foto"/>
                        </div>
                        <div class="mt-3 d-flex justify-content-center gap-3">
                            <button type="button" class="btn btn-primary btn-sm" id="presensi_btn_start_cam">
                                <i class="bi bi-camera me-1"></i> Buka Kamera
                            </button>
                            <button type="button" class="btn btn-success btn-sm" id="presensi_btn_capture" style="display:none;">
                                <i class="bi bi-camera-fill me-1"></i> Ambil Foto
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" id="presensi_btn_retake" style="display:none;">
                                <i class="bi bi-arrow-repeat me-1"></i> Ulangi
                            </button>
                        </div>
                        <input type="hidden" name="foto_base64" id="presensi_foto_base64">
                    </div>

                    {{-- Lokasi GPS --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold fs-6">Lokasi GPS</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-grow-1">
                                <input type="text" id="presensi_lokasi_display" class="form-control form-control-solid" readonly placeholder="Menunggu lokasi GPS..." />
                            </div>
                            <button type="button" class="btn btn-light-primary btn-sm" id="presensi_btn_gps">
                                <i class="bi bi-geo-alt-fill me-1"></i> Ambil Lokasi
                            </button>
                        </div>
                        <input type="hidden" name="latitude" id="presensi_latitude">
                        <input type="hidden" name="longitude" id="presensi_longitude">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="presensi_btn_submit" disabled>
                        <i class="bi bi-check-circle me-1"></i> Kirim Presensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal Presensi Sekarang-->

<!--begin::Modal Ajukan Izin-->
<div class="modal fade" id="modal_izin" tabindex="-1" aria-labelledby="modal_izin_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('siswa.izin') }}" id="form_izin">
                @csrf
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white fw-bolder" id="modal_izin_label">
                        <i class="bi bi-file-earmark-text text-white me-2"></i> Ajukan Izin / Sakit
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning d-flex align-items-center p-5 mb-6">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-3 me-3"></i>
                        <div class="fs-7">
                            Silakan isi formulir berikut untuk mengajukan izin atau sakit. <strong>Foto bukti</strong> dan <strong>lokasi GPS</strong> wajib disertakan.
                        </div>
                    </div>

                    {{-- Jenis Izin --}}
                    <div class="mb-5">
                        <label class="form-label fw-bold fs-6 required">Jenis Izin</label>
                        <select name="status" class="form-select form-select-solid" required id="izin_status">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                        </select>
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-5">
                        <label class="form-label fw-bold fs-6 required">Alasan / Keterangan</label>
                        <textarea name="keterangan" class="form-control form-control-solid" rows="3" maxlength="500" required placeholder="Jelaskan alasan izin/sakit Anda..." id="izin_keterangan"></textarea>
                        <div class="form-text">Maksimal 500 karakter.</div>
                    </div>

                    {{-- Kamera --}}
                    <div class="mb-6 text-center">
                        <label class="form-label fw-bold fs-6 mb-3">Ambil Foto Bukti</label>
                        <div class="position-relative mx-auto" style="max-width: 480px;">
                            <video id="izin_video" class="w-100 rounded border" autoplay playsinline style="display:none;"></video>
                            <canvas id="izin_canvas" class="w-100 rounded border" style="display:none;"></canvas>
                            <img id="izin_preview" class="w-100 rounded border" style="display:none;" alt="Preview Foto"/>
                        </div>
                        <div class="mt-3 d-flex justify-content-center gap-3">
                            <button type="button" class="btn btn-primary btn-sm" id="izin_btn_start_cam">
                                <i class="bi bi-camera me-1"></i> Buka Kamera
                            </button>
                            <button type="button" class="btn btn-success btn-sm" id="izin_btn_capture" style="display:none;">
                                <i class="bi bi-camera-fill me-1"></i> Ambil Foto
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" id="izin_btn_retake" style="display:none;">
                                <i class="bi bi-arrow-repeat me-1"></i> Ulangi
                            </button>
                        </div>
                        <input type="hidden" name="foto_base64" id="izin_foto_base64">
                    </div>

                    {{-- Lokasi GPS --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold fs-6">Lokasi GPS</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-grow-1">
                                <input type="text" id="izin_lokasi_display" class="form-control form-control-solid" readonly placeholder="Menunggu lokasi GPS..." />
                            </div>
                            <button type="button" class="btn btn-light-primary btn-sm" id="izin_btn_gps">
                                <i class="bi bi-geo-alt-fill me-1"></i> Ambil Lokasi
                            </button>
                        </div>
                        <input type="hidden" name="latitude" id="izin_latitude">
                        <input type="hidden" name="longitude" id="izin_longitude">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning" id="izin_btn_submit" disabled>
                        <i class="bi bi-send me-1"></i> Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal Ajukan Izin-->

{{-- begin::Scripts for Camera & GPS --}}
@push('scripts')
<script>
// Safe modal opener - works with SPA navigation
function openModalSafe(modalId) {
    var el = document.getElementById(modalId);
    if (el) {
        var modal = bootstrap.Modal.getOrCreateInstance(el);
        modal.show();
    } else {
        console.error('Modal #' + modalId + ' not found in DOM.');
    }
}

(function() {

    // ========== HELPER FUNCTIONS ==========
    function initCamera(videoEl, canvasEl, previewEl, btnStart, btnCapture, btnRetake, hiddenInput, onCaptureCallback) {
        let stream = null;

        btnStart.addEventListener('click', function() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } })
                .then(function(mediaStream) {
                    stream = mediaStream;
                    videoEl.srcObject = stream;
                    videoEl.style.display = 'block';
                    previewEl.style.display = 'none';
                    canvasEl.style.display = 'none';
                    btnStart.style.display = 'none';
                    btnCapture.style.display = 'inline-block';
                    btnRetake.style.display = 'none';
                })
                .catch(function(err) {
                    Swal.fire('Error', 'Tidak dapat mengakses kamera: ' + err.message, 'error');
                });
        });

        btnCapture.addEventListener('click', function() {
            canvasEl.width = videoEl.videoWidth;
            canvasEl.height = videoEl.videoHeight;
            canvasEl.getContext('2d').drawImage(videoEl, 0, 0);
            var dataUrl = canvasEl.toDataURL('image/jpeg', 0.8);
            hiddenInput.value = dataUrl;
            previewEl.src = dataUrl;
            previewEl.style.display = 'block';
            videoEl.style.display = 'none';
            btnCapture.style.display = 'none';
            btnRetake.style.display = 'inline-block';
            // Stop camera
            if (stream) {
                stream.getTracks().forEach(function(track) { track.stop(); });
                stream = null;
            }
            if (onCaptureCallback) onCaptureCallback();
        });

        btnRetake.addEventListener('click', function() {
            hiddenInput.value = '';
            previewEl.style.display = 'none';
            btnRetake.style.display = 'none';
            btnStart.style.display = 'inline-block';
            btnStart.click();
            if (onCaptureCallback) onCaptureCallback();
        });

        // Cleanup stream when modal is hidden
        return function stopStream() {
            if (stream) {
                stream.getTracks().forEach(function(track) { track.stop(); });
                stream = null;
            }
            videoEl.style.display = 'none';
            previewEl.style.display = 'none';
            canvasEl.style.display = 'none';
            btnStart.style.display = 'inline-block';
            btnCapture.style.display = 'none';
            btnRetake.style.display = 'none';
        };
    }

    function initGPS(btnGps, displayInput, latInput, lngInput, onSuccessCallback) {
        btnGps.addEventListener('click', function() {
            if (!navigator.geolocation) {
                Swal.fire('Error', 'Geolocation tidak didukung oleh browser Anda.', 'error');
                return;
            }
            displayInput.value = 'Mengambil lokasi...';
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    latInput.value = position.coords.latitude;
                    lngInput.value = position.coords.longitude;
                    displayInput.value = position.coords.latitude.toFixed(6) + ', ' + position.coords.longitude.toFixed(6);
                    if (onSuccessCallback) onSuccessCallback();
                },
                function(err) {
                    displayInput.value = 'Gagal mendapatkan lokasi';
                    Swal.fire('Error', 'Gagal mendapatkan lokasi GPS: ' + err.message, 'error');
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        });
    }

    // ========== PRESENSI MODAL ==========
    var presensiModal = document.getElementById('modal_presensi');
    if (presensiModal) {
        var presensiSubmitBtn = document.getElementById('presensi_btn_submit');

        function checkPresensiReady() {
            var hasFoto = document.getElementById('presensi_foto_base64').value !== '';
            var hasLat = document.getElementById('presensi_latitude').value !== '';
            var hasLng = document.getElementById('presensi_longitude').value !== '';
            presensiSubmitBtn.disabled = !(hasFoto && hasLat && hasLng);
        }

        var stopPresensiCam = initCamera(
            document.getElementById('presensi_video'),
            document.getElementById('presensi_canvas'),
            document.getElementById('presensi_preview'),
            document.getElementById('presensi_btn_start_cam'),
            document.getElementById('presensi_btn_capture'),
            document.getElementById('presensi_btn_retake'),
            document.getElementById('presensi_foto_base64'),
            checkPresensiReady
        );

        initGPS(
            document.getElementById('presensi_btn_gps'),
            document.getElementById('presensi_lokasi_display'),
            document.getElementById('presensi_latitude'),
            document.getElementById('presensi_longitude'),
            checkPresensiReady
        );

        presensiModal.addEventListener('hidden.bs.modal', function() {
            stopPresensiCam();
            document.getElementById('presensi_foto_base64').value = '';
            document.getElementById('presensi_latitude').value = '';
            document.getElementById('presensi_longitude').value = '';
            document.getElementById('presensi_lokasi_display').value = '';
            presensiSubmitBtn.disabled = true;
        });

        // Auto-fetch GPS on modal open
        presensiModal.addEventListener('shown.bs.modal', function() {
            document.getElementById('presensi_btn_gps').click();
        });
    }

    // ========== IZIN MODAL ==========
    var izinModal = document.getElementById('modal_izin');
    if (izinModal) {
        var izinSubmitBtn = document.getElementById('izin_btn_submit');

        function checkIzinReady() {
            var hasStatus = document.getElementById('izin_status').value !== '';
            var hasKet = document.getElementById('izin_keterangan').value.trim() !== '';
            var hasFoto = document.getElementById('izin_foto_base64').value !== '';
            var hasLat = document.getElementById('izin_latitude').value !== '';
            var hasLng = document.getElementById('izin_longitude').value !== '';
            izinSubmitBtn.disabled = !(hasStatus && hasKet && hasFoto && hasLat && hasLng);
        }

        var stopIzinCam = initCamera(
            document.getElementById('izin_video'),
            document.getElementById('izin_canvas'),
            document.getElementById('izin_preview'),
            document.getElementById('izin_btn_start_cam'),
            document.getElementById('izin_btn_capture'),
            document.getElementById('izin_btn_retake'),
            document.getElementById('izin_foto_base64'),
            checkIzinReady
        );

        initGPS(
            document.getElementById('izin_btn_gps'),
            document.getElementById('izin_lokasi_display'),
            document.getElementById('izin_latitude'),
            document.getElementById('izin_longitude'),
            checkIzinReady
        );

        // Listen for form field changes to enable submit
        document.getElementById('izin_status').addEventListener('change', checkIzinReady);
        document.getElementById('izin_keterangan').addEventListener('input', checkIzinReady);

        izinModal.addEventListener('hidden.bs.modal', function() {
            stopIzinCam();
            document.getElementById('form_izin').reset();
            document.getElementById('izin_foto_base64').value = '';
            document.getElementById('izin_latitude').value = '';
            document.getElementById('izin_longitude').value = '';
            document.getElementById('izin_lokasi_display').value = '';
            izinSubmitBtn.disabled = true;
        });

        // Auto-fetch GPS on modal open
        izinModal.addEventListener('shown.bs.modal', function() {
            document.getElementById('izin_btn_gps').click();
        });
    }

})();
</script>
@endpush
{{-- end::Scripts for Camera & GPS --}}

</x-base-layout>
