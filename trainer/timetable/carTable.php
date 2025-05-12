<?php
include('../../config.php');


?>

<?php

date_default_timezone_set('Asia/Kolkata');
$current_timestamp_by_mktime = mktime(date("m"), date("d"), date("Y"));
$currentDate = date("Y-m-d", $current_timestamp_by_mktime);

// $check = "SELECT * FROM car_one WHERE status = 'active'";
// $result = mysqli_query($conn, $check);
// // echo $currentDate."<br>";
// if (!$result) {
//     die("Error executing query: " . mysqli_error($conn));
// }

// if (mysqli_num_rows($result) > 0) {
//     // Loop through each row
//     while ($row = mysqli_fetch_assoc($result)) {
//         $endDate = (string) $row['end_date'];
//         // echo $endDate."<br>";

//         if ($endDate < $currentDate) {
//             $id = $row['id'];

//             $update = "UPDATE car_one SET name='', phone='', vehicle='', trainer='',  start_date='', end_date='', status='empty' WHERE id = '$id'";
//             $updateResult = mysqli_query($conn, $update);

//             if (!$updateResult) {
//                 die("Error updating row: " . mysqli_error($conn));
//             } else {
//                 logActivity('admin_logs', "System", "Name: " . $row['name'] . " Phone: " . $row['phone'] . " Training Has been Ended in i10 car TimeSlot was : " . $row['timeslots']);
//                 $msg[] = "Name: " . $row['name'] . " Phone: " . $row['phone'] . " Training Has been Ended in i10 car TimeSlot was : " . $row['timeslots'];
//                 // echo "<script>alert('Rows updated successfully in i10')</script>";
//             }
//         }
//     }


// } else {
//     $msg[] = "No change in i10";
//     // echo "<script>alert('No rows to update in i10')</script>";

// }

?>


<?php



if (isset($_GET['car'])) {
    $result = mysqli_query($conn, "SELECT * FROM vehicles WHERE data_base_table = '" . $_GET['car'] . "'");
    if (mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()) {
            $CarDetailsFromDB = $row;
        }
    }

?>

    <div class="title"><?php echo $CarDetailsFromDB['vehicle_name']; ?> Time-Table <i class='bx bx-table'></i></div>

    <table>
        <thead>
            <tr>
                <th>Timeslots</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Vehicle</th>
                <th>Trainer</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>

            <?php


            $select = "SELECT * FROM " . $CarDetailsFromDB['data_base_table'] . " ORDER BY id ASC";
            $result = $conn->query($select);
            if ($result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {

            ?>

                    <tr id="<?php echo $row['id']; ?>">
                        <td id="TS">
                            <?php echo $row['timeslots']; ?>
                        </td>
                        <td>
                            <?php echo $row['name']; ?>
                        </td>
                        <td>
                            <?php echo $row['phone']; ?>
                        </td>
                        <td>
                            <?php echo $row['vehicle']; ?>
                        </td>
                        <td>
                            <?php echo $row['trainer']; ?>
                        </td>
                        <td id="SD">
                            <?php convertDMY((string) $row['start_date']); ?>
                        </td>
                        <td id="ED">
                            <?php convertDMY((string) $row['end_date']); ?>
                        </td>
                        <td>
                            <?php echo $row['status']; ?>
                        </td>
                        <td class="btns">

                            <?php if ($row['status'] == 'active') {
                                $phone = $row['phone'];
                                $getCustId = "SELECT id, cust_uid FROM cust_details WHERE phone='$phone'";
                                $custResult = mysqli_query($conn, $getCustId);
                                if ($custResult && $custRow = mysqli_fetch_assoc($custResult)) {
                                    echo "<a class='btn' href='../view?id=" . $custRow['id'] . "&phone=" . $phone . "&route=" . urlencode("../timetable?car=" . $CarDetailsFromDB['data_base_table']) . "#view' style='background:#4070f4;'>VIEW</a>";
                                    
                                       echo "<button class='force-attendance-btn btn' 
                                    style='background-color: rgb(224, 35, 35);'
                                    data-id='" . $custRow['id'] . "'
                                    data-cust-uid='" . $custRow['cust_uid'] . "'
                                    data-acc-id='" . $_SESSION['trainer_ID'] . "'
                                    data-acc-name='" . $_SESSION['trainer_name'] . "'>
                                    Mark Attendance
                                </button>";
                                }
                            } ?>
                        </td>
                    </tr>

            <?php

                }
            }

            ?>

        </tbody>
    </table>

<?php

} else {
    exit();
}

?>