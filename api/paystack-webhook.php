<?php
require_once __DIR__.'/bootstrap.php';

$secrets=require ELEVEN8_CONFIG.'/secrets.php';
$secret=trim((string)($secrets['paystack_secret_key']??''));
if($secret==='') { http_response_code(500); exit; }

$raw=file_get_contents('php://input') ?: '';
$signature=$_SERVER['HTTP_X_PAYSTACK_SIGNATURE']??'';
$expected=hash_hmac('sha512',$raw,$secret);
if($signature==='' || !hash_equals($expected,$signature)) { http_response_code(401); exit; }

$event=json_decode($raw,true);
if(!is_array($event)) { http_response_code(400); exit; }

if(($event['event']??'')!=='charge.success') { http_response_code(200); exit; }

$tx=$event['data']??[];
$reference=trim((string)($tx['reference']??''));
$amount=(float)($tx['amount']??0)/100;
$currency=(string)($tx['currency']??'');
if($reference==='' || $amount<=0 || $currency!=='NGN') { http_response_code(400); exit; }

$bookingId=0;
foreach(($tx['metadata']['custom_fields']??[]) as $field) {
    if(($field['variable_name']??'')==='booking_id') { $bookingId=(int)($field['value']??0); break; }
}
if($bookingId<1) { http_response_code(200); exit; }

$stmt=$pdo->prepare('SELECT id,amount,status FROM bookings WHERE id=? LIMIT 1');
$stmt->execute([$bookingId]);
$booking=$stmt->fetch();
if(!$booking) { http_response_code(200); exit; }

if(abs((float)$booking['amount']-$amount)>0.01) { http_response_code(200); exit; }

$update=$pdo->prepare('UPDATE bookings SET payment_reference=?,status=?,paid_at=NOW(),paystack_transaction_id=?,payment_channel=?,payment_payload=? WHERE id=? AND (status<>\'paid\' OR payment_reference IS NULL)');
$update->execute([
    $reference,'paid',(int)($tx['id']??0),(string)($tx['channel']??''),
    json_encode($tx,JSON_UNESCAPED_SLASHES),$bookingId
]);

http_response_code(200);
echo 'OK';
