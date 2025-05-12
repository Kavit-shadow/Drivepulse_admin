<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper function to send JSON responses
function sendResponse($success, $message = '', $data = null) {
    header('Content-Type: application/json');
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit;
}

// Debug log function
function debug_log($message, $data = null) {
    $log = date('Y-m-d H:i:s') . " - " . $message;
    if ($data) {
        $log .= " - Data: " . print_r($data, true);
    }
    error_log($log . "\n", 3, "finance_tracker_debug.log");
}

// Log session data
debug_log("Session data at start of request", $_SESSION);

header('Content-Type: application/json');
require_once '../../config.php';
require_once 'backend.php';

// Check for authentication before processing any requests
if (!isset($_SESSION['admin_ID'])) {
    debug_log("Authentication failed - no admin_ID in session");
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Authentication required',
        'debug' => 'No admin_ID in session'
    ]);
    exit;
}


     // Helper function for budget status
     function getBudgetStatus($percentage) {
        if ($percentage >= 100) return 'exceeded';
        if ($percentage >= 90) return 'critical';
        if ($percentage >= 75) return 'warning';
        return 'good';
    }

// Helper function to check if user is a super admin
function isSuperAdmin($user_id) {
    $super_admin_ids = [5, 15];
    return in_array($user_id, $super_admin_ids);
}

