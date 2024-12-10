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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
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
<div class="max-w-4xl mx-auto mt-10 p-6 bg-white shadow-lg rounded-lg">

        <h1 class="text-3xl font-bold text-center mb-6">Select a Football Team</h1>

        <form method="POST" class="mb-8">
            <label for="team" class="block text-lg font-medium text-gray-700 mb-2">Choose a team:</label>
            <select name="team" id="team" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="" disabled selected>Select a team</option>
                <?php foreach ($teams as $team_name): ?>
                    <option value="<?php echo $team_name; ?>"><?php echo $team_name;?></option>
                <?php endforeach; ?>
            </select>
            <label for="kit" class="block text-lg font-medium text-gray-700 mb-2">Choose a kit:</label>
            <select name="kit" id="kit" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="" disabled selected>Select a kit</option>
                <option value="home">home</option>
                <option value="away">away</option>
            </select>

            <button type="submit" name="show_team" class="mt-4 w-full p-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300">Show Team</button>
            <button type="submit" name="add_to_card" class="mt-4 w-full p-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300">Add To Card</button>
      </form>
    </div>

  <?php 

  if(isset($_REQUEST["add_to_card"]) && isset($_REQUEST["team"]) && isset($_REQUEST["kit"])) {
    ShoppingCard::add_to_cart(User::get_user($_COOKIE["login"])["user_id"], $_REQUEST["team"] . " " . $_REQUEST["kit"]);
  }

  if (isset($_REQUEST["show_team"]) && isset($_REQUEST["team"]) && isset($_REQUEST["kit"])) {
    $file_path = Product::GetProduct($_REQUEST["team"], $_REQUEST["kit"]);
    echo "<img src='" . $file_path . "' alt='test' style='width: 20%; height:20%; border-style: solid;'></li>";
  }
  ?>
</body>
</html>