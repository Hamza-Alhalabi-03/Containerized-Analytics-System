<?php

// Check if user is authenticated using cookies
if (!isset($_COOKIE['auth_status']) || $_COOKIE['auth_status'] !== 'authenticated') {
    header('Location: http://localhost:8081');
    exit;
}

// Get user information from cookies
$username = $_COOKIE['auth_user'] ?? 'Unknown User';
$name = $_COOKIE['auth_name'] ?? 'Unknown';

// Include Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// MongoDB connection details
$mongoClient = new MongoDB\Client("mongodb://analytics_user:analytics_password@mongodb:27017/analytics_data");

// Select database and collection
$database = $mongoClient->analytics_data;
$collection = $database->analytics_results;

// Find all documents in the collection, sort by timestamp descending
$cursor = $collection->find(
    [], 
    [
        'sort' => ['timestamp' => -1],
        'limit' => 10  // Limit to the 10 most recent entries
    ]
);

// Convert cursor to array
$results = iterator_to_array($cursor);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Results Dashboard</title>
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
            background-color: #ef4444;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .logout-link:hover {
            background-color: #dc2626;
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
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-gray);
        }
        
        th {
            background-color: var(--light-gray);
            font-weight: 500;
        }
        
        tr:hover {
            background-color: var(--light-blue);
        }
        
        .timestamp {
            font-size: 0.8em;
            color: #666;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #666;
        }
        
        h2 {
            color: var(--black);
            font-size: 20px;
            font-weight: 600;
            margin: 24px 0 16px 0;
        }
        
        .refresh-btn {
            background-color: var(--primary-blue);
            color: var(--white);
            border: none;
            border-radius: 6px;
            padding: 14px 24px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
            display: block;
            margin: 20px auto;
        }
        
        .refresh-btn:hover {
            background-color: var(--dark-blue);
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
                <a href="http://localhost:8080" class="nav-link">Enter Data</a>
                <a href="logout.php" class="logout-link">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <h1>Analytics Results Dashboard</h1>
        
        <div class="card">
            <?php if (empty($results)): ?>
                <div class="no-data">No analytics data available. Please run the statistics calculation first.</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Metric</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Get the most recent result
                        $latestResult = $results[0];
                        
                        // Display the timestamp
                        echo "<tr><td colspan='2' class='timestamp'>Data collected at: " . date('Y-m-d H:i:s', strtotime($latestResult['timestamp'])) . "</td></tr>";
                        
                        // Display each metric
                        $metrics = [
                            'total_records' => 'Total Records',
                            'total_developers' => 'Total Developers',
                            'avg_working_hours' => 'Average Working Hours',
                            'max_working_hours' => 'Maximum Working Hours',
                            'min_working_hours' => 'Minimum Working Hours',
                            'avg_experience' => 'Average Experience (years)'
                        ];
                        
                        foreach ($metrics as $key => $label) {
                            if (isset($latestResult[$key])) {
                                $value = is_numeric($latestResult[$key]) ? number_format($latestResult[$key], 2) : $latestResult[$key];
                                echo "<tr><td>{$label}</td><td>{$value}</td></tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
                
                <h2>Historical Data</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Total Records</th>
                            <th>Total Developers</th>
                            <th>Avg Working Hours</th>
                            <th>Avg Experience</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $result): ?>
                        <tr>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($result['timestamp'])); ?></td>
                            <td><?php echo $result['total_records']; ?></td>
                            <td><?php echo $result['total_developers']; ?></td>
                            <td><?php echo number_format($result['avg_working_hours'], 2); ?></td>
                            <td><?php echo number_format($result['avg_experience'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            
            <div style="text-align: center; margin-top: 24px;">
                <button class="refresh-btn" onclick="location.reload()">Refresh Data</button>
            </div>
        </div>
    </div>
</body>
</html>