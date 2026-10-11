<?php
require_once 'config.php'; requirePost(); $user=requireRole('expert');
$type=trim((string)($_POST['request_type']??'normal')); if(!in_array($type,['normal','emergency'],true))$type='normal';
$expert=$user['username'];$sup=$user['supervisor'];
$st=$pdo->prepare("SELECT id FROM rest_requests WHERE expert_username=? AND status IN ('pending','approved') LIMIT 1");$st->execute([$expert]);
if($st->fetch()) jsonResponse(['success'=>false,'message'=>'برای شما یک درخواست یا استراحت فعال وجود دارد.'],409);
$st=$pdo->prepare("SELECT username FROM experts WHERE username=? AND supervisor_username=? LIMIT 1");$st->execute([$expert,$sup]);if(!$st->fetch())jsonResponse(['success'=>false,'message'=>'کارشناس معتبر نیست.'],403);
$st=$pdo->prepare("INSERT INTO rest_requests(expert_username,supervisor_username,status,request_type,reason,requested_at,created_at) VALUES(?,?, 'pending',?,NULL,?,NOW())");$st->execute([$expert,$sup,$type,date('Y-m-d H:i:s')]);
jsonResponse(['success'=>true,'id'=>(int)$pdo->lastInsertId(),'message'=>$type==='emergency'?'درخواست استراحت اضطراری ارسال شد.':'درخواست استراحت ارسال شد.']);
