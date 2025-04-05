<!--This HTML document creates a car service booking page featuring a responsive form that allows users to enter the service type, 
select a date (with a minimum date set to today), and view the selected service center and location, 
all styled with Bootstrap and custom CSS for a visually appealing interface.-->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Car Service Booking</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
        <style>
            .container {
                max-width: 500px; /* Adjust width as needed */
                background: rgba(255, 255, 255, 0.1); /* Semi-transparent white background */
                backdrop-filter: blur(10px); /* Apply blur effect */
                -webkit-backdrop-filter: blur(10px); /* For Safari support */
                padding: 20px;
                border-radius: 15px; /* Rounded corners */
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Subtle shadow */
                color: white; /* Text color */
            }

            /* Style button */
            .btn-primary {
                background: rgba(0, 123, 255, 0.7);
                border: none;
                transition: 0.3s ease-in-out;
            }

            .btn-primary:hover {
                background: rgba(0, 123, 255, 1);
            }
        </style>
    </head>
    <body>
        <div class="background"></div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href=""><strong>Car Service Tracking System</strong></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="map.php">Service Map</a></li>
                    </ul>
                </div>
            </div>
        </nav>  
        <div class="container my-5">
            <h2 class="text-center mb-4">Car Service Booking</h2>
            <form method="POST" action="process_booking.php">
                <div class="mb-3">
                    <label for="serviceType" class="form-label">Enter Service Type</label>
                    <input type="text" class="form-control" id="serviceType" name="serviceType">
                </div>

                <div class="mb-3">
                    <label for="serviceDate" class="form-label">Select Date</label>
                    <input type="date" class="form-control" id="serviceDate" name="serviceDate" required>
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
                        document.getElementById("serviceDate").setAttribute("min", formattedDate);
                    });
                </script>

                <div class="mb-3">
                    <label for="serviceCenter" class="form-label">Selected Service Center</label>
                    <input type="text" class="form-control" id="serviceCenter" name="serviceCenter" value="<?php echo isset($_GET['center']) ? htmlspecialchars($_GET['center']) : ''; ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>" readonly>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary w-100">Confirm Booking</button>
                </div>
            </form>
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
