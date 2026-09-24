<?php
require_once __DIR__.'/../config/app.php';
if(($_SESSION['user_role']??'')==='admin'){header('Location: index.php');exit;}
?><!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>ELEVEN8 Admin Login</title><link rel="stylesheet" href="../css/styles.css"></head>
<body><main class="section"><div class="container" style="max-width:520px">
<div class="eyebrow">ELEVEN8 ADMIN</div><h1>Administrator Login</h1>
<form class="form" id="adminLoginForm">
<div class="field"><label>Email</label><input type="email" id="adminEmail" required autocomplete="username"></div>
<div class="field"><label>Password</label><input type="password" id="adminPassword" required autocomplete="current-password"></div>
<button class="btn btn-gold" type="submit">Sign In</button><p id="adminStatus" class="form-status" aria-live="polite"></p>
</form><p><a href="../index.html">← Back to website</a></p></div></main>
<script>
async function csrf(){const r=await fetch('../api/csrf.php',{credentials:'same-origin'});return (await r.json()).csrf}
document.getElementById('adminLoginForm').addEventListener('submit',async e=>{e.preventDefault();const s=document.getElementById('adminStatus');try{const token=await csrf();const r=await fetch('../api/admin-login.php',{method:'POST',headers:{'Content-Type':'application/json'},credentials:'same-origin',body:JSON.stringify({csrf:token,email:adminEmail.value,password:adminPassword.value})});const d=await r.json();if(!r.ok)throw new Error(d.message||'Login failed');s.textContent=d.message;s.className='form-status success';setTimeout(()=>location.href='index.php',500)}catch(err){s.textContent=err.message;s.className='form-status error'}});
</script></body></html>
