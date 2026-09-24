<?php
declare(strict_types=1);
return [
    'host' => getenv('ELEVEN8_DB_HOST') ?: '127.0.0.1',
    'name' => getenv('ELEVEN8_DB_NAME') ?: 'eleven8',
    'user' => getenv('ELEVEN8_DB_USER') ?: 'root',
    'pass' => getenv('ELEVEN8_DB_PASS') ?: '',
    'charset' => 'utf8mb4',
];
