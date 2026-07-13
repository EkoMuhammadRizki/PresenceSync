<?php

use App\Models\User;

test('admin can view the panduan guide page', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('panduan.index'));

    $response->assertStatus(200);
    $response->assertSee('Panduan Lengkap Penggunaan');
});
