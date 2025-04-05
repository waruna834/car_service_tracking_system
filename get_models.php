<!--This PHP script connects to a MySQL database, 
retrieves car models associated with a specified brand from the car_types table when a POST request is made, 
and outputs the models as HTML <option> elements for a dropdown menu, 
ensuring that the output is safe from XSS attacks by using htmlspecialchars().-->

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carservicetrackingsystem";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["brand"])) {
    $brand = $_POST["brand"];

    $stmt = $conn->prepare("SELECT model FROM car_types WHERE brand = ? ORDER BY model");
    $stmt->bind_param("s", $brand);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<option value="">Select a Model</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row["model"]) . '">' . htmlspecialchars($row["model"]) . '</option>';
    }

    $stmt->close();
}

$conn->close();
?>
