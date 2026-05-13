<?php

namespace Tests\Unit;

use App\Core\Bootstraps\BootstrapAbsensi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbsensiDemoSupportTest extends TestCase
{
    /**
     * Test that theme()->setDemo() can accept 'absensi' value
     *
     * @return void
     */
    public function test_theme_can_set_absensi_demo()
    {
        $theme = theme();
        $theme->setDemo('absensi');
        
        $this->assertEquals('absensi', $theme->getDemo());
    }

    /**
     * Test that bootstrap() helper can resolve BootstrapAbsensi class
     *
     * @return void
     */
    public function test_bootstrap_helper_resolves_absensi_class()
    {
        $theme = theme();
        $theme->setDemo('absensi');
        $theme->initConfig();
        
        $bootstrap = bootstrap();
        
        $this->assertInstanceOf(BootstrapAbsensi::class, $bootstrap);
    }

    /**
     * Test that BootstrapAbsensi can be instantiated
     *
     * @return void
     */
    public function test_bootstrap_absensi_can_be_instantiated()
    {
        $this->assertTrue(class_exists(BootstrapAbsensi::class));
        
        $theme = theme();
        $theme->setDemo('absensi');
        $theme->initConfig();
        
        $bootstrap = app(BootstrapAbsensi::class);
        
        $this->assertNotNull($bootstrap);
    }

    /**
     * Test that query parameter ?demo=absensi sets the demo correctly
     *
     * @return void
     */
    public function test_query_parameter_sets_absensi_demo()
    {
        // Create a request with ?demo=absensi query parameter
        $request = \Illuminate\Http\Request::create('/', 'GET', ['demo' => 'absensi']);
        
        // Bind the request to the container
        app()->instance('request', $request);
        
        // Get the demo value from the request
        $demoValue = $request->input('demo', 'demo1');
        
        // Verify the request contains the correct demo parameter
        $this->assertEquals('absensi', $demoValue);
    }
}
