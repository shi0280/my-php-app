<?php
require(dirname(__FILE__) . '/../../controllers/TodoController.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $input = TodoController::new();
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    TodoController::store();
}
if (isset($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
}
//unset($_SESSION['errors']);

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
    <form action="new.php" method="post">
        <ul>
            <li></li><label for="title">タイトル:</label></li>
            <li><input type="text" name="title" value="<?php if (isset($input['title'])) {
                                                            echo $input['title'];
                                                        } ?>"></li>
            <li><label for="detail">詳細:</label></li>
            <li><textarea name="detail"><?php if (isset($input['detail'])) {
                                            echo $input['detail'];
                                        } ?></textarea></li>
            <li><label for="deadline_at">期限:</label></li>
            <li><input type="date" name="deadline_at" value="<?php if (isset($input['deadline_at'])) {
                                                                    echo $input['deadline_at'];
                                                                } ?>"></li>
            <li><input type="submit" name="store" value="登録"></li>
        </ul>
        <?php if (isset($errors)) : ?>
            <?php foreach ((array)$errors as $error) : ?>
                <p class="flash_message"><?= $error ?></p>
            <?php endforeach ?>
        <?php endif ?>
    </form>
</body>

</html>