<?php
include('../includes/authentication.php');
authenticationAdmin('../');
header('Content-Type: application/json');

include("../config.php");

// Handle only GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'success' => false,
        'message' => 'Only GET method is allowed'
    ]);
    exit();
}

try {
    // Get active employees ordered by newest first
    $sql = "SELECT id, emp_uid, name, phone, email, role, joining_date, photo, photo_type FROM employees WHERE is_ex_employee = 0 ORDER BY id DESC";
            
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    $employees = [];

    while ($row = $result->fetch_assoc()) {
        // Format joining date
        $row['joining_date'] = date('Y-m-d', strtotime($row['joining_date']));

        // Convert BLOB photo to base64
        if ($row['photo'] && $row['photo_type']) {
            $photoBase64 = base64_encode($row['photo']);
            $photoSrc = "data:" . $row['photo_type'] . ";base64," . $photoBase64;
            $row['photo'] = $photoSrc;
        } else {
            $row['photo'] = '../../assets/Default_Profile.png';
        }

        // Capitalize role
        $row['role'] = ucfirst(strtolower($row['role']));

        // Only include needed fields
        $employee = [
            'id' => $row['id'],
            'emp_uid' => $row['emp_uid'],
            'name' => $row['name'],
            'phone' => $row['phone'],
            'email' => $row['email'], 
            'role' => $row['role'],
            'joining_date' => $row['joining_date'],
            'photo' => $row['photo']
        ];

        $employees[] = $employee;
    }

    if (count($employees) > 0) {
        echo json_encode([
            'success' => true,
            'employees' => $employees
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No employees found'
        ]);
    }

} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch employee data. Please try again later.',
        'error' => $e->getMessage()
    ]);
}

$conn->close();
?>
