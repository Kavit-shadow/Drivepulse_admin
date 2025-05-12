<?php
//  function convertJsonToVehicleString($json)
// {
//     // Decode the JSON into a PHP array
//     $vehicleDetails = json_decode($json, true);

//     // Check if the array contains a 'duration' key
//     if (array_key_exists('duration', $vehicleDetails)) {
//         // Convert to a vehicle string with duration
//         $vehicleString = $vehicleDetails['vehicleType'] . ' ' . $vehicleDetails['shash'] . ' ' . $vehicleDetails['duration'];
//     } else {
//         // Convert to a vehicle string without duration
//         $vehicleString = $vehicleDetails['vehicleType'] . ' ' . $vehicleDetails['carType'] . ' ' . $vehicleDetails['shash'] . ' ' . $vehicleDetails['distance'];
//     }

//     // Return the vehicle string
//     return $vehicleString;
// }

// function convertVehicleStringToJson($vehicle)
// {
//     // Check if the vehicle string contains ' mins' (indicating duration)
//     if (strpos($vehicle, ' mins') !== false) {
//         // Extract the vehicle type and duration
//         list($vehicleType, $duration) = explode('/', $vehicle);

//         // Create an associative array with keys and values
//         $vehicleDetails = [
//             'vehicleType' => trim($vehicleType),
//             'shash' => '/',
//             'duration' => trim($duration)
//         ];
//     } else {

//         // Split the original string into parts
//         $parts = explode('/', $vehicle);

//         // Extract vehicle type and car type
//         $vehicleType = trim($parts[0]);
//         $distance = trim($parts[1], ', ');


//         $parts = explode(' ', $vehicleType);
//         $vehicleType = $parts[0] . ' ' . $parts[1];
//         $carType = $parts[2];


//         // Create an associative array
//         $vehicleDetails = [
//             "vehicleType" => $vehicleType,
//             "carType" => $carType,
//             "shash" => "/",
//             "distance" => $distance
//         ];

//     }

//     // Convert the associative array to JSON
//     return json_encode($vehicleDetails, JSON_PRETTY_PRINT);
// }






?>