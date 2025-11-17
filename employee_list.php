<?php
include 'db.php';

// Fetch employees
$result = $conn->query("SELECT * FROM employees ORDER BY created_at DESC");

$employees = [];
while ($row = $result->fetch_assoc()) {
    $employees[] = $row;
}

// Output JSON ONLY (no HTML allowed here)
header('Content-Type: application/json');
echo json_encode($employees);
exit; // stop script execution
?>
