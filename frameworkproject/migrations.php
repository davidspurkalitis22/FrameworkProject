<?php
// Include Database class if it doesn't exist
if (!class_exists('Database')) {
    require_once 'Database.php';
}

class Migrations {
    static function run_migrations($db) {
        Migrations::_25_02_2025_shopping_tables($db);
    }

    static function _25_02_2025_shopping_tables($db) {
        // Load SQL from file
        $sql_file = file_get_contents('sql/migrations.sql');
        
        // Split SQL file into individual queries
        $queries = array_filter(
            array_map(
                'trim',
                explode(';', $sql_file)
            ),
            function($query) {
                return !empty($query);
            }
        );

        // Execute each query
        foreach ($queries as $query) {
            $db->execute_query($query);
        }
    }
}
