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
INSERT INTO users (name, email, password, created_at ) VALUES ('test', 'test@test.com', 'testtest',now())；
INSERT INTO todos (user_id, title, detail, status, deadline_at, created_at) VALUES (1, '資料作成', '会議で使用する資料を作成する', 0, '20210930000000', now())；

UPDATE todos SET title='資料作成' WHERE id=1;