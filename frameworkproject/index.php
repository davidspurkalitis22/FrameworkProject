<?php
    include "User.php";
    include "Teams.php";
    include "Product.php";
    include "ShoppingCard.php";
    include_once "CookieManager.php";
    include_once "Database.php";
    include_once "migrations.php";
    
    // Initialize session
    SessionManager::init();
    
    // Handle theme toggle if requested
    if (isset($_GET['theme']) && in_array($_GET['theme'], ['light', 'dark'])) {
        CookieManager::setTheme($_GET['theme'], 180); // Set theme cookie for 180 days
    }
    
    // Get current theme from cookie or default to light
    $current_theme = CookieManager::getTheme('light');
    
    // Initialize database and create necessary data
    new Database();
    Teams::createTeams();
    Product::createProducts();

    if(!isset($_COOKIE["login"]) || !User::loggedIn($_COOKIE["login"])) {
        header("Location: /login.php");
        exit();
    }  

    // Get user ID and set up session
    $user_id = User::get_user($_COOKIE["login"])["user_id"];
    SessionManager::setupSession($user_id);

    // Handle AJAX requests
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once 'handlers/ajax_handler.php';
    }

    $teams = Teams::getTeams();
    
    // Get cart count from session
    $cart_count = count(SessionManager::getCart());
    
    // Get recently viewed items from session
    $recently_viewed = isset($_SESSION['recently_viewed']) ? $_SESSION['recently_viewed'] : [];
    
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
    <title>Football Kits</title>
    <link rel="icon" type="image/png" href="/images/jersey.png">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/main.js" defer></script>
</head>
<body>
    <nav class="theme-nav text-white p-4">
        <div class="flex justify-between items-center">
            <div class="text-xl font-bold">Shirtaliano</div>
            <div class="flex gap-4 items-center">
                <a href="/" class="text-white">Home</a>
                <a href="/card.php" class="text-white">Card <?php if($cart_count > 0): ?><span class="bg-red-500 text-white rounded-full px-2 py-1 text-xs"><?= $cart_count ?></span><?php endif; ?></a>
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

    <div class="max-w-4xl mx-auto mt-10 p-6 theme-panel shadow-lg rounded-lg">
        <h1 class="text-3xl font-bold text-center mb-6">Select a Football Team</h1>
        
        <div id="notification" class="hidden"></div>

        <form id="kitForm" class="mb-8">
            <label for="team" class="block text-lg font-medium mb-2">Choose a team:</label>
            <select name="team" id="team" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                <option value="" disabled selected>Select a team</option>
                <?php foreach ($teams as $team_name): ?>
                    <option value="<?php echo $team_name; ?>"><?php echo $team_name;?></option>
                <?php endforeach; ?>
            </select>

            <label for="kit" class="block text-lg font-medium mb-2 mt-4">Choose a kit:</label>
            <select name="kit" id="kit" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                <option value="" disabled selected>Select a kit</option>
                <option value="home">home</option>
                <option value="away">away</option>
            </select>

            <button type="button" id="showTeamBtn" class="mt-4 w-full p-3 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition duration-300">Show Team</button>
            <button type="button" id="addToCardBtn" class="mt-4 w-full p-3 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition duration-300">Add To Card</button>
        </form>

        <div id="kitImage" class="mt-4"></div>
        
        <!-- Display recently viewed items from session -->
        <?php if (!empty($recently_viewed)): ?>
            <div class="mt-8 border-t pt-4">
                <h2 class="text-xl font-bold mb-3">Recently Viewed</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <?php foreach($recently_viewed as $item): ?>
                        <div class="bg-gray-50 p-3 rounded shadow text-center">
                            <p><?= htmlspecialchars($item) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Session and Cookie Debug Info -->
        <?php if (isset($_GET['debug']) && $_GET['debug'] === '1'): ?>
            <div class="bg-gray-100 p-4 mt-6 border rounded">
                <h3 class="font-bold">SESSION Data:</h3>
                <pre><?php print_r($_SESSION); ?></pre>
                
                <h3 class="font-bold mt-4">COOKIE Data:</h3>
                <pre><?php print_r($_COOKIE); ?></pre>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>