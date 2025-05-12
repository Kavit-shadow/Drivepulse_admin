
<?php

include("../config.php");

// Get category from POST request
$category = isset($_POST['category']) ? $_POST['category'] : '';

// Validate category is not empty
if(empty($category)) {
    die("Category parameter is required");
}

// Fetch the data from the database
$query = "SELECT * FROM vehicles WHERE category = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $category);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if the query was successful
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

// Generate the options for the select element
$options = "<option disabled selected>Select Vehicle</option>";

while ($row = mysqli_fetch_assoc($result)) {
    $tableName = htmlspecialchars($row['data_base_table']); // Sanitize output
    $displayName = htmlspecialchars($row['vehicle_name']); // You can customize the display name if needed
    $options .= "<option value='$tableName'>$displayName</option>";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
echo $options;

?>