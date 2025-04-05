<!--This PHP script creates an admin panel for managing contact messages in a car service tracking system, 
allowing logged-in administrators to view, delete messages from a MySQL database, 
and send reminders via an AJAX request, all while providing a responsive user interface with Bootstrap styling 
and JavaScript for dynamic interactions.-->

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

// Handle message deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_message"])) {
    $messageId = $_POST["message_id"];
    $deleteQuery = "DELETE FROM messages WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $messageId);
    $stmt->execute();
    $stmt->close();
}

// Fetch contact messages
$contactQuery = "SELECT * FROM messages";
$contactResult = $conn->query($contactQuery);

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
            <h2>Contact Messages</h2>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>   
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $contactResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row["name"]); ?></td>
                            <td><?php echo htmlspecialchars($row["email"]); ?></td>
                            <td><?php echo htmlspecialchars($row["message"]); ?></td>
                            <td>
                                <form method="POST" onsubmit="refreshPage();">
                                    <input type="hidden" name="message_id" value="<?php echo $row["id"]; ?>">
                                    <button type="submit" name="delete_message" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div> 

        <!-- Send reminders manually -->
        <div class="text-center mt-4">
        <form onsubmit="return showStatus();">
            <button type="submit" class="btn btn-primary">Send Reminders</button>
        </form>
            <div id="status" class="mt-2"></div>
        </div>

        <script>
            function showStatus() {
                document.getElementById('status').innerText = "Sending reminders... Please wait.";
                
                // Create an AJAX request
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "send_reminders.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // Display the response in the status div
                        document.getElementById('status').innerText = xhr.responseText;
                    }
                };
                
                xhr.send();
                
                // Prevent the form from submitting normally
                return false;
            }
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
