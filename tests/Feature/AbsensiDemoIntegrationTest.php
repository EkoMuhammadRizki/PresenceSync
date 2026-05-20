<?php

use App\Models\User;

test('absensi demo loads successfully', function () {
    $user = User::factory()->create();
    
    // Access the root page with demo=absensi parameter as logged in user
    $response = $this->actingAs($user)->get('/?demo=absensi');
    
    // Should return a successful response
    $response->assertStatus(200);
});

test('absensi config is loaded', function () {
    // Set the demo to absensi
    theme()->setDemo('absensi');
    theme()->initConfig();
    
    // Verify that the config is loaded
    expect(config('absensi.general'))->not->toBeNull();
    expect(config('absensi.menu'))->not->toBeNull();
    expect(config('absensi.pages'))->not->toBeNull();
});

test('bootstrap run works with absensi', function () {
    theme()->setDemo('absensi');
    theme()->initConfig();
    
    // This should not throw an exception
    $bootstrap = bootstrap();
    $bootstrap->initLayout();
    
    // Verify that aside menu is initialized
    $asideMenu = $bootstrap->getAsideMenu();
    expect($asideMenu)->not->toBeNull();
});

test('get breadcrumb works with absensi', function () {
    theme()->setDemo('absensi');
    theme()->initConfig();
    
    $bootstrap = bootstrap();
    $bootstrap->initLayout();
    
    // Get breadcrumb
    $breadcrumb = $bootstrap->getBreadcrumb();
    
    // Should return an array
    expect($breadcrumb)->toBeArray();
});
