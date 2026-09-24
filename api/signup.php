<?php
require_once __DIR__.'/bootstrap.php'; require_post();
$data=json_input(); verify_csrf($data['csrf']??null);
$first=trim((string)($data['first_name']??'')); $last=trim((string)($data['last_name']??''));
$email=strtolower(trim((string)($data['email']??''))); $phone=trim((string)($data['phone']??'')); $password=(string)($data['password']??'');
if($first===''||$last===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($password)<8) json_response(['success'=>false,'message'=>'Please provide valid details.'],422);
$stmt=$pdo->prepare('SELECT id FROM users WHERE email=? LIMIT 1'); $stmt->execute([$email]);
if($stmt->fetch()) json_response(['success'=>false,'message'=>'An account with this email already exists.'],409);
$hash=password_hash($password,PASSWORD_DEFAULT);
$stmt=$pdo->prepare('INSERT INTO users(first_name,last_name,email,phone,password_hash) VALUES(?,?,?,?,?)');
$stmt->execute([$first,$last,$email,$phone,$hash]);
unset($_SESSION['csrf']); json_response(['success'=>true,'message'=>'Account created successfully.']);
