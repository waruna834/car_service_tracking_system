<!--This PHP script logs out an admin by clearing all session variables, 
destroying the session, optionally expiring any admin-specific cookies, 
and then redirecting the user to the admin login page.-->

<?php
session_start();

// Unset all admin session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Clear admin-specific cookies if any (optional)
setcookie("admin_email", "", time() - 3600, "/"); // Expire the cookie

// Redirect to the admin login page
header("Location: admin_login.php");
exit();
?>
