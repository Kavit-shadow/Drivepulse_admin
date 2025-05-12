<?php

// Set the appropriate headers for an API
header("Content-Type: application/json");

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only POST method is allowed."]);
    exit;
}

// Get POST data (assumes that the data is sent in JSON format)
$data = json_decode(file_get_contents('php://input'), true);


// Check if all required fields are present
if (!isset($data['id'], $data['name'], $data['email'], $data['phone'], $data['totalamount'], $data['paidamount'], $data['days'], $data['timeslot'], $data['vehicle'], $data['newlicence'], $data['trainername'], $data['trainerphone'], $data['formfiller'])) {
    // Build array of missing fields
    $missing = [];
    $required = ['id', 'name', 'email', 'phone', 'totalamount', 'paidamount', 'days', 'timeslot', 'vehicle', 'newlicence', 'trainername', 'trainerphone', 'formfiller'];

    foreach ($required as $field) {
        if (!isset($data[$field])) {
            $missing[] = $field;
        }
    }

    http_response_code(400); // Bad Request
    echo json_encode([
        "error" => "Missing required fields",
        "missing_fields" => $missing
    ]);
    exit;
}

// // Assign the data to variables
// $id = $data['id'];
// $name = $data['name'];
// $email = $data['email'];
// $phone = $data['phone'];
// $address = $data['address'];

// $totalA = $data['totalamount'];
// $paidA = $data['paidamount'];
// $dueA = $totalA - $paidA;
// $days = $data['days'];
// $timeSlot = $data['timeslot'];
// $vehicle = $data['vehicle'];
// $boolLicence = $data['newlicence'];
// $trainername = $data['trainername'];
// $trainerphone = $data['trainerphone'];
// $formfiller = $data['formfiller'];

// // Database connection
// // $conn = mysqli_connect("localhost", "root", "", "billing");
// include('../../config.php');




// ----- new code ------

include('../../config.php');

// Assign the data to variables
$id = mysqli_real_escape_string($conn, $data['id']);
$name = mysqli_real_escape_string($conn, $data['name']);
$email = mysqli_real_escape_string($conn, $data['email']);
$phone = mysqli_real_escape_string($conn, $data['phone']);
$address = mysqli_real_escape_string($conn, $data['address']);

$totalA = mysqli_real_escape_string($conn, $data['totalamount']);
$paidA = mysqli_real_escape_string($conn, $data['paidamount']);
$dueA = $totalA - $paidA;
$days = mysqli_real_escape_string($conn, $data['days']);
$timeSlot = mysqli_real_escape_string($conn, $data['timeslot']);
$vehicle = mysqli_real_escape_string($conn, $data['vehicle']);
$boolLicence = mysqli_real_escape_string($conn, $data['newlicence']);
$trainername = mysqli_real_escape_string($conn, $data['trainername']);
$trainerphone = mysqli_real_escape_string($conn, $data['trainerphone']);
$formfiller = mysqli_real_escape_string($conn, $data['formfiller']);
$paymentMethod = mysqli_real_escape_string($conn, $data['payment_method']);


// ----- new code ------


if (!$conn) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Database connection failed: " . mysqli_connect_error()]);
    exit;
}

// Check if phone already exists in cust_details for a different ID
$check_phone = "SELECT id FROM cust_details WHERE phone = $phone AND id != $id";
$check_result = mysqli_query($conn, $check_phone);

if (mysqli_num_rows($check_result) > 0) {
    http_response_code(400); // Bad Request
    echo json_encode([
        "error" => "Phone number already exists for another customer",
        "message" => "This phone number is already registered with a different customer"
    ]);
    exit;
}

// Get old phone number from ID
$get_old_phone = "SELECT phone FROM cust_details WHERE id = $id";
$old_phone_result = mysqli_query($conn, $get_old_phone);

if (!$old_phone_result) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to retrieve old phone number"]);
    exit;
}

$old_phone_row = mysqli_fetch_assoc($old_phone_result);
$old_phone = $old_phone_row['phone'];


// Get database table name based on vehicle
$tableQuery = "SELECT data_base_table FROM vehicles";
$tableResult = mysqli_query($conn, $tableQuery);



if ($tableResult) {
    while ($tableRow = mysqli_fetch_assoc($tableResult)) {
        $tableName = $tableRow['data_base_table'];

        // Check if phone exists in each table
        $phoneQuery = "SELECT id, phone FROM `$tableName` WHERE phone = $old_phone";
        $phoneResult = mysqli_query($conn, $phoneQuery);

        if ($phoneResult && mysqli_num_rows($phoneResult) > 0) {
            $phoneRow = mysqli_fetch_assoc($phoneResult);
            $tableId = $phoneRow['id'];
            $currentTable = $tableName;

            $updateTable = "UPDATE `$currentTable` SET 
                `name` = '$name',
                `phone` = $phone
                WHERE `id` = $tableId";
            mysqli_query($conn, $updateTable);
        }
    }
}

// Check and update pre_book_queue table
$preBookQuery = "SELECT id FROM pre_book_queue WHERE phone = $old_phone";
$preBookResult = mysqli_query($conn, $preBookQuery);

if ($preBookResult && mysqli_num_rows($preBookResult) > 0) {
    while ($preBookRow = mysqli_fetch_assoc($preBookResult)) {
        $preBookId = $preBookRow['id'];

        $updatePreBook = "UPDATE pre_book_queue SET 
            `name` = '$name',
            `phone` = $phone
            WHERE `id` = $preBookId";
        mysqli_query($conn, $updatePreBook);
    }
}



// Update query
$update = "UPDATE `cust_details` SET 
    `name` = '$name',
    `email` = '$email',
    `phone` = $phone,
    `address` = '$address',
    `totalamount` = '$totalA',
    `paidamount` = '$paidA',
    `dueamount` = '$dueA',
    `days` = '$days',
    `timeslot` = '$timeSlot',
    `vehicle` = '$vehicle',
    `newlicence` = '$boolLicence',
    `trainername` = '$trainername',
    `trainerphone` = '$trainerphone',
    `formfiller` = '$formfiller',
    `payment_method` = '$paymentMethod'
    WHERE `id` = $id";



$result = mysqli_query($conn, $update);

if (!$result) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Error updating row: " . mysqli_error($conn)]);
    exit;
}





// If update is successful, return success message
http_response_code(200); // OK
echo json_encode(["message" => "Profile updated successfully.", "phone" => $phone]);

// Close database connection
mysqli_close($conn);
