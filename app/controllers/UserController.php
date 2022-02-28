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
        $input['email'] = $_GET['email'];
        $input['pass'] = $_GET['pass'];
        // エラーがあったら取得する
        $errors = array();
        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
        }
        $_SESSION['errors'] = [];
        return [$input, $errors];
    }

    public static function store()
    {
        // データを配列に格納
        $checkData = array();
        $checkData['name'] = $_POST['name'];
        $checkData['email'] = $_POST['email'];
        $checkData['pass'] = $_POST['pass'];

        $userValidation = new UserValidation;
        if (!$userValidation->check($checkData)) {
            $_SESSION['errors']  = $userValidation->getErrorMessages();
            header("location: /../views/user/new.php?name=" . $checkData['name'] . "&email=" . $checkData['email'] . "&pass=" . $checkData['pass']);
            exit;
        }
        $data = $userValidation->getData();
        $user = new User();
        $user->setName($data['name']);
        $user->setMail($data['email']);
        $user->setPass($data['pass']);
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
}
