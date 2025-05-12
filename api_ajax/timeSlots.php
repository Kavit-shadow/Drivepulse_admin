<?php
// include('../includes/authenticationAdminOrStaff.php');
// authenticationAdminOrStaff();
include('../config.php');

// Add CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Define the time slots array with proper formatting
$timeSlots = array(
    "7:00am to 7:30am",
    "7:30am to 8:00am", 
    "8:00am to 8:30am",
    "8:30am to 9:00am",
    "9:00am to 9:30am",
    "9:30am to 10:00am",
    "10:00am to 10:30am",
    "10:30am to 11:00am",
    "11:00am to 11:30am",
    "11:30am to 12:00pm",
    "12:00pm to 12:30pm",
    "12:30pm to 1:00pm",
    "1:00pm to 1:30pm",
    "1:30pm to 2:00pm",
    "2:00pm to 2:30pm",
    "2:30pm to 3:00pm",
    "3:00pm to 3:30pm",
    "3:30pm to 4:00pm",
    "4:00pm to 4:30pm",
    "4:30pm to 5:00pm",
    "5:00pm to 5:30pm",
    "5:30pm to 6:00pm",
    "6:00pm to 6:30pm",
    "6:30pm to 7:00pm",
    "7:00pm to 7:30pm",
    "7:30pm to 8:00pm"
);

$timeSlotsJSON = array(
    "7:00am to 7:30am" => "07:00 AM - 07:30 AM",
    "7:30am to 8:00am" => "07:30 AM - 08:00 AM",
    "8:00am to 8:30am" => "08:00 AM - 08:30 AM",
    "8:30am to 9:00am" => "08:30 AM - 09:00 AM",
    "9:00am to 9:30am" => "09:00 AM - 09:30 AM",
    "9:30am to 10:00am" => "09:30 AM - 10:00 AM",
    "10:00am to 10:30am" => "10:00 AM - 10:30 AM",
    "10:30am to 11:00am" => "10:30 AM - 11:00 AM",
    "11:00am to 11:30am" => "11:00 AM - 11:30 AM",
    "11:30am to 12:00pm" => "11:30 AM - 12:00 PM",
    "12:00pm to 12:30pm" => "12:00 PM - 12:30 PM",
    "12:30pm to 1:00pm" => "12:30 PM - 01:00 PM",
    "1:00pm to 1:30pm" => "01:00 PM - 01:30 PM",
    "1:30pm to 2:00pm" => "01:30 PM - 02:00 PM",
    "2:00pm to 2:30pm" => "02:00 PM - 02:30 PM",
    "2:30pm to 3:00pm" => "02:30 PM - 03:00 PM",
    "3:00pm to 3:30pm" => "03:00 PM - 03:30 PM",
    "3:30pm to 4:00pm" => "03:30 PM - 04:00 PM",
    "4:00pm to 4:30pm" => "04:00 PM - 04:30 PM",
    "4:30pm to 5:00pm" => "04:30 PM - 05:00 PM",
    "5:00pm to 5:30pm" => "05:00 PM - 05:30 PM",
    "5:30pm to 6:00pm" => "05:30 PM - 06:00 PM",
    "6:00pm to 6:30pm" => "06:00 PM - 06:30 PM",
    "6:30pm to 7:00pm" => "06:30 PM - 07:00 PM",
    "7:00pm to 7:30pm" => "07:00 PM - 07:30 PM",
    "7:30pm to 8:00pm" => "07:30 PM - 08:00 PM"
);

// Define automatic transmission vehicles
$automaticVehicles = array(
    'WagonR'
);

// Vehicle types mapping
$vehicleTypes = array(
    'WagonR' => 'Hatchback',
    'Toyota Liva' => 'Hatchback',
    'Verna' => 'Sedan'
);

// Boolean to control whether to include 2-wheel vehicles
$includeTwoWheel = false;

try {
    // Get vehicles data
    $vehiclesQuery = "SELECT `id`, `category`, `vehicle_name`, `data_base_table`, `created_at` FROM `vehicles`";
    $vehiclesResult = mysqli_query($conn, $vehiclesQuery);

    if (!$vehiclesResult) {
        throw new Exception("Error fetching vehicles: " . mysqli_error($conn));
    }

    $response = array();
    
    while ($vehicle = mysqli_fetch_assoc($vehiclesResult)) {
        // Skip 2-wheel vehicles if includeTwoWheel is false
        if (!$includeTwoWheel && $vehicle['category'] == '2-wheel') {
            continue;
        }

        $vehicleName = $vehicle['vehicle_name'];
        // Get vehicle type from mapping
        $vehicleType = isset($vehicleTypes[$vehicleName]) ? $vehicleTypes[$vehicleName] : 'Other';
        
        // Determine category based on vehicle category
        if ($vehicle['category'] == '2-wheel') {
            $category = $vehicleType . ' - ' . $vehicleName;
        } else {
            // For 4-wheel, include transmission type and vehicle type
            $transmission = in_array($vehicleName, $automaticVehicles) ? 'Automatic' : 'Manual';
            $category = $vehicleType . ' ' . $transmission;
        }
        
        if (!isset($response[$category])) {
            $response[$category] = array();
        }

        $vehicleTimeSlots = array();
        $emptySlots = 0;
        $totalSlots = count($timeSlots);
        
       foreach ($timeSlots as $time) {
            // Query the vehicle's specific table for slot availability
            $slotQuery = "SELECT status, end_date FROM `" . $vehicle['data_base_table'] . "` WHERE timeslots = '$time'";
            $slotResult = mysqli_query($conn, $slotQuery);
            
            if ($slotResult && $row = mysqli_fetch_assoc($slotResult)) {
                $isAvailable = ($row['status'] == 'empty');
                if ($isAvailable) {
                    $emptySlots++;
                }
                $vehicleTimeSlots[] = array(
                    "time" => $timeSlotsJSON[$time],
                    "available" => $isAvailable,
                    "end_date" => $row['end_date']
                );
            } else {
                // If no status found, assume available
                $emptySlots++;
                $vehicleTimeSlots[] = array(
                    "time" => $timeSlotsJSON[$time],
                    "available" => true,
                    "end_date" => null
                );
            }
        }

        // If 8 or fewer empty slots, mark as busy
        $vehicleData = array(
            "id" => $vehicle['id'],
            "vehicleName" => $vehicle['vehicle_name'],
            "status" => ($emptySlots <= 8) ? "busy" : "available",
            "timeSlots" => $vehicleTimeSlots
        );

        $response[$category][] = $vehicleData;
    }

    // Set JSON header and encoding options
    header('Content-Type: application/json');
    echo json_encode(
        $response, 
        JSON_PRETTY_PRINT | 
        JSON_UNESCAPED_SLASHES | 
        JSON_UNESCAPED_UNICODE
    );

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(
        array('error' => $e->getMessage()),
        JSON_PRETTY_PRINT
    );
}
?>



