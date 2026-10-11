<?php
require_once 'config.php';
requirePost();
$sup=trim((string)($_POST['supervisor']??''));
if($sup==='') jsonResponse(['success'=>true,'experts'=>[]]);
$stmt=$pdo->prepare("SELECT username,display_name FROM experts WHERE supervisor_username=? ORDER BY display_name,username");
$stmt->execute([$sup]);
jsonResponse(['success'=>true,'experts'=>$stmt->fetchAll()]);
