<?php

$cfg = [
    'admin_user' => getenv('INTM_ADMIN_USER') ?: 'admin',
    'admin_pass' => getenv('INTM_ADMIN_PASS') ?: 'intm@2025',
];

$local_path = __DIR__ . '/config.local.php';
if (is_file($local_path)) {
    $local = require $local_path;
    if (is_array($local)) {
        $cfg = array_merge($cfg, $local);
    }
}

return $cfg;

