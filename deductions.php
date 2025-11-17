<?php
include 'db.php';

// Get current payroll period (latest)
$period_sql = "SELECT period_id FROM payroll_periods ORDER BY end_date DESC LIMIT 1";
$period_result = $conn->query($period_sql);
if ($period_result->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "No payroll period found."]);
    exit;
}
$period = $period_result->fetch_assoc();
$period_id = $period['period_id'];

// Fetch sum of deductions for each type for this period
$sql = "SELECT 
            SUM(sss) AS total_sss,
            SUM(philhealth) AS total_philhealth,
            SUM(pagibig) AS total_pagibig,
            SUM(withholding_tax) AS total_tax
        FROM payroll
        WHERE period_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $period_id);
$stmt->execute();
$result = $stmt->get_result();
$totals = $result->fetch_assoc();

$total_deductions = array_sum($totals);

echo json_encode([
    "success" => true,
    "totals" => $totals,
    "total_deductions" => $total_deductions
]);
?>
