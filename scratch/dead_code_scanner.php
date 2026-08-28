<?php
$base_dir = dirname(__DIR__);

// Load all project files text
$all_php_files = [];
$all_js_files = [];
$all_css_files = [];

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base_dir));
foreach ($it as $file) {
    if (!$file->isFile()) continue;
    $path = $file->getPathname();
    if (strpos($path, 'node_modules') !== false || strpos($path, '.git') !== false || strpos($path, 'scratch') !== false) continue;
    $ext = strtolower($file->getExtension());
    if ($ext === 'php') $all_php_files[$path] = file_get_contents($path);
    elseif ($ext === 'js') $all_js_files[$path] = file_get_contents($path);
    elseif ($ext === 'css') $all_css_files[$path] = file_get_contents($path);
}

$all_code = implode("\n", array_merge($all_php_files, $all_js_files));

// 1. Audit Views
$views = [];
$view_iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base_dir . '/application/views'));
foreach ($view_iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        $rel = str_replace([$base_dir . '/application/views/', $base_dir . '\\application\\views\\', '.php'], '', $path);
        $rel = str_replace('\\', '/', $rel);
        $views[$rel] = $path;
    }
}

$unused_views = [];
foreach ($views as $view_rel => $view_path) {
    // Search for load->view or render or string reference
    $view_no_pages = preg_replace('#^pages/#', '', $view_rel);
    $pattern = '#' . preg_quote($view_rel, '#') . '|' . preg_quote($view_no_pages, '#') . '#i';
    
    $found = false;
    foreach ($all_php_files as $p => $c) {
        if ($p === $view_path) continue;
        if (preg_match($pattern, $c)) {
            $found = true;
            break;
        }
    }
    if (!$found) {
        foreach ($all_js_files as $p => $c) {
            if (preg_match($pattern, $c)) {
                $found = true;
                break;
            }
        }
    }
    if (!$found) {
        $unused_views[] = $view_rel;
    }
}

// 2. Audit Model Methods
$models = [];
$unused_model_methods = [];
foreach (glob($base_dir . '/application/models/*.php') as $file) {
    $className = basename($file, '.php');
    if ($className === 'index') continue;
    $content = file_get_contents($file);
    preg_match_all('/public\s+function\s+([a-zA-Z0-9_]+)\s*\(/i', $content, $m);
    $methods = $m[1] ?? [];
    foreach ($methods as $method) {
        if ($method === '__construct') continue;
        // Search in all files
        $method_pattern = '/->' . preg_quote($method, '/') . '\s*\(/i';
        $found = false;
        foreach ($all_php_files as $p => $c) {
            if ($p === $file) {
                // Check if called inside self
                $count = preg_match_all($method_pattern, $c);
                if ($count > 0) {
                    // Could be internal call
                }
                continue;
            }
            if (preg_match($method_pattern, $c)) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $unused_model_methods[$className . '::' . $method] = [
                'model' => $className,
                'method' => $method,
                'file' => $file
            ];
        }
    }
}

// 3. Audit Controllers
$unused_ctrl_methods = [];
$routes_content = file_get_contents($base_dir . '/application/config/routes.php');
$app_js_content = file_get_contents($base_dir . '/assets/app.js');

foreach (glob($base_dir . '/application/controllers/*.php') as $file) {
    $className = basename($file, '.php');
    if ($className === 'index') continue;
    $content = file_get_contents($file);
    preg_match_all('/public\s+function\s+([a-zA-Z0-9_]+)\s*\(/i', $content, $m);
    $methods = $m[1] ?? [];
    foreach ($methods as $method) {
        if ($method === '__construct') continue;
        // Check if referenced in routes, app.js, or views
        $ctrl_lower = strtolower($className);
        $method_lower = strtolower($method);
        
        $patterns = [
            $ctrl_lower . '/' . $method_lower,
            $ctrl_lower . '/' . $method,
            $className . '/' . $method,
            "'$method'",
            "\"$method\""
        ];
        
        $found = false;
        foreach ($patterns as $pat) {
            if (stripos($routes_content, $pat) !== false || stripos($app_js_content, $pat) !== false) {
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            // Check in views and controllers
            foreach ($all_php_files as $p => $c) {
                if ($p === $file) continue;
                if (stripos($c, $ctrl_lower . '/' . $method_lower) !== false || stripos($c, $className . '/' . $method) !== false) {
                    $found = true;
                    break;
                }
            }
        }

        if (!$found) {
            $unused_ctrl_methods[$className . '::' . $method] = [
                'controller' => $className,
                'method' => $method,
                'file' => $file
            ];
        }
    }
}

echo "=== UNUSED CANDIDATES AUDIT RESULTS ===\n";
echo "Candidate Unused Views: " . count($unused_views) . "\n";
foreach ($unused_views as $v) echo " - View: $v\n";

echo "\nCandidate Unused Controller Methods: " . count($unused_ctrl_methods) . "\n";
foreach (array_keys($unused_ctrl_methods) as $cm) echo " - $cm\n";

echo "\nCandidate Unused Model Methods (Total " . count($unused_model_methods) . "):\n";
foreach (array_slice(array_keys($unused_model_methods), 0, 30) as $mm) echo " - $mm\n";
if (count($unused_model_methods) > 30) echo " ... and " . (count($unused_model_methods) - 30) . " more\n";
