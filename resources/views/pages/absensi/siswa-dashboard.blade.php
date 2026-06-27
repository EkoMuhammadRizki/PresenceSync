<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => ''
])

<!--begin::Welcome Card-->
<div class="card mb-8">
    <div class="card-body p-9">
        <div class="d-flex align-items-center">
            <div class="symbol symbol-60px symbol-circle me-5">
                <div class="symbol-label fs-1 bg-light-primary text-primary fw-bolder">
                    {{ substr($siswa->nama, 0, 1) }}
                </div>
            </div>
            <div class="flex-grow-1">
                <h1 class="text-gray-800 fw-boldest mb-1">{{ $siswa->nama }}</h1>
                <div class="text-muted fw-bold fs-6">Siswa Kelas: {{ $siswa->kelas ? $siswa->kelas->tingkat . ' ' . $siswa->kelas->nama : 'Belum Masuk Kelas' }}</div>
            </div>
        </div>
    </div>
</div>
<!--end::Welcome Card-->

<!--begin::Info Cards-->
<div class="row g-6 g-xl-9 mb-8">
    <div class="col-md-4">
        <!--begin::Card-->
        <div class="card card-dashed flex-center min-w-175px my-3 p-6">
            <span class="fs-4 fw-bold text-info pb-1px">Status Hari Ini</span>
            @if ($hasCheckedInToday && $kehadiranHariIni)
                @if ($kehadiranHariIni->status === 'hadir')
                    <span class="fs-2hx fw-bolder text-success">Tepat Waktu</span>
                    <span class="fs-7 fw-bold text-gray-400">Jam Masuk: {{ $kehadiranHariIni->jam_masuk }}</span>
                @elseif ($kehadiranHariIni->status === 'terlambat')
                    <span class="fs-2hx fw-bolder text-warning">Terlambat</span>
                    <span class="fs-7 fw-bold text-gray-400">Jam Masuk: {{ $kehadiranHariIni->jam_masuk }}</span>
                @elseif ($kehadiranHariIni->status === 'sakit')
                    <span class="fs-2hx fw-bolder text-primary">Sakit</span>
                    <span class="fs-7 fw-bold text-gray-400">{{ $kehadiranHariIni->keterangan }}</span>
                @elseif ($kehadiranHariIni->status === 'izin')
                    <span class="fs-2hx fw-bolder text-info">Izin</span>
                    <span class="fs-7 fw-bold text-gray-400">{{ $kehadiranHariIni->keterangan }}</span>
                @endif
            @else
                <span class="fs-2hx fw-bolder text-danger">Belum Absen</span>
                <span class="fs-7 fw-bold text-gray-400">Silakan lakukan presensi hari ini</span>
            @endif
        </div>
        <!--end::Card-->
    </div>

    <div class="col-md-4">
        <!--begin::Card-->
        <div class="card card-dashed flex-center min-w-175px my-3 p-6">
            <span class="fs-4 fw-bold text-success pb-1px">Total Hadir</span>
            <span class="fs-2hx fw-bolder text-dark">{{ $kehadirans->whereIn('status', ['hadir', 'terlambat'])->count() }}</span>
            <span class="fs-7 fw-bold text-gray-400">Pertemuan Semester Ini</span>
        </div>
        <!--end::Card-->
    </div>

    <div class="col-md-4">
        <!--begin::Card-->
        <div class="card card-dashed flex-center min-w-175px my-3 p-6">
            <span class="fs-4 fw-bold text-warning pb-1px">Ketidakhadiran & Izin</span>
            <span class="fs-2hx fw-bolder text-dark">{{ $kehadirans->whereIn('status', ['sakit', 'izin', 'alpha'])->count() }}</span>
            <span class="fs-7 fw-bold text-gray-400">Sakit, Izin & Alpha</span>
        </div>
        <!--end::Card-->
    </div>
</div>
<!--end::Info Cards-->

