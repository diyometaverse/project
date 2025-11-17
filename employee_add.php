<?php
include 'db.php'; // DB connection

header('Content-Type: application/json'); // always JSON

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect and sanitize input
    $first_name  = trim($_POST['first_name']);
    $last_name   = trim($_POST['last_name']);
    $email       = trim($_POST['email']);
    $phone       = trim($_POST['phone']);
    $position    = trim($_POST['position']);
    $department  = trim($_POST['department']);
    $daily_rate  = floatval($_POST['daily_rate']);
    $status      = $_POST['status'] ?? 'active';
    $date_hired  = $_POST['date_hired'];
    $rfid_tag    = !empty($_POST['rfid_tag']) ? trim($_POST['rfid_tag']) : null;
    $face_id     = !empty($_POST['face_id']) ? trim($_POST['face_id']) : null;

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($position) || empty($department)) {
        $response['message'] = "Missing required fields.";
        echo json_encode($response);
        exit;
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO employees 
        (first_name, last_name, email, phone, position, department, daily_rate, status, date_hired, rfid_tag, face_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "ssssssdssss",
        $first_name,
        $last_name,
        $email,
        $phone,
        $position,
        $department,
        $daily_rate,
        $status,
        $date_hired,
        $rfid_tag,
        $face_id
    );

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Employee added successfully!";
    } else {
        $response['message'] = "Database error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    echo json_encode($response);
    exit;
}
?>
