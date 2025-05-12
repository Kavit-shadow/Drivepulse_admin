<?php

include('../../../includes/authentication.php');
authenticationAdmin('../../../');
// $conn = mysqli_connect("localhost", "root", "", "billing");
include('../../../config.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT COUNT(email) as email_count FROM cust_details";
$result = $conn->query($sql);

$emailCount = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $emailCount = $row['email_count'];
}

echo json_encode(['count' => 3]);

$conn->close();
?>
