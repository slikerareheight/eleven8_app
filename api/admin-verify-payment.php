<?php
require_once __DIR__.'/bootstrap.php';
require_post();
require_admin();

$data=json_input();
verify_csrf($data['csrf']??null);

$bookingId=(int)($data['booking_id']??0);
$reference=trim((string)($data['reference']??''));

if($bookingId<1) json_response(['success'=>false,'message'=>'Invalid booking ID.'],422);

$stmt=$pdo->prepare('SELECT id,amount,status,payment_reference FROM bookings WHERE id=? LIMIT 1');
$stmt->execute([$bookingId]);
$booking=$stmt->fetch();

if(!$booking) json_response(['success'=>false,'message'=>'Booking not found.'],404);
if($reference==='') $reference=trim((string)($booking['payment_reference']??''));
if($reference==='') json_response(['success'=>false,'message'=>'Enter the Paystack transaction reference.'],422);

$secrets=require ELEVEN8_CONFIG.'/secrets.php';
$secret=trim((string)($secrets['paystack_secret_key']??''));
if($secret==='') json_response(['success'=>false,'message'=>'Paystack server secret is not configured.'],500);

$ch=curl_init('https://api.paystack.co/transaction/verify/'.rawurlencode($reference));
curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$secret,'Cache-Control: no-cache'],
    CURLOPT_TIMEOUT=>20,
]);
$raw=curl_exec($ch);
$http=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);
$error=curl_error($ch);
curl_close($ch);

if($raw===false || $error!=='') json_response(['success'=>false,'message'=>'Could not contact Paystack.'],502);

$result=json_decode($raw,true);
if($http<200 || $http>=300 || !is_array($result) || empty($result['status'])) {
    json_response(['success'=>false,'message'=>'Paystack verification failed.'],502);
}

$tx=$result['data']??[];
$paidStatus=($result['status']===true && ($tx['status']??'')==='success');
$paidAmount=(float)($tx['amount']??0)/100;
$expectedAmount=(float)$booking['amount'];
$currency=(string)($tx['currency']??'');
$txReference=(string)($tx['reference']??'');

if(!$paidStatus || $txReference!==$reference || $currency!=='NGN' || abs($paidAmount-$expectedAmount)>0.01) {
    json_response([
        'success'=>false,
        'message'=>'Payment could not be verified against the booking amount.',
        'status'=>$tx['status']??'unknown',
        'paid_amount'=>$paidAmount,
        'booking_amount'=>$expectedAmount
    ],422);
}

$update=$pdo->prepare('UPDATE bookings SET payment_reference=?,status=?,paid_at=NOW(),paystack_transaction_id=?,payment_channel=?,payment_payload=? WHERE id=?');
$update->execute([
    $reference,'paid',(int)($tx['id']??0),(string)($tx['channel']??''),
    json_encode($tx,JSON_UNESCAPED_SLASHES),$bookingId
]);

json_response([
    'success'=>true,
    'message'=>'Payment verified successfully.',
    'booking_id'=>$bookingId,
    'reference'=>$reference,
    'amount'=>$paidAmount,
    'channel'=>(string)($tx['channel']??'')
]);
