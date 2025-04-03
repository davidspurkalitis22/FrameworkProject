<?php
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
                ShoppingCard::add_to_cart(User::get_user($_COOKIE["login"])["user_id"], $_POST["team"] . " " . $_POST["kit"]);
                $html = "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4'>
                            Item added to cart successfully!
                        </div>";
                $response = [
                    'success' => true, 
                    'message' => 'Added to cart',
                    'html' => $html,
                    'status' => 'success'
                ];
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
        $file_path = Product::GetProduct($_POST["team"], $_POST["kit"]);
        if ($file_path) {
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