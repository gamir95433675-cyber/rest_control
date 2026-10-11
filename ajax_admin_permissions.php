<?php
require_once 'config.php';requirePost();requireRole('admin');
$sup=trim((string)($_POST['supervisor']??''));
if($sup==='')jsonResponse(['success'=>false,'message'=>'سرپرست انتخاب نشده است.']);
$fields=['can_approve','can_reject','can_force_end','can_manual_rest','can_chat','can_view_other_teams'];
$vals=[];foreach($fields as $f)$vals[$f]=(isset($_POST[$f])&&($_POST[$f]==='1'||$_POST[$f]==='true'))?1:0;
$sql="INSERT INTO supervisor_permissions(supervisor_username,can_approve,can_reject,can_force_end,can_manual_rest,can_chat,can_view_other_teams) VALUES(?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE can_approve=VALUES(can_approve),can_reject=VALUES(can_reject),can_force_end=VALUES(can_force_end),can_manual_rest=VALUES(can_manual_rest),can_chat=VALUES(can_chat),can_view_other_teams=VALUES(can_view_other_teams)";
$st=$pdo->prepare($sql);$st->execute(array_merge([$sup],array_values($vals)));
jsonResponse(['success'=>true,'message'=>'دسترسی‌ها ذخیره شد.']);
