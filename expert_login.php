<?php
require_once 'config.php';
requirePost();
$expert=trim((string)($_POST['expert']??''));
$supervisor=trim((string)($_POST['supervisor']??''));
if($expert==='' || $supervisor===''){ http_response_code(400); exit('سرپرست و کارشناس را انتخاب کنید.'); }
$stmt=$pdo->prepare("SELECT username,supervisor_username,display_name FROM experts WHERE username=? AND supervisor_username=? LIMIT 1");
$stmt->execute([$expert,$supervisor]);
$exp=$stmt->fetch();
if(!$exp){ http_response_code(404); exit('کارشناس یافت نشد.'); }
session_regenerate_id(true);
$_SESSION['user']=['username'=>$exp['username'],'role'=>'expert','supervisor'=>$exp['supervisor_username'],'display_name'=>$exp['display_name']];
redirect('expert.php');
