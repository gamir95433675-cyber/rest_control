<?php
require_once 'config.php'; requirePost();
$username=trim((string)($_POST['username']??''));$password=(string)($_POST['password']??'');
if($username===''||$password==='')jsonResponse(['success'=>false,'message'=>'نام کاربری و رمز عبور را وارد کنید.'],400);
$st=$pdo->prepare("SELECT username,password,supervisor_username,display_name FROM experts WHERE username=? LIMIT 1");$st->execute([$username]);$e=$st->fetch();
if(!$e||!password_verify($password,$e['password']))jsonResponse(['success'=>false,'message'=>'نام کاربری یا رمز عبور اشتباه است.'],401);
session_regenerate_id(true);$_SESSION['user']=['username'=>$e['username'],'role'=>'expert','supervisor'=>$e['supervisor_username'],'display_name'=>$e['display_name']];jsonResponse(['success'=>true,'redirect'=>'expert.php']);
