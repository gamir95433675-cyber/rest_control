<?php
declare(strict_types=1);

session_start();
error_reporting(E_ALL);
ini_set('display_errors', '0');

define('DB_HOST', 'localhost');
define('DB_NAME', 'rest_control');
define('DB_USER', 'root');
define('DB_PASS', '');
define('APP_TZ', 'Asia/Tehran');

date_default_timezone_set(APP_TZ);

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('خطا در اتصال به دیتابیس');
}

// Small automatic schema migration for existing installations.
try {
    $col = $pdo->query("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='rest_requests' AND COLUMN_NAME='request_type'")->fetchColumn();
    if ($col && strpos($col, "'emergency'") === false) {
        $pdo->exec("ALTER TABLE rest_requests MODIFY request_type ENUM('normal','emergency') NOT NULL DEFAULT 'normal'");
    }
} catch (Throwable $e) { /* keep the app usable if an existing DB user cannot ALTER schema */ }
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS announcements (id INT AUTO_INCREMENT PRIMARY KEY,message TEXT NOT NULL,created_by VARCHAR(100) NOT NULL,created_by_role ENUM('admin','supervisor') NOT NULL,created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,INDEX idx_ann_created_at(created_at)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    try { $pdo->exec("ALTER TABLE announcements CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"); } catch (Throwable $e) { /* existing DB user may not have ALTER permission */ }
    $hasExt=(int)$pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='rest_extensions'")->fetchColumn();
    if($hasExt){ $pdo->exec("DROP TABLE rest_extensions"); }
    $hasCanExtend=(int)$pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='supervisor_permissions' AND COLUMN_NAME='can_extend'")->fetchColumn();
    if($hasCanExtend){ $pdo->exec("ALTER TABLE supervisor_permissions DROP COLUMN can_extend"); }
} catch (Throwable $e) {}


function jsonResponse(array $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function requirePost(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(['success' => false, 'message' => 'متد نامعتبر'], 405);
    }
}

function requireLogin(): array {
    if (!isset($_SESSION['user'])) {
        jsonResponse(['success' => false, 'message' => 'لطفاً وارد شوید'], 401);
    }
    return $_SESSION['user'];
}

function requireRole(string ...$roles): array {
    $user = requireLogin();
    if (!in_array($user['role'], $roles, true)) {
        jsonResponse(['success' => false, 'message' => 'دسترسی غیرمجاز'], 403);
    }
    return $user;
}

