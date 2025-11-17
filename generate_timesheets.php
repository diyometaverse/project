<?php
include 'db.php';
header('Content-Type: application/json');

// ✅ Check if period_id is passed
if (!isset($_POST['period_id'])) {
    echo json_encode(["status" => "error", "message" => "No payroll period selected."]);
    exit;
}

$period_id = intval($_POST['period_id']);

// 1. Get payroll period by ID
$period_sql = "SELECT start_date, end_date FROM payroll_periods WHERE period_id = ?";
$stmt = $conn->prepare($period_sql);
$stmt->bind_param("i", $period_id);
$stmt->execute();
$period_result = $stmt->get_result();
$stmt->close();

if (!$period_result || $period_result->num_rows == 0) {
    echo json_encode(["status" => "error", "message" => "Invalid payroll period."]);
    exit;
}

$period = $period_result->fetch_assoc();

// Format the period to readable format
$start_date = date("F j, Y", strtotime($period['start_date']));
$end_date   = date("F j, Y", strtotime($period['end_date']));

// 2. Get attendance records within that period
$sql = "SELECT a.employee_id, a.date, a.time_in, a.time_out 
        FROM attendance a
        WHERE a.date BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $period['start_date'], $period['end_date']);
$stmt->execute();
$result = $stmt->get_result();

$countInserted = 0;

while ($row = $result->fetch_assoc()) {
    $time_in  = strtotime($row['time_in']);
    $time_out = $row['time_out'] ? strtotime($row['time_out']) : null;

    if (!$time_in || !$time_out) {
        continue; // Skip incomplete attendance
    }

    $hours_worked   = ($time_out - $time_in) / 3600;
    $regular_hours  = min($hours_worked, 8);
    $overtime_hours = max($hours_worked - 8, 0);

    // 3. Avoid duplicates
    $check = $conn->prepare("SELECT timesheet_id FROM timesheets WHERE employee_id = ? AND work_date = ?");
    $check->bind_param("is", $row['employee_id'], $row['date']);
    $check->execute();
    $check->store_result();

    if ($check->num_rows == 0) {
        $insert = $conn->prepare("INSERT INTO timesheets 
            (employee_id, work_date, hours_worked, overtime_hours, status) 
            VALUES (?, ?, ?, ?, 'Pending')");
        $insert->bind_param("isdd", $row['employee_id'], $row['date'], $hours_worked, $overtime_hours);
        if ($insert->execute()) {
            $countInserted++;
        }
        $insert->close();
    }
    $check->close();
}

// Return formatted message
echo json_encode([
    "success" => true, 
    "message" => "Timesheets generated successfully for $start_date to $end_date! ($countInserted new records added)"
]);
?>
