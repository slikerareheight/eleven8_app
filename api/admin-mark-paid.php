<?php
require_once __DIR__.'/bootstrap.php';
require_post();
require_admin();

$data=json_input();
verify_csrf($data['csrf']??null);

$bookingId=(int)($data['booking_id']??0);
$method=trim((string)($data['payment_method']??''));
$reference=trim((string)($data['payment_reference']??''));
$note=trim((string)($data['payment_note']??''));

$methods=['Bank Transfer','Cash','POS','Other'];
if($bookingId<1 || !in_array($method,$methods,true)) {
    json_response(['success'=>false,'message'=>'Select a valid payment method.'],422);
}

$stmt=$pdo->prepare('SELECT id,status,amount FROM bookings WHERE id=? LIMIT 1');
$stmt->execute([$bookingId]);
$booking=$stmt->fetch();

if(!$booking) json_response(['success'=>false,'message'=>'Booking not found.'],404);
if($booking['status']==='paid') {
    json_response(['success'=>true,'message'=>'This booking is already marked as paid.']);
}

$details=[
    'method'=>$method,
    'reference'=>$reference,
    'note'=>$note,
    'verified_by'=>(int)$_SESSION['user_id'],
    'verified_at'=>date('Y-m-d H:i:s')
];

$update=$pdo->prepare('UPDATE bookings SET status=?,paid_at=NOW(),payment_reference=?,payment_channel=?,payment_payload=? WHERE id=? AND status<>?');
$update->execute([
    'paid',
    $reference!==''?$reference:null,
    $method,
    json_encode($details,JSON_UNESCAPED_SLASHES),
    $bookingId,
    'paid'
]);

if($update->rowCount()<1) json_response(['success'=>false,'message'=>'Payment could not be updated.'],409);

$log=$pdo->prepare('INSERT INTO activity_logs(user_id,event_type,details,ip_address) VALUES(?,?,?,?)');
$log->execute([
    (int)$_SESSION['user_id'],
    'manual_payment_verified',
    json_encode(['booking_id'=>$bookingId,'amount'=>(float)$booking['amount'],'method'=>$method,'reference'=>$reference],JSON_UNESCAPED_SLASHES),
    $_SERVER['REMOTE_ADDR']??null
]);

json_response([
    'success'=>true,
    'message'=>'Payment marked as verified successfully.',
    'booking_id'=>$bookingId,
    'amount'=>(float)$booking['amount'],
    'method'=>$method
]);
