<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Include the database connection
require 'db.php'; // MongoDB connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handling Sign In
    if (isset($_POST['signin'])) {
        $id = $_POST['id'];
        $password = $_POST['password'];

        // Query MongoDB to find the user by ID
        $user = $studentsCollection->findOne(['id' => $id]);

        // Check if user exists and verify password
        if ($user) {
            $hashed_password = $user['password'];
            $name = $user['name'];

            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Set session variables to track the logged-in student
                $_SESSION['id'] = $id;
                $_SESSION['name'] = $name;

                // Redirect to the attendance page
                header('Location: attendance.php');
                exit;
            } else {
                echo "<script>alert('Password verification failed!'); window.location.href='login.html';</script>";
            }
        } else {
            echo "<script>alert('No account found with this ID!'); window.location.href='login.html';</script>";
        }
    }
}
?>
