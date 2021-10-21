<?php
require(dirname(__FILE__) . '/BaseModel.php');
class Todo extends BaseModel
{
    protected $title;
    protected $detail;
    protected $deadline_at;

    public function setTitle($title)
    {
        $this->title = $title;
    }
    public function setDetail($detail)
    {
        $this->detail = $detail;
    }
    public function setDeadline($deadline_at)
    {
        $this->deadline_at = $deadline_at;
    }

    public function save()
    {
        $this->store($this->title, $this->detail, $this->deadline_at);
    }

    public static function findAll()
    {
        $pdo = parent::connect_db();
        $sql = 'SELECT * FROM todos WHERE user_id = :user_id';
        $stmt = $pdo->prepare($sql);
        $user_id = 1; // 仮
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $todos;
    }

    public static function findById($id)
    {
        $pdo = parent::connect_db();
        $sql = 'SELECT * FROM todos WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $todo = $stmt->fetch(PDO::FETCH_ASSOC);

        return $todo;
    }

    public static function store($title, $detail, $deadline_at)
    {
        $pdo = parent::connect_db();
        $sql = 'INSERT INTO todos (user_id, title, detail, status, deadline_at, created_at, updated_at) 
                VALUES (:user_id, :title, :detail, :status, :deadline_at, now(), now())';
        $stmt = $pdo->prepare($sql);

        $user_id = 1; // 仮
        $status = 0; //未完了
        $created_at = date('Y-m-d H:i:s');
        /* 確認用
        $str = sprintf(
            " INSERT INTO todos (user_id, title, detail, status, deadline_at, created_at, updated_at) 
              VALUES (%d, %s, %s, %d, %s, now(), now())",
            $user_id,
            $title,
            $detail,
            $status,
            $deadline_at
        );
        echo $str;
        */
        /* 確認用
        echo $user_id;
        echo $title;
        echo $detail;
        echo $status;
        echo $deadline_at;
        echo $created_at;
        */

        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':detail', $detail, PDO::PARAM_STR);
        $stmt->bindValue(':status', $status, PDO::PARAM_INT);
        $stmt->bindValue(':deadline_at', $deadline_at, PDO::PARAM_STR);
        $stmt->execute();
    }
}
