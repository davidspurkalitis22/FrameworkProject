<?php
    include "User.php";
    include "Teams.php";
    include "Product.php";
    include "ShoppingCard.php";
    
    if(!isset($_COOKIE["login"]) || !User::loggedIn($_COOKIE["login"])) {
        header("Location: /login.php");
        exit();
    }  

    $errors = [];
    $success = false;
    $user_id = User::get_user($_COOKIE["login"])["user_id"];

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
                // Process the order logic here
                $success = true;
            }
        }
    }

    $cards = ShoppingCard::get_card($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-blue-400 text-white p-4">
  <div class="flex justify-between items-center">
    <a href="login.php" class="text-xl font-bold">Shirtaliano</a>
    <div class="flex gap-4">
      <a href="/" class="text-white">Home</a>
      <a href="/card.php" class="text-white">Card</a>
      <a href="login.php" class="text-white">Login</a>
      <a href="register.php" class="text-white">Register</a>
    </div>
  </div>
</nav>
<div class="container mx-auto p-5">
    <h1 class="text-4xl font-bold mb-5">Your Shopping Cart</h1>

    <?php if ($success): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
            <p class="font-bold">Success!</p>
            <p>Your order has been processed successfully.</p>
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

    <form action="Card.php" method="POST">
        <div class="bg-white p-4 rounded shadow-lg">
            <table class="w-full table-auto">
                <thead>
                    <tr>
                        <th class="border-b p-2">Product</th>
                        <th class="border-b p-2">Price</th>
                        <th class="border-b p-2">Quantity</th>
                        <th class="border-b p-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $cards as $card ): ?>
                        <tr>
                            <td class="border-b p-2"><?= $card["name"] ?></td>
                            <td class="border-b p-2">$<?= $card["price"] ?></td>
                            <td class="border-b p-2"> <?= $card["quantity"] ?> </td>
                            <td class="border-b p-2">$<?= $card["total"] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (!empty($cards)): ?>
                <div class="mt-6">
                    <h3 class="text-lg font-bold mb-2">Shipping Information</h3>
                    <div class="mb-4">
                        <label for="shipping_address" class="block text-gray-700">Shipping Address</label>
                        <textarea name="shipping_address" id="shipping_address" 
                            class="w-full p-2 mt-2 border <?= isset($errors['shipping_address']) ? 'border-red-500' : 'border-gray-300' ?> rounded"
                            required><?= isset($_POST['shipping_address']) ? htmlspecialchars($_POST['shipping_address']) : '' ?></textarea>
                        <?php if (isset($errors['shipping_address'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?= $errors['shipping_address'] ?></p>
                        <?php endif; ?>
                    </div>

                    <h3 class="text-lg font-bold mb-2">Payment Method</h3>
                    <div class="mb-4">
                        <select name="payment_method" id="payment_method" 
                            class="w-full p-2 mt-2 border <?= isset($errors['payment_method']) ? 'border-red-500' : 'border-gray-300' ?> rounded"
                            required>
                            <option value="" disabled <?= !isset($_POST['payment_method']) ? 'selected' : '' ?>>Select payment method</option>
                            <option value="credit_card" <?= isset($_POST['payment_method']) && $_POST['payment_method'] == 'credit_card' ? 'selected' : '' ?>>Credit Card</option>
                            <option value="paypal" <?= isset($_POST['payment_method']) && $_POST['payment_method'] == 'paypal' ? 'selected' : '' ?>>PayPal</option>
                        </select>
                        <?php if (isset($errors['payment_method'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?= $errors['payment_method'] ?></p>
                        <?php endif; ?>
                    </div>

                    <button type="submit" name="checkout" value="1" class="bg-blue-500 text-white p-3 rounded hover:bg-blue-600">Complete Purchase</button>
                </div>
            <?php endif; ?>
        </div>
    </form>

    <div class="mt-8">
        <p class="text-lg font-bold">Total: $<?= ShoppingCard::total($user_id) ?></p>
    </div>
    <div class="mt-8">
        <a href="index.php" class="bg-blue-400 text-white p-3 rounded mt-4 hover:bg-blue-500">Continue Shopping</a>
    </div>
</div>

</body>
</html>