<?php
// --- Mark Attendance API Endpoint ---

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Adjust for production
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Add Authorization if you implement token auth later

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Ensure it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Only POST is allowed.']);
    exit;
}

// Include database configuration
require_once __DIR__ . '/../config.php';

// Get raw POST data (assuming Flutter sends JSON)
$rawData = file_get_contents('php://input');
$postData = json_decode($rawData, true);

// --- Input Validation ---
// We expect 'scanned_cust_uid' (from QR) and 'logged_in_cust_uid' (from app's session/token)
if ($postData === null || !isset($postData['scanned_cust_uid']) || !isset($postData['logged_in_cust_uid'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields: scanned_cust_uid and logged_in_cust_uid.']);
    exit;
}

$scanned_cust_uid = trim($postData['scanned_cust_uid']);
$logged_in_cust_uid = trim($postData['logged_in_cust_uid']); // This UID should be retrieved securely after login in the app

if (empty($scanned_cust_uid) || empty($logged_in_cust_uid)) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Scanned UID and logged-in UID cannot be empty.']);
    exit;
}

// --- Database Connection Check ---
if (!$conn) {
    http_response_code(500); // Internal Server Error
    error_log("Database connection failed in mark_attendance.php");
    echo json_encode(['status' => 'error', 'message' => 'Database connection error.']);
    exit;
}

// --- Attendance Logic ---
$response = [];
$current_date = date('Y-m-d'); // Get current date

// 1. Verify logged_in_cust_uid matches scanned_cust_uid
if ($logged_in_cust_uid !== $scanned_cust_uid) {
    http_response_code(403); // Forbidden
    $response = ['status' => 'error', 'message' => 'Scanned QR code does not match the logged-in user.'];
    echo json_encode($response);
    mysqli_close($conn);
    exit;
}

// 2. Verify scanned_cust_uid exists and get cust_details.id
$cust_id = null;
$sql_verify = "SELECT id FROM cust_details WHERE cust_uid = ? LIMIT 1";
$stmt_verify = mysqli_prepare($conn, $sql_verify);

if ($stmt_verify) {
    mysqli_stmt_bind_param($stmt_verify, "s", $scanned_cust_uid);
    mysqli_stmt_execute($stmt_verify);
    $result_verify = mysqli_stmt_get_result($stmt_verify);
    if ($row_verify = mysqli_fetch_assoc($result_verify)) {
        $cust_id = $row_verify['id']; // Get the primary key ID
    } else {
        // Scanned UID doesn't exist in cust_details
        http_response_code(400); // Bad Request
        $response = ['status' => 'error', 'message' => 'Invalid QR Code: User not found.'];
        echo json_encode($response);
        mysqli_stmt_close($stmt_verify);
        mysqli_close($conn);
        exit;
    }
    mysqli_stmt_close($stmt_verify);
} else {
    http_response_code(500);
    error_log("SQL prepare failed (verify user) in mark_attendance.php: " . mysqli_error($conn));
    $response = ['status' => 'error', 'message' => 'Database error during user verification.'];
    echo json_encode($response);
    mysqli_close($conn);
    exit;
}

// 3. Check for duplicate attendance today
$sql_check = "SELECT id FROM customer_attendance WHERE cust_uid = ? AND date = ?";
$stmt_check = mysqli_prepare($conn, $sql_check);

if ($stmt_check) {
    mysqli_stmt_bind_param($stmt_check, "ss", $scanned_cust_uid, $current_date);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        // Duplicate found
        http_response_code(409); // Conflict
        $response = ['status' => 'error', 'message' => 'Attendance already marked for today.'];
        echo json_encode($response);
        mysqli_stmt_close($stmt_check);
        mysqli_close($conn);
        exit;
    }
    mysqli_stmt_close($stmt_check);
} else {
    http_response_code(500);
    error_log("SQL prepare failed (check duplicate) in mark_attendance.php: " . mysqli_error($conn));
    $response = ['status' => 'error', 'message' => 'Database error during duplicate check.'];
    echo json_encode($response);
    mysqli_close($conn);
    exit;
}

// 4. Insert new attendance record
// Corrected version ensuring all columns match bind_param

date_default_timezone_set('Asia/Kolkata'); // Ensure timezone
$current_date = date('Y-m-d');
$current_datetime = date('Y-m-d H:i:s');
$current_attendance_time = date('H:i:s'); // Use only time for attendance_time column if it's TIME type

$marked_by = $cust_name . ' (App)'; // Use fetched cust_name
$notes = 'Self-scan via mobile app.';

// Fetch necessary details again just before insert (if not already available)
// This assumes $cust_id, $scanned_cust_uid, $cust_name, $vehicle_name, $trainer_name are correctly populated from Step 2

// Corrected INSERT query based on user's actual columns
// (id, cust_id, cust_uid, emp_uid, cust_name, date, attendance_time, time_in, time_out, vehicle_name, trainer_name, notes, created_at, updated_at)
// We will insert into: cust_id, cust_uid, cust_name, date, attendance_time, vehicle_name, trainer_name, notes, created_at, updated_at
// emp_uid, time_in, time_out will be left NULL or default
$sql_insert = "INSERT INTO customer_attendance (
                   cust_id, cust_uid, cust_name, date, attendance_time,
                   vehicle_name, trainer_name, notes,
                   created_at, updated_at, emp_uid -- explicitly setting emp_uid to NULL
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)"; // 10 placeholders for 10 values + NULL for emp_uid

$stmt_insert = mysqli_prepare($conn, $sql_insert);

if ($stmt_insert) {
    // Corrected bind_param: Check types carefully!
    // Assuming: cust_id(i), cust_uid(s), cust_name(s), date(s), attendance_time(s - using H:i:s format),
    // vehicle_name(s), trainer_name(s), notes(s), created_at(s - using datetime), updated_at(s - using datetime)
    mysqli_stmt_bind_param(
        $stmt_insert,
        "isssssssss", // i, s, s, s, s, s, s, s, s, s -> 10 types matching the 10 columns being inserted
        $cust_id,
        $scanned_cust_uid, // THIS WAS THE MISSING VARIABLE IN BIND previously
        $cust_name,
        $current_date,
        $current_attendance_time, // Use H:i:s format if column type is TIME
        $vehicle_name,
        $trainer_name,
        $notes,
        $current_datetime, // created_at
        $current_datetime  // updated_at
    );

    if (mysqli_stmt_execute($stmt_insert)) {
        // Success
        http_response_code(201); // Created
        $response = ['status' => 'success', 'message' => 'Attendance marked successfully!'];
    } else {
        // Insert failed
        http_response_code(500);
        error_log("SQL execute failed (insert attendance) in mark_attendance.php: " . mysqli_stmt_error($stmt_insert));
        $response = ['status' => 'error', 'message' => 'Failed to record attendance due to database error.'];
    }
    mysqli_stmt_close($stmt_insert);
} else {
    // SQL prepare statement error
    http_response_code(500);
    error_log("SQL prepare failed (insert attendance) in mark_attendance.php: " . mysqli_error($conn) . " SQL: " . $sql_insert);
    $response = ['status' => 'error', 'message' => 'Database error during attendance recording preparation.'];
}

mysqli_close($conn);

// --- Output JSON Response ---
echo json_encode($response);
exit;

?> // Make sure this is the absolute end of the file