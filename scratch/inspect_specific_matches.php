<?php
$data = json_decode(file_get_contents(__DIR__ . '/audit_all_matches.json'), true);

$targets = ['schoolnew', 'localhost', '127.0.0.1', 'windows_drive', 'wwwroot', 'server_globals'];

foreach ($targets as $t) {
    echo "==================== TARGET: $t ====================\n";
    foreach ($data as $item) {
        if ($item['pattern'] === $t) {
            echo "{$item['file']}:{$item['line']} [{$item['match']}] => {$item['content']}\n";
        }
    }
    echo "\n";
}
