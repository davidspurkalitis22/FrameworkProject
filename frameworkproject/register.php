<?php
  include 'User.php';
  include_once 'SessionManager.php';
  include_once 'CookieManager.php';
  
  // Initialize session
  SessionManager::init();
  
  // Get current theme from cookie or default to light
  $current_theme = CookieManager::getTheme('light');
  
  // Handle theme toggle if requested
  if (isset($_GET['theme']) && in_array($_GET['theme'], ['light', 'dark'])) {
    CookieManager::setTheme($_GET['theme'], 180); // Set theme cookie for 180 days
    // Redirect to remove the query parameter
    header("Location: register.php");
    exit();
  }
  
  $errors = [];
  
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Name validation
    if (empty($_POST['name'])) {
      $errors['name'] = "Name is required";
    } elseif (strlen($_POST['name']) < 2) {
      $errors['name'] = "Name must be at least 2 characters";
    }
    
    // Email validation
    if (empty($_POST['email'])) {
      $errors['email'] = "Email is required";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = "Invalid email format";
    } elseif (User::user_exists($_POST['email'])) {
      $errors['email'] = "Email already exists";
    }
    
    // Password validation
    if (empty($_POST['password'])) {
      $errors['password'] = "Password is required";
    } elseif (strlen($_POST['password']) < 8) {
      $errors['password'] = "Password must be at least 8 characters";
    }
    
    // Confirm password validation
    if (empty($_POST['confirm_password'])) {
      $errors['confirm_password'] = "Please confirm your password";
    } elseif ($_POST['password'] !== $_POST['confirm_password']) {
      $errors['confirm_password'] = "Passwords do not match";
    }
    
    // If no errors, create user and authenticate
    if (empty($errors)) {
      User::createUser($_POST['name'], $_POST['email'], $_POST["password"]);
      if(User::authenticate($_POST["email"], $_POST["password"])) {
        // Get user ID for session
        $user = Database::query("SELECT * FROM users WHERE email = ?", [$_POST["email"]], "s");
        if ($user) {
          // Set up session for this user
          SessionManager::setupSession($user['user_id']);
          // Store registration time in session
          $_SESSION['registration_time'] = time();
          $_SESSION['is_new_user'] = true;
        }
        
        header("Location: /");
        exit;
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?= htmlspecialchars($current_theme) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="icon" type="image/png" href="/images/jersey.png">
  <link rel="stylesheet" href="css/style.css">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<nav class="theme-nav text-white p-4">
  <div class="max-w-20xl mx-auto flex justify-between items-center">
    <a href="login.php" class="text-xl font-bold">Shirtaliano</a>
    <div class="flex gap-4 items-center">
      <a href="/" class="px-4 py-2 hover:bg-blue-700 rounded">Home</a>
      <a href="/card.php" class="px-4 py-2 hover:bg-blue-700 rounded">Card</a>
      <a href="login.php" class="px-4 py-2 hover:bg-blue-700 rounded">Login</a>
      <a href="register.php" class="px-4 py-2 hover:bg-blue-700 rounded">Register</a>
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

<div class="flex items-center justify-center min-h-screen">
  <div class="theme-panel p-6 rounded-lg shadow-lg w-96">
    <h2 class="text-2xl font-bold text-center mb-6">Register</h2>
    <form action="register.php" method="POST">
      <div class="mb-4">
        <label for="name" class="block text-gray-700">Full Name</label>
        <input type="text" name="name" id="name" class="w-full p-2 mt-2 border <?= isset($errors['name']) ? 'border-red-500' : 'border-gray-300' ?> rounded" required value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
        <?php if (isset($errors['name'])): ?>
          <p class="text-red-500 text-sm mt-1"><?= $errors['name'] ?></p>
        <?php endif; ?>
      </div>
      <div class="mb-4">
        <label for="email" class="block text-gray-700">Email</label>
        <input type="email" name="email" id="email" class="w-full p-2 mt-2 border <?= isset($errors['email']) ? 'border-red-500' : 'border-gray-300' ?> rounded" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        <?php if (isset($errors['email'])): ?>
          <p class="text-red-500 text-sm mt-1"><?= $errors['email'] ?></p>
        <?php endif; ?>
      </div>
      <div class="mb-4">
        <label for="password" class="block text-gray-700">Password</label>
        <input type="password" name="password" id="password" class="w-full p-2 mt-2 border <?= isset($errors['password']) ? 'border-red-500' : 'border-gray-300' ?> rounded" required>
        <?php if (isset($errors['password'])): ?>
          <p class="text-red-500 text-sm mt-1"><?= $errors['password'] ?></p>
        <?php endif; ?>
      </div>
      <div class="mb-4">
        <label for="confirm_password" class="block text-gray-700">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" class="w-full p-2 mt-2 border <?= isset($errors['confirm_password']) ? 'border-red-500' : 'border-gray-300' ?> rounded" required>
        <?php if (isset($errors['confirm_password'])): ?>
          <p class="text-red-500 text-sm mt-1"><?= $errors['confirm_password'] ?></p>
        <?php endif; ?>
      </div>
      <button type="submit" class="w-full p-2 mt-4 bg-blue-500 text-white rounded hover:bg-blue-600">Register</button>
      <p class="mt-4 text-center">Already have an account? <a href="login.php" class="text-blue-500">Login here</a></p>
    </form>
  </div>
</div>
</body>
</html>
