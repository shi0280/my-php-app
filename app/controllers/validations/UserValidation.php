<?php
require_once(dirname(__FILE__) . "/BaseValidation.php");
class UserValidation extends BaseValidation
{
    protected $data;

    public function check_pre_user($email)
    {
        if (!$email) {
            $this->errors[] = "メールアドレスを入力してください。";
        } else if (mb_strlen($email) > 100) {
            $this->errors[] = "メールアドレスを100文字以内で入力してください。";
        } else if (!filter_var($email,  FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "メールアドレスの形式が正しくありません。";
        }

        if (count($this->errors) > 0) {
            return false;
        }

        // バリデーションOKなら入力された値を保存
        $this->data['email'] = $email;

        return true;
    }

    public function check($checkData)
    {
        if (!$checkData['name']) {
            $this->errors[] = "名前を入力してください。";
        } else if (mb_strlen($checkData['name']) > 50) {
            $this->errors[] = "名前を50文字以内で入力してください。";
        }

        if (!$checkData['pass']) {
            $this->errors[] = "パスワードを入力してください。";
        } else if (mb_strlen($checkData['pass']) > 100) {
            $this->errors[] = "パスワードを100文字以内で入力してください。";
        } else if (!preg_match('/^[a-z0-9]{6,}$/i', $checkData['pass'])) {
            $this->errors[] = "パスワードは英数字6文字以上です。";
        } else if ($checkData['pass'] !== $checkData['pass-confirm']) {
            $this->errors[] = "パスワードが一致しません。";
        }

        if (count($this->errors) > 0) {
            return false;
        }

        // バリデーションOKなら入力された値を保存
        $this->data['name'] = $checkData['name'];
        $this->data['pass'] = $checkData['pass'];

        return true;
    }


    public function getData()
    {
        return $this->data;
    }
}
