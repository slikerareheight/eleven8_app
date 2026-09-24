<?php
require_once __DIR__.'/bootstrap.php'; require_post();
$data=json_input(); verify_csrf($data['csrf']??null);
$id=(int)($data['booking_id']??0); $reference=trim((string)($data['reference']??''));
if($id<1||$reference==='') json_response(['success'=>false,'message'=>'Invalid payment reference.'],422);

$stmt=$pdo->prepare('SELECT user_id,email,status FROM bookings WHERE id=? LIMIT 1');
$stmt->execute([$id]); $booking=$stmt->fetch();
if(!$booking) json_response(['success'=>false,'message'=>'Booking not found.'],404);

if(!empty($_SESSION['user_id']) && $booking['user_id']!==null && (int)$booking['user_id']!==(int)$_SESSION['user_id']) {
    json_response(['success'=>false,'message'=>'You are not authorized to update this booking.'],403);
}
if($booking['status']==='paid') json_response(['success'=>true,'message'=>'Booking is already paid.']);

$stmt=$pdo->prepare('UPDATE bookings SET payment_reference=?,status=? WHERE id=? AND status<>?');
$stmt->execute([$reference,'payment_initiated',$id,'paid']);
json_response(['success'=>true]);
