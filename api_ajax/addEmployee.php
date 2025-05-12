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

// Validate required fields
$required_fields = ['name', 'phone', 'dob', 'gender', 'address', 'joining_date', 'role'];
$missing_fields = [];

foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    http_response_code(400); // Bad Request
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields: ' . implode(', ', $missing_fields)
    ]);
    exit();
}

// Photo and aadhar image are optional based on form
$has_photo = isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK;
$has_aadhar = isset($_FILES['aadhar_image']) && $_FILES['aadhar_image']['error'] === UPLOAD_ERR_OK;

// Validate image types if files are uploaded
$allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];

if ($has_photo && !in_array($_FILES['photo']['type'], $allowed_types)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid file type for profile photo. Only JPG, JPEG & PNG allowed'
    ]);
    exit();
}

if ($has_aadhar && !in_array($_FILES['aadhar_image']['type'], $allowed_types)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid file type for aadhar image. Only JPG, JPEG & PNG allowed'
    ]);
    exit();
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Generate unique employee UID
    $employee_uid = '';
    $max_attempts = 10;
    $attempt = 0;
    
    do {
        $employee_uid = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
        
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM employees WHERE emp_uid = ?");
        if (!$check_stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        
        $check_stmt->bind_param("s", $employee_uid);
        if (!$check_stmt->execute()) {
            throw new Exception('Failed to check UID: ' . $check_stmt->error);
        }
        
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();
        
        $attempt++;
        if ($attempt >= $max_attempts && $count > 0) {
            throw new Exception('Unable to generate unique employee ID after ' . $max_attempts . ' attempts');
        }
    } while ($count > 0);

    $domain = "https://".$_SERVER['HTTP_HOST']."/attendance/uid/?id=".md5($employee_uid);
    // Generate QR code
    $qrServerAPI = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode((string)$domain);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $qrServerAPI);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $qrImageData = curl_exec($ch);
    
    if ($qrImageData === false) {
        throw new Exception('QR code generation failed: ' . curl_error($ch));
    }
    
    curl_close($ch);
    
    $qrImageBlob = base64_encode($qrImageData);

    // Process uploaded images
    $profileImageData = null;
    $profileImageType = null;
    $aadharImageData = null;
    $aadharImageType = null;

    if ($has_photo) {
        $profileImageData = file_get_contents($_FILES['photo']['tmp_name']);
        if ($profileImageData === false) {
            throw new Exception('Failed to read profile photo');
        }
        $profileImageType = $_FILES['photo']['type'];
    }

    if ($has_aadhar) {
        $aadharImageData = file_get_contents($_FILES['aadhar_image']['tmp_name']);
        if ($aadharImageData === false) {
            throw new Exception('Failed to read aadhar image');
        }
        $aadharImageType = $_FILES['aadhar_image']['type'];
    }

    // Sanitize inputs
    $name = strip_tags(trim($_POST['name']));
    $phone = strip_tags(trim($_POST['phone']));
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $aadhar = isset($_POST['aadhar']) ? strip_tags(trim($_POST['aadhar'])) : '';
    $dob = strip_tags(trim($_POST['dob']));
    $gender = strip_tags(trim($_POST['gender']));
    $address = strip_tags(trim($_POST['address']));
    $joining_date = strip_tags(trim($_POST['joining_date']));
    $role = strip_tags(trim($_POST['role']));

    // Prepare and execute insert statement
    $stmt = $conn->prepare("
        INSERT INTO employees (
            emp_uid, name, phone, email, aadhar, dob, 
            gender, address, joining_date, role, 
            photo, photo_type, aadhar_image, aadhar_image_type,
            emp_att_qr
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }

    $stmt->bind_param("sssssssssssssss",
        $employee_uid,
        $name,
        $phone,
        $email,
        $aadhar,
        $dob,
        $gender,
        $address,
        $joining_date,
        $role,
        $profileImageData,
        $profileImageType,
        $aadharImageData,
        $aadharImageType,
        $qrImageBlob 
    );

    if (!$stmt->execute()) {
        throw new Exception('Failed to add employee: ' . $stmt->error);
    }

    $stmt->close();
    
    // Commit transaction
    $conn->commit();

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Employee added successfully',
        'employee_id' => $employee_uid
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn)) {
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
