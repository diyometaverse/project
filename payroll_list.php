<?php
include 'db.php';

$period = isset($_GET['period']) ? $_GET['period'] : 'all';

if($period == 'all') {
    $sql = "SELECT p.payroll_id, e.first_name, e.last_name, p.gross_pay, p.deductions, p.net_pay,
                   p.sss, p.philhealth, p.pagibig, p.withholding_tax
            FROM payroll p
            JOIN employees e ON p.employee_id = e.employee_id
            ORDER BY e.last_name, e.first_name";
    $result = $conn->query($sql);
} else {
    $sql = "SELECT p.payroll_id, e.first_name, e.last_name, p.gross_pay, p.deductions, p.net_pay,
                   p.sss, p.philhealth, p.pagibig, p.withholding_tax
            FROM payroll p
            JOIN employees e ON p.employee_id = e.employee_id
            WHERE p.period_id = ?
            ORDER BY e.last_name, e.first_name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $period);
    $stmt->execute();
    $result = $stmt->get_result();
}

$data = [];
while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
