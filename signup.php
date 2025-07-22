<?php
// Include MongoDB connection file
require 'db.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    // Get form data
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $course = $_POST['course'];
    $password = $_POST['password'];

    // Validate inputs (you can add more validation as needed)
    if (empty($id) || empty($name) || empty($email) || empty($course) || empty($password)) {
        die("Please fill all fields.");
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if user already exists
    $existingUser = $studentsCollection->findOne(['id' => $id]);
    
    if ($existingUser) {
        echo "<script>alert('User ID already exists!'); window.location.href='signup.html';</script>";
        exit;
    }

    // Prepare document to insert
    $studentDocument = [
        'id' => $id,
        'name' => $name,
        'email' => $email,
        'course' => $course,
        'password' => $hashed_password
    ];

    // Insert new user document
    try {
        $result = $studentsCollection->insertOne($studentDocument);
        
        if ($result->getInsertedCount() == 1) {
            echo "<script>alert('Sign up successful!'); window.location.href='login.html';</script>";
        } else {
            echo "<script>alert('Error: Failed to add user.'); window.location.href='signup.html';</script>";
        }
    } catch (Exception $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='signup.html';</script>";
    }
}
?>
