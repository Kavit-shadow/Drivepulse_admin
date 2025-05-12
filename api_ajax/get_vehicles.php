<?php
include("../config.php");

header('Content-Type: application/json');

$query = "SELECT * FROM `vehicles`";
$result = mysqli_query($conn, $query);

$vehicles = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
}

echo json_encode($vehicles);
?>
