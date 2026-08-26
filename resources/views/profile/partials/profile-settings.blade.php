@php
    $siswa = $siswa ?? null;
    $guru = $guru ?? null;

    // Determine the profile owner's role rather than the logged-in visitor's role
    $profileRole = 'admin';
    if ($siswa) {
        $profileRole = 'siswa';
    } elseif ($guru) {
        if (isset($user) && $user->hasRole('kesiswaan')) {
            $profileRole = 'kesiswaan';
        } else {
            $profileRole = 'guru';
        }
    }

    $updateAction = match($profileRole) {
        'admin' => route('profil-admin.update'),
        'guru' => $guru ? route('profil-guru.update', $guru->id) : '#',
        'siswa' => $siswa ? route('profil-siswa.update', $siswa->id) : '#',
        default => '#'
    };

    $passwordAction = match($profileRole) {
        'admin' => route('profil-admin.changePassword'),
        'guru' => route('profil-guru.changePassword'),
        'siswa' => route('profil-siswa.changePassword'),
        default => '#'
    };

    $isStudentUser = ($userRole === 'siswa' || $userRole === 'orang_tua');
    $canStudentEditClass = (\App\Models\Setting::where('key', 'restriksi_kelas')->value('value') ?? 'off') === 'on';
@endphp

<!--begin::Card - Edit Profil-->
<div class="card mb-5 mb-xl-10">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bolder">Edit Informasi Profil</h3>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ $updateAction }}" method="POST" enctype="multipart/form-data" id="form_edit_profil">
            @csrf
            @method('PUT')

            <!-- Avatar Upload -->
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-bold fs-6">Avatar / Foto Profil</label>
                <div class="col-lg-8">
                    <!-- Image Input -->
                    <div class="image-input image-input-outline {{ !($user->info->avatar ?? null) ? 'image-input-empty' : '' }}" data-kt-image-input="true" style="background-image: url({{ asset('absensi/media/avatars/blank.png') }})">
                        <!-- Preview existing avatar -->
                        <div class="image-input-wrapper w-125px h-125px" style="background-image: url({{ $user->avatar_url ?? asset('absensi/media/avatars/blank.png') }}); background-size: cover; background-position: center;"></div>
                        
                        <!-- Edit Button -->
                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ubah foto">
                            <i class="bi bi-pencil-fill fs-7"></i>
                            <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                            <input type="hidden" name="avatar_remove" />
                        </label>
                        
                        <!-- Cancel Button -->
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Batalkan">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                        
                        <!-- Remove Button -->
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Hapus foto">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                    </div>
                    <div class="form-text">Tipe file yang didukung: png, jpg, jpeg (Otomatis terkompresi).</div>
                </div>
            </div>

            @if($profileRole === 'admin')
                <!-- Name (Admin) -->
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-bold fs-6">Nama Depan & Belakang</label>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-6 fv-row">
                                <input type="text" name="first_name" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Nama Depan" value="{{ old('first_name', $user->first_name) }}" required />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <input type="text" name="last_name" class="form-control form-control-lg form-control-solid" placeholder="Nama Belakang" value="{{ old('last_name', $user->last_name) }}" required />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email (Admin) -->
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-bold fs-6">Alamat Email</label>
                    <div class="col-lg-8 fv-row">
                        <input type="email" name="email" class="form-control form-control-lg form-control-solid" placeholder="Email" value="{{ old('email', $user->email) }}" required />
                    </div>
                </div>

                <!-- Phone (Admin) -->
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">Nomor Telepon</label>
                    <div class="col-lg-8 fv-row">
                        <input type="text" name="phone" class="form-control form-control-lg form-control-solid" placeholder="Nomor Telepon" value="{{ old('phone', $info->phone ?? '') }}" />
                    </div>
                </div>

            @elseif($profileRole === 'guru' || $profileRole === 'kesiswaan')
                @php $isOwnGuruProfile = ($userRole === 'guru' || $userRole === 'kesiswaan'); @endphp
                <!-- Nama Lengkap -->
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-bold fs-6">Nama Lengkap</label>
                    <div class="col-lg-8 fv-row">
                        <input type="text" name="nama" class="form-control form-control-lg form-control-solid" placeholder="Nama Lengkap" value="{{ old('nama', $guru->nama) }}" required />
                    </div>
                </div>

                <!-- NIP (readonly untuk own profile) -->
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">NIP (Nomor Induk Pegawai)</label>
                    <div class="col-lg-8 fv-row">
                        <input type="text" name="nip" class="form-control form-control-lg form-control-solid" placeholder="NIP" value="{{ old('nip', $guru->nip) }}" {{ $isOwnGuruProfile ? 'readonly' : '' }} />
                    </div>
                </div>



                <!-- No HP -->
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">Nomor HP</label>
                    <div class="col-lg-8 fv-row">
                        <input type="text" name="no_hp" class="form-control form-control-lg form-control-solid" placeholder="Nomor HP" value="{{ old('no_hp', $guru->no_hp) }}" inputmode="numeric" pattern="[0-9]*" maxlength="15" />
                    </div>
                </div>

                <!-- Alamat -->
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">Alamat Lengkap</label>
                    <div class="col-lg-8 fv-row">
                        <textarea name="alamat" class="form-control form-control-lg form-control-solid" rows="3" placeholder="Alamat lengkap">{{ old('alamat', $guru->alamat) }}</textarea>
                    </div>
                </div>

            @elseif($profileRole === 'siswa')
                <!-- Nama, NIS, NISN (Siswa) -->
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-bold fs-6">Nama Lengkap</label>
                    <div class="col-lg-8 fv-row">
                        <input type="text" name="nama" class="form-control form-control-lg form-control-solid" placeholder="Nama Lengkap" value="{{ old('nama', $siswa->nama) }}" 
                            {{ $isStudentUser ? 'readonly' : '' }} required />
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">NIS</label>
                    <div class="col-lg-8 fv-row">
                        <input type="text" name="nis" class="form-control form-control-lg form-control-solid" placeholder="NIS" value="{{ old('nis', $siswa->nis) }}" 
                            {{ $isStudentUser ? 'readonly' : '' }} />
                    </div>
                </div>

                <!-- Kelas & Jenis Kelamin (Siswa) -->
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">Kelas</label>
                    <div class="col-lg-8 fv-row">
                        @if ($isStudentUser && !$canStudentEditClass)
                            <input type="text" class="form-control form-control-lg form-control-solid" value="{{ ($siswa->kelas->tingkat ?? '') . ' ' . ($siswa->kelas->nama ?? '') }}" readonly />
                        @else
                            <select name="kelas_id" class="form-select form-select-lg form-select-solid" data-control="select2">
                                <option value="">Pilih Kelas...</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ old('kelas_id', $siswa->kelas_id) == $k->id ? 'selected' : '' }}>
                                        {{ $k->tingkat }} {{ $k->nama }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-bold fs-6">Jenis Kelamin</label>
                    <div class="col-lg-8 fv-row">
                        @if ($isStudentUser)
                            <input type="text" class="form-control form-control-lg form-control-solid" value="{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}" readonly />
                        @else
                            <select name="jenis_kelamin" class="form-select form-select-lg form-select-solid">
                                <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        @endif
                    </div>
                </div>

                <!-- Tanggal Lahir (Siswa) -->
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">Tanggal Lahir</label>
                    <div class="col-lg-8 fv-row">
                        <input type="date" name="tanggal_lahir" class="form-control form-control-lg form-control-solid" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('Y-m-d') : '') }}" max="{{ date('Y-m-d') }}" 
                            {{ $isStudentUser ? 'readonly' : '' }} />
                    </div>
                </div>

                <!-- Nama Orang Tua (Siswa) -->
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">Nama Orang Tua / Wali</label>
                    <div class="col-lg-8 fv-row">
                        <input type="text" name="nama_orang_tua" class="form-control form-control-lg form-control-solid" placeholder="Nama Orang Tua / Wali" value="{{ old('nama_orang_tua', $siswa->nama_orang_tua) }}" />
                    </div>
                </div>

                <!-- No HP & No HP Orang Tua (Siswa) -->
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">Kontak Telepon</label>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-6 fv-row">
                                <label class="fs-8 text-muted">No. HP Siswa</label>
                                <input type="text" name="no_hp" class="form-control form-control-lg form-control-solid" placeholder="No HP Siswa" value="{{ old('no_hp', $siswa->no_hp) }}" inputmode="numeric" pattern="[0-9]*" maxlength="15" />
                            </div>
                            <div class="col-lg-6 fv-row">
                                <label class="fs-8 text-muted">No. HP Orang Tua / Wali</label>
                                <input type="text" name="no_hp_orang_tua" class="form-control form-control-lg form-control-solid" placeholder="No HP Orang Tua" value="{{ old('no_hp_orang_tua', $siswa->no_hp_orang_tua) }}" inputmode="numeric" pattern="[0-9]*" maxlength="15" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alamat (Siswa) -->
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">Alamat Lengkap</label>
                    <div class="col-lg-8 fv-row">
                        <textarea name="alamat" class="form-control form-control-lg form-control-solid" rows="3" placeholder="Alamat lengkap">{{ old('alamat', $siswa->alamat) }}</textarea>
                    </div>
                </div>

                <!-- Keaktifan Status (Siswa) -->
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">Status Keaktifan</label>
                    <div class="col-lg-8 fv-row">
                        @if ($isStudentUser)
                            <input type="text" class="form-control form-control-lg form-control-solid" value="{{ ucfirst($siswa->status ?? 'aktif') }}" readonly />
                        @else
                            <select name="status" class="form-select form-select-lg form-select-solid">
                                <option value="aktif" {{ old('status', $siswa->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status', $siswa->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        @endif
                    </div>
                </div>
            @endif

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <button type="submit" class="btn btn-primary" id="btn_save_profil">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<!--end::Card - Edit Profil-->

<!--begin::Card - Ubah Password-->
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="fw-bolder">Ubah Password Akun</h3>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ $passwordAction }}" method="POST" id="form_change_password">
            @csrf
            @method('PUT')

            <!-- Password Saat Ini -->
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label required fw-bold fs-6">Password Saat Ini</label>
                <div class="col-lg-8 fv-row">
                    <input type="password" name="current_password" class="form-control form-control-lg form-control-solid" placeholder="Masukkan password saat ini" required />
                </div>
            </div>

            <!-- Password Baru -->
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label required fw-bold fs-6">Password Baru</label>
                <div class="col-lg-8 fv-row">
                    <input type="password" name="password" class="form-control form-control-lg form-control-solid" placeholder="Password baru (minimal 6 karakter)" required />
                </div>
            </div>

            <!-- Konfirmasi Password Baru -->
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label required fw-bold fs-6">Konfirmasi Password Baru</label>
                <div class="col-lg-8 fv-row">
                    <input type="password" name="password_confirmation" class="form-control form-control-lg form-control-solid" placeholder="Ketik ulang password baru" required />
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <button type="submit" class="btn btn-primary">Ubah Password</button>
            </div>
        </form>
    </div>
</div>
<!--end::Card - Ubah Password-->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.jQuery) {
            // Filter non-numeric input on phone fields
            $(document).on('input', 'input[inputmode="numeric"]', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Image Compression & 1:1 Center-Crop untuk Avatar Upload
            var MAX_SIZE = 800;
            var QUALITY = 0.8;

            $('input[name="avatar"]').on('change', function(e) {
                var file = e.target.files[0];
                if (!file) return;

                // Cek batas ukuran maksimal 2MB (2 * 1024 * 1024 bytes)
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        html: '<ul class="text-start mb-0"><li>Foto avatar gagal diunggah karena melebihi batas ukuran maksimal 2MB. Silakan pilih foto dengan ukuran lebih kecil.</li></ul>',
                        confirmButtonText: 'Perbaiki',
                        confirmButtonColor: '#F1416C'
                    });
                    this.value = '';
                    return;
                }

                var reader = new FileReader();
                reader.onload = function(ev) {
                    var img = new Image();
                    img.onload = function() {
                        try {
                            var canvas = document.createElement('canvas');
                            var size = Math.min(img.width, img.height);
                            var sx = Math.max(0, (img.width - size) / 2);
                            var sy = Math.max(0, (img.height - size) / 2);
                            var targetSize = Math.min(size, MAX_SIZE);

                            canvas.width = targetSize;
                            canvas.height = targetSize;
                            var ctx = canvas.getContext('2d');
                            ctx.fillStyle = '#FFFFFF';
                            ctx.fillRect(0, 0, targetSize, targetSize);
                            ctx.drawImage(img, sx, sy, size, size, 0, 0, targetSize, targetSize);

                            canvas.toBlob(function(blob) {
                                if (blob && window.DataTransfer) {
                                    var newFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                                        type: 'image/jpeg',
                                        lastModified: Date.now()
                                    });
                                    var dt = new DataTransfer();
                                    dt.items.add(newFile);
                                    e.target.files = dt.files;
                                }
                            }, 'image/jpeg', QUALITY);
                        } catch (err) {
                            console.warn('Client-side crop skipped, backend will handle crop:', err);
                        }
                    };
                    img.src = ev.target.result;
                };
                reader.readAsDataURL(file);
            });

            // Handle SweetAlert2 Confirmation for Edit Profile
            $('#form_edit_profil').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                Swal.fire({
                    icon: 'question',
                    title: 'Konfirmasi',
                    text: 'Simpan perubahan profil Anda?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#009EF7',
                    cancelButtonColor: '#7E8299'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Handle SweetAlert2 Confirmation for Password Change
            $('#form_change_password').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                Swal.fire({
                    icon: 'warning',
                    title: 'Ubah Password?',
                    text: 'Anda akan mengubah password login akun ini. Pastikan Anda mengingat password baru Anda.',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Ubah',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#F1416C',
                    cancelButtonColor: '#7E8299'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }
    });
</script>
