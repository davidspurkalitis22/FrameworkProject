<?php
// Include Database class if it doesn't exist
if (!class_exists('Database')) {
    require_once 'Database.php';
}

// Include Migrations class if it doesn't exist
if (!class_exists('Migrations')) {
    require_once 'migrations.php';
}

class Teams
{
    public static function createTeams() {
        $folder_path = getcwd() . "/images/";

        $files = scandir($folder_path);
        $products = [];
        foreach ($files as $file) {
            if ($file == '.'|| $file == '..') {
                continue;
            }

            $products[] =  $file;
        }

        // Ensure the database exists and migrations have run
        new Database();

        foreach($products as $product_img) {
            // get the team name of the file(roma home.png => "roma")
            $file_name = explode('.', $product_img)[0];
            $team_name = explode(' ', $file_name)[0];
            
            $founded_year = random_int(1855, 2015);
            if(Database::query("SELECT * FROM teams WHERE teams.name = ?", [$team_name], "s") != null) {
                continue;
            }

            Database::query("INSERT INTO teams (name, founded_year, stadium, league, logo_url) VALUES (?, ?, ?, ?, ?)", 
                [$team_name, $founded_year, "Tigers Arena", "Serie A", "/images/" . $product_img] ,"sisss");
        }
    }

    public static function getTeams() {
        $folder_path = getcwd() . "/images/";

        $files = scandir($folder_path);
        $teams = [];

        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            
            // get the team name of the file(roma home.png => "roma")
            $team = explode(".", explode(" ", $file)[0])[0];
            if(!in_array($team, $teams)) {
                $teams[] = $team;
            }
        }

        return $teams;
    }
}