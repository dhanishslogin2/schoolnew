<?php
$vendor_dir = __DIR__ . '/../assets/vendor';
if (!is_dir($vendor_dir)) {
    mkdir($vendor_dir, 0777, true);
}
if (!is_dir($vendor_dir . '/datatables')) {
    mkdir($vendor_dir . '/datatables', 0777, true);
}

$files = [
    $vendor_dir . '/jquery.min.js' => 'https://code.jquery.com/jquery-3.7.1.min.js',
    $vendor_dir . '/datatables/jquery.dataTables.min.js' => 'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js',
    $vendor_dir . '/datatables/dataTables.responsive.min.js' => 'https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js',
    $vendor_dir . '/datatables/jquery.dataTables.min.css' => 'https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css',
    $vendor_dir . '/datatables/responsive.dataTables.min.css' => 'https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css',
    $vendor_dir . '/tailwind.min.js' => 'https://cdn.tailwindcss.com?plugins=forms,container-queries'
];

echo "Downloading vendor assets locally for zero-latency local asset serving...\n";
foreach ($files as $dest => $url) {
    echo "Downloading " . basename($dest) . " from $url ... ";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code === 200 && strlen($data) > 0) {
        file_put_contents($dest, $data);
        echo "OK (" . number_format(strlen($data) / 1024, 2) . " KB)\n";
    } else {
        echo "FAILED (HTTP $code)\n";
    }
}
