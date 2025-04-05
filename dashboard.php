<!--This PHP and HTML code creates a "Car Health Dashboard" for logged-in users of a car service tracking system, 
displaying their upcoming services and bookings, allowing them to manage their service records, 
select vehicles for service overviews, and visualize service proximity through a dynamic chart, 
all while utilizing Bootstrap for styling and jQuery for interactive features such as AJAX requests for fetching data 
and updating service statuses.-->

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

// Fetch user data including name
$userId = $_SESSION['user_id'];
$userResult = $conn->query("SELECT fname FROM user_details WHERE id = $userId");

// Check if user data is found and retrieve name
if ($userResult->num_rows > 0) {
    $userData = $userResult->fetch_assoc();
    $userName = $userData['fname']; // Get the user's name
} else {
    $userName = "Guest"; // Default value if user not found (should not happen)
}

// Fetch user services
$services_query = "SELECT * FROM service_records WHERE user_id = ? ORDER BY next_service_date ASC";
$stmt = $conn->prepare($services_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$services_result = $stmt->get_result();

$services = [];
while ($row = $services_result->fetch_assoc()) {
    $services[] = $row;
}
$stmt->close();

// Fetch vehicle brands
$brands_query = "SELECT DISTINCT brand FROM service_records";
$brands_result = $conn->query($brands_query);
$brands = [];
while ($row = $brands_result->fetch_assoc()) {
    $brands[] = $row['brand'];
}

// Fetch all booking details for the logged-in user
$user_id = $_SESSION['user_id'];
$sql = "SELECT service_type, service_date, service_center, location FROM service_bookings WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Car Health Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/style.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <style>
            .card {
                margin: 15px;
            }
            .immediate {
                background-color: #f8d7da; /* Light red */
                border-color: #f5c6cb;
            }
            .normal {
                background-color: #d4edda; /* Light green */
                border-color: #c3e6cb;
            }
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
            .table-scroll {
                max-height: 200px; /* Adjust height as needed */
                overflow-y: auto; /* Enable vertical scrolling */
                display: block; /* Make the table body block-level */
            }

            .table-scroll table {
                width: 100%; /* Ensure the table takes full width */
            }

            .table-scroll thead, .table-scroll tbody {
                display: table; /* Ensure the header and body are displayed as tables */
                width: 100%; /* Ensure the header and body take full width */
                table-layout: fixed; /* Prevent layout issues */
            }
        </style>
    </head>
    <body>
        <div class="background"></div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href=""><strong>Car Service Tracking System</strong> - Welcome <?php echo htmlspecialchars($userName); ?>!</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="vehicleInfo.php">Service Predict</a></li>
                        <li class="nav-item"><a class="nav-link" href="findSolution.php">Find Solution</a></li>
                        <li class="nav-item"><a class="nav-link" href="service_history.php">Service History</a></li>
                        <li class="nav-item"><a class="nav-link" href="map.php">Service Map</a></li>
                        <li class="nav-item"><a class="nav-link" href="settings.php">Settings</a></li>
                        <li class="nav-item"><a class="btn btn-danger" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>   

        <div class="container">
            <h1 class="text-center my-4">Car Health Dashboard</h1>
            <!--upcoming service cards-->
                <h3>Upcoming Services</h3>
                <div id="serviceCards" class="row">
                    <?php foreach ($services as $service): ?>
                        <div class="col-md-3 mb-4"> <!-- 4 cards per row (3 columns per card) -->
                            <div class="card <?= (strtotime($service['next_service_date']) < time()) ? 'immediate' : 'normal' ?>" style="width: 100%;">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($service['brand']) ?> - <?= htmlspecialchars($service['model']) ?> - <?= htmlspecialchars($service['year']) ?></h5>
                                    <p class="card-text"><strong>Service:</strong> <?= htmlspecialchars($service['service_type']) ?></p>
                                    <p class="card-text"><strong>Next Service Date:</strong> <?= htmlspecialchars($service['next_service_date']) ?></p>
                                    <p class="card-text"><strong>Next Service Mileage:</strong> <?= htmlspecialchars($service['mileage']) ?></p>
                                    <button class="btn btn-success btn-sm mark-done" data-id="<?= $service['id'] ?>">Done</button>
                                    <button class="btn btn-danger btn-sm delete-service" data-id="<?= $service['id'] ?>">Delete</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <br>
            <!--Service booking list-->
                <h3>Your Bookings</h3><br>
                <?php if ($result->num_rows > 0): ?>
                    <div class="table-scroll">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Service Type</th>
                                    <th>Service Date</th>
                                    <th>Service Center</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $rowCount = 0; // Initialize row count
                                while ($rowCount < 5 && $row = $result->fetch_assoc()): // Limit to 5 rows ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['service_type']); ?></td>
                                        <td><?php echo htmlspecialchars($row['service_date']); ?></td>
                                        <td><?php echo htmlspecialchars($row['service_center']); ?></td>
                                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                                    </tr>
                                    <?php $rowCount++; // Increment row count ?>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center">No booking details available.</p>
                <?php endif; ?>
            </br>
            <!--Car health chart-->    
            </br>
                <h3>Select Car for Service Overview</h3></br>
                <form id="vehicleSelectionForm">
                    <select id="brand" name="brand" required>
                        <option value="">Select Brand</option>
                        <!-- Populate with models based on selected brand -->
                    </select>
                    <select id="model" name="model" required>
                        <option value="">Select Model</option>
                        <!-- Populate with models based on selected brand -->
                    </select>
                    <select id="year" name="year" required>
                        <option value="">Select Year</option>
                        <!-- Populate with years based on selected model -->
                    </select>
                    <button type="submit" class="btn btn-primary">Select Vehicle</button>
                </form>
            </br>   
                <canvas id="vehicleBarChart"></canvas>
            </br>
            
            
        </div>

        <script>
            // Handle vehicle selection
            $("#vehicleSelectionForm").submit(function (event) {
                event.preventDefault();
                let brand = $("#brand").val();
                let model = $("#model").val();
                let year = $("#year").val();

                $.post("fetch_vehicle_services.php", { brand: brand, model: model, year: year }, function (response) {
                    let serviceData = JSON.parse(response);
                    loadVehicleChart(serviceData);
                    // Reset the form after generating the chart
                    $("#vehicleSelectionForm")[0].reset();
                    clearChart();
                });
            });

            // Load vehicle chart data
            function loadVehicleChart(serviceData) {
                let ctx = document.getElementById('vehicleBarChart').getContext('2d');
                let labels = serviceData.map(service => service.service_type);
                let percentages = serviceData.map(service => {
                    let nextServiceDate = new Date(service.next_service_date);
                    let today = new Date();
                    let totalDays = (nextServiceDate - today) / (1000 * 60 * 60 * 24);
                    
                    // Calculate percentage based on a maximum threshold of 30 days
                    let percentage = Math.max(0, Math.min(100, (totalDays / 30) * 100)); // Scale to 0-100%
                    return percentage;
                });

                // Determine colors based on proximity to service date
                let backgroundColors = percentages.map((percentage, index) => {
                    let daysUntilService = (new Date(serviceData[index].next_service_date) - new Date()) / (1000 * 60 * 60 * 24);
                    return daysUntilService <= 10 ? '#FF6384' : '#4BC0C0'; // Red for <=10 days, Green for >10 days
                });

                let vehicleHealthData = {
                    labels: labels,
                    datasets: [{
                        label: 'Service Proximity (%)',
                        data: percentages,
                        backgroundColor: backgroundColors // Use the dynamic colors
                    }]
                };

                // Clear the previous chart if it exists
                if (window.vehicleChart) {
                    window.vehicleChart.destroy();
                }

                // Create a new chart
                window.vehicleChart = new Chart(ctx, {
                    type: 'bar',
                    data: vehicleHealthData,
                    options: {
                        scales: {
                            x: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    color: 'white' // Set x-axis text color to white
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.2)' // Light white grid for better visibility
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: 'white' // Set y-axis text color to white
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.2)' // Light white grid for better visibility
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    color: 'white' // Set legend text color to white
                                }
                            }
                        }
                    }
                });
            }

            // Fetch brands when the page loads
            $(document).ready(function () {
                $.post("fetch_brand.php", function (response) {
                    let brands = JSON.parse(response);
                    $("#brand").empty().append('<option value="">Select Brand</option>');
                    brands.forEach(function (brand) {
                        $("#brand").append('<option value="' + brand + '">' + brand + '</option>');
                    });
                });
            });

            // Fetch models based on selected brand
            $("#brand").change(function () {
                let brand = $(this).val();
                $.post("fetch_models.php", { brand: brand }, function (response) {
                    let models = JSON.parse(response);
                    $("#model").empty().append('<option value="">Select Model</option>');
                    models.forEach(function (model) {
                        $("#model").append('<option value="' + model + '">' + model + '</option>');
                    });
                });
            });

            // Fetch years based on selected model
            $("#model").change(function () {
                let model = $(this).val();
                $.post("fetch_years.php", { model: model }, function (response) {
                    let years = JSON.parse(response);
                    $("#year").empty().append('<option value="">Select Year</option>');
                    years.forEach(function (year) {
                        $("#year").append('<option value="' + year + '">' + year + '</option>');
                    });
                });
            });

            // Mark service as done
            $(".mark-done").click(function () {
                let serviceId = $(this).data("id");
                $.post("service_done.php", { id: serviceId }, function (response) {
                    if (response === "Success") {
                        location.reload();
                    } else {
                        alert("Error updating service!");
                    }
                });
            });

            // Delete service
            $(".delete-service").click(function () {
                let serviceId = $(this).data("id");
                if (confirm("Are you sure you want to delete this service?")) {
                    $.post("delete_service.php", { id: serviceId }, function (response) {
                        if (response === "Success") {
                            location.reload();
                        } else {
                            alert("Error deleting service!");
                        }
                    });
                }
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