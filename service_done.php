<!--This PHP script processes a POST request to update a user's car service record by verifying their session, 
fetching the current service details, calculating the next service date and mileage based on predefined intervals, 
inserting the completed service into a history table, and updating the original service record to reflect its completion status.-->

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    exit("Unauthorized");
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carservicetrackingsystem";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $service_id = intval($_POST['id']);
    $user_id = $_SESSION['user_id']; // Get logged-in user's ID

    // Fetch current service details
    $stmt = $conn->prepare("SELECT * FROM service_records WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $service_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        exit("Invalid Service ID or Unauthorized Access");
    }
    
    $service = $result->fetch_assoc();
    $stmt->close();

    $brand = $service['brand'];
    $model = $service['model'];
    $year = $service['year'];
    $service_type = $service['service_type'];
    $last_service_date = $service['next_service_date'];
    $mileage_per_week = $service['mileage_per_week'];
    $current_mileage = $service['mileage'];

    // Fetch service interval from admin settings
    $interval_stmt = $conn->prepare("SELECT time_interval, mileage_interval FROM service_intervals WHERE service_type = ?");
    $interval_stmt->bind_param("s", $service_type);
    $interval_stmt->execute();
    $interval_result = $interval_stmt->get_result();

    if ($interval_result->num_rows === 0) {
        exit("Service type not found in admin settings");
    }
    
    $interval_data = $interval_result->fetch_assoc();
    $interval_stmt->close();

    $time_interval = intval($interval_data['time_interval']);
    $mileage_interval = intval($interval_data['mileage_interval']);

    // Calculate new service date dynamically based on time interval
    $new_service_date = date('Y-m-d', strtotime($last_service_date . " +$time_interval months"));

    // Calculate mileage estimate for next service
    $new_mileage = $current_mileage + ($mileage_per_week * ($time_interval * 4)); // Approx 4 weeks per month

    // Insert completed service details into `service_history`
    $history_stmt = $conn->prepare("INSERT INTO service_history (user_id, brand, model, year, service_type, next_service_date, mileage) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)");
    $history_stmt->bind_param("isssssi", $user_id, $brand, $model, $year, $service_type, $last_service_date, $current_mileage);

    if (!$history_stmt->execute()) {
        echo "Error saving to service history: " . $history_stmt->error;
        $history_stmt->close();
        $conn->close();
        exit();
    }
    $history_stmt->close();

    // Update service record with new next service date and mileage
    $update_stmt = $conn->prepare("UPDATE service_records 
        SET last_service_date = ?, next_service_date = ?, mileage = ?, status = 'Completed' 
        WHERE id = ?");
    $update_stmt->bind_param("ssii", $last_service_date, $new_service_date, $new_mileage, $service_id);

    if ($update_stmt->execute()) {
        echo "Success";
    } else {
        echo "Error updating service record: " . $update_stmt->error;
    }

    $update_stmt->close();
}

$conn->close();
?>
