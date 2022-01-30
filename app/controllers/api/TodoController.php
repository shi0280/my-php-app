<?php
require(dirname(__FILE__) . '/../../models/Todo.php');
date_default_timezone_set('Asia/Tokyo');

// todoリストファイル
const TODOLIST_FILE_NAME = "todolist.csv";
const TODOLIST_FILE_PATH = "/var/tmp/" . TODOLIST_FILE_NAME;

// ロックファイルパス
const LOCK_FILE_NAME = "lock.txt";
const LOCK_FILE_PATH = "/var/tmp/" . LOCK_FILE_NAME;
class TodoController
{
    public static function update_status()
    {
        $Result = array(
            'result' => '',
            'msg' => ''
        );

        $todo_id = $_POST['todo_id'];
        $status = $_POST['status'];

        try {
            $result = Todo::update_status($todo_id, $status);
        } catch (Exception $e) {
            $Result['result'] = false;
            $Result['msg'] = $e->getMessage();
            return $Result;
        }
        if ($result === false) {
            $Result['result'] = false;
            $Result['msg'] = "DBの保存に失敗しました。";
            return $Result;
        }
        $Result['result'] = 'sccess';
        return $Result;
    }

    public static function delete()
    {
        $Result = array(
            'result' => '',
            'msg' => ''
        );

        $todo_id = $_POST['todo_id'];

        try {
            $result = Todo::delete($todo_id);
        } catch (Exception $e) {
            $Result['result'] = false;
            $Result['msg'] = $e->getMessage();
            return $Result;
        }
        if ($result === false) {
            $Result['result'] = false;
            $Result['msg'] = "DBの保存に失敗しました。";
            return $Result;
        }
        $Result['result'] = 'sccess';
        return $Result;
    }

    public static function create_csv()
    {
        $Result = array(
            'result' => '',
            'msg' => '',
            'filename' => '',
            'created_at' => '',
        );

        $status = $_POST['status'];
        $title = $_POST['search_word'];
        $sort = $_POST['sort'];

        // $file = dirname(__FILE__) . '/../../bin/create_csv.php'; //うまくいかない
        $file = "/var/www/html/app/bin/create_csv.php";
        $cmd = 'php ' . $file . ' ' . $status . ' ' . $title . ' ' . $sort . ' > /dev/null 2>&1 &';
        exec($cmd);

        $Result['filename'] = 'download.php';
        $Result['created_at'] = date("Y/m/d H:i");
        $Result['result'] = "success";

        return $Result;
    }

    public static function export()
    {
        $filename = "download.csv";
        if (file_exists(TODOLIST_FILE_PATH) && file_exists(LOCK_FILE_PATH)) {
            try {
                header("Content-Type: text/csv");
                header('Content-Disposition: attachment; filename=' . $filename);
                header('Content-Transfer-Encoding: binary');

                // ファイル出力
                readfile(TODOLIST_FILE_PATH);
            } catch (Exception $e) {
            }
        }
    }

    public static function check_lock_file()
    {
        $Result = array(
            'result' => '',
            'msg' => ''
        );

        //ファイルチェック
        if (file_exists(LOCK_FILE_PATH)) {

            $fp = fopen(LOCK_FILE_PATH, 'r');
            $status = explode(",", fgets($fp));
            $Result['result'] = "success";
            $Result['status'] = $status;
        } else {
            $Result['result'] = false;
        }

        return $Result;
    }
}
