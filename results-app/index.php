<?php
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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f9f9f9;
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
        .refresh-btn {
            display: block;
            margin: 20px auto;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .refresh-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Analytics Results Dashboard</h1>
        
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
        
        <button class="refresh-btn" onclick="location.reload()">Refresh Data</button>
    </div>
</body>
</html>