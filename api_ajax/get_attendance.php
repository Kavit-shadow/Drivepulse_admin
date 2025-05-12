<?php

// include('../includes/authenticationAdminOrStaffOrTrainer.php');
// authenticationAdminOrStaffOrTrainer('../');


include("../config.php");
// Check if ID was sent
if (!isset($_POST['id'])) {
    echo json_encode(['error' => 'No ID provided']);
    exit;
}

$cust_id = $_POST['id'];

// Prepare SQL statement to prevent SQL injection
$sql = "SELECT `id`, `cust_id`, `cust_uid`, `emp_uid` as employee_uid, 
        `cust_name` as customer_name, `date`, `attendance_time`, 
        `time_in`, `time_out`, `vehicle_name`, `trainer_name`, 
        `notes` as note
        FROM `customer_attendance` 
        WHERE cust_id = ? OR cust_uid = ?";

try {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $cust_id, $cust_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

// Close connections
$stmt->close();
$conn->close();
