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
