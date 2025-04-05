<!--The PHP script functions as an admin panel for managing car types in a car service tracking system, 
ensuring that only logged-in administrators can access it, 
while securely interacting with a MySQL database to perform CRUD operations using prepared statements, 
and providing a user-friendly interface with HTML, JavaScript for form management, and alerts for feedback on actions taken.-->

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

// Handle new car type addition
if (isset($_POST["add_car"])) {
    $brand = trim($_POST["brand"]);
    $model = trim($_POST["model"]);

    $insertQuery = "INSERT INTO car_types (brand, model) VALUES (?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ss", $brand, $model);
    
    if ($stmt->execute()) {
        echo "<script>alert('Car type added successfully');</script>";
    } else {
        echo "<script>alert('Error adding car type: " . $stmt->error . "');</script>";
    }
    
    $stmt->close();
}

// Handle update logic
if (isset($_POST['update_car'])) {
    $id = $_POST['update_id'];
    $brand = $_POST['update_brand'];
    $model = $_POST['update_model'];

    // Update query
    $updateQuery = "UPDATE car_types SET brand=?, model=? WHERE id=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssi", $brand, $model, $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Car type updated successfully');</script>";
    } else {
        echo "<script>alert('Error updating car type: " . $stmt->error . "');</script>";
    }
    
    $stmt->close();
}

//handle delete logic
if (isset($_POST['delete_message'])) {
    $id = $_POST['message_id'];
    $deleteQuery = "DELETE FROM car_types WHERE id=?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Record deleted successfully');</script>";
    } else {
        echo "<script>alert('Error deleting record: " . $stmt->error . "');</script>";
    }
    
    $stmt->close();
}

// Fetch car types
$carQuery = "SELECT * FROM car_types ORDER BY brand, model";
$carResult = $conn->query($carQuery);

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
            function openUpdateForm(id, brand, model) {
                document.getElementById('update_id').value = id;
                document.getElementById('update_brand').value = brand;
                document.getElementById('update_model').value = model;
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
        <h2 class="mt-5">Existing Car Types</h2>
        <button class="btn btn-success mb-3" onclick="document.getElementById('addForm').style.display='block'">Add New Car Type</button>
        
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $carResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["brand"]); ?></td>
                        <td><?php echo htmlspecialchars($row["model"]); ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="openUpdateForm(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['brand']); ?>', '<?php echo htmlspecialchars($row['model']); ?>')">Update</button>
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
            <h3>Add New Car Type</h3>
            <form method="POST" onsubmit="refreshPage();">
                <div>
                    <label>Brand:</label>
                    <input type="text" name="brand" required>
                </div>
                <div>
                    <label>Model:</label>
                    <input type="text" name="model" required>
                </div>
                <button type="submit" name="add_car" class="btn btn-primary">Add Car Type</button>
            </form>
        </div>

        <div id="updateForm" style="display:none;">
            <h3>Update Car Type</h3>
            <form method="POST" onsubmit="refreshPage();">
                <input type="hidden" id="update_id" name="update_id">
                <div>
                    <label>Brand:</label>
                    <input type="text" id="update_brand" name="update_brand" required>
                </div>
                <div>
                    <label>Model:</label>
                    <input type="text" id="update_model" name="update_model" required>
                </div>
                <button type="submit" name="update_car" class="btn btn-primary">Update Car Type</button>
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