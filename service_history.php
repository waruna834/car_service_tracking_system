<!--This PHP script retrieves and displays the service history of a logged-in user from a MySQL database, 
presenting the data in a styled HTML table with a live search feature, 
while ensuring that only authenticated users can access the page and providing navigation options to the dashboard and logout.-->

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

$user_id = $_SESSION['user_id'];

// Fetch service history records for the logged-in user
$history_query = "SELECT * FROM service_history WHERE user_id = ? ORDER BY next_service_date DESC";
$stmt = $conn->prepare($history_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$history_result = $stmt->get_result();

$service_history = [];
while ($row = $history_result->fetch_assoc()) {
    $service_history[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .container {
            max-width: 1350px; /* Adjust width as needed */
            background: rgba(255, 255, 255, 0.1); /* Semi-transparent white background */
            backdrop-filter: blur(10px); /* Apply blur effect */
            -webkit-backdrop-filter: blur(10px); /* For Safari support */
            padding: 20px;
            border-radius: 15px; /* Rounded corners */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Subtle shadow */
            color: white; /* Text color */
        }
    </style>
</head>
<body>
    <div class="background"></div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php"><strong>Car Service Tracking System</strong></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="btn btn-danger" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center">Service History</h2>

        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Search Service History...">
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Service Type</th>
                    <th>Last Service Date</th>
                    <th>Mileage at Service</th>
                </tr>
            </thead>
            <tbody id="historyTable">
                <?php if (!empty($service_history)): ?>
                    <?php foreach ($service_history as $service): ?>
                        <tr>
                            <td><?= htmlspecialchars($service['brand']) ?></td>
                            <td><?= htmlspecialchars($service['model']) ?></td>
                            <td><?= htmlspecialchars($service['year']) ?></td>
                            <td><?= htmlspecialchars($service['service_type']) ?></td>
                            <td><?= htmlspecialchars($service['next_service_date']) ?></td>
                            <td><?= htmlspecialchars($service['mileage']) ?> km</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No service history found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Live search filter
        $(document).ready(function () {
            $("#searchInput").on("keyup", function () {
                let value = $(this).val().toLowerCase();
                $("#historyTable tr").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
        });
    </script>

    <!-- Footer -->
    <footer class="text-center py-3">
        &copy; 2024 Car Service Tracking System. All Rights Reserved.
        Contact Us: 0112785623 / info@carServiceTracking.lk
    </footer>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>