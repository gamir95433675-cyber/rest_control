<?php
require_once 'config.php'; requirePost(); $user=requirePermission('can_approve');
$id=postInt('request_id');$duration=clampDuration(postInt('duration',10));if($id<1)jsonResponse(['success'=>false,'message'=>'شناسه نامعتبر است.'],400);
$sql="UPDATE rest_requests SET status='approved',approved_at=NOW(),duration_minutes=?,completed_at=NULL,overtime_seconds=0 WHERE id=? AND status='pending'";$params=[$duration,$id];
if($user['role']==='supervisor'){$sql.=" AND supervisor_username=?";$params[]=$user['username'];}
$pdo->beginTransaction();try{$st=$pdo->prepare($sql);$st->execute($params);if($st->rowCount()!==1){$pdo->rollBack();jsonResponse(['success'=>false,'message'=>'درخواست پیدا نشد یا قبلاً پردازش شده است.'],409);}
$st=$pdo->prepare("SELECT expert_username FROM rest_requests WHERE id=?");$st->execute([$id]);$expert=$st->fetchColumn();
$st=$pdo->prepare("UPDATE experts SET rest_login_count=CASE WHEN last_rest_login_date=CURDATE() THEN rest_login_count+1 ELSE 1 END,last_rest_login_date=CURDATE() WHERE username=?");$st->execute([$expert]);
$pdo->commit();jsonResponse(['success'=>true,'message'=>'استراحت تأیید و فعال شد.']);}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();jsonResponse(['success'=>false,'message'=>'خطا در تأیید استراحت.'],500);}
