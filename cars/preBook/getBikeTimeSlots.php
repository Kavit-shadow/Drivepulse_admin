<?php
include('../../includes/authenticationAdminOrStaff.php');
authenticationAdminOrStaff('../../');
include('../../config.php');

// Retrieve the table name from the API request
$apiRequestTableName = isset($_GET['table_name']) ? htmlspecialchars($_GET['table_name']) : '';

// Ensure table name is provided and valid
if (empty($apiRequestTableName)) {
    die("Table name is required.");
}


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
echo "<option disabled selected>Select Time Slot PreBook (Two Wheeler)</option>";

// Loop through the time slots
foreach ($timeSlots as $index => $slot) {
    $i = $index + 1;
    $carquery = "SELECT * FROM `".$apiRequestTableName."` WHERE id=$i";
    $carquery1 = mysqli_query($conn, $carquery);
    
    while ($row = mysqli_fetch_assoc($carquery1)) {
        $text = $row['vehicle'];
        $timeSlot = $row['timeslots'];
        
        $queueSelect = "SELECT COUNT(*)  as queue_count FROM pre_book_queue WHERE timeslot = '$timeSlot' AND vehicle = '".$_GET['vehicle_name']."'";
        $queueCount = mysqli_query($conn,$queueSelect);
        if ($queueCount) {
            $count = $queueCount->fetch_assoc();
            $totalCount = $count['queue_count'];
        }

        echo "<option title='In Queue: $totalCount'value='$slot'>$slot</option>";

    }
}
echo "</select>";
?>
