<?php
include('../includes/authentication.php');
authenticationAdmin('../');

header('Content-Type: application/json');
include("../config.php");

$response = ['status' => 'error', 'message' => ''];

function idExists($conn, $id) {
    $query = "SELECT * FROM vehicles WHERE data_base_table = '$id'";
    $result = mysqli_query($conn, $query);
    return mysqli_num_rows($result) > 0;
}

function twoWheelerExists($conn) {
    $query = "SELECT COUNT(*) as count FROM vehicles WHERE category = '2-wheel'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] > 0;
}

function generateUniqueCarId($conn) {
    do {
        $prefix = 'car_';
        $randomNumber = mt_rand(1000, 9999);
        $newId = $prefix . $randomNumber;
    } while (idExists($conn, $newId));
    return $newId;
}

function generateTimeSlots($category) {
    $timeSlots = [];
    $startTime = strtotime('7:00');
    $endTime = strtotime('20:00'); // 8:00 PM
    
    $interval = ($category === '4-wheel') ? 30 * 60 : 45 * 60; // 30 mins for 4-wheel, 45 mins for 2-wheel
    
    for ($time = $startTime; $time < $endTime; $time += $interval) {
        $slotStart = date('g:ia', $time);
        $slotEnd = date('g:ia', $time + $interval);
        $timeSlots[] = "$slotStart to $slotEnd";
    }
    
    return $timeSlots;
}

function createTableIntoDB($conn, $vehicleTableName, $category) {
    $timeSlots = generateTimeSlots($category);
    
    // Create table query
    $createTableQuery = "
    CREATE TABLE IF NOT EXISTS `$vehicleTableName` (
        `id` int NOT NULL AUTO_INCREMENT,
        `timeslots` varchar(256) NOT NULL,
        `name` varchar(256) NOT NULL,
        `phone` varchar(256) NOT NULL,
        `vehicle` varchar(256) NOT NULL,
        `trainer` varchar(256) NOT NULL,
        `start_date` date NOT NULL,
        `end_date` date NOT NULL,
        `status` varchar(56) NOT NULL DEFAULT 'empty',
        PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
    
    // Execute create table query first
    if (!$conn->query($createTableQuery)) {
        return false;
    }
    
    // Insert time slots query
    $insertQuery = "INSERT INTO `$vehicleTableName` (`timeslots`, `name`, `phone`, `vehicle`, `trainer`, `start_date`, `end_date`, `status`) VALUES ";
    $values = [];
    
    foreach ($timeSlots as $slot) {
        $values[] = "('$slot', '', '', '', '', '0000-00-00', '0000-00-00', 'empty')";
    }
    
    $insertQuery .= implode(',', $values) . ';';
    
    // Execute insert query separately
    return $conn->query($insertQuery);
}

if (isset($_POST['vehicle-name']) && isset($_POST['category'])) {
    $vehicleName = mysqli_real_escape_string($conn, $_POST['vehicle-name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    
    if (!in_array($category, ['2-wheel', '4-wheel'])) {
        $response['message'] = 'Invalid vehicle category';
        echo json_encode($response);
        exit;
    }
    
    // Check if trying to add a 2-wheeler when one already exists
    if ($category === '2-wheel' && twoWheelerExists($conn)) {
        $response['message'] = 'Only one two-wheeler vehicle is allowed in the system';
        echo json_encode($response);
        exit;
    }
    
    $tableNameDB = generateUniqueCarId($conn);
    $query = "INSERT INTO vehicles (category, vehicle_name, data_base_table, created_at) 
              VALUES ('$category', '$vehicleName', '$tableNameDB', current_timestamp())";
    
    if (mysqli_query($conn, $query)) {
        if (createTableIntoDB($conn, $tableNameDB, $category)) {
            $response['status'] = 'success';
            $response['message'] = 'Vehicle added successfully';
        } else {
            $response['message'] = 'Error creating time slots: ' . mysqli_error($conn);
        }
    } else {
        $response['message'] = 'Error adding vehicle: ' . mysqli_error($conn);
    }
} else {
    $response['message'] = 'Vehicle name and category are required';
}

echo json_encode($response);
?>