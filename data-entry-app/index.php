<?php

// Check if user is authenticated using cookies
if (!isset($_COOKIE['auth_status']) || $_COOKIE['auth_status'] !== 'authenticated') {
    header('Location: http://localhost:8081/login.php');
    exit;
}

// Get user information from cookies
$username = $_COOKIE['auth_user'] ?? 'Unknown User';
$name = $_COOKIE['auth_name'] ?? 'Unknown';

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
    $stmt->bind_param("ssiss", $developer_name, $primary_language, $years_experience, $project_type, $hours_per_week);
    
    if ($stmt->execute()) {
        $success_message = "Data submitted successfully!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Developer Analytics Data Entry</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; cursor: pointer; }
        .success { color: green; }
        .error { color: red; }
        .logout { float: right; }
    </style>
</head>
<body>
    <div class="logout">
        <a href="logout.php">Logout</a>
        <span>Welcome, <?php echo htmlspecialchars($name); ?></span>
    </div>
    
    <h1>Developer Analytics Data Entry</h1>
    
    <?php if (isset($success_message)): ?>
        <p class="success"><?php echo $success_message; ?></p>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <p class="error"><?php echo $error_message; ?></p>
    <?php endif; ?>
    
    <form method="post" action="">
        <div class="form-group">
            <label for="developer_name">Developer Name:</label>
            <input type="text" id="developer_name" name="developer_name" required>
        </div>
        
        <div class="form-group">
            <label for="primary_language">Primary Programming Language:</label>
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
        
        <div class="form-group">
            <label for="years_experience">Years of Experience:</label>
            <input type="number" id="years_experience" name="years_experience" min="0" max="50" required>
        </div>
        
        <div class="form-group">
            <label for="project_type">Project Type:</label>
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
        
        <div class="form-group">
            <label for="hours_per_week">Hours Coding Per Week:</label>
            <input type="number" id="hours_per_week" name="hours_per_week" min="1" max="168" required>
        </div>
        
        <button type="submit">Submit Data</button>
    </form>
</body>
</html>