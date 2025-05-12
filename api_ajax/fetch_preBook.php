<?php

include("../config.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $car = $_GET['car'];
    $timeSlot = $_GET['timeSlot'];


    // $car = "ALL";
    // $timeSlot = "ALL";

    if ($timeSlot === "ALL" && $car === "ALL") {
        $sql = "SELECT * FROM pre_book_queue";
    } elseif ($timeSlot !== "ALL" && $car === "ALL") {
        $sql = "SELECT * FROM pre_book_queue WHERE timeslot = '$timeSlot'";
    } elseif ($timeSlot === "ALL" && $car !== "ALL") {
        $sql = "SELECT * FROM pre_book_queue WHERE vehicle = '$car'";
    } elseif ($timeSlot !== "ALL" && $car !== "ALL") {
        $sql = "SELECT * FROM pre_book_queue WHERE vehicle = '$car' AND timeslot = '$timeSlot'";
    } else {
        $sql = "SELECT * FROM pre_book_queue";
    }

    $result = mysqli_query($conn, $sql);


    if (mysqli_num_rows($result) > 0) {

        while ($row = $result->fetch_assoc()) {
            echo
            "<tr><td data-cell='Priority' >" . $row["priority"] . "</td><td data-cell='Timeslot' >" . $row["timeslot"] . "</td><td data-cell='Name' >" . $row["name"] . "</td><td data-cell='Phone' >" . $row["phone"] . "</td><td data-cell='Vehicle' >" . $row["vehicle"] . "</td><td data-cell='Trainer' >" . $row["trainer"] . "</td><td data-cell='Start Date' >" . $row["start_date"] . "</td><td data-cell='End Date' >" . $row["end_date"] . "</td><td data-cell='Status' >" . $row["status"] . "</td><td class='btns'> 
            <button class='btn moveToTT'  data-id='" . $row["id"] . "' style='background:#339ddd;' >Move</button> 
             <button  class='btn removeFromPreBook'  data-id='" . $row["id"] . "' style='background:#e74141;' >Remove</button></td></tr>";
        }
    } else {
        echo '<tr><td colspan="9" data-cell="data" >No Pre Booking</td></tr>';
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}
