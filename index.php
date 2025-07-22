<?php
// Start a session to store student attendance records across requests
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include MongoDB connection
require 'db.php';

// Handle barcode scanning and attendance recording
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['barcode'])) {
    $id = $_POST['barcode'];

    // Find student in MongoDB
    $student = $studentsCollection->findOne(['id' => $id]);

    if ($student) {
        date_default_timezone_set('Asia/Kolkata');
        $now = new DateTime();
        $entryTime = $now->format('Y-m-d H:i:s');
        
        // Check for recent scan (within last 5 seconds) to prevent duplicates
        $fiveSecondsAgo = (new DateTime())->modify('-5 seconds');
        $recentScans = $attendanceCollection->count([
            'id' => $id,
            'entry_time' => [
                '$gte' => new MongoDB\BSON\UTCDateTime($fiveSecondsAgo->getTimestamp() * 1000)
            ]
        ]);

        if ($recentScans == 0) { // No duplicate found
            $attendanceCollection->insertOne([
                'id' => $id,
                'entry_time' => new MongoDB\BSON\UTCDateTime($now->getTimestamp() * 1000)
            ]);
        }
    } else {
        echo "<script>alert('Student not found!');</script>";
    }

    // Redirect to prevent form resubmission on refresh
    header("Location: index.php");
    exit();
}

// Handle filtering
$filter = [];
if (!empty($_POST['filter_date']) || !empty($_POST['filter_branch'])) {
    if (!empty($_POST['filter_date'])) {
        $dateStart = new DateTime($_POST['filter_date']);
        $dateStart->setTime(0, 0, 0);
        $dateEnd = clone $dateStart;
        $dateEnd->setTime(23, 59, 59);
        
        $filter['entry_time'] = [
            '$gte' => new MongoDB\BSON\UTCDateTime($dateStart->getTimestamp() * 1000),
            '$lte' => new MongoDB\BSON\UTCDateTime($dateEnd->getTimestamp() * 1000)
        ];
    }
    if (!empty($_POST['filter_branch'])) {
        $filter['course'] = $_POST['filter_branch'];
    }
}

// Fetch filtered attendance records
$pipeline = [
    [
        '$lookup' => [
            'from' => 'students',
            'localField' => 'id',
            'foreignField' => 'id',
            'as' => 'student_info'
        ]
    ],
    ['$unwind' => '$student_info'],
    [
        '$project' => [
            'id' => 1,
            'entry_time' => 1,
            'name' => '$student_info.name',
            'course' => '$student_info.course',
            'email' => '$student_info.email'
        ]
    ]
];

// Add match stage for filtering if needed
if (!empty($filter)) {
    array_unshift($pipeline, ['$match' => $filter]);
}

// Sort by entry time descending
$pipeline[] = ['$sort' => ['entry_time' => -1]];

$cursor = $attendanceCollection->aggregate($pipeline);
$filteredAttendance = [];

foreach ($cursor as $document) {
    // Convert MongoDB ISODate to PHP DateTime for display
    $entry_time = $document['entry_time']->toDateTime();
    $document['entry_time'] = $entry_time->format('Y-m-d H:i:s');
    $filteredAttendance[] = $document;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Attendance System</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <script>
        // Function to automatically focus the barcode input field
        function focusBarcodeInput() {
            document.getElementById('barcode').focus();
        }

        // Function to print the attendance page
        function printAttendance() {
            window.print();
        }

        // Automatically focus the input field when the page loads
        window.onload = focusBarcodeInput;
    </script>
</head>
<body>
    <div class="container">
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
                <li><a href="#" class="logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="nav-item">Log out</span>
                </a></li>
            </ul>
        </nav>

        <div class="main-content">
            <h2>Library Attendance System</h2>

            <!-- Barcode Input Form -->
            <form method="POST" action="index.php">
                <div class="scan-section">
                    <input type="text" id="barcode" name="barcode" placeholder="Scan Barcode Here" required>
                    <button type="submit">Scan</button>
                </div>
            </form>

            <!-- Filter Form for Date and Branch -->
            <form method="POST" action="index.php">
                <div class="filter-section">
                    <label for="filter_date">Filter by Date:</label>
                    <input type="date" name="filter_date" id="filter_date">
                    
                    <label for="filter_branch">Filter by Branch:</label>
                    <select name="filter_branch" id="filter_branch">
                        <option value="">Select Branch</option>
                        <option value="TE CSE">TE CSE</option>
                        <option value="SE CSE">SE CSE</option>
                        <option value="FE CSE">FE CSE</option>
                    </select>

                    <button type="submit">Filter</button>
                </div>
            </form>

            <!-- Attendance Table -->
            <table id="attendanceTable">
                <thead>
                    <tr>
                        <th>StudentID</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Email</th>
                        <th>EntryTime</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Display filtered attendance records
                    foreach ($filteredAttendance as $student) {
                        echo "<tr>
                            <td>{$student['id']}</td>
                            <td>{$student['name']}</td>
                            <td>{$student['course']}</td>
                            <td>{$student['email']}</td>
                            <td>{$student['entry_time']}</td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Print Button -->
            <div class="print-section">
                <button onclick="printAttendance()">Print Attendance</button>
            </div>
        </div>
    </div>
</body>
</html>
