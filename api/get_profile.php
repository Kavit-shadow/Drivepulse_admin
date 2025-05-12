<?php
// --- Get Student Profile API Endpoint ---

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Adjust for production
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Ensure it's a GET request
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Only GET is allowed.']);
    exit;
}

// Include database configuration
require_once __DIR__ . '/../config.php';

// --- Input Validation ---
if (!isset($_GET['cust_uid']) || empty(trim($_GET['cust_uid']))) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameter: cust_uid.']);
    exit;
}

$cust_uid = trim($_GET['cust_uid']);

// --- Database Connection Check ---
if (!$conn) {
    http_response_code(500); // Internal Server Error
    error_log("Database connection failed in get_profile.php");
    echo json_encode(['status' => 'error', 'message' => 'Database connection error.']);
    exit;
}

// --- Fetch Profile Logic ---
$response = [];

// Select specific columns needed for the profile screen
// Added aliases for date/time for clarity
$sql = "SELECT
            id, cust_uid, name, email, phone, address,
            totalamount, paidamount, dueamount,
            days, timeslot, vehicle, newlicence AS rto_work,
            trainername, trainerphone,
            date AS registration_date, time AS registration_time,
            startedAT AS training_start_date, endedAT AS training_end_date,
            formfiller, payment_method IS NOT NULL AS is_app_user -- Check if app password is set
        FROM cust_details
        WHERE cust_uid = ?
        LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $cust_uid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($profile_data = mysqli_fetch_assoc($result)) {
        // Profile found
        $response = [
            'status' => 'success',
            'profile' => $profile_data
        ];
        http_response_code(200); // OK

        // Optional: You might want to format dates/numbers here before sending
        // Example: $profile_data['registration_date'] = date('d M Y', strtotime($profile_data['registration_date']));

    } else {
        // No user found with that cust_uid
        $response = ['status' => 'error', 'message' => 'Profile not found.'];
        http_response_code(404); // Not Found
    }
    mysqli_stmt_close($stmt);
} else {
    // SQL prepare statement error
    http_response_code(500); // Internal Server Error
    error_log("SQL prepare failed in get_profile.php: " . mysqli_error($conn));
    $response = ['status' => 'error', 'message' => 'An internal error occurred.'];
}

mysqli_close($conn);

// --- Output JSON Response ---
echo json_encode($response);
exit;

?>