<?php
$base_dir = dirname(__DIR__);

// 1. Controllers
$controllers = [];
foreach (glob($base_dir . '/application/controllers/*.php') as $file) {
    $className = basename($file, '.php');
    if ($className === 'index') continue;
    $content = file_get_contents($file);
    preg_match_all('/public\s+function\s+([a-zA-Z0-9_]+)\s*\(/i', $content, $m);
    $methods = $m[1] ?? [];
    $controllers[$className] = [
        'file' => $file,
        'methods' => $methods
    ];
}

// 2. Models
$models = [];
foreach (glob($base_dir . '/application/models/*.php') as $file) {
    $className = basename($file, '.php');
    if ($className === 'index') continue;
    $content = file_get_contents($file);
    preg_match_all('/public\s+function\s+([a-zA-Z0-9_]+)\s*\(/i', $content, $m);
    $methods = $m[1] ?? [];
    $models[$className] = [
        'file' => $file,
        'methods' => $methods
    ];
}

// 3. Views
$views = [];
$view_iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base_dir . '/application/views'));
foreach ($view_iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $rel = str_replace([$base_dir . '/application/views/', $base_dir . '\\application\\views\\', '.php'], '', $file->getPathname());
        $rel = str_replace('\\', '/', $rel);
        $views[] = $rel;
    }
}

// 4. Routes
$routes_content = file_get_contents($base_dir . '/application/config/routes.php');
preg_match_all('/\$route\[[\'"]([^\'"]+)[\'"]\]\s*=\s*[\'"]([^\'"]+)[\'"]\s*;/i', $routes_content, $m_routes);
$routes = array_combine($m_routes[1], $m_routes[2]);

echo "=== PROJECT INVENTORY SUMMARY ===\n";
echo "Controllers: " . count($controllers) . "\n";
$total_ctrl_methods = 0;
foreach ($controllers as $c => $d) $total_ctrl_methods += count($d['methods']);
echo "Controller Methods: $total_ctrl_methods\n";

echo "Models: " . count($models) . "\n";
$total_model_methods = 0;
foreach ($models as $m => $d) $total_model_methods += count($d['methods']);
echo "Model Methods: $total_model_methods\n";

echo "Views: " . count($views) . "\n";
echo "Explicit Routes: " . count($routes) . "\n";
