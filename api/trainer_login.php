<?php
// --- Trainer Login API Endpoint ---
// Location: /api/trainer_login.php

// Error reporting off for production simulation
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Adjust for production
header('Access-Control-Allow-Methods: POST, OPTIONS'); // Login should be POST
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

// Expecting JSON input from Flutter for login data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Only POST is allowed.']); exit; }

require_once __DIR__ . '/../config.php'; // Ensure mysqli_set_charset is in config.php

// --- Input Validation ---
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE); // Decode JSON body

if ($input === null || !isset($input['emp_uid'])) {
    http_response_code(400);
    error_log("Trainer Login Input Fail. Raw Input: " . $inputJSON . " | Parsed: " . print_r($input, true));
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameter: emp_uid.']);
    exit;
}

$scanned_emp_uid = trim($input['emp_uid']); // Plain text Trainer emp_uid from QR

if (empty($scanned_emp_uid)) {
     http_response_code(400);
     echo json_encode(['status' => 'error', 'message' => 'Trainer UID cannot be empty.']);
     exit;
}

// --- Database Connection Check ---
if (!$conn || $conn->connect_error) { http_response_code(500); $error_msg = $conn ? $conn->connect_error : mysqli_connect_error(); error_log("DB connection failed: " . $error_msg); echo json_encode(['status' => 'error', 'message' => 'Database error.']); exit; }

$response = [];

// --- Verify Trainer ---
// Check employees table for the plain emp_uid, ensure they are active and have role 'trainer'
$sql = "SELECT emp_uid, name, role
        FROM employees
        WHERE emp_uid = ? AND is_ex_employee = 0 AND role = 'trainer'
        LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $scanned_emp_uid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($trainer = mysqli_fetch_assoc($result)) {
        // Trainer found and is active trainer! Login successful.
        http_response_code(200); // OK
        $response = [
            'status' => 'success',
            'message' => 'Trainer login successful!',
            'emp_uid' => $trainer['emp_uid'], // Send UID back
            'name' => $trainer['name'],      // Send name back
            'role' => $trainer['role']       // Send role back (confirming it's trainer)
            // Add any other basic details needed immediately after login
        ];
        // In a real app, you would generate a session token here and return it
        // For simplicity, Flutter will just store the emp_uid, name, role.

    } else {
        // No active trainer found with that emp_uid
        http_response_code(401); // Unauthorized
        $response = ['status' => 'error', 'message' => 'Invalid QR Code: Active trainer not found.'];
    }
    mysqli_stmt_close($stmt);
} else {
    // SQL prepare statement error
    http_response_code(500);
    error_log("SQL prepare failed in trainer_login.php: " . mysqli_error($conn));
    $response = ['status' => 'error', 'message' => 'An internal error occurred during login.'];
}

mysqli_close($conn);

// --- Output JSON Response ---
echo json_encode($response);
exit;

?>