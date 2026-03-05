# 移动端优先新闻发布系统（Cyber 风格）

## 运行要求
- PHP 8+
- MySQL 5.7+/8+

## 快速部署
1. 创建数据库并导入表结构：
   ```bash
   mysql -u root -p 33app_top < schema.sql
   ```
2. 检查 `config.php` 中数据库账号、管理员密码是否符合环境。
3. 使用 PHP 内置服务器运行：
   ```bash
   php -S 0.0.0.0:8000
   ```
4. 访问：`http://localhost:8000`

## 目录结构
- `index.php` 首页新闻看板
- `post.php` 投稿页面（支持 Markdown 与图片上传）
- `news.php` 新闻详情与评论
- `comment.php` 评论提交入口
- `admin/login.php` 管理员登录
- `admin/dashboard.php` 后台总览（访问日志）
- `admin/review.php` 新闻审核（通过/删除）
- `css/style.css` 黑客风移动端样式
- `js/main.js` Markdown 渲染逻辑
- `schema.sql` 数据库建表 SQL

## 安全策略
- 全站 PDO 预处理语句防 SQL 注入
- 输出统一 `htmlspecialchars`
- Markdown 内容先转义后再渲染
- 上传严格限制类型和大小（<=5MB）
- 文件名随机化并固定到 `/uploads/`
- 全页面访问写入 `visits` 日志
