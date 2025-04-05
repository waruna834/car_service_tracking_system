<!--This PHP script generates a password reset token for a user based on their email address, 
updates the user's record in the database with the token and its expiry time, 
and sends an email containing a reset link using PHPMailer, while handling potential errors throughout the process.-->

<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/vendor/autoload.php"; // Ensure PHPMailer is autoloaded

// Get the email from the form
$email = $_POST["email"];

// Generate reset token
$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);

// Create a DateTime object for the current time in Sri Lanka timezone
$date = new DateTime("now", new DateTimeZone("Asia/Colombo"));

// Add 5 minutes to the current time
$date->add(new DateInterval('PT5M'));

// Format the expiry time to a string suitable for database storage
$expiry = $date->format("Y-m-d H:i:s");

// Now you can use $token_hash and $expiry in your database operations

// Connect to database
$mysqli = require __DIR__ . "/database.php";

// Check if email exists in the database
$sql = "SELECT * FROM user_details WHERE email = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("No account found with that email address.");
}

// Update the reset token and expiry in the database
$sql = "UPDATE user_details
        SET reset_token = ?, reset_token_expire_time = ?
        WHERE email = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute();

// Check if the update was successful
if ($stmt->affected_rows === 0) {
    die("Failed to generate reset token. Please try again.");
}

// Set up the email
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com"; // Update with your SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = "weemandra8@gmail.com"; // Update with your email
    $mail->Password = "keys ueva hxnu ikdk"; // Update with your password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom("noreply@example.com", "Car service tracking system");
    $mail->addAddress($email);

    $mail->Subject = "Password Reset Request";
    $mail->isHTML(true);
    $mail->Body = <<<EOT
        <p>Hello {$user['fname']},</p>
        <p>We received a request to reset your password. Click the link below to reset your password:</p>
        <p><a href="http://localhost/CarServiceTrackingSystem/reset_password.php?token=$token">Reset Password</a></p>
        <p>This link will expire in 5 minutes (UTC).</p>
        <p>If you did not request this, you can ignore this email.</p>
        <p>Thanks,</p>
        <p>Car service tracking system!</p>
    EOT;

    $mail->send();
    echo "Password reset email has been sent! Check your inbox.";

} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
