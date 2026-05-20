<x-auth-layout>

    <!--begin::Signin Form-->
    <form method="POST" action="{{ theme()->getPageUrl('login') }}" class="form w-100" novalidate="novalidate" id="kt_sign_in_form">
    @csrf

    <!--begin::Heading-->
        <div class="text-center mb-10">
            <!--begin::Title-->
            <h1 class="text-dark mb-3">
                {{ __('Sign In to PresenceSync') }}
            </h1>
            <!--end::Title-->
        </div>
        <!--begin::Heading-->

        <div class="mb-10 bg-light-info p-8 rounded"><div class="text-info"> Use account <strong>admin@demo.com</strong> and password <strong>demo</strong> to continue. </div></div>

        <!--begin::Input group-->
        <div class="fv-row mb-10">
            <!--begin::Label-->
            <label class="form-label fs-6 fw-bolder text-dark">{{ __('Email') }}</label>
            <!--end::Label-->

            <!--begin::Input-->
            <input class="form-control form-control-lg form-control-solid" type="email" name="email" autocomplete="off" value="{{ old('email', 'demo@demo.com') }}" required autofocus/>
            <!--end::Input-->
        </div>
        <!--end::Input group-->

        <!--begin::Input group-->
        <div class="fv-row mb-10">
            <!--begin::Wrapper-->
            <div class="d-flex flex-stack mb-2">
                <!--begin::Label-->
                <label class="form-label fw-bolder text-dark fs-6 mb-0">{{ __('Password') }}</label>
                <!--end::Label-->

                <!--begin::Link-->
                @if (Route::has('password.request'))
                    <a href="{{ theme()->getPageUrl('password.request') }}" class="link-primary fs-6 fw-bolder">
                        {{ __('Forgot Password ?') }}
                    </a>
            @endif
            <!--end::Link-->
            </div>
            <!--end::Wrapper-->

            <!--begin::Input-->
            <input class="form-control form-control-lg form-control-solid" type="password" name="password" autocomplete="off" value="demo" required/>
            <!--end::Input-->
        </div>
        <!--end::Input group-->

        <!--begin::Input group-->
        <div class="fv-row mb-10">
            <label class="form-check form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox" name="remember"/>
                <span class="form-check-label fw-bold text-gray-700 fs-6">{{ __('Remember me') }}
            </span>
            </label>
        </div>
        <!--end::Input group-->

        <!--begin::Actions-->
        <div class="text-center">
            <!--begin::Submit button-->
            <button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-primary w-100 mb-5">
                @include('partials.general._button-indicator', ['label' => __('Continue')])
            </button>
            <!--end::Submit button-->

            <!--begin::Separator-->
            <div class="text-center text-muted text-uppercase fw-bolder mb-5">or</div>
            <!--end::Separator-->

            <!--begin::Google link-->
            <a href="{{ url('/auth/redirect/google') }}?redirect_uri={{ url()->previous() }}" class="btn btn-flex flex-center btn-light btn-lg w-100 mb-5">
                <img alt="Logo" src="{{ asset(theme()->getMediaUrlPath() . 'svg/brand-logos/google-icon.svg') }}" class="h-20px me-3"/>
                {{ __('Continue with Google') }}
            </a>
            <!--end::Google link-->

            <!--begin::Facebook link-->
            <a href="{{ url('/auth/redirect/facebook') }}?redirect_uri={{ url()->previous() }}" class="btn btn-flex flex-center btn-light btn-lg w-100 mb-5">
                <img alt="Logo" src="{{ asset(theme()->getMediaUrlPath() . 'svg/brand-logos/facebook-4.svg') }}" class="h-20px me-3"/>
                {{ __('Continue with Facebook') }}
            </a>
            <!--end::Facebook link-->
        </div>
        <!--end::Actions-->
    </form>
    <!--end::Signin Form-->

    {{-- Named slot: scripts → diteruskan ke @section('scripts') di auth/layout.blade.php --}}
    <x-slot:scripts>
    <script>
    (function () {
        // Tunggu sampai jQuery & Swal siap
        function initLoginHandler() {
            if (typeof $ === 'undefined' || typeof Swal === 'undefined') {
                return setTimeout(initLoginHandler, 50);
            }

            // Sembunyikan spinner saat pertama load
            $('#kt_sign_in_submit .indicator-progress').hide();

            $('#kt_sign_in_form').off('submit').on('submit', function (e) {
                e.preventDefault();

                var $form  = $(this);
                var $btn   = $('#kt_sign_in_submit');
                var $label = $btn.find('.indicator-label');
                var $spin  = $btn.find('.indicator-progress');

                // Tampilkan loading spinner
                $btn.prop('disabled', true);
                $label.hide();
                $spin.show();

                // Kirim via AJAX dengan header JSON agar Laravel return 422 JSON saat error
                axios.post($form.attr('action'), {
                    email    : $form.find('[name=email]').val(),
                    password : $form.find('[name=password]').val(),
                    remember : $form.find('[name=remember]').is(':checked') ? 'on' : '',
                    _token   : $form.find('[name=_token]').val(),
                }, {
                    headers: {
                        'Accept'           : 'application/json',
                        'X-Requested-With' : 'XMLHttpRequest',
                    },
                    maxRedirects: 5,
                })
                .then(function () {
                    // Login berhasil → tampil SwalSuccess lalu redirect
                    Swal.fire({
                        icon             : 'success',
                        title            : 'Login Berhasil!',
                        text             : 'Selamat datang kembali di PresenceSync 🎉',
                        timer            : 1800,
                        timerProgressBar : true,
                        showConfirmButton : false,
                        allowOutsideClick : false,
                    }).then(function () {
                        window.location.href = '/absensi/dashboard';
                    });
                })
                .catch(function (error) {
                    // Reset tombol
                    $btn.prop('disabled', false);
                    $label.show();
                    $spin.hide();

                    var errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';

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
    </x-slot:scripts>

</x-auth-layout>
