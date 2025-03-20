<?php
// Include Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Output loaded extensions
echo "Loaded PHP Extensions:<br>";
foreach (get_loaded_extensions() as $ext) {
    echo "$ext<br>";
}

echo "<hr>";

// Test MongoDB connection
echo "Testing MongoDB Connection:<br>";
try {
    // MongoDB connection details
    $mongoClient = new MongoDB\Client("mongodb://analytics_user:analytics_password@mongodb:27017/analytics_data");
    
    // Ping the database
    $result = $mongoClient->analytics_data->command(['ping' => 1]);
    
    echo "MongoDB Connection Successful!<br>";
    echo "Result: <pre>" . print_r($result->toArray(), true) . "</pre>";
} catch (Exception $e) {
    echo "MongoDB Connection Failed:<br>";
    echo "Error: " . $e->getMessage() . "<br>";
}