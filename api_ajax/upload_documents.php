<?php
include "../config.php";
include "../configWeb.php";

header('Content-Type: application/json');

// Check if files were uploaded
if (!isset($_FILES['license_docs']) || empty($_FILES['license_docs']['name'][0])) {
    echo json_encode(['success' => false, 'message' => 'No files uploaded']);
    exit;
}

// Check if customer ID is provided
if (!isset($_POST['cust_uid'])) {
    echo json_encode(['success' => false, 'message' => 'Customer ID is required']);
    exit;
}

$cust_uid = $_POST['cust_uid'];
$baseUploadDir = '../storage/uploads/customer_documents/';
$customerDir = $baseUploadDir . $cust_uid . '/';

// Create base upload directory if it doesn't exist
if (!file_exists($baseUploadDir)) {
    mkdir($baseUploadDir, 0777, true);
}

// Create customer-specific directory if it doesn't exist
if (!file_exists($customerDir)) {
    mkdir($customerDir, 0777, true);
}

$response = ['success' => true, 'files' => []];
$allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
$maxFileSize = 5 * 1024 * 1024; // 5MB

// Process each uploaded file
foreach ($_FILES['license_docs']['tmp_name'] as $key => $tmp_name) {
    $file = [
        'name' => $_FILES['license_docs']['name'][$key],
        'type' => $_FILES['license_docs']['type'][$key],
        'tmp_name' => $tmp_name,
        'error' => $_FILES['license_docs']['error'][$key],
        'size' => $_FILES['license_docs']['size'][$key]
    ];

    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $response['files'][] = [
            'name' => $file['name'],
            'success' => false,
            'message' => 'Upload failed'
        ];
        continue;
    }

    // Check file type
    if (!in_array($file['type'], $allowedTypes)) {
        $response['files'][] = [
            'name' => $file['name'],
            'success' => false,
            'message' => 'Invalid file type'
        ];
        continue;
    }

    // Check file size
    if ($file['size'] > $maxFileSize) {
        $response['files'][] = [
            'name' => $file['name'],
            'success' => false,
            'message' => 'File too large (max 5MB)'
        ];
        continue;
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFilename = uniqid() . '.' . $extension;
    $relativePath = $cust_uid . '/' . $newFilename;
    $targetPath = $customerDir . $newFilename;

    // Move file to upload directory
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Insert file information into database
        $stmt = $conn->prepare("INSERT INTO customer_documents (cust_uid, filename, filepath, upload_date) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $cust_uid, $file['name'], $relativePath);
        
        if ($stmt->execute()) {
            $response['files'][] = [
                'name' => $file['name'],
                'success' => true,
                'message' => 'Upload successful'
            ];
        } else {
            $response['files'][] = [
                'name' => $file['name'],
                'success' => false,
                'message' => 'Database error'
            ];
            // Remove uploaded file if database insert fails
            unlink($targetPath);
        }
        $stmt->close();
    } else {
        $response['files'][] = [
            'name' => $file['name'],
            'success' => false,
            'message' => 'Failed to move uploaded file'
        ];
    }
}

echo json_encode($response); 