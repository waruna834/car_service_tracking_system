<!--This HTML and PHP code creates a "Contact Us" page for a car service tracking system, 
allowing users to submit their name, email, and message through a form, 
which is then processed by the server to store the information in a MySQL database, 
providing feedback via JavaScript alerts for successful or failed submissions.-->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contact Us</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/style.css">
        <style>
            .container {
                max-width: 800px; /* Adjust width as needed */
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
        <div class="container my-5">
            <h2 class="text-center">Contact Us</h2>
            <form action="contacts.php" method="POST" class="mx-auto mt-4" style="max-width: 600px;">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="4" placeholder="Enter your message" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Submit</button>
            </form><br>
            <h6 class="text-center">If there any problem or issure, please inform us. Thank you!</h6>
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

<?php
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

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $message = trim($_POST["message"]);

    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        echo "<script>alert('All fields are required!'); window.location.href='contacts.php';</script>";
        exit();
    }

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);

    // Execute and check if successful
    if ($stmt->execute()) {
        echo "<script>alert('Message submitted successfully!'); window.location.href='contacts.php';</script>";
    } else {
        echo "<script>alert('Error submitting message!'); window.location.href='contacts.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>

