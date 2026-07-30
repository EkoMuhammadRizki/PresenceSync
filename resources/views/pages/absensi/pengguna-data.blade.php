<x-base-layout>
@include('pages.absensi._partials.toolbar')

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

@if ($errors->any())
    <div class="alert alert-danger p-5 mb-10">
        <div class="d-flex flex-column">
            <h4 class="mb-1 text-dark">Validasi Gagal</h4>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<!--begin::Card-->
<div class="card mt-2">
    <!--begin::Card header-->
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                {!! theme()->getSvgIcon("icons/duotune/general/gen021.svg", "svg-icon-1 position-absolute ms-6") !!}
                <input type="text" id="search_pengguna" class="form-control form-control-solid w-250px ps-14" placeholder="Cari pengguna..." />
            </div>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_tambah_pengguna">
                {!! theme()->getSvgIcon("icons/duotune/arrows/arr075.svg", "svg-icon-2") !!}
                Tambah Pengguna
            </button>
        </div>
    </div>
    <!--end::Card header-->

    <!--begin::Card body-->
    <div class="card-body py-4">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_pengguna" data-bulk-type="pengguna">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="w-30px">
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input select-all-checkbox" type="checkbox" />
                        </div>
                    </td>
                    <th class="min-w-100px">Username</th>
                    <th class="min-w-150px">NIS / NIP / Email</th>
                    <th class="min-w-100px">Peran</th>
                    <th class="min-w-120px">Terakhir Login</th>
                    <th class="min-w-100px">Status Akun</th>
                    <th class="text-end min-w-70px">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @foreach ($users as $user)
                @php
                    $roleClass = match(strtolower($user->role)) {
                        'admin' => 'badge-light-primary',
                        'staff admin' => 'badge-light-primary',
                        'kesiswaan' => 'badge-light-success',
                        'orang tua' => 'badge-light-dark',
                        'guru' => 'badge-light-info',
                        'siswa' => 'badge-light-warning',
                        default => 'badge-light-primary',
                    };
                    $status = $user->siswa ? $user->siswa->status : 'aktif';
                    $statusClass = $status === 'aktif' ? 'badge-light-success' : 'badge-light-danger';
                @endphp
                <tr>
                    <td>
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input select-item-checkbox" type="checkbox" value="{{ $user->id }}" />
                        </div>
                    </td>
                    <td>
                        @php
                            $profilUrl = '#';
                            $role = strtolower($user->role);
                            if (in_array($role, ['admin', 'staff admin', 'kesiswaan', 'orang_tua'])) {
                                $profilUrl = route('profil-admin.show');
                            } elseif ($role === 'guru' && $user->guru) {
                                $profilUrl = route('profil-guru.show', ['id' => $user->guru->id]);
                            } elseif ($role === 'siswa' && $user->siswa) {
                                $profilUrl = route('profil-siswa.show', ['id' => $user->siswa->id]);
                            }
                        @endphp
                        @if ($profilUrl !== '#')
                            <a href="{{ $profilUrl }}" class="text-gray-800 text-hover-primary">{{ $user->username }}</a>
                        @else
                            {{ $user->username }}
                        @endif
                    </td>
                    <td>
                        @if ($user->siswa && $user->siswa->nis)
                            {{ $user->siswa->nis }}
                        @elseif ($user->guru && $user->guru->nip)
                            {{ $user->guru->nip }}
                        @elseif (str_contains($user->email, '@siswa.internal') || str_contains($user->email, '@guru.internal'))
                            {{ explode('@', $user->email)[0] }}
                        @else
                            {{ $user->email }}
                        @endif
                    </td>
                    <td><span class="badge {{ $roleClass }} fw-bolder">{{ $user->role }}</span></td>
                    <td>{{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}</td>
                    <td><span class="badge {{ $statusClass }} fw-bolder">{{ ucfirst($status) }}</span></td>
                    <td class="text-end">
                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            Aksi {!! theme()->getSvgIcon("icons/duotune/arrows/arr072.svg", "svg-icon-5 m-0") !!}
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            @if ($profilUrl !== '#')
                            <div class="menu-item px-3">
                                <a href="{{ $profilUrl }}" class="menu-link px-3">Detail</a>
                            </div>
                            @endif
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3 btn-edit-user"
                                   data-id="{{ $user->id }}"
                                   data-username="{{ $user->username }}"
                                   data-email="{{ $user->email }}"
                                   data-role="{{ strtolower($user->role) }}"
                                   data-status="{{ $status }}"
                                   data-bs-toggle="modal"
                                   data-bs-target="#modal_ubah_pengguna">Ubah</a>
                            </div>
                            <div class="menu-item px-3">
                                <form action="{{ route('pengguna.destroy', $user->id) }}" method="POST" class="d-inline form-konfirmasi">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="menu-link px-3 text-danger border-0 bg-transparent w-100 text-start">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!--end::Card body-->
</div>
<!--end::Card-->

<!--begin::Modal Tambah Pengguna-->
<div class="modal fade" id="modal_tambah_pengguna" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Tambah Pengguna</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="form_tambah_pengguna" class="form" method="POST" action="{{ route('pengguna.store') }}">
                    @csrf
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Username</label>
                        <input type="text" name="username" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Masukkan username" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Email</label>
                        <input type="email" name="email" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Masukkan email" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Password</label>
                        <div class="position-relative">
                            <input type="password" name="password" class="form-control form-control-solid pe-12 mb-3 mb-lg-0" placeholder="Masukkan password (minimal 6 karakter)" required />
                            <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-1 toggle-password" style="cursor: pointer;">
                                <i class="bi bi-eye-slash fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Peran</label>
                        <select name="role" class="form-select form-select-solid fw-bolder" data-control="select2" data-dropdown-parent="#modal_tambah_pengguna" data-placeholder="Pilih peran..." required>
                            <option value="">Pilih peran...</option>
                            <option value="admin">Admin</option>
                            <option value="kesiswaan">Kesiswaan</option>
                            <option value="orang_tua">Orang Tua</option>
                        </select>
                    </div>
                    <div class="text-center pt-5">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::Modal Tambah Pengguna-->

<!--begin::Modal Ubah Pengguna-->
<div class="modal fade" id="modal_ubah_pengguna" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bolder">Ubah Pengguna</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="form_ubah_pengguna" class="form" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Username</label>
                        <input type="text" name="username" class="form-control form-control-solid mb-3 mb-lg-0" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="required fw-bold fs-6 mb-2">Email</label>
                        <input type="email" name="email" class="form-control form-control-solid mb-3 mb-lg-0" required />
                    </div>
                    <div class="fv-row mb-7">
                        <label class="fw-bold fs-6 mb-2">Password Baru <span class="text-muted fs-7">(kosongkan jika tidak ingin mengubah)</span></label>
                        <div class="position-relative">
                            <input type="password" name="password" class="form-control form-control-solid pe-12 mb-3 mb-lg-0" placeholder="Password baru" />
                            <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-1 toggle-password" style="cursor: pointer;">
                                <i class="bi bi-eye-slash fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="fv-row mb-7" id="ubah_role_wrapper">
                        <label class="required fw-bold fs-6 mb-2">Peran</label>
                        <select name="role" class="form-select form-select-solid fw-bolder" data-control="select2" data-dropdown-parent="#modal_ubah_pengguna" data-placeholder="Pilih peran...">
                            <option value="admin">Admin</option>
                            <option value="kesiswaan">Kesiswaan</option>
                            <option value="orang_tua">Orang Tua</option>
                        </select>
                    </div>
                    <div class="fv-row mb-7 d-none" id="ubah_status_wrapper">
                        <label class="required fw-bold fs-6 mb-2">Status Akun</label>
                        <select name="status" class="form-select form-select-solid fw-bolder" data-control="select2" data-dropdown-parent="#modal_ubah_pengguna">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                    <div class="text-center pt-5">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::Modal Ubah Pengguna-->

<style>
    #kt_table_pengguna tbody tr {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    #kt_table_pengguna tbody tr:hover {
        background-color: var(--bs-table-hover-bg) !important;
    }
