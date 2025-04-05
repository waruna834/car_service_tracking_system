<!--This PHP script logs out a user by unsetting all session variables, 
destroying the session, clearing associated cookies for user ID and email, 
and then redirecting the user to the login page.-->

<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Clear cookies
setcookie("user_id", "", time() - 3600, "/"); // Expire the cookie
setcookie("user_email", "", time() - 3600, "/"); // Expire the cookie

// Redirect to the login page
header("Location: login.php");
exit();
?>