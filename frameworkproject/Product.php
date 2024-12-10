<?php

class Product {
    public static function GetProduct($team_name, $status) {
        $folder_path = getcwd() . "/images/";

        $files = scandir($folder_path);
        foreach ($files as $file) {
            if ($file == '.'|| $file == '..') {
                continue;
            }

            if (str_starts_with($file, $team_name . " " . $status)) {
                return "/images/" . $file;
            }
        }

        return null;
    }

    public static function createProducts() {
        // add random products to the db if they don't exists, we get all the products from the images folder
        $folder_path = getcwd() . "/images/";

        $files = scandir($folder_path);
        $products = [];
        foreach ($files as $file) {
            if ($file == '.'|| $file == '..') {
                continue;
            }

            $products[] =  $file;
        }

        foreach($products as $product_img) {
            $file_name = explode('.', $product_img)[0];
            
            $stock = random_int(20, 43234);
            $price = random_int(40,100);
            if(Database::query("SELECT * FROM teams WHERE teams.name = ?", [$file_name], "s") != null) {
                continue;
            }

            Database::query("INSERT INTO products (name, description, price, stock, image_url) VALUES (?, ?, ?, ?, ?)", 
                [$file_name, "No desc", $price, $stock, "/images/" . $product_img] ,"ssiis");
        }
    }

}