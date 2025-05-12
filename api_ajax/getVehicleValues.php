<?php
header('Content-Type: application/json');
include('../config.php');

// Array to hold the response data
$response = [];

// SQL query to fetch all data_base_table entries from the vehicles table
$query = "SELECT data_base_table  FROM vehicles";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if ($result) {
    // Fetch all rows as an associative array
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    // Prepare the response data
    $response['status'] = 'success';
    $response['data'] = $data;
} else {
    // Handle query failure
    $response['status'] = 'error';
    $response['message'] = 'Database query failed: ' . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);

// Output the JSON response
echo json_encode($response);
?>
