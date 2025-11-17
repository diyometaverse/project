<?php
include 'db.php';

$response = ['success' => false, 'message' => ''];

if(isset($_POST['start_date'], $_POST['end_date'])) {
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];

    // Check for overlapping periods
    $check = $conn->prepare("
        SELECT * FROM payroll_periods
        WHERE (start_date <= ? AND end_date >= ?)
           OR (start_date <= ? AND end_date >= ?)
           OR (start_date >= ? AND end_date <= ?)
    ");
    $check->bind_param("ssssss", $start, $start, $end, $end, $start, $end);
    $check->execute();
    $result = $check->get_result();

    if($result->num_rows > 0) {
        $response['message'] = "This payroll period overlaps with an existing period.";
    } else {
        // Generate label like "Oct 1 - Oct 15"
        $label = date("M j", strtotime($start)) . " - " . date("M j", strtotime($end));

        $stmt = $conn->prepare("INSERT INTO payroll_periods (start_date, end_date, label) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $start, $end, $label);

        if($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Payroll period '$label' added successfully!";
        } else {
            $response['message'] = "Failed to add period.";
        }

        $stmt->close();
    }

    $check->close();
}

header('Content-Type: application/json');
echo json_encode($response);
?>
