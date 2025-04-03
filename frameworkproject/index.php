<?php
    include "User.php";
    include "Teams.php";
    include "Product.php";
    include "ShoppingCard.php";
    
    Teams::createTeams();

    if(!isset($_COOKIE["login"]) || !User::loggedIn($_COOKIE["login"])) {
        header("Location: /login.php");
        exit();
    }  

    // Handle AJAX requests
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once 'handlers/ajax_handler.php';
    }

    $teams = Teams::getTeams();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Football Kits</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/main.js" defer></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-400 text-white p-4">
        <div class="flex justify-between items-center">
            <div class="text-xl font-bold">Shirtaliano</div>
            <div class="flex gap-4">
                <a href="/" class="text-white">Home</a>
                <a href="/card.php" class="text-white">Card</a>
                <a href="login.php" class="text-white">Login</a>
                <a href="register.php" class="text-white">Register</a>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto mt-10 p-6 bg-white shadow-lg rounded-lg">
        <h1 class="text-3xl font-bold text-center mb-6">Select a Football Team</h1>

        <div id="notification" class="hidden"></div>

        <form id="kitForm" class="mb-8">
            <label for="team" class="block text-lg font-medium text-gray-700 mb-2">Choose a team:</label>
            <select name="team" id="team" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                <option value="" disabled selected>Select a team</option>
                <?php foreach ($teams as $team_name): ?>
                    <option value="<?php echo $team_name; ?>"><?php echo $team_name;?></option>
                <?php endforeach; ?>
            </select>

            <label for="kit" class="block text-lg font-medium text-gray-700 mb-2">Choose a kit:</label>
            <select name="kit" id="kit" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                <option value="" disabled selected>Select a kit</option>
                <option value="home">home</option>
                <option value="away">away</option>
            </select>

            <button type="button" id="showTeamBtn" class="mt-4 w-full p-3 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition duration-300">Show Team</button>
            <button type="button" id="addToCardBtn" class="mt-4 w-full p-3 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition duration-300">Add To Card</button>
        </form>

        <div id="kitImage" class="mt-4"></div>
    </div>
</body>
</html>