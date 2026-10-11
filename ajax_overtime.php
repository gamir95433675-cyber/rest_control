<?php
require_once 'config.php';
requirePost();
$user=requireRole('expert');
$id=postInt('request_id');
$stmt=$pdo->prepare("SELECT approved_at,duration_minutes FROM rest_requests WHERE id=? AND expert_username=? AND status='approved' LIMIT 1");
$stmt->execute([$id,$user['username']]); $r=$stmt->fetch();
if(!$r) jsonResponse(['success'=>false,'message'=>'استراحت فعال پیدا نشد.'],404);
$overtime=max(0,time()-strtotime($r['approved_at'])-((int)$r['duration_minutes']*60));
jsonResponse(['success'=>true,'overtime_seconds'=>$overtime]);