<!--begin::Card - Riwayat Kehadiran-->
<div class="card">
    <!--begin::Card Header-->
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bolder">Daftar Kehadiran</h3>
        </div>
        <div class="card-toolbar">
            @if (!$hasCheckedInToday)
                <!-- Tombol Izin -->
                <button type="button" class="btn btn-warning btn-sm me-3" data-bs-toggle="modal" data-bs-target="#modal_izin">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen044.svg", "svg-icon-3") !!}
                    Izin
                </button>

                <!-- Tombol Presensi -->
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modal_presensi">
                    {!! theme()->getSvgIcon("icons/duotune/general/gen048.svg", "svg-icon-3") !!}
                    Presensi
                </button>
            @else
                <button type="button" class="btn btn-light-success btn-sm disabled" disabled>
                    {!! theme()->getSvgIcon("icons/duotune/general/gen043.svg", "svg-icon-3") !!}
                    Sudah Absen Hari Ini
                </button>
            @endif
        </div>
    </div>
    <!--end::Card Header-->

    <!--begin::Card Body-->
    <div class="card-body py-4">
        @php
            $daysIndo = [
                'Sunday' => 'Minggu',
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
            ];
        @endphp

        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_kehadiran_siswa">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-50px">No</th>
                    <th class="min-w-150px">Nama</th>
                    <th class="min-w-100px">Hari</th>
                    <th class="min-w-120px">Tanggal</th>
                    <th class="min-w-100px">Jam Masuk</th>
                    <th class="min-w-150px">Keterangan Masuk</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @foreach ($kehadirans as $kh)
                    @php
                        $dateObj = \Carbon\Carbon::parse($kh->tanggal);
                        $hari = $daysIndo[$dateObj->format('l')] ?? 'Senin';
                        $tanggalFormatted = $dateObj->translatedFormat('d F Y');
                        $dayNumber = $dateObj->day;
                    @endphp
                    <tr>
                        <td class="text-gray-800">{{ $dayNumber }}</td>
                        <td>{{ $siswa->nama }}</td>
                        <td>{{ $hari }}</td>
                        <td>{{ $tanggalFormatted }}</td>
                        <td>{{ $kh->jam_masuk ?? '-' }}</td>
                        <td>
                            @if ($kh->status === 'hadir')
                                <span class="badge badge-light-success fw-bolder">Tepat</span>
                            @elseif ($kh->status === 'terlambat')
                                <span class="badge badge-light-warning fw-bolder">Terlambat</span>
                            @elseif ($kh->status === 'sakit')
                                <span class="badge badge-light-primary fw-bolder">Sakit</span>
                            @elseif ($kh->status === 'izin')
                                <span class="badge badge-light-info fw-bolder">Izin</span>
                            @else
                                <span class="badge badge-light-danger fw-bolder">Alpha</span>
                            @endif

                            @if($kh->keterangan)
                                <div class="fs-7 text-muted fw-normal mt-1">{{ $kh->keterangan }}</div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!--end::Card Body-->
</div>
<!--end::Card-->

