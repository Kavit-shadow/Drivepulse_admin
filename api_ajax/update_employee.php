<?php
include('../includes/authentication.php');
authenticationAdmin('../');
header('Content-Type: application/json');

include("../config.php");

try {
    // Validate required fields
    if (!isset($_POST['id']) || !isset($_POST['name']) || !isset($_POST['phone'])) {
        throw new Exception('Required fields are missing');
    }

    $id = $_POST['id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'] ?? '';
    $aadhar = $_POST['aadhar'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $role = $_POST['role'] ?? '';
    $joining_date = $_POST['joining_date'] ?? '';
    $address = $_POST['address'] ?? '';

    // If role is provided, update permissions in users_db
    if (!empty($role)) {
        // Get the corresponding permissions for the role
        $permissions = '';
        switch(strtolower($role)) {
            case 'admin':
                $permissions = 'admin';
                break;
            case 'staff':
                $permissions = 'staff'; 
                break;
            case 'trainer':
                $permissions = 'trainer';
                break;
            default:
                $permissions = 'staff';
        }

        // Update permissions in users_db for this employee
        $sql = "UPDATE users_db SET permissions = ? WHERE emp_uid = (SELECT emp_uid FROM employees WHERE id = ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("si", $permissions, $id);
        if (!$stmt->execute()) {
            throw new Exception('Failed to update permissions: ' . $stmt->error);
        }
    }
    // Start transaction
    $conn->begin_transaction();

    // Update basic employee information
    $sql = "UPDATE employees SET 
            name = ?, phone = ?, email = ?, aadhar = ?,
            dob = ?, gender = ?, role = ?, joining_date = ?, 
            address = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?";
            
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }

    $stmt->bind_param("sssssssssi", 
        $name, $phone, $email, $aadhar,
        $dob, $gender, $role, $joining_date,
        $address, $id
    );

    if (!$stmt->execute()) {
        throw new Exception('Failed to update employee: ' . $stmt->error);
    }

    // Handle photo upload if provided
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($_FILES['photo']['type'], $allowed_types)) {
            throw new Exception('Invalid file type for profile photo. Only JPG, JPEG & PNG allowed');
        }

        $photo_data = file_get_contents($_FILES['photo']['tmp_name']);
        $photo_type = $_FILES['photo']['type'];
        
        if ($photo_data !== false) {
            $sql = "UPDATE employees SET photo = ?, photo_type = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmt->bind_param("ssi", $photo_data, $photo_type, $id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to update photo: ' . $stmt->error);
            }
        }
    }

    // Handle aadhar image upload if provided
    if (isset($_FILES['aadhar_image']) && $_FILES['aadhar_image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($_FILES['aadhar_image']['type'], $allowed_types)) {
            throw new Exception('Invalid file type for aadhar image. Only JPG, JPEG & PNG allowed');
        }

        $aadhar_data = file_get_contents($_FILES['aadhar_image']['tmp_name']);
        $aadhar_type = $_FILES['aadhar_image']['type'];
        
        if ($aadhar_data !== false) {
            $sql = "UPDATE employees SET aadhar_image = ?, aadhar_image_type = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmt->bind_param("ssi", $aadhar_data, $aadhar_type, $id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to update aadhar image: ' . $stmt->error);
            }
        }
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Employee updated successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn) && !$conn->connect_errno) {
        $conn->rollback();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
