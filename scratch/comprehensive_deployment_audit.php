<?php
$rootDir = realpath(__DIR__ . '/..');

$patterns = [
    'schoolnew' => '/schoolnew/i',
    'schoolmanagement' => '/schoolmanagement/i',
    'localhost' => '/localhost/i',
    '127.0.0.1' => '/127\.0\.0\.1/',
    '::1' => '/::1/',
    'dev.login2.in' => '/dev\.login2\.in/i',
    'windows_drive' => '/[C-Z]:[\\\\\/]/i',
    'xampp' => '/xampp/i',
    'wwwroot' => '/\/www\/(?:wwwroot\/)?/i',
    'server_globals' => '/\$_SERVER\[[\'"](DOCUMENT_ROOT|SCRIPT_FILENAME|SCRIPT_NAME|REQUEST_URI|PATH_INFO|PHP_SELF|HTTP_HOST)[\'"]\]/',
    'url_helpers' => '/\b(base_url|site_url|current_url|uri_string)\s*\(/',
    'js_redirects' => '/(window\.location|location\.href|location\.replace)\b/',
    'php_redirects' => '/\b(redirect\s*\(|header\s*\(\s*[\'"]Location:)/i',
];

$ignoreDirs = ['.git', 'node_modules', 'playwright-report', 'test-results', 'application/cache/sessions', 'scratch'];

$results = [];

function scanDirRecursive($dir, &$results, $patterns, $ignoreDirs, $rootDir) {
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        $relPath = str_replace($rootDir . DIRECTORY_SEPARATOR, '', $path);
        $relPath = str_replace('\\', '/', $relPath);

        foreach ($ignoreDirs as $ignore) {
            if (strpos($relPath, $ignore) === 0 || strpos($item, $ignore) === 0) {
                continue 2;
            }
        }

        if (is_dir($path)) {
            scanDirRecursive($path, $results, $patterns, $ignoreDirs, $rootDir);
        } elseif (is_file($path)) {
            // Check file extensions
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            if (in_array($ext, ['php', 'js', 'html', 'css', 'json', 'htaccess', 'env', 'example', 'md', 'config']) || basename($path) === '.htaccess') {
                $lines = file($path);
                foreach ($lines as $lineNum => $line) {
                    foreach ($patterns as $pKey => $regex) {
                        if (preg_match_all($regex, $line, $matches, PREG_SET_ORDER)) {
                            foreach ($matches as $match) {
                                $results[] = [
                                    'file' => $relPath,
                                    'line' => $lineNum + 1,
                                    'pattern' => $pKey,
                                    'match' => $match[0],
                                    'content' => trim($line)
                                ];
                            }
                        }
                    }
                }
            }
        }
    }
}

scanDirRecursive($rootDir, $results, $patterns, $ignoreDirs, $rootDir);

echo "Total occurrences found: " . count($results) . "\n\n";

// Group by pattern
$grouped = [];
foreach ($results as $r) {
    $grouped[$r['pattern']][] = $r;
}

foreach ($grouped as $key => $items) {
    echo "=== Pattern: {$key} (Count: " . count($items) . ") ===\n";
    $sample = array_slice($items, 0, 15);
    foreach ($sample as $s) {
        echo "  [{$s['file']}:{$s['line']}] {$s['content']}\n";
    }
    if (count($items) > 15) {
        echo "  ... and " . (count($items) - 15) . " more\n";
    }
    echo "\n";
}

file_put_contents(__DIR__ . '/audit_all_matches.json', json_encode($results, JSON_PRETTY_PRINT));
echo "Full audit saved to scratch/audit_all_matches.json\n";
