<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/app.php';
$config = require ELEVEN8_CONFIG . '/database.php';
try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['name']};charset={$config['charset']}",
        $config['user'], $config['pass'],
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]
    );
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success'=>false,'message'=>'Database connection failed.']);
    exit;
}
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function json_input(): array {
    $data=json_decode(file_get_contents('php://input'),true);
    return is_array($data)?$data:$_POST;
}
function json_response(array $data,int $status=200): never {
    http_response_code($status); echo json_encode($data); exit;
}
function require_post(): void { if($_SERVER['REQUEST_METHOD']!=='POST') json_response(['success'=>false,'message'=>'POST required.'],405); }
function csrf_token(): string {
    if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function verify_csrf(?string $token): void {
    if(!$token || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'],$token)) json_response(['success'=>false,'message'=>'Invalid security token.'],419);
}
function require_admin(): void {
    if(empty($_SESSION['user_id']) || ($_SESSION['user_role']??'')!=='admin') {
        json_response(['success'=>false,'message'=>'Administrator access required.'],403);
    }
}
