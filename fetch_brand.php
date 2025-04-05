<!--This PHP script checks if a user is logged in, 
connects to a MySQL database to retrieve distinct vehicle brands associated with the logged-in user's service records, 
and returns the list of brands as a JSON response.-->

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carservicetrackingsystem";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetch distinct brands for the logged-in user
$brands_query = "SELECT DISTINCT brand FROM service_records WHERE user_id = ?";
$stmt = $conn->prepare($brands_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$brands_result = $stmt->get_result();

$brands = [];
while ($row = $brands_result->fetch_assoc()) {
    $brands[] = $row['brand'];
}

// Close statement and connection
$stmt->close();
$conn->close();

// Return the brands as JSON
echo json_encode($brands);
?>
