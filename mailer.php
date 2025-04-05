<!--This PHP script defines a function createMailer() that configures and 
returns a PHPMailer instance for sending emails via Gmail's SMTP server, 
with options for debugging and secure TLS encryption, 
while handling exceptions to display any errors encountered during the setup process.-->

<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/vendor/autoload.php";

function createMailer(): PHPMailer
{
    $mail = new PHPMailer(true);

    try {
        // Enable SMTP debugging (use SMTP::DEBUG_OFF in production)
        $mail->SMTPDebug = SMTP::DEBUG_OFF; // Change to DEBUG_SERVER for detailed logs in development

        // Server settings
        $mail->Host = 'smtp.gmail.com';
        $mail->Username = 'weemandra8@gmail.com'; // Replace with your Gmail
        $mail->Password = 'keys ueva hxnu ikdk'; // Use App Password
        $mail->Port = 587; // TLS
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        

        // Email formatting
        $mail->isHTML(true);

        return $mail;
    } catch (Exception $e) {
        echo "Mailer Error: {$e->getMessage()}";
        exit; // Ensure the script stops on failure
    }
}

?>
