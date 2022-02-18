<?php
require_once(dirname(__FILE__) . "/BaseValidation.php");
class UserValidation extends BaseValidation
{
    protected $data;

    public function check($checkData)
    {
        if (!$checkData['email']) {
            $this->errors[] = "メールアドレスを入力してください。";
        }

        if (filter_var($checkData['email'],  FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "メールアドレスの形式が正しくありません。";
        }

        if (!$checkData['password']) {
            $this->errors[] = "パスワード入力してください。";
        }

        if (count($this->errors) > 0) {
            return false;
        }

        // バリデーションOKなら入力された値を保存
        $this->data['email'] = $checkData['email'];
        $this->data['password'] = $checkData['password'];

        return true;
    }
}
