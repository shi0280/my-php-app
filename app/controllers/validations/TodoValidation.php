<?php
require_once(dirname(__FILE__) . "/BaseValidation.php");
date_default_timezone_set('Asia/Tokyo');
class TodoValidation extends BaseValidation
{

    public function check($title, $deadline_at)
    {
        if ($title === NULL || $title === '') {
            $this->errors[] = "タイトルを入力してください。";
        }

        $today = date("Y/m/d");
        if (strtotime($today) > strtotime($deadline_at)) {
            $this->errors[] = $today . "過去の日が入力されています。";
        }

        if (count($this->errors) > 0) {
            return false;
        }

        return true;
    }
}
