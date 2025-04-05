<!--This PHP script creates a vehicle information form for a car service tracking system, 
allowing logged-in users to select their car's brand and model, input details such as year, mileage, and service type, 
and predict the next service date based on the provided information, while dynamically loading models based on the selected brand 
and validating the form before submission.-->

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carservicetrackingsystem";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .container {
            max-width: 600px; /* Adjust width as needed */
            background: rgba(255, 255, 255, 0.1); /* Semi-transparent white background */
            backdrop-filter: blur(10px); /* Apply blur effect */
            -webkit-backdrop-filter: blur(10px); /* For Safari support */
            padding: 20px;
            border-radius: 15px; /* Rounded corners */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Subtle shadow */
            color: white; /* Text color */
        }
        .is-invalid {
            border-color: red;
        }
    </style>
</head>
<body>
    <div class="background"></div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php"><strong>Car Service Tracking System</strong></a>
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

    <div class="container mt-5">
        <h2 class="text-center">Car Service Prediction Form</h2>
        <form id="vehicleForm">
            <input type="hidden" name="user_id" value="<?= $user_id; ?>">

            <!-- Brand Dropdown -->
            <div class="mb-3">
                <label for="brand" class="form-label">Brand</label>
                <select class="form-control" id="brand" name="brand" required>
                    <option value="">Select a Brand</option>
                    <?php
                    $carQuery = "SELECT DISTINCT brand FROM car_types ORDER BY brand";
                    $carResult = $conn->query($carQuery);
                    while ($row = $carResult->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['brand']) . "'>" . htmlspecialchars($row['brand']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Model Dropdown -->
            <div class="mb-3">
                <label for="model" class="form-label">Model</label>
                <select class="form-control" id="model" name="model" required>
                    <option value="">Select a Model</option>
                </select>
            </div>

            <!-- Year Input -->
            <div class="mb-3">
                <label for="year" class="form-label">Year</label>
                <input type="number" name="year" class="form-control" required>
            </div>

            <!-- Current Mileage -->
            <div class="mb-3">
                <label>Current Mileage</label>
                <input type="number" name="mileage" class="form-control" required>
            </div>

            <!-- Weekly Mileage -->
            <div class="mb-3">
                <label>Mileage Driven Per Week</label>
                <select name="mileage_per_week" class="form-control" required>
                    <option value="50">0-50 miles</option>
                    <option value="100">51-100 miles</option>
                    <option value="150">101-150 miles</option>
                    <option value="200">151-200 miles</option>
                    <option value="250">201-250 miles</option>
                    <option value="300">251-300 miles</option>
                    <option value="350">300+ miles</option>
                </select>
            </div>

            <!-- Driving Conditions -->
            <div class="mb-3">
                <label>Driving Conditions</label>
                <select name="driving_conditions" class="form-control" required>
                    <option value="On Road">On Road</option>
                    <option value="Off Road">Off Road</option>
                </select>
            </div>

            <!-- Service Type -->
            <div class="mb-3">
                <label>Service Type</label>
                <select name="service_type" class="form-control" required>
                    <?php
                    $services = $conn->query("SELECT service_type FROM service_intervals");
                    while ($row = $services->fetch_assoc()) {
                        echo "<option value='{$row['service_type']}'>{$row['service_type']}</option>";
                    }
                    ?>
                </select>
            </div>

        <!-- Last Service Date -->
        <div class="mb-3">
            <label for="last_service_date">Last Service Date</label>
            <input type="date" id="last_service_date" name="last_service_date" class="form-control" required>
        </div>

        <script>
            // Set the min attribute to today's date
            document.addEventListener("DOMContentLoaded", function() {
                const today = new Date();
                const dd = String(today.getDate()).padStart(2, '0');
                const mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
                const yyyy = today.getFullYear();

                // Format the date as YYYY-MM-DD
                const formattedDate = `${yyyy}-${mm}-${dd}`;
                document.getElementById("last_service_date").setAttribute("min", formattedDate);
            });
        </script>

            <!-- Predict Button -->
            <button type="button" id="predictButton" class="btn btn-primary">Predict Next Service Date</button>
        </form>

        <!-- Results -->
        <div id="servicePredictionResults" class="mt-4"></div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Load Models Based on Selected Brand
            $("#brand").change(function () {
                let brand = $(this).val();
                if (brand) {
                    $.ajax({
                        url: "get_models.php",
                        type: "POST",
                        data: { brand: brand },
                        success: function (response) {
                            $("#model").html(response);
                        }
                    });
                } else {
                    $("#model").html('<option value="">Select a Model</option>');
                }
            });

            // Form Validation
            function validateForm() {
                let isValid = true;
                $('#vehicleForm input, #vehicleForm select').each(function () {
                    if ($(this).val() === "") {
                        $(this).addClass("is-invalid");
                        isValid = false;
                    } else {
                        $(this).removeClass("is-invalid");
                    }
                });
                return isValid;
            }

            // Predict Next Service
            $("#predictButton").click(function () {
                if (validateForm()) {
                    var formData = $("#vehicleForm").serialize();

                    $.ajax({
                        type: "POST",
                        url: "calculate_service.php",
                        data: formData,
                        dataType: "json",
                        success: function (response) {
                            if (response.error) {
                                $("#servicePredictionResults").html(`
                                    <div class="alert alert-danger">${response.error}</div>
                                `);
                            } else {
                                $("#servicePredictionResults").html(`
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">${response.service_type}</h5>
                                            <p><strong>Next Service Mileage:</strong> ${response.next_mileage} km</p>
                                            <p><strong>Next Service Date:</strong> ${response.next_service_date}</p>
                                        </div>
                                    </div>
                                `);
                            }
                        },
                        error: function () {
                            $("#servicePredictionResults").html(`
                                <div class="alert alert-danger">Error processing request. Please try again.</div>
                            `);
                        }
                    });
                } else {
                    $("#servicePredictionResults").html(''); // Clear previous results if validation fails
                    alert("Please fill out all required fields before predicting.");
                }
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
