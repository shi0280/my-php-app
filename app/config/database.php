<?php
const DB_DSN = "mysql:host=my-php-app_mysql_1; dbname=common; charset=utf8";
const DB_USER = 'shi';
const DB_PASSWORD = 'shi';

function connect_db(){
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
?>