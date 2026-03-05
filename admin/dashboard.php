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
?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台总览</title>
    <link rel="stylesheet" href="/css/style.css?v=3">
</head>
<body>
<div class="scanline"></div>
<header class="top-nav">
    <div class="brand admin-highlight">[管理员后台]</div>
    <div class="actions">
        <a class="btn" href="/index.php">前台</a>
        <a class="btn" href="/admin/dashboard.php?logout=1">退出</a>
    </div>
</header>
<main class="container with-nav">
    <h1 class="title" data-text="管理菜单">管理菜单</h1>
    <div class="card">
        <ul class="admin-menu-list">
            <li><a class="admin-menu-link" href="/admin/visits.php">访问记录</a></li>
            <li><a class="admin-menu-link" href="/admin/review.php">新闻审核</a></li>
            <li><a class="admin-menu-link" href="/admin/edit.php">新闻修改</a></li>
            <li><a class="admin-menu-link" href="/admin/comments.php">评论管理</a></li>
        </ul>
    </div>
</main>
<script src="/js/main.js"></script>
</body>
</html>
