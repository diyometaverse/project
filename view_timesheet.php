<?php
include 'db.php';

$employee_id = $_GET['employee_id'];
$work_date = $_GET['work_date'];

$sql = "SELECT * FROM timesheets WHERE employee_id=? AND work_date=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $employee_id, $work_date);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if($row){
    echo "<p>Employee: $employee_id</p>";
    echo "<p>Date: {$row['work_date']}</p>";
    echo "<p>Hours Worked: {$row['hours_worked']}</p>";
    echo "<p>Overtime: {$row['overtime_hours']}</p>";
    echo "<p>Status: {$row['status']}</p>";
} else {
    echo "<p>No details found.</p>";
}
