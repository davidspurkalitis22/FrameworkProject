<?php

class Database
{
    private static $db = null;
    
    public function __construct($servername="localhost", $user="root", $pass="", $database="football")
    {
        if(self::$db != null) return;
        
        try {
            // First connect without specifying a database
            $temp_connection = new mysqli($servername, $user, $pass);
            
            // Check for connection error
            if ($temp_connection->connect_error) {
                die("Connection failed: " . $temp_connection->connect_error);
            }
            
            // Create the database if it doesn't exist
            $temp_connection->query("CREATE DATABASE IF NOT EXISTS $database");
            $temp_connection->close();
            
            // Now connect to the database
            self::$db = new mysqli($servername, $user, $pass, $database);
            
            if (self::$db->connect_error) {
                die("Connection failed: " . self::$db->connect_error);
            }
            
            // Run migrations to create tables if they don't exist
            if (class_exists('Migrations')) {
                Migrations::run_migrations(new self());
            }
        } catch (Exception $e) {
            die("Database error: " . $e->getMessage());
        }
    }

    public static function init($servername="localhost", $user="root", $pass="", $database="football") {
        if(self::$db != null) return;
        
        try {
            // First connect without specifying a database
            $temp_connection = new mysqli($servername, $user, $pass);
            
            // Check for connection error
            if ($temp_connection->connect_error) {
                die("Connection failed: " . $temp_connection->connect_error);
            }
            
            // Create the database if it doesn't exist
            $temp_connection->query("CREATE DATABASE IF NOT EXISTS $database");
            $temp_connection->close();
            
            // Now connect to the database
            self::$db = new mysqli($servername, $user, $pass, $database);
            
            if (self::$db->connect_error) {
                die("Connection failed: " . self::$db->connect_error);
            }
            
            // Run migrations to create tables if they don't exist
            if (class_exists('Migrations')) {
                Migrations::run_migrations(new self());
            }
        } catch (Exception $e) {
            die("Database error: " . $e->getMessage());
        }
    }

    public static function execute_query($query) {
        if(self::$db == null) {
            self::init();
        }
        
        return self::$db->query($query);
    }

    public static function query($query, $vars = null, $types = null) {
        if(self::$db == null) {
            self::init();
        }

        // prepare statement, we do this so we can add variables via ? instead if in the sql code itself.
        // we do this to prevent sql injection...
        $stmt = self::$db->prepare($query);
        if ($vars !== null && $types !== null) {
            $stmt->bind_param($types, ...$vars);
        }
        $stmt->execute();
        $sql_res = $stmt->get_result();

        if(!$sql_res) return self::$db->insert_id;

        if($row = $sql_res->fetch_assoc()) {
            return $row;
        } else {
            return null;
        }
    }

    public static function query_many($query, $vars = null, $types = null) {
        if(self::$db == null) {
            self::init();
        }
        // prepare statement, we do this so we can add variables via ? instead if in the sql code itself.
        // we do this to prevent sql injection...
        $stmt = self::$db->prepare($query);
        if ($vars !== null && $types !== null) {
            $stmt->bind_param($types, ...$vars);
        }
        $stmt->execute();
        $sql_res = $stmt->get_result();

        if(!$sql_res) return self::$db->insert_id;

        $rows = [];
        while($row = $sql_res->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }
}