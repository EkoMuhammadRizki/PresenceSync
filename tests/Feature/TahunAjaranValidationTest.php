<?php

use App\Models\User;
use App\Models\TahunAjaran;

test('admin can add tahun ajaran when there is no active one', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('tahun-ajaran.store'), [
        'nama'          => '2026/2027',
        'bulan_mulai'   => '2026-07-01',
        'bulan_selesai' => '2027-06-30',
    ]);

    $response->assertRedirect(route('tahun-ajaran.index'));
    $this->assertDatabaseHas('tahun_ajarans', [
        'nama' => '2026/2027',
    ]);
});

test('admin cannot add tahun ajaran when there is already an active one', function () {
    $user = User::factory()->create();

    // Create an active Tahun Ajaran first
    TahunAjaran::create([
        'nama'          => '2025/2026',
        'bulan_mulai'   => '2025-07-01',
        'bulan_selesai' => '2026-06-30',
        'status'        => 'aktif',
    ]);

    // Attempt to add a new one
    $response = $this->actingAs($user)->post(route('tahun-ajaran.store'), [
        'nama'          => '2026/2027',
        'bulan_mulai'   => '2026-07-01',
        'bulan_selesai' => '2027-06-30',
    ]);

    $response->assertRedirect(route('tahun-ajaran.index'));
    $response->assertSessionHas('error', 'Tidak dapat menambahkan tahun ajaran baru. Ubah status tahun ajaran yang aktif menjadi "Selesai" terlebih dahulu.');
    
    // Ensure the new one was NOT added
    $this->assertDatabaseMissing('tahun_ajarans', [
        'nama' => '2026/2027',
    ]);
});

test('admin can add new tahun ajaran after setting the active one to selesai', function () {
    $user = User::factory()->create();

    // Create an active Tahun Ajaran first
    $activeTa = TahunAjaran::create([
        'nama'          => '2025/2026',
        'bulan_mulai'   => '2025-07-01',
        'bulan_selesai' => '2026-06-30',
        'status'        => 'aktif',
    ]);

    // Update active one to selesai
    $responseUpdate = $this->actingAs($user)->put(route('tahun-ajaran.update', $activeTa->id), [
        'nama'          => '2025/2026',
        'bulan_mulai'   => '2025-07-01',
        'bulan_selesai' => '2026-06-30',
        'status'        => 'selesai',
    ]);

    $responseUpdate->assertRedirect(route('tahun-ajaran.index'));
    $this->assertDatabaseHas('tahun_ajarans', [
        'id'     => $activeTa->id,
        'status' => 'selesai',
    ]);

    // Now, adding a new one should succeed
    $responseStore = $this->actingAs($user)->post(route('tahun-ajaran.store'), [
        'nama'          => '2026/2027',
        'bulan_mulai'   => '2026-07-01',
        'bulan_selesai' => '2027-06-30',
    ]);

    $responseStore->assertRedirect(route('tahun-ajaran.index'));
    $this->assertDatabaseHas('tahun_ajarans', [
        'nama' => '2026/2027',
    ]);
});
