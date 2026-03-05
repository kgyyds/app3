<?php
session_start();
require_once __DIR__ . '/lib.php';
log_visit();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $author = trim($_POST['author'] ?? '匿名');
    $content = trim($_POST['content'] ?? '');

    if ($title === '' || $content === '') {
        $errors[] = '标题和内容不能为空。';
    }

    if (strlen($title) > 255 || strlen($subtitle) > 255 || strlen($author) > 100) {
        $errors[] = '输入内容超出长度限制。';
    }

    if (!empty($_FILES['image']['name'])) {
        global $allowed_image_ext, $max_upload_size, $upload_url_prefix;
        ensure_upload_dir();

        $file = $_FILES['image'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = '图片上传失败。';
        } elseif ($file['size'] > $max_upload_size) {
            $errors[] = '图片大小不能超过 5MB。';
        } else {
            $original = $file['name'];
            $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed_image_ext, true)) {
                $errors[] = '仅支持 jpg/jpeg/png/webp/gif 图片。';
            } else {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                if (!in_array($mime, $allowedMime, true)) {
                    $errors[] = '非法图片文件。';
                } else {
                    $safeName = date('YmdHis') . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                    $target = $upload_dir . $safeName;
                    if (!str_starts_with(realpath(dirname($target)) ?: '', realpath($upload_dir) ?: '')) {
                        $errors[] = '上传路径异常。';
                    } elseif (!move_uploaded_file($file['tmp_name'], $target)) {
                        $errors[] = '保存图片失败。';
                    } else {
                        $content .= "\n\n![上传图片](" . $upload_url_prefix . $safeName . ')';
                    }
                }
            }
        }
    }

    if (!$errors) {
        $sql = 'INSERT INTO news (title, subtitle, author, content, status, created_at, ip, ua) VALUES (?, ?, ?, ?, 0, NOW(), ?, ?)';
        $stmt = db()->prepare($sql);
        $stmt->execute([$title, $subtitle, $author, $content, client_ip(), client_ua()]);
        $success = '投稿成功，等待管理员审核。';
    }
}
?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>发布新闻</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="scanline"></div>
<header class="top-nav">
    <div class="brand">[发布新闻]</div>
    <a class="btn" href="/index.php">返回首页</a>
</header>
<main class="container with-nav">
    <h1 class="title">发布新闻（Markdown）</h1>
    <?php foreach ($errors as $err): ?>
        <div class="alert error"><?= e($err) ?></div>
    <?php endforeach; ?>
    <?php if ($success): ?>
        <div class="alert success"><?= e($success) ?></div>
    <?php endif; ?>

    <form class="card form" method="post" enctype="multipart/form-data">
        <label>标题<input name="title" maxlength="255" required></label>
        <label>副标题<input name="subtitle" maxlength="255"></label>
        <label>作者<input name="author" maxlength="100" placeholder="匿名"></label>
        <label>内容（Markdown）<textarea name="content" rows="10" required></textarea></label>
        <label>上传图片（可选）<input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif"></label>
        <button class="btn" type="submit">提交审核</button>
    </form>
</main>
<script src="/js/main.js"></script>
</body>
</html>
