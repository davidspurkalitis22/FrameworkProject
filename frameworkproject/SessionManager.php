<?php
// Include guard to prevent multiple declarations
if (!class_exists('SessionManager')) {

class SessionManager {
    // Initialize session if not already started
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Set up basic session structure
    public static function setupSession($user_id) {
        self::init();
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = $user_id;
        }
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        if (!isset($_SESSION['payment'])) {
            $_SESSION['payment'] = [
                'method' => '',
                'shipping_address' => '',
                'card_details' => [],
                'total' => 0
            ];
        }
    }
    
    // Add item to cart
    public static function addToCart($product_id, $product_name, $price, $quantity = 1) {
        self::init();
        
        // Check if product already exists in cart
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] == $product_id) {
                $item['quantity'] += $quantity;
                $item['total'] = $item['price'] * $item['quantity'];
                $found = true;
                break;
            }
        }
        
        // If product not in cart, add it
        if (!$found) {
            $_SESSION['cart'][] = [
                'product_id' => $product_id,
                'name' => $product_name,
                'price' => $price,
                'quantity' => $quantity,
                'total' => $price * $quantity
            ];
        }
        
        // Recalculate total
        self::calculateCartTotal();
    }
    
    // Get cart items
    public static function getCart() {
        self::init();
        return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    }
    
    // Calculate cart total
    public static function calculateCartTotal() {
        self::init();
        
        $total = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['total'];
            }
        }
        
        $_SESSION['payment']['total'] = $total;
        return $total;
    }
    
    // Save payment info
    public static function savePaymentInfo($method, $shipping_address, $card_details = []) {
        self::init();
        
        $_SESSION['payment']['method'] = $method;
        $_SESSION['payment']['shipping_address'] = $shipping_address;
        
        if (!empty($card_details)) {
            $_SESSION['payment']['card_details'] = $card_details;
        }
    }
    
    // Get payment info
    public static function getPaymentInfo() {
        self::init();
        return isset($_SESSION['payment']) ? $_SESSION['payment'] : null;
    }
    
    // Clear cart
    public static function clearCart() {
        self::init();
        $_SESSION['cart'] = [];
        $_SESSION['payment']['total'] = 0;
    }
    
    // Remove item from cart by index
    public static function removeFromCart($index) {
        self::init();
        
        if (isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);
            // Re-index the array
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            // Recalculate total
            self::calculateCartTotal();
        }
    }
    
    // Clear payment info
    public static function clearPaymentInfo() {
        self::init();
        $_SESSION['payment'] = [
            'method' => '',
            'shipping_address' => '',
            'card_details' => [],
            'total' => $_SESSION['payment']['total']
        ];
    }
    
    // Get session variable
    public static function get($key) {
        self::init();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }
    
    // Set session variable
    public static function set($key, $value) {
        self::init();
        $_SESSION[$key] = $value;
    }
    
    // Remove session variable
    public static function remove($key) {
        self::init();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    // Destroy session
    public static function destroy() {
        self::init();
        session_destroy();
    }
}

// End of include guard
}
?> 