</style>

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#kt_table_pengguna').DataTable({
        dom: "<'table-responsive'tr><'row'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'li><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>",
        info: true, 
        order: [[4, 'desc']], 
        pageLength: 10, 
        lengthChange: true,
        columnDefs: [{ orderable: false, targets: [0, 6] }]
    });

    // Row click → navigate to profile
    $('#kt_table_pengguna').on('click', 'tbody tr', function(e) {
        var targetTd = $(e.target).closest('td');
        if (targetTd.length === 0) return;
        var idx = targetTd.index();
        if (idx === 0 || idx === 6 || $(e.target).closest('.menu').length || $(e.target).closest('[data-kt-menu-trigger]').length) {
            return;
        }
        var link = $(this).find('td:nth-child(2) a');
        if (link.length) {
            window.location.href = link.attr('href');
        }
    });
    $('#search_pengguna').on('keyup', function() { table.search(this.value).draw(); });

    // Toggle Password Visibility
    $(document).on('click', '.toggle-password', function(e) {
        e.preventDefault();
        var input = $(this).siblings('input');
        var icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        } else {
            input.attr('type', 'password');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        }
    });

    // Populate Edit Modal
    $(document).on('click', '.btn-edit-user', function() {
        var id = $(this).data('id');
        var username = $(this).data('username');
        var email = $(this).data('email');
        var role = $(this).data('role');
        var status = $(this).data('status');
        
        var form = $('#form_ubah_pengguna');
        form.attr('action', '{{ url("absensi/pengguna/data") }}/' + id);
        
        form.find('input[name="username"]').val(username);
        form.find('input[name="email"]').val(email);
        
        var roleSelect = form.find('select[name="role"]');
        
        // Remove dynamically added roles if any
        roleSelect.find('option[value="siswa"]').remove();
        roleSelect.find('option[value="guru"]').remove();
        
        if (role === 'siswa') {
            roleSelect.append('<option value="siswa">Siswa</option>');
            $('#ubah_role_wrapper').addClass('d-none');
            $('#ubah_status_wrapper').removeClass('d-none');
        } else if (role === 'guru') {
            roleSelect.append('<option value="guru">Guru</option>');
            $('#ubah_role_wrapper').addClass('d-none');
            $('#ubah_status_wrapper').addClass('d-none');
        } else {
            $('#ubah_role_wrapper').removeClass('d-none');
            $('#ubah_status_wrapper').addClass('d-none');
        }
        
        roleSelect.val(role).trigger('change');
        form.find('select[name="status"]').val(status).trigger('change');
        form.find('input[name="password"]').val('');
    });
});
</script>
@endsection

</x-base-layout>
