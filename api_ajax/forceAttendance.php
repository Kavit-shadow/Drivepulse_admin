<?php
//include('../includes/authenticationAdminOrStaffOrTrainer.php');
//authenticationAdminOrStaffOrTrainer('../');

include("../config.php");

// Check if required parameters are set
if (!isset($_POST['cust_uid']) && !isset($_POST['acc_id']) && !isset($_POST['cust_id']) && !isset($_POST['acc_name'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$cust_uid = $_POST['cust_uid'];
$cust_id = $_POST['cust_id'];

// Get emp_id from users_db based on acc_id
$acc_id = $_POST['acc_id'];
$emp_stmt = $conn->prepare("SELECT emp_uid FROM users_db WHERE id = ?");
$emp_stmt->bind_param("i", $acc_id);
$emp_stmt->execute();
$emp_result = $emp_stmt->get_result();
$emp_row = $emp_result->fetch_assoc();
$emp_uid = $emp_row['emp_uid'] ?? 'XXX';
$emp_stmt->close();

$note = isset($_POST['note']) ? $_POST['note'] : 'Attendance marked by employee @' . $_POST['acc_name'] . ' (UID: ' . $emp_uid . ')';

// Get current date and time
date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d');
$attendance_time = date('Y-m-d H:i:s');
$created_at = date('Y-m-d H:i:s');
$updated_at = date('Y-m-d H:i:s');

// Get employee details
if ($emp_uid !== 'XXX') {
    $emp_details_stmt = $conn->prepare("SELECT emp_uid, name FROM employees WHERE emp_uid = ?");
    $emp_details_stmt->bind_param("s", $emp_uid);
    $emp_details_stmt->execute();
    $emp_result = $emp_details_stmt->get_result();
    $emp_row = $emp_result->fetch_assoc();
    $emp_details_stmt->close();
} else {
    $emp_row = [
        'emp_uid' => 'XXX',
        'name' => $_POST['acc_name']
    ];
}

if (!$emp_row) {
    echo json_encode(['success' => false, 'message' => 'Employee not found']);
    exit;
}

$emp_id = $emp_row['emp_uid'];
$emp_name = $emp_row['name'];

// Get customer details from customer table
$stmt = $conn->prepare("SELECT id, name, vehicle, days, phone FROM cust_details WHERE cust_uid = ? OR id = ?");
$stmt->bind_param("ss", $cust_uid, $cust_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $cust_id = $row['id'];
    $cust_name = $row['name'];
    $cust_vehicle = $row['vehicle'];
    $cust_days = $row['days'];
    $cust_phone = $row['phone'];
    $stmt->close();

    // First check total attendance count
    $count_sql = "SELECT COUNT(*) as attendance_count FROM customer_attendance WHERE cust_uid = ?";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("s", $cust_uid);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $count_row = $count_result->fetch_assoc();
    $attendance_count = $count_row['attendance_count'];
    $count_stmt->close();

    // if ($attendance_count >= $cust_days) {
    //     echo json_encode(['success' => false, 'message' => 'Maximum attendance days reached']);
    //     exit;
    // }

    // Check if there's an existing entry for today
    $check_sql = "SELECT id, time_out FROM customer_attendance WHERE cust_uid = ? AND date = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $cust_uid, $date);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    // Get all vehicle database tables
    $vehicle_tables_sql = "SELECT data_base_table FROM vehicles";
    $vehicle_tables_result = mysqli_query($conn, $vehicle_tables_sql);
    $vehicle_tables = mysqli_fetch_all($vehicle_tables_result);

    // Check if customer has any active training slots
    $has_active_training = false;
    foreach ($vehicle_tables as $table) {
        $check_active_sql = "SELECT * FROM " . $table[0] . " WHERE phone = ? AND status = 'active'";
        $check_active_stmt = $conn->prepare($check_active_sql);
        $check_active_stmt->bind_param("s", $cust_phone);
        $check_active_stmt->execute();
        $check_active_result = $check_active_stmt->get_result();

        if ($check_active_result->num_rows > 0) {
            $has_active_training = true;
            $check_active_stmt->close();
            break;
        }
        $check_active_stmt->close();
    }

    if (!$has_active_training) {
        echo json_encode(['success' => false, 'message' => 'Customer training has been ended']);
        exit;
    }

    if ($check_result->num_rows > 0) {
        $existing_record = $check_result->fetch_assoc();
        $check_stmt->close();



        if ($attendance_count >= $cust_days && ($existing_record['time_out'] !== NULL)) {
            echo json_encode(['success' => false, 'message' => 'Maximum attendance days reached']);
            exit;
        }
        if ($existing_record['time_out'] === NULL) {
            // Update existing record with time_out
            $update_sql = "UPDATE customer_attendance SET time_out = ?, updated_at = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ssi", $attendance_time, $updated_at, $existing_record['id']);

            if ($update_stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update time out']);
            }
            $update_stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Already checked out for today']);
        }
    } else {
        $check_stmt->close();
        // Insert new attendance record with time_in
        $sql = "INSERT INTO customer_attendance (
            cust_id, cust_uid, emp_uid, cust_name, trainer_name,
            date, attendance_time, time_in, notes, 
            created_at, updated_at, vehicle_name
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $insert_stmt = $conn->prepare($sql);
        $insert_stmt->bind_param(
            "ssssssssssss",
            $cust_id,
            $cust_uid,
            $emp_id,
            $cust_name,
            $emp_name,
            $date,
            $attendance_time,
            $attendance_time,
            $note,
            $created_at,
            $updated_at,
            $cust_vehicle
        );

        if ($insert_stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to record attendance']);
        }
        $insert_stmt->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Customer not found']);
    $stmt->close();
}

$conn->close();
