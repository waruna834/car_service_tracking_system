<!--This PHP script checks if a user is logged in, 
establishes a connection to a MySQL database, 
processes a form submission to book a car service by inserting the booking details into the service_bookings table, 
and provides feedback to the user on the success or failure of the booking operation, redirecting them accordingly.-->

<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carservicetrackingsystem";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $serviceType = $conn->real_escape_string($_POST['serviceType']);
    $serviceDate = $conn->real_escape_string($_POST['serviceDate']);
    $serviceCenter = $conn->real_escape_string($_POST['serviceCenter']);
    $location = $conn->real_escape_string($_POST['location']);

    // Insert booking details into the database
    $sql = "INSERT INTO service_bookings (user_id, service_type, service_date, service_center, location) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $user_id, $serviceType, $serviceDate, $serviceCenter, $location);

    if ($stmt->execute()) {
        echo "<script>alert('Booking confirmed successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error: Unable to process booking. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
}

$conn->close();
?>
