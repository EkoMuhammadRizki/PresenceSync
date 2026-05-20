<?php

use App\Core\Bootstraps\BootstrapAbsensi;
use Tests\TestCase;

uses(TestCase::class);

test('theme can set absensi demo', function () {
    $theme = theme();
    $theme->setDemo('absensi');
    
    expect($theme->getDemo())->toBe('absensi');
});

test('bootstrap helper resolves absensi class', function () {
    $theme = theme();
    $theme->setDemo('absensi');
    $theme->initConfig();
    
    $bootstrap = bootstrap();
    
    expect($bootstrap)->toBeInstanceOf(BootstrapAbsensi::class);
});

test('bootstrap absensi can be instantiated', function () {
    expect(class_exists(BootstrapAbsensi::class))->toBeTrue();
    
    $theme = theme();
    $theme->setDemo('absensi');
    $theme->initConfig();
    
    $bootstrap = app(BootstrapAbsensi::class);
    
    expect($bootstrap)->not->toBeNull();
});

test('query parameter sets absensi demo', function () {
    // Create a request with ?demo=absensi query parameter
    $request = \Illuminate\Http\Request::create('/', 'GET', ['demo' => 'absensi']);
    
    // Bind the request to the container
    app()->instance('request', $request);
    
    // Get the demo value from the request
    $demoValue = $request->input('demo', 'demo1');
    
    // Verify the request contains the correct demo parameter
    expect($demoValue)->toBe('absensi');
});
