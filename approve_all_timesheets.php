<?php
include 'db.php';

$conn->query("UPDATE timesheets SET status='Approved' WHERE status='Pending'");
echo json_encode(['success' => true, 'message' => 'All timesheets approved.']);
?>
