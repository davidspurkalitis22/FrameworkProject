<?php
// Initialize session
require_once __DIR__ . '/../SessionManager.php';
SessionManager::init();

// Initialize response array
$response = ['success' => false, 'errors' => []];

// Validate common required fields
if (!isset($_POST["team"]) || empty($_POST["team"])) {
    $response['errors']['team'] = "Team is required";
}

if (!isset($_POST["kit"]) || empty($_POST["kit"])) {
    $response['errors']['kit'] = "Kit type is required";
}

// Valid kit types
$valid_kits = ["home", "away"];
if (isset($_POST["kit"]) && !in_array($_POST["kit"], $valid_kits)) {
    $response['errors']['kit'] = "Invalid kit type";
}

// For add to cart - return JSON response
if(isset($_POST["add_to_card"])) {
    // Check if user is logged in
    if(!isset($_COOKIE["login"]) || !User::loggedIn($_COOKIE["login"])) {
        $response['errors']['login'] = "You must be logged in to add items to cart";
    } else {
        // If no validation errors, proceed with adding to cart
        if (empty($response['errors'])) {
            try {
                $user_id = User::get_user($_COOKIE["login"])["user_id"];
                
                // Set up session for this user
                SessionManager::setupSession($user_id);
                
                // Add item to cart (which now uses sessions internally)
                ShoppingCard::add_to_cart($user_id, $_POST["team"] . " " . $_POST["kit"]);
                
                // Get cart count from session for UI update
                $cart_count = count(SessionManager::getCart());
                
                $html = "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4'>
                            Item added to cart successfully!
                        </div>";
                $response = [
                    'success' => true, 
                    'message' => 'Added to cart',
                    'html' => $html,
                    'cart_count' => $cart_count,
                    'status' => 'success'
                ];
                
                // Also add item name to session for a recent items list
                if (!isset($_SESSION['recent_items'])) {
                    $_SESSION['recent_items'] = [];
                }
                
                // Add to recent items (limit to 5)
                array_unshift($_SESSION['recent_items'], $_POST["team"] . " " . $_POST["kit"]);
                if (count($_SESSION['recent_items']) > 5) {
                    array_pop($_SESSION['recent_items']);
                }
                
            } catch (Exception $e) {
                $response['errors']['system'] = $e->getMessage();
                $response['status'] = 'error';
            }
        } else {
            $response['status'] = 'error';
        }
    }
    
    echo json_encode($response);
    exit;
}

// For show team - return HTML directly
if (isset($_POST["show_team"])) {
    if (!empty($response['errors'])) {
        // Return error response if validation failed
        echo json_encode($response);
    } else {
        // Check if product exists in database first
        $product_name = $_POST["team"] . " " . $_POST["kit"];
        $product = Database::query("SELECT * FROM products WHERE name = ?", [$product_name], "s");
        
        if (!$product) {
            // If product doesn't exist, try to create it
            Product::createProducts();
            // Try again
            $product = Database::query("SELECT * FROM products WHERE name = ?", [$product_name], "s");
        }
        
        $file_path = Product::GetProduct($_POST["team"], $_POST["kit"]);
        if ($file_path) {
            // If user is logged in, also store this view in session history
            if(isset($_COOKIE["login"]) && User::loggedIn($_COOKIE["login"])) {
                $user_id = User::get_user($_COOKIE["login"])["user_id"];
                SessionManager::setupSession($user_id);
                
                // Track recently viewed items in session
                if (!isset($_SESSION['recently_viewed'])) {
                    $_SESSION['recently_viewed'] = [];
                }
                
                // Add to recently viewed (limit to 10 and prevent duplicates)
                if (!in_array($product_name, $_SESSION['recently_viewed'])) {
                    array_unshift($_SESSION['recently_viewed'], $product_name);
                    if (count($_SESSION['recently_viewed']) > 10) {
                        array_pop($_SESSION['recently_viewed']);
                    }
                }
            }
            
            echo "<div class='text-center'>
                    <img src='{$file_path}' alt='Selected kit' class='w-64 h-64 mx-auto mb-4'>
                    <p class='text-gray-700 font-semibold'>{$_POST['team']} {$_POST['kit']} Kit</p>
                  </div>";
        } else {
            $response['errors']['product'] = "Product not found";
            echo json_encode($response);
        }
    }
    exit;
} 