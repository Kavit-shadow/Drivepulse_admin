<?php
// include('../includes/authentication.php');
// authenticationAdmin('../');

include("../config.php");

// Check if required parameters are set
if (!isset($_POST['uid']) && !isset($_POST['emp_uid'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$cust_uid = $_POST['uid'];
$emp_uid = $_POST['emp_uid'];
$note = isset($_POST['note']) ? $_POST['note'] : '';

// Get current date and time
date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d');
$attendance_time = date('Y-m-d H:i:s');
$created_at = date('Y-m-d H:i:s');
$updated_at = date('Y-m-d H:i:s');

// Get employee details
$emp_stmt = $conn->prepare("SELECT emp_uid, name FROM employees WHERE MD5(emp_uid) COLLATE utf8mb4_unicode_ci = ? COLLATE utf8mb4_unicode_ci");
$emp_stmt->bind_param("s", $emp_uid);
$emp_stmt->execute();
$emp_result = $emp_stmt->get_result();
$emp_row = $emp_result->fetch_assoc();

if (!$emp_row) {
    echo json_encode(['success' => false, 'message' => 'Employee not found']);
    exit;
}

$emp_id = $emp_row['emp_uid'];
$emp_name = $emp_row['name'];

// Get customer details from customer table
$stmt = $conn->prepare("SELECT id, name, vehicle, days, phone FROM cust_details WHERE cust_uid = ?");
$stmt->bind_param("s", $cust_uid);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $cust_id = $row['id'];
    $cust_name = $row['name'];
    $cust_vehicle = $row['vehicle'];
    $cust_days = $row['days'];
    $cust_phone = $row['phone'];
    // First check total attendance count
    $count_sql = "SELECT COUNT(*) as attendance_count FROM customer_attendance WHERE cust_uid = ?";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("s", $cust_uid);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $count_row = $count_result->fetch_assoc();
    $attendance_count = $count_row['attendance_count'];

    // if ($attendance_count >= $cust_days) {
    //     echo json_encode(['success' => false, 'message' => 'Maximum attendance days reached']);
    //     $count_stmt->close();
    //     exit;
    // }

    // Check if there's an existing entry for today
    $check_sql = "SELECT id, time_out FROM customer_attendance WHERE cust_uid = ? AND date = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $cust_uid, $date);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    
    // Check if less than 10 minutes
    $query = "SELECT id, time_out, time_in FROM customer_attendance WHERE cust_uid = ? AND date = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $cust_uid, $date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $record = $result->fetch_assoc();
        $check_in_time = new DateTime($record['time_in']); // Parse the time_in string
        $current_time = new DateTime(); // Get the current time

        $time_difference = $check_in_time->diff($current_time);
        $elapsed_minutes = ($time_difference->days * 24 * 60) + ($time_difference->h * 60) + $time_difference->i; // Calculate total minutes

        if ($elapsed_minutes < 10) { // Check if less than 10 minutes
            echo json_encode(['success' => false, 'message' => 'You need to wait at least 10 minutes before checking in again.']);
            exit;
        }
    }
    

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
        echo json_encode(['success' => false, 'message' => 'Your training has been ended']);
        exit;
    }


    if ($check_result->num_rows > 0) {
        $existing_record = $check_result->fetch_assoc();

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
        // Insert new attendance record with time_in
        $sql = "INSERT INTO customer_attendance (
            cust_id, cust_uid, emp_uid, cust_name, trainer_name,
            date, attendance_time, time_in, notes, 
            created_at, updated_at, vehicle_name
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
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

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to record attendance']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Customer not found']);
}

$emp_stmt->close();
$stmt->close();
$conn->close();
