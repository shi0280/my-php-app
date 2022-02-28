<?php
require(dirname(__FILE__) . '/BaseModel.php');

class User extends BaseModel
{
    protected $name;
    protected $email;
    protected $pass;

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

    public function save()
    {
        $result = $this->store($this->name, $this->email, $this->pass);

        return $result;
    }
    public static function store($name, $email, $pass)
    {
        $hash_pass = password_hash($pass, PASSWORD_DEFAULT);

        try {
            $pdo = parent::connect_db();

            $sql = 'SELECT COUNT(*) FROM users WHERE email = :email';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            if ($count > 0) {
                return "メールアドレスは登録済みです";
            }

            $sql = 'INSERT INTO users (name, email, password, created_at, updated_at) 
                    VALUES (:name, :email, :password, now(), now())';
            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $hash_pass, PDO::PARAM_STR);
            $res = $stmt->execute();

            if (!$res) {
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

    public static function login($email)
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
}
