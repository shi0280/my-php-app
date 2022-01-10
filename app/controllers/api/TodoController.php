<?php
require(dirname(__FILE__) . '/../../models/Todo.php');
date_default_timezone_set('Asia/Tokyo');
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

        $file = dirname(__FILE__) . '/../../bin/create_csv.php';
        $cmd = 'nohup php ' . $file . ' ' . $status . ' ' . $title . ' ' . $sort . ' > /dev/null 2>&1 &';
        exec($cmd);

        $Result['filename'] = 'download.php';
        $Result['created_at'] = date("Y/m/d H:i");
        $Result['result'] = 'sccess';

        return $Result;
    }



    public static function export()
    {
        $Result = array(
            'result' => '',
            'msg' => ''
        );

        $filepath = "../../var/tmp/todos.csv";
        $filename = "download.csv";
        try {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Content-Transfer-Encoding: binary');

            // ファイル出力
            readfile($filepath);
        } catch (Exception $e) {
            $Result['result'] = 'false';
            $Result['msg'] = $e->getMessage();
        }

        $Result['result'] = 'sccess';
        return $Result;
    }
}
