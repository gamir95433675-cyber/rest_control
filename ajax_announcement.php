<?php
require_once 'config.php';
requirePost();

function ensureAnnouncements(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS announcements (id INT AUTO_INCREMENT PRIMARY KEY,message TEXT NOT NULL,created_by VARCHAR(100) NOT NULL,created_by_role ENUM('admin','supervisor') NOT NULL,created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,INDEX idx_ann_created_at(created_at)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}
function latestAnnouncement(PDO $pdo): ?array {
    ensureAnnouncements($pdo);
    $st=$pdo->query("SELECT a.*,COALESCE(u.display_name,a.created_by) created_by_name FROM announcements a LEFT JOIN users u ON u.username COLLATE utf8mb4_unicode_ci = a.created_by COLLATE utf8mb4_unicode_ci ORDER BY a.id DESC LIMIT 1");
    $r=$st->fetch(); return $r?:null;
}
$action=trim((string)($_POST['action']??'get'));
try {
    if($action==='get') jsonResponse(['success'=>true,'announcement'=>latestAnnouncement($pdo)]);
    $user=$_SESSION['user']??null;
    if(!$user || !in_array($user['role'],['admin','supervisor'],true)) jsonResponse(['success'=>false,'message'=>'دسترسی ندارید.'],403);
    ensureAnnouncements($pdo);
    if($action==='save') {
        $msg=trim((string)($_POST['message']??''));
        if($msg==='') jsonResponse(['success'=>false,'message'=>'متن اطلاعیه خالی است.'],400);
        if(mb_strlen($msg,'UTF-8')>2000) $msg=mb_substr($msg,0,2000,'UTF-8');
        $pdo->beginTransaction();
        $pdo->exec('DELETE FROM announcements');
        $st=$pdo->prepare('INSERT INTO announcements(message,created_by,created_by_role) VALUES(?,?,?)');
        $st->execute([$msg,$user['username'],$user['role']]);
        $pdo->commit();
        jsonResponse(['success'=>true,'message'=>'اطلاعیه منتشر شد و جایگزین اطلاعیه قبلی شد.','announcement'=>latestAnnouncement($pdo)]);
    }
    if($action==='clear') {
        $pdo->exec('DELETE FROM announcements');
        jsonResponse(['success'=>true,'message'=>'اطلاعیه حذف شد.']);
    }
    jsonResponse(['success'=>false,'message'=>'عملیات نامعتبر است.'],400);
} catch(Throwable $e) {
    if($pdo->inTransaction()) $pdo->rollBack();
    jsonResponse(['success'=>false,'message'=>'خطا در بخش اطلاعیه: '.$e->getMessage()],500);
}
