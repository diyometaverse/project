<?php
include 'db.php';

$periods = [];
$sql = "SELECT period_id, start_date, end_date FROM payroll_periods ORDER BY end_date DESC";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    $periods[] = $row;
}

header('Content-Type: application/json');
echo json_encode($periods);
?>
