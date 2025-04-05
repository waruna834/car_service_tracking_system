<!--This PHP script connects to a MySQL database, 
retrieves vehicle service information from a JSON payload sent via a POST request, 
and securely inserts the data into a vehicle table associated with the logged-in user, 
returning a JSON response indicating success or failure of the operation.-->

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carservicetrackingsystem";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Get the user ID from the session
session_start();
$userId = $_SESSION['user_id']; // Ensure the user is logged in

$data = json_decode(file_get_contents("php://input"), true);

$brand = $data["brand"];
$model = $data["model"];
$year = $data["year"];
$serviceType = $data["serviceType"];
$serviceDate = $data["serviceDate"];
$currentMileage = $data["currentMileage"];
$nextServiceDate = $data["nextServiceDate"];
$nextServiceMileage = $data["nextServiceMileage"];

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare(
    "INSERT INTO vehicle (user_id, brand, model, year, service_type, service_date, current_mileage, next_service_date, next_service_mileage) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("ississisi", $userId, $brand, $model, $year, $serviceType, $serviceDate, $currentMileage, $nextServiceDate, $nextServiceMileage);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "id" => $conn->insert_id]);
} else {
    echo json_encode(["success" => false, "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
