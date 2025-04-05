<!--This PHP script creates an admin panel for managing service intervals in a car service tracking system, 
allowing logged-in administrators to perform CRUD operations (create, read, update, delete) 
on service types through a user-friendly interface 
that includes forms for adding and updating services, a table displaying existing services, 
and JavaScript functions for handling form visibility and page refreshes, 
while ensuring secure database interactions and session management.-->

<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php"); // Redirect to login page
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

// Fetch service intervals
$serviceQuery = "SELECT * FROM service_intervals ORDER BY service_type, time_interval, mileage_interval";
$serviceResult = $conn->query($serviceQuery);

// Check if the query was successful
if (!$serviceResult) {
    die("Query failed: " . $conn->error);
}

//handle delete logic
if (isset($_POST['delete_message'])) {
    $id = $_POST['message_id'];
    $deleteQuery = "DELETE FROM service_intervals WHERE id=?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Record deleted successfully');</script>";
    } else {
        echo "<script>alert('Error deleting record: " . $stmt->error . "');</script>";
    }
    
    $stmt->close();
}

// Handle update logic
if (isset($_POST['update_service'])) {
    $id = $_POST['update_id'];
    $serviceType = $_POST['update_service_type'];
    $timeInterval = $_POST['update_time_interval'];
    $mileageInterval = $_POST['update_mileage_interval'];

    // Update query
    $updateQuery = "UPDATE service_intervals SET service_type=?, time_interval=?, mileage_interval=? WHERE id=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssi", $serviceType, $timeInterval, $mileageInterval, $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Record updated successfully');</script>";
    } else {
        echo "<script>alert('Error updating record: " . $stmt->error . "');</script>";
    }
    
    $stmt->close();
}

// Handle add logic
if (isset($_POST['add_service'])) {
    $newServiceType = $_POST['new_service_type'];
    $newTimeInterval = $_POST['new_time_interval'];
    $newMileageInterval = $_POST['new_mileage_interval'];

    // Insert query
    $insertQuery = "INSERT INTO service_intervals (service_type, time_interval, mileage_interval) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sss", $newServiceType, $newTimeInterval, $newMileageInterval);
    
    if ($stmt->execute()) {
        echo "<script>alert('New service type added successfully');</script>";
    } else {
        echo "<script>alert('Error adding service type: " . $stmt->error . "');</script>";
    }
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Panel</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                background-color: #566573;
                color: var(--dark-color);
            }
        </style>
        <script>
            function openUpdateForm(id, serviceType, timeInterval, mileageInterval) {
                document.getElementById('update_id').value = id;
                document.getElementById('update_service_type').value = serviceType;
                document.getElementById('update_time_interval').value = timeInterval;
                document.getElementById('update_mileage_interval').value = mileageInterval;
                document.getElementById('updateForm').style.display = 'block';
            }
            function refreshPage() {
                setTimeout(function() {
                    location.reload();
                }, 100);
            }
        </script>
    </head>
    <body>
        <div class="background"></div>
        <nav class="navbar navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="">Admin Panel</a>
                <a href="admin_serviceType.php" class="btn btn-outline-light">Services</a>
                <a href="admin_carType.php" class="btn btn-outline-light">Cars</a>
                <a href="admin_userMessages.php" class="btn btn-outline-light">Messages</a>
                <a href="admin_logout.php" class="btn btn-danger">Logout</a>            
            </div>
        </nav>

        <div class="container mt-5">    
            <h2 class="mt-5">Existing Service Types</h2>
            <button class="btn btn-success mb-3" onclick="document.getElementById('addForm').style.display='block'">Add New Service Type</button>

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Service Type</th>
                        <th>Time Interval (Months)</th>
                        <th>Mileage Interval (Km)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $serviceResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row["service_type"]); ?></td>
                            <td><?php echo htmlspecialchars($row["time_interval"]); ?></td>
                            <td><?php echo htmlspecialchars($row["mileage_interval"]); ?></td>
                            <td>
                            <button class="btn btn-primary btn-sm" onclick="openUpdateForm(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['service_type']); ?>', '<?php echo htmlspecialchars($row['time_interval']); ?>', '<?php echo htmlspecialchars($row['mileage_interval']); ?>')">Update</button>
                                <form method="POST" style="display:inline;" onsubmit="refreshPage();">
                                    <input type="hidden" name="message_id" value="<?php echo $row["id"]; ?>">
                                    <button type="submit" name="delete_message" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div id="addForm" style="display:none;">
                <h3>Add New Service Type</h3>
                <form method="POST" onsubmit="refreshPage();">
                    <div>
                        <label>Service Type:</label>
                        <input type="text" name="new_service_type" required>
                    </div>
                    <div>
                        <label>Time Interval (Months):</label>
                        <input type="number" name="new_time_interval" required>
                    </div>
                    <div>
                        <label>Mileage Interval (Km):</label>
                        <input type="number" name="new_mileage_interval" required>
                    </div>
                    <button type="submit" name="add_service" class="btn btn-primary">Add</button>
                </form>
            </div>

            <div id="updateForm" style="display:none;">
                <h3>Update Service Type</h3>
                <form method="POST" onsubmit="refreshPage();">
                    <input type="hidden" name="update_id" id="update_id">
                    <div>
                        <label>Service Type:</label>
                        <input type="text" name="update_service_type" id="update_service_type" required>
                    </div>
                    <div>
                        <label>Time Interval (Months):</label>
                        <input type="number" name="update_time_interval" id="update_time_interval" required>
                    </div>
                    <div>
                        <label>Mileage Interval (Km):</label>
                        <input type="number" name="update_mileage_interval" id="update_mileage_interval" required>
                    </div>
                    <button type="submit" name="update_service" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>    
        <!-- Footer -->
        <footer class="text-center py-3">
            &copy; 2024 Car Service Tracking System. All Rights Reserved.
            Contact Us: 0112785623 / info@carServiceTracking.lk
        </footer>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>