<?php
include('../../../includes/authentication.php');
authenticationAdmin('../../../');
include('../../../config.php');

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Test database connection
    if (!mysqli_ping($conn)) {
        throw new Exception("Database connection is not active");
    }

    // Get parameters with proper validation
    $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
    $month = isset($_GET['month']) ? $_GET['month'] : date('n');
    $metric = isset($_GET['metric']) ? $_GET['metric'] : 'revenue';

    // Build the WHERE clause based on filters
    $where_clause = "trainername IS NOT NULL AND trainername != ''";
    $params = [];
    $param_types = "";

    if ($year !== 'all' && $year !== '') {
        $where_clause .= " AND YEAR(date) = ?";
        $params[] = intval($year);
        $param_types .= "i";
    }

    if ($month !== 'all' && $month !== '') {
        $where_clause .= " AND MONTH(date) = ?";
        $params[] = intval($month);
        $param_types .= "i";
    }

    // Enhanced debug info
    $debug_info = [
        'year' => $year,
        'month' => $month,
        'metric' => $metric,
        'where_clause' => $where_clause,
        'params' => $params,
        'param_types' => $param_types,
        'php_version' => PHP_VERSION,
        'mysql_version' => mysqli_get_server_info($conn),
        'connection_status' => mysqli_stat($conn)
    ];

    // Prepare the query based on the metric
    switch ($metric) {
        case 'revenue':
            $query = "SELECT 
                        trainername as label,
                        SUM(CAST(totalamount AS DECIMAL(10,2))) as value,
                        COUNT(*) as total_students,
                        COUNT(DISTINCT vehicle) as unique_vehicles,
                        SUM(CAST(paidamount AS DECIMAL(10,2))) as collected_amount,
                        SUM(CAST(dueamount AS DECIMAL(10,2))) as pending_amount
                     FROM cust_details 
                     WHERE $where_clause
                     GROUP BY trainername 
                     ORDER BY value DESC";
            $metricLabel = 'Revenue Generated';
            $yAxisLabel = 'Revenue (₹)';
            break;

        case 'students':
            $query = "SELECT 
                        trainername as label,
                        COUNT(*) as value,
                        COUNT(DISTINCT vehicle) as unique_vehicles,
                        SUM(CAST(totalamount AS DECIMAL(10,2))) as total_revenue,
                        AVG(CAST(totalamount AS DECIMAL(10,2))) as avg_revenue_per_student
                     FROM cust_details 
                     WHERE $where_clause
                     GROUP BY trainername 
                     ORDER BY value DESC";
            $metricLabel = 'Total Students';
            $yAxisLabel = 'Number of Students';
            break;

        case 'hours':
            $query = "SELECT 
                        trainername as label,
                        SUM(TIMESTAMPDIFF(HOUR, starttime, endtime)) as value,
                        COUNT(*) as total_students,
                        COUNT(DISTINCT vehicle) as unique_vehicles,
                        AVG(TIMESTAMPDIFF(HOUR, starttime, endtime)) as avg_hours_per_student
                     FROM cust_details 
                     WHERE $where_clause 
                     AND starttime IS NOT NULL 
                     AND endtime IS NOT NULL
                     GROUP BY trainername 
                     ORDER BY value DESC";
            $metricLabel = 'Training Hours';
            $yAxisLabel = 'Hours';
            break;

        case 'rating':
            $query = "SELECT 
                        trainername as label,
                        AVG(IFNULL(rating, 0)) as value,
                        COUNT(*) as total_ratings,
                        COUNT(DISTINCT vehicle) as unique_vehicles,
                        MIN(rating) as lowest_rating,
                        MAX(rating) as highest_rating
                     FROM cust_details 
                     WHERE $where_clause
                     AND rating IS NOT NULL
                     GROUP BY trainername 
                     ORDER BY value DESC";
            $metricLabel = 'Average Rating';
            $yAxisLabel = 'Rating';
            break;

        default:
            throw new Exception('Invalid metric');
    }

    // Prepare and execute query
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Failed to prepare query: " . $conn->error);
    }

    if (!empty($params)) {
        if (!$stmt->bind_param($param_types, ...$params)) {
            throw new Exception("Failed to bind parameters: " . $stmt->error);
        }
    }

    if (!$stmt->execute()) {
        throw new Exception("Failed to execute query: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception("Failed to get result: " . $stmt->error);
    }

    // Prepare data
    $labels = [];
    $values = [];
    $insights = [];

    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['label'];
        $values[] = floatval($row['value']);
        
        // Remove label and value from insights
        $rowInsights = $row;
        unset($rowInsights['label']);
        unset($rowInsights['value']);
        $insights[] = $rowInsights;
    }

    // Return data with enhanced information
    echo json_encode([
        'success' => true,
        'labels' => $labels,
        'values' => $values,
        'insights' => $insights,
        'metricLabel' => $metricLabel,
        'yAxisLabel' => $yAxisLabel,
        'debug' => $debug_info
    ]);

} catch (Exception $e) {
    error_log("Trainer Performance API Error: " . $e->getMessage());
    error_log("Debug Info: " . print_r($debug_info ?? [], true));
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => [
            'error_details' => [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ],
            'request_data' => [
                'year' => $year ?? null,
                'month' => $month ?? null,
                'metric' => $metric ?? null,
                'get_data' => $_GET
            ],
            'server_info' => [
                'php_version' => PHP_VERSION,
                'mysql_version' => $conn ? mysqli_get_server_info($conn) : 'Not connected',
                'last_mysql_error' => $conn ? mysqli_error($conn) : 'No connection',
                'memory_usage' => memory_get_usage(true),
                'peak_memory' => memory_get_peak_usage(true),
                'timezone' => date_default_timezone_get(),
                'current_time' => date('Y-m-d H:i:s')
            ],
            'debug_info' => $debug_info ?? []
        ]
    ]);
} 