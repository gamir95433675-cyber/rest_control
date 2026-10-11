<?php
require_once 'config.php';
requirePost();
$user=requireRole('admin','supervisor');
$after=max(0,postInt('after_id',0));
if($user['role']==='supervisor'){
 $stmt=$pdo->prepare("SELECT id,expert_username,request_type,created_at FROM rest_requests WHERE supervisor_username=? AND status='pending' AND id>? ORDER BY id ASC");
 $stmt->execute([$user['username'],$after]);
}else{
 $stmt=$pdo->prepare("SELECT id,expert_username,supervisor_username,request_type,created_at FROM rest_requests WHERE status='pending' AND id>? ORDER BY id ASC");
 $stmt->execute([$after]);
}
jsonResponse(['success'=>true,'requests'=>$stmt->fetchAll()]);
