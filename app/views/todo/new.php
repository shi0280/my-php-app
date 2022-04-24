<?php
require(dirname(__FILE__) . '/../../controllers/TodoController.php');
session_start();

$todo = new TodoController();
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    list($input, $errors) = TodoController::new();
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = TodoController::store();
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
        <p style="border-bottom: 1px solid #ccc; font-weight:bold;">新規登録</p>
        <a href="index.php">一覧画面へ</a>
    </header>
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
        <?php if ($result !== true) : ?>
            <p class="db_error_message"><?= $result ?></p>
        <?php endif ?>
    </form>
</body>

</html>