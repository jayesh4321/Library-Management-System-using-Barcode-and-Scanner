<?php
// db.php (MongoDB connection)
require_once __DIR__ . '/vendor/autoload.php'; // Include Composer's autoloader

// MongoDB Atlas connection string
// Replace with your actual connection string from MongoDB Atlas
$connectionString = "mongodb+srv://jayeshchaurasia06:<jayesh4321>@cluster0.svk5mo9.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";

try {
    // Create a MongoDB client
    $mongoClient = new MongoDB\Client($connectionString);
    
    // Select database and collections
    $db = $mongoClient->xie;
    $studentsCollection = $db->students;
    $attendanceCollection = $db->attendance;
    
} catch (MongoDB\Driver\Exception\Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
