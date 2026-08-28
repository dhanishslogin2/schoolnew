<?php
$urls = [
    'Tailwind Play CDN' => 'https://cdn.tailwindcss.com?plugins=forms,container-queries',
    'Google Fonts Material' => 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap',
    'Google Fonts Inter' => 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
    'DataTables CSS' => 'https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css',
    'DataTables Responsive CSS' => 'https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css',
    'jQuery 3.7.1 CDN' => 'https://code.jquery.com/jquery-3.7.1.min.js',
    'DataTables JS' => 'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js',
    'DataTables Responsive JS' => 'https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js'
];

printf("%-30s %-12s %-12s %-12s %-10s\n", "Resource", "Status", "DNS Time", "Total Time", "Size (KB)");
echo str_repeat("-", 80) . "\n";

foreach ($urls as $name => $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $res = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    printf("%-30s %-12d %-10.4fs %-10.4fs %-10.2f\n", 
        $name, 
        $info['http_code'] ?? 0, 
        $info['namelookup_time'] ?? 0, 
        $info['total_time'] ?? 0, 
        strlen($res) / 1024
    );
}
