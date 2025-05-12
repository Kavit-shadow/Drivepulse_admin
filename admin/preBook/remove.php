<?php

include('../../config.php');
session_start();


function logActivity($logType, $who, $activity)
{
    date_default_timezone_set('Asia/Kolkata');

    $logFolder = '../../logs/' . $logType;

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




header('Content-Type: application/json');


function removeData($conn, $id)
{

    $selectQuery = "SELECT * FROM pre_book_queue WHERE id = $id";
    $result = mysqli_query($conn, $selectQuery);
    if (mysqli_num_rows($result) === 0) {
        return [
            'status' => 'error',
            'message' => 'No record found with the given ID.'
        ];
    }

    $data = $result->fetch_assoc();
    if (mysqli_query($conn, "DELETE FROM `pre_book_queue` WHERE id = $id")) {
        logActivity('admin_logs', $_SESSION['admin_name'], "Customer ".$data['name']." phone: ".$data['phone'].", has been removed to time table successfully.");
        return [
            'status' => 'success',
            'message' => 'Customer has been removed from pre book successfully.'
        ];
    }

    return [
        'status' => 'error',
        'message' => 'Failed to removed the customer From Pre Book.'
    ];
}

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $response = removeData($conn, $id);
} else {
    $response = [
        'status' => 'error',
        'message' => 'ID parameter is missing.'
    ];
}

echo json_encode($response);
