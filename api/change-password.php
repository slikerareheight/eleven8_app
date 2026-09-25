<?php
require_once __DIR__.'/bootstrap.php'; require_post();
if(empty($_SESSION['user_id'])) json_response(['success'=>false,'message'=>'Please log in first.'],401);
$data=json_input(); verify_csrf($data['csrf']??null);
$id=(int)$_SESSION['user_id']; $current=(string)($data['current_password']??''); $new=(string)($data['new_password']??'');
if(strlen($new)<8) json_response(['success'=>false,'message'=>'New password must contain at least 8 characters.'],422);
$stmt=$pdo->prepare('SELECT password_hash FROM users WHERE id=? AND is_active=1'); $stmt->execute([$id]); $u=$stmt->fetch();
if(!$u || !password_verify($current,$u['password_hash'])) json_response(['success'=>false,'message'=>'Current password is incorrect.'],401);
$stmt=$pdo->prepare('UPDATE users SET password_hash=? WHERE id=?'); $stmt->execute([password_hash($new,PASSWORD_DEFAULT),$id]);
$pdo->prepare('INSERT INTO activity_logs(user_id,event_type,details,ip_address) VALUES(?,?,?,?)')->execute([$id,'password_change',json_encode(['result'=>'success']),$_SERVER['REMOTE_ADDR']??null]);
json_response(['success'=>true,'message'=>'Password changed successfully.']);
