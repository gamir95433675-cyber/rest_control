<?php
require_once 'config.php';
requirePost();
$user=requireRole('expert');
$stmt=$pdo->prepare("SELECT id,status,approved_at,duration_minutes,completed_at,overtime_seconds FROM rest_requests WHERE expert_username=? AND status IN ('pending','approved') ORDER BY id DESC LIMIT 1");
$stmt->execute([$user['username']]);
$req=$stmt->fetch();
jsonResponse(['success'=>true,'request'=>$req ?: null]);
