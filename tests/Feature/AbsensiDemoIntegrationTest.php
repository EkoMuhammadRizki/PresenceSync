<?php

namespace Tests\Feature;

use Tests\TestCase;

class AbsensiDemoIntegrationTest extends TestCase
{
    /**
     * Test that accessing a page with ?demo=absensi works correctly
     *
     * @return void
     */
    public function test_absensi_demo_loads_successfully()
    {
        // Access the root page with demo=absensi parameter
        $response = $this->get('/?demo=absensi');
        
        // Should return a successful response
        $response->assertStatus(200);
    }

    /**
     * Test that the absensi config is loaded correctly
     *
     * @return void
     */
    public function test_absensi_config_is_loaded()
    {
        // Set the demo to absensi
        theme()->setDemo('absensi');
        theme()->initConfig();
        
        // Verify that the config is loaded
        $this->assertNotNull(config('absensi.general'));
        $this->assertNotNull(config('absensi.menu'));
        $this->assertNotNull(config('absensi.pages'));
    }

    /**
     * Test that bootstrap()->run() works with absensi demo
     *
     * @return void
     */
    public function test_bootstrap_run_works_with_absensi()
    {
        theme()->setDemo('absensi');
        theme()->initConfig();
        
        // This should not throw an exception
        $bootstrap = bootstrap();
        $bootstrap->initLayout();
        
        // Verify that aside menu is initialized
        $asideMenu = $bootstrap->getAsideMenu();
        $this->assertNotNull($asideMenu);
    }

    /**
     * Test that getBreadcrumb() works with absensi demo
     *
     * @return void
     */
    public function test_get_breadcrumb_works_with_absensi()
    {
        theme()->setDemo('absensi');
        theme()->initConfig();
        
        $bootstrap = bootstrap();
        $bootstrap->initLayout();
        
        // Get breadcrumb
        $breadcrumb = $bootstrap->getBreadcrumb();
        
        // Should return an array
        $this->assertIsArray($breadcrumb);
    }
}
