<?php
$files = array_merge(
    glob(__DIR__ . '/../application/models/*.php'),
    glob(__DIR__ . '/../application/controllers/*.php')
);

echo "=== ACCURATE N+1 QUERY DETECTOR ===\n\n";

foreach ($files as $file) {
    $code = file_get_contents($file);
    $tokens = token_get_all($code);
    $count = count($tokens);
    $basename = basename($file);

    $loopStack = []; // stores stack of loop depths with their start lines
    $depth = 0;

    for ($i = 0; $i < $count; $i++) {
        $tok = $tokens[$i];

        if (is_array($tok)) {
            $id = $tok[0];
            $text = $tok[1];
            $line = $tok[2];

            if ($id === T_FOREACH || $id === T_FOR || $id === T_WHILE) {
                // look ahead to opening parenthesis and matching closing paren/brace
                $loopStack[] = ['type' => $text, 'line' => $line, 'depth' => $depth];
            } elseif (!empty($loopStack)) {
                // Check if we are inside a loop and calling database / model methods
                if ($id === T_VARIABLE && $text === '$this') {
                    // Check next tokens
                    $k = $i + 1;
                    while ($k < $count && is_array($tokens[$k]) && $tokens[$k][0] === T_WHITESPACE) $k++;
                    if ($k < $count && is_array($tokens[$k]) && $tokens[$k][0] === T_OBJECT_OPERATOR) {
                        $k++;
                        while ($k < $count && is_array($tokens[$k]) && $tokens[$k][0] === T_WHITESPACE) $k++;
                        if ($k < $count && is_array($tokens[$k])) {
                            $member = $tokens[$k][1];
                            if ($member === 'db' || substr($member, -6) === '_model' || $member === 'User_model') {
                                // Find method call
                                $m = $k + 1;
                                while ($m < $count && is_array($tokens[$m]) && $tokens[$m][0] === T_WHITESPACE) $m++;
                                if ($m < $count && is_array($tokens[$m]) && $tokens[$m][0] === T_OBJECT_OPERATOR) {
                                    $m++;
                                    while ($m < $count && is_array($tokens[$m]) && $tokens[$m][0] === T_WHITESPACE) $m++;
                                    $method = is_array($tokens[$m]) ? $tokens[$m][1] : '';
                                    $curLoop = end($loopStack);
                                    echo "File: {$basename} (Line {$line})\n";
                                    echo "  Inside {$curLoop['type']} loop (Line {$curLoop['line']}): \$this->{$member}->{$method}()\n\n";
                                }
                            }
                        }
                    }
                }
            }
        } else {
            if ($tok === '{') {
                $depth++;
            } elseif ($tok === '}') {
                $depth--;
                // Check if a loop closed
                if (!empty($loopStack)) {
                    $curLoop = end($loopStack);
                    if ($depth <= $curLoop['depth']) {
                        array_pop($loopStack);
                    }
                }
            }
        }
    }
}
