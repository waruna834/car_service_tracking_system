<!--This PHP script retrieves a password reset token from the URL, 
verifies its validity against a database to ensure it hasn't expired, 
and then presents a form for the user to enter and confirm a new password, 
which will be processed by a separate script upon submission.-->

<?php

$token = $_GET["token"] ?? die("Token is required.");

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

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Reset Password</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
    <div class="container my-5">
        <h1>Car Service Tracking System - Reset Password</h1>
        <form class="mx-auto mt-4" style="max-width: 400px;" method="post" action="process_reset_password.php">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <label for="password">New password</label>
            <input type="password" id="password" name="password" required>
            <label for="password_confirmation">Repeat password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
    </body>
</html>
