<?php
include "Database.php";

/**
 * We set a cookie for the users hash with the name: login.
 * we do this so we can easly authenticate be searching the hash on the db(if its found we are sure that we are logged in)
 * we refresh this cookie every 30days
 */
class User
{
    public function __construct()
    {

    }

    public static function get_user($hash) {
        $user = Database::query("SELECT * FROM users WHERE password_hash = ?",  [$hash], "s");
        return $user;
    }

    public static function user_exists($email) {
        $user = Database::query("SELECT * FROM users WHERE email = ?", [$email], "s");
        return $user != null;
    }

    public static function createUser($name, $email, $password)
    {
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        Database::query("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)", [$name, $email, $pass_hash], "sss");
        setcookie("login", $pass_hash, time() + (86400 * 30), "/");
    }

    public static function authenticate($email, $password)
    {
        $user = Database::query("SELECT password_hash FROM users WHERE email = ?", [$email], "s");
        if(password_verify($password, $user['password_hash'])) {
            setcookie("login", $user['password_hash'], time() + (86400 * 30), "/");
            return true;
        }

        return false;
    }

    public static function loggedIn($password) {
        $user = Database::query("SELECT * FROM users WHERE password_hash = ?",  [$password], "s");
        return $user != null;
    }
}