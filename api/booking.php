<?php
require_once __DIR__.'/bootstrap.php'; require_post();
$data=json_input(); verify_csrf($data['csrf']??null);

$name=trim((string)($data['name']??''));
$email=strtolower(trim((string)($data['email']??'')));
$phone=trim((string)($data['phone']??''));
$service=trim((string)($data['service']??''));
$date=trim((string)($data['date']??''));
$package=trim((string)($data['package']??''));
$location=trim((string)($data['location']??''));
$details=trim((string)($data['details']??''));
$customAmount=trim((string)($data['custom_amount']??''));

$services=['Wedding Photography','Wedding Videography','Portrait Session','Corporate Coverage','Commercial Content','Creative Production'];
$packages=['Silver'=>150000.00,'Gold'=>350000.00,'Platinum'=>700000.00,'Custom Quote'=>null];

$dt=DateTime::createFromFormat('Y-m-d',$date);
if($name==='' || !filter_var($email,FILTER_VALIDATE_EMAIL) || $phone==='' || !in_array($service,$services,true) || !$dt || $dt->format('Y-m-d')!==$date || $package==='' || !array_key_exists($package,$packages)) {
    json_response(['success'=>false,'message'=>'Please complete all required booking details.'],422);
}

$amount=$packages[$package];

if($package==='Custom Quote') {
    // Use the dedicated custom amount field sent by the booking form.
    $amount=(float)$customAmount;
    if($amount<100) {
        json_response(['success'=>false,'message'=>'Enter a valid confirmed custom booking amount.'],422);
    }
}

$userId=!empty($_SESSION['user_id'])?(int)$_SESSION['user_id']:null;
$stmt=$pdo->prepare('INSERT INTO bookings(user_id,name,email,phone,service,booking_date,package,location,details,amount,status) VALUES(?,?,?,?,?,?,?,?,?,?,?)');
$stmt->execute([$userId,$name,$email,$phone,$service,$date,$package,$location,$details,$amount,'pending']);
$id=(int)$pdo->lastInsertId();

json_response(['success'=>true,'booking_id'=>$id,'amount'=>$amount,'message'=>'Booking saved.']);
