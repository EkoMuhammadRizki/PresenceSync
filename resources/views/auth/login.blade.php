@extends('auth.layout')

@section('content')
    <!--begin::Signin Form-->
    <form method="POST" action="{{ theme()->getPageUrl('login') }}" class="form w-100" novalidate="novalidate" id="kt_sign_in_form">
    @csrf

        <!--begin::Heading-->
        <div class="text-center mb-5">
            <!--begin::Title-->
            <h1 class="text-dark mb-2 fw-bolder fs-2">
                {{ __('Masuk ke SIAP') }}
            </h1>
            <!--end::Title-->
        </div>
        <!--begin::Heading-->

        <!--begin::Notice Box-->
        <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-3 mb-5">
            <div class="d-flex flex-stack flex-grow-1">
                <div class="fw-bold text-gray-700 fs-7 text-center w-100">
                    Gunakan <span class="fw-bolder text-primary">NIS</span> (Siswa) atau <span class="fw-bolder text-primary">NIP</span> (Guru) untuk masuk.
                </div>
            </div>
        </div>
        <!--end::Notice Box-->

        <!--begin::Input group - Identifier-->
        <div class="fv-row mb-5">
            <!--begin::Label-->
            <label class="form-label fs-6 fw-bolder text-dark">{{ __('NIS / NIP') }}</label>
            <!--end::Label-->

            <!--begin::Input-->
            <input class="form-control form-control-lg form-control-solid" type="text" name="identifier" autocomplete="off" placeholder="Masukkan NIS atau NIP" value="{{ old('identifier', $lastIdentifier ?? '') }}" required autofocus/>
            <!--end::Input-->
        </div>
        <!--end::Input group-->

        <!--begin::Input group - Password-->
        <div class="fv-row mb-5">
            <!--begin::Wrapper-->
            <div class="d-flex flex-stack mb-2">
                <!--begin::Label-->
                <label class="form-label fw-bolder text-dark fs-6 mb-0">{{ __('Password') }}</label>
                <!--end::Label-->
            </div>
            <!--end::Wrapper-->

            <!--begin::Input wrapper-->
            <div class="position-relative mb-2" data-kt-password-meter="true">
                <input class="form-control form-control-lg form-control-solid" type="password" name="password" id="password_field" autocomplete="new-password" placeholder="Masukkan Password" value="" required/>
                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                    <i class="bi bi-eye-slash fs-2"></i>
                    <i class="bi bi-eye fs-2 d-none"></i>
                </span>
            </div>
            <!--end::Input wrapper-->
        </div>
        <!--end::Input group-->

        <!--begin::Input group - Remember-->
        <div class="fv-row mb-6">
            <label class="form-check form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox" name="remember" id="remember_me_checkbox" {{ !empty($lastIdentifier) ? 'checked' : '' }}/>
                <span class="form-check-label fw-bold text-gray-700 fs-6">{{ __('Ingat saya') }}</span>
            </label>
        </div>
        <!--end::Input group-->

        <!--begin::Actions-->
        <div class="text-center">
            <!--begin::Submit button-->
            <button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-primary w-100 mb-0">
                @include('partials.general._button-indicator', ['label' => __('Masuk')])
            </button>
            <!--end::Submit button-->
        </div>
        <!--end::Actions-->
    </form>
    <!--end::Signin Form-->
@endsection

@section('scripts')
    <script>
    (function () {
        // ── Ingat Saya: isi password dari cookie jika checkbox tercentang ──
        var savedIdentifier = '{{ addslashes($lastIdentifier ?? '') }}';
        var savedPassword   = '{{ addslashes($lastPassword ?? '') }}';
        var isRemembered    = savedIdentifier !== '';

        if (isRemembered && savedPassword !== '' && savedPassword !== 'demo') {
            document.getElementById('password_field').value = savedPassword;
        }

        function initLoginHandler() {
            if (typeof $ === 'undefined' || typeof Swal === 'undefined') {
                return setTimeout(initLoginHandler, 50);
            }

            // Jika remember checkbox diubah ke unchecked, hapus isian password
            $('#remember_me_checkbox').on('change', function () {
                if (!this.checked) {
                    $('#password_field').val('');
                } else if (savedPassword !== '' && savedPassword !== 'demo') {
                    $('#password_field').val(savedPassword);
                }
            });

            $('#kt_sign_in_form').off('submit').on('submit', function (e) {
                e.preventDefault();

                var $form  = $(this);
                var $btn   = $('#kt_sign_in_submit');

                // Tampilkan loading spinner
                $btn.prop('disabled', true);
                $btn.attr('data-kt-indicator', 'on');

                // Kirim via AJAX
                axios.post($form.attr('action'), {
                    identifier : $form.find('[name=identifier]').val(),
                    password   : $form.find('[name=password]').val(),
                    remember   : $form.find('[name=remember]').is(':checked') ? 'on' : '',
                    _token     : $form.find('[name=_token]').val(),
                }, {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $form.find('[name=_token]').val(),
                        'Accept': 'application/json'
                    }
                })
                .then(function (response) {
                    var data = response.data;

                    if (data.redirectUrl) {
                        Swal.fire({
                            icon             : 'success',
                            title            : 'Berhasil Masuk!',
                            text             : data.message || 'Mengalihkan ke dashboard...',
                            timer            : 1200,
                            showConfirmButton: false
                        }).then(function () {
                            window.location.href = data.redirectUrl;
                        });
                    } else {
                        window.location.reload();
                    }
                })
                .catch(function (error) {
                    $btn.prop('disabled', false);
                    $btn.removeAttr('data-kt-indicator');

                    var errorMsg = 'NIS / NIP atau password salah.';
                    if (error.response) {
                        if (error.response.status === 422) {
                            var data = error.response.data;
                            if (data.errors) {
                                var firstKey = Object.keys(data.errors)[0];
                                errorMsg = data.errors[firstKey][0];
                            } else if (data.message) {
                                errorMsg = data.message;
                            }
                        } else if (error.response.status === 429) {
                            errorMsg = 'Terlalu banyak percobaan login. Silakan tunggu beberapa menit.';
                        }
                    }

                    Swal.fire({
                        icon             : 'error',
                        title            : 'Login Gagal!',
                        text             : errorMsg,
                        confirmButtonText: 'Coba Lagi',
                        customClass      : { confirmButton: 'btn btn-primary fw-bold px-6' },
                        buttonsStyling   : false,
                    });
                });
            });
        }

        initLoginHandler();
    })();
    </script>
@endsection
