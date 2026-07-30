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
        <form method="POST" action="{{ url('/absensi/siswa/profil') }}" class="form">
            @csrf

            <!--begin::Card body-->
            <div class="card-body border-top p-9">
                
                @if($errors->any())
                    <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                        <span class="svg-icon svg-icon-2hx svg-icon-danger me-4">
                            <i class="bi bi-exclamation-triangle-fill fs-1 text-danger"></i>
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
                            <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                        </span>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-dark fw-bold">Berhasil!</h4>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <div class="row g-9">
                    <!--==================== FATHER PROFILE COLUMN ====================-->
                    <div class="col-lg-6 border-end-lg">
                        <div class="d-flex align-items-center mb-6">
                            <i class="bi bi-person-fill text-primary fs-2 me-3"></i>
                            <h4 class="fw-boldest text-gray-800 m-0">Profil Ayah</h4>
                        </div>

                        <!-- NIK Ayah -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">NIK Ayah</label>
                            <div class="col-lg-8 fv-row">
                                <input type="text" name="nik_ayah" class="form-control form-control-lg form-control-solid" placeholder="NIK Ayah" value="{{ old('nik_ayah', $profile->nik_ayah) }}"/>
                            </div>
                        </div>

                        <!-- Nama Ayah -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">Nama Ayah</label>
                            <div class="col-lg-8 fv-row">
                                <input type="text" name="nama_ayah" class="form-control form-control-lg form-control-solid" placeholder="Nama Lengkap Ayah" value="{{ old('nama_ayah', $profile->nama_ayah) }}"/>
                            </div>
                        </div>

                        <!-- Pekerjaan Ayah -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">Pekerjaan</label>
                            <div class="col-lg-8 fv-row">
                                <input type="text" name="pekerjaan_ayah" class="form-control form-control-lg form-control-solid" placeholder="Pekerjaan Ayah" value="{{ old('pekerjaan_ayah', $profile->pekerjaan_ayah) }}"/>
                            </div>
                        </div>

                        <!-- Ket Pekerjaan Ayah -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">Ket. Pekerjaan</label>
                            <div class="col-lg-8 fv-row">
                                <input type="text" name="ket_pekerjaan_ayah" class="form-control form-control-lg form-control-solid" placeholder="Keterangan Pekerjaan" value="{{ old('ket_pekerjaan_ayah', $profile->ket_pekerjaan_ayah) }}"/>
                            </div>
                        </div>

                        <!-- Pendidikan Ayah -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">Pendidikan</label>
                            <div class="col-lg-8 fv-row">
                                <input type="text" name="pendidikan_ayah" class="form-control form-control-lg form-control-solid" placeholder="Pendidikan Terakhir" value="{{ old('pendidikan_ayah', $profile->pendidikan_ayah) }}"/>
                            </div>
                        </div>

                        <!-- Alamat Ayah -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">Alamat</label>
                            <div class="col-lg-8 fv-row">
                                <textarea name="alamat_ayah" class="form-control form-control-lg form-control-solid" rows="3" placeholder="Alamat Tinggal Ayah">{{ old('alamat_ayah', $profile->alamat_ayah) }}</textarea>
                            </div>
                        </div>

                        <!-- Nomor HP Ayah -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">Nomor HP</label>
                            <div class="col-lg-8 fv-row">
                                <input type="text" name="no_hp_ayah" class="form-control form-control-lg form-control-solid" placeholder="Nomor Telepon Seluler" value="{{ old('no_hp_ayah', $profile->no_hp_ayah) }}"/>
                            </div>
                        </div>
                    </div>

                    <!--==================== MOTHER PROFILE COLUMN ====================-->
                    <div class="col-lg-6">
                        <div class="d-flex align-items-center mb-6">
                            <i class="bi bi-person-fill text-danger fs-2 me-3"></i>
                            <h4 class="fw-boldest text-gray-800 m-0">Profil Ibu</h4>
                        </div>

                        <!-- NIK Ibu -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">NIK Ibu</label>
                            <div class="col-lg-8 fv-row">
                                <input type="text" name="nik_ibu" class="form-control form-control-lg form-control-solid" placeholder="NIK Ibu" value="{{ old('nik_ibu', $profile->nik_ibu) }}"/>
                            </div>
                        </div>

                        <!-- Nama Ibu -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">Nama Ibu</label>
                            <div class="col-lg-8 fv-row">
                                <input type="text" name="nama_ibu" class="form-control form-control-lg form-control-solid" placeholder="Nama Lengkap Ibu" value="{{ old('nama_ibu', $profile->nama_ibu) }}"/>
                            </div>
                        </div>

                        <!-- Pekerjaan Ibu -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">Pekerjaan</label>
                            <div class="col-lg-8 fv-row">
                                <input type="text" name="pekerjaan_ibu" class="form-control form-control-lg form-control-solid" placeholder="Pekerjaan Ibu" value="{{ old('pekerjaan_ibu', $profile->pekerjaan_ibu) }}"/>
                            </div>
                        </div>

                        <!-- Ket Pekerjaan Ibu -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">Ket. Pekerjaan</label>
                            <div class="col-lg-8 fv-row">
                                <input type="text" name="ket_pekerjaan_ibu" class="form-control form-control-lg form-control-solid" placeholder="Keterangan Pekerjaan" value="{{ old('ket_pekerjaan_ibu', $profile->ket_pekerjaan_ibu) }}"/>
                            </div>
                        </div>

                        <!-- Pendidikan Ibu -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">Pendidikan</label>
                            <div class="col-lg-8 fv-row">
                                <input type="text" name="pendidikan_ibu" class="form-control form-control-lg form-control-solid" placeholder="Pendidikan Terakhir" value="{{ old('pendidikan_ibu', $profile->pendidikan_ibu) }}"/>
                            </div>
                        </div>

                        <!-- Alamat Ibu -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">Alamat</label>
                            <div class="col-lg-8 fv-row">
                                <textarea name="alamat_ibu" class="form-control form-control-lg form-control-solid" rows="3" placeholder="Alamat Tinggal Ibu">{{ old('alamat_ibu', $profile->alamat_ibu) }}</textarea>
                            </div>
                        </div>

                        <!-- Nomor HP Ibu -->
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">Nomor HP</label>
                            <div class="col-lg-8 fv-row">
                                <input type="text" name="no_hp_ibu" class="form-control form-control-lg form-control-solid" placeholder="Nomor Telepon Seluler" value="{{ old('no_hp_ibu', $profile->no_hp_ibu) }}"/>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!--end::Card body-->

            <!--begin::Actions-->
            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <button type="submit" class="btn btn-primary fw-bold px-8">Simpan Perubahan</button>
            </div>
            <!--end::Actions-->
        </form>
        <!--end::Form-->
    </div>
    <!--end::Content-->
</div>
</x-base-layout>
