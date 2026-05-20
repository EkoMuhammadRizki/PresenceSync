<?php

test('system log page can be rendered', function () {
    $response = $this->get('/log/system');

    $response->assertStatus(200);
});
