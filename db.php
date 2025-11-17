<?php
// =======================
// Database Connection
// =======================
$host = "localhost";
$user = "root";     // change if you have a different user
$pass = "";         // add your password if any
$db   = "payroll_system"; // change to your database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>