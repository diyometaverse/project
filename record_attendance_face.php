<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');

$employee_id = intval($_POST['employee_id'] ?? 0);
$face_data = $_POST['face_image'] ?? '';

$debug = [];
$debug[] = "START";

if (!$employee_id || !$face_data) {
    $debug[] = "Invalid face input";
    echo json_encode(['success' => false, 'message' => 'Invalid face input', 'debug' => $debug]);
    exit;
}

$face_data = str_replace("data:image/jpeg;base64,", "", $face_data);
$face_data = base64_decode($face_data);

$temp_live = __DIR__ . "/temp/live_face_{$employee_id}.jpg"; // Correct path
file_put_contents($temp_live, $face_data);

// Get stored reference face
$host="localhost"; $user="root"; $pass=""; $db="payroll_system";
$conn = new mysqli($host,$user,$pass,$db);

$q = $conn->prepare("SELECT face_image_path FROM employees WHERE employee_id=?");
$q->bind_param("i", $employee_id);
$q->execute();
$res = $q->get_result();

$debug[] = "Employee ID: $employee_id";
$debug[] = "Temp live: $temp_live";

if ($res->num_rows === 0) {
    $debug[] = "Employee not found";
    echo json_encode(['success'=>false, 'message'=>'Employee not found', 'debug' => $debug]);
    exit;
}

$ref = $res->fetch_assoc()['face_image_path'];
$stored_face = __DIR__ . "/" . $ref;

$debug[] = "Stored face path: $stored_face";

if (!file_exists($stored_face)) {
    $debug[] = "Stored face missing";
    echo json_encode(['success'=>false, 'message'=>'Stored face missing', 'debug' => $debug]);
    exit;
}

$pythonPath = escapeshellarg(__DIR__ . "/face_matcher.py");
$cmd = escapeshellcmd("python $pythonPath \"$stored_face\" \"$temp_live\"");
$debug[] = "Running Python: $cmd";
$output = exec($cmd);
$debug[] = "Python output: $output";

if ($output === "MATCH") {
    $debug[] = "Face MATCH";
    $attendance = file_get_contents("http://localhost/project/record_attendance.php", false,
        stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded",
                'content' => http_build_query([
                    'employee_id' => $employee_id,
                    'face_verified' => 1
                ])
            ]
        ])
    );
    $json = json_decode($attendance, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $debug[] = "Attendance JSON valid";
        $json['debug'] = $debug;
        echo json_encode($json);
        exit;
    } else {
        $debug[] = "Attendance JSON invalid";
        echo json_encode(['success' => false, 'message' => 'Attendance response not valid JSON', 'debug' => $debug]);
        exit;
    }
}

$debug[] = "Unknown error";
echo json_encode(['success' => false, 'message' => 'Unknown error', 'debug' => $debug]);
exit;
