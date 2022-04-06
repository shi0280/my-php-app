<?php
require(dirname(__FILE__) . '/../../controllers/TodoController.php');
$todo = TodoController::detail();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todos</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>

<body>
    <header>
        <p style="border-bottom: 1px solid #ccc; font-weight:bold;">ToDo詳細</p>
        <a href="index.php">一覧画面へ</a>
    </header>
    <?php
    echo 'todo: ' . $todo['title'];
    echo '<br>';
    echo 'detail: ' . $todo['detail'];
    echo '<br>';
    switch ($todo['status']) {
        case 0:
            echo 'status: 未完了';
            break;
        case 1:
            echo 'status: 完了';
            break;
    }
    echo '<br>';
    echo 'deadline_at: ' . $todo['deadline_at'];
    echo '<br>';
    echo 'created_at: ' . $todo['created_at'];
    echo '<br>';
    echo 'updated_at: ' . $todo['updated_at'];
    echo '<br>';
    ?>
    <button onclick="location.href='edit.php?todo_id=<?php echo $todo['id']; ?>'">編集</button>
</body>

</html>