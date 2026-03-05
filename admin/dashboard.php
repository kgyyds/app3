<?php
session_start();
require_once __DIR__ . '/../lib.php';
log_visit();
require_admin_login();

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /admin/login.php');
    exit;
}

$visitStmt = db()->query('SELECT ip, ua, path, referer, created_at FROM visits ORDER BY created_at DESC LIMIT 100');
$visits = $visitStmt->fetchAll();
?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台总览</title>
    <link rel="stylesheet" href="/css/style.css?v=2">
</head>
<body>
<div class="scanline"></div>
<header class="top-nav">
    <div class="brand admin-highlight">[管理员后台]</div>
    <div class="actions">
        <a class="btn admin" href="/admin/review.php">审核新闻</a>
        <a class="btn" href="/index.php">前台</a>
        <a class="btn" href="/admin/dashboard.php?logout=1">退出</a>
    </div>
</header>
<main class="container with-nav">
    <h1 class="title">访问日志（最近100条）</h1>
    <div class="card table-wrap">
        <table>
            <thead><tr><th>IP</th><th>UA</th><th>PATH</th><th>REFERER</th><th>时间</th></tr></thead>
            <tbody>
            <?php foreach ($visits as $v): ?>
                <tr>
                    <td><?= e($v['ip']) ?></td>
                    <td><?= e($v['ua']) ?></td>
                    <td><?= e($v['path']) ?></td>
                    <td><?= e($v['referer']) ?></td>
                    <td><?= e($v['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<script src="/js/main.js"></script>
</body>
</html>
