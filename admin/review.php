<?php
session_start();
require_once __DIR__ . '/../lib.php';
log_visit();
require_admin_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($id > 0) {
        if ($action === 'approve') {
            $stmt = db()->prepare('UPDATE news SET status = 1 WHERE id = ?');
            $stmt->execute([$id]);
        } elseif ($action === 'delete') {
            $stmt = db()->prepare('DELETE FROM news WHERE id = ?');
            $stmt->execute([$id]);
        }
    }
    header('Location: /admin/review.php');
    exit;
}

$stmt = db()->query('SELECT * FROM news WHERE status = 0 ORDER BY created_at DESC');
$pending = $stmt->fetchAll();
?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>审核新闻</title>
    <link rel="stylesheet" href="/css/style.css?v=<?=time()?>">
</head>
<body>
<div class="scanline"></div>
<header class="top-nav">
    <div class="brand admin-highlight">[审核新闻]</div>
    <a class="btn" href="/admin/dashboard.php">返回后台</a>
</header>
<main class="container with-nav">
    <?php if (!$pending): ?>
        <div class="card">暂无待审核新闻。</div>
    <?php endif; ?>

    <?php foreach ($pending as $n): ?>
        <article class="card">
            <h2><?= e($n['title']) ?></h2>
            <p class="subtitle"><?= e($n['subtitle']) ?></p>
            <p class="meta">作者：<?= e($n['author']) ?> · <?= e($n['created_at']) ?></p>
            <pre class="preview"><?= e(mb_substr($n['content'], 0, 300)) ?></pre>
            <div class="row">
                <form method="post">
                    <input type="hidden" name="id" value="<?= (int) $n['id'] ?>">
                    <input type="hidden" name="action" value="approve">
                    <button class="btn admin" type="submit">通过</button>
                </form>
                <form method="post" onsubmit="return confirm('确定删除该投稿?');">
                    <input type="hidden" name="id" value="<?= (int) $n['id'] ?>">
                    <input type="hidden" name="action" value="delete">
                    <button class="btn danger" type="submit">删除</button>
                </form>
            </div>
        </article>
    <?php endforeach; ?>
</main>
<script src="/js/main.js"></script>
</body>
</html>
