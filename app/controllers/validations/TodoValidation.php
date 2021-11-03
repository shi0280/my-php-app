<?php
require_once(dirname(__FILE__) . "/BaseValidation.php");
date_default_timezone_set('Asia/Tokyo');
class TodoValidation extends BaseValidation
{
    protected $data;

    public function check($checkData)
    {
        if ($checkData['title'] === NULL || $checkData['title'] === '') {
            $this->errors[] = "タイトルを入力してください。";
        }

        if ($checkData['deadline_at'] !== '' && preg_match('/\A[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}\z/', $checkData['deadline_at']) == false) {
            $this->errors[] =  "日付の形式が正しくありません。";
        }

        $today = date("Y/m/d");
        if ($checkData['deadline_at'] !== '' && strtotime($today) > strtotime($checkData['deadline_at'])) {
            $this->errors[] =  "過去の日が入力されています。";
        }

        if (array_key_exists('status', $checkData)) {
            if ((int)$checkData['status'] !== 0 && (int)$checkData['status'] !== 1) {
                $this->errors[] =  "完了か未完了を選択してください。";
            }
        }

        if (count($this->errors) > 0) {
            return false;
        }

        // バリデーションOKなら入力された値を保存
        $this->data['title'] = $checkData['title'];
        if ($checkData['detail'] === '') {
            $this->data['detail'] = NULL;
        } else {
            $this->data['detail'] = $checkData['detail'];
        }
        if ($checkData['deadline_at'] === '') {
            $this->data['deadline_at'] = NULL;
        } else {
            $this->data['deadline_at'] = $checkData['deadline_at'];
        }
        if (array_key_exists('status', $checkData)) {
            $this->data['status'] = $checkData['status'];
        }

        return true;
    }

    public function getData()
    {
        return $this->data;
    }
}
