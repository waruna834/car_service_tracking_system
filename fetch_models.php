<!--This PHP script verifies that a user is logged in, 
connects to a MySQL database to retrieve distinct vehicle models associated with a specified brand from the user's service records, 
and returns the list of models as a JSON response.-->

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
$user_id = $_SESSION['user_id'];

$models_query = "SELECT DISTINCT model FROM service_records WHERE brand = ? AND user_id = ?";
$stmt = $conn->prepare($models_query);
$stmt->bind_param("si", $brand, $user_id);  // 's' for string (brand), 'i' for integer (user_id)
$stmt->execute();
$models_result = $stmt->get_result();

$models = [];
while ($row = $models_result->fetch_assoc()) {
    $models[] = $row['model'];
}
$stmt->close();
$conn->close();

echo json_encode($models);
?>
