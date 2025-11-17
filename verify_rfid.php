<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$pass = "";
$db   = "payroll_system";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$rfid_tag = $_POST['rfid_tag'] ?? '';
$rfid_tag = preg_replace("/\D/", "", trim($rfid_tag));

if (!$rfid_tag) {
    echo json_encode(['success' => false, 'message' => 'Invalid RFID']);
    exit;
}

$stmt = $conn->prepare("SELECT employee_id, first_name, last_name, face_image_path 
                        FROM employees WHERE rfid_tag = ?");
$stmt->bind_param("s", $rfid_tag);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'RFID not registered']);
    exit;
}

$emp = $res->fetch_assoc();

echo json_encode([
    'success' => true,
    'employee' => [
        'employee_id' => $emp['employee_id'],
        'first_name' => $emp['first_name'],
        'last_name' => $emp['last_name'],
        'face_image_path' => $emp['face_image_path']
    ]
]);
$stmt->close();
$conn->close();
?>