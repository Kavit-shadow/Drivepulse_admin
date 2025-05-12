<?php
include "../config.php";
include "../configWeb.php";

header('Content-Type: application/json');

if (!isset($_POST['doc_id'])) {
    echo json_encode(['success' => false, 'message' => 'Document ID is required']);
    exit;
}

$doc_id = $_POST['doc_id'];

// Get document information before deletion
$stmt = $conn->prepare("SELECT filepath FROM customer_documents WHERE id = ?");
$stmt->bind_param("i", $doc_id);
$stmt->execute();
$result = $stmt->get_result();
$document = $result->fetch_assoc();
$stmt->close();

if (!$document) {
    echo json_encode(['success' => false, 'message' => 'Document not found']);
    exit;
}

// Delete file from storage
$filepath = '../storage/uploads/customer_documents/' . $document['filepath'];
if (file_exists($filepath)) {
    unlink($filepath);
    
    // Check if customer folder is empty and delete if it is
    $folderPath = dirname($filepath);
    if (is_dir($folderPath) && count(glob("$folderPath/*")) === 0) {
        rmdir($folderPath);
    }
}

// Delete record from database
$stmt = $conn->prepare("DELETE FROM customer_documents WHERE id = ?");
$stmt->bind_param("i", $doc_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Document deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete document']);
}

$stmt->close(); 