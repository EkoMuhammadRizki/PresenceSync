<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('kesiswaan user is redirected to kesiswaan dashboard and sees admin dashboard content plus siswa and kelas menus', function () {
    // 1. Create kesiswaan role and user
    $kesiswaanRole = Role::firstOrCreate(['name' => 'kesiswaan']);
    $user = User::factory()->create();
    $user->assignRole($kesiswaanRole);

    // 2. Hit root path and assert redirect to kesiswaan dashboard
    $rootResponse = $this->actingAs($user)->get('/');
    $rootResponse->assertRedirect('/absensi/kesiswaan/dashboard');

    // 3. Hit kesiswaan dashboard and assert it has admin dashboard contents but NO panduan modal
    $dashboardResponse = $this->actingAs($user)->get('/absensi/kesiswaan/dashboard');
    $dashboardResponse->assertStatus(200);
    $dashboardResponse->assertSee('Tren Kehadiran');
    $dashboardResponse->assertDontSee('Langkah cepat mengonfigurasi sistem Presence Sync');
 
    // 4. Assert sidebar menus (Data Siswa & Kelas are present, Master Data & Kehadiran Siswa are not)
    $dashboardResponse->assertSee('Data Siswa');
    $dashboardResponse->assertSee('absensi/master/siswa');
    $dashboardResponse->assertSee('absensi/master/kelas/data');
    $dashboardResponse->assertSee('absensi/master/kelas/pembagian');
    $dashboardResponse->assertDontSee('Master Data');
    $dashboardResponse->assertDontSee('absensi/kehadiran');
});
