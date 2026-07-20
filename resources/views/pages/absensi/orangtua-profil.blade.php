<x-base-layout>
@include('pages.absensi._partials.toolbar', [
    'toolbarActions' => ''
])

<div class="card mb-5 mb-xl-10 shadow-sm">
    <!--begin::Card header-->
    <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
        <div class="card-title m-0">
            <h3 class="fw-bolder m-0 text-gray-800">Edit Profil Orang Tua</h3>
        </div>
    </div>
    <!--end::Card header-->

    <!--begin::Content-->
    <div id="kt_account_profile_details" class="collapse show">
        <!--begin::Form-->
        <form method="POST" action="{{ route('orangtua.profil.update') }}" class="form">
            @csrf

            <!--begin::Card body-->
            <div class="card-body border-top p-9">
                
                @if($errors->any())
                    <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                        <span class="svg-icon svg-icon-2hx svg-icon-danger me-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="red"/>
                                <rect x="11" y="14" width="2" height="2" rx="1" fill="black"/>
                                <rect x="11" y="6" width="2" height="6" rx="1" fill="black"/>
                            </svg>
                        </span>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-dark fw-bold">Terjadi Kesalahan!</h4>
                            <ul class="mb-0 text-danger ps-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                        <span class="svg-icon svg-icon-2hx svg-icon-success me-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black"/>
                                <path d="M10.4343 12.4343L8.75 10.75C8.33579 10.3358 7.66421 10.3358 7.25 10.75C6.83579 11.1642 6.83579 11.8358 7.25 12.25L10.2929 15.2929C10.6834 15.6834 11.3166 15.6834 11.7071 15.2929L17.25 9.75C17.6642 9.33579 17.6642 8.66421 17.25 8.25C16.8358 7.83579 16.1642 7.83579 15.75 8.25L10.4343 12.4343Z" fill="black"/>
                            </svg>
                        </span>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-dark fw-bold">Berhasil!</h4>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <div class="row g-9">
                    <!--==================== FATHER PROFILE COLUMN ====================-->
                    <div class="col-lg-6 col-md-12 border-end-lg pe-lg-8">
                        <div class="d-flex align-items-center mb-6">
                            <div class="symbol symbol-35px symbol-circle me-3 bg-light-primary text-primary d-flex align-items-center justify-content-center fw-boldest p-2">
                                <i class="bi bi-person-fill text-primary fs-3"></i>
                            </div>
                            <h4 class="text-gray-800 fw-boldest mb-0">Profil Ayah</h4>
                        </div>

                        <!-- NIK Ayah -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">NIK Ayah</label>
                            <div class="col-lg-8">
                                <input type="text" name="nik_ayah" class="form-control form-control-lg form-control-solid" placeholder="NIK Ayah" value="{{ old('nik_ayah', $profile->nik_ayah) }}" maxlength="16" pattern="[0-9]*" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                            </div>
                        </div>

                        <!-- Nama Ayah -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Nama Ayah</label>
                            <div class="col-lg-8">
                                <input type="text" name="nama_ayah" class="form-control form-control-lg form-control-solid" placeholder="Nama Lengkap Ayah" value="{{ old('nama_ayah', $profile->nama_ayah) }}" maxlength="100" oninput="this.value = this.value.replace(/[^a-zA-Z\s']/g, '')" />
                            </div>
                        </div>

                        <!-- Pekerjaan Ayah -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Pekerjaan</label>
                            <div class="col-lg-8">
                                <input type="text" name="pekerjaan_ayah" class="form-control form-control-lg form-control-solid" placeholder="Pekerjaan Ayah" value="{{ old('pekerjaan_ayah', $profile->pekerjaan_ayah) }}" maxlength="100" />
                            </div>
                        </div>

                        <!-- Ket Pekerjaan Ayah -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Ket. Pekerjaan</label>
                            <div class="col-lg-8">
                                <input type="text" name="ket_pekerjaan_ayah" class="form-control form-control-lg form-control-solid" placeholder="Keterangan Pekerjaan" value="{{ old('ket_pekerjaan_ayah', $profile->ket_pekerjaan_ayah) }}" maxlength="200" />
                            </div>
                        </div>

                        <!-- Pendidikan Ayah -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Pendidikan</label>
                            <div class="col-lg-8">
                                <input type="text" name="pendidikan_ayah" class="form-control form-control-lg form-control-solid" placeholder="Pendidikan Terakhir" value="{{ old('pendidikan_ayah', $profile->pendidikan_ayah) }}" maxlength="100" />
                            </div>
                        </div>

                        <!-- Alamat Ayah -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Alamat</label>
                            <div class="col-lg-8">
                                <textarea name="alamat_ayah" class="form-control form-control-lg form-control-solid" rows="3" placeholder="Alamat Tinggal Ayah" maxlength="500">{{ old('alamat_ayah', $profile->alamat_ayah) }}</textarea>
                                <div class="form-text text-muted fs-7 mt-1">Maksimal 500 karakter.</div>
                            </div>
                        </div>

                        <!-- Nomor HP Ayah -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Nomor HP</label>
                            <div class="col-lg-8">
                                <input type="text" name="no_hp_ayah" class="form-control form-control-lg form-control-solid" placeholder="Nomor Telepon Seluler" value="{{ old('no_hp_ayah', $profile->no_hp_ayah) }}" maxlength="13" pattern="[0-9]*" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                            </div>
                        </div>

                        <!-- Penghasilan Ayah -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Penghasilan</label>
                            <div class="col-lg-8">
                                <input type="text" name="penghasilan_ayah" class="form-control form-control-lg form-control-solid" placeholder="Penghasilan Per Bulan" value="{{ old('penghasilan_ayah', $profile->penghasilan_ayah) }}" maxlength="50" />
                            </div>
                        </div>
                    </div>

                    <!--==================== MOTHER PROFILE COLUMN ====================-->
                    <div class="col-lg-6 col-md-12 ps-lg-8">
                        <div class="d-flex align-items-center mb-6">
                            <div class="symbol symbol-35px symbol-circle me-3 bg-light-danger text-danger d-flex align-items-center justify-content-center fw-boldest p-2">
                                <i class="bi bi-person-fill text-danger fs-3"></i>
                            </div>
                            <h4 class="text-gray-800 fw-boldest mb-0">Profil Ibu</h4>
                        </div>

                        <!-- NIK Ibu -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">NIK Ibu</label>
                            <div class="col-lg-8">
                                <input type="text" name="nik_ibu" class="form-control form-control-lg form-control-solid" placeholder="NIK Ibu" value="{{ old('nik_ibu', $profile->nik_ibu) }}" maxlength="16" pattern="[0-9]*" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                            </div>
                        </div>

                        <!-- Nama Ibu -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Nama Ibu</label>
                            <div class="col-lg-8">
                                <input type="text" name="nik_ibu" class="d-none" /> <!-- prevent auto autofill issues -->
                                <input type="text" name="nama_ibu" class="form-control form-control-lg form-control-solid" placeholder="Nama Lengkap Ibu" value="{{ old('nama_ibu', $profile->nama_ibu) }}" maxlength="100" oninput="this.value = this.value.replace(/[^a-zA-Z\s']/g, '')" />
                            </div>
                        </div>

                        <!-- Pekerjaan Ibu -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Pekerjaan</label>
                            <div class="col-lg-8">
                                <input type="text" name="pekerjaan_ibu" class="form-control form-control-lg form-control-solid" placeholder="Pekerjaan Ibu" value="{{ old('pekerjaan_ibu', $profile->pekerjaan_ibu) }}" maxlength="100" />
                            </div>
                        </div>

                        <!-- Ket Pekerjaan Ibu -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Ket. Pekerjaan</label>
                            <div class="col-lg-8">
                                <input type="text" name="ket_pekerjaan_ibu" class="form-control form-control-lg form-control-solid" placeholder="Keterangan Pekerjaan" value="{{ old('ket_pekerjaan_ibu', $profile->ket_pekerjaan_ibu) }}" maxlength="200" />
                            </div>
                        </div>

                        <!-- Pendidikan Ibu -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Pendidikan</label>
                            <div class="col-lg-8">
                                <input type="text" name="pendidikan_ibu" class="form-control form-control-lg form-control-solid" placeholder="Pendidikan Terakhir" value="{{ old('pendidikan_ibu', $profile->pendidikan_ibu) }}" maxlength="100" />
                            </div>
                        </div>

                        <!-- Alamat Ibu -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Alamat</label>
                            <div class="col-lg-8">
                                <textarea name="alamat_ibu" class="form-control form-control-lg form-control-solid" rows="3" placeholder="Alamat Tinggal Ibu" maxlength="500">{{ old('alamat_ibu', $profile->alamat_ibu) }}</textarea>
                                <div class="form-text text-muted fs-7 mt-1">Maksimal 500 karakter.</div>
                            </div>
                        </div>

                        <!-- Nomor HP Ibu -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Nomor HP</label>
                            <div class="col-lg-8">
                                <input type="text" name="no_hp_ibu" class="form-control form-control-lg form-control-solid" placeholder="Nomor Telepon Seluler" value="{{ old('no_hp_ibu', $profile->no_hp_ibu) }}" maxlength="13" pattern="[0-9]*" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                            </div>
                        </div>

                        <!-- Penghasilan Ibu -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6 text-gray-700">Penghasilan</label>
                            <div class="col-lg-8">
                                <input type="text" name="penghasilan_ibu" class="form-control form-control-lg form-control-solid" placeholder="Penghasilan Per Bulan" value="{{ old('penghasilan_ibu', $profile->penghasilan_ibu) }}" maxlength="50" />
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!--end::Card body-->

            <!--begin::Actions-->
            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <button type="submit" class="btn btn-primary" id="kt_account_profile_details_submit">
                    Simpan Perubahan
                </button>
            </div>
            <!--end::Actions-->
        </form>
        <!--end::Form-->
    </div>
    <!--end::Content-->
</div>
@push('scripts')
<script>
    $(document).ready(function() {
        function formatRupiah(value) {
            var number_string = value.replace(/[^0-9]/g, '');
            if (number_string === '') return '';
            var sisa = number_string.length % 3,
                rupiah = number_string.substr(0, sisa),
                ribuan = number_string.substr(sisa).match(/\d{3}/g);
                
            if (ribuan) {
                var separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return 'Rp ' + rupiah;
        }

        $(document).on('input', 'input[name="penghasilan_ayah"], input[name="penghasilan_ibu"]', function() {
            $(this).val(formatRupiah($(this).val()));
        });

        // Format on page load
        $('input[name="penghasilan_ayah"], input[name="penghasilan_ibu"]').each(function() {
            var currentVal = $(this).val();
            if (currentVal) {
                $(this).val(formatRupiah(currentVal));
            }
        });
    });
</script>
@endpush
</x-base-layout>
