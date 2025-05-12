<?php
include("../config.php");
include('../includes/authenticationAdminOrStaff.php');
authenticationAdminOrStaff();


// Retrieve the table name from the API request
$apiRequestTableName = isset($_GET['table_name']) ? htmlspecialchars($_GET['table_name']) : '';

// Ensure table name is provided and valid
if (empty($apiRequestTableName)) {
    die("Table name is required.");
}

// // First, find the table name for the 2-wheel vehicle
// $findTableQuery = "SELECT data_base_table FROM vehicles WHERE category='2-wheel' AND data_base_table='$apiRequestTableName'";
// $tableResult = mysqli_query($conn, $findTableQuery);

// if (!$tableResult) {
//     die("Error finding two-wheeler: " . mysqli_error($conn));
// }

// $tableRow = mysqli_fetch_assoc($tableResult);
// if (!$tableRow) {
//     die("No two-wheeler vehicle found in the system.");
// }

// $twoWheelerTable = $tableRow['data_base_table'];

$timeSlots = array(
    "7:00am to 7:45am",
    "7:45am to 8:30am",
    "8:30am to 9:15am",
    "9:15am to 10:00am",
    "10:00am to 10:45am",
    "10:45am to 11:30am",
    "11:30am to 12:15pm",
    "12:15pm to 1:00pm",
    "1:00pm to 1:45pm",
    "1:45pm to 2:30pm",
    "2:30pm to 3:15pm",
    "3:15pm to 4:00pm",
    "4:00pm to 4:45pm",
    "4:45pm to 5:30pm",
    "5:30pm to 6:15pm",
    "6:15pm to 7:00pm",
    "7:00pm to 7:45pm",
    "7:45pm to 8:30pm"
);

echo "<label>Time Slot</label>";
echo "<select required name='time-slot'>";
echo "<option disabled selected>Select Time Slot (Two Wheeler) ".$_GET['vehicle_name']."</option>";
// Loop through the time slots
foreach ($timeSlots as $index => $slot) {
    $i = $index + 1; // Starting from id=2 to match getCarsTimeSlots.php
    // Prepare SQL query with the dynamic table name


    $carquery = "SELECT * FROM `$apiRequestTableName` WHERE id=$i";
    $carquery1 = mysqli_query($conn, $carquery);

   

    if (!$carquery1) {
        die("Query failed: " . mysqli_error($conn));
    }

    while ($row = mysqli_fetch_assoc($carquery1)) {
        $status = $row['status'];

        if ($status == "empty") {
            echo "<option value='$slot'>$slot</option>";
        } elseif ($status == "active") {
            echo "<option disabled title='Name: ".$row['name']." &#13;Start_Date: ".$row['start_date']." &#13;End_date: ".$row['end_date']."' value='$slot' style='background-color: red; color: white; font-weight: 700;'>$slot (Occupied)</option>";
        }
    }
}

echo "</select>";
?>