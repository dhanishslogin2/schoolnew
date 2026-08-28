<?php
$models_dir = __DIR__ . '/../application/models';
$files = glob($models_dir . '/*.php');

echo "=== SCANNING MODELS FOR POTENTIAL N+1 PATTERNS ===\n\n";

foreach ($files as $file) {
    $content = file_get_contents($file);
    $basename = basename($file);
    $lines = explode("\n", $content);
    
    $in_loop = false;
    $loop_start_line = 0;
    $brace_depth = 0;
    $found_in_file = [];

    foreach ($lines as $i => $line) {
        $lineno = $i + 1;
        
        // Detect loop starts
        if (preg_match('/(foreach|while|for)\s*\(/i', $line)) {
            $in_loop = true;
            $loop_start_line = $lineno;
        }

        if ($in_loop) {
            // Check for db queries inside loop
            if (preg_match('/\$this->(db|load->model|.*_model)->(get|query|select|where|insert|update|delete|get_by)/i', $line)
                || preg_match('/\$this->db->/i', $line)) {
                $found_in_file[] = [
                    'line' => $lineno,
                    'loop_start' => $loop_start_line,
                    'content' => trim($line)
                ];
            }
        }

        // Simple heuristic for closing loop
        if ($in_loop && strpos($line, '}') !== false) {
            // we will keep it simple
        }
    }

    if (!empty($found_in_file)) {
        echo "File: $basename\n";
        foreach ($found_in_file as $item) {
            echo "  Line {$item['line']} (Inside loop started at {$item['loop_start']}): {$item['content']}\n";
        }
        echo "\n";
    }
}
