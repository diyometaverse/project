<?php
session_start();
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$employee_id = $data['employee_id'] ?? null;
$action = $data['action'] ?? null;
$date = $data['date'] ?? date("Y-m-d");

if(!$employee_id || !$action){
    echo json_encode(['success'=>false, 'message'=>'Invalid request']);
    exit;
}

if($action === 'time_in'){
    $time_in = date("H:i:s");
    // Insert or update
    $check = $conn->query("SELECT * FROM attendance WHERE employee_id=$employee_id AND date='$date'");
    if($check->num_rows>0){
        $conn->query("UPDATE attendance SET time_in='$time_in', status='present' WHERE employee_id=$employee_id AND date='$date'");
    } else {
        $conn->query("INSERT INTO attendance(employee_id,date,time_in,status) VALUES($employee_id,'$date','$time_in','present')");
    }
    echo json_encode(['success'=>true,'time_in'=>$time_in]);
    exit;
}

if($action === 'time_out'){
    $time_out = date("H:i:s");
    $check = $conn->query("SELECT * FROM attendance WHERE employee_id=$employee_id AND date='$date'");
    if($check->num_rows>0){
        $conn->query("UPDATE attendance SET time_out='$time_out' WHERE employee_id=$employee_id AND date='$date'");
        echo json_encode(['success'=>true,'time_out'=>$time_out]);
        exit;
    } else {
        echo json_encode(['success'=>false,'message'=>'Employee has not timed in yet']);
        exit;
    }
}
?>
