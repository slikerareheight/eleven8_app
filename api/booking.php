<?php
require_once __DIR__.'/bootstrap.php'; require_post();
$data=json_input(); verify_csrf($data['csrf']??null);
$name=trim((string)($data['name']??'')); $email=strtolower(trim((string)($data['email']??''))); $phone=trim((string)($data['phone']??'')); $service=trim((string)($data['service']??'')); $date=trim((string)($data['date']??'')); $package=trim((string)($data['package']??'')); $location=trim((string)($data['location']??'')); $details=trim((string)($data['details']??'')); $amount=(float)($data['amount']??0);
if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||$phone===''||$service===''||$date===''||$package===''||$amount<100) json_response(['success'=>false,'message'=>'Please complete all required booking details.'],422);
$stmt=$pdo->prepare('INSERT INTO bookings(user_id,name,email,phone,service,booking_date,package,location,details,amount,status) VALUES(?,?,?,?,?,?,?,?,?,?,?)');
$stmt->execute([$_SESSION['user_id']??null,$name,$email,$phone,$service,$date,$package,$location,$details,$amount,'pending']);
$id=(int)$pdo->lastInsertId(); json_response(['success'=>true,'booking_id'=>$id,'message'=>'Booking saved.']);
