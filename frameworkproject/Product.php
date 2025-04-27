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
    
    // Create products from image files
    public static function createProducts() {
        $folder_path = getcwd() . "/images/";
        $files = scandir($folder_path);
        
        foreach ($files as $file) {
            if ($file == '.' || $file == '..' || $file == 'jersey.png') {
                continue;
            }
            
            // Extract product name from file name (team + kit type)
            $file_name = pathinfo($file, PATHINFO_FILENAME);
            
            // Check if product already exists
            $existing = Database::query("SELECT * FROM products WHERE name = ?", [$file_name], "s");
            if ($existing) {
                continue;
            }
            
            // Create random product details
            $price = mt_rand(6000, 12000) / 100; // Random price between $60-$120
            $stock = mt_rand(10, 100);
            $image_url = "/images/" . $file;
            $description = "Official " . ucfirst($file_name) . " jersey.";
            
            // Insert product into database
            Database::query(
                "INSERT INTO products (name, description, price, stock, image_url) VALUES (?, ?, ?, ?, ?)",
                [$file_name, $description, $price, $stock, $image_url],
                "ssdis"
            );
        }
    }
}