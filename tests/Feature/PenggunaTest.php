<?php

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user management page can be loaded by authenticated admin', function () {
    $admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $admin->assignRole($role);

    $response = $this->actingAs($admin)->get('absensi/pengguna/data');

    $response->assertStatus(200);
});

test('admin can create a new user account', function () {
    $admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $admin->assignRole($role);

    $response = $this->actingAs($admin)->post(route('pengguna.store'), [
        'username' => 'test.kesiswaan',
        'email'    => 'kesiswaan@test.com',
        'password' => 'password123',
        'role'     => 'kesiswaan',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'email' => 'kesiswaan@test.com',
    ]);

    $user = User::where('email', 'kesiswaan@test.com')->first();
    expect($user->hasRole('kesiswaan'))->toBeTrue();
});

test('admin can update a user account', function () {
    $admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $admin->assignRole($role);

    $user = User::create([
        'first_name' => 'Old',
        'last_name'  => 'Name',
        'email'      => 'old@test.com',
        'password'   => bcrypt('password123'),
    ]);
    $user->assignRole(Role::firstOrCreate(['name' => 'orang_tua']));

    $response = $this->actingAs($admin)->put(route('pengguna.update', $user->id), [
        'username' => 'new.name',
        'email'    => 'new@test.com',
        'password' => 'newpassword123',
        'role'     => 'admin',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $user->refresh();
    expect($user->email)->toBe('new@test.com');
    expect($user->first_name)->toBe('new');
    expect($user->last_name)->toBe('name');
    expect($user->hasRole('admin'))->toBeTrue();
});

test('admin can delete a user account', function () {
    $admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $admin->assignRole($role);

    $user = User::create([
        'first_name' => 'ToBe',
        'last_name'  => 'Deleted',
        'email'      => 'delete@test.com',
        'password'   => bcrypt('password123'),
    ]);

    $response = $this->actingAs($admin)->delete(route('pengguna.destroy', $user->id));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});

test('creating a student automatically list them in the user management page', function () {
    $admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $admin->assignRole($role);

    // Create student via SiswaController store endpoint
    $response = $this->actingAs($admin)->post(route('siswa.store'), [
        'email'         => 'eko.muhammad@siswa.com',
        'password'      => 'siswa123',
        'nama'          => 'Eko Muhammad',
        'nis'           => '12345678',
        'jenis_kelamin' => 'L',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Retrieve the user page and check that student username and email exists
    $pageResponse = $this->actingAs($admin)->get('absensi/pengguna/data');
    $pageResponse->assertSee('eko.muhammad');
    $pageResponse->assertSee('eko.muhammad@siswa.com');
    $pageResponse->assertSee('Siswa');
});

test('creating a teacher automatically list them in the user management page', function () {
    $admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $admin->assignRole($role);

    // Create teacher via GuruController store endpoint
    $response = $this->actingAs($admin)->post(route('guru.store'), [
        'email'    => 'eko.guru@sekolah.com',
        'password' => 'guru123',
        'nama'     => 'Eko Guru',
        'nip'      => '987654321',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Retrieve the user page and check that teacher username and email exists
    $pageResponse = $this->actingAs($admin)->get('absensi/pengguna/data');
    $pageResponse->assertSee('eko.guru');
    $pageResponse->assertSee('eko.guru@sekolah.com');
    $pageResponse->assertSee('Guru');
});
