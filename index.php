<?php
session_start();
require_once __DIR__ . '/lib.php';
log_visit();

$stmt = db()->query('SELECT id, title, subtitle, author, created_at FROM news WHERE status = 1 ORDER BY created_at DESC');
$newsList = $stmt->fetchAll();
?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($site_name) ?> - 首页</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<header class="top-nav">
    <div class="brand">[<?= e($site_name) ?>]</div>
    <div class="actions">
        <a class="btn" href="/post.php">发布新闻</a>
        <?php if (is_admin_logged_in()): ?>
            <a class="btn admin" href="/admin/dashboard.php">管理台</a>
        <?php endif; ?>
    </div>
</header>

<main class="container with-nav">
    <h1 class="title">新闻看板</h1>

    <?php if (!$newsList): ?>
        <div class="card">暂无已发布新闻。</div>
    <?php endif; ?>

    <?php foreach ($newsList as $item): ?>
        <article class="card news-card">
            <h2><a href="/news.php?id=<?= (int) $item['id'] ?>"><?= e($item['title']) ?></a></h2>
            <p class="subtitle"><?= e($item['subtitle']) ?></p>
            <p class="meta">作者：<?= e($item['author']) ?> · <?= e($item['created_at']) ?></p>
            <a class="btn" href="/news.php?id=<?= (int) $item['id'] ?>">阅读详情</a>
        </article>
    <?php endforeach; ?>
</main>
<script src="/js/main.js"></script>
</body>
</html>
