<?php

include("../config.php");


$phone = $_GET["phone"];


$sql = "SELECT * FROM cust_details WHERE phone = '$phone'";
$result = mysqli_query($conn,$sql);


if ($result->num_rows > 0) {
    echo json_encode(array('exists' => true, 'message' => 'Phone Number already exist!'));
} else {
    echo json_encode(array('exists' => false, 'message' => '' ));
}


$conn->close();
?>