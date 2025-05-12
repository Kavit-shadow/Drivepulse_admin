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

function getTableByCarName($conn, $carName)
{
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `data_base_table` FROM `vehicles` WHERE vehicle_name = '$carName'"));
    if ($row) {
        return $row["data_base_table"];
    }
    return null;
}

function checkStatusIsEmpty($conn, $table, $timeslot): bool
{   
  
   
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status FROM $table WHERE timeslots = '$timeslot'"));
    $status = $row['status'];
   
    if ($status === "empty") {
        return true;
    }
    return false;
}

function getVehFromCust($conn, $phone, $name)
{
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT vehicle FROM cust_details WHERE phone = $phone AND name = '$name'"));
    return $row['vehicle'];
}

function moveDataToTable($conn, $phone, $name, $sDate, $eDate, $timeSlot, $tainer, $vehFormCust, $table, $id): bool
{
    $result = mysqli_query($conn, "UPDATE $table SET name='$name', phone='$phone', vehicle='$vehFormCust', trainer='$tainer',  start_date='$sDate', end_date='$eDate', status='active' WHERE timeslot = '$timeSlot'");
    if ($result) {

        if (mysqli_query($conn, "DELETE FROM `pre_book_queue` WHERE id = $id")) {
            return true;
        }
    }

    return false;
}

function moveData($conn, $id)
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
    $table = getTableByCarName($conn, $data['vehicle']);
    if ($table == null) {
        return [
            'status' => 'error',
            'message' => 'Failed to find vehicle.'
        ];
    }

    if (checkStatusIsEmpty($conn, $table, $data['timeslot'])) {

        $vehFormCust = getVehFromCust($conn, $data['phone'], $data['name']);
        if (moveDataToTable($conn, $data['phone'], $data['name'], $data['start_date'], $data['end_date'], $data['timeslot'], $data['trainer'], $vehFormCust, $table, $id)) {

            logActivity('admin_logs', $_SESSION['admin_name'], "Customer ".$data['name']." phone: ".$data['phone'].", has been moved to time table successfully.");
            return [
                'status' => 'success',
                'message' => 'Customer has been moved to time table successfully.'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to moved customer.'
            ];
        }
    } else {
        return [
            'status' => 'error',
            'message' => 'Timeslot is not Empty'
        ];
    }
}

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $response = moveData($conn, $id);
} else {
    $response = [
        'status' => 'error',
        'message' => 'ID parameter is missing.'
    ];
}

echo json_encode($response);
