<?php
include('../includes/authenticationAdminOrStaffOrTrainer.php');
authenticationAdminOrStaffOrTrainer('../');


function logActivity($logType, $who, $activity)
{
    date_default_timezone_set('Asia/Kolkata');

    $logFolder = '../logs/' . $logType;

    if (!file_exists($logFolder)) {
        mkdir($logFolder, 0755, true);
    }

    $logFile = $logFolder . '/logs.json';

    // Read existing log entries from the file, or create an empty array if the file doesn't exist
    $existingLogs = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];

    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'who' => $who,
        'activity' => $activity,
    ];

    // Append the new log entry to the existing logs array
    array_unshift($existingLogs, $logEntry);
    // Save the updated logs array back to the file
    file_put_contents($logFile, json_encode($existingLogs, JSON_PRETTY_PRINT));
}

// Include database connection
include('../config.php');

// Get current date in Y-m-d format
$today = date('Y-m-d');

// Get current timestamp
$current_time = date('Y-m-d H:i:s');

// Calculate timestamp from 1 hour ago
$one_hour_ago = date('Y-m-d H:i:s', strtotime('-1 hour'));

// First check all records for null time_out from previous dates
$all_null_check = "SELECT ca.id, cd.name, cd.timeslot, ca.time_in, cd.trainername, cd.vehicle, 
                   DATEDIFF(CURDATE(), DATE(ca.time_in)) as days_ago
                   FROM customer_attendance ca
                   JOIN cust_details cd ON ca.cust_uid = cd.cust_uid 
                   WHERE ca.time_out IS NULL 
                   AND DATE(ca.time_in) < CURDATE()";

$null_check_stmt = $conn->prepare($all_null_check);
$null_check_stmt->execute();
$null_check_result = $null_check_stmt->get_result();

// Log any sessions with null time_out
while ($null_record = $null_check_result->fetch_assoc()) {
    $formatted_time = date('h:i A', strtotime($null_record['time_in']));
    $days_ago = $null_record['days_ago'];
    $day_text = $days_ago == 1 ? "yesterday" : $days_ago . " days ago";

    logActivity('admin_logs', "System", "Warning: Incomplete session found - " . $null_record['name'] .
        " checked in at " . $formatted_time . " " . $day_text .
        ". Training slot: " . $null_record['timeslot'] .
        ". Trainer: " . $null_record['trainername'] .
        ". Vehicle: " . $null_record['vehicle']);
}
$null_check_stmt->close();

// Check for any attendance records from today with null time_out that are overdue
$check_query = "SELECT ca.id, cd.name, cd.timeslot, ca.time_in, cd.trainername, cd.vehicle
                FROM customer_attendance ca
                JOIN cust_details cd ON ca.cust_uid = cd.cust_uid 
                WHERE DATE(ca.time_in) = CURDATE() 
                AND ca.time_out IS NULL
                AND TIME_TO_SEC(TIMEDIFF(NOW(), ca.time_in))/60 > 50";

$check_stmt = $conn->prepare($check_query);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

// Log any overdue sessions to admin log
while ($overdue = $check_result->fetch_assoc()) {
    $formatted_time = date('h:i A', strtotime($overdue['time_in']));
    logActivity('admin_logs', "System", "Alert: " . $overdue['name'] . " has not checked out since " . $formatted_time .
        ". Training slot: " . $overdue['timeslot'] .
        ". Trainer: " . $overdue['trainername'] .
        ". Vehicle: " . $overdue['vehicle']);
}
$check_stmt->close();

// Prepare SQL query to get live training sessions
$query = "SELECT 
    ca.id,
    ca.cust_id,
    cd.cust_uid,
    cd.name as customer_name,
    cd.phone,
    cd.vehicle,
    cd.trainername as instructor,
    cd.timeslot as time_slot,
    ca.time_in as start_time,
    TIMESTAMPDIFF(MINUTE, ca.time_in, NOW()) as duration
FROM customer_attendance ca
JOIN cust_details cd ON ca.cust_uid = cd.cust_uid
WHERE DATE(ca.time_in) = CURDATE()
AND ca.time_out IS NULL 
AND TIMESTAMPDIFF(MINUTE, ca.time_in, NOW()) <= 50
ORDER BY ca.time_in DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$trainings = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Format duration to show in minutes
        $duration = intval($row['duration']); // Convert to integer
        if ($duration <= 0) {
            $row['duration'] = 'Just started';
        } else {
            $row['duration'] = $duration . ' mins';
        }
        $row['start_time_2'] = htmlspecialchars($row['start_time'], ENT_QUOTES);
        // Format start time to readable format
        $row['start_time'] = date('h:i A', strtotime($row['start_time']));

        // Ensure all fields exist and are properly escaped for JS
        $row['id'] = htmlspecialchars($row['id'], ENT_QUOTES);
        $row['cust_id'] = htmlspecialchars($row['cust_id'], ENT_QUOTES);
        $row['customer_name'] = htmlspecialchars($row['customer_name'], ENT_QUOTES);
        $row['phone'] = htmlspecialchars($row['phone'], ENT_QUOTES);
        $row['vehicle'] = htmlspecialchars($row['vehicle'], ENT_QUOTES);
        $row['instructor'] = htmlspecialchars($row['instructor'], ENT_QUOTES);
        $row['time_slot'] = htmlspecialchars($row['time_slot'], ENT_QUOTES);


        $trainings[] = $row;
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($trainings);

$stmt->close();
$conn->close();
