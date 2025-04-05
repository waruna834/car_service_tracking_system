<!--This PHP script processes a service record submission for a logged-in user by validating the input data, 
calculating the next service date and mileage based on service intervals from a MySQL database, 
adjusting the intervals based on driving conditions and mileage per week, 
and then inserting the data into the service_records table while returning a JSON response indicating success or 
any errors encountered during the process.-->

<?php
session_start();
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carservicetrackingsystem";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed."]));
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die(json_encode(["error" => "User not authenticated."]));
}
$userId = $_SESSION['user_id'];

// Validate form data
if (!isset($_POST['brand'], $_POST['model'], $_POST['year'], $_POST['mileage'], $_POST['mileage_per_week'], $_POST['driving_conditions'], $_POST['service_type'], $_POST['last_service_date'])) {
    die(json_encode(["error" => "Missing form data"]));
}

// Retrieve form data
$brand = $_POST['brand'];
$model = $_POST['model'];
$year = $_POST['year'];
$currentMileage = intval($_POST['mileage']);
$mileagePerWeek = intval($_POST['mileage_per_week']);
$drivingCondition = $_POST['driving_conditions'];
$serviceType = $_POST['service_type'];
$lastServiceDate = $_POST['last_service_date'];

// Get service intervals from the database
$stmt = $conn->prepare("SELECT time_interval, mileage_interval FROM service_intervals WHERE service_type = ?");
$stmt->bind_param("s", $serviceType);
$stmt->execute();
$stmt->bind_result($timeInterval, $mileageInterval);
$stmt->fetch();
$stmt->close();

if (!$timeInterval || !$mileageInterval) {
    die(json_encode(["error" => "Service interval data not found for service type: $serviceType"]));
}

// Adjust time interval based on mileage per week
if ($mileagePerWeek >= 300) {
    $timeInterval *= 0.5;
} elseif ($mileagePerWeek >= 251) {
    $timeInterval *= 0.6;
} elseif ($mileagePerWeek >= 201) {
    $timeInterval *= 0.75;
} elseif ($mileagePerWeek >= 151) {
    $timeInterval *= 0.85;
} elseif ($mileagePerWeek >= 101) {
    $timeInterval *= 0.9;
} elseif ($mileagePerWeek >= 51) {
    $timeInterval *= 0.95;
}

// Further adjust for off-road conditions
if (strtolower($drivingCondition) === "off road") {
    $timeInterval *= 0.8;
}

// Ensure valid time interval as an integer
$timeInterval = round($timeInterval);

// Ensure valid last service date
if (!strtotime($lastServiceDate)) {
    die(json_encode(["error" => "Invalid last service date format"]));
}

// Calculate next service date
$nextServiceDate = date('Y-m-d', strtotime("+$timeInterval months", strtotime($lastServiceDate)));

// Adjust mileage interval based on weekly mileage
$adjustedMileageInterval = $mileageInterval * ($mileagePerWeek / 100);
$nextMileage = $currentMileage + round($adjustedMileageInterval);

// Insert data into service_records table
$stmt = $conn->prepare("INSERT INTO service_records (user_id, brand, model, year, mileage, mileage_per_week, driving_conditions, service_type, last_service_date, next_service_date)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssiiisss", $userId, $brand, $model, $year, $currentMileage, $mileagePerWeek, $drivingCondition, $serviceType, $lastServiceDate, $nextServiceDate);

if (!$stmt->execute()) {
    die(json_encode(["error" => "Data insert failed: " . $stmt->error]));
}
$stmt->close();

// Return JSON response
echo json_encode([
    "success" => true,
    "service_type" => $serviceType,
    "next_mileage" => $nextMileage,
    "next_service_date" => $nextServiceDate
]);

$conn->close();
?>
