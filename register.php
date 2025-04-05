<!--This HTML and PHP code creates a user registration page for a Car Service Tracking System, 
featuring a form for users to input their personal details and password, 
which upon submission validates the input, checks for matching passwords, 
hashes the password, and stores the user information in a MySQL database, 
while providing feedback on the registration status and redirecting to the login page upon successful registration.-->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
        </style>
    </head>
    <body>
        <!--navigation bar-->
        <div class="background"></div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="home.php">Car Service Tracking</a>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="home.php">Home</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!--registration form-->
        <div class="container my-5">
            <h2 class="text-center">Register</h2>
            <form id="registerForm" method="post" action="register.php" class="mx-auto mt-4" style="max-width: 500px;">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="fname" id="name" placeholder="Enter your name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" name="pnumber" id="phone" placeholder="Enter your phone number (Enter a 10-digit phone number)" pattern="[0-9]{10}" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password (Password must be at least 8 characters long)" minlength="8" required>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder="Confirm your password" minlength="8" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
                <div class="text-center mt-3">
                    <p class="mb-1">
                        Already have an account? <a href="login.php">Login</a>
                    </p>
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

<!--registration logic and validation-->
<?php
    $host = "localhost";
    $dbname = "carservicetrackingsystem";
    $username = "root";
    $password = "";

    // Create a new mysqli connection
    $mysqli = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if ($mysqli->connect_errno) {
        die("Connection error: " . $mysqli->connect_error);
    }

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $name = $_POST['fname'];
        $email = $_POST['email'];
        $phone = $_POST['pnumber'];
        $password = $_POST['password_hash'];
        $confirmPassword = $_POST['confirm_password']; // Assuming you have a confirm password field

        // Check if passwords match
        if ($password !== $confirmPassword) {
            echo "<p class='text-danger text-center mt-3'>Passwords do not match!</p>";
        } else {
            // Hash the password before storing it
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Prepare and bind SQL statement
            $stmt = $mysqli->prepare("INSERT INTO user_details (fname, email, pnumber, password_hash) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);

            // Execute the statement
            if ($stmt->execute() === TRUE) {
                echo "<script>
                        alert('Registration successful! Redirecting to login page.');
                        window.location.href = 'login.php';
                        window.history.pushState(null, null, window.location.href);
                        window.onpopstate = function () {
                            window.history.go(1);
                        };
                    </script>";
            } else {
                echo "<p class='text-danger text-center mt-3'>Unsuccessful registration!</p>";
            }

            // Close the statement
            $stmt->close();
        }
    }

    // Close the database connection
    $mysqli->close();
?>