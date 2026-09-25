<?php
require_once __DIR__.'/../api/bootstrap.php';
require_admin();
header('Content-Type: text/html; charset=utf-8');

$stmt=$pdo->query('SELECT id,name,email,phone,service,booking_date,package,amount,status,payment_reference,paid_at,payment_channel,created_at FROM bookings ORDER BY created_at DESC');
$bookings=$stmt->fetchAll();
$total=(float)$pdo->query("SELECT COALESCE(SUM(amount),0) FROM bookings WHERE status='paid'")->fetchColumn();
$paid=(int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status='paid'")->fetchColumn();
$pending=(int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status<>'paid'")->fetchColumn();
$userCount=(int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$users=$pdo->query('SELECT id,first_name,last_name,email,phone,role,is_active,created_at FROM users ORDER BY created_at DESC')->fetchAll();
?><!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>ELEVEN8 Admin Dashboard</title><link rel="stylesheet" href="../css/styles.css"></head>
<body><header class="nav"><div class="container nav-inner"><a class="brand" href="../index.html"><img src="../images/logo.png" alt="ELEVEN8"></a><div class="nav-actions"><a class="btn btn-outline" href="../index.html">Website</a><a class="btn btn-gold" href="logout.php">Logout</a></div></div></header>
<main class="section"><div class="container">
<div class="eyebrow">ADMIN DASHBOARD</div><h1>Bookings & Payments</h1>
<div class="form-grid" style="margin:25px 0"><div class="notice"><strong><?=number_format($paid)?></strong><br>Paid bookings</div><div class="notice"><strong><?=number_format($pending)?></strong><br>Pending / unpaid</div><div class="notice"><strong><?=number_format($userCount)?></strong><br>Registered users</div><div class="notice"><strong>₦<?=number_format($total,2)?></strong><br>Verified revenue</div></div>

<div style="margin:40px 0">
<div class="eyebrow">REGISTERED USERS</div><h2>Client Accounts</h2>
<div style="overflow:auto"><table style="width:100%;border-collapse:collapse;min-width:900px">
<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Registered</th></tr></thead><tbody>
<?php if(!$users): ?><tr><td colspan="7" style="padding:25px;text-align:center">No users have registered yet.</td></tr>
<?php else: foreach($users as $u): ?><tr>
<td><?=htmlspecialchars((string)$u['id'])?></td>
<td><?=htmlspecialchars($u['first_name'].' '.$u['last_name'])?></td>
<td><?=htmlspecialchars($u['email'])?></td>
<td><?=htmlspecialchars($u['phone'])?></td>
<td><?=htmlspecialchars(strtoupper($u['role']))?></td>
<td><?=((int)$u['is_active']===1?'ACTIVE':'INACTIVE')?></td>
<td><?=htmlspecialchars($u['created_at'])?></td>
</tr><?php endforeach; endif; ?>
</tbody></table></div></div>

<div class="eyebrow">BOOKINGS & PAYMENT VERIFICATION</div><h2>Booking Records</h2>
<p class="muted">Enter the Paystack transaction reference supplied by the customer, then verify it against the booking amount. The booking is marked <strong>PAID</strong> only when Paystack confirms a successful NGN transaction for the exact amount.</p>
<div style="overflow:auto"><table style="width:100%;border-collapse:collapse;min-width:1450px">
<thead><tr><th>ID</th><th>Customer</th><th>Service</th><th>Date</th><th>Package</th><th>Amount</th><th>Status</th><th>Paystack Reference</th><th>Paid At</th><th>Action</th></tr></thead><tbody>
<?php if(!$bookings): ?><tr><td colspan="10" style="padding:25px;text-align:center">No bookings have been created yet.</td></tr>
<?php else: foreach($bookings as $b): ?>
<tr id="booking-row-<?=htmlspecialchars((string)$b['id'])?>">
<td><?=htmlspecialchars((string)$b['id'])?></td>
<td><?=htmlspecialchars($b['name'])?><br><small><?=htmlspecialchars($b['email'])?><br><?=htmlspecialchars($b['phone'])?></small></td>
<td><?=htmlspecialchars($b['service'])?></td>
<td><?=htmlspecialchars($b['booking_date'])?></td>
<td><?=htmlspecialchars($b['package'])?></td>
<td>₦<?=number_format((float)$b['amount'],2)?></td>
<td class="payment-status"><?=htmlspecialchars(strtoupper($b['status']))?><?php if(!empty($b['payment_channel'])): ?><br><small><?=htmlspecialchars($b['payment_channel'])?></small><?php endif; ?></td>
<td><input class="admin-payment-ref" id="ref-<?=htmlspecialchars((string)$b['id'])?>" type="text" value="<?=htmlspecialchars((string)($b['payment_reference']??''))?>" placeholder="e.g. 1234567890"></td>
<td class="paid-at"><?=htmlspecialchars((string)($b['paid_at']??''))?></td>
<td><?php if($b['status']==='paid'): ?><span class="notice" style="display:inline-block;padding:8px 12px">Verified</span><?php else: ?><button type="button" class="btn btn-gold verify-payment" data-booking-id="<?=htmlspecialchars((string)$b['id'])?>">Verify Payment</button><div class="form-status verify-status" id="status-<?=htmlspecialchars((string)$b['id'])?>" aria-live="polite"></div><?php endif; ?></td>
</tr>
<?php endforeach; endif; ?>
</tbody></table></div>
</div></main>
<script>
async function getCsrf(){
 const r=await fetch('../api/csrf.php',{credentials:'same-origin'});
 const d=await r.json();
 if(!r.ok||!d.csrf) throw new Error(d.message||'Could not obtain security token.');
 return d.csrf;
}
document.querySelectorAll('.verify-payment').forEach(button=>{
 button.addEventListener('click',async()=>{
  const id=button.dataset.bookingId;
  const input=document.getElementById('ref-'+id);
  const status=document.getElementById('status-'+id);
  const reference=input.value.trim();
  if(!reference){status.textContent='Enter the Paystack transaction reference first.';status.className='form-status error';input.focus();return;}
  button.disabled=true;
  button.textContent='Verifying...';
  status.textContent='';
  try{
   const csrf=await getCsrf();
   const r=await fetch('../api/admin-verify-payment.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    credentials:'same-origin',
    body:JSON.stringify({csrf,booking_id:Number(id),reference})
   });
   const d=await r.json();
   if(!r.ok||!d.success) throw new Error(d.message||'Payment verification failed.');
   status.textContent='Payment verified successfully.';
   status.className='form-status success';
   const row=document.getElementById('booking-row-'+id);
   row.querySelector('.payment-status').innerHTML='PAID<br><small>'+((d.channel||'').replace(/</g,'&lt;'))+'</small>';
   row.querySelector('.paid-at').textContent=new Date().toLocaleString();
   button.outerHTML='<span class="notice" style="display:inline-block;padding:8px 12px">Verified</span>';
   setTimeout(()=>location.reload(),900);
  }catch(err){
   status.textContent=err.message;
   status.className='form-status error';
   button.disabled=false;
   button.textContent='Verify Payment';
  }
 });
});
</script></body></html>