try {
    // Initialize database connection
    $finance = new FinanceTracker();

    // Handle CORS
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // Get the request method
    $method = $_SERVER['REQUEST_METHOD'];

    // Get action from POST or GET
    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    debug_log("Request received", [
        'method' => $method,
        'action' => $action,
        'GET' => $_GET,
        'POST' => $_POST
    ]);

    switch ($action) {
        case 'transactions':
            $filters = [
                'start_date' => $_GET['start_date'] ?? null,
                'end_date' => $_GET['end_date'] ?? null,
                'category_id' => $_GET['category_id'] ?? null,
                'type' => $_GET['type'] ?? null,
                'payment_method' => $_GET['payment_method'] ?? null
            ];
            
            // Add user_id filter for super admins
            if (isset($_GET['user_id']) && isSuperAdmin($_SESSION['admin_ID'])) {
                $filters['user_id'] = intval($_GET['user_id']);
            }
            
            $result = $finance->getTransactions($filters);
            debug_log("Transactions retrieved", $result);
            echo json_encode([
                'success' => true, 
                'data' => $result,
                'is_super_admin' => isSuperAdmin($_SESSION['admin_ID'])
            ]);
            break;

        case 'get_transaction':
            try {
                if (!isset($_GET['id'])) {
                    throw new Exception("Transaction ID is required");
                }
                
                $transactionId = intval($_GET['id']);
                if ($transactionId <= 0) {
                    throw new Exception("Invalid transaction ID");
                }

                $transaction = $finance->getTransaction($transactionId);
                echo json_encode(['success' => true, 'data' => $transaction]);
                
            } catch (Exception $e) {
                error_log("API Error in get_transaction: " . $e->getMessage());
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            break;

        case 'categories':
            $type = isset($_GET['type']) ? $_GET['type'] : null;
            $result = $finance->getCategories($type);
            debug_log("Categories retrieved", ['type' => $type, 'result' => $result]);
            echo json_encode(['success' => true, 'data' => $result]);
            break;

        case 'summary':
            $result = $finance->getSummary();
            debug_log("Summary retrieved", $result);
            
            // Add super admin flag to the response
            $result['is_super_admin'] = isSuperAdmin($_SESSION['admin_ID']);
            
            echo json_encode(['success' => true, 'data' => $result]);
            break;

        case 'update_transaction':
            try {
                // Debug log the incoming data
                error_log("Update transaction request data: " . print_r($_POST, true));

                // Validate required fields
                $required_fields = ['transaction_id', 'type', 'category_id', 'amount', 'date', 'payment_method'];
                foreach ($required_fields as $field) {
                    if (!isset($_POST[$field]) || $_POST[$field] === '') {
                        throw new Exception("Missing required field: {$field}");
                    }
                }

                // Validate amount
                if (!is_numeric($_POST['amount']) || floatval($_POST['amount']) <= 0) {
                    throw new Exception("Invalid amount");
                }

                // Prepare data for update
                $updateData = [
                    'transaction_id' => intval($_POST['transaction_id']),
                    'type' => $_POST['type'],
                    'category_id' => intval($_POST['category_id']),
                    'amount' => floatval($_POST['amount']),
                    'date' => $_POST['date'],
                    'description' => $_POST['description'] ?? '',
                    'payment_method' => $_POST['payment_method']
                ];

                // Debug log the processed data
                error_log("Processed update data: " . print_r($updateData, true));

                // Call update method
                $result = $finance->updateTransaction($updateData);
                
                if ($result['success']) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Transaction updated successfully',
                        'debug_info' => $result['debug_info'] ?? null
                    ]);
                } else {
                    throw new Exception($result['error'] ?? 'Unknown error occurred');
                }
                
            } catch (Exception $e) {
                error_log("API Error in update_transaction: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'debug_info' => [
                        'post_data' => $_POST,
                        'error_details' => $e->getMessage()
                    ]
                ]);
            }
            break;

        case 'add_transaction':
            try {
                $data = $_POST;
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    throw new Exception("Invalid request method");
                }
                
                $result = $finance->addTransaction($data);
                echo json_encode($result);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;

        case 'delete_transaction':
            try {
                error_log("Delete transaction request received: " . print_r($_POST, true));
                
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    throw new Exception("Invalid request method");
                }
                
                $transaction_id = isset($_POST['transaction_id']) ? intval($_POST['transaction_id']) : null;
                
                if (!$transaction_id) {
                    error_log("Missing transaction_id in request");
                    throw new Exception("Transaction ID required for deletion");
                }
                
                error_log("Attempting to delete transaction ID: " . $transaction_id);
                $result = $finance->deleteTransaction($transaction_id);
                
                error_log("Delete operation result: " . print_r($result, true));
                echo json_encode($result);
                
            } catch (Exception $e) {
                error_log("Error in delete_transaction: " . $e->getMessage());
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            break;

        case 'add_category':
            try {
                // Get data from POST request
                $data = json_decode(file_get_contents('php://input'), true);
                
                // Validate required fields
                if (!isset($data['name']) || empty($data['name'])) {
                    throw new Exception('Category name is required');
                }
                if (!isset($data['type']) || empty($data['type'])) {
                    throw new Exception('Category type is required');
                }
                
                // Add category
                if ($finance->addCategory($data)) {
                    echo json_encode(['success' => true, 'message' => 'Category added successfully']);
                } else {
                    throw new Exception('Failed to add category');
                }
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            break;

        case 'delete_category':
            try {
                $data = json_decode(file_get_contents('php://input'), true);
                if (!isset($data['id'])) {
                    throw new Exception("Category ID is required");
                }
                
                $categoryId = intval($data['id']);
                if ($categoryId <= 0) {
                    throw new Exception("Invalid category ID");
                }

                $result = $finance->deleteCategory($categoryId);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                error_log("API Error in delete_category: " . $e->getMessage());
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            break;

        case 'chart_data':
            try {
                $filters = [
                    'period' => $_GET['period'] ?? 'monthly',
                    'data_type' => $_GET['data_type'] ?? 'all',
                    'start_date' => $_GET['start_date'] ?? null,
                    'end_date' => $_GET['end_date'] ?? null
                ];
                
                // Add user_id filter for super admins
                if (isset($_GET['user_id']) && isSuperAdmin($_SESSION['admin_ID'])) {
                    $filters['user_id'] = intval($_GET['user_id']);
                }
                
                $result = $finance->getChartData($filters);
                echo json_encode([
                    'success' => true, 
                    'data' => $result['data'], 
                    'stats' => $result['stats'],
                    'is_super_admin' => isSuperAdmin($_SESSION['admin_ID'])
                ]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            break;

        case 'export_data':
            try {
                require_once '../../vendor/autoload.php';
                
                $type = $_POST['type'] ?? 'all';
                $format = $_POST['format'] ?? 'excel';
                $startDate = $_POST['start_date'] ?? null;
                $endDate = $_POST['end_date'] ?? null;

                error_log("Export request received - Type: $type, Format: $format, Dates: $startDate to $endDate");

                $result = $finance->exportData([
                    'type' => $type,
                    'format' => $format,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]);

                if (empty($result)) {
                    throw new Exception("No data generated for export");
                }

                // Set appropriate headers based on format
                $filename = "finance_export_" . date('Y-m-d_His');
                
                switch($format) {
                    case 'excel':
                        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                        $filename .= '.xlsx';
                        break;
                    case 'csv':
                        header('Content-Type: text/csv');
                        $filename .= '.csv';
                        break;
                    case 'pdf':
                        header('Content-Type: application/pdf');
                        $filename .= '.pdf';
                        break;
                    default:
                        throw new Exception("Invalid export format");
                }

                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Cache-Control: max-age=0');
                header('Content-Length: ' . strlen($result));
                
                echo $result;
                exit;

            } catch (Exception $e) {
                error_log("Export API error: " . $e->getMessage());
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'error' => $e->getMessage(),
                    'details' => 'Check server logs for more information'
                ]);
                exit;
            }
            break;

        case 'set_budget':
            try {
                $data = json_decode(file_get_contents('php://input'), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Invalid JSON data: ' . json_last_error_msg());
                }

                if (!isset($data['category_id']) || !isset($data['amount']) || !isset($data['period'])) {
                    throw new Exception('Missing required fields: category_id, amount, and period are required');
                }

                if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
                    throw new Exception('Amount must be a positive number');
                }

                if (!in_array($data['period'], ['monthly', 'yearly'])) {
                    throw new Exception('Period must be either monthly or yearly');
                }

                // Check if budget already exists for this category
                $checkStmt = $conn->prepare("
                    SELECT budget_id 
                    FROM budgets 
                    WHERE user_id = ? AND category_id = ?
                ");
                $checkStmt->bind_param("ii", $_SESSION['admin_ID'], $data['category_id']);
                $checkStmt->execute();
                $existingBudget = $checkStmt->get_result()->fetch_assoc();
                $checkStmt->close();

                if ($existingBudget) {
                    // Update existing budget
                    $stmt = $conn->prepare("
                        UPDATE budgets 
                        SET amount = ?,
                            period = ?,
                            start_date = CURRENT_DATE
                        WHERE user_id = ? 
                        AND category_id = ?
                    ");
                    $stmt->bind_param("dsii", 
                        $data['amount'],
                        $data['period'],
                        $_SESSION['admin_ID'],
                        $data['category_id']
                    );
                } else {
                    // Insert new budget
                    $stmt = $conn->prepare("
                        INSERT INTO budgets (user_id, category_id, amount, period, start_date)
                        VALUES (?, ?, ?, ?, CURRENT_DATE)
                    ");
                    $stmt->bind_param("iids", 
                        $_SESSION['admin_ID'],
                        $data['category_id'],
                        $data['amount'],
                        $data['period']
                    );
                }

                if (!$stmt->execute()) {
                    throw new Exception('Failed to ' . ($existingBudget ? 'update' : 'set') . ' budget: ' . $stmt->error);
                }

                sendResponse(true, 'Budget ' . ($existingBudget ? 'updated' : 'set') . ' successfully');
            } catch (Exception $e) {
                debug_log("Error in set_budget: " . $e->getMessage());
                sendResponse(false, $e->getMessage());
            }
            break;

        case 'get_budgets':
            try {
                $stmt = $conn->prepare("
                    SELECT 
                        c.category_id,
                        c.name as category_name,
                        b.amount as budget_amount,
                        b.period,
                        b.start_date,
                        COALESCE(SUM(t.amount), 0) as spent_amount
                    FROM budgets b
                    JOIN categories c ON b.category_id = c.category_id
                    LEFT JOIN transactions t ON 
                        t.category_id = b.category_id AND 
                        t.user_id = b.user_id AND
                        t.type = 'expense' AND
                        CASE 
                            WHEN b.period = 'monthly' THEN 
                                t.transaction_date >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')
                            ELSE 
                                t.transaction_date >= DATE_FORMAT(CURRENT_DATE, '%Y-01-01')
                        END
                    WHERE b.user_id = ?
                    GROUP BY b.budget_id, c.category_id, c.name, b.amount, b.period, b.start_date
                    ORDER BY c.name ASC
                ");

                $stmt->bind_param("i", $_SESSION['admin_ID']);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to retrieve budgets: " . $stmt->error);
                }

                $result = $stmt->get_result();
                $budgets = [];
                
                while ($row = $result->fetch_assoc()) {
                    $spent = floatval($row['spent_amount']);
                    $total = floatval($row['budget_amount']);
                    $percentage = $total > 0 ? round(($spent / $total) * 100, 1) : 0;

                    $budgets[$row['category_name']] = [
                        'category_id' => $row['category_id'],
                        'amount' => $total,
                        'spent' => $spent,
                        'period' => $row['period'],
                        'percentage' => $percentage,
                        'remaining' => max(0, $total - $spent),
                        'start_date' => $row['start_date'],
                        'status' => getBudgetStatus($percentage)
                    ];
                }

                sendResponse(true, 'Budgets retrieved successfully', $budgets);
            } catch (Exception $e) {
                debug_log("Error in get_budgets: " . $e->getMessage());
                sendResponse(false, 'Error retrieving budgets: ' . $e->getMessage());
            }
            break;

        case 'delete_budget':
            try {
                $data = json_decode(file_get_contents('php://input'), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Invalid JSON data: ' . json_last_error_msg());
                }

                if (!isset($data['category_id'])) {
                    throw new Exception('Category ID is required');
                }

                // Delete the budget
                $stmt = $conn->prepare("
                    DELETE FROM budgets 
                    WHERE user_id = ? AND category_id = ?
                ");
                
                $stmt->bind_param("ii", $_SESSION['admin_ID'], $data['category_id']);
                
                if (!$stmt->execute()) {
                    throw new Exception('Failed to delete budget: ' . $stmt->error);
                }

                if ($stmt->affected_rows === 0) {
                    throw new Exception('Budget not found or already deleted');
                }

                sendResponse(true, 'Budget deleted successfully');
            } catch (Exception $e) {
                debug_log("Error in delete_budget: " . $e->getMessage());
                sendResponse(false, $e->getMessage());
            }
            break;

        case 'revenue_breakdown':
            try {
                $params = [
                    'year' => isset($_GET['year']) ? intval($_GET['year']) : null,
                    'month' => isset($_GET['month']) ? intval($_GET['month']) : null,
                    'week' => isset($_GET['week']) ? intval($_GET['week']) : null,
                    'day' => isset($_GET['day']) ? $_GET['day'] : null
                ];

                // Add user_id filter for super admins
                if (isset($_GET['user_id']) && isSuperAdmin($_SESSION['admin_ID'])) {
                    $params['user_id'] = intval($_GET['user_id']);
                }

                $result = $finance->getRevenueBreakdown($params);
                echo json_encode([
                    'success' => true,
                    'data' => $result['data'],
                    'available_years' => $result['available_years'],
                    'current_filters' => $result['current_filters'],
                    'is_super_admin' => isSuperAdmin($_SESSION['admin_ID'])
                ]);
            } catch (Exception $e) {
                debug_log("Error in revenue_breakdown: " . $e->getMessage());
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            break;

        default:
            error_log("Invalid action requested: " . $action);
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action: ' . $action]);
            break;
    }
} catch (PDOException $e) {
    debug_log("Database Error", ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => 'Database error occurred',
        'debug_message' => $e->getMessage()
    ]);
} catch (Exception $e) {
    debug_log("Application Error", ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
