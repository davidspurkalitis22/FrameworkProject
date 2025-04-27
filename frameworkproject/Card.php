<?php
    include "User.php";
    include "Teams.php";
    include "Product.php";
    include "ShoppingCard.php";
    include_once "CookieManager.php";
    
    // Start the session
    SessionManager::init();
    
    // Get current theme from cookie or default to light
    $current_theme = CookieManager::getTheme('light');
    
    if(!isset($_COOKIE["login"]) || !User::loggedIn($_COOKIE["login"])) {
        header("Location: /login.php");
        exit();
    }  

    $errors = [];
    $success = false;
    $user_id = User::get_user($_COOKIE["login"])["user_id"];
    
    // Initialize session for this user
    SessionManager::setupSession($user_id);

    // Handle item removal
    if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
        $index = (int)$_GET['remove'];
        ShoppingCard::removeItem($user_id, $index);
        header("Location: Card.php");
        exit();
    }

    // Process form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Checkout validation
        if (isset($_POST['checkout'])) {
            // Validate shipping info if it exists
            if (isset($_POST['shipping_address']) && empty($_POST['shipping_address'])) {
                $errors['shipping_address'] = "Shipping address is required";
            }
            
            // Validate payment info if it exists
            if (isset($_POST['payment_method']) && empty($_POST['payment_method'])) {
                $errors['payment_method'] = "Payment method is required";
            }
            
            // If no errors, process the order
            if (empty($errors)) {
                // Save payment information to session
                ShoppingCard::savePaymentInfo(
                    $user_id,
                    $_POST['payment_method'],
                    $_POST['shipping_address']
                );
                
                // Additional card details for credit card payment
                if ($_POST['payment_method'] === 'credit_card' && isset($_POST['card_number'])) {
                    $card_details = [
                        'card_number' => substr($_POST['card_number'], -4), // Store only last 4 digits for security
                        'expiry' => isset($_POST['card_expiry']) ? $_POST['card_expiry'] : '',
                        'holder_name' => isset($_POST['card_holder']) ? $_POST['card_holder'] : ''
                    ];
                    
                    // Update session with card details
                    $_SESSION['payment']['card_details'] = $card_details;
                }
                
                // Process the order logic here
                // ...
                
                $success = true;
                
                // Optional: Clear cart after successful checkout
                if ($success) {
                    // Don't clear cart yet, just for demo purposes
                    // ShoppingCard::clearCart($user_id);
                }
            }
        }
    }

    // Get cart items from session
    $cards = ShoppingCard::get_card($user_id);
    
    // Get saved payment info if any
    $payment_info = ShoppingCard::getPaymentInfo($user_id);
    
    // Handle logout if requested
    if (isset($_GET['logout']) && $_GET['logout'] === '1') {
        User::logout();
        header("Location: /login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?= htmlspecialchars($current_theme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="icon" type="image/png" href="/images/jersey.png">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/card.js" defer></script>
</head>
<body>
<nav class="theme-nav text-white p-4">
  <div class="flex justify-between items-center">
    <a href="login.php" class="text-xl font-bold">Shirtaliano</a>
    <div class="flex gap-4 items-center">
      <a href="/" class="text-white">Home</a>
      <a href="/card.php" class="text-white">Card</a>
      <a href="/profile.php" class="text-white">Profile</a>
      <a href="?logout=1" class="text-white">Logout</a>
      <div class="ml-4">
          <a href="?theme=<?= $current_theme === 'light' ? 'dark' : 'light' ?>" class="inline-flex items-center px-3 py-1 border border-white rounded-full text-xs">
              <?php if ($current_theme === 'light'): ?>
                  🌙 Dark Mode
              <?php else: ?>
                  ☀️ Light Mode
              <?php endif; ?>
          </a>
      </div>
    </div>
  </div>
</nav>
<div class="container mx-auto p-5">
    <h1 class="text-4xl font-bold mb-5">Your Shopping Cart</h1>

    <?php if ($success): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
            <p class="font-bold">Success!</p>
            <p>Your order has been processed successfully.</p>
            
            <?php if (isset($_SESSION['payment']) && !empty($_SESSION['payment']['method'])): ?>
                <div class="mt-2">
                    <p><strong>Payment Method:</strong> <?= htmlspecialchars($_SESSION['payment']['method']) ?></p>
                    <p><strong>Shipping Address:</strong> <?= htmlspecialchars($_SESSION['payment']['shipping_address']) ?></p>
                    <?php if ($_SESSION['payment']['method'] === 'credit_card' && isset($_SESSION['payment']['card_details']['card_number'])): ?>
                        <p><strong>Card:</strong> XXXX-XXXX-XXXX-<?= htmlspecialchars($_SESSION['payment']['card_details']['card_number']) ?></p>
                    <?php endif; ?>
                    <p><strong>Total:</strong> $<?= number_format($_SESSION['payment']['total'], 2) ?></p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
            <p class="font-bold">Errors:</p>
            <ul class="ml-4 list-disc">
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <!-- Session and Cookie Debug Info -->
    <?php if (isset($_GET['debug']) && $_GET['debug'] === '1'): ?>
        <div class="theme-panel p-4 mb-4 border rounded">
            <h3 class="font-bold">SESSION Data:</h3>
            <pre><?php print_r($_SESSION); ?></pre>
            
            <h3 class="font-bold mt-4">COOKIE Data:</h3>
            <pre><?php print_r($_COOKIE); ?></pre>
        </div>
    <?php endif; ?>

    <form action="Card.php" method="POST">
        <div class="theme-panel p-4 rounded shadow-lg">
            <table class="w-full table-auto">
                <thead>
                    <tr>
                        <th class="border-b p-2">Product</th>
                        <th class="border-b p-2">Price</th>
                        <th class="border-b p-2">Quantity</th>
                        <th class="border-b p-2">Total</th>
                        <th class="border-b p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cards)): ?>
                        <tr>
                            <td colspan="5" class="border-b p-2 text-center">Your cart is empty</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cards as $index => $card): ?>
                            <tr>
                                <td class="border-b p-2"><?= htmlspecialchars($card["name"]) ?></td>
                                <td class="border-b p-2">$<?= number_format($card["price"], 2) ?></td>
                                <td class="border-b p-2"><?= $card["quantity"] ?></td>
                                <td class="border-b p-2">$<?= number_format($card["total"], 2) ?></td>
                                <td class="border-b p-2">
                                    <a href="Card.php?remove=<?= $index ?>" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Remove</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php if (!empty($cards)): ?>
                <div class="mt-6">
                    <h3 class="text-lg font-bold mb-2">Shipping Information</h3>
                    <div class="mb-4">
                        <label for="shipping_address" class="block">Shipping Address</label>
                        <textarea name="shipping_address" id="shipping_address" 
                            class="w-full p-2 mt-2 border <?= isset($errors['shipping_address']) ? 'border-red-500' : 'border-gray-300' ?> rounded"
                            required><?= isset($_POST['shipping_address']) ? htmlspecialchars($_POST['shipping_address']) : (isset($_SESSION['payment']['shipping_address']) ? htmlspecialchars($_SESSION['payment']['shipping_address']) : '') ?></textarea>
                        <?php if (isset($errors['shipping_address'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?= $errors['shipping_address'] ?></p>
                        <?php endif; ?>
                    </div>

                    <h3 class="text-lg font-bold mb-2">Payment Method</h3>
                    <div class="mb-4">
                        <select name="payment_method" id="payment_method" 
                            class="w-full p-2 mt-2 border <?= isset($errors['payment_method']) ? 'border-red-500' : 'border-gray-300' ?> rounded"
                            required>
                            <option value="" disabled <?= !isset($_POST['payment_method']) && !isset($_SESSION['payment']['method']) ? 'selected' : '' ?>>Select payment method</option>
                            <option value="credit_card" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'credit_card') || (isset($_SESSION['payment']['method']) && $_SESSION['payment']['method'] == 'credit_card') ? 'selected' : '' ?>>Credit Card</option>
                            <option value="paypal" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'paypal') || (isset($_SESSION['payment']['method']) && $_SESSION['payment']['method'] == 'paypal') ? 'selected' : '' ?>>PayPal</option>
                        </select>
                        <?php if (isset($errors['payment_method'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?= $errors['payment_method'] ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Credit Card Details (show only when credit card is selected) -->
                    <div id="credit_card_details" class="mb-4 <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'credit_card') || (isset($_SESSION['payment']['method']) && $_SESSION['payment']['method'] == 'credit_card') ? '' : 'hidden' ?>">
                        <div class="mb-2">
                            <label for="card_number" class="block">Card Number</label>
                            <input type="text" name="card_number" id="card_number" 
                                class="w-full p-2 mt-1 border border-gray-300 rounded"
                                placeholder="XXXX-XXXX-XXXX-XXXX">
                        </div>
                        <div class="mb-2">
                            <label for="card_expiry" class="block">Expiry Date</label>
                            <input type="text" name="card_expiry" id="card_expiry" 
                                class="w-full p-2 mt-1 border border-gray-300 rounded"
                                placeholder="MM/YY">
                        </div>
                        <div class="mb-2">
                            <label for="card_cvv" class="block">CVV</label>
                            <input type="text" name="card_cvv" id="card_cvv" 
                                class="w-full p-2 mt-1 border border-gray-300 rounded"
                                placeholder="123">
                        </div>
                        <div class="mb-2">
                            <label for="card_holder" class="block">Card Holder Name</label>
                            <input type="text" name="card_holder" id="card_holder" 
                                class="w-full p-2 mt-1 border border-gray-300 rounded"
                                placeholder="John Doe">
                        </div>
                    </div>

                    <button type="submit" name="checkout" value="1" class="bg-blue-500 text-white p-3 rounded hover:bg-blue-600">Complete Purchase</button>
                </div>
            <?php endif; ?>
        </div>
    </form>

    <div class="mt-8">
        <p class="text-lg font-bold">Total: $<?= number_format(SessionManager::calculateCartTotal(), 2) ?></p>
    </div>
    <div class="mt-8">
        <a href="index.php" class="bg-blue-400 text-white p-3 rounded mt-4 hover:bg-blue-500">Continue Shopping</a>
    </div>
</div>

</body>
</html>