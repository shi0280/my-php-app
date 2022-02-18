<?php
require(dirname(__FILE__) . '/../../controllers/AuthController.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $error = AuthController::login();
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todos</title>
</head>

<body>
    <h1>ログインページ</h1>
    <form action="login.php" method="post">
        <div>
            <label>メールアドレス：<label>
                    <input type="text" name="email" required>
        </div>
        <div>
            <label>パスワード：<label>
                    <input type="password" name="password" required>
        </div>
        <input type="submit" value="ログイン">
        <br />
        <?php if ($error) echo "<p>" . $error . "</p>"; ?>
    </form>
</body>

</html>