<?php

include('../includes/authenticationTrainer.php');
authenticationTrainer('../');


if (isset($_POST['input'])) {

    $input = $_POST['input'];
    $query = "SELECT * FROM `cust_details` WHERE name LIKE '{$input}%' OR phone LIKE '{$input}%' OR date LIKE '{$input}%' OR cust_uid LIKE '{$input}%' ORDER BY date DESC";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {

        ?>

        <div class="search-table">
            <table>

                <thead>
                    <tr>
                        <th>Admission Date</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Time Slots</th>
                        <th>Vehicle</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr><td>" . $row["date"] . "</td><td>" 
                            . $row["name"] . "</td><td>" 
                            . $row["phone"] . "</td><td>"
                            . $row["email"] . "</td><td>" 
                            . $row["timeslot"] . "</td><td>" 
                            . $row["vehicle"] . "</td><td>"
                            . "<a href='view/?id=" . $row["id"] . "&phone=" . $row["phone"] . "&date=" . $row["date"] ."&route=../'><i class='bx bxs-show' style='font-size: 24px;'></i></a>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
 <?php

    } else {

        echo "<h4>Not Data Found</h4>";
    }
}




?>