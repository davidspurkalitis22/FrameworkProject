<?php
  include 'User.php';
  $errors = [];
  
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Email validation
    if (empty($_POST['email'])) {
      $errors['email'] = "Email is required";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = "Invalid email format";
    }
    
    // Password validation
    if (empty($_POST['password'])) {
      $errors['password'] = "Password is required";
    }
    
    // If no errors, attempt authentication
    if (empty($errors)) {
      if(User::authenticate($_POST["email"], $_POST["password"])) {
        header("Location: /");
        exit;
      } else {
        $errors['auth'] = "Invalid email or password";
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<!-- Navbar -->
<nav class="bg-blue-500 text-white p-4">
  <div class="max-w-20xl mx-auto flex justify-between items-center">
    <a href="login.php" class="text-xl font-bold">Shirtaliano</a>
    <div>
      <a href="/" class="px-4 py-2 hover:bg-blue-700 rounded">Home</a>
      <a href="/card.php" class="px-4 py-2 hover:bg-blue-700 rounded">Card</a>
      <a href="login.php" class="px-4 py-2 hover:bg-blue-700 rounded">Login</a>
      <a href="register.php" class="px-4 py-2 hover:bg-blue-700 rounded">Register</a>
    </div>
  </div>
</nav>

<div class="flex items-center justify-center min-h-screen">
  <div class="bg-white p-6 rounded-lg shadow-lg w-96">
    <h2 class="text-2xl font-bold text-center mb-6">Login</h2>
    
    <?php if (isset($errors['auth'])): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <p><?= $errors['auth'] ?></p>
      </div>
    <?php endif; ?>
    
    <form action="login.php" method="POST">
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
      <button type="submit" class="w-full p-2 mt-4 bg-blue-500 text-white rounded hover:bg-blue-600">Login</button>
      <p class="mt-4 text-center">Don't have an account? <a href="register.php" class="text-blue-500">Register here</a></p>
    </form>
  </div>
</div>
</body>
</html>
