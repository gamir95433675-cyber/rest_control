<?php
require_once 'config.php';
requirePost();
$username=trim((string)($_POST['username']??''));
$password=(string)($_POST['password']??'');
if($username===''||$password==='') jsonResponse(['success'=>false,'message'=>'نام کاربری و رمز عبور را وارد کنید.'],400);
$stmt=$pdo->prepare("SELECT username,password,role,display_name FROM users WHERE username=? LIMIT 1");
$stmt->execute([$username]); $user=$stmt->fetch();
if(!$user || !password_verify($password,$user['password'])) jsonResponse(['success'=>false,'message'=>'نام کاربری یا رمز عبور اشتباه است.'],401);
session_regenerate_id(true);
$_SESSION['user']=['username'=>$user['username'],'role'=>$user['role'],'display_name'=>$user['display_name']];
jsonResponse(['success'=>true,'redirect'=>$user['role']==='admin'?'admin.php':'supervisor.php']);
