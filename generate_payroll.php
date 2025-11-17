<?php
include 'db.php';

// 1. Get selected payroll period from POST
$period_id = $_POST['period_id'] ?? null;

if (!$period_id) {
    echo json_encode(["success" => false, "message" => "No payroll period selected!"]);
    exit;
}

// Fetch start_date and end_date for the selected period
$period_sql = "SELECT start_date, end_date FROM payroll_periods WHERE period_id=?";
$stmt = $conn->prepare($period_sql);
$stmt->bind_param("i", $period_id);
$stmt->execute();
$period = $stmt->get_result()->fetch_assoc();

if (!$period) {
    echo json_encode(["success" => false, "message" => "Payroll period not found!"]);
    exit;
}

$start_date = $period['start_date'];
$end_date = $period['end_date'];

// 2. Fetch approved timesheets for this period
$ts_sql = "SELECT t.employee_id, SUM(t.hours_worked) as total_hours, SUM(t.overtime_hours) as overtime
           FROM timesheets t
           WHERE t.status='Approved' AND t.work_date BETWEEN ? AND ?
           GROUP BY t.employee_id";
$ts_stmt = $conn->prepare($ts_sql);
$ts_stmt->bind_param("ss", $start_date, $end_date);
$ts_stmt->execute();
$ts_result = $ts_stmt->get_result();

if ($ts_result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "No approved timesheets found for this period."]);
    exit;
}

$countInserted = 0;

// 3. Process each employee
while($row = $ts_result->fetch_assoc()) {
    $employee_id = $row['employee_id'];
    $total_hours = $row['total_hours'];
    $overtime_hours = $row['overtime'];

    // Fetch daily rate
    $emp_sql = "SELECT daily_rate FROM employees WHERE employee_id=?";
    $emp_stmt = $conn->prepare($emp_sql);
    $emp_stmt->bind_param("i", $employee_id);
    $emp_stmt->execute();
    $daily_rate = $emp_stmt->get_result()->fetch_assoc()['daily_rate'];

    // Regular hours capped at 8 per day
    $regular_hours = min($total_hours, 8 * ceil($total_hours/8));

    $base_salary = ($regular_hours / 8) * $daily_rate;
    $gross_pay = $base_salary + ($overtime_hours * ($daily_rate / 8));

    // --- Add statutory deductions ---
    $sss = $gross_pay * 0.036;
    $philhealth = $gross_pay * 0.027;
    $pagibig = $gross_pay * 0.02;
    $withholding_tax = $gross_pay * 0.10;

    $deductions = $sss + $philhealth + $pagibig + $withholding_tax;
    $net_pay = $gross_pay - $deductions;

    // Insert or update payroll record
    $insert_sql = "INSERT INTO payroll (employee_id, period_id, total_hours, base_salary, overtime, gross_pay, deductions, net_pay, sss, philhealth, pagibig, withholding_tax)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                   ON DUPLICATE KEY UPDATE 
                       total_hours=VALUES(total_hours),
                       base_salary=VALUES(base_salary),
                       overtime=VALUES(overtime),
                       gross_pay=VALUES(gross_pay),
                       deductions=VALUES(deductions),
                       net_pay=VALUES(net_pay),
                       sss=VALUES(sss),
                       philhealth=VALUES(philhealth),
                       pagibig=VALUES(pagibig),
                       withholding_tax=VALUES(withholding_tax)";

    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param(
        "iiddddddiiii",
        $employee_id,
        $period_id,
        $total_hours,
        $base_salary,
        $overtime_hours,
        $gross_pay,
        $deductions,
        $net_pay,
        $sss,
        $philhealth,
        $pagibig,
        $withholding_tax
    );
    $insert_stmt->execute();

    // Count inserted/updated records
    $countInserted++;
}

// Return success message
echo json_encode([
    "success" => true, 
    "message" => "Payroll generated successfully for $start_date to $end_date! ($countInserted records processed)"
]);

?>
