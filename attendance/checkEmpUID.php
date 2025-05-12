<?php
header('Content-Type: application/json');
require_once '../config.php';

$uid = isset($_POST['uid']) ? $_POST['uid'] : '';

if (empty($uid)) {
    echo json_encode([
        'success' => false,
        'message' => 'UID is required'
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM employees WHERE MD5(emp_uid) COLLATE utf8mb4_unicode_ci = ? COLLATE utf8mb4_unicode_ci");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Return response
    echo json_encode([
        'success' => true,
        'exists' => $row['count'] > 0,
        'message' => $row['count'] > 0 ? 'Employee found' : 'Not an employee'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error checking UID: ' . $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();




?>
