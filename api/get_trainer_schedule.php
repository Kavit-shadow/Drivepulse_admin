<?php
// --- Get Trainer Schedule API Endpoint ---
// Location: /api/get_trainer_schedule.php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'GET') { http_response_code(405); echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']); exit; }

require_once __DIR__ . '/../config.php';

// --- Input Validation ---
if (!isset($_GET['emp_uid']) || empty(trim($_GET['emp_uid']))) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameter: emp_uid.']);
    exit;
}
$trainer_emp_uid = trim($_GET['emp_uid']);

// --- Database Connection Check ---
if (!$conn || $conn->connect_error) { http_response_code(500); error_log("DB connection failed: " . ($conn ? $conn->connect_error : mysqli_connect_error())); echo json_encode(['status' => 'error', 'message' => 'Database error.']); exit; }

// --- Fetch Trainer Name ---
$trainer_name = null;
$sql_trainer = "SELECT name FROM employees WHERE emp_uid = ? LIMIT 1";
$stmt_trainer = mysqli_prepare($conn, $sql_trainer);
if ($stmt_trainer) {
    mysqli_stmt_bind_param($stmt_trainer, "s", $trainer_emp_uid); mysqli_stmt_execute($stmt_trainer); $result_trainer = mysqli_stmt_get_result($stmt_trainer);
    if ($trainer_row = mysqli_fetch_assoc($result_trainer)) { $trainer_name = $trainer_row['name']; }
    mysqli_stmt_close($stmt_trainer);
} else { http_response_code(500); error_log("SQL fail (get trainer name): " . mysqli_error($conn)); echo json_encode(['status' => 'error', 'message' => 'DB error trainer lookup.']); mysqli_close($conn); exit; }
if ($trainer_name === null) { http_response_code(404); echo json_encode(['status' => 'error', 'message' => 'Trainer not found.']); mysqli_close($conn); exit; }

// --- Fetch Schedule Logic ---
$response = [];
$schedule_records = [];
date_default_timezone_set('Asia/Kolkata');
$current_date = date('Y-m-d');

// 1. Get list of vehicle tables
$sql_vehicles = "SELECT data_base_table, vehicle_name FROM vehicles"; // Get display name too
$result_vehicles = mysqli_query($conn, $sql_vehicles);

if ($result_vehicles) {
    while ($vehicle_row = mysqli_fetch_assoc($result_vehicles)) {
        $vehicle_table_name = $vehicle_row['data_base_table'];
        $vehicle_display_name = $vehicle_row['vehicle_name']; // e.g., "Swift"

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $vehicle_table_name)) { continue; } // Skip invalid table names

        // 2. Query each vehicle table for active slots for this trainer NAME today or in future
        // Using trainer name for matching, based on apparent schema usage
        $sql_schedule = "SELECT timeslots, name as student_name, phone as student_phone, start_date, end_date
                         FROM `$vehicle_table_name`
                         WHERE trainer = ?
                         AND status = 'active'
                         AND end_date >= ?
                         ORDER BY timeslots ASC";

        $stmt_schedule = mysqli_prepare($conn, $sql_schedule);

        if ($stmt_schedule) {
            mysqli_stmt_bind_param($stmt_schedule, "ss", $trainer_name, $current_date);
            mysqli_stmt_execute($stmt_schedule);
            $result_schedule = mysqli_stmt_get_result($stmt_schedule);

            while ($schedule_row = mysqli_fetch_assoc($result_schedule)) {
                 // Add vehicle display name for context
                 $schedule_row['vehicle'] = $vehicle_display_name;
                 // Format dates for potentially better display
                 $schedule_row['start_date_formatted'] = date('d M Y', strtotime($schedule_row['start_date']));
                 $schedule_row['end_date_formatted'] = date('d M Y', strtotime($schedule_row['end_date']));
                 $schedule_records[] = $schedule_row;
            }
            mysqli_stmt_close($stmt_schedule);
        } else { error_log("SQL prepare failed for table $vehicle_table_name: " . mysqli_error($conn)); }
    }
    mysqli_free_result($result_vehicles);

    // Sort all collected records by timeslot (needed as they come from different tables)
    usort($schedule_records, function($a, $b) {
         // Convert timeslot string to comparable time (e.g., 7:00am -> 700, 1:30pm -> 1330)
         $timeA = preg_replace('/[^\d:]/', '', strtolower(explode(' ', $a['timeslots'])[0]));
         $timeB = preg_replace('/[^\d:]/', '', strtolower(explode(' ', $b['timeslots'])[0]));
         $pmA = strpos(strtolower($a['timeslots']), 'pm') !== false;
         $pmB = strpos(strtolower($b['timeslots']), 'pm') !== false;
         list($hA, $mA) = explode(':', $timeA); list($hB, $mB) = explode(':', $timeB);
         $hA = intval($hA); $hB = intval($hB);
         if ($pmA && $hA != 12) $hA += 12; if (!$pmA && $hA == 12) $hA = 0; // Handle 12 AM/PM
         if ($pmB && $hB != 12) $hB += 12; if (!$pmB && $hB == 12) $hB = 0;
         $numericTimeA = $hA * 100 + intval($mA);
         $numericTimeB = $hB * 100 + intval($mB);
         return $numericTimeA <=> $numericTimeB; // Spaceship operator for comparison
    });

    $response = [ 'status' => 'success', 'schedule' => $schedule_records ];
    http_response_code(200);

} else { http_response_code(500); error_log("SQL query failed (get vehicles list): " . mysqli_error($conn)); $response = ['status' => 'error', 'message' => 'DB error fetching vehicle list.']; }

mysqli_close($conn);
echo json_encode($response);
exit;
?>