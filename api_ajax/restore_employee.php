<?php
include('../includes/authentication.php');
authenticationAdmin('../');
header('Content-Type: application/json');

include("../config.php");

// Handle only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'success' => false,
        'message' => 'Only POST method is allowed'
    ]);
    exit();
}

// Validate employee ID
if (!isset($_POST['employee_id']) || !is_numeric($_POST['employee_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid employee ID'
    ]);
    exit();
}

$employeeId = intval($_POST['employee_id']);

try {
    // Begin transaction
    $conn->begin_transaction();

    // Update employee status
    $sql = "UPDATE employees SET is_ex_employee = 0, rejoin_date = CURRENT_DATE WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employeeId);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to restore employee: " . $conn->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception("Employee not found or already active");
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Employee restored successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to restore employee. Please try again later.',
        'error' => $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
?>
