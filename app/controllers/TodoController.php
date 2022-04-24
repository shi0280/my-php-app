<?php

require(dirname(__FILE__) . '/../models/Todo.php');
require(dirname(__FILE__) . '/validations/TodoValidation.php');

session_start();

// ロックファイルパス
const LOCK_FILE_NAME = "lock.txt";
const LOCK_FILE_PATH = "/var/tmp/" . LOCK_FILE_NAME;
const STATUS = array(
    'start' => 1,
    'processing' => 2,
    'end' => 3
);
class TodoController
{
    public function __construct()
    {
        if (!isset($_SESSION['id'])) {
            $_SESSION['errors'] = 'ログインしてください。';
            header("location: /../views/auth/login.php");
        }
    }

    public static function index()
    {
        $pagination_items = array();
        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = $_GET['page'];
        } else {
            $page = 1;
        }
        $pagination_items['page'] = $page;

        $status = $_GET['status'];
        $title = $_GET['search-word'];
        $sort = $_GET['sort'];

        $sql_items = array();

        if ($title) {
            $sql_item = [
                'type' => 'like',
                'column' => 'title',
                'value' => $title
            ];
            $sql_items[] = $sql_item;
        }

        if ($status !== null && $status !== '') { // if($status)にすると0の場合を受け取れない。
            $sql_item = [
                'type' => 'eq',
                'column' => 'status',
                'value' => $status
            ];
            $sql_items[] = $sql_item;
        }

        if ($sort) {
            $sort_val = explode(",", $sort);
            $sql_item = [
                'type' => 'sort',
                'column' => $sort_val[0],
                'value' => $sort_val[1]
            ];
            $sql_items[] = $sql_item;
        }
        $type = 'select';
        list($sql, $placeholder) = self::buildQuery($type, $sql_items, $pagination_items['page']);
        $todos = Todo::findByQuery($sql, $placeholder);

        $type = 'count';
        list($count_sql, $placeholder) = self::buildQuery($type, $sql_items);
        $count = Todo::findByQuery($count_sql, $placeholder);
        $max_page = ceil($count[0]['cnt'] / 5);
        $pagination_items['max_page'] = $max_page;

        $from_record = ($page - 1) * Todo::LIMIT + 1;
        if ($page == $max_page && $count[0]['cnt'] % Todo::LIMIT !== 0) {
            $to_record = ($page - 1) * Todo::LIMIT + $count[0]['cnt'] % Todo::LIMIT;
        } else {
            $to_record = $page * Todo::LIMIT;
        }
        $pagination_items['count'] = $count[0]['cnt'];
        $pagination_items['from_record'] = $from_record;
        $pagination_items['to_record'] = $to_record;

        $isCreatingCsv = self::check_isCreatingCsv();

        return [$todos, $pagination_items, $isCreatingCsv];
    }

    private static function buildQuery($type, $sql_items, $page = null)
    {
        $sql = '';
        $limit = '';
        if ($type === 'select') {
            $sql =  'SELECT * FROM todos WHERE user_id = :user_id ';
        } else if ($type === 'count') {
            $sql = 'SELECT COUNT(*) as cnt FROM todos WHERE user_id = :user_id';
        }
        $where = '';
        $order = '';
        $placeholder = [];
        if ($sql_items) {
            foreach ($sql_items as $item) {
                switch ($item['type']) {
                    case 'eq':
                        $column = $item['column'];
                        $type = "=";
                        $value = $item['value'];
                        $where .= " and " . $column . " " . $type . " :" . $column;
                        $placeholder[":" . $column] = $value;
                        break;
                    case 'like':
                        $column = $item['column'];
                        $type = "LIKE";
                        $value = "%" . $item['value'] . "%";
                        $where .= " and " . $column . " " . $type . " :" . $column;
                        $placeholder[":" . $column] = $value;
                        break;
                    case 'sort':
                        $column = $item['column'];
                        $type = "ORDER BY";
                        $value = $item['value'];
                        $order .= " " . $type . " "  . $column . " " . $value;
                }
            }
            $sql .= $where . " " . $order;
        }
        if ($page !== null) {
            $limit = 'LIMIT ' . (($page - 1) * Todo::LIMIT) . ',' . Todo::LIMIT;
        }
        $sql .= " " . $limit;
        return [$sql, $placeholder];
    }

    // csv作成中かどうか
    private static function check_isCreatingCsv()
    {
        //ファイルチェック
        if (file_exists(LOCK_FILE_PATH)) {
            $fp = fopen(LOCK_FILE_PATH, 'r');
            $status = explode(",", fgets($fp)); // 
            if ($status[0] == STATUS["end"]) {
                return false;
            } else {
                return true;     // lockファイルが存在する && statusがstartまたはprocessingの場合は作成中
            }
            $Result['status'] = STATUS["end"];
        } else {
            return false;
        }
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

    private static function download_csv($todos)
    {
        header('Content-Disposition: attachment; filename="todolist.csv"');
        $header = array("タイトル", "説明", "ステータス", "締切", "登録日");
        echo mb_convert_encoding(implode(",", $header), 'SJIS-win', "UTF-8") . "\n";
        foreach ($todos as $todo) {
            foreach ($todo as $key => $value) {
                if ($key === 'id' || $key === 'user_id' || $key === 'updated_at') {
                    continue;
                }
                if ($key === 'status') {
                    if ($value == 0) {
                        echo mb_convert_encoding('未完了' . ",", 'SJIS-win', "UTF-8");
                    } else {
                        echo mb_convert_encoding('完了' . ",", 'SJIS-win', "UTF-8");
                    }
                    continue;
                }
                echo mb_convert_encoding($value . ",", 'SJIS-win', "UTF-8");
            }
            echo "\n";
        }
        exit;
    }
}
