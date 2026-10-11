<?php
require_once 'config.php';requirePost();$user=requireRole('expert');
$st=$pdo->prepare("SELECT r.*,UNIX_TIMESTAMP(r.approved_at) approved_ts FROM rest_requests r WHERE r.expert_username=? AND r.status IN ('pending','approved') ORDER BY r.id DESC LIMIT 1");$st->execute([$user['username']]);$r=$st->fetch();
$st=$pdo->prepare("SELECT rest_login_count,daily_rest_limit,last_rest_login_date FROM experts WHERE username=?");$st->execute([$user['username']]);$e=$st->fetch();if(!$e||$e['last_rest_login_date']!==date('Y-m-d'))$e['rest_login_count']=0;
jsonResponse(['success'=>true,'request'=>$r?:null,'login_count'=>(int)$e['rest_login_count'],'login_limit'=>(int)$e['daily_rest_limit']]);
