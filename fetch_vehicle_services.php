<!--This PHP script checks if a user is logged in, 
connects to a MySQL database to retrieve service types and 
their next service dates for a specified vehicle brand, model, and year associated with the user, 
and returns the results as a JSON response.-->

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carservicetrackingsystem";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$brand = $_POST['brand'];
$model = $_POST['model'];
$year = $_POST['year'];
$user_id = $_SESSION['user_id'];

$services_query = "SELECT service_type, next_service_date FROM service_records WHERE brand = ? AND model = ? AND year = ? AND user_id = ?";
$stmt = $conn->prepare($services_query);
$stmt->bind_param("ssii", $brand, $model, $year, $user_id);  // 's' for string (brand, model), 'i' for integers (year, user_id)
$stmt->execute();
$services_result = $stmt->get_result();

$services = [];
while ($row = $services_result->fetch_assoc()) {
    $services[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode($services);
?>
