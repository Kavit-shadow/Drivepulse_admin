<?php
header('Content-Type: application/json');
include('../../config.php');

try {
    // Initialize response array
    $response = [
        'status' => 'success',
        'data' => [],
        'message' => ''
    ];

    // Prepare base query with proper JOINs based on schema
    $query = "SELECT 
        id,
        name,
        email, 
        phone,
        package_name,
        package_price,
        package_features,
        vehicle_type,
        vehicle_name,
        duration,
        time_slot,
        booking_inquiry_date,
        distance,
        session_duration,
        is_read,
        created_at
    FROM booking_inquiries";

    // Add WHERE clauses based on filters
    $whereConditions = [];
    $params = [];
    $types = '';

    // Search functionality
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $searchTerm = '%' . $_GET['search'] . '%';
        $whereConditions[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ?)";
        $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        $types .= 'sss';
    }

    // Filter functionality
    if (isset($_GET['filter'])) {
        switch($_GET['filter']) {
            case 'New':
                $whereConditions[] = "is_read = 0";
                break;
            case 'Today':
                $whereConditions[] = "DATE(booking_inquiry_date) = CURDATE()";
                break;
            case 'This Week':
                $whereConditions[] = "YEARWEEK(booking_inquiry_date) = YEARWEEK(NOW())";
                break;
            case 'Standard':
            case 'Premium': 
            case 'Gold':
            case 'Platinum':
            case 'Emerald':
            case 'Two Wheeler':
                $whereConditions[] = "package_name LIKE ?";
                $params[] = $_GET['filter'] . '%';
                $types .= 's';
                break;
        }
    }

    // Combine WHERE conditions
    if (!empty($whereConditions)) {
        $query .= " WHERE " . implode(" AND ", $whereConditions);
    }

    // Add sorting - sort by is_read first, then created_at
    $query .= " ORDER BY is_read ASC, created_at DESC";

    // Prepare and execute statement
    $stmt = mysqli_prepare($conn, $query);
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        throw new Exception(mysqli_error($conn));
    }

    // Format results
    while ($row = mysqli_fetch_assoc($result)) {
        // Parse package features - handle both JSON and comma-separated strings
        $features = [];
        if (!empty($row['package_features'])) {
            $features = json_decode($row['package_features'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $features = explode(',', $row['package_features']);
            }
        }

        // Format the booking date for display
        $bookingDate = new DateTime($row['booking_inquiry_date']);
        $formattedDate = $bookingDate->format('Y-m-d H:i:s');

        // Check if phone or email exists in cust_details
        $checkCustomerQuery = "SELECT COUNT(*) as count FROM cust_details WHERE phone = ? OR email = ?";
        $checkStmt = mysqli_prepare($conn, $checkCustomerQuery);
        mysqli_stmt_bind_param($checkStmt, "ss", $row['phone'], $row['email']);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);
        $customerExists = mysqli_fetch_assoc($checkResult)['count'] > 0;
        mysqli_stmt_close($checkStmt);

        $inquiry = [
            'id' => (int)$row['id'],
            'name' => htmlspecialchars($row['name']),
            'email' => htmlspecialchars($row['email']),
            'phone' => htmlspecialchars($row['phone']),
            'package_name' => htmlspecialchars($row['package_name']),
            'package_price' => '₹' . number_format((float)$row['package_price'], 2),
            'package_features' => array_map('htmlspecialchars', array_map('trim', $features)),
            'vehicle_type' => htmlspecialchars($row['vehicle_type']),
            'vehicle_name' => htmlspecialchars($row['vehicle_name']),
            'duration' => htmlspecialchars($row['duration']),
            'time_slot' => htmlspecialchars($row['time_slot']),
            'distance' => htmlspecialchars($row['distance']),
            'session_duration' => htmlspecialchars($row['session_duration']),
            'is_read' => (bool)$row['is_read'],
            'booking_inquiry_date' => $formattedDate,
            'created_at' => $row['created_at'],
            'is_customer' => $customerExists
        ];

        $response['data'][] = $inquiry;
    }

    $response['message'] = count($response['data']) . ' inquiries found';

} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => 'Error fetching inquiries: ' . $e->getMessage()
    ];
    http_response_code(500);
} finally {
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
}

// Send JSON response with proper headers
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
echo json_encode($response);
?>
