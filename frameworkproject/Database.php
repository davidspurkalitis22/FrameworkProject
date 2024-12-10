<?php

class Database
{
    private static $db = null;
    public function __construct($servername="frameworkProject", $user="root", $pass="", $database="football")
    {
        if(self::$db != null) return;
        self::$db = new mysqli($servername, $user, $pass, $database);

        if (self::$db->connect_error) {
            die("Connection failed: " . self::$db->connect_error);
        }
    }

    public static function init($servername="frameworkProject", $user="root", $pass="", $database="football") {
        self::$db = new mysqli($servername, $user, $pass, $database);

        if (self::$db->connect_error) {
            die("Connection failed: " . self::$db->connect_error);
        }
    }

    public static function query($query, $vars = null, $types = null) {
        if(self::$db == null) {
            self::init();
        }

        // prepare statement, we do this so we can add variables via ? instead if in the sql code itself.
        // we do this to prevent sql injection...
        $stmt = self::$db->prepare($query);
        $stmt->bind_param($types, ...$vars);
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
        $stmt->bind_param($types, ...$vars);
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