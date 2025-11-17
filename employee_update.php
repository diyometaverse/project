<?php
include 'db.php';

$id = $_POST['employee_id'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$department = $_POST['department'];
$position = $_POST['position'];
$daily_rate = $_POST['daily_rate'];
$status = $_POST['status'];
$rfid_tag = $_POST['rfid_tag'];
$face_id = $_POST['face_id'];

$sql = "UPDATE employees SET 
    first_name='$first_name',
    last_name='$last_name',
    email='$email',
    phone='$phone',
    department='$department',
    position='$position',
    daily_rate='$daily_rate',
    status='$status',
    rfid_tag='$rfid_tag',
    face_id='$face_id'
    WHERE employee_id=$id";

if ($conn->query($sql)) {
    echo json_encode(['success' => true, 'message' => 'Employee updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update employee']);
}
?>
