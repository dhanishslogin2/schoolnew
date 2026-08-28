<?php
$dir = __DIR__ . '/../application';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$php_files = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

$errors = [];
$checked = 0;

foreach ($php_files as $file) {
    $filepath = $file[0];
    $output = [];
    $ret = 0;
    exec("php -l " . escapeshellarg($filepath), $output, $ret);
    $checked++;
    if ($ret !== 0) {
        $errors[] = implode("\n", $output);
    }
}

echo "Checked $checked PHP files.\n";
if (empty($errors)) {
    echo "SUCCESS: 0 PHP syntax errors found!\n";
} else {
    echo "ERRORS FOUND:\n" . implode("\n---\n", $errors) . "\n";
}
