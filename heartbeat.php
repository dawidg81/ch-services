<?php
// heartbeat.php (debug-friendly version)

// Log everything we receive
$logFile = __DIR__ . "/heartbeat_log.txt";
$logData = date("c") . " - From " . $_SERVER['REMOTE_ADDR'] . "\n";
$logData .= "Request method: " . $_SERVER['REQUEST_METHOD'] . "\n";
$logData .= "Query string: " . $_SERVER['QUERY_STRING'] . "\n";
$logData .= "GET data: " . print_r($_GET, true) . "\n";
$logData .= "POST data: " . print_r($_POST, true) . "\n\n";
file_put_contents($logFile, $logData, FILE_APPEND);

// Merge GET and POST parameters (some servers may POST instead of GET)
$params = array_merge($_GET, $_POST);

// Required heartbeat params
$required = ['port', 'max', 'name', 'public', 'version', 'salt', 'users'];

// Validate
foreach ($required as $param) {
    if (!isset($params[$param])) {
        http_response_code(400);
        die("Missing parameter: $param");
    }
}

// Build server record
$server = [
    "ip"      => $_SERVER['REMOTE_ADDR'],
    "port"    => intval($params['port']),
    "max"     => intval($params['max']),
    "name"    => $params['name'],
    "public"  => $params['public'] === "True",
    "version" => intval($params['version']),
    "salt"    => $params['salt'],
    "users"   => intval($params['users']),
    "lastSeen"=> time()
];

// Load existing list
$file = __DIR__ . "/servers.json";
$list = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

// Use IP:port as key
$key = $server["ip"] . ":" . $server["port"];
$list[$key] = $server;

// Save
file_put_contents($file, json_encode($list, JSON_PRETTY_PRINT));

echo "OK";
