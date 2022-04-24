<?php
require(dirname(__FILE__) . '/../models/User.php');

const LOGIN_FAILED_LIMIT = 3;
const LOCKED_HOUR = 1;
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
        // メールアドレスがない場合
        if (!$user) {
            $error = "メールアドレスまたはパスワードが違います。";
            return $error;
        }
        // 仮登録の場合
        if ($user['status'] == 0) {
            $error = "本登録してください。";
            return $error;
        }

        // ロックされているかチェック
        if ($user['is_locked']) {
            $now =  new DateTime('now');
            $locked_time = new DateTime($user['locked_time']);
            $locked_time_diff = $now->diff($locked_time);
            $locked_time_diff_hour =  $locked_time_diff->days * 24 + $locked_time_diff->h;
            if ($locked_time_diff_hour < LOCKED_HOUR) {
                $error = "アカウントがロックされています。時間を空けてログインしてください。";
                return $error;
            } else {
                // 1時間以上経過していたらロック解除
                User::unlock_login_account($email);
            }
        }
        //パスワードが一致していない場合
        if (!password_verify($password, $user['password'])) {
            User::login_failed_count_up($email);
            $login_failed_count = User::get_login_failed_count($email);
            if ($login_failed_count >= LOGIN_FAILED_LIMIT) {
                User::lock_login_account($email);
            }
            $error = "メールアドレスまたはパスワードが違います。";
            return $error;
        }

        // ログイン → DBのユーザー情報をセッションに保存
        $_SESSION['id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        header("location: /../views/todo/index.php");
    }

    public static function logout()
    {
        $_SESSION = array();
        session_destroy();
    }
}
