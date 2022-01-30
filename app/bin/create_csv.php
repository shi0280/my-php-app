<?php
require(dirname(__FILE__) . '/../models/Todo.php');
date_default_timezone_set('Asia/Tokyo');

// ロックファイルパス
const LOCK_FILE_NAME = "lock.txt";
const LOCK_FILE_PATH = "/var/tmp/" . LOCK_FILE_NAME;
const HEADER = "status,filename,count,total,updated_at";
const STATUS = array(
    'start' => 1,
    'processing' => 2,
    'end' => 3
);

// todoリストファイル
const TODOLIST_FILE_NAME = "todolist.csv";
const TODOLIST_FILE_PATH = "/var/tmp/" . TODOLIST_FILE_NAME;

$status =  $argv[1];
$title = $argv[2];
$sort = $argv[3];

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
list($sql, $placeholder) = buildQuery($type, $sql_items);
$todos = Todo::findByQuery($sql, $placeholder);


input_csvfile($todos);

function buildQuery($type, $sql_items, $page = null)
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

function input_csvfile($todos)
{

    // ファイルが存在してたら削除
    if (file_exists(TODOLIST_FILE_PATH)) {
        unlink(TODOLIST_FILE_PATH);
    }

    // todoの数
    $todos_count = Count($todos);

    $fp = fopen(TODOLIST_FILE_PATH, "a");
    if (!$fp) {
        $Result['result'] = false;
        $Result['msg'] = 'ファイルの書き込みに失敗しました。';
        return;
    }

    // todoが0件だった場合
    if (!$todos_count) {
        $text = array("該当するtodoは0件です。");
        mb_convert_variables('SJIS', 'UTF-8', $text);
        fputcsv($fp, $text);
        return;
    }
    // ロックファイル書き込み開始
    $status = STATUS['start']; // 開始
    $count = 0;
    $total = $todos_count;
    update_lock_file($status, $count, $total);
    // タイトル
    $header = array("タイトル", "説明", "ステータス", "締切", "登録日");
    mb_convert_variables('SJIS', 'UTF-8', $header);
    fputcsv($fp, $header);

    // TODO 書き込み
    sleep(20); // 確認用
    $status = STATUS['processing']; // 作成中
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
        $count++;
        if ($count  % 100 === 0) {
            // ロックファイル更新
            update_lock_file($status, $count, $total);
        }
    }
    fclose($fp);
    // ロックファイル更新
    $status  = STATUS['end']; //完了
    update_lock_file($status, $count, $total);
}

// ロックファイルを更新
function update_lock_file($status, $count, $total)
{
    $fp = fopen(LOCK_FILE_PATH, "w");
    //$header = "status,filename,count,total,updated_at" . PHP_EOL;
    // fwrite($fp, $header);
    $line = $status . "," . date("Ymd") . "_" . TODOLIST_FILE_NAME . ","
        . $count . "," . $total . "," . date("Ymd H:i:s");
    fwrite($fp, $line);
    fclose($fp);
}
