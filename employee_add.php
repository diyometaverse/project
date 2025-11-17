<?php
include 'db.php'; 
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

/* ============================================
   INPUT VALIDATION & EXTRACTION
============================================ */

function sanitize($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

$first_name  = sanitize($_POST['first_name'] ?? '');
$last_name   = sanitize($_POST['last_name'] ?? '');
$email       = sanitize($_POST['email'] ?? '');
$phone       = sanitize($_POST['phone'] ?? '');
$position    = sanitize($_POST['position'] ?? '');
$department  = sanitize($_POST['department'] ?? '');
$daily_rate  = floatval($_POST['daily_rate'] ?? 0);
$status      = sanitize($_POST['status'] ?? 'active');
$date_hired  = sanitize($_POST['date_hired'] ?? '');
$rfid_tag    = !empty($_POST['rfid_tag']) ? sanitize($_POST['rfid_tag']) : null;

$face_image_base64 = $_POST['face_image'] ?? null;

/* ============================================
   REQUIRED FIELD CHECK
============================================ */

if (!$first_name || !$last_name || !$position || !$department) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

/* ============================================
   STEP 1: INSERT BASE EMPLOYEE FIRST
   We will update face_image_path AFTER saving file
============================================ */

$stmt = $conn->prepare("INSERT INTO employees 
    (first_name, last_name, email, phone, position, department, daily_rate, status, date_hired, rfid_tag, face_image_path)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)");

$stmt->bind_param(
    "ssssssdsss",
    $first_name,
    $last_name,
    $email,
    $phone,
    $position,
    $department,
    $daily_rate,
    $status,
    $date_hired,
    $rfid_tag
);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Insert error: ' . $stmt->error]);
    exit;
}

$employee_id = $stmt->insert_id;
$stmt->close();


/* ============================================
   STEP 2: SAVE FACE IMAGE TO FILE SYSTEM
============================================ */

$face_filename = null;

if (!empty($face_image_base64)) {

    // Validate format: data:image/jpeg;base64,xxxx
    if (preg_match('/^data:image\/(jpeg|jpg|png);base64,/', $face_image_base64)) {

        $img_data = preg_replace('/^data:image\/\w+;base64,/', '', $face_image_base64);
        $decoded  = base64_decode($img_data, true);

        if ($decoded) {

            // Ensure directory exists
            $upload_dir = __DIR__ . "/uploads/faces/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Save as JPG regardless of source type
            $face_filename = "EMP" . str_pad($employee_id, 3, "0", STR_PAD_LEFT) . ".jpg";
            $file_path = $upload_dir . $face_filename;

            // Save image
            if (file_put_contents($file_path, $decoded) !== false) {

                // Update DB with image path
                $relative_path = "uploads/faces/" . $face_filename;

                $stmt2 = $conn->prepare("UPDATE employees SET face_image_path = ? WHERE employee_id = ?");
                $stmt2->bind_param("si", $relative_path, $employee_id);
                $stmt2->execute();
                $stmt2->close();

            } else {
                $response['message'] = "Failed to save face image.";
            }
        } else {
            $response['message'] = "Invalid image encoding.";
        }
    } else {
        $response['message'] = "Invalid image format.";
    }
}

$conn->close();

$response['success'] = true;
$response['message'] = "Employee added successfully.";
$response['face_saved'] = $face_filename ? true : false;
$response['face_image_path'] = $face_filename ? "uploads/faces/" . $face_filename : null;

echo json_encode($response);
exit;
?>
