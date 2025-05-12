<?php

function authenticationAdmin($path = "../"){

$path .= 'config.php';

@include $path;

session_start();


// Old authentication code that searched directories
// if (!isset($_SESSION['admin_name'])) {

//     // $base_url = '/Billing_Software/';

//     // Define the file name to search for
//     $file_to_find = 'login_form.php';

//     $found = false;

//     // Loop to search for the file in parent directories
//     $current_dir = __DIR__;
//     while (!$found && $current_dir !== '/') {
//         $file_path = $current_dir . $base_url . $file_to_find;
//         if (file_exists($file_path)) {
//             header('Location: ' . $base_url . $file_to_find);
//             exit;
//         }

//         // Move up one directory
//         $current_dir = dirname($current_dir);
//     }


//     // header('location:../login_form.php');
//     // header('Location: ' . $base_url . 'login_form.php');
// }

// New authentication check that works on both local and hosted sites
if (!isset($_SESSION['admin_name'])) {
    // Get the protocol (http/https)
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    
    // Get the host (domain name)
    $host = $_SERVER['HTTP_HOST'];
    
    // Check if running locally or on hosted server
    $is_local = (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false);
    
    if ($is_local) {
        // Local environment - use base_url from config
        $redirect_url = $protocol . $host . $base_url . 'login_form.php';
    } else {
        // Hosted environment - construct path from document root
        $doc_root = $_SERVER['DOCUMENT_ROOT'];
        $current_path = dirname(__FILE__);
        $relative_path = str_replace($doc_root, '', $current_path);
        $redirect_url = $protocol . $host . dirname($relative_path) . '/login_form';
    }
    
    header('Location: ' . $redirect_url);
    exit();
}

}

?>