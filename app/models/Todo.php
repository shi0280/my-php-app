<?php
require(dirname(__FILE__) . '/BaseModel.php');
class Todo extends BaseModel
{
    protected $id;
    protected $title;
    protected $detail;
    protected $deadline_at;
    protected $status;

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
    public function setStatus($status)
    {
        $this->status = $status;
    }


    public function save($id = null)
    {
        if ($id == null) {
            $result = $this->store($this->title, $this->detail, $this->deadline_at);
        } else {
            $result = $this->update($id, $this->title, $this->detail, $this->deadline_at, $this->status);
        }

        if ($result === true) {
            return true;
        } else {
            return $result;
        }
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

        $pdo = null;

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

        $pdo = null;

        return $todo;
    }

    public static function findByQuery($sql)
    {
        $pdo = parent::connect_db();
        $stmt = $pdo->prepare($sql);
        $user_id = 1; // 仮
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $todos = $stmt->fetchALL(PDO::FETCH_ASSOC);

        $pdo = null;

        return $todos;
    }

    public static function store($title, $detail, $deadline_at)
    {
        try {
            $pdo = parent::connect_db();
            $sql = 'INSERT INTO todos (user_id, title, detail, status, deadline_at, created_at, updated_at) 
                    VALUES (:user_id, :title, :detail, :status, :deadline_at, now(), now())';
            $stmt = $pdo->prepare($sql);

            $user_id = 1; // 仮
            $status = 0; //未完了

            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':title', $title, PDO::PARAM_STR);
            $stmt->bindValue(':detail', $detail, PDO::PARAM_STR);
            $stmt->bindValue(':status', $status, PDO::PARAM_INT);
            $stmt->bindValue(':deadline_at', $deadline_at, PDO::PARAM_STR);
            $res = $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException("DBエラーです");
            return false;
        } finally {
            // データベースの接続解除
            $pdo = null;
        }

        return $res; // 成功true 失敗false
    }

    public static function update($todo_id, $title, $detail, $deadline_at, $status)
    {
        try {
            $pdo = parent::connect_db();
            // トランザクション開始
            $pdo->beginTransaction();

            $sql = 'UPDATE todos SET user_id=:user_id, title=:title, detail=:detail, deadline_at=:deadline_at, status=:status, updated_at=now()
                    WHERE id=:id';
            $stmt = $pdo->prepare($sql);

            $user_id = 1; // 仮

            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':title', $title, PDO::PARAM_STR);
            $stmt->bindValue(':detail', $detail, PDO::PARAM_STR);
            $stmt->bindValue(':deadline_at', $deadline_at, PDO::PARAM_STR);
            $stmt->bindValue(':status', $status, PDO::PARAM_INT);
            $stmt->bindValue(':id', $todo_id, PDO::PARAM_INT);
            $res = $stmt->execute();
            // 成功したらコミット
            if ($res) {
                $pdo->commit();
            }
        } catch (PDOException $e) {
            // ロールバック
            $pdo->rollBack();
            throw new Exception("DBエラーです");
            return false;
        } finally {
            // データベースの接続解除
            $pdo = null;
        }

        return $res; // 成功true 失敗false
    }

    public static function update_status($todo_id, $status)
    {
        try {
            $pdo = parent::connect_db();
            // トランザクション開始
            $pdo->beginTransaction();

            $sql = 'UPDATE todos SET status=:status, updated_at=now() WHERE id=:id';
            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':status', $status, PDO::PARAM_INT);
            $stmt->bindValue(':id', $todo_id, PDO::PARAM_INT);
            $res = $stmt->execute();
            // 成功したらコミット
            if ($res) {
                $pdo->commit();
            }
        } catch (PDOException $e) {
            // ロールバック
            $pdo->rollBack();
            throw new Exception("DBエラーです");
            return false;
        } finally {
            // データベースの接続解除
            $pdo = null;
        }

        return $res; // 成功true 失敗false
    }

    public static function delete($todo_id)
    {
        try {
            $pdo = parent::connect_db();
            // トランザクション開始
            $pdo->beginTransaction();

            $sql = 'DELETE FROM todos WHERE id=:id';
            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':id', $todo_id, PDO::PARAM_INT);
            $res = $stmt->execute();
            // 成功したらコミット
            if ($res) {
                $pdo->commit();
            }
        } catch (PDOException $e) {
            // ロールバック
            $pdo->rollBack();
            throw new Exception("DBエラーです");
            return false;
        } finally {
            // データベースの接続解除
            $pdo = null;
        }

        return $res; // 成功true 失敗false
    }
}
