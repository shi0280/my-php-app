<?php
require(dirname(__FILE__) . '/../../controllers/UserController.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    list($input, $errors) = UserController::new_pre_user();
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = UserController::store_pre_user();
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
        <p style="border-bottom: 1px solid #ccc; font-weight:bold;">メール登録</p>
        <a href="../auth/login.php">ログイン画面へ</a>
    </header>
    <form action="registration_mail.php" method="post">
        <ul>
            <li></li><label for="email">メールアドレス:</label></li>
            <li><input type="text" name="email" value="<?php if (isset($input['email'])) {
                                                            echo $input['email'];
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