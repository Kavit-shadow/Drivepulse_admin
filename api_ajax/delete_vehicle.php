<?php
include('../includes/authentication.php');
authenticationAdmin('../');
include("../config.php");

header('Content-Type: application/json');

function removeTable($conn, $tableName) {
    // Sanitize the table name to prevent SQL injection
    $tableName = mysqli_real_escape_string($conn, $tableName);

    // Prepare the SQL statement to drop the table
    $sql = "DROP TABLE IF EXISTS `$tableName`";

    // Execute the SQL query
    if (mysqli_query($conn, $sql)) {
        return [
            'status' => 'success',
            'message' => "Table '$tableName' has been dropped successfully."
        ];
    } else {
        return [
            'status' => 'error',
            'message' => "Error dropping table '$tableName': " . mysqli_error($conn)
        ];
    }
}

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Fetch the table name associated with the given ID
    $result = mysqli_query($conn, "SELECT data_base_table FROM vehicles WHERE id = '$id'");
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $tableName = $row['data_base_table'];

        // Delete the vehicle entry
        $query = "DELETE FROM vehicles WHERE id = '$id'";

        if (mysqli_query($conn, $query)) {
            // Attempt to remove the associated table
            $tableResponse = removeTable($conn, $tableName);
            $response['status'] = $tableResponse['status'];
            $response['message'] = $tableResponse['message'];
        } else {
            $response['message'] = 'Error deleting vehicle: ' . mysqli_error($conn);
        }
    } else {
        $response['message'] = 'No vehicle found with the given ID';
    }
} else {
    $response['message'] = 'Vehicle ID is required';
}

echo json_encode($response);
?>
