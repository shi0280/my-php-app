<?php
require(dirname(__FILE__) . '/../../controllers/TodoController.php');
$todos = TodoController::index();

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todos</title>
</head>

<body>
    <p style="border-bottom: 1px solid #ccc; font-weight:bold;">ToDo</p>
    <?php
    foreach ($todos as $todo) {
        echo '<a href=' . 'detail.php?todo_id=' . $todo['id'] . '> todo: ' . $todo['title'] . '</a>';
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
    }
    ?>
</body>

</html>