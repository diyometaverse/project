<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('Asia/Manila');

header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$pass = "";
$db   = "payroll_system";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'DB Connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$rfid_tag = $_POST['rfid_tag'] ?? null;
$face_verified = $_POST['face_verified'] ?? 0; // 1 after face match
$employee_id = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
$today = date("Y-m-d");

if ($rfid_tag) {
    $rfid_tag = preg_replace("/\D/", "", trim($rfid_tag));

    $find = $conn->prepare("SELECT employee_id FROM employees WHERE rfid_tag = ?");
    $find->bind_param("s", $rfid_tag);
    $find->execute();
    $res = $find->get_result();

    if ($res->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'RFID not recognized']);
        exit;
    }

    $employee_id = $res->fetch_assoc()['employee_id'];
}

if (!$employee_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid employee']);
    exit;
}

// Must pass face verification first
if (!$face_verified) {
    echo json_encode(['status' => 'pending_face', 'employee_id' => $employee_id]);
    exit;
}

$emp = $conn->prepare("SELECT first_name, last_name FROM employees WHERE employee_id = ?");
$emp->bind_param("i", $employee_id);
$emp->execute();
$info = $emp->get_result()->fetch_assoc();
$name = $info['first_name']." ".$info['last_name'];

$att = $conn->prepare("SELECT * FROM attendance WHERE employee_id = ? AND date = ?");
$att->bind_param("is", $employee_id, $today);
$att->execute();
$res = $att->get_result();

if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    if ($row['time_out'] == null) {
        $t = date("H:i:s");
        $update = $conn->prepare("UPDATE attendance SET time_out=? WHERE attendance_id=?");
        $update->bind_param("si", $t, $row['attendance_id']);
        $update->execute();

        echo json_encode([
            'status' => 'success',
            'type' => 'out',
            'employee_name' => $name,
            'time' => date("g:i A"),
            'message' => 'Time OUT recorded'
        ]);
        exit;
    }

    echo json_encode(['status' => 'warning', 'message' => 'Already logged OUT']);
    exit;
}

$t = date("H:i:s");
$insert = $conn->prepare("INSERT INTO attendance (employee_id, date, time_in) VALUES (?, ?, ?)");
$insert->bind_param("iss", $employee_id, $today, $t);
$insert->execute();

echo json_encode([
    'status' => 'success',
    'type' => 'in',
    'employee_name' => $name,
    'time' => date("g:i A"),
    'message' => 'Time IN recorded'
]);
exit;
