<?php
include "Database.php";
include_once "CookieManager.php";

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
        
        // Set login cookie using CookieManager (persists for 30 days)
        CookieManager::setCookie("login", $pass_hash, 30, "/");
        
        // Track registration date in a long-term cookie (1 year)
        CookieManager::setCookie("registered_date", date('Y-m-d H:i:s'), 365, "/");
    }

    public static function authenticate($email, $password)
    {
        $user = Database::query("SELECT password_hash FROM users WHERE email = ?", [$email], "s");
        if(password_verify($password, $user['password_hash'])) {
            // Set login cookie using CookieManager (persists for 30 days)
            CookieManager::setCookie("login", $user['password_hash'], 30, "/");
            
            // Track last login date in a long-term cookie (1 year)
            CookieManager::setCookie("last_login", date('Y-m-d H:i:s'), 365, "/");
            
            // Optional: Update visit count in a persistent cookie
            $visit_count = 1;
            if (CookieManager::hasCookie("visit_count")) {
                $visit_count = (int)CookieManager::getCookie("visit_count") + 1;
            }
            CookieManager::setCookie("visit_count", $visit_count, 365, "/");
            
            return true;
        }

        return false;
    }

    public static function loggedIn($password) {
        $user = Database::query("SELECT * FROM users WHERE password_hash = ?",  [$password], "s");
        return $user != null;
    }
    
    public static function logout() {
        // Remove the login cookie
        CookieManager::deleteCookie("login", "/");
        
        // If using sessions, destroy the session
        if (function_exists('session_status') && session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
    
    public static function getVisitCount() {
        return (int)CookieManager::getCookie("visit_count", 0);
    }
    
    public static function getLastLogin() {
        return CookieManager::getCookie("last_login", "Never");
    }
    
    public static function getRegisteredDate() {
        return CookieManager::getCookie("registered_date", "Unknown");
    }
}