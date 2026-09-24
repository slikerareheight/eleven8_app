<?php
require_once __DIR__.'/bootstrap.php'; require_post();
$data=json_input(); verify_csrf($data['csrf']??null);
$email=strtolower(trim((string)($data['email']??''))); $password=(string)($data['password']??'');
if(!filter_var($email,FILTER_VALIDATE_EMAIL)||$password==='') json_response(['success'=>false,'message'=>'Enter a valid email and password.'],422);

$stmt=$pdo->prepare('SELECT id,first_name,last_name,email,password_hash,role FROM users WHERE email=? AND is_active=1 LIMIT 1');
$stmt->execute([$email]); $user=$stmt->fetch();
if(!$user || $user['role']!=='admin' || !password_verify($password,$user['password_hash'])) json_response(['success'=>false,'message'=>'Invalid administrator credentials.'],401);

session_regenerate_id(true);
$_SESSION['user_id']=(int)$user['id'];
$_SESSION['user_name']=$user['first_name'];
$_SESSION['user_role']='admin';
$_SESSION['csrf']=bin2hex(random_bytes(32));
$log=$pdo->prepare('INSERT INTO activity_logs(user_id,event_type,details,ip_address) VALUES(?,?,?,?)');
$log->execute([(int)$user['id'],'admin_login',json_encode(['email'=>$user['email']]),$_SERVER['REMOTE_ADDR']??null]);
json_response(['success'=>true,'message'=>'Administrator login successful.']);