<!--begin::Modal Presensi-->
<div class="modal fade" id="modal_presensi" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered mw-550px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Presensi Kehadiran</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="btn_close_presensi"></button>
            </div>
            <div class="modal-body mx-4 my-4">
                <form action="{{ route('siswa.presensi') }}" method="POST" id="form_presensi" enctype="multipart/form-data">
                    @csrf

                    <!-- Kamera -->
                    <div class="fv-row mb-5">
                        <label class="required fw-bold fs-6 mb-2">Foto Kehadiran</label>
                        <div class="position-relative rounded overflow-hidden bg-dark" style="width:100%; aspect-ratio:4/3;">
                            <video id="presensi_video" autoplay playsinline muted
                                   style="width:100%; height:100%; object-fit:cover; display:block;"></video>
                            <canvas id="presensi_canvas" style="display:none;"></canvas>
                            <!-- Preview hasil foto -->
                            <img id="presensi_preview" src="" alt="Preview"
                                 style="display:none; width:100%; height:100%; object-fit:cover; position:absolute; top:0; left:0;" />
                            <!-- Overlay tombol -->
                            <div id="camera_controls" class="position-absolute bottom-0 start-0 end-0 d-flex justify-content-center gap-3 p-3">
                                <button type="button" id="btn_capture" class="btn btn-success btn-sm px-5">
                                    <i class="bi bi-camera-fill me-1"></i> Ambil Foto
                                </button>
                            </div>
                            <div id="retake_controls" style="display:none;"
                                 class="position-absolute bottom-0 start-0 end-0 d-flex justify-content-center gap-3 p-3">
                                <button type="button" id="btn_retake" class="btn btn-light btn-sm px-5">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Ulangi
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="foto_base64" id="foto_base64" />
                        <div id="foto_error" class="text-danger fs-7 mt-1" style="display:none;">Foto wajib diambil sebelum presensi.</div>
                    </div>

                    <!-- Maps -->
                    <div class="fv-row mb-3">
                        <label class="required fw-bold fs-6 mb-2">Lokasi Saat Ini</label>
                        <div id="presensi_map" class="rounded" style="width:100%; height:220px; background:#e9ecef;">
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted fs-7" id="map_loading">
                                <span class="spinner-border spinner-border-sm me-2"></span> Memuat lokasi...
                            </div>
                        </div>
                    </div>

                    <!-- Koordinat (read only) -->
                    <div class="fv-row mb-6">
                        <label class="fw-bold fs-6 mb-2">Koordinat</label>
                        <input type="text" id="koordinat_display" class="form-control form-control-solid"
                               placeholder="Mendeteksi koordinat..." readonly />
                        <input type="hidden" name="latitude" id="input_latitude" />
                        <input type="hidden" name="longitude" id="input_longitude" />
                        <div id="lokasi_error" class="text-danger fs-7 mt-1" style="display:none;">Izinkan akses lokasi untuk presensi.</div>
                    </div>

                    <div class="text-center pt-2">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" id="btn_submit_presensi">
                            <span class="indicator-label">
                                <i class="bi bi-check-circle me-1"></i> Kirim Presensi
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::Modal Presensi-->

<!--begin::Modal Izin-->
<div class="modal fade" id="modal_izin" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered mw-550px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Formulir Izin / Sakit</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="btn_close_izin"></button>
            </div>
            <div class="modal-body mx-4 my-4">
                <form class="form" action="{{ route('siswa.izin') }}" method="POST" id="form_izin_siswa" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Pilihan Status -->
                    <div class="fv-row mb-5">
                        <label class="required fw-bold fs-6 mb-2">Jenis Pengajuan</label>
                        <select name="status" class="form-select form-select-solid" required>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                        </select>
                    </div>

                    <!-- Kamera -->
                    <div class="fv-row mb-5">
                        <label class="required fw-bold fs-6 mb-2">Foto Bukti</label>
                        <div class="position-relative rounded overflow-hidden bg-dark" style="width:100%; aspect-ratio:4/3;">
                            <video id="izin_video" autoplay playsinline muted
                                   style="width:100%; height:100%; object-fit:cover; display:block;"></video>
                            <canvas id="izin_canvas" style="display:none;"></canvas>
                            <!-- Preview hasil foto -->
                            <img id="izin_preview" src="" alt="Preview"
                                 style="display:none; width:100%; height:100%; object-fit:cover; position:absolute; top:0; left:0;" />
                            <!-- Overlay tombol -->
                            <div id="izin_camera_controls" class="position-absolute bottom-0 start-0 end-0 d-flex justify-content-center gap-3 p-3">
                                <button type="button" id="btn_capture_izin" class="btn btn-warning btn-sm px-5">
                                    <i class="bi bi-camera-fill me-1"></i> Ambil Foto
                                </button>
                            </div>
                            <div id="izin_retake_controls" style="display:none;"
                                 class="position-absolute bottom-0 start-0 end-0 d-flex justify-content-center gap-3 p-3">
                                <button type="button" id="btn_retake_izin" class="btn btn-light btn-sm px-5">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Ulangi
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="foto_base64" id="izin_foto_base64" />
                        <div id="izin_foto_error" class="text-danger fs-7 mt-1" style="display:none;">Foto wajib diambil sebelum mengirim pengajuan.</div>
                    </div>

                    <!-- Maps -->
                    <div class="fv-row mb-3">
                        <label class="required fw-bold fs-6 mb-2">Lokasi Saat Ini</label>
                        <div id="izin_map" class="rounded" style="width:100%; height:220px; background:#e9ecef;">
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted fs-7" id="izin_map_loading">
                                <span class="spinner-border spinner-border-sm me-2"></span> Memuat lokasi...
                            </div>
                        </div>
                    </div>

                    <!-- Koordinat (read only) -->
                    <div class="fv-row mb-5">
                        <label class="fw-bold fs-6 mb-2">Koordinat</label>
                        <input type="text" id="izin_koordinat_display" class="form-control form-control-solid"
                               placeholder="Mendeteksi koordinat..." readonly />
                        <input type="hidden" name="latitude" id="izin_input_latitude" />
                        <input type="hidden" name="longitude" id="izin_input_longitude" />
                        <div id="izin_lokasi_error" class="text-danger fs-7 mt-1" style="display:none;">Izinkan akses lokasi untuk mengirim pengajuan.</div>
                    </div>

                    <!-- Alasan Izin -->
                    <div class="fv-row mb-2">
                        <label class="required fw-bold fs-6 mb-2">Alasan Izin</label>
                        <textarea name="keterangan" id="textarea_izin" class="form-control form-control-solid"
                                  rows="6" placeholder="Tuliskan alasan izin secara detail (minimal 500 karakter)..."
                                  required minlength="500"></textarea>
                        <div class="d-flex justify-content-between mt-1">
                            <div id="izin_length_error" class="text-danger fs-7" style="display:none;">
                                Alasan izin minimal 500 karakter.
                            </div>
                            <div class="text-muted fs-7 ms-auto">
                                <span id="izin_char_count">0</span> / 500 karakter minimum
                            </div>
                        </div>
                    </div>

                    <!-- Submit / Batal -->
                    <div class="text-center pt-5">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning" id="btn_submit_izin">
                            <span class="indicator-label">Kirim Pengajuan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::Modal Izin-->

