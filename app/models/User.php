<?php
require(dirname(__FILE__) . '/BaseModel.php');

class User extends BaseModel
{

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
