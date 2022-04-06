<?php
require(dirname(__FILE__) . '/../../controllers/TodoController.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $todo_id = $_GET['todo_id'];
    list($todo, $input, $errors) = TodoController::edit();
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = TodoController::update();
}

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
        <p style="border-bottom: 1px solid #ccc; font-weight:bold;">編集</p>
        <a href="detail.php?todo_id=<?php echo $todo_id ?>">詳細画面へ</a>
    </header>
    <form action="edit.php" method="post">
        <ul>
            <li></li><label for="title">タイトル:</label></li>
            <li><input type="text" name="title" value="<?php if (isset($input['title'])) {
                                                            echo $input['title'];
                                                        } else {
                                                            echo $todo['title'];
                                                        } ?>"></li>
            <li><label for="detail">詳細:</label></li>
            <li><textarea name="detail"><?php if (isset($input['detail'])) {
                                            echo $input['detail'];
                                        } else {
                                            echo $todo['detail'];
                                        } ?></textarea></li>
            <li><label for="deadline_at">期限:</label></li>
            <li><input type="date" name="deadline_at" value="<?php if (isset($input['deadline_at'])) {
                                                                    echo $input['deadline_at'];
                                                                } else {
                                                                    echo substr($todo['deadline_at'], 0, 10);
                                                                } ?>"></li>
            <li><input type="radio" name="status" value="0" <?php if ((int)$input['status'] !== 1 && (int)$todo['status'] !== 1) {
                                                                echo "checked";
                                                            } ?>>未完了</li>
            <li><input type="radio" name="status" value="1" <?php if ((int)$input['status'] === 1 || (int)$todo['status'] === 1) {
                                                                echo "checked";
                                                            } ?>>完了</li>
            <li><input type="hidden" name="todo_id" value="<?php echo $todo_id ?>"></li>
            <li><input type="submit" name="store" value="登録"></li>
        </ul>
        <?php if (isset($errors)) : ?>
            <?php foreach ((array)$errors as $error) : ?>
                <p class="flash_message"><?= $error ?></p>
            <?php endforeach ?>
        <?php endif ?>
        <?php if ($result !== true) : ?>
            <p class="db_error_message"><?= $result ?></p>
        <?php endif ?>
    </form>
</body>

</html>