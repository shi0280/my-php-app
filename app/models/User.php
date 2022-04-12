<?php
require(dirname(__FILE__) . '/BaseModel.php');

const PREUSER_NAME = "pre_user";
const PREUSER_PASS = "pre_pass";
const STATUS = array(
    'PREUSER' => 0,
    'USER' => 1
);
class User extends BaseModel
{
    protected $name;
    protected $email;
    protected $pass;
    protected $token;

    public function setName($name)
    {
        $this->name = $name;
    }
    public function setMail($email)
    {
        $this->email = $email;
    }
    public function setPass($pass)
    {
        $this->pass = $pass;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function save_pre_user()
    {
        $result = $this->store_pre_user($this->email, $this->token);
        return $result;
    }

    public static function store_pre_user($email, $token)
    {
        // 新規登録かどうか
        $is_new_user = false;

        try {
            $pdo = parent::connect_db();
            // トランザクション開始
            $pdo->beginTransaction();

            $sql = 'SELECT * FROM users WHERE email = :email';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                $is_new_user = true;
            } else if ($user['status'] === STATUS['USER']) {
                return "メールアドレスは登録済みです";
            } else if ($user['status'] === STATUS['PREUSER']) {
                $is_new_user = false;
            }
            // 仮登録未済だったら登録、仮登録済みだったら更新
            if ($is_new_user) {
                $sql = 'INSERT INTO users (name, email, password, created_at, updated_at, status, token, token_created_at) 
                        VALUES (:name, :email, :password, now(), now(), :status, :token, now())';
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':name', PREUSER_NAME, PDO::PARAM_STR);
                $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                $stmt->bindValue(':password', PREUSER_PASS, PDO::PARAM_STR);
                $stmt->bindValue(':status', STATUS['PREUSER'], PDO::PARAM_STR);
                $stmt->bindValue(':token', $token, PDO::PARAM_STR);
            } else {
                $sql = 'UPDATE users SET token=:token, updated_at=now(), token_created_at=now() WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':token', $token, PDO::PARAM_STR);
                $stmt->bindValue(':id', $user['id'], PDO::PARAM_STR);
            }
            $res = $stmt->execute();

            if ($res) {
                $pdo->commit();
            } else {
                throw new Exception("DBに登録できませんでした");
            }
        } catch (PDOException $e) {
            throw new PDOException("DBエラーです");
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        } finally {
            // データベースの接続解除
            $pdo = null;
        }

        return $res; // 成功true 
    }


    public function save()
    {
        $result = $this->store($this->name, $this->pass, $this->token);
        return $result;
    }

    public static function store($name, $pass, $token)
    {
        $hash_pass = password_hash($pass, PASSWORD_DEFAULT);
        $status = STATUS['USER'];

        try {
            $pdo = parent::connect_db();
            // トランザクション開始
            $pdo->beginTransaction();

            $sql = 'UPDATE users SET name=:name, password=:password, status=:status, updated_at=now() WHERE token=:token';
            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':password', $hash_pass, PDO::PARAM_STR);
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
            $stmt->bindValue(':token', $token, PDO::PARAM_STR);
            $res = $stmt->execute();

            if ($res) {
                $pdo->commit();
            } else {
                throw new Exception("登録に失敗しました。");
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            return $e->getMessage();
        } catch (Exception $e) {
            $pdo->rollBack();
            return $e->getMessage();
        } finally {
            // データベースの接続解除
            $pdo = null;
        }

        return $res; // 成功true 
    }


    public static function getUserByEmail($email)
    {
        try {
            $pdo = parent::connect_db();
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("DBエラーです");
            return false;
        } finally {
            // データベースの接続解除
            $pdo = null;
        }
        return $user;
    }

    public static function getUserByToken($token)
    {
        try {
            $pdo = parent::connect_db();
            $sql = "SELECT * FROM users WHERE token = :token AND status = :status";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':token', $token);
            $status = STATUS['PREUSER']; // 仮登録のみ
            $stmt->bindValue(':status', $status);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("DBエラーです");
            return false;
        } finally {
            // データベースの接続解除
            $pdo = null;
        }

        return $user;
    }
}
