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

// Validate employee ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid employee ID'
    ]);
    exit();
}

$employeeId = intval($_GET['id']);

try {
    // Get employee details including all required fields
    $sql = "SELECT id, emp_uid, emp_att_qr, name, phone, email, aadhar, dob, gender, role, joining_date, address,
            photo, photo_type, aadhar_image, aadhar_image_type,
            created_at, updated_at, leaving_date, rejoin_date
            FROM employees 
            WHERE id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    if ($row = $result->fetch_assoc()) {
        // Format dates
        $row['joining_date'] = date('Y-m-d', strtotime($row['joining_date']));
        $row['dob'] = date('Y-m-d', strtotime($row['dob']));
        $row['created_at'] = date('Y-m-d H:i:s', strtotime($row['created_at']));
        $row['updated_at'] = date('Y-m-d H:i:s', strtotime($row['updated_at']));
        $row['leaving_date'] = $row['leaving_date'] ? date('Y-m-d', strtotime($row['leaving_date'])) : null;
        $row['rejoin_date'] = $row['rejoin_date'] ? date('Y-m-d', strtotime($row['rejoin_date'])) : null;
        // Convert BLOB photo to base64
        if ($row['photo'] && $row['photo_type']) {
            $photoBase64 = base64_encode($row['photo']);
            $row['photo'] = "data:" . $row['photo_type'] . ";base64," . $photoBase64;
        } else {
            $row['photo'] = null;
        }

        // Convert BLOB aadhar photo to base64  
        if ($row['aadhar_image'] && $row['aadhar_image_type']) {
            $aadharPhotoBase64 = base64_encode($row['aadhar_image']);
            $row['aadhar_image'] = "data:" . $row['aadhar_image_type'] . ";base64," . $aadharPhotoBase64;
        } else {
            $row['aadhar_image'] = null;
        }

      
        $row['emp_att_qr'] = "data:image/png;base64," . $row['emp_att_qr'];


        // Capitalize role and gender
        $row['role'] = ucfirst(strtolower($row['role']));
        $row['gender'] = ucfirst(strtolower($row['gender']));

        // Remove binary data and type fields from response
        unset($row['photo_type']);
        unset($row['aadhar_image_type']);

       

        echo json_encode([  
            'success' => true,
            'employee' => $row
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Employee not found'
        ]);
    }

} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch employee details. Please try again later.',
        'error' => $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
?>
