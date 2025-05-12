<?php
header("Content-Type: application/json");
include('../includes/authentication.php');
authenticationAdmin('../');
include('../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Prepare the SQL statement
        $query = "UPDATE customer_attendance SET time_out = time_in WHERE time_out IS NULL";
        
        // Execute the query
        $result = mysqli_query($conn, $query);
        
        // Check the affected rows
        $affectedRows = mysqli_affected_rows($conn);
        
        // Respond with success or no rows affected
        if ($affectedRows > 0) {
            echo json_encode(['success' => true, 'message' => "Successfully updated $affectedRows records."]);
        } else {
            echo json_encode(['success' => true, 'message' => 'No records were updated.']);
        }
    } catch (Exception $e) {
        // Handle errors
        http_response_code(500);
        echo json_encode(['error' => 'An error occurred while updating records: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'This request method is not allowed. Please use POST.']);
}
?>
