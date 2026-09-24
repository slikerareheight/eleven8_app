<?php
declare(strict_types=1);

$secure=!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off';
session_set_cookie_params([
    'lifetime'=>0,
    'path'=>'/',
    'secure'=>$secure,
    'httponly'=>true,
    'samesite'=>'Lax'
]);
session_name('eleven8_session');
session_start();
define('ELEVEN8_ROOT', dirname(__DIR__));
define('ELEVEN8_CONFIG', ELEVEN8_ROOT . '/config');
