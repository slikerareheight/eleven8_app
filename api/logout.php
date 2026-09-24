<?php
require_once __DIR__.'/bootstrap.php';
if(!empty($_SESSION['user_id'])) { $pdo->prepare('INSERT INTO activity_logs(user_id,event_type,details,ip_address) VALUES(?,?,?,?)')->execute([$_SESSION['user_id'],'logout','{}',$_SERVER['REMOTE_ADDR']??null]); }
$_SESSION=[]; session_destroy(); json_response(['success'=>true]);
