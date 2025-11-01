<?php

class UserSession extends DbConnection
{
    public $id;
    public $user_id;
    public $token;
    public $expires_at;

    public function __construct($db, $user_id, $token)
    {
        $this->conn = $db;
        $this->user_id = $user_id;
        $this->token = $token;
    }


    public function save()
    {
        $token = bin2hex(random_bytes(32));
        $now = new DateTime();
        $now->modify('+7 days');
        $expiry = $now->format('Y-m-d H:i:s');

        $stmt = $this->conn->prepare(
            "INSERT INTO " . DBTables::$sessions . " 
        (user_id, token, expires_at) 
        VALUES (?, ?, ?)"
        );


        $stmt->execute([$this->user_id, $token, $expiry]);

        $session = $stmt->fetch();
        if ($session) {
            return $token;
        }

        return null;
    }

    public function get_token()
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM " . DBTables::$sessions .
            " WHERE user_id = ? AND expires_at > DATETIME('now') "
        );
        $stmt->execute([$this->user_id]);

        $session = $stmt->fetch();

        if ($session) {
            return $session['token'];
        }
        return null;
    }

    public function verify_token()
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM " . DBTables::$sessions .
            " WHERE token = ? AND expires_at > DATETIME('now')"
        );
        $stmt->execute([$this->token]);

        $session = $stmt->fetch();
        if ($session) {
            return $session['user_id'];
        }
        return null;
    }

    public function remove_token()
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM " . DBTables::$sessions . "  WHERE user_id = ?"
        );

        $result = $stmt->execute([$this->user_id]);
        if ($result) {
            return true;
        }
        return false;
    }
}