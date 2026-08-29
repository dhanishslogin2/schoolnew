<?php
$data = json_decode(file_get_contents(__DIR__ . '/audit_all_matches.json'), true);

foreach ($data as $item) {
    if ($item['pattern'] === 'schoolnew') {
        echo "{$item['file']}:{$item['line']} => {$item['content']}\n";
    }
}
