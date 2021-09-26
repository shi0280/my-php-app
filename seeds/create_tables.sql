CREATE TABLE common.users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    created_at DATETIME,
    updated_at DATETIME,
    deleted_at DATETIME
);

CREATE TABLE common.todos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    detail TEXT,
    status TINYINT NOT NULL DEFAULT 0,
    deadline_at DATETIME,
    created_at DATETIME,
    updated_at DATETIME
);

-- ダミーデータ
INSERT INTO users (name, email, password, created_at ) VALUES ('testユーザー', 'test@test.com', 'testtest',now());
INSERT INTO todos (user_id, title, detail, status, deadline_at, created_at) VALUES (1, 'テスト', 'テストテスト', 0, '20210930000000', now());

UPDATE todos SET title='テスト' WHERE id=1;