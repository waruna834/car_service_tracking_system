<!--This PHP script checks if a user is logged in, establishes a connection to a MySQL database, 
and deletes a specific service record associated with the logged-in user based on the provided ID from a POST request, 
returning a success message or an error if the operation fails.-->

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carservicetrackingsystem";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['id'])) {
    $serviceId = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM service_records WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $serviceId, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>