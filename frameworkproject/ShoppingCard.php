<?php

class ShoppingCard {
    public function __construct() {
    
    }

    public static function add_to_cart($user_id, $product_name) {
        // add order to db
        $amount = random_int(1, 4);

        $product_id = Database::query("SELECT product_id FROM products WHERE name = ?", [$product_name], "s")["product_id"];
        $price = Database::query("SELECT price FROM products WHERE product_id = ?", [$product_id], "i")["price"];

        $id = Database::query("INSERT INTO orders(user_id, total_amount, status) VALUES (?, ?, ?)",
        [$user_id, $price * $amount, 'Pending'], "iis");

        Database::query("INSERT INTO order_items(order_id, product_id, quantity, price) VALUES (?,?,?,?)", [$id, $product_id, $amount, $price], "iiii");
    }

    public static function get_card($user_id) {
        // get all orders from db
        return Database::query_many("SELECT products.name as name, products.price as price, order_items.quantity as quantity, orders.total_amount as total FROM products 
            LEFT JOIN order_items ON order_items.product_id = products.product_id LEFT JOIN orders ON orders.order_id = order_items.order_id WHERE orders.user_id = ?", [$user_id], "i");
    }

    public static function total($user_id) {
        $orders = self::get_card($user_id);
        $sum = 0;
        foreach ($orders as $order) {
            // string to int
            $sum += intval($order["price"]);
        }

        return $sum;
    }
}
?>
