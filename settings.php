<!--This PHP script provides a user settings page for a car service tracking system, 
allowing logged-in users to update their name, phone number, and email (with current password verification), 
as well as change their password, while displaying success or error messages based on the outcomes of their actions.-->

<?php
session_start();

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carservicetrackingsystem";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$fname = $email = $pnumber = '';
$current_password = $new_password = $confirm_password = '';
$error_name = $success_name = '';
$error_email = $success_email = '';
$error_password = $success_password = '';

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT fname, email, pnumber FROM user_details WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($fname, $email, $pnumber);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update name and phone number
    if (isset($_POST['update_details'])) {
        $fname = trim($_POST['fname']);
        $pnumber = trim($_POST['pnumber']);

        $stmt = $conn->prepare("UPDATE user_details SET fname = ?, pnumber = ? WHERE id = ?");
        $stmt->bind_param("ssi", $fname, $pnumber, $user_id);
        if ($stmt->execute()) {
            $success_name = "Name and phone number updated successfully.";
        } else {
            $error_name = "Error updating name and phone number.";
        }
        $stmt->close();
    }

    // Update email
    if (isset($_POST['update_email'])) {
        $email = trim($_POST['email']);
        $current_password = trim($_POST['current_password']);

        $stmt = $conn->prepare("SELECT password_hash FROM user_details WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($password_hash);
        $stmt->fetch();
        $stmt->close();

        if (password_verify($current_password, $password_hash)) {
            $stmt = $conn->prepare("UPDATE user_details SET email = ? WHERE id = ?");
            $stmt->bind_param("si", $email, $user_id);
            if ($stmt->execute()) {
                $success_email = "Email updated successfully.";
            } else {
                $error_email = "Error updating email.";
            }
            $stmt->close();
        } else {
            $error_email = "Current password is incorrect.";
        }
    }

    // Change password
    if (isset($_POST['change_password'])) {
        $current_password = trim($_POST['current_password']);
        $new_password = trim($_POST['new_password']);
        $confirm_password = trim($_POST['confirm_password']);

        $stmt = $conn->prepare("SELECT password_hash FROM user_details WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($password_hash);
        $stmt->fetch();
        $stmt->close();

        if (password_verify($current_password, $password_hash)) {
            if ($new_password === $confirm_password && strlen($new_password) >= 8) {
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE user_details SET password_hash = ? WHERE id = ?");
                $stmt->bind_param("si", $new_password_hash, $user_id);
                if ($stmt->execute()) {
                    $success_password = "Password changed successfully.";
                } else {
                    $error_password = "Error changing password.";
                }
                $stmt->close();
            } else {
                $error_password = "New passwords do not match or are too short.";
            }
        } else {
            $error_password = "Current password is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: url('css/3.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            margin-top: 50px;
        }
        h1, h2 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .btn {
            width: 100%;
        }
    </style>
</head>
<body>
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

<div class="container">    
    <h1>Settings</h1>
    <br>

    <!-- Update Name and Phone Number -->
    <h2>Update Name and Phone Number</h2>
    <form method="POST">
        <div class="form-group">
            <label for="fname">Full Name:</label>
            <input type="text" name="fname" class="form-control" value="<?php echo htmlspecialchars($fname); ?>" required>
        </div>
        <div class="form-group">
            <label for="pnumber">Phone Number:</label>
            <input type="text" name="pnumber" class="form-control" value="<?php echo htmlspecialchars($pnumber); ?>" required>
        </div>
        <button type="submit" name="update_details" class="btn btn-primary">Update Name and Phone Number</button>
        <?php if ($error_name): ?><div class="alert alert-danger mt-2"><?php echo $error_name; ?></div><?php endif; ?>
        <?php if ($success_name): ?><div class="alert alert-success mt-2"><?php echo $success_name; ?></div><?php endif; ?>
    </form>
    <br>

    <!-- Update Email -->
    <h2>Update Email</h2>
    <form method="POST">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="form-group">
            <label for="current_password">Current Password:</label>
            <input type="password" name="current_password" class="form-control" required>
        </div>
        <button type="submit" name="update_email" class="btn btn-primary">Update Email</button>
        <?php if ($error_email): ?><div class="alert alert-danger mt-2"><?php echo $error_email; ?></div><?php endif; ?>
        <?php if ($success_email): ?><div class="alert alert-success mt-2"><?php echo $success_email; ?></div><?php endif; ?>
    </form>
    <br>

    <!-- Change Password -->
    <h2>Change Password</h2>
    <form method="POST">
        <div class="form-group">
            <label for="current_password">Current Password:</label>
            <input type="password" name="current_password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
        <?php if ($error_password): ?><div class="alert alert-danger mt-2"><?php echo $error_password; ?></div><?php endif; ?>
        <?php if ($success_password): ?><div class="alert alert-success mt-2"><?php echo $success_password; ?></div><?php endif; ?>
    </form>
</div>

<!-- Footer -->
<footer class="text-center py-3">
    &copy; 2024 Car Service Tracking System. All Rights Reserved.
    Contact Us: 0112785623 / info@carServiceTracking.lk
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
