<?php

use App\Models\AturanJam;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can view aturan jam page', function () {
    $admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $admin->assignRole($role);

    $response = $this->actingAs($admin)->get('absensi/master/aturan-jam');

    $response->assertStatus(200);
});

test('admin can create aturan jam on Sunday', function () {
    $admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $admin->assignRole($role);

    $response = $this->actingAs($admin)->post(route('aturan-jam.store'), [
        'hari'                    => 'Minggu',
        'jam_masuk'               => '19:00',
        'toleransi_keterlambatan' => 15,
        'jam_pulang'              => '03:00',
        'is_aktif'                => '1',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('aturan_jams', [
        'hari'                    => 'Minggu',
        'jam_masuk'               => '19:00',
        'toleransi_keterlambatan' => 15,
        'jam_pulang'              => '03:00',
        'is_aktif'                => true,
    ]);
});

test('admin can update aturan jam', function () {
    $admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $admin->assignRole($role);

    $aturanJam = AturanJam::create([
        'hari'                    => 'Senin',
        'jam_masuk'               => '07:00:00',
        'toleransi_keterlambatan' => 10,
        'jam_pulang'              => '14:00:00',
        'is_aktif'                => true,
    ]);

    $response = $this->actingAs($admin)->put(route('aturan-jam.update', $aturanJam->id), [
        'hari'                    => 'Minggu',
        'jam_masuk'               => '08:00',
        'toleransi_keterlambatan' => 20,
        'jam_pulang'              => '15:00',
        'is_aktif'                => '0',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $aturanJam->refresh();
    expect($aturanJam->hari)->toBe('Minggu');
    expect($aturanJam->toleransi_keterlambatan)->toBe(20);
    expect($aturanJam->is_aktif)->toBeFalse();
});
