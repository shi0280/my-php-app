<?php
require( dirname(__FILE__) . '/../config/database.php' );

class Todo{
    public function connect_db(){
        //オプション
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try{
            //PDOインスタンスを作成
            $pdo = new PDO(DB_DSN, DB_USER, DB_USER, $options);
    
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    
        //PDOインスタンスを返す
        return $pdo;
    }

    public function findAll() {
        $pdo = $this->connect_db();
        $sql = 'SELECT * FROM users';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchall(PDO::FETCH_ASSOC);
        
        $pdo = $this->connect_db();
        $sql = 'SELECT * FROM todos';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $todos = $stmt->fetchall(PDO::FETCH_ASSOC);

        return [$users, $todos];
    }
}
?>