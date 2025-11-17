<?php
include 'db.php';

$period = isset($_GET['period']) ? $_GET['period'] : 'all';

if($period == 'all') {
    $sql = "SELECT 
                SUM(gross_pay) AS total_gross,
                SUM(deductions) AS total_deductions,
                SUM(net_pay) AS total_net,
                SUM(sss) AS total_sss,
                SUM(philhealth) AS total_philhealth,
                SUM(pagibig) AS total_pagibig,
                SUM(withholding_tax) AS total_tax,
                COUNT(DISTINCT employee_id) AS total_employees
            FROM payroll";
    $result = $conn->query($sql);
    $totals = $result->fetch_assoc();
} else {
    $sql = "SELECT 
                SUM(gross_pay) AS total_gross,
                SUM(deductions) AS total_deductions,
                SUM(net_pay) AS total_net,
                SUM(sss) AS total_sss,
                SUM(philhealth) AS total_philhealth,
                SUM(pagibig) AS total_pagibig,
                SUM(withholding_tax) AS total_tax,
                COUNT(DISTINCT employee_id) AS total_employees
            FROM payroll
            WHERE period_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $period);
    $stmt->execute();
    $totals = $stmt->get_result()->fetch_assoc();
}

echo json_encode($totals);
