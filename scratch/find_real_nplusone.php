<?php
$dirs = [
    __DIR__ . '/../application/models',
    __DIR__ . '/../application/controllers'
];

echo "=== PRECISE N+1 PARSER FOR MODELS & CONTROLLERS ===\n\n";

$found = [];

foreach ($dirs as $dir) {
    foreach (glob($dir . '/*.php') as $file) {
        $tokens = token_get_all(file_get_contents($file));
        $basename = basename($file);
        $loop_depth = 0;
        $loop_tokens = [T_FOR, T_FOREACH, T_WHILE, T_DO];
        $brace_levels = [];
        $current_loop_line = null;

        for ($i = 0; $i < count($tokens); $i++) {
            $token = $tokens[$i];

            if (is_array($token)) {
                if (in_array($token[0], $loop_tokens)) {
                    $loop_depth++;
                    $current_loop_line = $token[2];
                } elseif ($loop_depth > 0 && $token[0] === T_VARIABLE && $token[1] === '$this') {
                    // Check if following token is -> db -> (get|query|insert|update|select|where) or similar
                    $j = $i + 1;
                    while ($j < count($tokens) && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) $j++;
                    if ($j < count($tokens) && is_array($tokens[$j]) && $tokens[$j][0] === T_OBJECT_OPERATOR) {
                        $j++;
                        while ($j < count($tokens) && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) $j++;
                        if ($j < count($tokens) && is_array($tokens[$j])) {
                            $prop = $tokens[$j][1];
                            if ($prop === 'db' || strpos($prop, '_model') !== false || $prop === 'User_model') {
                                $found[] = [
                                    'file' => $basename,
                                    'line' => $token[2],
                                    'loop_line' => $current_loop_line,
                                    'call' => '$this->' . $prop
                                ];
                            }
                        }
                    }
                }
            } else {
                if ($token === '{') {
                    $brace_levels[] = $loop_depth;
                } elseif ($token === '}') {
                    if (!empty($brace_levels)) {
                        $last = array_pop($brace_levels);
                        if ($loop_depth > 0 && count($brace_levels) < $loop_depth) {
                            $loop_depth--;
                        }
                    }
                }
            }
        }
    }
}

foreach ($found as $item) {
    echo "File: {$item['file']} Line: {$item['line']} (Inside loop started at line {$item['loop_line']}) -> {$item['call']}\n";
}
echo "\nTotal detected: " . count($found) . "\n";
