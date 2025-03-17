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
        // Cookies that can be read by the data entry app
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Service</title>
    <style>
        :root {
            --primary-blue: #2563eb;
            --dark-blue: #1e40af;
            --light-blue: #dbeafe;
            --black: #1f2937;
            --white: #ffffff;
            --light-gray: #f3f4f6;
            --error-red: #ef4444;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-gray);
            color: var(--black);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
            padding: 32px;
        }
        
        .header {
            margin-bottom: 24px;
            text-align: center;
        }
        
        h1 {
            color: var(--primary-blue);
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 16px;
        }
        
        .logo {
            width: 48px;
            height: 48px;
            background-color: var(--primary-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: bold;
            font-size: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: var(--black);
        }
        
        input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        input:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }
        
        button {
            background-color: var(--primary-blue);
            color: var(--white);
            border: none;
            border-radius: 6px;
            padding: 14px;
            width: 100%;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: var(--dark-blue);
        }
        
        button:active {
            transform: translateY(1px);
        }
        
        .error {
            color: var(--error-red);
            background-color: #fee2e2;
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .note {
            margin-top: 24px;
            padding: 16px;
            background-color: var(--light-blue);
            border-radius: 6px;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .note strong {
            color: var(--primary-blue);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <div class="logo">DA</div>
            </div>
            <h1>Developer Analytics</h1>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            
            <button type="submit">Sign In</button>
        </form>
        
        <div class="note">
            <strong>Note:</strong> This is a prototype authentication service
        </div>
    </div>
</body>
</html>