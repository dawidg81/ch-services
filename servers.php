<?php
// servers.php

$file = __DIR__ . "/servers.json";
$list = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

// Drop stale servers (older than 60s)
$now = time();
$list = array_filter($list, function($srv) use ($now) {
    return ($now - $srv["lastSeen"]) < 60 && $srv["public"];
});

// Output JSON (or XML if you want oldschool)
header("Content-Type: application/json");
echo json_encode(array_values($list), JSON_PRETTY_PRINT);
