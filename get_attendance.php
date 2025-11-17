<?php
header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

// DB connection
$conn = new mysqli("localhost","root","","payroll_system");
if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

$today = date("Y-m-d");

// Fetch all attendance today with employee info
$sql = "SELECT a.attendance_id, a.employee_id, a.time_in, a.time_out, 
               CONCAT(e.first_name,' ',e.last_name) AS employee_name
        FROM attendance a
        JOIN employees e ON a.employee_id = e.employee_id
        WHERE a.date = ?
        ORDER BY a.time_in ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

$attendances = [];
while($row = $result->fetch_assoc()){
    $attendances[] = $row;
}

echo json_encode($attendances);
