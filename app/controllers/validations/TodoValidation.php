<?php
require_once(dirname(__FILE__) . "/BaseValidation.php");
date_default_timezone_set('Asia/Tokyo');
class TodoValidation extends BaseValidation
{
    protected $data;

    public function check($title, $detail, $deadline_at)
    {
        if ($title === NULL || $title === '') {
            $this->errors[] = "タイトルを入力してください。";
        }

        if ($deadline_at !== '' && preg_match('/\A[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}\z/', $deadline_at) == false) {
            $this->errors[] =  "日付の形式が正しくありません。";
        }

        $today = date("Y/m/d");
        if ($deadline_at !== '' && strtotime($today) > strtotime($deadline_at)) {
            $this->errors[] =  "過去の日が入力されています。";
        }

        if (count($this->errors) > 0) {
            return false;
        }

        // バリデーションOKなら入力された値を保存
        $this->data['title'] = $title;
        if ($detail === '') {
            $this->data['detail'] = NULL;
        } else {
            $this->data['detail'] = $detail;
        }
        if ($deadline_at === '') {
            $this->data['deadline_at'] = NULL;
        } else {
            $this->data['deadline_at'] = $deadline_at;
        }

        return true;
    }

    public function getData()
    {
        return $this->data;
    }
}