function isLoggedIn(): bool {
    return isset($_SESSION['user']);
}
function isAdmin(): bool {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}
function isSupervisor(): bool {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'supervisor';
}
function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}
function h(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function postInt(string $key, int $default = 0): int {
    return isset($_POST[$key]) && is_numeric($_POST[$key]) ? (int)$_POST[$key] : $default;
}

/* Gregorian -> Jalali. */
function gregorianToJalali(int $gy, int $gm, int $gd): array {
    $gDaysInMonth = [31, (($gy % 4 === 0 && $gy % 100 !== 0) || ($gy % 400 === 0)) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    $jDaysInMonth = [31,31,31,31,31,31,30,30,30,30,30,29];
    $gy2 = $gm > 2 ? $gy + 1 : $gy;
    $days = 355666 + (365 * $gy) + intdiv($gy2 + 3, 4) - intdiv($gy2 + 99, 100) + intdiv($gy2 + 399, 400) + $gd;
    for ($i = 0; $i < $gm - 1; $i++) $days += $gDaysInMonth[$i];
    $jy = -1595 + 33 * intdiv($days, 12053);
    $days %= 12053;
    $jy += 4 * intdiv($days, 1461);
    $days %= 1461;
    if ($days > 365) {
        $jy += intdiv($days - 1, 365);
        $days = ($days - 1) % 365;
    }
    if ($days < 186) {
        $jm = 1 + intdiv($days, 31);
        $jd = 1 + ($days % 31);
    } else {
        $jm = 7 + intdiv($days - 186, 30);
        $jd = 1 + (($days - 186) % 30);
    }
    return [$jy, $jm, $jd];
}
function toJalali(?string $datetime, bool $withTime = true): string {
    if (!$datetime) return '-';
    $ts = strtotime($datetime);
    if ($ts === false) return '-';
    $dt = new DateTime('@' . $ts);
    $dt->setTimezone(new DateTimeZone(APP_TZ));
    [$jy,$jm,$jd] = gregorianToJalali((int)$dt->format('Y'), (int)$dt->format('n'), (int)$dt->format('j'));
    $s = sprintf('%04d/%02d/%02d', $jy, $jm, $jd);
    return $withTime ? $s . ' ' . $dt->format('H:i:s') : $s;
}

/* Jalali -> Gregorian. */
function jalaliToGregorian(int $jy, int $jm, int $jd): array {
    $jy += 1595;
    $days = -355668 + (365 * $jy) + intdiv($jy, 33) * 8 + intdiv(($jy % 33) + 3, 4) + $jd;
    if ($jm <= 6) {
        $days += ($jm - 1) * 31;
    } else {
        $days += (($jm - 7) * 30) + 186;
    }
    $gy = 400 * intdiv($days, 146097);
    $days %= 146097;
    if ($days > 36524) {
        $gy += 100 * intdiv(--$days, 36524);
        $days %= 36524;
        if ($days >= 365) $days++;
    }
    $gy += 4 * intdiv($days, 1461);
    $days %= 1461;
    if ($days > 365) {
        $gy += intdiv($days - 1, 365);
        $days = ($days - 1) % 365;
    }
    $gd = $days + 1;
    $sal = [0,31,28,31,30,31,30,31,31,30,31,30,31];
    $leap = (($gy % 4 === 0 && $gy % 100 !== 0) || ($gy % 400 === 0));
    $sal[2] = $leap ? 29 : 28;
    $gm = 1;
    while ($gm <= 12 && $gd > $sal[$gm]) {
        $gd -= $sal[$gm];
        $gm++;
    }
    return [$gy, $gm, $gd];
}
function jalaliDateToGregorian(string $date): ?string {
    if (!preg_match('/^(13|14)\d{2}[\/-](0[1-9]|1[0-2])[\/-](0[1-9]|[12]\d|3[01])$/', $date, $m)) return null;
    $parts = preg_split('/[\/-]/', $date);
    [$gy,$gm,$gd] = jalaliToGregorian((int)$parts[0], (int)$parts[1], (int)$parts[2]);
    $check = gregorianToJalali($gy,$gm,$gd);
    if ($check !== [(int)$parts[0],(int)$parts[1],(int)$parts[2]]) return null;
    return sprintf('%04d-%02d-%02d', $gy,$gm,$gd);
}

function faDigits(string $value): string {
    return strtr($value, ['0'=>'۰','1'=>'۱','2'=>'۲','3'=>'۳','4'=>'۴','5'=>'۵','6'=>'۶','7'=>'۷','8'=>'۸','9'=>'۹']);
}
function toJalaliFa(?string $datetime, bool $withTime = true): string {
    return faDigits(toJalali($datetime, $withTime));
}
function statusText(string $status): string {
    return [
        'pending'=>'در انتظار تأیید',
        'approved'=>'در حال استراحت',
        'rejected'=>'رد شده',
        'completed'=>'تمام شده',
        'overtime'=>'استراحت غیر مجاز',
        'cancelled'=>'لغو شده',
    ][$status] ?? $status;
}
function statusBadge(string $status): string {
    $map = [
        'pending'=>['warn','در انتظار تأیید'],
        'approved'=>['ok','در حال استراحت'],
        'rejected'=>['danger','رد شده'],
        'completed'=>['','تمام شده'],
        'overtime'=>['danger','استراحت غیر مجاز'],
        'cancelled'=>['','لغو شده'],
    ];
    [$cls,$label] = $map[$status] ?? ['',$status];
    return '<span class="pill '.h($cls).'">' . h($label) . '</span>';
}
function clampDuration(int $duration): int {
    return max(1, min(60, $duration));
}
// Feature helpers added for the Shaya rest-control build.
function currentUsername(): string { return (string)($_SESSION['user']['username'] ?? ''); }
function currentDisplayName(): string { return (string)($_SESSION['user']['display_name'] ?? ''); }
function hasPermission(string $permission): bool {
    $u=requireLogin();
    if($u['role']==='admin') return true;
    $allowed=['can_approve','can_reject','can_force_end','can_manual_rest','can_chat','can_view_other_teams'];
    if(!in_array($permission,$allowed,true)) return false;
    $st=$GLOBALS['pdo']->prepare("SELECT {$permission} FROM supervisor_permissions WHERE supervisor_username=? LIMIT 1");
    $st->execute([$u['username']]);
    return (bool)$st->fetchColumn();
}
function requirePermission(string $permission): array {
    $u=requireRole('admin','supervisor');
    if(!hasPermission($permission)) jsonResponse(['success'=>false,'message'=>'این دسترسی توسط مدیر غیرفعال شده است.'],403);
    return $u;
}
function allChatUsers(PDO $pdo, array $user): array {
    $rows=[]; $seen=[];
    $st=$pdo->query("SELECT username,display_name,role FROM users WHERE role IN ('admin','supervisor') ORDER BY role='admin' DESC, display_name");
    foreach($st->fetchAll() as $r){
        if($r['username']===$user['username'] && $r['role']===$user['role']) continue;
        $k=$r['role'].'|'.$r['username']; if(!isset($seen[$k])){$rows[]=$r;$seen[$k]=1;}
    }
    $st=$pdo->query("SELECT username,display_name,'expert' role FROM experts WHERE username NOT IN (SELECT username FROM users WHERE role IN ('admin','supervisor')) ORDER BY display_name");
    foreach($st->fetchAll() as $r){
        if($r['username']===$user['username'] && $r['role']===$user['role']) continue;
        $k=$r['role'].'|'.$r['username']; if(!isset($seen[$k])){$rows[]=$r;$seen[$k]=1;}
    }
    return $rows;
}
function participantKey(string $role,string $username): string { return $role.':'.$username; }
function canAccessChatConversation(PDO $pdo,int $conversationId,string $role,string $username): bool {
    $st=$pdo->prepare("SELECT 1 FROM chat_members WHERE conversation_id=? AND username=? AND role=? LIMIT 1");
    $st->execute([$conversationId,$username,$role]); return (bool)$st->fetchColumn();
}
function ensureDirectConversation(PDO $pdo,string $a,string $aName,string $aRole,string $b,string $bName,string $bRole): int {
    $st=$pdo->prepare("SELECT c.id FROM chat_conversations c JOIN chat_members m1 ON m1.conversation_id=c.id JOIN chat_members m2 ON m2.conversation_id=c.id WHERE c.type='direct' AND m1.username=? AND m1.role=? AND m2.username=? AND m2.role=? LIMIT 1");
    $st->execute([$a,$aRole,$b,$bRole]);$id=$st->fetchColumn();if($id)return (int)$id;
    $pdo->beginTransaction();try{
        $st=$pdo->prepare("INSERT INTO chat_conversations(type,title,created_by,created_by_role) VALUES('direct',NULL,?,?)");$st->execute([$a,$aRole]);$id=(int)$pdo->lastInsertId();
        $st=$pdo->prepare("INSERT INTO chat_members(conversation_id,username,role,display_name) VALUES(?,?,?,?),(?,?,?,?)");$st->execute([$id,$a,$aRole,$aName,$id,$b,$bRole,$bName]);$pdo->commit();return $id;
    }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
}
function normalizeText(string $s,int $max=2000): string { return trim(mb_substr($s,0,$max)); }
