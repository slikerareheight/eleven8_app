<?php
require_once __DIR__.'/bootstrap.php'; require_post();
$id=$_SESSION['user_id']??null;
if($id){$pdo->prepare('INSERT INTO activity_logs(user_id,event_type,details,ip_address) VALUES(?,?,?,?)')->execute([(int)$id,'logout',json_encode([]),$_SERVER['REMOTE_ADDR']??null]);}
$_SESSION=[]; if(ini_get('session.use_cookies')){ $p=session_get_cookie_params(); setcookie(session_name(),'',['expires'=>time()-42000,'path'=>$p['path'],'domain'=>$p['domain'],'secure'=>$p['secure'],'httponly'=>$p['httponly'],'samesite'=>$p['samesite']??'Lax']); }
session_destroy(); json_response(['success'=>true,'message'=>'Logged out successfully.']);
