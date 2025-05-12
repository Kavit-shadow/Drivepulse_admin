<?php
include "../configWeb.php";
include "../config.php";

// Set headers for JSON response
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if file and customer ID are provided
if (!isset($_FILES['profile_picture']) || !isset($_POST['cust_uid'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$file = $_FILES['profile_picture'];
$cust_uid = $_POST['cust_uid'];

// Validate file
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Error uploading file']);
    exit;
}

// Validate file size (5MB max)
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'File too large']);
    exit;
}

// Validate file type
$allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type']);
    exit;
}

// Create customer directory if it doesn't exist
$upload_dir = "../storage/uploads/customer_documents/$cust_uid";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Process the image
$source_path = $file['tmp_name'];
$target_path = "$upload_dir/pfp.png";

// Get image type
$image_info = getimagesize($source_path);
$image_type = $image_info[2];

// Create image resource based on type
switch ($image_type) {
    case IMAGETYPE_JPEG:
        $source_image = imagecreatefromjpeg($source_path);
        break;
    case IMAGETYPE_PNG:
        $source_image = imagecreatefrompng($source_path);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Unsupported image type']);
        exit;
}

// Create a square crop of the image
$source_width = imagesx($source_image);
$source_height = imagesy($source_image);
$size = min($source_width, $source_height);

$target_image = imagecreatetruecolor(300, 300);
imagealphablending($target_image, false);
imagesavealpha($target_image, true);

// Fill with transparent background
$transparent = imagecolorallocatealpha($target_image, 255, 255, 255, 127);
imagefilledrectangle($target_image, 0, 0, 300, 300, $transparent);

// Calculate crop position
$x = ($source_width - $size) / 2;
$y = ($source_height - $size) / 2;

// Copy and resize the image
imagecopyresampled(
    $target_image,
    $source_image,
    0, 0,
    $x, $y,
    300, 300,
    $size, $size
);

// Save the processed image
if (imagepng($target_image, $target_path)) {
    // Clean up
    imagedestroy($source_image);
    imagedestroy($target_image);
    
    echo json_encode(['success' => true, 'message' => 'Profile picture updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error saving profile picture']);
} 