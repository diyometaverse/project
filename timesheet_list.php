<?php
include 'db.php'; // your DB connection

$sql = "SELECT t.*, e.first_name, e.last_name 
        FROM timesheets t
        JOIN employees e ON t.employee_id = e.employee_id
        ORDER BY t.work_date DESC";
$result = $conn->query($sql);

$timesheets = [];
while($row = $result->fetch_assoc()){
    $timesheets[] = $row;
}

echo json_encode($timesheets);
