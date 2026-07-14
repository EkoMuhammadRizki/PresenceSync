<?php

use App\Models\User;

test('admin can view the panduan guide page', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('panduan.index'));

    $response->assertStatus(200);
    $response->assertSee('Panduan Lengkap Penggunaan');
});

test('admin dashboard shows the short guide modal on first visit, then does not show it again, and shows it again after logout and login', function () {
    $user = User::factory()->create();

    // 1. First login
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);
    
    $this->assertAuthenticated();

    // 2. Visit dashboard: should see modal
    $response = $this->get('/absensi/dashboard');
    $response->assertStatus(200);
    $response->assertSee('modal_panduan_singkat');
    $response->assertSee('bootstrap.Modal');

    // 3. Visit dashboard again: should NOT see modal script
    $response = $this->get('/absensi/dashboard');
    $response->assertStatus(200);
    $response->assertDontSee('bootstrap.Modal');

    // 4. Logout (using GET route or POST route to simulate user logout)
    $this->get('/logout');
    $this->assertGuest();

    // 5. Login again
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);
    $this->assertAuthenticated();

    // 6. Visit dashboard: should see modal again
    $response = $this->get('/absensi/dashboard');
    $response->assertStatus(200);
    $response->assertSee('modal_panduan_singkat');
    $response->assertSee('bootstrap.Modal');
});

