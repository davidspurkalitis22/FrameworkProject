<?php

class Product {
    public static function GetProduct($team_name, $status) {
        // Get product directly from database instead of filesystem
        $product = Database::query("SELECT image_url FROM products WHERE name = ?", 
            [$team_name . " " . $status], "s");
        
        return $product ? $product['image_url'] : null;
    }

    public static function getAllProducts() {
        return Database::query_many("SELECT * FROM products", [], "");
    }

    public static function getProductsByTeam($team_name) {
        return Database::query_many("SELECT * FROM products WHERE name LIKE ?", 
            [$team_name . "%"], "s");
    }
}