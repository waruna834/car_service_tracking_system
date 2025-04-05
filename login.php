<!--This HTML and PHP code creates a login page for a Car Service Tracking System, 
featuring a form for users to enter their email and password, 
which upon submission validates the credentials against a MySQL database, starts a user session, 
sets cookies for automatic login, and provides feedback for login errors, all while styled with Bootstrap and custom CSS.-->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
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
                <a class="navbar-brand" href="home.php">Car Service Tracking System</a>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="home.php">Home</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!--login form-->
        <div class="container my-5">
            <h2 class="text-center">Login</h2>
            <form id="loginForm" method="post" action="" class="mx-auto mt-4" style="max-width: 500px;">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
                <div class="text-center mt-3">
                    <p class="mb-1">
                        Don't have an account? <a href="register.php">Register</a>
                    </p>
                    <p class="mb-0">
                        <a href="forgot_password.php" class="text-decoration-none">Forgot Password?</a>
                    </p>
                </div>
            </form>
            <!--login form validations and logics-->
            <?php
                session_start();
                
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "carservicetrackingsystem";

                    // Create connection
                    $conn = new mysqli($servername, $username, $password, $dbname);

                    // Check connection
                    if ($conn->connect_error) {
                        die("<p class='text-danger text-center mt-3'>Database connection failed: " . $conn->connect_error . "</p>");
                    }

                    // Sanitize and validate input
                    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                    $password = $_POST['password'];

                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        echo "<p class='text-danger text-center mt-3'>Invalid email format</p>";
                    } else {

                        // Prepared statement for user login
                        $stmt = $conn->prepare("SELECT * FROM user_details WHERE email = ?");
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            $user = $result->fetch_assoc();
                            // Verify password from database
                            if (password_verify($password, $user['password_hash'])) {
                                // Start session for user
                                $_SESSION['user_id'] = $user['id'];
                                $_SESSION['user_email'] = $user['email'];
                                // Set a cookie for 1 hour
                                setcookie("user_id", $user['id'], time() + 3600, "/"); // 1 hour expiration
                                setcookie("user_email", $user['email'], time() + 3600, "/");
                                header("Location: dashboard.php");
                                exit();
                            } else {
                                echo "<p class='text-danger text-center mt-3'>Incorrect password</p>";
                            }
                        } else {
                            echo "<p class='text-danger text-center mt-3'>No account found with that email</p>";
                        }
                        $stmt->close();
                    }

                    $conn->close();
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
