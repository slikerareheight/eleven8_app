<?php
require_once __DIR__.'/bootstrap.php'; require_post();
$data=json_input(); verify_csrf($data['csrf']??null);
$id=(int)($data['booking_id']??0); $reference=trim((string)($data['reference']??''));
if($id<1||$reference==='') json_response(['success'=>false,'message'=>'Invalid payment reference.'],422);
$stmt=$pdo->prepare('UPDATE bookings SET payment_reference=?,status=? WHERE id=?'); $stmt->execute([$reference,'payment_initiated',$id]);
json_response(['success'=>true]);
