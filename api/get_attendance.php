<?php
// --- Get Student Attendance API Endpoint (Corrected - v5 Final) ---

ini_set('display_errors', 0); // Turn off error display for production simulation
ini_set('display_startup_errors', 0);
error_reporting(0); // Report no errors directly to output

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
if (!$conn || $conn->connect_error) {
    http_response_code(500); // Internal Server Error
    $error_msg = $conn ? $conn->connect_error : mysqli_connect_error();
    error_log("Database connection failed in get_attendance.php: " . $error_msg);
    echo json_encode(['status' => 'error', 'message' => 'Database connection error.']);
    exit;
}

// --- Fetch Attendance Logic ---
$response = [];
$attendance_records = [];

// Select columns relevant for displaying attendance history in the app
// Corrected SQL: Removed 'mark_type' as it doesn't exist in the user's actual table.
$sql = "SELECT
            date,
            attendance_time,
            trainer_name,
            vehicle_name
            
        FROM customer_attendance
        WHERE cust_uid = ?
        ORDER BY date DESC, attendance_time DESC";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $cust_uid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = $result->fetch_assoc()) {
        // --- Improved Date/Time Formatting ---
        $display_datetime = '';
        if (!empty($row['date'])) {
             $date_part = $row['date'];
             $time_part = !empty($row['attendance_time']) ? $row['attendance_time'] : '00:00:00'; // Default time if missing

             // Combine date and time for strtotime
             $datetime_string = $date_part . ' ' . $time_part;
             $ts = strtotime($datetime_string);

             if ($ts !== false) {
                 // Format if timestamp is valid
                 $display_datetime = date('d M Y, H:i A', $ts);
             } else {
                 // Fallback if combined string is invalid
                 $display_datetime = $row['date']; // Just show date
                 error_log("Failed to parse datetime string: " . $datetime_string . " for cust_uid: " . $cust_uid);
             }
        } else {
             // Fallback if date is missing
             $display_datetime = 'Invalid Date';
        }
        $row['display_datetime'] = $display_datetime;
        // Optionally remove original fields if not needed by Flutter
        // unset($row['date']);--
        // unset($row['attendance_time']);
        // --- End Formatting ---

        $attendance_records[] = $row;
    }
    mysqli_free_result($result);
    mysqli_stmt_close($stmt);

    $response = [ 'status' => 'success', 'attendance' => $attendance_records ];
    http_response_code(200);

}
else {
    // SQL prepare statement error
    http_response_code(500);
    error_log("SQL prepare failed in get_attendance.php: " . mysqli_error($conn) . " SQL: " . $sql);
    $response = ['status' => 'error', 'message' => 'An internal error occurred while preparing to fetch attendance.'];
}

mysqli_close($conn);

// --- Output JSON Response ---
echo json_encode($response);
exit;

?>