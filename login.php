<?php
declare(strict_types=1);
require_once __DIR__ . '/api/bootstrap.php';
if (!empty($_SESSION['user_id'])) {
    header('Location: ' . (($_SESSION['user_role'] ?? 'client') === 'admin' ? 'admin/index.php' : 'dashboard.php'));
    exit;
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login | ELEVEN8 Cinematic</title><link rel="stylesheet" href="css/styles.css"></head><body>
<header class="nav"><div class="container nav-inner"><a class="brand" href="index.html"><img src="images/logo.png" alt="ELEVEN8 logo"></a><nav class="nav-links"><a href="index.html">Home</a><a href="services.html">Services</a><a href="portfolio.html">Portfolio</a><a href="booking.php">Booking</a><a href="contact.html">Contact</a><a class="active" href="login.php">Login</a><a href="signup.php">Sign Up</a></nav><div class="nav-actions"><a class="btn btn-gold" href="booking.php">Book Now</a><button class="menu" aria-label="Open menu">☰</button></div></div></header>
<main><section class="page-hero"><div class="container"><div class="eyebrow">Client Account</div><h1>Welcome <span class="gold">back.</span></h1><p>Sign in to your ELEVEN8 client account.</p></div></section>
<section class="section"><div class="container" style="max-width:560px"><form class="form" id="loginForm">
<div class="field"><label>Email</label><input type="email" name="Email" autocomplete="email" required></div>
<div class="field" style="margin-top:16px"><label>Password</label><input type="password" name="Password" autocomplete="current-password" required></div>
<label style="display:flex;gap:8px;align-items:center;margin:18px 0;color:#bbb"><input type="checkbox" name="Remember"> Remember me</label>
<button class="btn btn-gold" type="submit" style="width:100%">Login</button><p id="loginStatus" class="form-status" aria-live="polite"></p>
<p style="text-align:center;color:var(--muted)">Forgot your password? <a class="gold" href="contact.html">Contact us</a></p><p style="text-align:center;color:var(--muted)">New to ELEVEN8? <a class="gold" href="signup.php">Create an account</a></p>
</form></div></section></main><footer class="footer"><div class="container"><div class="copyright">© 2026 ELEVEN8 Cinematic. All rights reserved.</div></div></footer><script src="js/main.js"></script></body></html>