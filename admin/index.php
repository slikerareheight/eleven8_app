<?php
require_once __DIR__.'/../api/bootstrap.php';
require_admin();
header('Content-Type: text/html; charset=utf-8');
$stmt=$pdo->query('SELECT id,name,email,phone,service,booking_date,package,amount,status,payment_reference,paid_at,created_at FROM bookings ORDER BY created_at DESC');
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
<div style="overflow:auto"><table style="width:100%;border-collapse:collapse;min-width:1000px"><thead><tr><th>ID</th><th>Customer</th><th>Service</th><th>Date</th><th>Package</th><th>Amount</th><th>Status</th><th>Reference</th><th>Created</th></tr></thead><tbody>
<?php foreach($bookings as $b): ?><tr><td><?=htmlspecialchars((string)$b['id'])?></td><td><?=htmlspecialchars($b['name'])?><br><small><?=htmlspecialchars($b['email'])?></small></td><td><?=htmlspecialchars($b['service'])?></td><td><?=htmlspecialchars($b['booking_date'])?></td><td><?=htmlspecialchars($b['package'])?></td><td>₦<?=number_format((float)$b['amount'],2)?></td><td><?=htmlspecialchars(strtoupper($b['status']))?></td><td><?=htmlspecialchars((string)($b['payment_reference']??''))?></td><td><?=htmlspecialchars($b['created_at'])?></td></tr><?php endforeach; ?></tbody></table></div>
</div></main></body></html>
