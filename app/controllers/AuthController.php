<?php
require(dirname(__FILE__) . '/../models/User.php');

session_start();

class AuthController
{
    public static function login()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = User::login($email);

        if ($user) {
            //パスワードマッチしているかチェック
            if ($password === $user['password']) {
                //DBのユーザー情報をセッションに保存
                $_SESSION['id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                header("location: /../views/todo/index.php");
                return;
            }
        }
        $error = "メールアドレスまたはパスワードが違います。";
        return $error;
    }
}
