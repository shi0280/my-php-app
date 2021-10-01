<?php
require( dirname(__FILE__) . '/BaseModel.php' );

class Todo extends BaseModel{

    public static function findAll() {
        $pdo = BaseModel::connect_db();
        $sql = 'SELECT * FROM todos WHERE user_id = :user_id';
        $stmt = $pdo->prepare($sql);
        $user_id = 1; // 仮
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT); 
        $stmt->execute();
        $todos = $stmt->fetchall(PDO::FETCH_ASSOC);

        return $todos;
    }
}
?>