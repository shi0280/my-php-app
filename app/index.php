<?php
function connect_db(){
    //ホスト名、データベース名、文字コード
    $host = 'my-php-app_mysql_1';
    $db = 'common';
    $charset = 'utf8';
    $dsn = "mysql:host=$host; dbname=$db; charset=$charset";

    //ユーザー名、パスワード
    $user = 'shi';
    $pass = 'shi';

    //オプション
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try{
        //PDOインスタンスを作成
        $pdo = new PDO($dsn, $user, $pass, $options);

    }catch(PDOException $e){
        echo $e->getMessage();
    }

    //PDOインスタンスを返す
    return $pdo;
}

$pdo = connect_db();
$sql = 'SELECT * FROM users';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$users = $stmt->fetchall();

$pdo = connect_db();
$sql = 'SELECT * FROM todos';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$todos = $stmt->fetchall();

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todos</title>
</head>
<body>
    <p style="border-bottom: 1px solid #ccc; font-weight:bold;">ユーザー</p>
    <?php
        foreach($users as $user){
            echo 'username: '. $user['name'];
            echo '<br>';
            echo 'email: '. $user['email'];
            echo '<br>';
        }
    ?>
    <p style="border-bottom: 1px solid #ccc; font-weight:bold;">ToDo</p>
    <?php
        foreach($todos as $todo){
            echo 'todo: '. $todo['title'];
            echo '<br>';
            echo 'detail: '. $todo['detail'];
            echo '<br>';
            switch ($todo['status']){
                case 0:
                    echo 'status: 未完了';
                    break;
                case 1:
                    echo 'status: 完了';
                    break;
            }
            echo '<br>';
            echo 'deadline_at: '. $todo['deadline_at'];
            echo '<br>';
            echo 'created_at: '. $todo['created_at'];
            echo '<br>';
        }
    ?>
</body>

</html>
