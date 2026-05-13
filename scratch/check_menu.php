<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Core\Adapters\Theme;
use App\Core\Adapters\Menu;

Theme::setDemo('absensi');
$menu = new Menu(Theme::getOption('menu', 'main'), 'absensi/kehadiran');
$html = $menu->build();
file_put_contents(__DIR__ . '/menu_output.html', $html);
echo "Saved to menu_output.html";
