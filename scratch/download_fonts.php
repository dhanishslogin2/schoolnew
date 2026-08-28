<?php
$fonts_dir = __DIR__ . '/../assets/fonts';
if (!is_dir($fonts_dir)) {
    mkdir($fonts_dir, 0777, true);
}

// Download Material Symbols Outlined CSS & woff2 font
$mat_css_url = 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap';
$ch = curl_init($mat_css_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
$mat_css = curl_exec($ch);
curl_close($ch);

if (preg_match('/src:\s*url\((https:\/\/[^)]+\.woff2)\)/', $mat_css, $m)) {
    $font_url = $m[1];
    $ch = curl_init($font_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $font_bin = curl_exec($ch);
    curl_close($ch);
    file_put_contents($fonts_dir . '/material-symbols.woff2', $font_bin);
    $mat_css_local = "/* Material Symbols Local */\n@font-face {\n  font-family: 'Material Symbols Outlined';\n  font-style: normal;\n  font-weight: 100 700;\n  font-display: swap;\n  src: url('material-symbols.woff2') format('woff2');\n}\n.material-symbols-outlined {\n  font-family: 'Material Symbols Outlined';\n  font-weight: normal;\n  font-style: normal;\n  font-size: 24px;\n  line-height: 1;\n  letter-spacing: normal;\n  text-transform: none;\n  display: inline-block;\n  white-space: nowrap;\n  word-wrap: normal;\n  direction: ltr;\n  -webkit-font-feature-settings: 'liga';\n  -webkit-font-smoothing: antialiased;\n}\n";
    file_put_contents($fonts_dir . '/material-symbols.css', $mat_css_local);
    echo "Saved Material Symbols locally.\n";
}

// Download Inter CSS & woff2
$inter_css_url = 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap';
$ch = curl_init($inter_css_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
$inter_css = curl_exec($ch);
curl_close($ch);

// Replace URLs with local woff2 files
preg_match_all('/src:\s*url\((https:\/\/[^)]+\.woff2)\)\s*format\(\'woff2\'\);/', $inter_css, $matches);
if (!empty($matches[1])) {
    foreach ($matches[1] as $idx => $url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $font_bin = curl_exec($ch);
        curl_close($ch);
        $local_filename = "inter-font-$idx.woff2";
        file_put_contents($fonts_dir . '/' . $local_filename, $font_bin);
        $inter_css = str_replace($url, $local_filename, $inter_css);
    }
    file_put_contents($fonts_dir . '/inter.css', $inter_css);
    echo "Saved Inter font locally (" . count($matches[1]) . " slices).\n";
}
