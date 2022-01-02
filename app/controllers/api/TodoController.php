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
        list($sql, $placeholder) = self::buildQuery($type, $sql_items);
        $todos = Todo::findByQuery($sql, $placeholder);

        // csv出力
        $csv_file_name = "todos.csv";
        $csv_file_path = "../../var/tmp/" . $csv_file_name;
        self::input_csvfile($csv_file_path, $todos);

        $Result['filename'] = $csv_file_name;
        $Result['created_at'] = date("Y/m/d H:i");
        $Result['result'] = 'sccess';

        return $Result;
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

    private static function input_csvfile($csv_file_path, $todos)
    {

        // ファイルが存在してたら削除
        if (file_exists($csv_file_path)) {
            unlink($csv_file_path);
        }

        $fp = fopen($csv_file_path, "a");
        if (!$fp) {
            $Result['result'] = false;
            $Result['msg'] = 'ファイルの書き込みに失敗しました。';
        }

        // タイトル
        $header = array("タイトル", "説明", "ステータス", "締切", "登録日");
        mb_convert_variables('SJIS', 'UTF-8', $header);
        fputcsv($fp, $header);

        foreach ($todos as $todo) {
            $line = '';
            foreach ($todo as $key => $value) {
                if ($key === 'id' || $key === 'user_id' || $key === 'updated_at') {
                    continue;
                }
                if ($key === 'status') {
                    if ($value == 0) {
                        $line .= '未完了' . ",";
                    } else {
                        $line .= '完了' . ",";
                    }
                } else {
                    $line .= $value . ",";
                }
            }
            mb_convert_variables('SJIS', 'UTF-8', $line);
            $line = rtrim($line, ',');
            fwrite($fp, $line . "\n");
        }
        fclose($fp);
    }
}
