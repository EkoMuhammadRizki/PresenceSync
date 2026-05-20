<?php

use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->menuConfig = require base_path('config/absensi/menu.php');
    $this->pagesConfig = require base_path('config/absensi/pages.php');
});

test('menu items with sub have required attributes and classes', function () {
    $itemsWithSub = collectItemsWithSub($this->menuConfig['main']);
    
    $this->assertNotEmpty($itemsWithSub, 'Should have at least one menu item with sub-menu');
    
    foreach ($itemsWithSub as $item) {
        $this->assertArrayHasKey('attributes', $item, 
            "Menu item '{$item['title']}' with 'sub' must have 'attributes' key");
        
        $this->assertArrayHasKey('data-kt-menu-trigger', $item['attributes'], 
            "Menu item '{$item['title']}' with 'sub' must have 'data-kt-menu-trigger' in attributes");
        
        $this->assertEquals('click', $item['attributes']['data-kt-menu-trigger'], 
            "Menu item '{$item['title']}' should have 'data-kt-menu-trigger' set to 'click'");
        
        $this->assertArrayHasKey('classes', $item, 
            "Menu item '{$item['title']}' with 'sub' must have 'classes' key");
        
        $this->assertArrayHasKey('item', $item['classes'], 
            "Menu item '{$item['title']}' with 'sub' must have 'classes['item']' key");
        
        $this->assertStringContainsString('menu-accordion', $item['classes']['item'], 
            "Menu item '{$item['title']}' with 'sub' must have 'menu-accordion' in classes['item']");
    }
});

test('menu items with path have corresponding pages config', function () {
    $itemsWithPath = collectItemsWithPath($this->menuConfig['main']);
    
    $this->assertNotEmpty($itemsWithPath, 'Should have at least one menu item with path');
    
    foreach ($itemsWithPath as $item) {
        $path = $item['path'];
        
        if ($path === 'logout' || $path === '#') {
            continue;
        }
        
        $pathSegments = explode('/', $path);
        
        $pageConfig = $this->pagesConfig;
        
        foreach ($pathSegments as $segment) {
            $this->assertArrayHasKey($segment, $pageConfig, 
                "Path segment '{$segment}' from menu item '{$item['title']}' (path: {$path}) not found in pages.php");
            
            $pageConfig = $pageConfig[$segment];
        }
        
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
});

function collectItemsWithSub(array $items): array
{
    $result = [];
    
    foreach ($items as $item) {
        if (isset($item['sub'])) {
            $result[] = $item;
            
            if (isset($item['sub']['items'])) {
                $result = array_merge($result, collectItemsWithSub($item['sub']['items']));
            }
        }
    }
    
    return $result;
}

function collectItemsWithPath(array $items): array
{
    $result = [];
    
    foreach ($items as $item) {
        if (isset($item['path']) && !empty($item['path'])) {
            $result[] = $item;
        }
        
        if (isset($item['sub']['items'])) {
            $result = array_merge($result, collectItemsWithPath($item['sub']['items']));
        }
    }
    
    return $result;
}