@section('scripts')
{{-- Leaflet.js untuk peta --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
$(document).ready(function() {
    // ─── DataTable ───────────────────────────────────────────────
    $('#kt_table_kehadiran_siswa').DataTable({
        dom:"<'table-responsive'tr><'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'li><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>",
        info: true,
        order: [],
        pageLength: 10,
        lengthChange: true
    });

    // ─── Modal Presensi: Kamera & Maps ───────────────────────────
    var mediaStream = null;
    var leafletMap  = null;
    var hasFoto     = false;

    // Mulai kamera & minta lokasi saat modal terbuka
    $('#modal_presensi').on('shown.bs.modal', function() {
        startCamera();
        getLocation();
    });

    // Stop kamera saat modal ditutup
    $('#modal_presensi').on('hide.bs.modal', function() {
        stopCamera();
        hasFoto = false;
        $('#presensi_preview').hide();
        $('#presensi_video').show();
        $('#camera_controls').show();
        $('#retake_controls').hide();
        $('#foto_base64').val('');
        $('#foto_error').hide();
    });

    function startCamera() {
        if (mediaStream) return;
        navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } },
            audio: false
        }).then(function(stream) {
            mediaStream = stream;
            var video = document.getElementById('presensi_video');
            video.srcObject = stream;
        }).catch(function(err) {
            console.warn('Kamera tidak tersedia:', err);
        });
    }

    function stopCamera() {
        if (mediaStream) {
            mediaStream.getTracks().forEach(function(t) { t.stop(); });
            mediaStream = null;
        }
    }

    // Ambil foto
    $('#btn_capture').on('click', function() {
        var video  = document.getElementById('presensi_video');
        var canvas = document.getElementById('presensi_canvas');
        canvas.width  = video.videoWidth  || 640;
        canvas.height = video.videoHeight || 480;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

        var dataUrl = canvas.toDataURL('image/jpeg', 0.85);
        $('#foto_base64').val(dataUrl);
        $('#presensi_preview').attr('src', dataUrl).show();
        $('#presensi_video').hide();
        $('#camera_controls').hide();
        $('#retake_controls').show();
        $('#foto_error').hide();
        hasFoto = true;
    });

    // Ulangi foto
    $('#btn_retake').on('click', function() {
        $('#presensi_preview').hide().attr('src', '');
        $('#presensi_video').show();
        $('#camera_controls').show();
        $('#retake_controls').hide();
        $('#foto_base64').val('');
        hasFoto = false;
    });

    // Dapatkan lokasi & tampilkan peta Leaflet
    function getLocation() {
        if (!navigator.geolocation) {
            $('#map_loading').text('Browser tidak mendukung geolokasi.');
            return;
        }
        navigator.geolocation.getCurrentPosition(function(pos) {
            var lat = pos.coords.latitude;
            var lng = pos.coords.longitude;
            $('#input_latitude').val(lat);
            $('#input_longitude').val(lng);
            $('#koordinat_display').val(lat.toFixed(6) + ', ' + lng.toFixed(6));
            $('#lokasi_error').hide();
            renderMap(lat, lng);
        }, function() {
            $('#map_loading').text('Izin lokasi ditolak. Aktifkan GPS dan muat ulang modal.');
            $('#lokasi_error').show();
        }, { enableHighAccuracy: true, timeout: 10000 });
    }

    function renderMap(lat, lng) {
        $('#map_loading').hide();
        if (leafletMap) {
            leafletMap.setView([lat, lng], 16);
            return;
        }
        leafletMap = L.map('presensi_map').setView([lat, lng], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(leafletMap);
        L.marker([lat, lng]).addTo(leafletMap)
            .bindPopup('Lokasi Anda saat ini').openPopup();
    }

    // Validasi & submit presensi
    $('#form_presensi').on('submit', function(e) {
        e.preventDefault();
        var lat = $('#input_latitude').val();
        var valid = true;

        if (!hasFoto || !$('#foto_base64').val()) {
            $('#foto_error').show();
            valid = false;
        }
        if (!lat) {
            $('#lokasi_error').show();
            valid = false;
        }
        if (!valid) return;

        var form = this;
        Swal.fire({
            icon: 'question',
            title: 'Kirim Presensi?',
            text: 'Foto dan lokasi Anda akan disimpan sebagai bukti kehadiran.',
            showCancelButton: true,
            confirmButtonText: 'Ya, Presensi',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#50CD89',
            cancelButtonColor: '#7E8299'
        }).then(function(result) {
            if (result.isConfirmed) {
                stopCamera();
                form.submit();
            }
        });
    });

    // ─── Modal Izin: Kamera, Maps & Validasi ────────────────────
    var izinMediaStream = null;
    var izinLeafletMap  = null;
    var izinHasFoto     = false;

    // Mulai kamera & minta lokasi saat modal izin terbuka
    $('#modal_izin').on('shown.bs.modal', function() {
        startIzinCamera();
        getIzinLocation();
    });

    // Stop kamera saat modal izin ditutup
    $('#modal_izin').on('hide.bs.modal', function() {
        stopIzinCamera();
        izinHasFoto = false;
        $('#izin_preview').hide();
        $('#izin_video').show();
        $('#izin_camera_controls').show();
        $('#izin_retake_controls').hide();
        $('#izin_foto_base64').val('');
        $('#izin_foto_error').hide();
    });

    function startIzinCamera() {
        if (izinMediaStream) return;
        navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } },
            audio: false
        }).then(function(stream) {
            izinMediaStream = stream;
            var video = document.getElementById('izin_video');
            video.srcObject = stream;
        }).catch(function(err) {
            console.warn('Kamera tidak tersedia:', err);
        });
    }

    function stopIzinCamera() {
        if (izinMediaStream) {
            izinMediaStream.getTracks().forEach(function(t) { t.stop(); });
            izinMediaStream = null;
        }
    }

    // Ambil foto izin
    $('#btn_capture_izin').on('click', function() {
        var video  = document.getElementById('izin_video');
        var canvas = document.getElementById('izin_canvas');
        canvas.width  = video.videoWidth  || 640;
        canvas.height = video.videoHeight || 480;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

        var dataUrl = canvas.toDataURL('image/jpeg', 0.85);
        $('#izin_foto_base64').val(dataUrl);
        $('#izin_preview').attr('src', dataUrl).show();
        $('#izin_video').hide();
        $('#izin_camera_controls').hide();
        $('#izin_retake_controls').show();
        $('#izin_foto_error').hide();
        izinHasFoto = true;
    });

    // Ulangi foto izin
    $('#btn_retake_izin').on('click', function() {
        $('#izin_preview').hide().attr('src', '');
        $('#izin_video').show();
        $('#izin_camera_controls').show();
        $('#izin_retake_controls').hide();
        $('#izin_foto_base64').val('');
        izinHasFoto = false;
    });

    // Dapatkan lokasi untuk izin
    function getIzinLocation() {
        if (!navigator.geolocation) {
            $('#izin_map_loading').text('Browser tidak mendukung geolokasi.');
            return;
        }
        navigator.geolocation.getCurrentPosition(function(pos) {
            var lat = pos.coords.latitude;
            var lng = pos.coords.longitude;
            $('#izin_input_latitude').val(lat);
            $('#izin_input_longitude').val(lng);
            $('#izin_koordinat_display').val(lat.toFixed(6) + ', ' + lng.toFixed(6));
            $('#izin_lokasi_error').hide();
            renderIzinMap(lat, lng);
        }, function() {
            $('#izin_map_loading').text('Izin lokasi ditolak. Aktifkan GPS dan muat ulang modal.');
            $('#izin_lokasi_error').show();
        }, { enableHighAccuracy: true, timeout: 10000 });
    }

    function renderIzinMap(lat, lng) {
        $('#izin_map_loading').hide();
        if (izinLeafletMap) {
            izinLeafletMap.setView([lat, lng], 16);
            return;
        }
        izinLeafletMap = L.map('izin_map').setView([lat, lng], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(izinLeafletMap);
        L.marker([lat, lng]).addTo(izinLeafletMap)
            .bindPopup('Lokasi Anda saat ini').openPopup();
    }

    // Counter karakter
    $('#textarea_izin').on('input', function() {
        var len = $(this).val().length;
        $('#izin_char_count').text(len);
        if (len < 500) {
            $('#izin_char_count').addClass('text-danger').removeClass('text-success');
        } else {
            $('#izin_char_count').addClass('text-success').removeClass('text-danger');
            $('#izin_length_error').hide();
        }
    });

    // Validasi & submit izin
    $('#form_izin_siswa').on('submit', function(e) {
        e.preventDefault();
        var valid = true;

        // Validasi foto
        if (!izinHasFoto || !$('#izin_foto_base64').val()) {
            $('#izin_foto_error').show();
            valid = false;
        }

        // Validasi lokasi
        if (!$('#izin_input_latitude').val()) {
            $('#izin_lokasi_error').show();
            valid = false;
        }

        // Validasi karakter
        var len = $('#textarea_izin').val().length;
        if (len < 500) {
            $('#izin_length_error').show();
            $('#textarea_izin').focus();
            valid = false;
        }

        if (!valid) return;

        $('#izin_length_error').hide();
        var form = this;
        Swal.fire({
            icon: 'warning',
            title: 'Kirim Pengajuan?',
            text: 'Foto dan lokasi Anda akan disimpan sebagai bukti pengajuan izin/sakit.',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#F1416C',
            cancelButtonColor: '#7E8299'
        }).then(function(result) {
            if (result.isConfirmed) {
                stopIzinCamera();
                form.submit();
            }
        });
    });
});
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{{ session('success') }}',
        confirmButtonText: 'Selesai',
        buttonsStyling: false,
        customClass: { confirmButton: 'btn btn-primary' }
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: '{{ session('error') }}',
        confirmButtonText: 'Tutup',
        buttonsStyling: false,
        customClass: { confirmButton: 'btn btn-danger' }
    });
</script>
@endif

@if ($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Validasi Gagal',
        html: '<ul class="text-start">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
        confirmButtonText: 'Perbaiki',
        buttonsStyling: false,
        customClass: { confirmButton: 'btn btn-danger' }
    });
</script>
@endif
@endsection
</x-base-layout>
