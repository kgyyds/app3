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

<link rel="stylesheet" href="/css/style.css?v=<?=time()?>">

<style>

/* ===== 新闻标题 ===== */

.news-title{
font-size:22px;
color:#00ff88;
font-weight:700;
margin-bottom:6px;
text-shadow:
0 0 6px rgba(0,255,80,0.6),
0 0 14px rgba(0,255,80,0.3);
}

/* ===== 整个卡片点击 ===== */

.news-link{
display:block;
color:inherit;
text-decoration:none;
}

/* ===== 卡片 hover ===== */

.news-card{
cursor:pointer;
transition:all .25s;
}

.news-card:hover{
transform:translateY(-4px);
border-color:#00ff88;
box-shadow:
0 0 14px rgba(0,255,80,.18),
inset 0 0 18px rgba(0,255,80,.08);
}

/* ===== 副标题 ===== */

.news-sub{
color:rgba(0,255,120,0.6);
margin-bottom:6px;
}

/* ===== 时间作者 ===== */

.news-meta{
font-size:13px;
color:rgba(0,255,120,0.45);
}

</style>

</head>
<body>

<div class="scanline"></div>

<header class="top-nav">

<div class="brand">
[<?= e($site_name) ?>]
</div>

<div class="actions">

<a class="btn" href="/post.php">发布新闻</a>

<?php if (is_admin_logged_in()): ?>
<a class="btn admin" href="/admin/dashboard.php">管理台</a>
<?php endif; ?>

</div>

</header>

<main class="container with-nav">

<h1 class="title" data-text="新闻看板">新闻看板</h1>

<?php if (!$newsList): ?>

<div class="card">
暂无已发布新闻
</div>

<?php endif; ?>

<?php foreach ($newsList as $item): ?>

<a class="news-link" href="/news.php?id=<?= (int)$item['id'] ?>">

<article class="card news-card">

<div class="news-title">
<?= e($item['title']) ?>
</div>

<div class="news-sub">
<?= e($item['subtitle']) ?>
</div>

<div class="news-meta">
作者：<?= e($item['author']) ?> · <?= e($item['created_at']) ?>
</div>

</article>

</a>

<?php endforeach; ?>

</main>

<script src="/js/main.js"></script>

</body>
</html>