<?php
include('../includes/authentication.php');
authenticationAdmin('../');
include("../config.php");
try {
    if (!isset($_POST['emp_id'])) {
        throw new Exception('Employee ID is required');
    }

    $emp_id = $_POST['emp_id'];
    // Query to check if employee exists in users_db
    $sql = "SELECT COUNT(*) as count FROM users_db WHERE emp_uid = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        // Get user details if account exists
        $sql = "SELECT username, name, permissions, time FROM users_db WHERE emp_uid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $emp_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $userDetails = $result->fetch_assoc();

        $timestamp = date('Y-m-d H:i:s', strtotime($userDetails['time']));

        echo json_encode([
            'hasAccess' => true,
            'username' => $userDetails['username'],
            'name' => $userDetails['name'],
            'permissions' => $userDetails['permissions'],
            'time' => $timestamp
        ]);
    } else {
        echo json_encode([
            'hasAccess' => false
        ]);
    }

} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to check software access. Please try again later.',
        'error' => $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
?>
