<?php
include('../includes/authentication.php');
authenticationAdmin('../');
include('../config.php');

header('Content-Type: application/json');

$response = array(
    'success' => false,
    'message' => ''
);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $employeeId = mysqli_real_escape_string($conn, $_POST['id']);
    
    // First check if employee exists and is not already inactive
    $checkSql = "SELECT is_ex_employee FROM employees WHERE id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $employeeId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        $response['message'] = 'Employee not found';
    } else {
        $employee = $result->fetch_assoc();
        if ($employee['is_ex_employee'] === 1) {
            $response['message'] = 'Employee is already marked as ex-employee';
        } else {
            // Get employee's emp_uid first
            $getEmpUidSql = "SELECT emp_uid FROM employees WHERE id = ?";
            $getEmpUidStmt = $conn->prepare($getEmpUidSql);
            $getEmpUidStmt->bind_param("i", $employeeId);
            $getEmpUidStmt->execute();
            $empUidResult = $getEmpUidStmt->get_result();
            $empData = $empUidResult->fetch_assoc();
            $emp_uid = $empData['emp_uid'];
            $getEmpUidStmt->close();

            // Delete user account if exists
            $deleteUserSql = "DELETE FROM users_db WHERE emp_uid = ?";
            $deleteUserStmt = $conn->prepare($deleteUserSql);
            $deleteUserStmt->bind_param("s", $emp_uid);
            $deleteUserStmt->execute();
            $deleteUserStmt->close();

            // Update employee status to ex-employee
            $updateSql = "UPDATE employees SET is_ex_employee = 1, leaving_date = CURRENT_DATE() WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("i", $employeeId);
            
            if ($updateStmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Employee marked as ex-employee and user account removed successfully';
            } else {
                $response['message'] = 'Database error: ' . $conn->error;
            }
            $updateStmt->close();
        }
    }
    $checkStmt->close();
} else {
    $response['message'] = 'Invalid request method or missing employee ID';
}

$conn->close();
echo json_encode($response);
?>
