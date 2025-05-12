<?php
// --- Student Login API Endpoint ---

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow requests from any origin (adjust for production)
header('Access-Control-Allow-Methods: POST, OPTIONS'); // Allow POST and OPTIONS methods
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS request (preflight request for CORS)
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

// Include database configuration (adjust path if needed - assuming api/ is in the root)
require_once __DIR__ . '/../config.php'; // Go up one level to find config.php

// Get raw POST data
$rawData = file_get_contents('php://input');
$postData = json_decode($rawData, true); // Assuming Flutter sends JSON data

// --- Input Validation ---
if ($postData === null || !isset($postData['phone']) || !isset($postData['password'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields: phone and password.']);
    exit;
}

$phone = trim($postData['phone']);
$password = trim($postData['password']);

if (empty($phone) || empty($password)) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Phone and password cannot be empty.']);
    exit;
}

// --- Database Connection Check ---
if (!$conn) {
    http_response_code(500); // Internal Server Error
    error_log("Database connection failed in student_login.php"); // Log error server-side
    echo json_encode(['status' => 'error', 'message' => 'Database connection error. Please try again later.']);
    exit;
}

// --- Login Logic ---
$response = [];
$hashed_input_pass = md5($password); // Calculate MD5 hash of the input password

// Use prepared statement to prevent SQL injection
$sql = "SELECT id, cust_uid, name, app_md5_pass FROM cust_details WHERE phone = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $phone);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // User found by phone number, now check password hash
        $stored_hash = $row['app_md5_pass'];

        if ($stored_hash === null) {
            // Password not set for this user
            $response = ['status' => 'error', 'message' => 'App access not set up for this account.'];
            http_response_code(401); // Unauthorized
        } elseif ($stored_hash === $hashed_input_pass) {
            // Password matches! Login successful.
            $response = [
                'status' => 'success',
                'message' => 'Login successful!',
                'cust_uid' => $row['cust_uid'], // Send cust_uid back to the app
                'name' => $row['name']         // Send name back
                // Add any other data the app needs after login
            ];
            http_response_code(200); // OK
            // Here you might typically generate and return a session token for subsequent API calls
            // For simplicity now, we just return success and user info.
        } else {
            // Password does not match
            $response = ['status' => 'error', 'message' => 'Invalid phone number or password.'];
            http_response_code(401); // Unauthorized
        }
    } else {
        // No user found with that phone number
        $response = ['status' => 'error', 'message' => 'Invalid phone number or password.'];
        http_response_code(401); // Unauthorized
    }
    mysqli_stmt_close($stmt);
} else {
    // SQL prepare statement error
    http_response_code(500); // Internal Server Error
    error_log("SQL prepare failed in student_login.php: " . mysqli_error($conn)); // Log error
    $response = ['status' => 'error', 'message' => 'An internal error occurred. Please try again later.'];
}

mysqli_close($conn);

// --- Output JSON Response ---
echo json_encode($response);
exit;

?>