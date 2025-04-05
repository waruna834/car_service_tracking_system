<!--This PHP script verifies that a user is logged in, 
connects to a MySQL database to retrieve distinct years of service records for a specified vehicle model associated with the user, 
and returns the list of years as a JSON response.-->

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

$model = $_POST['model'];
$user_id = $_SESSION['user_id'];

$years_query = "SELECT DISTINCT year FROM service_records WHERE model = ? AND user_id = ?";
$stmt = $conn->prepare($years_query);
$stmt->bind_param("si", $model, $user_id);  // 's' for string (model), 'i' for integer (user_id)
$stmt->execute();
$years_result = $stmt->get_result();

$years = [];
while ($row = $years_result->fetch_assoc()) {
    $years[] = $row['year'];
}
$stmt->close();
$conn->close();

echo json_encode($years);
?>
