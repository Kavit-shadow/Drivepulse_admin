<?php
include "../config.php";
include "../configWeb.php";
header('Content-Type: application/json');

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if required parameters are present
if (!isset($_POST['doc_id']) || !isset($_POST['new_name'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$doc_id = (int)$_POST['doc_id'];
$new_name = trim($_POST['new_name']);

// Validate new name
if (empty($new_name)) {
    echo json_encode(['success' => false, 'message' => 'New name cannot be empty']);
    exit;
}

// Get current document info
$query = "SELECT * FROM customer_documents WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doc_id);
$stmt->execute();
$result = $stmt->get_result();
$document = $result->fetch_assoc();

if (!$document) {
    echo json_encode(['success' => false, 'message' => 'Document not found']);
    exit;
}

// Get file extension from current filename
$current_ext = pathinfo($document['filename'], PATHINFO_EXTENSION);
$new_ext = pathinfo($new_name, PATHINFO_EXTENSION);

// Ensure new name has the same extension
if (strtolower($current_ext) !== strtolower($new_ext)) {
    $new_name .= '.' . $current_ext;
}

// Get the directory path and construct file paths
$baseDir = '../storage/uploads/customer_documents/';
$customerDir = $document['cust_uid'] . '/';
$oldFilePath = $baseDir . $document['filepath'];
$newFilePath = $baseDir . $customerDir . $new_name;

// Check if a file with the new name already exists
if (file_exists($newFilePath) && $newFilePath !== $oldFilePath) {
    echo json_encode(['success' => false, 'message' => 'A file with this name already exists']);
    exit;
}

// Ensure source file exists
if (!file_exists($oldFilePath)) {
    echo json_encode(['success' => false, 'message' => 'Source file not found']);
    exit;
}

// Rename the physical file
if (!rename($oldFilePath, $newFilePath)) {
    echo json_encode(['success' => false, 'message' => 'Error renaming file: ' . error_get_last()['message']]);
    exit;
}

// Update database with new filepath
$newRelativePath = $customerDir . $new_name;
$query = "UPDATE customer_documents SET filename = ?, filepath = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssi", $new_name, $newRelativePath, $doc_id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Document renamed successfully',
        'new_name' => $new_name,
        'new_path' => $newRelativePath
    ]);
} else {
    // If database update fails, try to revert the file rename
    rename($newFilePath, $oldFilePath);
    echo json_encode(['success' => false, 'message' => 'Error updating database: ' . $conn->error]);
}

$stmt->close();
$conn->close(); 