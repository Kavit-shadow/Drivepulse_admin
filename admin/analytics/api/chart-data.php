<?php
include('../../../includes/authentication.php');
authenticationAdmin('../../../');
include('../../../config.php');

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the year and month from query parameters
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$month = isset($_GET['month']) ? $_GET['month'] : 'all';

try {
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Test database connection
    if (!mysqli_ping($conn)) {
        throw new Exception("Database connection is not active");
    }

    // Build the WHERE clause based on filters
    $where_clause = "1=1"; // Always true condition as base
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
        'where_clause' => $where_clause,
        'params' => $params,
        'param_types' => $param_types,
        'php_version' => PHP_VERSION,
        'mysql_version' => mysqli_get_server_info($conn),
        'connection_status' => mysqli_stat($conn),
        'memory_usage' => memory_get_usage(true),
        'peak_memory' => memory_get_peak_usage(true),
        'timezone' => date_default_timezone_get(),
        'current_time' => date('Y-m-d H:i:s')
    ];

    // Test if the table exists with error details
    $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'cust_details'");
    if (!$table_check) {
        throw new Exception("Failed to check table existence: " . mysqli_error($conn));
    }
    if (mysqli_num_rows($table_check) == 0) {
        throw new Exception("Table 'cust_details' does not exist in the database");
    }

    // Test if there's any data with detailed error handling
    $count_query = "SELECT COUNT(*) as count FROM cust_details WHERE $where_clause";
    $debug_info['count_query'] = $count_query;
    
    $stmt = $conn->prepare($count_query);
    if (!$stmt) {
        throw new Exception("Failed to prepare count query: " . $conn->error);
    }

    if (!empty($params)) {
        try {
            if (!$stmt->bind_param($param_types, ...$params)) {
                throw new Exception("Failed to bind parameters: " . $stmt->error);
            }
        } catch (Exception $e) {
            throw new Exception("Parameter binding error: " . $e->getMessage() . ". Params: " . print_r($params, true));
        }
    }

    if (!$stmt->execute()) {
        throw new Exception("Failed to execute count query: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception("Failed to get count result: " . $stmt->error);
    }

    $year_count = $result->fetch_assoc();
    $debug_info['records_found'] = $year_count['count'];

    if ($year_count['count'] == 0) {
        echo json_encode([
            'success' => true,
            'months' => [],
            'sales' => [],
            'customers' => [],
            'distribution' => [
                'labels' => [],
                'values' => []
            ],
            'stats' => [
                'totalAmount' => 0,
                'totalPaid' => 0,
                'totalDue' => 0,
                'totalCustomers' => 0,
                'averageSale' => 0,
                'monthlyGrowth' => 0
            ],
            'debug' => $debug_info
        ]);
        exit;
    }

    // Monthly data query with payment details
    $monthly_query = "SELECT 
        DATE_FORMAT(date, '%m-%Y') as month,
        SUM(totalamount) as total_amount,
        SUM(paidamount) as total_paid,
        SUM(dueamount) as total_due,
        COUNT(*) as total_customers,
        COUNT(DISTINCT trainername) as total_trainers,
        COUNT(DISTINCT timeslot) as total_slots
        FROM cust_details
        WHERE $where_clause
        GROUP BY month
        ORDER BY month";

    // Prepare and execute monthly query
    $stmt = $conn->prepare($monthly_query);
    if (!$stmt) {
        throw new Exception("Failed to prepare monthly query: " . $conn->error);
    }

    if (!empty($params)) {
        if (!$stmt->bind_param($param_types, ...$params)) {
            throw new Exception("Failed to bind parameters: " . $stmt->error);
        }
    }
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute monthly query: " . $stmt->error);
    }

    $monthly_result = $stmt->get_result();
    if (!$monthly_result) {
        throw new Exception("Failed to get monthly results: " . $stmt->error);
    }

    $months = [];
    $total_amounts = [];
    $paid_amounts = [];
    $due_amounts = [];
    $customers = [];
    $total_amount = 0;
    $total_paid = 0;
    $total_due = 0;
    $total_customers = 0;
    $prev_month_paid = 0;
    $current_month_paid = 0;

    while ($row = $monthly_result->fetch_assoc()) {
        $month_name = date('M Y', strtotime('01-' . $row['month']));
        $months[] = $month_name;
        $total_amounts[] = floatval($row['total_amount']);
        $paid_amounts[] = floatval($row['total_paid']);
        $due_amounts[] = floatval($row['total_due']);
        $customers[] = intval($row['total_customers']);
        
        $total_amount += $row['total_amount'];
        $total_paid += $row['total_paid'];
        $total_due += $row['total_due'];
        $total_customers += $row['total_customers'];

        // Track last two months for growth calculation
        $prev_month_paid = $current_month_paid;
        $current_month_paid = $row['total_paid'];
    }

    // Calculate monthly growth
    $monthly_growth = 0;
    if ($prev_month_paid > 0) {
        $monthly_growth = round((($current_month_paid - $prev_month_paid) / $prev_month_paid) * 100, 2);
    }

    // Calculate yearly growth including all years
    $yearly_growth_query = "WITH YearlyData AS (
        SELECT 
            YEAR(date) as year,
            SUM(paidamount) as total_revenue,
            COUNT(DISTINCT DATE_FORMAT(date, '%Y-%m')) as num_months,
            COUNT(*) as num_customers
        FROM cust_details
        GROUP BY YEAR(date)
        HAVING num_months >= 1
        ORDER BY year DESC
    )
    SELECT 
        year,
        total_revenue,
        num_months,
        num_customers,
        total_revenue / num_months as monthly_avg
    FROM YearlyData
    ORDER BY year DESC";

    $yearly_result = $conn->query($yearly_growth_query);
    $yearly_data = [];
    while ($row = $yearly_result->fetch_assoc()) {
        $yearly_data[$row['year']] = [
            'total_revenue' => floatval($row['total_revenue']),
            'num_months' => intval($row['num_months']),
            'monthly_avg' => floatval($row['monthly_avg']),
            'num_customers' => intval($row['num_customers'])
        ];
    }

    // Debug information
    error_log("All Years Data: " . json_encode($yearly_data));

    // Find the years for comparison (using the two most recent complete years)
    $available_years = array_keys($yearly_data);
    $current_year = date('Y');  // Get current year (2025)
    
    // Calculate growth between the two most recent complete years
    $yearly_growth = 0;
    if (count($available_years) >= 2) {
        // Get the two most recent complete years (excluding current year)
        $complete_years = array_filter($available_years, function($year) use ($current_year) {
            return $year < $current_year;
        });
        rsort($complete_years);  // Sort in descending order
        
        if (count($complete_years) >= 2) {
            $recent_year = $complete_years[0];    // 2024
            $previous_year = $complete_years[1];  // 2023
            
            $recent_total = $yearly_data[$recent_year]['total_revenue'];
            $previous_total = $yearly_data[$previous_year]['total_revenue'];
            
            if ($previous_total > 0) {
                $yearly_growth = round((($recent_total - $previous_total) / $previous_total) * 100, 2);
            }
            
            // Debug information
            error_log("All Available Years: " . implode(", ", $available_years));
            error_log("Complete Years Found: " . implode(", ", $complete_years));
            error_log("Recent Year ($recent_year) Revenue: " . $recent_total);
            error_log("Previous Year ($previous_year) Revenue: " . $previous_total);
            error_log("Growth Rate ($recent_year vs $previous_year): " . $yearly_growth . "%");
        }
    }

    // Distribution queries
    $distributions = [];

    // 1. Enhanced Vehicle Type Distribution with Revenue Analysis
    $vehicle_query = "SELECT 
        vehicle as label,
        COUNT(*) as value,
        SUM(totalamount) as total_revenue,
        SUM(paidamount) as paid_revenue,
        AVG(totalamount) as avg_revenue_per_vehicle,
        COUNT(DISTINCT trainername) as unique_trainers,
        SUM(CASE WHEN dueamount > 0 THEN 1 ELSE 0 END) as pending_payments,
        (SUM(paidamount) / SUM(totalamount) * 100) as collection_rate
        FROM cust_details
        WHERE $where_clause
        GROUP BY vehicle
        ORDER BY value DESC";

    // Vehicle query execution
    $stmt = $conn->prepare($vehicle_query);
    if (!$stmt) {
        throw new Exception("Failed to prepare vehicle query: " . $conn->error);
    }
    if (!empty($params)) {
        if (!$stmt->bind_param($param_types, ...$params)) {
            throw new Exception("Failed to bind parameters for vehicle query: " . $stmt->error);
        }
    }
    $stmt->execute();
    $vehicle_result = $stmt->get_result();

    $vehicle_labels = [];
    $vehicle_values = [];
    $vehicle_insights = [];
    while ($row = $vehicle_result->fetch_assoc()) {
        $vehicle_labels[] = $row['label'];
        $vehicle_values[] = intval($row['value']);
        $vehicle_insights[] = [
            'total_revenue' => round(floatval($row['total_revenue']), 2),
            'paid_revenue' => round(floatval($row['paid_revenue']), 2),
            'avg_revenue' => round(floatval($row['avg_revenue_per_vehicle']), 2),
            'unique_trainers' => intval($row['unique_trainers']),
            'pending_payments' => intval($row['pending_payments']),
            'collection_rate' => round(floatval($row['collection_rate']), 2)
        ];
    }

    // 2. Enhanced Payment Method Analysis
    $payment_query = "SELECT 
        payment_method as label,
        COUNT(*) as value,
        SUM(totalamount) as total_revenue,
        AVG(totalamount) as avg_transaction,
        MIN(totalamount) as min_transaction,
        MAX(totalamount) as max_transaction,
        COUNT(DISTINCT DATE(date)) as unique_days,
        COUNT(DISTINCT vehicle) as unique_vehicles
        FROM cust_details
        WHERE $where_clause
        GROUP BY payment_method
        ORDER BY total_revenue DESC";

    // Payment query execution
    $stmt = $conn->prepare($payment_query);
    if (!$stmt) {
        throw new Exception("Failed to prepare payment query: " . $conn->error);
    }
    if (!empty($params)) {
        if (!$stmt->bind_param($param_types, ...$params)) {
            throw new Exception("Failed to bind parameters for payment query: " . $stmt->error);
        }
    }
    $stmt->execute();
    $payment_result = $stmt->get_result();

    $payment_labels = [];
    $payment_values = [];
    $payment_insights = [];
    while ($row = $payment_result->fetch_assoc()) {
        $payment_labels[] = $row['label'];
        $payment_values[] = intval($row['value']);
        $payment_insights[] = [
            'total_revenue' => round(floatval($row['total_revenue']), 2),
            'avg_transaction' => round(floatval($row['avg_transaction']), 2),
            'min_transaction' => round(floatval($row['min_transaction']), 2),
            'max_transaction' => round(floatval($row['max_transaction']), 2),
            'unique_days' => intval($row['unique_days']),
            'unique_vehicles' => intval($row['unique_vehicles'])
        ];
    }

    // 3. Enhanced Time Slot Analysis
    $timeslot_query = "SELECT 
        timeslot as label,
        COUNT(*) as value,
        COUNT(DISTINCT DATE(date)) as unique_days,
        COUNT(DISTINCT vehicle) as unique_vehicles,
        COUNT(DISTINCT trainername) as unique_trainers,
        SUM(CAST(totalamount AS DECIMAL(10,2))) as total_revenue,
        AVG(CAST(totalamount AS DECIMAL(10,2))) as avg_revenue,
        SUM(CASE WHEN dueamount > 0 THEN 1 ELSE 0 END) as pending_payments,
        (COUNT(*) / (SELECT COUNT(*) FROM cust_details WHERE $where_clause) * 100) as slot_utilization
    FROM cust_details
    WHERE $where_clause
    GROUP BY timeslot
    ORDER BY 
    CASE 
        -- Four Wheeler Slots (Morning)
        WHEN timeslot = '7:00am to 7:30am' THEN 1
        WHEN timeslot = '7:30am to 8:00am' THEN 2
        WHEN timeslot = '8:00am to 8:30am' THEN 3
        WHEN timeslot = '8:30am to 9:00am' THEN 4
        WHEN timeslot = '9:00am to 9:30am' THEN 5
        WHEN timeslot = '9:30am to 10:00am' THEN 6
        WHEN timeslot = '10:00am to 10:30am' THEN 7
        WHEN timeslot = '10:30am to 11:00am' THEN 8
        WHEN timeslot = '11:00am to 11:30am' THEN 9
        WHEN timeslot = '11:30am to 12:00pm' THEN 10
        WHEN timeslot = '12:00pm to 12:30pm' THEN 11
        WHEN timeslot = '12:30pm to 1:00pm' THEN 12
        WHEN timeslot = '1:00pm to 1:30pm' THEN 13
        WHEN timeslot = '1:30pm to 2:00pm' THEN 14
        WHEN timeslot = '2:00pm to 2:30pm' THEN 15
        WHEN timeslot = '2:30pm to 3:00pm' THEN 16
        WHEN timeslot = '3:00pm to 3:30pm' THEN 17
        WHEN timeslot = '3:30pm to 4:00pm' THEN 18
        WHEN timeslot = '4:00pm to 4:30pm' THEN 19
        WHEN timeslot = '4:30pm to 5:00pm' THEN 20
        WHEN timeslot = '5:00pm to 5:30pm' THEN 21
        WHEN timeslot = '5:30pm to 6:00pm' THEN 22
        WHEN timeslot = '6:00pm to 6:30pm' THEN 23
        WHEN timeslot = '6:30pm to 7:00pm' THEN 24
        WHEN timeslot = '7:00pm to 7:30pm' THEN 25
        WHEN timeslot = '7:30pm to 8:00pm' THEN 26
        
        -- Two Wheeler Slots
        WHEN timeslot = '7:00am to 7:45am' THEN 27
        WHEN timeslot = '7:45am to 8:30am' THEN 28
        WHEN timeslot = '8:30am to 9:15am' THEN 29
        WHEN timeslot = '9:15am to 10:00am' THEN 30
        WHEN timeslot = '10:00am to 10:45am' THEN 31
        WHEN timeslot = '10:45am to 11:30am' THEN 32
        WHEN timeslot = '11:30am to 12:15pm' THEN 33
        WHEN timeslot = '12:15pm to 1:00pm' THEN 34
        WHEN timeslot = '1:00pm to 1:45pm' THEN 35
        WHEN timeslot = '1:45pm to 2:30pm' THEN 36
        WHEN timeslot = '2:30pm to 3:15pm' THEN 37
        WHEN timeslot = '3:15pm to 4:00pm' THEN 38
        WHEN timeslot = '4:00pm to 4:45pm' THEN 39
        WHEN timeslot = '4:45pm to 5:30pm' THEN 40
        WHEN timeslot = '5:30pm to 6:15pm' THEN 41
        WHEN timeslot = '6:15pm to 7:00pm' THEN 42
        WHEN timeslot = '7:00pm to 7:45pm' THEN 43
        WHEN timeslot = '7:45pm to 8:30pm' THEN 44
        ELSE 45
    END";

    // Timeslot query execution
    $stmt = $conn->prepare($timeslot_query);
    if (!$stmt) {
        throw new Exception("Failed to prepare timeslot query: " . $conn->error);
    }
    if (!empty($params)) {
        // For timeslot query, we need to duplicate the parameters for the subquery
        $combined_params = array_merge($params, $params);
        $combined_types = $param_types . $param_types;
        if (!$stmt->bind_param($combined_types, ...$combined_params)) {
            throw new Exception("Failed to bind parameters for timeslot query: " . $stmt->error);
        }
    }
    $stmt->execute();
    $timeslot_result = $stmt->get_result();

    $timeslot_labels = [];
    $timeslot_values = [];
    $timeslot_insights = [];
    while ($row = $timeslot_result->fetch_assoc()) {
        $timeslot_labels[] = $row['label'];
        $timeslot_values[] = intval($row['value']);
        $timeslot_insights[] = [
            'unique_days' => intval($row['unique_days']),
            'unique_vehicles' => intval($row['unique_vehicles']),
            'unique_trainers' => intval($row['unique_trainers']),
            'total_revenue' => round(floatval($row['total_revenue']), 2),
            'avg_revenue' => round(floatval($row['avg_revenue']), 2),
            'pending_payments' => intval($row['pending_payments']),
            'slot_utilization' => round(floatval($row['slot_utilization']), 2)
        ];
    }

    // 4. Additional Business Metrics
    $metrics_query = "SELECT
        COUNT(DISTINCT trainername) as total_trainers,
        COUNT(DISTINCT DATE(date)) as total_active_days,
        SUM(CASE WHEN dueamount = 0 THEN 1 ELSE 0 END) as fully_paid_bookings,
        SUM(CASE WHEN dueamount > 0 THEN 1 ELSE 0 END) as partial_paid_bookings,
        AVG(CASE WHEN dueamount > 0 THEN dueamount ELSE NULL END) as avg_pending_amount,
        COUNT(DISTINCT vehicle) as unique_vehicles,
        (COUNT(*) / COUNT(DISTINCT DATE(date))) as avg_daily_bookings
        FROM cust_details
        WHERE $where_clause";

    // Metrics query execution
    $stmt = $conn->prepare($metrics_query);
    if (!$stmt) {
        throw new Exception("Failed to prepare metrics query: " . $conn->error);
    }
    if (!empty($params)) {
        if (!$stmt->bind_param($param_types, ...$params)) {
            throw new Exception("Failed to bind parameters for metrics query: " . $stmt->error);
        }
    }
    $stmt->execute();
    $metrics_result = $stmt->get_result();
    $metrics_data = $metrics_result->fetch_assoc();

    // Calculate average sale
    $average_sale = $total_customers > 0 ? round($total_amount / $total_customers, 2) : 0;

    // 5. Advanced Customer Analytics
    $customer_analytics_query = "SELECT 
        COUNT(DISTINCT CASE WHEN DATEDIFF(CURRENT_DATE, date) <= 30 THEN id END) as new_customers_30d,
        COUNT(DISTINCT CASE WHEN DATEDIFF(CURRENT_DATE, date) <= 90 THEN id END) as new_customers_90d,
        AVG(DATEDIFF(endedAT, startedAT)) as avg_training_duration,
        COUNT(DISTINCT CASE WHEN newlicence = 'Applied' THEN id END) as new_license_count,
        COUNT(DISTINCT CASE WHEN newlicence = 'Not Applied' THEN id END) as existing_license_count,
        MAX(totalamount) as highest_payment,
        MIN(totalamount) as lowest_payment,
        COUNT(DISTINCT address) as unique_locations
        FROM cust_details
        WHERE $where_clause";

    $stmt = $conn->prepare($customer_analytics_query);
    if (!empty($params)) {
        $stmt->bind_param($param_types, ...$params);
    }
    $stmt->execute();
    $customer_analytics = $stmt->get_result()->fetch_assoc();

    // 6. Trainer Performance Analytics
    $trainer_analytics_query = "SELECT 
        trainername,
        COUNT(*) as total_students,
        SUM(CAST(totalamount AS DECIMAL(10,2))) as total_revenue,
        SUM(CAST(dueamount AS DECIMAL(10,2))) as total_dues,
        COUNT(DISTINCT vehicle) as vehicle_types_handled,
        COUNT(DISTINCT timeslot) as time_slots_covered,
        AVG(DATEDIFF(endedAT, startedAT)) as avg_training_days
        FROM cust_details
        WHERE $where_clause
        GROUP BY trainername
        ORDER BY total_revenue DESC";

    $stmt = $conn->prepare($trainer_analytics_query);
    if (!empty($params)) {
        $stmt->bind_param($param_types, ...$params);
    }
    $stmt->execute();
    $trainer_analytics_result = $stmt->get_result();
    $trainer_analytics = [];
    while ($row = $trainer_analytics_result->fetch_assoc()) {
        $trainer_analytics[] = $row;
    }

    // 7. Time Slot Efficiency Analysis
    $timeslot_efficiency_query = "SELECT 
        timeslot,
        COUNT(*) as booking_count,
        COUNT(DISTINCT DATE(date)) as unique_days,
        COUNT(DISTINCT trainername) as trainers_count,
        SUM(CAST(totalamount AS DECIMAL(10,2))) as revenue,
        AVG(CAST(totalamount AS DECIMAL(10,2))) as avg_revenue_per_booking,
        COUNT(DISTINCT vehicle) as vehicle_types
        FROM cust_details
        WHERE $where_clause
        GROUP BY timeslot
        ORDER BY booking_count DESC";

    $stmt = $conn->prepare($timeslot_efficiency_query);
    if (!empty($params)) {
        $stmt->bind_param($param_types, ...$params);
    }
    $stmt->execute();
    $timeslot_efficiency_result = $stmt->get_result();
    $timeslot_efficiency = [];
    while ($row = $timeslot_efficiency_result->fetch_assoc()) {
        $timeslot_efficiency[] = $row;
    }

    // 8. Payment Analytics
    $payment_analytics_query = "SELECT 
        payment_method,
        COUNT(*) as transaction_count,
        SUM(CAST(totalamount AS DECIMAL(10,2))) as total_amount,
        SUM(CAST(paidamount AS DECIMAL(10,2))) as collected_amount,
        SUM(CAST(dueamount AS DECIMAL(10,2))) as pending_amount,
        AVG(CAST(totalamount AS DECIMAL(10,2))) as avg_transaction_value,
        COUNT(DISTINCT DATE(date)) as active_days
        FROM cust_details
        WHERE $where_clause
        GROUP BY payment_method";

    $stmt = $conn->prepare($payment_analytics_query);
    if (!empty($params)) {
        $stmt->bind_param($param_types, ...$params);
    }
    $stmt->execute();
    $payment_analytics_result = $stmt->get_result();
    $payment_analytics = [];
    while ($row = $payment_analytics_result->fetch_assoc()) {
        $payment_analytics[] = $row;
    }

    // 9. Due Amount Aging Analysis
    $dues_aging_query = "SELECT 
        CASE 
            WHEN DATEDIFF(CURRENT_DATE, date) <= 30 THEN '0-30 days'
            WHEN DATEDIFF(CURRENT_DATE, date) <= 60 THEN '31-60 days'
            WHEN DATEDIFF(CURRENT_DATE, date) <= 90 THEN '61-90 days'
            ELSE 'Over 90 days'
        END as aging_period,
        COUNT(*) as customer_count,
        SUM(CAST(dueamount AS DECIMAL(10,2))) as total_dues
        FROM cust_details
        WHERE CAST(dueamount AS DECIMAL(10,2)) > 0 AND $where_clause
        GROUP BY aging_period
        ORDER BY 
        CASE aging_period
            WHEN '0-30 days' THEN 1
            WHEN '31-60 days' THEN 2
            WHEN '61-90 days' THEN 3
            ELSE 4
        END";

    $stmt = $conn->prepare($dues_aging_query);
    if (!empty($params)) {
        $stmt->bind_param($param_types, ...$params);
    }
    $stmt->execute();
    $dues_aging_result = $stmt->get_result();
    $dues_aging = [];
    while ($row = $dues_aging_result->fetch_assoc()) {
        $dues_aging[] = $row;
    }

    // Prepare response with enhanced insights
    $response = [
        'success' => true,
        'months' => $months,
        'revenue' => [
            'total' => $total_amounts,
            'paid' => $paid_amounts,
            'due' => $due_amounts
        ],
        'customers' => $customers,
        'distributions' => [
            'vehicles' => [
                'labels' => $vehicle_labels,
                'values' => $vehicle_values,
                'insights' => $vehicle_insights
            ],
            'payments' => [
                'labels' => $payment_labels,
                'values' => $payment_values,
                'insights' => $payment_insights
            ],
            'timeslots' => [
                'labels' => $timeslot_labels,
                'values' => $timeslot_values,
                'insights' => $timeslot_insights
            ]
        ],
        'stats' => [
            'totalAmount' => $total_amount,
            'totalPaid' => $total_paid,
            'totalDue' => $total_due,
            'totalCustomers' => $total_customers,
            'averageSale' => $average_sale,
            'monthlyGrowth' => $monthly_growth,
            'yearlyGrowth' => $yearly_growth,
            'collectionRate' => $total_amount > 0 ? round(($total_paid / $total_amount) * 100, 2) : 0,
            'totalTrainers' => intval($metrics_data['total_trainers']),
            'totalActiveDays' => intval($metrics_data['total_active_days']),
            'fullyPaidBookings' => intval($metrics_data['fully_paid_bookings']),
            'partialPaidBookings' => intval($metrics_data['partial_paid_bookings']),
            'avgPendingAmount' => round(floatval($metrics_data['avg_pending_amount']), 2),
            'uniqueVehicles' => intval($metrics_data['unique_vehicles']),
            'avgDailyBookings' => round(floatval($metrics_data['avg_daily_bookings']), 2)
        ],
        'advanced_analytics' => [
            'customer_insights' => $customer_analytics,
            'trainer_performance' => $trainer_analytics,
            'timeslot_efficiency' => $timeslot_efficiency,
            'payment_analytics' => $payment_analytics,
            'dues_aging' => $dues_aging,
            'key_metrics' => [
                'customer_acquisition' => [
                    'last_30_days' => $customer_analytics['new_customers_30d'],
                    'last_90_days' => $customer_analytics['new_customers_90d']
                ],
                'training_metrics' => [
                    'avg_duration' => round($customer_analytics['avg_training_duration'], 1),
                    'new_vs_existing' => [
                        'new' => $customer_analytics['new_license_count'],
                        'existing' => $customer_analytics['existing_license_count']
                    ]
                ],
                'geographic_reach' => $customer_analytics['unique_locations'],
                'price_range' => [
                    'highest' => $customer_analytics['highest_payment'],
                    'lowest' => $customer_analytics['lowest_payment']
                ]
            ]
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    error_log("Analytics API Error: " . $e->getMessage());
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
                'get_data' => $_GET,
                'post_data' => $_POST
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