<?php
require(dirname(__FILE__) . '/../models/User.php');
require(dirname(__FILE__) . '/validations/UserValidation.php');
session_start();
class UserController
{
    public static function new()
    {
        // GETパラメータがあったら取得する
        $input['name'] = $_GET['name'];
        $input['pass'] = $_GET['pass'];
        // エラーがあったら取得する
        $errors = array();
        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
        }
        $_SESSION['errors'] = [];
        return [$input, $errors];
    }

    public static function store($token)
    {
        // データを配列に格納
        $checkData = array();
        $checkData['name'] = $_POST['name'];
        $checkData['pass'] = $_POST['pass'];
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
        $user->setToken($data['token']);
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
            self::send_mail($data['email'], $content);
            header("location: /../views/user/registration_mail_check.php");
            return $result;
        } else {
            return $result;
        }
    }

    public static function send_mail($email, $content)
    {
        mb_language("ja");
        mb_internal_encoding("UTF-8");

        $from = 'from@todo_app.com';
        $header = "From: $from \n";
        $header = $header
            . "MIME-Version: 1.0\r\n"
            . "Content-Transfer-Encoding: 8bit\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n";
        $subject = 'アカウント仮登録';
        $body = $content;

        $body = mb_convert_encoding($body, "UTF-8");

        $ret = mb_send_mail($email, $subject, $body, $header);
    }
}
