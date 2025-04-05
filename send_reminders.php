<!--This PHP script connects to a MySQL database to retrieve users with car service appointments 
or service bookings scheduled for the next day, generates reminder emails with relevant details for each user, 
and sends these emails using PHPMailer, while handling potential errors in the process.-->

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/vendor/autoload.php"; // Ensure PHPMailer is autoloaded

// Connect to database
$mysqli = require __DIR__ . "/database.php";

// Get tomorrow's date
$tomorrow = date('Y-m-d', strtotime('+1 day'));

// Function to send email
function sendEmail($email, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'weemandra8@gmail.com'; // Your Gmail
        $mail->Password   = 'keys ueva hxnu ikdk'; // Your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender and recipient
        $mail->setFrom('weemandra8@gmail.com', 'Car Service Tracking System');
        $mail->addAddress($email);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        echo "Services & Bookings reminder Emails sent to: $email\n";
    } catch (Exception $e) {
        error_log("Email failed for $email: {$mail->ErrorInfo}");
    }
}

// Query users whose next service date is tomorrow
$query = "SELECT user_details.email, service_records.brand, service_records.model, 
                 service_records.year, service_records.service_type, 
                 service_records.mileage, service_records.next_service_date
          FROM service_records 
          JOIN user_details ON service_records.user_id = user_details.id
          WHERE DATE(next_service_date) = '$tomorrow'";

$result = $mysqli->query($query);

// Check if the query was successful
if (!$result) {
    // Output the error message
    die("Query failed: " . $mysqli->error);
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $email = $row['email'];
        $brand = $row['brand'];
        $model = $row['model'];
        $year = $row['year'];
        $serviceType = $row['service_type'];
        $mileage = $row['mileage'];
        $nextServiceDate = $row['next_service_date'];

        // Email content for service reminder
        $subject = "Car Service Reminder - $brand $model";
        $body = "
            <h3>Dear User,</h3>
            <p>This is a reminder that your car service is due tomorrow.</p>
            <ul>
                <li><strong>Brand:</strong> $brand</li>
                <li><strong>Model:</strong> $model</li>
                <li><strong>Year:</strong> $year</li>
                <li><strong>Service Type:</strong> $serviceType</li>
                <li><strong>Current Mileage:</strong> $mileage km</li>
                <li><strong>Next Service Date:</strong> $nextServiceDate</li>
            </ul>
            <p>Please schedule your service appointment in time.</p>
            <p>Best Regards,<br>Car Service Tracking System</p>
        ";

        sendEmail($email, $subject, $body);
    }
} else {
    echo "No upcoming service reminders for tomorrow.\n";
}

// Query service bookings for tomorrow
$bookingQuery = "SELECT user_details.email, service_bookings.service_type, 
                        service_bookings.service_date, service_bookings.service_center, 
                        service_bookings.location
                 FROM service_bookings 
                 JOIN user_details ON service_bookings.user_id = user_details.id
                 WHERE DATE(service_date) = '$tomorrow'";

$bookingResult = $mysqli->query($bookingQuery);

// Check if the booking query was successful
if (!$bookingResult) {
    // Output the error message
    die("Booking query failed: " . $mysqli->error);
}

if ($bookingResult ->num_rows > 0) {
    while ($row = $bookingResult->fetch_assoc()) {
        $email = $row['email'];
        $serviceType = $row['service_type'];
        $serviceDate = $row['service_date'];
        $serviceCenter = $row['service_center'];
        $location = $row['location'];

        // Email content for service booking reminder
        $subject = "Service Booking Reminder - $serviceType";
        $body = "
            <h3>Dear User,</h3>
            <p>This is a reminder for your service booking scheduled for tomorrow.</p>
            <ul>
                <li><strong>Service Type:</strong> $serviceType</li>
                <li><strong>Service Date:</strong> $serviceDate</li>
                <li><strong>Service Center:</strong> $serviceCenter</li>
                <li><strong>Location:</strong> $location</li>
            </ul>
            <p>Please ensure to arrive on time for your appointment.</p>
            <p>Best Regards,<br>Car Service Tracking System</p>
        ";

        sendEmail($email, $subject, $body);
    }
} else {
    echo "No service booking reminders for tomorrow.\n";
}

// Close the database connection
$mysqli->close();
?>