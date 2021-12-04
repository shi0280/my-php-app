<?php
require(dirname(__FILE__) . '/../models/Todo.php');
require(dirname(__FILE__) . '/validations/TodoValidation.php');

session_start();
class TodoController
{
    public static function index()
    {
        $status = $_GET['status'];
        $title = $_GET['search-word'];

        $sql_items = array();

        if ($title) {
            $title_sql = [
                'type' => 'like',
                'column' => 'title',
                'value' => $title
            ];
            $sql_items[] = $title_sql;
        }

        if ($status !== null && $status !== '') {
            $status_sql = [
                'type' => 'eq',
                'column' => 'status',
                'value' => $status
            ];
            $sql_items[] = $status_sql;
        }

        list($sql, $placeholder) = self::buildQuery($sql_items);
        $todos = Todo::findByQuery($sql, $placeholder);
        return $todos;
    }

    private static function buildQuery($sql_items)
    {
        $sql =  'SELECT * FROM todos WHERE user_id = :user_id ';
        $where = '';
        $placeholder = [];
        if ($sql_items) {
            foreach ($sql_items as $item) {
                $column = $item['column'];
                switch ($item['type']) {
                    case 'eq':
                        $type = "=";
                        $value = $item['value'];
                        break;
                    case 'like':
                        $type = "LIKE";
                        $value = "%" . $item['value'] . "%";
                        break;
                }
                $where .= ' and ' . $column . " " . $type . " :" . $column;
                $placeholder[":" . $column] = $value;
            }
            $sql .= $where;
        }
        return [$sql, $placeholder];
    }

    public static function detail()
    {
        $id = $_GET['todo_id'];
        if (empty($id)) {
            header("location: /../views/error/404.php");
            return;
        }
        $todo = Todo::findById($id);
        if (!$todo) {
            header("location: /../views/error/404.php");
            return;
        }
        return $todo;
    }

    public static function new()
    {
        // GETパラメータがあったら取得する
        $input['title'] = $_GET['title'];
        $input['detail'] = $_GET['detail'];
        $input['deadline_at'] = $_GET['deadline_at'];
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
        $checkData['title'] = $_POST['title'];
        $checkData['detail'] = $_POST['detail'];
        $checkData['deadline_at'] = $_POST['deadline_at'];

        $todoValidation = new TodoValidation;
        if (!$todoValidation->check($checkData)) {
            $_SESSION['errors']  = $todoValidation->getErrorMessages();
            header("location: /../views/todo/new.php?title=" . $checkData['title'] . "&detail=" . $checkData['detail'] . "&deadline_at=" . $checkData['deadline_at']);
            exit;
        }
        $data = $todoValidation->getData();
        $todo = new Todo();
        $todo->setTitle($data['title']);
        $todo->setDetail($data['detail']);
        $todo->setDeadline($data['deadline_at']);
        try {
            $result = $todo->save();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($result === true) {
            header("location: /../views/todo/index.php");
            return $result;
        } else {
            $result = "DBの保存に失敗しました。";
            return $result;
        }
    }

    public static function edit()
    {
        // DBからtodoを取得する
        $id = $_GET['todo_id'];
        if (empty($id)) {
            header("location: /../views/error/404.php");
            return;
        }
        $todo = Todo::findById($id);
        if (!$todo) {
            header("location: /../views/error/404.php");
            return;
        }

        // GETパラメータがあったら取得する
        $input['title'] = $_GET['title'];
        $input['detail'] = $_GET['detail'];
        $input['deadline_at'] = $_GET['deadline_at'];
        $input['status'] = $_GET['status'];

        // エラーがあったら取得する
        $errors = array();
        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
        }
        $_SESSION['errors'] = [];

        return [$todo, $input, $errors];
    }


    public static function update()
    {
        $todo_id = $_POST['todo_id'];
        // データを配列に格納
        $checkData = array();
        $checkData['title'] = $_POST['title'];
        $checkData['detail'] = $_POST['detail'];
        $checkData['deadline_at'] = $_POST['deadline_at'];
        $checkData['status'] = $_POST['status'];

        $todoValidation = new TodoValidation;
        if (!$todoValidation->check($checkData)) {
            $_SESSION['errors']  = $todoValidation->getErrorMessages();
            header("location: /../views/todo/edit.php?todo_id=" . $todo_id . "&title=" . $checkData['title'] . "&detail=" . $checkData['detail'] . "&deadline_at=" . $checkData['deadline_at'] . "&status=" . $checkData['status']);
            exit;
        }
        $data = $todoValidation->getData();
        $todo = new Todo();
        $todo->setTitle($data['title']);
        $todo->setDetail($data['detail']);
        $todo->setDeadline($data['deadline_at']);
        $todo->setStatus($data['status']);
        try {
            $result = $todo->save($todo_id);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        if ($result === true) {
            header("location: /../views/todo/index.php");
            exit;
        } else {
            $result = "DBの保存に失敗しました。";
            return $result;
        }
    }
}
