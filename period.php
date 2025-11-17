<?php
include 'db.php';

// Check latest payroll period
$sql = "SELECT * FROM payroll_periods ORDER BY end_date DESC LIMIT 1";
$result = $conn->query($sql);
$latest = $result->fetch_assoc();

if ($latest) {
    // Next period starts the day after the latest period ends
    $start_date = date('Y-m-d', strtotime($latest['end_date'] . ' +1 day'));
} else {
    // If no periods exist, start from a default date
    $start_date = date('Y-m-01'); // e.g., 1st of current month
}

// Next period ends 14 days after start date (bi-weekly)
$end_date = date('Y-m-d', strtotime($start_date . ' +14 days'));

// Generate a label (optional)
$label = date('M d', strtotime($start_date)) . ' - ' . date('M d', strtotime($end_date));

// Insert new period if it doesn’t exist
$check_sql = "SELECT * FROM payroll_periods WHERE start_date=? AND end_date=?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$check_result = $stmt->get_result();

if ($check_result->num_rows == 0) {
    $insert_sql = "INSERT INTO payroll_periods (start_date, end_date, label) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sss", $start_date, $end_date, $label);
    $insert_stmt->execute();
    echo "New payroll period created: $label";
} else {
    echo "Payroll period already exists: $label";
}
