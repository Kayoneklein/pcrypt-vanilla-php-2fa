<?php
require_once CORE_PATH . DS . 'app.php';



class UserController
{
    public static function register($body)
    {
        global $pdo;
        $user = new User($pdo);

        $user->email = $body['email'] ?? null;
        $user->password = $body['password'] ?? null;
        $user->name = $body['name'] ?? null;

        $user->register();
    }
    public static function login($body)
    {
        global $pdo;
        $user = new User($pdo);
        $user->email = $body['email'] ?? null;
        $user->password = $body['password'] ?? null;
        $user->login();
    }

    public function get()
    {
    }

}