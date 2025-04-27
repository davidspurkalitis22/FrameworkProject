<?php
include "User.php";
include_once "CookieManager.php";
include_once "SessionManager.php";

// Initialize session
SessionManager::init();

// Get current theme from cookie or default to light
$current_theme = CookieManager::getTheme('light');

// Handle theme toggle if requested
if (isset($_GET['theme']) && in_array($_GET['theme'], ['light', 'dark'])) {
    CookieManager::setTheme($_GET['theme'], 180); // Set theme cookie for 180 days
    // Redirect to remove the query parameter
    header("Location: profile.php");
    exit();
}

// Check if user is logged in
if(!isset($_COOKIE["login"]) || !User::loggedIn($_COOKIE["login"])) {
    header("Location: /login.php");
    exit();
}

// Get user information
$user = User::get_user($_COOKIE["login"]);
$user_id = $user["user_id"];

// Set up session for this user
SessionManager::setupSession($user_id);

// Get persistent cookie data
$visit_count = User::getVisitCount();
$last_login = User::getLastLogin();
$registered_date = User::getRegisteredDate();

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
    <title>User Profile</title>
    <link rel="icon" type="image/png" href="/images/jersey.png">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <nav class="theme-nav text-white p-4">
        <div class="flex justify-between items-center">
            <div class="text-xl font-bold">Shirtaliano</div>
            <div class="flex gap-4 items-center">
                <a href="/" class="text-white">Home</a>
                <a href="/card.php" class="text-white">Cart</a>
                <a href="/profile.php" class="text-white font-bold underline">Profile</a>
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
        <h1 class="text-3xl font-bold text-center mb-6">User Profile</h1>
        
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-4">User Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 border rounded">
                    <p class="font-bold">Username:</p>
                    <p><?= htmlspecialchars($user['username']) ?></p>
                </div>
                <div class="p-4 border rounded">
                    <p class="font-bold">Email:</p>
                    <p><?= htmlspecialchars($user['email']) ?></p>
                </div>
            </div>
        </div>
        
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-4">Persistent Cookie Data</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 border rounded">
                    <p class="font-bold">Visit Count:</p>
                    <p><?= $visit_count ?> visits</p>
                    <p class="text-sm text-gray-500 mt-2">This data is stored in a cookie that lasts for 1 year</p>
                </div>
                <div class="p-4 border rounded">
                    <p class="font-bold">Last Login:</p>
                    <p><?= htmlspecialchars($last_login) ?></p>
                    <p class="text-sm text-gray-500 mt-2">This data is stored in a cookie that lasts for 1 year</p>
                </div>
                <div class="p-4 border rounded">
                    <p class="font-bold">Registration Date:</p>
                    <p><?= htmlspecialchars($registered_date) ?></p>
                    <p class="text-sm text-gray-500 mt-2">This data is stored in a cookie that lasts for 1 year</p>
                </div>
            </div>
        </div>
        
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-4">Theme Preference</h2>
            <div class="p-4 border rounded">
                <p><strong>Current Theme:</strong> <?= htmlspecialchars(ucfirst($current_theme)) ?> Mode</p>
                <p class="mt-2">This preference is stored in a cookie that lasts for 180 days</p>
                <div class="mt-4">
                    <a href="?theme=<?= $current_theme === 'light' ? 'dark' : 'light' ?>" class="inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Switch to <?= $current_theme === 'light' ? 'Dark' : 'Light' ?> Mode
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 