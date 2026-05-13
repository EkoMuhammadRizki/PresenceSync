<?php

namespace Tests\Unit;

use Tests\TestCase;

class AbsensiConfigStructureTest extends TestCase
{
    private $menuConfig;
    private $pagesConfig;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Load the menu and pages configuration
        $this->menuConfig = require base_path('config/absensi/menu.php');
        $this->pagesConfig = require base_path('config/absensi/pages.php');
    }

    /**
     * Test that all menu items with 'sub' have required attributes and classes
     * 
     * Validates: Requirements 6.1, 6.2, 6.4
     *
     * @return void
     */
    public function test_menu_items_with_sub_have_required_attributes_and_classes()
    {
        $itemsWithSub = $this->collectItemsWithSub($this->menuConfig['main']);
        
        $this->assertNotEmpty($itemsWithSub, 'Should have at least one menu item with sub-menu');
        
        foreach ($itemsWithSub as $item) {
            // Verify data-kt-menu-trigger exists in attributes
            $this->assertArrayHasKey('attributes', $item, 
                "Menu item '{$item['title']}' with 'sub' must have 'attributes' key");
            
            $this->assertArrayHasKey('data-kt-menu-trigger', $item['attributes'], 
                "Menu item '{$item['title']}' with 'sub' must have 'data-kt-menu-trigger' in attributes");
            
            $this->assertEquals('click', $item['attributes']['data-kt-menu-trigger'], 
                "Menu item '{$item['title']}' should have 'data-kt-menu-trigger' set to 'click'");
            
            // Verify menu-accordion exists in classes['item']
            $this->assertArrayHasKey('classes', $item, 
                "Menu item '{$item['title']}' with 'sub' must have 'classes' key");
            
            $this->assertArrayHasKey('item', $item['classes'], 
                "Menu item '{$item['title']}' with 'sub' must have 'classes['item']' key");
            
            $this->assertStringContainsString('menu-accordion', $item['classes']['item'], 
                "Menu item '{$item['title']}' with 'sub' must have 'menu-accordion' in classes['item']");
        }
    }

    /**
     * Test that all menu items with 'path' have corresponding entries in pages.php
     * 
     * Validates: Requirements 6.1, 6.2, 6.4
     *
     * @return void
     */
    public function test_menu_items_with_path_have_corresponding_pages_config()
    {
        $itemsWithPath = $this->collectItemsWithPath($this->menuConfig['main']);
        
        $this->assertNotEmpty($itemsWithPath, 'Should have at least one menu item with path');
        
        foreach ($itemsWithPath as $item) {
            $path = $item['path'];
            
            // Skip special paths like 'logout' that don't need pages config
            if ($path === 'logout' || $path === '#') {
                continue;
            }
            
            // Convert path to nested array keys (e.g., 'absensi/dashboard' -> ['absensi', 'dashboard'])
            $pathSegments = explode('/', $path);
            
            // Navigate through the pages config using path segments
            $pageConfig = $this->pagesConfig;
            $fullPath = '';
            
            foreach ($pathSegments as $segment) {
                $fullPath .= ($fullPath ? '/' : '') . $segment;
                
                $this->assertArrayHasKey($segment, $pageConfig, 
                    "Path segment '{$segment}' from menu item '{$item['title']}' (path: {$path}) not found in pages.php");
                
                $pageConfig = $pageConfig[$segment];
            }
            
            // Verify the final config has 'title' and 'view' keys
            $this->assertIsArray($pageConfig, 
                "Page config for path '{$path}' should be an array");
            
            $this->assertArrayHasKey('title', $pageConfig, 
                "Page config for path '{$path}' must have 'title' key");
            
            $this->assertArrayHasKey('view', $pageConfig, 
                "Page config for path '{$path}' must have 'view' key");
            
            $this->assertNotEmpty($pageConfig['title'], 
                "Page config for path '{$path}' must have non-empty 'title'");
            
            $this->assertNotEmpty($pageConfig['view'], 
                "Page config for path '{$path}' must have non-empty 'view'");
        }
    }

    /**
     * Recursively collect all menu items that have a 'sub' key
     *
     * @param array $items
     * @return array
     */
    private function collectItemsWithSub(array $items): array
    {
        $result = [];
        
        foreach ($items as $item) {
            if (isset($item['sub'])) {
                $result[] = $item;
                
                // Recursively check sub-items
                if (isset($item['sub']['items'])) {
                    $result = array_merge($result, $this->collectItemsWithSub($item['sub']['items']));
                }
            }
        }
        
        return $result;
    }

    /**
     * Recursively collect all menu items that have a 'path' key
     *
     * @param array $items
     * @return array
     */
    private function collectItemsWithPath(array $items): array
    {
        $result = [];
        
        foreach ($items as $item) {
            if (isset($item['path']) && !empty($item['path'])) {
                $result[] = $item;
            }
            
            // Recursively check sub-items
            if (isset($item['sub']['items'])) {
                $result = array_merge($result, $this->collectItemsWithPath($item['sub']['items']));
            }
        }
        
        return $result;
    }
}
