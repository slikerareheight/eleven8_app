<?php
require_once __DIR__.'/bootstrap.php'; require_post();
if(empty($_SESSION['user_id'])) json_response(['success'=>false,'message'=>'Please log in first.'],401);
$data=json_input(); verify_csrf($data['csrf']??null);
$id=(int)$_SESSION['user_id'];
$first=trim((string)($data['first_name']??'')); $last=trim((string)($data['last_name']??''));
$email=strtolower(trim((string)($data['email']??''))); $phone=trim((string)($data['phone']??''));
if($first===''||$last===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||$phone==='') json_response(['success'=>false,'message'=>'Please provide valid profile details.'],422);
$stmt=$pdo->prepare('SELECT id FROM users WHERE email=? AND id<>? LIMIT 1'); $stmt->execute([$email,$id]);
if($stmt->fetch()) json_response(['success'=>false,'message'=>'That email address is already in use.'],409);
$stmt=$pdo->prepare('UPDATE users SET first_name=?,last_name=?,email=?,phone=? WHERE id=?');
$stmt->execute([$first,$last,$email,$phone,$id]);
$_SESSION['user_name']=$first.' '.$last;
$pdo->prepare('INSERT INTO activity_logs(user_id,event_type,details,ip_address) VALUES(?,?,?,?)')->execute([$id,'profile_update',json_encode(['email'=>$email]),$_SERVER['REMOTE_ADDR']??null]);
json_response(['success'=>true,'message'=>'Profile updated successfully.','user'=>['first_name'=>$first,'last_name'=>$last,'email'=>$email,'phone'=>$phone]]);
