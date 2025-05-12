<?php


$server = "localhost";
$username = "root";
$password = "";
$database = "demo_db";

// $server = "localhost";
// $username = "u511129607_ayushx309";
// $password = "@Bobby2005123@";
// $database = "u511129607_pmds";

// $server = "localhost";
// $username = "u511129607_ayush";
// $password = "@Bobby2005123@";
// $database = "u511129607_development";

global $conn;
$conn = mysqli_connect($server, $username, $password, $database);
mysqli_set_charset($conn, "utf8mb4");

$base_url = __DIR__;
$last_folder = basename($base_url);
$base_url = '/'.$last_folder.'/';


?>