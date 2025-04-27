<?php
include_once "SessionManager.php";

class ShoppingCard {
    public function __construct() {
        SessionManager::init();
    }

    public static function add_to_cart($user_id, $product_name) {
        // Initialize session
        SessionManager::init();
        SessionManager::setupSession($user_id);
        
        // Get product details from database
        $product_id = Database::query("SELECT product_id FROM products WHERE name = ?", [$product_name], "s")["product_id"];
        $price = Database::query("SELECT price FROM products WHERE product_id = ?", [$product_id], "i")["price"];
        
        // Random quantity between 1 and 4
        $amount = random_int(1, 4);
        
        // Add to session cart
        SessionManager::addToCart($product_id, $product_name, $price, $amount);
        
        // Also store in database for persistence
        $id = Database::query("INSERT INTO orders(user_id, total_amount, status) VALUES (?, ?, ?)",
            [$user_id, $price * $amount, 'Pending'], "iis");
        
        Database::query("INSERT INTO order_items(order_id, product_id, quantity, price) VALUES (?,?,?,?)", 
            [$id, $product_id, $amount, $price], "iiii");
    }

    public static function get_card($user_id) {
        // Initialize session
        SessionManager::init();
        SessionManager::setupSession($user_id);
        
        // Get cart from session
        $cart_items = SessionManager::getCart();
        
        // If session cart is empty, try to load from database
        if (empty($cart_items)) {
            $db_items = Database::query_many("SELECT products.product_id as product_id, products.name as name, products.price as price, order_items.quantity as quantity, orders.total_amount as total FROM products 
                LEFT JOIN order_items ON order_items.product_id = products.product_id 
                LEFT JOIN orders ON orders.order_id = order_items.order_id 
                WHERE orders.user_id = ?", [$user_id], "i");
            
            // Store items in session
            if (!empty($db_items)) {
                foreach ($db_items as $item) {
                    SessionManager::addToCart(
                        $item['product_id'], 
                        $item['name'], 
                        $item['price'], 
                        $item['quantity']
                    );
                }
                $cart_items = SessionManager::getCart();
            }
        }
        
        return $cart_items;
    }

    public static function total($user_id) {
        // Initialize session
        SessionManager::init();
        SessionManager::setupSession($user_id);
        
        // Get total from session
        return SessionManager::calculateCartTotal();
    }
    
    // Save payment information to session
    public static function savePaymentInfo($user_id, $method, $shipping_address, $card_details = []) {
        // Initialize session
        SessionManager::init();
        SessionManager::setupSession($user_id);
        
        // Save payment info to session
        SessionManager::savePaymentInfo($method, $shipping_address, $card_details);
        
        return true;
    }
    
    // Get payment information from session
    public static function getPaymentInfo($user_id) {
        // Initialize session
        SessionManager::init();
        SessionManager::setupSession($user_id);
        
        return SessionManager::getPaymentInfo();
    }
    
    // Clear cart in session
    public static function clearCart($user_id) {
        // Initialize session
        SessionManager::init();
        SessionManager::setupSession($user_id);
        
        SessionManager::clearCart();
        
        // Optional: Also clear cart in database by updating status
        // Database::query("UPDATE orders SET status = 'Completed' WHERE user_id = ? AND status = 'Pending'", [$user_id], "i");
    }
    
    // Remove an item from the cart by index
    public static function removeItem($user_id, $index) {
        // Initialize session
        SessionManager::init();
        SessionManager::setupSession($user_id);
        
        // Remove item from session cart
        SessionManager::removeFromCart($index);
        
        // Note: We're not removing from the database here 
        // as that would require tracking which item corresponds to which database record
        // In a real application, you would also update the database
    }
}
?>
