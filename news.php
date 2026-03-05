<?php
session_start();
require_once __DIR__ . '/lib.php';
log_visit();

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(404);
    exit('无效新闻ID');
}

$stmt = db()->prepare('SELECT * FROM news WHERE id = ? AND status = 1 LIMIT 1');
$stmt->execute([$id]);
$news = $stmt->fetch();

if (!$news) {
    http_response_code(404);
    exit('新闻不存在或未发布');
}

$cStmt = db()->prepare('SELECT * FROM comments WHERE news_id = ? ORDER BY is_admin DESC, created_at DESC');
$cStmt->execute([$id]);
$comments = $cStmt->fetchAll();
?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($news['title']) ?></title>
    <link rel="stylesheet" href="/css/style.css?v=<?=time()?>">
</head>
<body>
<div class="scanline"></div>
<header class="top-nav">
    <div class="brand">[新闻详情]</div>
    <a class="btn" href="/index.php">返回</a>
</header>
<main class="container with-nav">
    <article class="card">
        <h1><?= e($news['title']) ?></h1>
        <p class="subtitle"><?= e($news['subtitle']) ?></p>
        <p class="meta">作者：<?= e($news['author']) ?> · <?= e($news['created_at']) ?></p>

        <textarea id="md-source" hidden><?= safe_markdown_text($news['content']) ?></textarea>
        <div id="md-render" class="markdown"></div>
    </article>

    <section class="card">
        <h2>评论区</h2>
        <form class="form" method="post" action="/comment.php">
            <input type="hidden" name="news_id" value="<?= (int) $news['id'] ?>">
            <label>昵称<input name="author" maxlength="100" required></label>
            <label>评论内容<textarea name="content" rows="4" required></textarea></label>
            <button class="btn" type="submit">发表评论</button>
        </form>
    </section>

    <section>
        <?php foreach ($comments as $c): ?>
            <article class="card comment message-item <?= (int) $c['is_admin'] === 1 ? 'admin-comment' : '' ?>">
                <div class="comment-header">
                    <span class="comment-name"><?= e($c['author']) ?><?php if ((int) $c['is_admin'] === 1): ?><span class="admin-tag">（管理员）</span><?php endif; ?></span>
                    <span class="comment-time"><?= e($c['created_at']) ?></span>
                </div>
                <p><?= nl2br(e($c['content'])) ?></p>
            </article>
        <?php endforeach; ?>
    </section>
</main>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="/js/main.js"></script>
</body>
</html>
