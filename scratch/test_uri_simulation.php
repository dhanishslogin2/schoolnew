<?php
// Test URI detection simulation for both local and server environments

function simulate_ci_uri($server_env, $config_protocol = 'REQUEST_URI') {
    $_SERVER = $server_env;
    
    // Simulate URI::_parse_request_uri()
    $uri = parse_url('http://dummy' . $_SERVER['REQUEST_URI']);
    $query = isset($uri['query']) ? $uri['query'] : '';
    $uri_path = isset($uri['path']) ? $uri['path'] : '';

    if (isset($_SERVER['SCRIPT_NAME'][0])) {
        if (strpos($uri_path, $_SERVER['SCRIPT_NAME']) === 0) {
            $uri_path = (string) substr($uri_path, strlen($_SERVER['SCRIPT_NAME']));
        } elseif (strpos($uri_path, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
            $uri_path = (string) substr($uri_path, strlen(dirname($_SERVER['SCRIPT_NAME'])));
        }
    }

    if (trim($uri_path, '/') === '' && strncmp($query, '/', 1) === 0) {
        $query_parts = explode('?', $query, 2);
        $uri_path = $query_parts[0];
    }

    $uri_string = trim($uri_path, '/');
    return $uri_string;
}

// 1. Localhost /schoolnew/auth/login with mod_rewrite
$env_local = [
    'HTTP_HOST' => 'localhost',
    'SCRIPT_NAME' => '/schoolnew/index.php',
    'REQUEST_URI' => '/schoolnew/auth/login',
    'QUERY_STRING' => '',
];
echo "Localhost URI result: " . simulate_ci_uri($env_local) . " (Expected: auth/login)\n";

// 2. Server /schoolManagement/auth/login with index.php?/$1
$env_server_qs = [
    'HTTP_HOST' => 'dev.login2.in',
    'SCRIPT_NAME' => '/schoolManagement/index.php',
    'REQUEST_URI' => '/schoolManagement/auth/login',
    'QUERY_STRING' => '/auth/login',
];
echo "Server (index.php?/\$1) URI result: " . simulate_ci_uri($env_server_qs) . " (Expected: auth/login)\n";

// 3. Server with fastcgi / ProxyPassMatch where SCRIPT_NAME might be /index.php or /schoolManagement/index.php
$env_server_proxy = [
    'HTTP_HOST' => 'dev.login2.in',
    'SCRIPT_NAME' => '/schoolManagement/index.php',
    'REQUEST_URI' => '/schoolManagement/auth/login',
    'QUERY_STRING' => '/auth/login',
    'HTTP_X_FORWARDED_PROTO' => 'https',
];
echo "Server with Proxy URI result: " . simulate_ci_uri($env_server_proxy) . " (Expected: auth/login)\n";

// 4. Base URL detection test
function detect_base_url($server_env) {
    $_SERVER = $server_env;
    $is_https = (
        (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] == 1)) ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
        (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
        (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
    );
    $scheme = $is_https ? 'https' : 'http';
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    $script_dir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    return $scheme . '://' . $host . $script_dir;
}

echo "Local Base URL: " . detect_base_url($env_local) . " (Expected: http://localhost/schoolnew/)\n";
echo "Server Base URL: " . detect_base_url($env_server_proxy) . " (Expected: https://dev.login2.in/schoolManagement/)\n";
