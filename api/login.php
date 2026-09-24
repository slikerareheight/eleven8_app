<?php
require_once __DIR__.'/bootstrap.php'; require_post();
$data=json_input(); verify_csrf($data['csrf']??null);
$email=strtolower(trim((string)($data['email']??''))); $password=(string)($data['password']??'');
$stmt=$pdo->prepare('SELECT id,first_name,last_name,email,password_hash,role FROM users WHERE email=? AND is_active=1 LIMIT 1'); $stmt->execute([$email]); $user=$stmt->fetch();
if(!$user || !password_verify($password,$user['password_hash'])) json_response(['success'=>false,'message'=>'Invalid email or password.'],401);
session_regenerate_id(true); $_SESSION['user_id']=(int)$user['id']; $_SESSION['user_name']=$user['first_name'].' '.$user['last_name'];
$pdo->prepare('INSERT INTO activity_logs(user_id,event_type,details,ip_address) VALUES(?,?,?,?)')->execute([$user['id'],'login',json_encode(['email'=>$email]),$_SERVER['REMOTE_ADDR']??null]);
json_response(['success'=>true,'message'=>'Login successful.','user'=>['first_name'=>$user['first_name'],'last_name'=>$user['last_name'],'email'=>$user['email']]]);
