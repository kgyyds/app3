<?php
require_once __DIR__ . '/config.php';

date_default_timezone_set($timezone);

function db(): PDO
{
    static $pdo = null;
    global $host, $db, $user, $pass;

    if ($pdo === null) {
        $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    return $pdo;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function client_ua(): string
{
    return substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 512);
}

function request_path(): string
{
    return substr($_SERVER['REQUEST_URI'] ?? '/', 0, 255);
}

function request_referer(): string
{
    return substr($_SERVER['HTTP_REFERER'] ?? '', 0, 255);
}

function log_visit(): void
{
    try {
        $sql = 'INSERT INTO visits (ip, ua, path, referer, created_at) VALUES (?, ?, ?, ?, NOW())';
        $stmt = db()->prepare($sql);
        $stmt->execute([client_ip(), client_ua(), request_path(), request_referer()]);
    } catch (Throwable $e) {
        // 记录失败时静默，避免影响业务流程。
    }
}

function ensure_upload_dir(): void
{
    global $upload_dir;
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
}

function is_admin_logged_in(): bool
{
    return !empty($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

function require_admin_login(): void
{
    if (!is_admin_logged_in()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function safe_markdown_text(string $text): string
{
    // 在前端 marked 渲染前先做 HTML 转义，避免脚本注入。
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
