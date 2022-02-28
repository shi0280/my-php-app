<?php
require(dirname(__FILE__) . '/../../controllers/UserController.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    list($input, $errors) = UserController::new();
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = UserController::store();
}

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
    <header>
        <p style="border-bottom: 1px solid #ccc; font-weight:bold;">ユーザー新規登録</p>
        <a href="../auth/login.php">ログイン画面へ</a>
    </header>
    <form action="new.php" method="post">
        <ul>
            <li></li><label for="name">名前:</label></li>
            <li><input type="text" name="name" value="<?php if (isset($input['name'])) {
                                                            echo $input['name'];
                                                        } ?>"></li>

            <li></li><label for="email">メールアドレス:</label></li>
            <li><input type="text" name="email" value="<?php if (isset($input['email'])) {
                                                            echo $input['email'];
                                                        } ?>"></li>

            <li></li><label for="password">パスワード:</label></li>
            <li><input type="password" name="pass" value="<?php if (isset($input['pass'])) {
                                                                echo $input['pass'];
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