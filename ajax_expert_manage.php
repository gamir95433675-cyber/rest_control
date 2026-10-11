<?php
require_once 'config.php'; requirePost(); $user=requireRole('admin','supervisor');
$action=trim((string)($_POST['action']??''));
if($action==='add'){
 $username=trim((string)($_POST['username']??''));$name=normalizeText((string)($_POST['display_name']??''),150);$pass=(string)($_POST['password']??'');$sup=trim((string)($_POST['supervisor']??''));
 if(!preg_match('/^[A-Za-z0-9._-]{3,100}$/',$username)||$name===''||strlen($pass)<4||$sup==='')jsonResponse(['success'=>false,'message'=>'اطلاعات کارشناس کامل یا معتبر نیست.'],400);
 if($user['role']==='supervisor' && $sup!==$user['username'])jsonResponse(['success'=>false,'message'=>'سرپرست فقط می‌تواند کارشناس را به تیم خودش اضافه کند.'],403);
 $st=$pdo->prepare("SELECT username FROM users WHERE username=? UNION SELECT username FROM experts WHERE username=?");$st->execute([$username,$username]);if($st->fetch())jsonResponse(['success'=>false,'message'=>'این نام کاربری قبلاً استفاده شده است.'],409);
 $st=$pdo->prepare("SELECT username FROM users WHERE username=? AND role='supervisor'");$st->execute([$sup]);if(!$st->fetch())jsonResponse(['success'=>false,'message'=>'سرپرست معتبر نیست.'],400);
 $st=$pdo->prepare("INSERT INTO experts(username,password,supervisor_username,display_name) VALUES(?,?,?,?)");$st->execute([$username,password_hash($pass,PASSWORD_DEFAULT),$sup,$name]);jsonResponse(['success'=>true,'message'=>'کارشناس با موفقیت اضافه شد.']);
}
if($action==='delete'){
 $username=trim((string)($_POST['username']??''));$st=$pdo->prepare("SELECT username,supervisor_username FROM experts WHERE username=?");$st->execute([$username]);$e=$st->fetch();if(!$e)jsonResponse(['success'=>false,'message'=>'کارشناس پیدا نشد.'],404);
 if($user['role']==='supervisor' && $e['supervisor_username']!==$user['username'])jsonResponse(['success'=>false,'message'=>'فقط کارشناس تیم خودتان قابل حذف است.'],403);
 try{$pdo->beginTransaction();
  $st=$pdo->prepare("SELECT DISTINCT conversation_id FROM chat_members WHERE username=? AND role='expert'");$st->execute([$username]);$cids=$st->fetchAll(PDO::FETCH_COLUMN);

  $pdo->prepare("DELETE FROM overtime_alerts WHERE expert_username=?")->execute([$username]);
  $pdo->prepare("DELETE FROM rest_requests WHERE expert_username=?")->execute([$username]);
  $pdo->prepare("DELETE FROM chat_members WHERE username=? AND role='expert'")->execute([$username]);
  if($cids){$in=implode(',',array_fill(0,count($cids),'?'));$pdo->prepare("DELETE cm FROM chat_messages cm LEFT JOIN chat_members m ON m.conversation_id=cm.conversation_id WHERE cm.conversation_id IN ($in) AND m.conversation_id IS NULL")->execute($cids);$pdo->prepare("DELETE FROM chat_conversations WHERE id IN ($in) AND NOT EXISTS (SELECT 1 FROM chat_members m WHERE m.conversation_id=chat_conversations.id)")->execute($cids);}
  $pdo->prepare("DELETE FROM experts WHERE username=?")->execute([$username]);$pdo->commit();jsonResponse(['success'=>true,'message'=>'کارشناس حذف شد.']);
 }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();jsonResponse(['success'=>false,'message'=>'حذف کارشناس انجام نشد.'],500);}
}
jsonResponse(['success'=>false,'message'=>'عملیات نامعتبر است.'],400);
