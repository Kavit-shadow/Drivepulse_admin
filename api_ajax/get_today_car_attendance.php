<?php
include('../includes/authenticationAdminOrStaffOrTrainer.php');
authenticationAdminOrStaffOrTrainer('../');
include '../config.php';


// Set the API response headers
header('Content-Type: application/json');

// Check if the car table name is provided
if (!isset($_POST['car_table'])) {
    http_response_code(400);
    echo json_encode(array('error' => 'Car table name is required'));
    exit;
}

// Get the car table name
$car_table = $_POST['car_table'];

// Get all customer phone numbers from the car table
$sql = "SELECT * FROM $car_table";
$result = $conn->query($sql);

// Initialize the response array
$response = array();

if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        // Get the customer data info with the phone number
        // $phone = $row['phone'] ? $row['phone'] : null;
        $phone = $row['phone'];
        $name = $row['name'];
        $vehicle = $row['vehicle'];
        $trainer = $row['trainer'];
        $start_date = $row['start_date'];
        $end_date = $row['end_date'];
        $time_slot = $row['timeslots'];
        $status = $row['status'];
        $id = $row['id'];

        // Check if the customer has filled attendance or not using cust_id and cust_uid
        if ($status == 'active') {
            $sql_cust_details = "SELECT id, cust_uid FROM cust_details WHERE phone = '$phone'";
            $cust_details_result = $conn->query($sql_cust_details);

            if ($cust_details_result->num_rows > 0) {
                $cust_details_row = $cust_details_result->fetch_assoc();
                $cust_id = $cust_details_row['id'];
                $cust_uid = $cust_details_row['cust_uid'];

                $sql_attendance = "SELECT * FROM customer_attendance WHERE cust_id = $cust_id AND cust_uid = '$cust_uid' AND date = CURDATE()";
                $attendance_result = $conn->query($sql_attendance);

                if ($attendance_result->num_rows > 0) {
                    $attendance_status = true;
                } else {
                    $attendance_status = false;
                }
            } else {
                http_response_code(400);
                echo json_encode(array('error' => 'Customer details not found'));
                exit;
            }
        } else {
            $attendance_status = null; // Assuming null for inactive customers
        }

        // Add the customer data to the response array
        $response[] = array(
            'id' => $id,
            'Time Slots' => $time_slot,
            'Name' => $name,
            'Phone' => $phone,
            'Vehicle' => $vehicle,
            'Trainer' => $trainer,
            'Start Date' => $start_date,
            'End Date' => $end_date,
            'Status' => $status,
            'Attendance Status' => $attendance_status ? 'Present' : ($status == 'empty' ? 'N/A' : 'Absent'),
            'att_status' => $attendance_status
        );
    }
} else {
    $response['error'] = 'No customers found in the car table';
}

// Close the database connection
$conn->close();

// Output the response
echo json_encode($response);
