<?php
include('../includes/authenticationAdminOrStaffOrTrainer.php');
authenticationAdminOrStaffOrTrainer('../');
include '../config.php';

// Get car ID from request
$carId = isset($_GET['car']) ? $_GET['car'] : null;

if (!$carId) {
    echo "No car ID provided";
    exit;
}

// Query to get timetable data
$query = "SELECT timeslots, name, phone, vehicle, trainer, start_date, end_date, status 
          FROM $carId 
          ORDER BY id ASC";

$result = mysqli_query($conn, $query);

if (!$result) {
    echo "Error retrieving timetable data";
    exit;
}

// Build HTML table
$html = '<table class="timetable-table" style="width:100%; border-collapse: collapse; margin-top: 20px;">
         <thead>
           <tr>
             <th style="padding: 10px; border: 1px solid #ddd; background-color: #f4f4f4;">Time Slots</th>
             <th style="padding: 10px; border: 1px solid #ddd; background-color: #f4f4f4;">Name</th>
             <th style="padding: 10px; border: 1px solid #ddd; background-color: #f4f4f4;">Phone</th>
             <th style="padding: 10px; border: 1px solid #ddd; background-color: #f4f4f4;">Vehicle</th>
             <th style="padding: 10px; border: 1px solid #ddd; background-color: #f4f4f4;">Trainer</th>
             <th style="padding: 10px; border: 1px solid #ddd; background-color: #f4f4f4;">Start Date</th>
             <th style="padding: 10px; border: 1px solid #ddd; background-color: #f4f4f4;">End Date</th>
             <th style="padding: 10px; border: 1px solid #ddd; background-color: #f4f4f4;">Status</th>
           </tr>
         </thead>
         <tbody>';

while ($row = mysqli_fetch_assoc($result)) {
     $html .= '<tr>
                <td style="padding: 10px; border: 1px solid #ddd;">'.$row['timeslots'].'</td>
                <td style="padding: 10px; border: 1px solid #ddd;">'.$row['name'].'</td>
                <td style="padding: 10px; border: 1px solid #ddd;"><a href="tel:'.$row['phone'].'">'.$row['phone'].'</a></td>
                <td style="padding: 10px; border: 1px solid #ddd;">'.$row['vehicle'].'</td>
                <td style="padding: 10px; border: 1px solid #ddd;">'.$row['trainer'].'</td>
                <td style="padding: 10px; border: 1px solid #ddd;">'.$row['start_date'].'</td>
                <td style="padding: 10px; border: 1px solid #ddd;">'.$row['end_date'].'</td>
                <td style="padding: 10px; border: 1px solid #ddd;">'.$row['status'].'</td>
              </tr>';
}

$html .= '</tbody></table>';

// Return the HTML table
echo $html;

mysqli_close($conn);
?>
