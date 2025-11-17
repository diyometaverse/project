<?php
include 'db.php';

if(isset($_POST['timesheet_id'])){
    $timesheet_id = $_POST['timesheet_id'];

    $sql = "UPDATE timesheets SET status='Approved' WHERE timesheet_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $timesheet_id);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Timesheet approved!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
