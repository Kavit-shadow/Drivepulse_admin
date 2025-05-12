<?php
// --- Mark Student Attendance API Endpoint (Simple Trainer UID Scan v1) ---

// Error reporting off for production simulation
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Only POST is allowed.']); exit; }

require_once __DIR__ . '/../config.php'; // Ensure mysqli_set_charset is in config.php

// --- Input Validation ---
// Expecting 'uid' (student cust_uid) and 'emp_uid' (PLAIN trainer emp_uid)
$input = file_get_contents('php://input');
parse_str($input, $postData); // Assumes Flutter sends form-urlencoded

if ($postData === null || !isset($postData['uid']) || !isset($postData['emp_uid'])) {
    http_response_code(400);
    error_log("Mark Attendance Input Fail. Input: " . $input . " | Parsed: " . print_r($postData, true));
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters: uid (student) and emp_uid (trainer).']);
    exit;
}

$student_cust_uid = trim($postData['uid']);
$trainer_emp_uid = trim($postData['emp_uid']); // Plain text Trainer UID from QR
$note = isset($postData['note']) ? trim($postData['note']) : 'Scan via Flutter App';

if (empty($student_cust_uid) || empty($trainer_emp_uid)) {
     http_response_code(400);
     echo json_encode(['status' => 'error', 'message' => 'Student UID and Trainer UID cannot be empty.']);
     exit;
}

// --- Database Connection Check ---
if (!$conn || $conn->connect_error) { http_response_code(500); $error_msg = $conn ? $conn->connect_error : mysqli_connect_error(); error_log("DB connection failed: " . $error_msg); echo json_encode(['status' => 'error', 'message' => 'Database error.']); exit; }

// Set timezone
date_default_timezone_set('Asia/Kolkata');
$current_date = date('Y-m-d');
$current_datetime = date('Y-m-d H:i:s');
$current_attendance_time = date('H:i:s');
$response = [];

// --- Simplified Logic ---

// 1. Verify Trainer Exists using plain emp_uid
$sql_emp = "SELECT name FROM employees WHERE emp_uid = ? AND is_ex_employee = 0 LIMIT 1";
$stmt_emp = mysqli_prepare($conn, $sql_emp);
if (!$stmt_emp) { http_response_code(500); error_log("SQL prepare failed (get trainer): " . mysqli_error($conn)); echo json_encode(['status' => 'error', 'message' => 'DB error checking trainer.']); mysqli_close($conn); exit; }
mysqli_stmt_bind_param($stmt_emp, "s", $trainer_emp_uid);
mysqli_stmt_execute($stmt_emp);
$result_emp = mysqli_stmt_get_result($stmt_emp);
$employee = mysqli_fetch_assoc($result_emp);
mysqli_stmt_close($stmt_emp);

if (!$employee) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid Trainer QR: Trainer UID not found or inactive.']);
    mysqli_close($conn); exit;
}
$trainer_actual_name = $employee['name']; // Name of the trainer whose QR was scanned

// 2. Verify Student Exists and get details
$cust_id = null; $cust_name = null; $vehicle_name = null;
$sql_cust = "SELECT id, name, vehicle FROM cust_details WHERE cust_uid = ? LIMIT 1";
$stmt_cust = mysqli_prepare($conn, $sql_cust);
if ($stmt_cust) {
    mysqli_stmt_bind_param($stmt_cust, "s", $student_cust_uid); mysqli_stmt_execute($stmt_cust); $result_cust = mysqli_stmt_get_result($stmt_cust);
    if ($customer = mysqli_fetch_assoc($result_cust)) { $cust_id = $customer['id']; $cust_name = $customer['name']; $vehicle_parts = explode('/', $customer['vehicle']); $vehicle_name = trim($vehicle_parts[0] ?? 'N/A'); }
    else { http_response_code(400); $response = ['status' => 'error', 'message' => 'Invalid Student UID: Student not found.']; echo json_encode($response); mysqli_stmt_close($stmt_cust); mysqli_close($conn); exit; }
    mysqli_stmt_close($stmt_cust);
} else { http_response_code(500); error_log("SQL prepare failed (get customer): " . mysqli_error($conn)); $response = ['status' => 'error', 'message' => 'DB error checking customer.']; echo json_encode($response); mysqli_close($conn); exit; }

// 3. Check for Duplicate Attendance Today
$sql_check = "SELECT id FROM customer_attendance WHERE cust_uid = ? AND date = ?";
$stmt_check = mysqli_prepare($conn, $sql_check);
if ($stmt_check) {
    mysqli_stmt_bind_param($stmt_check, "ss", $student_cust_uid, $current_date); mysqli_stmt_execute($stmt_check); mysqli_stmt_store_result($stmt_check);
    if (mysqli_stmt_num_rows($stmt_check) > 0) { http_response_code(409); $response = ['status' => 'error', 'message' => 'Attendance already marked for today.']; echo json_encode($response); mysqli_stmt_close($stmt_check); mysqli_close($conn); exit; }
    mysqli_stmt_close($stmt_check);
} else { http_response_code(500); error_log("SQL prepare failed (check duplicate): " . mysqli_error($conn)); $response = ['status' => 'error', 'message' => 'Database error during duplicate check.']; echo json_encode($response); mysqli_close($conn); exit; }

// 4. Insert New Attendance Record
$sql_insert = "INSERT INTO customer_attendance (cust_id, cust_uid, cust_name, date, attendance_time, vehicle_name, trainer_name, notes, emp_uid, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt_insert = mysqli_prepare($conn, $sql_insert);
if ($stmt_insert) {
    mysqli_stmt_bind_param( $stmt_insert, "issssssssss", $cust_id, $student_cust_uid, $cust_name, $current_date, $current_attendance_time, $vehicle_name, $trainer_actual_name, $note, $trainer_emp_uid, $current_datetime, $current_datetime ); // Pass plain trainer UID to emp_uid column
    if (mysqli_stmt_execute($stmt_insert)) {
        http_response_code(201); $response = ['status' => 'success', 'message' => 'Attendance marked successfully by ' . $trainer_actual_name];
    } else { http_response_code(500); error_log("SQL execute failed (insert attendance): " . mysqli_stmt_error($stmt_insert)); $response = ['status' => 'error', 'message' => 'Failed to record attendance.']; }
    mysqli_stmt_close($stmt_insert);
} else { http_response_code(500); error_log("SQL prepare failed (insert attendance): " . mysqli_error($conn) . " SQL: " . $sql_insert); $response = ['status' => 'error', 'message' => 'DB error preparing attendance record.']; }

mysqli_close($conn);
echo json_encode($response);
exit;
?>