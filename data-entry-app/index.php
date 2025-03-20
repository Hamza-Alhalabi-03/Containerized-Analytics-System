<?php

// Check if user is authenticated using cookies
if (!isset($_COOKIE['auth_status']) || $_COOKIE['auth_status'] !== 'authenticated') {
    header('Location: http://localhost:8081/login.php');
    exit;
}

// Get user information from cookies
$username = $_COOKIE['auth_user'] ?? 'Unknown User';
$name = $_COOKIE['auth_name'] ?? 'Unknown';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('mysql', 'devuser', 'devpassword', 'dev_analytics');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $developer_name = $_POST['developer_name'];
    $primary_language = $_POST['primary_language'];
    $years_experience = $_POST['years_experience'];
    $project_type = $_POST['project_type'];
    $hours_per_week = $_POST['hours_per_week'];
    
    $sql = "INSERT INTO developer_data (developer_name, primary_language, years_experience, project_type, hours_per_week) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $error_message = "Prepare failed: " . $conn->error;
    } else {
        $stmt->bind_param("ssiss", $developer_name, $primary_language, $years_experience, $project_type, $hours_per_week);
        
        if ($stmt->execute()) {
            $success_message = "Data submitted successfully!";
            // Verify the insert
            $verify_sql = "SELECT * FROM developer_data WHERE developer_name = ? AND created_at >= NOW() - INTERVAL 1 MINUTE";
            $verify_stmt = $conn->prepare($verify_sql);
            $verify_stmt->bind_param("s", $developer_name);
            $verify_stmt->execute();
            $result = $verify_stmt->get_result();
            
            if ($result->num_rows === 0) {
                $error_message = "Warning: Data might not have been saved. Please check the database.";
            }
            
            $verify_stmt->close();
        } else {
            $error_message = "Error executing query: " . $stmt->error;
        }
        
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer Analytics Data Entry</title>
    <style>
        :root {
            --primary-blue: #2563eb;
            --dark-blue: #1e40af;
            --light-blue: #dbeafe;
            --black: #1f2937;
            --white: #ffffff;
            --light-gray: #f3f4f6;
            --border-gray: #e5e7eb;
            --success-green: #10b981;
            --success-light: #d1fae5;
            --error-red: #ef4444;
            --error-light: #fee2e2;
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
            padding: 0;
            margin: 0;
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 24px;
        }
        
        .header {
            background-color: var(--white);
            border-bottom: 1px solid var(--border-gray);
            padding: 16px 0;
            margin-bottom: 32px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 800px;
            margin: 0 auto;
            padding: 0 24px;
        }
        
        .logo {
            color: var(--primary-blue);
            font-size: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .logo-icon {
            width: 36px;
            height: 36px;
            background-color: var(--primary-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: bold;
            margin-right: 12px;
        }
        
        .user-controls {
            display: flex;
            align-items: center;
        }
        
        .welcome {
            margin-right: 16px;
            font-size: 14px;
        }

        .nav-link {
            color: var(--white);
            background-color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 4px;
            margin-right: 16px;
            transition: background-color 0.2s, color 0.2s;
        }

        .nav-link:hover {
            background-color: var(--dark-blue);
        }
        
        .logout-link {
            color: white;
            background-color: #ef4444; /* Red background */
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .logout-link:hover {
            background-color: #dc2626; /* Darker red on hover */
        }
        
        h1 {
            color: var(--black);
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 24px;
        }
        
        .card {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 32px;
            margin-bottom: 32px;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: var(--black);
        }
        
        input, select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-gray);
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
            background-color: var(--white);
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }
        
        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%231f2937' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: calc(100% - 12px) center;
            padding-right: 40px;
        }
        
        .row {
            display: flex;
            gap: 24px;
            margin-bottom: 0;
        }
        
        .row .form-group {
            flex: 1;
        }
        
        button {
            background-color: var(--primary-blue);
            color: var(--white);
            border: none;
            border-radius: 6px;
            padding: 14px 24px;
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

        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 24px;
        }
        
        .success {
            color: var(--success-green);
            background-color: var(--success-light);
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 24px;
        }
        
        .error {
            color: var(--error-red);
            background-color: var(--error-light);
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 24px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <div class="logo-icon">DA</div>
                Developer Analytics
            </div>
            <div class="user-controls">
                <span class="welcome">Welcome, <?php echo htmlspecialchars($name); ?></span>
                <a href="http://localhost:8082" class="nav-link">View Results</a>
                <a href="logout.php" class="logout-link">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <h1>Developer Data Entry</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <form method="post" action="">
                <div class="row">
                    <div class="form-group">
                        <label for="developer_name">Developer Name</label>
                        <input type="text" id="developer_name" name="developer_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="primary_language">Primary Programming Language</label>
                        <select id="primary_language" name="primary_language" required>
                            <option value="">Select a language</option>
                            <option value="PHP">PHP</option>
                            <option value="JavaScript">JavaScript</option>
                            <option value="Python">Python</option>
                            <option value="Java">Java</option>
                            <option value="C#">C#</option>
                            <option value="Ruby">Ruby</option>
                            <option value="Go">Go</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="form-group">
                        <label for="years_experience">Years of Experience</label>
                        <input type="number" id="years_experience" name="years_experience" min="0" max="50" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="hours_per_week">Hours Coding Per Week</label>
                        <input type="number" id="hours_per_week" name="hours_per_week" min="1" max="168" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="project_type">Project Type</label>
                    <select id="project_type" name="project_type" required>
                        <option value="">Select a project type</option>
                        <option value="Web Application">Web Application</option>
                        <option value="Mobile App">Mobile App</option>
                        <option value="Desktop Software">Desktop Software</option>
                        <option value="API/Microservice">API/Microservice</option>
                        <option value="Data Analysis">Data Analysis</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="button-container">
                    <button type="submit">Submit Data</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>