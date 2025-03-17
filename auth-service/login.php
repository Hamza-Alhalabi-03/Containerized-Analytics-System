<?php

// Include the simple users configuration
require_once 'config.php';

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Simple authentication check
    if (isset($users[$username]) && $users[$username]['password'] === $password) {
        // Set cookies that can be read by the data entry app
        setcookie("auth_user", $username, 0, "/", "", false, false);
        setcookie("auth_name", $users[$username]['name'], 0, "/", "", false, false);
        setcookie("auth_status", "authenticated", 0, "/", "", false, false);
        
        // Redirect with absolute URL
        header('Location: http://localhost:8080/');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Authentication Service</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; cursor: pointer; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Developer Analytics Login</h1>
    
    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <form method="post" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit">Login</button>
    </form>
    
    <p><strong>Note:</strong> This is a prototype authentication service. For testing, use usernames user1 through user5 with passwords password1 through password5.</p>
</body>
</html>