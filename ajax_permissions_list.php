<?php
require_once 'config.php';requirePost();requireRole('admin');
$st=$pdo->query("SELECT u.username,u.display_name,COALESCE(p.can_approve,1) can_approve,COALESCE(p.can_reject,1) can_reject,COALESCE(p.can_force_end,1) can_force_end,COALESCE(p.can_manual_rest,1) can_manual_rest,COALESCE(p.can_chat,1) can_chat,COALESCE(p.can_view_other_teams,1) can_view_other_teams FROM users u LEFT JOIN supervisor_permissions p ON p.supervisor_username=u.username WHERE u.role='supervisor' ORDER BY u.display_name");
jsonResponse(['success'=>true,'items'=>$st->fetchAll()]);
