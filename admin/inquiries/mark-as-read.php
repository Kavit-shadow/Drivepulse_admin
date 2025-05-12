<?php
header('Content-Type: application/json');
include('../../config.php');

// Initialize response array
$response = [
    'status' => 'error',
    'message' => ''
];

try {
    // Validate input
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        throw new Exception('Inquiry ID is required');
    }

    $id = (int)$_POST['id'];

    // Prepare and execute update query
    $query = "UPDATE booking_inquiries SET is_read = 1 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'i', $id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to execute statement: ' . mysqli_stmt_error($stmt));
    }

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $response['status'] = 'success';
        $response['message'] = 'Inquiry marked as read successfully';
    } else {
        throw new Exception('No inquiry found with the given ID');
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(500);
} finally {
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    if (isset($conn)) {
        mysqli_close($conn);
    }
}

// Send JSON response
echo json_encode($response);
?>
