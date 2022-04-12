<?php
require(dirname(__FILE__) . '/../models/User.php');
require(dirname(__FILE__) . '/validations/UserValidation.php');
require(dirname(__FILE__) . '/../service/mailService.php');

session_start();
class UserController
{
    public static function new()
    {
        // トークンを取得
        $token = '';
        if (isset($_GET['token'])) {
            $token = $_GET['token'];
        }

        // トークンがない場合ログイン画面へ
        $user = User::getUserByToken($token);
        if (!$token || !$user) {
            header("location: /../views/auth/login.php");
        }

        // トークン発行から1時間以上経過したらメール登録ページへ
        $now =  new DateTime('now');
        $token_created_at = new DateTime($user['token_created_at']);
        $token_duration = $now->diff($token_created_at);
        $token_duration_hour =  $token_duration->days * 24 + $token_duration->h;
        if ($token_duration_hour >= 1) {
            header("location: /../views/user/registration_mail.php");
        };

        // GETパラメータがあったら取得する
        $input['name'] = $_GET['name'];
        $input['pass'] = $_GET['pass'];
        $input['pass-confirm'] = $_GET['pass-confirm'];
        // エラーがあったら取得する
        $errors = array();
        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
        }
        $_SESSION['errors'] = [];
        return [$input, $errors, $token];
    }

    public static function store()
    {
        // トークンを取得
        $token = $_POST['token'];
        $token;
        // データを配列に格納
        $checkData = array();
        $checkData['name'] = $_POST['name'];
        $checkData['pass'] = $_POST['pass'];
        $checkData['pass-confirm'] = $_POST['pass-confirm'];
        $userValidation = new UserValidation;
        if (!$userValidation->check($checkData)) {
            $_SESSION['errors']  = $userValidation->getErrorMessages();
            header("location: /../views/user/new.php?token=" . $token . "&name=" . $checkData['name'] . "&pass=" . $checkData['pass']);
            exit;
        }
        $data = $userValidation->getData();
        $user = new User();
        $user->setName($data['name']);
        $user->setPass($data['pass']);
        $user->setToken($token);
        try {
            $result = $user->save();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($result === true) {
            header("location: /../views/todo/index.php");
            return $result;
        } else {
            return $result;
        }
    }

    public static function new_pre_user()
    {
        // GETパラメータがあったら取得する
        $input['email'] = $_GET['email'];
        // エラーがあったら取得する
        $errors = array();
        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
        }
        $_SESSION['errors'] = [];
        return [$input, $errors];
    }

    public static function store_pre_user()
    {
        $email = $_POST['email'];

        $userValidation = new UserValidation;
        if (!$userValidation->check_pre_user($email)) {
            $_SESSION['errors']  = $userValidation->getErrorMessages();
            header("location: /../views/user/registration_mail.php?email=" . $email);
            exit;
        }

        $data = $userValidation->getData();
        $user = new User();
        $user->setMail($data['email']);
        // トークンをユニークな文字列で生成
        $token = uniqid(mt_rand());
        $user->setToken($token);

        try {
            $result = $user->save_pre_user();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($result === true) {
            $content = "1時間以内に以下に記載されたURLからご登録下さい。\r\n" .
                "http://127.0.0.1:8000/views/user/new.php?token=" . $token;
            mailService::send($data['email'], $content);
            header("location: /../views/user/registration_mail_check.php");
            return $result;
        } else {
            return $result;
        }
    }
}
