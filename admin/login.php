<?php
session_start();
require_once __DIR__ . '/../lib.php';
log_visit();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if (hash_equals($admin_password, $password)) {
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_name'] = '管理员';
        header('Location: /admin/dashboard.php');
        exit;
    }
    $error = '密码错误';
}
?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员登录</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<main class="container">
    <h1 class="title">管理员登录</h1>
    <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <form class="card form" method="post">
        <label>管理员密码<input type="password" name="password" required></label>
        <button class="btn admin" type="submit">登录后台</button>
    </form>
</main>
</body>
</html>
