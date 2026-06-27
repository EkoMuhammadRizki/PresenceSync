<x-base-layout>
    <!--begin::Toolbar-->
    @include('pages.absensi._partials.toolbar', [
        'toolbarActions' => '
            <a href="' . url('/') . '" class="btn btn-sm btn-light me-2">
                ' . theme()->getSvgIcon("icons/duotune/arrows/arr063.svg", "svg-icon-4") . ' Kembali ke Dashboard
            </a>'
    ])
    <!--end::Toolbar-->

    <!--begin::Navbar-->
    @include('profile.partials.profile-header', [
        'user' => $user,
        'info' => $info,
        'stats' => $stats,
        'userRole' => $userRole,
        'completionRate' => $completionRate
    ])
    <!--end::Navbar-->

    <!--begin::Tab Content-->
    <div class="tab-content" id="profileTabContent">
        <!--begin::Tab Pane - Biodata-->
        <div class="tab-pane fade show active" id="tab_biodata" role="tabpanel">
            @include('profile.partials.profile-info-card', [
                'user' => $user,
                'info' => $info,
                'userRole' => $userRole
            ])
        </div>
        <!--end::Tab Pane - Biodata-->

        <!--begin::Tab Pane - Pengaturan-->
        <div class="tab-pane fade" id="tab_pengaturan" role="tabpanel">
            @include('profile.partials.profile-settings', [
                'user' => $user,
                'info' => $info,
                'userRole' => $userRole
            ])
        </div>
        <!--end::Tab Pane - Pengaturan-->
    </div>
    <!--end::Tab Content-->

    @section('scripts')
        @if(session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'OK',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
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
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
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
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });
            </script>
        @endif
    @endsection
</x-base-layout>
