<?php
$base_dir = dirname(__DIR__);

$views_dir = $base_dir . '/application/views';
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($views_dir));

$all_views = [];
foreach ($it as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $full = str_replace('\\', '/', $file->getPathname());
        $rel = str_replace(str_replace('\\', '/', $views_dir) . '/', '', $full);
        $rel = preg_replace('/\.php$/', '', $rel);
        $all_views[$rel] = $full;
    }
}

// Load controllers and views code
$controllers_code = '';
foreach (glob($base_dir . '/application/controllers/*.php') as $f) {
    $controllers_code .= "\n" . file_get_contents($f);
}
$core_code = file_get_contents($base_dir . '/application/core/MY_Controller.php');
$views_code = '';
foreach ($all_views as $rel => $full) {
    $views_code .= "\n" . file_get_contents($full);
}
$helpers_code = file_get_contents($base_dir . '/application/helpers/app_helper.php');
$app_js = file_get_contents($base_dir . '/assets/app.js');

$combined_code = $controllers_code . "\n" . $core_code . "\n" . $views_code . "\n" . $helpers_code . "\n" . $app_js;

$unreferenced_views = [];
foreach ($all_views as $rel => $full) {
    if (in_array($rel, ['templates/header', 'templates/footer', 'auth/login', 'pages/dashboard', 'pages/unauthorized'])) continue;
    
    // Check if $rel is in combined code
    // Patterns: 'pages/...', "pages/...", '$view', etc.
    $patterns = [
        "'$rel'",
        "\"$rel\"",
        "'$rel.php'",
        "\"$rel.php\""
    ];
    // Also without 'pages/'
    $without_pages = preg_replace('#^pages/#', '', $rel);
    $patterns[] = "'$without_pages'";
    $patterns[] = "\"$without_pages\"";

    $found = false;
    foreach ($patterns as $p) {
        if (strpos($combined_code, trim($p, "'\"")) !== false) {
            $found = true;
            break;
        }
    }

    if (!$found) {
        $unreferenced_views[] = $rel;
    }
}

echo "Total views audited: " . count($all_views) . "\n";
echo "Unreferenced views: " . count($unreferenced_views) . "\n";
foreach ($unreferenced_views as $uv) {
    echo " - $uv\n";
}
