<?php
declare(strict_types=1);
require_once __DIR__ . '/api/bootstrap.php';
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="description" content="Book ELEVEN8 photography, videography and creative production services.">
<title>Booking | ELEVEN8 Cinematic</title><link rel="stylesheet" href="css/styles.css"></head>
<body>
<header class="nav"><div class="container nav-inner">
<a class="brand" href="index.html"><img src="images/logo.png" alt="ELEVEN8 logo"></a>
<nav class="nav-links"><a href="index.html">Home</a><a href="services.html">Services</a><a href="portfolio.html">Portfolio</a><a href="about.html">About</a><a href="packages.html">Packages</a><a class="active" href="booking.php">Booking</a><a href="contact.html">Contact</a></nav>
<div class="nav-actions"><a class="btn btn-outline" href="login.php">Login</a><a class="btn btn-gold" href="signup.php">Sign Up</a><button class="menu" aria-label="Open menu">☰</button></div>
</div></header>
<main>
<section class="site-slider" aria-label="ELEVEN8 featured work"><div class="slider-track">
<div class="slide active"><img src="images/SLIDER 2.png" alt="ELEVEN8 creative production"><div class="slide-overlay"></div><div class="slide-content"><div class="eyebrow">BOOKING</div><h1>Reserve your <span class="gold">date.</span></h1><p>Choose your service and package, submit your details, then send your payment request to ELEVEN8 on WhatsApp.</p><a class="btn btn-gold" href="#booking-form">Start Booking →</a></div></div>
<div class="slide"><img src="images/slider 1.png" alt="ELEVEN8 event coverage"><div class="slide-overlay"></div><div class="slide-content"><div class="eyebrow">Events & Celebrations</div><h2>Moments captured with <span class="gold">purpose.</span></h2><p>Professional coverage that preserves the atmosphere and details.</p></div></div>
<div class="slide"><img src="images/WEDDINGS.jpg" alt="ELEVEN8 wedding photography"><div class="slide-overlay"></div><div class="slide-content"><div class="eyebrow">Wedding Stories</div><h2>Your day. Your story. <span class="gold">Beautifully preserved.</span></h2><p>Every important frame matters.</p></div></div>
<div class="slide"><img src="images/PORTRAIT.jpg" alt="ELEVEN8 portrait photography"><div class="slide-overlay"></div><div class="slide-content"><div class="eyebrow">Portraits</div><h2>People. Personality. <span class="gold">Presence.</span></h2><p>Portrait sessions designed around authentic expression.</p></div></div>
</div><button class="slider-control prev" type="button" aria-label="Previous slide">‹</button><button class="slider-control next" type="button" aria-label="Next slide">›</button><div class="slider-dots" aria-label="Slider navigation"></div><div class="slider-progress"></div></section>
<section class="section"><div class="container split">
<div><div class="eyebrow">Booking Request</div><h2>Book your session.</h2><p style="color:var(--muted)">Your booking details are securely recorded in the ELEVEN8 database. After submission, WhatsApp opens with your booking reference so the team can confirm payment arrangements.</p>
<div class="notice"><strong>Payment:</strong> ELEVEN8 will provide the payment instructions through WhatsApp after your booking is recorded. Do not send card details through WhatsApp or this website.</div>
<div class="notice" style="margin-top:14px">Passwords and payment/card information are never written to the local activity log.</div>
</div>
<form class="form" id="bookingForm" data-whatsapp="2348120167383">
<div class="form-grid">
<div class="field"><label>Name</label><input name="Name" autocomplete="name" required></div>
<div class="field"><label>Email</label><input type="email" name="Email" autocomplete="email" required></div>
<div class="field"><label>Phone</label><input name="Phone" autocomplete="tel" required></div>
<div class="field"><label>Service</label><select name="Service" required><option value="">Select service</option><option>Wedding Photography</option><option>Wedding Videography</option><option>Portrait Session</option><option>Corporate Coverage</option><option>Commercial Content</option><option>Creative Production</option></select></div>
<div class="field"><label>Preferred Date</label><input type="date" name="Date" required></div>
<div class="field"><label>Package</label><select id="bookingPackage" name="Package" required><option value="">Select package</option><option value="Silver" data-amount="150000">Silver — ₦150,000</option><option value="Gold" data-amount="350000">Gold — ₦350,000</option><option value="Platinum" data-amount="700000">Platinum — ₦700,000</option><option value="Custom Quote" data-amount="0">Custom Quote</option></select></div>
<div class="field" id="customAmountField" hidden><label>Custom Booking Amount (₦)</label><input id="customAmount" name="Custom Amount" type="number" min="100" step="100" placeholder="Enter confirmed amount"></div>
<div class="field"><label>Location</label><input name="Location"></div>
<div class="field full"><label>Project Details</label><textarea name="Details" placeholder="Tell us about the event or project..."></textarea></div>
<div class="field full"><div class="payment-summary"><span>Booking amount</span><strong id="bookingAmount">Select a package</strong></div></div>
<div class="field full"><button class="btn btn-gold" type="submit" style="width:100%">Submit Booking & Continue on WhatsApp →</button></div>
<div class="field full"><p id="bookingStatus" class="form-status" aria-live="polite"></p></div>
</div></form></div></section>
</main>
<footer class="footer"><div class="container"><div class="copyright">© 2026 ELEVEN8 Cinematic. All rights reserved.</div></div></footer>
<script src="js/main.js"></script></body></html>