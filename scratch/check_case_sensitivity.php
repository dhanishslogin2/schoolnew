<?php
$modelsDir = realpath(__DIR__ . '/../application/models');
$controllersDir = realpath(__DIR__ . '/../application/controllers');

$actualFiles = [];
foreach (scandir($modelsDir) as $f) {
    if (pathinfo($f, PATHINFO_EXTENSION) === 'php') {
        $actualFiles[$f] = true;
    }
}

$errors = [];

foreach (scandir($controllersDir) as $c) {
    if (pathinfo($c, PATHINFO_EXTENSION) === 'php') {
        $content = file_get_contents($controllersDir . '/' . $c);
        preg_match_all('/\$this->load->model\s*\(\s*(?:array\s*\((.*?)\)|\'([^\']+)\'|"([^"]+)")/s', $content, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $m) {
            $loaded = [];
            if (!empty($m[1])) {
                preg_match_all('/[\'"]([^\'"]+)[\'"]/', $m[1], $arrMatches);
                $loaded = $arrMatches[1];
            } elseif (!empty($m[2])) {
                $loaded[] = $m[2];
            } elseif (!empty($m[3])) {
                $loaded[] = $m[3];
            }
            
            foreach ($loaded as $modelName) {
                // CI standard: model name can be Case or lowercase, but file is ucfirst($modelName).php
                $expectedFile = ucfirst($modelName) . '.php';
                if (!isset($actualFiles[$expectedFile])) {
                    $errors[] = "Controller {$c} loads model '{$modelName}' -> expected file '{$expectedFile}' NOT FOUND!";
                }
            }
        }
    }
}

if (empty($errors)) {
    echo "SUCCESS: All model loadings across all controllers match exact filenames!\n";
} else {
    echo "ERRORS FOUND:\n" . implode("\n", $errors) . "\n";
}
