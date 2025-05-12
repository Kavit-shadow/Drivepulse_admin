<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session before including authentication
session_start();

// Include authentication
include('../includes/authentication.php');

// Check if user is logged in
if (!isset($_SESSION['admin_name'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Authentication failed. Please log in.',
        'session' => $_SESSION
    ]);
    exit;
}

header('Content-Type: application/json');

// Check if customer UID is provided
if (!isset($_POST['cust_uid']) || empty($_POST['cust_uid'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Customer UID is required'
    ]);
    exit;
}

$custUid = $_POST['cust_uid'];

// Validate customer UID (alphanumeric only)
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $custUid)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid customer UID format'
    ]);
    exit;
}

// Path to customer documents
$documentsPath = '../storage/uploads/customer_documents/' . $custUid . '/';
$absolutePath = realpath($documentsPath);

// Check if directory exists
if (!file_exists($documentsPath) || !is_dir($documentsPath)) {
    echo json_encode([
        'success' => false,
        'message' => 'Customer documents directory not found',
        'path' => $documentsPath,
        'absolute_path' => $absolutePath,
        'exists' => file_exists($documentsPath),
        'is_dir' => is_dir($documentsPath)
    ]);
    exit;
}

// Scan directory for files
$files = [];
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];

try {
    $dirContents = scandir($documentsPath);
    
    if ($dirContents === false) {
        throw new Exception("Failed to scan directory: " . error_get_last()['message']);
    }

    foreach ($dirContents as $file) {
        // Skip . and .. directories and hidden files
        if ($file === '.' || $file === '..' || substr($file, 0, 1) === '.') {
            continue;
        }
        
        // Skip directories
        if (is_dir($documentsPath . $file)) {
            continue;
        }
        
        // Get file extension
        $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        
        // Check if file extension is allowed
        if (!in_array($fileExtension, $allowedExtensions)) {
            continue;
        }
        
        // Get file size and last modified time
        $fileSize = filesize($documentsPath . $file);
        $lastModified = filemtime($documentsPath . $file);
        
        // Add file to list
        $files[] = [
            'name' => $file,
            'size' => $fileSize,
            'last_modified' => $lastModified,
            'extension' => $fileExtension
        ];
    }

    // Sort files by last modified time (newest first)
    usort($files, function($a, $b) {
        return $b['last_modified'] - $a['last_modified'];
    });

    // Return JSON response
    echo json_encode([
        'success' => true,
        'files' => $files,
        'count' => count($files),
        'path' => $documentsPath,
        'absolute_path' => $absolutePath
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error scanning directory: ' . $e->getMessage(),
        'path' => $documentsPath,
        'absolute_path' => $absolutePath
    ]);
} 