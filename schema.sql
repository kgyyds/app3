CREATE TABLE IF NOT EXISTS news (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255) DEFAULT '',
    author VARCHAR(100) NOT NULL,
    content MEDIUMTEXT NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=待审核,1=已通过',
    created_at DATETIME NOT NULL,
    ip VARCHAR(45) NOT NULL,
    ua VARCHAR(512) NOT NULL,
    INDEX idx_status_created (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS comments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    news_id INT UNSIGNED NOT NULL,
    author VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=普通用户,1=管理员',
    created_at DATETIME NOT NULL,
    ip VARCHAR(45) NOT NULL,
    ua VARCHAR(512) NOT NULL,
    INDEX idx_news_created (news_id, created_at),
    CONSTRAINT fk_comments_news FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS visits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip VARCHAR(45) NOT NULL,
    ua VARCHAR(512) NOT NULL,
    path VARCHAR(255) NOT NULL,
    referer VARCHAR(255) DEFAULT '',
    created_at DATETIME NOT NULL,
    INDEX idx_created_at (created_at),
    INDEX idx_path (path)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
