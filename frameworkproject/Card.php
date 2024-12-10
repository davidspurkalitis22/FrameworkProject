<?php
    include "User.php";
    include "Teams.php";
    include "Product.php";
    include "ShoppingCard.php";
    
    Product::createProducts();
    Teams::createTeams();

    if(!User::loggedIn($_COOKIE["login"])) {
        header("Location: /login.php");
        exit();
    }  

    $teams = Teams::getTeams();

    $cards = ShoppingCard::get_card(User::get_user($_COOKIE["login"])["user_id"]);
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
<nav class="bg-green-500 text-white p-4">
  <div class="max-w-20xl mx-auto flex justify-between items-center">
    <a href="login.php" class="text-xl font-bold">Shirtaliano</a>
    <div>
      <a href="/" class="px-4 py-2 hover:bg-red-700 rounded">Home</a>
      <a href="/card.php" class="px-4 py-2 hover:bg-red-700 rounded">Card</a>
      <a href="login.php" class="px-4 py-2 hover:bg-red-700 rounded">Login</a>
      <a href="register.php" class="px-4 py-2 hover:bg-red-700 rounded">Register</a>
    </div>
  </div>
</nav>
<div class="container mx-auto p-5">
    <h1 class="text-4xl font-bold mb-5">Your Shopping Cart</h1>

    <form action="cart.php" method="POST">
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
        </div>
    </form>

    <div class="mt-8">
        <p class="text-lg font-bold">Total: $</p>
    </div>
    <div class="mt-8">
        <a href="index.php" class="bg-gray-500 text-white p-3 rounded mt-4">Continue Shopping</a>
    </div>
</div>

</body>
</html>