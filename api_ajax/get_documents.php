<?php
include "../config.php";
include "../configWeb.php";

header('Content-Type: application/json');

if (!isset($_POST['cust_uid'])) {
    echo json_encode(['success' => false, 'message' => 'Customer ID is required']);
    exit;
}

$cust_uid = $_POST['cust_uid'];

// Get documents for the customer
$stmt = $conn->prepare("SELECT id, filename, filepath, upload_date FROM customer_documents WHERE cust_uid = ? ORDER BY upload_date DESC");
$stmt->bind_param("s", $cust_uid);
$stmt->execute();
$result = $stmt->get_result();

$documents = [];
while ($row = $result->fetch_assoc()) {
    $documents[] = [
        'id' => $row['id'],
        'filename' => $row['filename'],
        'url' => '../storage/uploads/customer_documents/' . $row['filepath'],
        'upload_date' => $row['upload_date']
    ];
}

$stmt->close();
echo json_encode($documents); 