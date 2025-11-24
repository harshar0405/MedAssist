<?php
$servername = "localhost";
$username   = "root";
$password   = "Harsha@2005";
$database   = "patient_management_db";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>