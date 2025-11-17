<?php
include 'db.php';

$id = $_GET['id'] ?? 0;
$result = $conn->query("SELECT * FROM employees WHERE employee_id = $id");

if ($result && $result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(['error' => 'Employee not found']);
}
?>
