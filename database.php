<!--This PHP code establishes a connection to a MySQL database named "carservicetrackingsystem" 
using the MySQLi extension, handling any connection errors by terminating the script and displaying an error message.-->

<?php
$host = "localhost";
$dbname = "carservicetrackingsystem";
$username = "root";
$password = "";

$mysqli = new mysqli(hostname: $host,
                     username: $username,
                     password: $password,
                     database: $dbname);
                     
if ($mysqli->connect_errno) {
    die("Connection error: " . $mysqli->connect_error);
}

return $mysqli;
?>