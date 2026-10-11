<?php
require_once 'config.php'; requirePost(); $user=requirePermission('can_reject');$id=postInt('request_id');if($id<1)jsonResponse(['success'=>false,'message'=>'شناسه نامعتبر است.'],400);
$sql="UPDATE rest_requests SET status='rejected',completed_at=NOW() WHERE id=? AND status='pending'";$p=[$id];if($user['role']==='supervisor'){$sql.=" AND supervisor_username=?";$p[]=$user['username'];}$st=$pdo->prepare($sql);$st->execute($p);if($st->rowCount()!==1)jsonResponse(['success'=>false,'message'=>'درخواست پیدا نشد یا قبلاً پردازش شده است.'],409);jsonResponse(['success'=>true]);
