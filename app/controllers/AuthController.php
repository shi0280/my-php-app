<?php
require(dirname(__FILE__) . '/../models/User.php');

session_start();

class AuthController
{
    public static function login_form()
    {
        // エラーがあったら取得する
        $error = '';
        if (isset($_SESSION['errors'])) {
            $error = $_SESSION['errors'];
        }
        $_SESSION['errors'] = [];
        return $error;
    }

    public static function login()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = User::getUserByEmail($email);

        if ($user) {
            if ($user['status'] == 0) {
                $error = "本登録してください。";
                return $error;
            }
            //パスワードマッチしているかチェック
            else if (password_verify($password, $user['password'])) {
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

    public static function logout()
    {
        $_SESSION = array();
        session_destroy();
    }
}
