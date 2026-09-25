<?php
require_once __DIR__.'/api/bootstrap.php';

if(empty($_SESSION['user_id'])){
    header('Location: login.html');
    exit;
}

$userId=(int)$_SESSION['user_id'];
$stmt=$pdo->prepare('SELECT id,first_name,last_name,email,phone,role,created_at FROM users WHERE id=? AND is_active=1 LIMIT 1');
$stmt->execute([$userId]);
$user=$stmt->fetch();

if(!$user){
    session_unset();
    session_destroy();
    header('Location: login.html');
    exit;
}

$stmt=$pdo->prepare('SELECT id,service,booking_date,package,amount,status,created_at FROM bookings WHERE user_id=? ORDER BY created_at DESC');
$stmt->execute([$userId]);
$bookings=$stmt->fetchAll();

$pending=0; $paid=0; $total=0.0;
foreach($bookings as $booking){
    if($booking['status']==='paid'){ $paid++; $total+=(float)$booking['amount']; }
    else { $pending++; }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Dashboard | ELEVEN8 Cinematic</title>
<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<header class="nav"><div class="container nav-inner">
<a class="brand" href="index.html"><img src="images/logo.png" alt="ELEVEN8 logo"></a>
<nav class="nav-links"><a href="index.html">Home</a><a href="services.html">Services</a><a href="portfolio.html">Portfolio</a><a href="booking.html">Booking</a><a class="active" href="dashboard.php">My Dashboard</a></nav>
<div class="nav-actions"><a class="btn btn-gold" href="booking.html">New Booking</a><a class="btn btn-outline" href="admin/logout.php">Logout</a></div>
</div></header>

<main>
<section class="page-hero"><div class="container"><div class="eyebrow">CLIENT DASHBOARD</div>
<h1>Welcome, <span class="gold"><?=htmlspecialchars($user['first_name'])?></span>.</h1>
<p>Manage your ELEVEN8 account and view your booking history.</p></div></section>

<section class="section"><div class="container">
<div class="form-grid" style="margin-bottom:30px">
<div class="notice"><strong><?=count($bookings)?></strong><br>Total bookings</div>
<div class="notice"><strong><?=number_format($pending)?></strong><br>Pending / unpaid</div>
<div class="notice"><strong><?=number_format($paid)?></strong><br>Paid bookings</div>
<div class="notice"><strong>₦<?=number_format($total,2)?></strong><br>Total paid</div>
</div>

<div class="split">
<div><div class="eyebrow">ACCOUNT INFORMATION</div><h2><?=htmlspecialchars($user['first_name'].' '.$user['last_name'])?></h2>
<p><strong>Email:</strong> <?=htmlspecialchars($user['email'])?></p>
<p><strong>Phone:</strong> <?=htmlspecialchars($user['phone'])?></p>
<p><strong>Account:</strong> <?=htmlspecialchars(ucfirst($user['role']))?></p>
<p><strong>Member since:</strong> <?=htmlspecialchars($user['created_at'])?></p></div>
<div><div class="eyebrow">QUICK ACTION</div><h2>Ready for your next project?</h2>
<p style="color:var(--muted)">Create a new booking and send your payment request directly to ELEVEN8 on WhatsApp.</p>
<a class="btn btn-gold" href="booking.html">Make a New Booking →</a></div>
</div>

<div style="margin-top:50px"><div class="eyebrow">BOOKING HISTORY</div><h2>Your bookings</h2>
<div style="overflow:auto"><table style="width:100%;border-collapse:collapse;min-width:900px">
<thead><tr><th>ID</th><th>Service</th><th>Date</th><th>Package</th><th>Amount</th><th>Status</th><th>Created</th></tr></thead>
<tbody>
<?php if(!$bookings): ?>
<tr><td colspan="7" style="padding:25px;text-align:center;color:var(--muted)">No bookings yet. <a class="gold" href="booking.html">Create your first booking</a>.</td></tr>
<?php else: foreach($bookings as $b): ?>
<tr><td>#<?=htmlspecialchars((string)$b['id'])?></td><td><?=htmlspecialchars($b['service'])?></td><td><?=htmlspecialchars($b['booking_date'])?></td><td><?=htmlspecialchars($b['package'])?></td><td>₦<?=number_format((float)$b['amount'],2)?></td><td><?=htmlspecialchars(strtoupper($b['status']))?></td><td><?=htmlspecialchars($b['created_at'])?></td></tr>
<?php endforeach; endif; ?>
</tbody></table></div></div>
</div></section>
</main>
<footer class="footer"><div class="container"><div class="copyright">© 2026 ELEVEN8 Cinematic. All rights reserved.</div></div></footer>
</body></html>