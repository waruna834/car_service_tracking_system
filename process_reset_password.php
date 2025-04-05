<!--This PHP script handles a password reset process by verifying a provided token, 
ensuring the new password meets criteria, hashing the new password, 
updating the user's password in the database while clearing the reset token, 
and then displaying a success message along with a button to navigate back to the login page.-->

<?php

$token = $_POST["token"] ?? die("Token is required.");
$password = $_POST["password"];
$password_confirmation = $_POST["password_confirmation"];

if ($password !== $password_confirmation) {
    die("Passwords do not match.");
}

if (strlen($password) < 8) {
    die("Password must be at least 8 characters.");
}

// Hash the new password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Hash the token
$token_hash = hash("sha256", $token);

// Connect to database
$mysqli = require __DIR__ . "/database.php";

// Fetch user with the token
$sql = "SELECT * FROM user_details
        WHERE reset_token = ? AND reset_token_expire_time > NOW()";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Invalid or expired token.");
}

// Update the password and clear the token
$sql = "UPDATE user_details
        SET password_hash = ?, reset_token = NULL, reset_token_expire_time = NULL
        WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("si", $password_hash, $user["id"]);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    die("Failed to update password.");
}

echo "Your password has been successfully reset. You can now log in.";

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <!-- Add a button to navigate back to the login page -->
        <div>
            <form action="login.php" method="get">
                <button type="submit" class="btn btn-primary">Go to Login Page</button>
            </form>
        </div>
    </body>
</html>