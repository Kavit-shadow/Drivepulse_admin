<?php

include('../../config.php');
session_start();
header('Content-Type: application/json');
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

function updateTimeTable($conn, $id, $table)
{
    $update2 = "UPDATE `$table` SET name='', phone='', vehicle='', trainer='', start_date='', end_date='', status='empty' WHERE id = $id";
    if (mysqli_query($conn, $update2)) {
        return true;
    }
    return false;
}


if (isset($_GET['id']) && isset($_GET['car'])) {

    $id = $_GET['id'];
    $table = $_GET['car'];


    $selectDate = "SELECT * FROM `$table` WHERE id = $id";
    $result = mysqli_query($conn, $selectDate);

    if (!$result) {

        echo json_encode([
            'status' => 'error',
            'message' => 'Error fetching customer details: ' . mysqli_error($conn)
        ]);
        exit;
    }

    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $name = $row['name'];
        $phone = $row['phone'];




        if (!updateTimeTable($conn, $id, $table)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error updating row: ' . mysqli_error($conn)
            ]);
            exit;
        } else {

            logActivity('admin_logs', $_SESSION['admin_name'], "Removed Customer from timetable. name: $name ,phone: $phone.");


            echo json_encode([
                'status' => 'success',
                'message' => 'Customer Removed successfully',
                'name' =>  $name
            ]);
        }
    } else {

        echo json_encode([
            'status' => 'error',
            'message' => 'No customer data found for the given ID'
        ]);
    }
} else {

    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required parameters: id, or car'
    ]);
}
