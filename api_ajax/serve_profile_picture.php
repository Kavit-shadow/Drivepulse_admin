<?php
// This script serves profile pictures with proper cache control headers

// Get the customer ID from the query string
if (!isset($_GET['cust_uid'])) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

$cust_uid = $_GET['cust_uid'];

// Define the path to the profile picture
$pfp_path = "../storage/uploads/customer_documents/$cust_uid/pfp.png";

// Check if the file exists
if (!file_exists($pfp_path)) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

// Set cache control headers to prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Set the content type
header("Content-Type: image/png");

// Output the file
readfile($pfp_path);
exit; 