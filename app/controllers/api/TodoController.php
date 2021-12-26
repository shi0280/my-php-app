<?php
require(dirname(__FILE__) . '/../../models/Todo.php');

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


    public static function download()
    {
        // データを配列に格納
        $checkData = array();
        $checkData['title'] = $_POST['title'];
        $checkData['detail'] = $_POST['detail'];
        $checkData['deadline_at'] = $_POST['deadline_at'];
        $checkData['status'] = $_POST['status'];

        // try {
        //     $result = Todo::delete($todo_id);
        // } catch (Exception $e) {
        //     $Result['result'] = false;
        //     $Result['msg'] = $e->getMessage();
        //     return $Result;
        // }
        // if ($result === false) {
        //     $Result['result'] = false;
        //     $Result['msg'] = "DBの保存に失敗しました。";
        //     return $Result;
        // }
        // $Result['result'] = 'sccess';
        // return $Result;
    }
}
