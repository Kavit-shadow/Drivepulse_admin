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

// Check if the required parameters are provided in the request
if (isset($_GET['id']) && isset($_GET['car'])) {

    $id = $_GET['id'];
    $table = $_GET['car'];

    // Fetch the customer details from the database
    $selectDate = "SELECT * FROM `$table` WHERE id = $id";
    $result = mysqli_query($conn, $selectDate);

    if (!$result) {
        // Send a JSON response in case of database query error
        echo json_encode([
            'status' => 'error',
            'message' => 'Error fetching customer details: ' . mysqli_error($conn)
        ]);
        exit;
    }

    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $end_date = $row['end_date'];
        $name = $row['name'];
        $phone = $row['phone'];

        $newEndDate = date('Y-m-d', strtotime($end_date . '+1 day'));

        // Update the customer details in the database
        $update = "UPDATE `cust_details` SET endedAT = '$newEndDate', days = days + 1 WHERE name = '$name' AND phone='$phone'";
        mysqli_query($conn, $update);

        $update = "UPDATE `$table` SET end_date = '$newEndDate' WHERE id = $id";
        $result = mysqli_query($conn, $update);

        if (!$result) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error updating row: ' . mysqli_error($conn)
            ]);
            exit;
        } else {
            
            logActivity('admin_logs', $_SESSION['admin_name'], array(
                "What" => "Added one day to Customer in timetable",
                array(
                "customer_details" => array("name" => $name, "phone" => $phone),
                "changed_things" => array("car" => $_GET['car'], "date" => array("0ld" => $end_date, "new" => $newEndDate))
            )));
            
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Date updated successfully',
                'updated_date' => $newEndDate
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
        'message' => 'Missing required parameters: id, car, or route'
    ]);
}

?>
