<!--This HTML document creates a responsive admin login page for a car service tracking system, 
featuring a form for email and password input, which, upon submission, 
validates the credentials against hardcoded values, starts a session for the logged-in admin, 
and redirects to an admin management page while providing feedback for invalid login attempts.-->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login</title>
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
        <div class="background"></div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="home.php">Car Service Tracking System - Admin</a>
            </div>
        </nav>
        <div class="container my-5">
            <h2 class="text-center">Admin Login</h2>
            <form id="adminLoginForm" method="post" action="" class="mx-auto mt-4" style="max-width: 500px;">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter admin email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter admin password" required>
                </div>
                <button type="submit" class="btn btn-danger w-100">Admin Log In</button>
            </form>
            <?php
                session_start();

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $adminEmail = "warunaAdmin@gmail.com";
                    $adminPassword = "waruna@187";

                    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                    $password = $_POST['password'];

                    if ($email === $adminEmail && $password === $adminPassword) {
                        $_SESSION['admin_logged_in'] = true;
                        $_SESSION['admin_email'] = $email;
                        header("Location: admin_serviceType.php");
                        exit();
                    } else {
                        echo "<p class='text-danger text-center mt-3'>Invalid admin credentials</p>";
                    }
                }
            ?>
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
