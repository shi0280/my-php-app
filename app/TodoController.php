<?php
require( dirname(__FILE__) . '/config/database.php' );

class TodoController{

    public function index() {
        $pdo = connect_db();
        $sql = 'SELECT * FROM users';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchall(PDO::FETCH_ASSOC);
        
        $pdo = connect_db();
        $sql = 'SELECT * FROM todos';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $todos = $stmt->fetchall(PDO::FETCH_ASSOC);

        return [$users, $todos];
    }
}
?>