<?php
// --- Get Trainer Profile API Endpoint ---
// Location: /api/get_trainer_profile.php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'GET') { http_response_code(405); echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']); exit; }

require_once __DIR__ . '/../config.php'; // Ensure mysqli_set_charset is in config.php

// --- Input Validation ---
if (!isset($_GET['emp_uid']) || empty(trim($_GET['emp_uid']))) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameter: emp_uid.']);
    exit;
}
$emp_uid = trim($_GET['emp_uid']);

// --- Database Connection Check ---
if (!$conn || $conn->connect_error) { http_response_code(500); error_log("DB connection failed: " . ($conn ? $conn->connect_error : mysqli_connect_error())); echo json_encode(['status' => 'error', 'message' => 'Database error.']); exit; }

$response = [];

// --- Fetch Trainer Profile Logic ---
// Select relevant fields from the employees table
$sql = "SELECT
            emp_uid, name, phone, email, aadhar, dob, gender, address, role, joining_date,
            created_at, updated_at
            -- photo, aadhar_image, photo_type, aadhar_image_type -- Omitted as storage/retrieval method is unclear
        FROM employees
        WHERE emp_uid = ? AND is_ex_employee = 0 AND role = 'trainer'
        LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $emp_uid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($profile_data = mysqli_fetch_assoc($result)) {
        // Profile found
        // Add dummy image URLs for now, replace later if needed
        $profile_data['photo_url'] = null; // Or path to a default image
        $profile_data['aadhar_image_url'] = null;

        $response = [
            'status' => 'success',
            'profile' => $profile_data
        ];
        http_response_code(200); // OK
    } else {
        // No active trainer found with that emp_uid
        $response = ['status' => 'error', 'message' => 'Trainer profile not found or inactive.'];
        http_response_code(404); // Not Found
    }
    mysqli_stmt_close($stmt);
} else {
    // SQL prepare statement error
    http_response_code(500);
    error_log("SQL prepare failed in get_trainer_profile.php: " . mysqli_error($conn));
    $response = ['status' => 'error', 'message' => 'An internal error occurred while fetching profile.'];
}

mysqli_close($conn);

// --- Output JSON Response ---
echo json_encode($response);
exit;
?>