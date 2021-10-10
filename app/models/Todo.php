<?php
require(dirname(__FILE__) . '/BaseModel.php');
class Todo extends BaseModel
{

    public static function findAll()
    {
        $pdo = parent::connect_db();
        $sql = 'SELECT * FROM todos WHERE user_id = :user_id';
        $stmt = $pdo->prepare($sql);
        $user_id = 1; // 仮
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $todos;
    }

    public static function findById($id)
    {
        $pdo = parent::connect_db();
        $sql = 'SELECT * FROM todos WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $todo = $stmt->fetch(PDO::FETCH_ASSOC);

        return $todo;
    }

    public static function store($title, $detail, $deadline_at)
    {
        $pdo = parent::connect_db();
        $sql = 'INSERT INTO todos (user_id, title, detail, status, deadline_at, created_at) VALUES (:user_id, :title, :detail, :status, :deadline_at, :created_at）';
        $stmt = $pdo->prepare($sql);

        $user_id = 1; // 仮
        $status = 0; //未完了
        $created_at = date('Y-m-d H:i:s');
        /* 確認用
        echo $user_id;
        echo $title;
        echo $detail;
        echo $status;
        echo $deadline_at;
        echo $created_at;
        */
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':detail', $detail, PDO::PARAM_STR);
        $stmt->bindValue(':status', $status, PDO::PARAM_INT);
        $stmt->bindValue(':deadline_at', $deadline_at, PDO::PARAM_STR);
        $stmt->bindValue(':created_at', $created_at, PDO::PARAM_STR);
        $stmt->execute();
    }
}
