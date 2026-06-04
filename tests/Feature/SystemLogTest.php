<?php

use App\Models\User;

test('system log page can be rendered', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/log/system');

    $response->assertStatus(200);
});
