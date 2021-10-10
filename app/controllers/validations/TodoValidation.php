<?php
require_once(dirname(__FILE__) . "/BaseValidation.php");

class TodoValidation extends BaseValidation
{

    public function check($title)
    {
        if ($title === NULL || $title === '') {
            $this->errors[] = "タイトルを入力してください。";
        }
        if (count($this->errors) > 0) {
            return false;
        }

        return true;
    }
}
