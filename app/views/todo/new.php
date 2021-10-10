<?php
require(dirname(__FILE__) . '/../../controllers/TodoController.php');
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todos</title>
    <style>
        li {
            list-style: none;
        }
    </style>
</head>

<body>
    <p style="border-bottom: 1px solid #ccc; font-weight:bold;">新規登録</p>
    <form action="../../controllers/Todocontroller.php" method="post">
        <ul>
            <li></li><label for="title">タイトル:</label></li>
            <li><input type="text" name="title"></li>
            <li><label for="detail">詳細:</label></li>
            <li><textarea name="detail"></textarea></li>
            <li><label for="deadline_at">期限:</label></li>
            <li><input type="date" name="deadline_at"></li>
            <li><input type="submit" name="store" value="登録"></li>
        </ul>
    </form>
</body>

</html>