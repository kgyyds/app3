<?php
session_start();
require_once __DIR__ . '/../lib.php';
log_visit();
require_admin_login();

$message = '';
$messageType = 'success';
$currentNews = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($id > 0 && $action === 'update') {
        $title = trim($_POST['title'] ?? '');
        $subtitle = trim($_POST['subtitle'] ?? '');
        $author = trim($_POST['author'] ?? '匿名');
        $content = trim($_POST['content'] ?? '');
        $status = (int) ($_POST['status'] ?? 0);
        $status = $status === 1 ? 1 : 0;

        if ($title === '' || $content === '') {
            $message = '标题和正文不能为空。';
            $messageType = 'error';
            $currentNews = [
                'id' => $id,
                'title' => $title,
                'subtitle' => $subtitle,
                'author' => $author,
                'content' => $content,
                'status' => $status,
            ];
        } else {
            $stmt = db()->prepare('UPDATE news SET title = ?, subtitle = ?, author = ?, content = ?, status = ? WHERE id = ?');
            $stmt->execute([$title, $subtitle, $author, $content, $status, $id]);
            $message = '新闻已更新。';
        }
    }
}

if ($currentNews === null) {
    $selectedId = (int) ($_GET['id'] ?? 0);
    if ($selectedId > 0) {
        $stmt = db()->prepare('SELECT id, title, subtitle, author, content, status FROM news WHERE id = ? LIMIT 1');
        $stmt->execute([$selectedId]);
        $currentNews = $stmt->fetch();
    }
}

$listStmt = db()->query('SELECT id, title, author, status, created_at FROM news ORDER BY created_at DESC LIMIT 200');
$newsList = $listStmt->fetchAll();
?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新闻修改</title>
    <link rel="stylesheet" href="/css/style.css?v=3">
</head>
<body>
<div class="scanline"></div>
<header class="top-nav">
    <div class="brand admin-highlight">[新闻修改]</div>
    <a class="btn" href="/admin/dashboard.php">返回后台</a>
</header>
<main class="container with-nav">
    <h1 class="title" data-text="新闻修改">新闻修改</h1>

    <?php if ($message): ?>
        <div class="alert <?= $messageType === 'error' ? 'error' : 'success' ?>"><?= e($message) ?></div>
    <?php endif; ?>

    <div class="card table-wrap">
        <table>
            <thead>
            <tr><th>ID</th><th>标题</th><th>作者</th><th>状态</th><th>时间</th><th>操作</th></tr>
            </thead>
            <tbody>
            <?php foreach ($newsList as $item): ?>
                <tr>
                    <td><?= (int) $item['id'] ?></td>
                    <td><?= e($item['title']) ?></td>
                    <td><?= e($item['author']) ?></td>
                    <td><?= (int) $item['status'] === 1 ? '已通过' : '待审核' ?></td>
                    <td><?= e($item['created_at']) ?></td>
                    <td><a class="btn" href="/admin/edit.php?id=<?= (int) $item['id'] ?>">编辑</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($currentNews): ?>
        <form class="card form" method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= (int) $currentNews['id'] ?>">
            <label>标题<input type="text" name="title" required value="<?= e($currentNews['title']) ?>"></label>
            <label>副标题<input type="text" name="subtitle" value="<?= e($currentNews['subtitle']) ?>"></label>
            <label>作者<input type="text" name="author" value="<?= e($currentNews['author']) ?>"></label>
            <label>状态
                <select name="status">
                    <option value="0" <?= (int) $currentNews['status'] === 0 ? 'selected' : '' ?>>待审核</option>
                    <option value="1" <?= (int) $currentNews['status'] === 1 ? 'selected' : '' ?>>已通过</option>
                </select>
            </label>
            <label>正文<textarea name="content" required rows="12"><?= e($currentNews['content']) ?></textarea></label>
            <button class="btn admin" type="submit">保存修改</button>
        </form>
    <?php endif; ?>
</main>
<script src="/js/main.js"></script>
</body>
</html>
