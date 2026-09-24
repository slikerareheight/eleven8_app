<?php
declare(strict_types=1);
session_name('eleven8_session');
session_start();
define('ELEVEN8_ROOT', dirname(__DIR__));
define('ELEVEN8_CONFIG', ELEVEN8_ROOT . '/config');
