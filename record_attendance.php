<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Manila');

header('Content-Type: application/json');

// DB connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "payroll_system";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'DB Connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rfid_tag = $_POST['rfid_tag'] ?? null;
    $employee_id = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
    $today = date("Y-m-d");

    // Normalize numeric RFID
    if ($rfid_tag) {
        $rfid_tag = trim($rfid_tag);           // remove spaces
        $rfid_tag = preg_replace("/\D/", "", $rfid_tag); // remove non-numeric characters
    }

    // Step 1: Check RFID first if employee_id is not provided
    if ($rfid_tag && !$employee_id) {
        $find_emp = $conn->prepare("SELECT employee_id FROM employees WHERE rfid_tag = ?");
        $find_emp->bind_param("s", $rfid_tag);
        $find_emp->execute();
        $res = $find_emp->get_result();

        if ($res->num_rows === 0) {
            echo json_encode(['status' => 'error', 'message' => 'RFID not recognized']);
            exit;
        }

        $row = $res->fetch_assoc();
        $employee_id = $row['employee_id'];
    }

    // If still no employee_id
    if (!$employee_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Employee or RFID']);
        exit;
    }

    // Step 2: Get employee info
    $emp_check = $conn->prepare("SELECT employee_id, first_name, last_name, position FROM employees WHERE employee_id = ?");
    $emp_check->bind_param("i", $employee_id);
    $emp_check->execute();
    $result = $emp_check->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Employee not found']);
        exit;
    }

    $employee = $result->fetch_assoc();
    $employee_name = $employee['first_name'] . ' ' . $employee['last_name'];
    $employee_position = $employee['position'] ?? '';

    // Step 3: Check today's attendance
    $stmt = $conn->prepare("SELECT * FROM attendance WHERE employee_id = ? AND date = ?");
    $stmt->bind_param("is", $employee_id, $today);
    $stmt->execute();
    $att_result = $stmt->get_result();

    $response = [
        'employee_id' => $employee_id,
        'employee_name' => $employee_name,
        'employee_position' => $employee_position,
        'date' => $today,
        'verification' => $rfid_tag ? 'RFID' : 'Manual'
    ];

    // Step 4: Insert or update attendance
    if ($att_result->num_rows > 0) {
        $row = $att_result->fetch_assoc();
        if ($row['time_out'] === NULL) {
            $time_out = date("H:i:s");
            $readable_time = date("g:i A");
            $update = $conn->prepare("UPDATE attendance SET time_out = ? WHERE attendance_id = ?");
            $update->bind_param("si", $time_out, $row['attendance_id']);
            $update->execute();

            $response['status'] = 'success';
            $response['type'] = 'out';
            $response['time'] = $readable_time;
            $response['message'] = "⏱ Time OUT recorded at $readable_time";
        } else {
            $response['status'] = 'warning';
            $response['message'] = "⚠️ Already logged OUT today.";
        }
    } else {
        $time_in = date("H:i:s");
        $readable_time = date("g:i A");
        $insert = $conn->prepare("INSERT INTO attendance (employee_id, date, time_in) VALUES (?, ?, ?)");
        $insert->bind_param("iss", $employee_id, $today, $time_in);
        $insert->execute();

        $response['status'] = 'success';
        $response['type'] = 'in';
        $response['time'] = $readable_time;
        $response['message'] = "✅ Time IN recorded at $readable_time";
    }

    echo json_encode($response);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
