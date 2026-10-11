<?php
require_once 'config.php'; requirePost(); $user=requireRole('admin','supervisor','expert');$id=postInt('request_id');if($id<1)jsonResponse(['success'=>false,'message'=>'شناسه نامعتبر است.'],400);
$st=$pdo->prepare("SELECT * FROM rest_requests WHERE id=? AND status='approved' LIMIT 1");$st->execute([$id]);$r=$st->fetch();if(!$r)jsonResponse(['success'=>false,'message'=>'استراحت فعال پیدا نشد.'],404);
if($user['role']==='expert'&&$r['expert_username']!==$user['username'])jsonResponse(['success'=>false,'message'=>'دسترسی غیرمجاز'],403);
if($user['role']==='supervisor'&&$r['supervisor_username']!==$user['username'])jsonResponse(['success'=>false,'message'=>'دسترسی غیرمجاز'],403);
if($user['role']==='supervisor'&&!hasPermission('can_force_end'))jsonResponse(['success'=>false,'message'=>'دسترسی پایان دستی توسط مدیر غیرفعال شده است.'],403);
$elapsed=max(0,time()-strtotime($r['approved_at']));$allowed=max(0,(int)$r['duration_minutes']*60);$over=max(0,$elapsed-$allowed);$status=$over>0?'overtime':'completed';
$st=$pdo->prepare("UPDATE rest_requests SET status=?,completed_at=NOW(),overtime_seconds=? WHERE id=? AND status='approved'");$st->execute([$status,$over,$id]);
if($st->rowCount()===1&&$over>0){$msg='استراحت کارشناس '.$r['expert_username'].' از زمان مجاز عبور کرده است.';$st=$pdo->prepare("INSERT INTO overtime_alerts(expert_username,supervisor_username,rest_request_id,overtime_seconds,alert_date,message,created_at) VALUES(?,?,?,?,CURDATE(),?,NOW()) ON DUPLICATE KEY UPDATE overtime_seconds=VALUES(overtime_seconds)");$st->execute([$r['expert_username'],$r['supervisor_username'],$id,$over,$msg]);}
jsonResponse(['success'=>true,'status'=>$status,'overtime_seconds'=>$over]);
