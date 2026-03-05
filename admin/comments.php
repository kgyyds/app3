<?php
session_start();
require_once __DIR__ . '/../lib.php';
log_visit();
require_admin_login();

$message = '';
$messageType = 'success';

$newsId = (int) ($_GET['news_id'] ?? $_POST['news_id'] ?? 0);
$editId = (int) ($_GET['edit_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentId = (int) ($_POST['comment_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($newsId > 0 && $commentId > 0) {
        if ($action === 'delete') {
            $stmt = db()->prepare('DELETE FROM comments WHERE id = ? AND news_id = ?');
            $stmt->execute([$commentId, $newsId]);
            $message = '评论已删除。';
        } elseif ($action === 'update') {
            $author = trim($_POST['author'] ?? '匿名');
            $content = trim($_POST['content'] ?? '');

            if ($content === '') {
                $message = '评论内容不能为空。';
                $messageType = 'error';
                $editId = $commentId;
            } else {
                if (mb_strlen($author) > 100) {
                    $author = mb_substr($author, 0, 100);
                }
                if (mb_strlen($content) > 2000) {
                    $content = mb_substr($content, 0, 2000);
                }

                $stmt = db()->prepare('UPDATE comments SET author = ?, content = ? WHERE id = ? AND news_id = ?');
                $stmt->execute([$author, $content, $commentId, $newsId]);
                $message = '评论已更新。';
            }
        }
    }
}

$newsStmt = db()->query('SELECT id, title, created_at FROM news ORDER BY created_at DESC LIMIT 200');
$newsList = $newsStmt->fetchAll();

$currentNews = null;
$comments = [];
if ($newsId > 0) {
    $nStmt = db()->prepare('SELECT id, title, subtitle, author, created_at FROM news WHERE id = ? LIMIT 1');
    $nStmt->execute([$newsId]);
    $currentNews = $nStmt->fetch();

    if ($currentNews) {
        $cStmt = db()->prepare('SELECT * FROM comments WHERE news_id = ? ORDER BY created_at DESC');
        $cStmt->execute([$newsId]);
        $comments = $cStmt->fetchAll();
    }
}
?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>评论管理</title>
    <link rel="stylesheet" href="/css/style.css?v=4">
</head>
<body>
<div class="scanline"></div>
<header class="top-nav">
    <div class="brand admin-highlight">[评论管理]</div>
    <a class="btn" href="/admin/dashboard.php">返回后台</a>
</header>
<main class="container with-nav">
    <h1 class="title" data-text="评论管理">评论管理</h1>

    <?php if ($message): ?>
        <div class="alert <?= $messageType === 'error' ? 'error' : 'success' ?>"><?= e($message) ?></div>
    <?php endif; ?>

    <div class="card table-wrap">
        <table>
            <thead>
            <tr><th>ID</th><th>新闻标题</th><th>发布时间</th><th>操作</th></tr>
            </thead>
            <tbody>
            <?php foreach ($newsList as $item): ?>
                <tr>
                    <td><?= (int) $item['id'] ?></td>
                    <td><?= e($item['title']) ?></td>
                    <td><?= e($item['created_at']) ?></td>
                    <td><a class="btn" href="/admin/comments.php?news_id=<?= (int) $item['id'] ?>">管理评论</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($currentNews): ?>
        <section class="card">
            <h2><?= e($currentNews['title']) ?></h2>
            <p class="subtitle"><?= e($currentNews['subtitle']) ?></p>
            <p class="meta">作者：<?= e($currentNews['author']) ?> · <?= e($currentNews['created_at']) ?></p>
        </section>

        <?php if (!$comments): ?>
            <div class="card">该新闻暂无评论。</div>
        <?php endif; ?>

        <?php foreach ($comments as $c): ?>
            <article class="card comment message-item <?= (int) $c['is_admin'] === 1 ? 'admin-comment' : '' ?>">
                <div class="comment-header">
                    <span class="comment-name">#<?= (int) $c['id'] ?> · <?= e($c['author']) ?><?php if ((int) $c['is_admin'] === 1): ?><span class="admin-tag">（管理员）</span><?php endif; ?></span>
                    <span class="comment-time"><?= e($c['created_at']) ?></span>
                </div>

                <?php if ($editId === (int) $c['id']): ?>
                    <form class="form" method="post">
                        <input type="hidden" name="news_id" value="<?= (int) $newsId ?>">
                        <input type="hidden" name="comment_id" value="<?= (int) $c['id'] ?>">
                        <input type="hidden" name="action" value="update">
                        <label>昵称<input type="text" name="author" maxlength="100" value="<?= e($c['author']) ?>"></label>
                        <label>评论内容<textarea name="content" rows="4" required><?= e($c['content']) ?></textarea></label>
                        <div class="row">
                            <button class="btn admin" type="submit">保存</button>
                            <a class="btn" href="/admin/comments.php?news_id=<?= (int) $newsId ?>">取消</a>
                        </div>
                    </form>
                <?php else: ?>
                    <p><?= nl2br(e($c['content'])) ?></p>
                    <div class="row">
                        <a class="btn" href="/admin/comments.php?news_id=<?= (int) $newsId ?>&edit_id=<?= (int) $c['id'] ?>">编辑</a>
                        <form method="post" onsubmit="return confirm('确定删除这条评论吗？');">
                            <input type="hidden" name="news_id" value="<?= (int) $newsId ?>">
                            <input type="hidden" name="comment_id" value="<?= (int) $c['id'] ?>">
                            <input type="hidden" name="action" value="delete">
                            <button class="btn danger" type="submit">删除</button>
                        </form>
                    </div>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</main>
<script src="/js/main.js"></script>
</body>
</html>
