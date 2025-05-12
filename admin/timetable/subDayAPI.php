<?php

include('../../config.php');
session_start();

// Function to log activity
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

// Set the response header to JSON
header('Content-Type: application/json');

// Check if `id` and `car` parameters are set
if (isset($_GET['id']) && isset($_GET['car'])) {

    // Get the necessary parameters from the URL
    $id = $_GET['id'];
    $table = $_GET['car'];

    // Fetch the row from the database
    $selectDate = "SELECT * FROM `$table` WHERE id = $id";
    $result = mysqli_query($conn, $selectDate);

    if (!$result) {
        // Error with the SQL query
        echo json_encode(['status' => 'error', 'message' => 'Error fetching data: ' . mysqli_error($conn)]);
        exit;
    }

    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        // If no data was found for the given ID
        echo json_encode(['status' => 'error', 'message' => 'Record not found.']);
        exit;
    }

    // Get customer details
    $end_date = $row['end_date'];
    $name = $row['name'];
    $phone = $row['phone'];

    // Calculate the new end date (subtract one day)
    $newEndDate = date('Y-m-d', strtotime($end_date . '-1 day'));

    // Update the customer details in the `cust_details` table
    $updateCustDetails = "UPDATE `cust_details` SET endedAT = '$newEndDate' WHERE name = '$name' AND phone = '$phone'";
    if (!mysqli_query($conn, $updateCustDetails)) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating cust_details: ' . mysqli_error($conn)]);
        exit;
    }

    // Update the end date in the specific table
    $updateTable = "UPDATE `$table` SET end_date = '$newEndDate' WHERE id = $id";
    if (!mysqli_query($conn, $updateTable)) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating timetable: ' . mysqli_error($conn)]);
        exit;
    }

    // Log the activity
    logActivity('admin_logs', $_SESSION['admin_name'], array("What" => "Subtracted one day to Customer in timetable", array(
        "customer_details" => array("name" => $name, "phone" => $phone),
        "changed_things" => array("car" => $_GET['car'], "date" => array("0ld" => $end_date, "new" => $newEndDate))
    )));

    // Return success response
    echo json_encode(['status' => 'success', 'message' => 'Date updated successfully.', 'updated_date' => $newEndDate]);
} else {
    // If the required parameters are missing
    echo json_encode(['status' => 'error', 'message' => 'Invalid request. Missing required parameters.']);
}
