<?php
// Start the session to access session variables
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['id'])) {
    header('Location: login.html');
    exit;
}

// Include the database connection
require 'db.php';

$id = $_SESSION['id'];
$name = $_SESSION['name'];

// Fetch attendance data for the logged-in student from MongoDB
$filter = ['id' => $id];
$options = [
    'sort' => ['entry_time' => -1] // Sort by entry_time in descending order
];

$cursor = $attendanceCollection->find($filter, $options);
$attendance_records = [];

foreach ($cursor as $document) {
    // Format the MongoDB ISODate to separate date and time
    $entry_time = new DateTime($document['entry_time']->toDateTime()->format('c'));
    $attendance_records[] = [
        'entry_date' => $entry_time->format('Y-m-d'),
        'entry_time' => $entry_time->format('H:i:s')
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="attendance.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <title>Attendance Records</title>
</head>
<body>
    <div class="container">
        <!-- Left Navigation Panel -->
        <nav>
            <ul>
                <li><a href="#" class="logo">
                    <img src="logo.png" alt="Logo">
                </a></li>
                <li><a href="home.html">
                    <i class="fas fa-home"></i>
                    <span class="nav-item">Home</span>
                </a></li>
                <li><a href="admin.html">
                    <i class="fas fa-book-open"></i>
                    <span class="nav-item">Library</span>
                </a></li>
                <li><a href="login.html">
                    <i class="fas fa-user-graduate"></i>
                    <span class="nav-item">Student</span>
                </a></li>
                <li><a href="contact.html">
                    <i class="fas fa-envelope"></i>
                    <span class="nav-item">Contact</span>
                </a></li>
                <li><a href="logout.php" class="logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="nav-item">Log Out</span>
                </a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Welcome, <?php echo htmlspecialchars($name); ?>!</h1>
            <h2>Your Attendance Records</h2>
            
            <!-- Display Attendance Data in a Table -->
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Entry Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($attendance_records)): ?>
                        <?php foreach ($attendance_records as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['entry_date']); ?></td>
                                <td><?php echo htmlspecialchars($record['entry_time']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">No attendance records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>
</body>
</html>
