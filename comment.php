<?php
session_start();
require_once __DIR__ . '/lib.php';
log_visit();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

$newsId = (int) ($_POST['news_id'] ?? 0);
$author = trim($_POST['author'] ?? '匿名');
$content = trim($_POST['content'] ?? '');

if ($newsId <= 0 || $content === '') {
    header('Location: /index.php');
    exit;
}

$isAdmin = is_admin_logged_in() ? 1 : 0;
if (strlen($author) > 100) {
    $author = mb_substr($author, 0, 100);
}
if (strlen($content) > 2000) {
    $content = mb_substr($content, 0, 2000);
}

$sql = 'INSERT INTO comments (news_id, author, content, is_admin, created_at, ip, ua) VALUES (?, ?, ?, ?, NOW(), ?, ?)';
$stmt = db()->prepare($sql);
$stmt->execute([$newsId, $author, $content, $isAdmin, client_ip(), client_ua()]);

header('Location: /news.php?id=' . $newsId);
exit;
