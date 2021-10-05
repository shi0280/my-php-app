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
}
