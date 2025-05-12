<?php
require_once "../../config.php";
require_once('../../vendor/autoload.php'); // PhpSpreadsheet
require_once('../../vendor/tecnickcom/tcpdf/tcpdf.php'); // TCPDF

// -- Categories Table
// CREATE TABLE categories (
//     category_id INT AUTO_INCREMENT PRIMARY KEY,
//     user_id INT NOT NULL,
//     name VARCHAR(100) NOT NULL,
//     type ENUM('income', 'expense') NOT NULL,
//     is_default BOOLEAN DEFAULT FALSE,
//     FOREIGN KEY (user_id) REFERENCES users(user_id)
// );

// -- Transactions Table
// CREATE TABLE transactions (
//     transaction_id INT AUTO_INCREMENT PRIMARY KEY,
//     user_id INT NOT NULL,
//     category_id INT,
//     amount DECIMAL(10,2) NOT NULL,
//     transaction_date DATE NOT NULL,
//     description TEXT,
//     type ENUM('income', 'expense') NOT NULL,
//     source_table VARCHAR(50),
//     source_id INT,
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     FOREIGN KEY (user_id) REFERENCES users(user_id),
//     FOREIGN KEY (category_id) REFERENCES categories(category_id)
// );

// -- Budgets Table
// CREATE TABLE budgets (
//     budget_id INT AUTO_INCREMENT PRIMARY KEY,
//     user_id INT NOT NULL,
//     category_id INT,
//     amount DECIMAL(10,2) NOT NULL,
//     period ENUM('monthly', 'yearly') NOT NULL,
//     start_date DATE NOT NULL,
//     end_date DATE,
//     notification_threshold INT DEFAULT 80,
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     FOREIGN KEY (user_id) REFERENCES users(user_id),
//     FOREIGN KEY (category_id) REFERENCES categories(category_id)
// );

// -- Default Categories
// INSERT INTO categories (name, type, is_default) VALUES 
// ('Salary', 'income', TRUE),
// ('Freelance', 'income', TRUE),
// ('Investments', 'income', TRUE),
// ('Food', 'expense', TRUE),
// ('Transport', 'expense', TRUE),
// ('Utilities', 'expense', TRUE),
// ('Other', 'expense', TRUE);




class FinanceTracker {
    private $conn;
    private $user_id;
    private $is_super_admin;
    private $super_admin_ids = [5, 15]; // Array of super admin IDs

    public function __construct() {
        global $conn;
        try {
            // Start session if not already started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Debug log for session and connection
            error_log("Session data: " . print_r($_SESSION, true));
            error_log("Connection state: " . ($conn ? "Connected" : "Not connected"));

            // Check database connection
            if (!$conn) {
                error_log("Database connection is null");
                throw new Exception("Database connection failed - connection is null");
            }

            $this->conn = $conn;
            
            // Check for admin authentication
            if (!isset($_SESSION['admin_ID'])) {
                error_log("Admin ID not found in session");
                throw new Exception("User not authenticated");
            }

            $this->user_id = $_SESSION['admin_ID'];
            
            // Check if current user is a super admin
            $this->is_super_admin = in_array($this->user_id, $this->super_admin_ids);
            error_log("User ID: " . $this->user_id . ", Is Super Admin: " . ($this->is_super_admin ? "Yes" : "No"));
            
            // Test database connection
            $test = $this->conn->query("SELECT 1");
            if (!$test) {
                throw new Exception("Database connection test failed: " . $this->conn->error);
            }
            
            // Log successful initialization
            error_log("FinanceTracker initialized successfully for admin_ID: " . $this->user_id);
            
        } catch (Exception $e) {
            error_log("FinanceTracker initialization error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new Exception("FinanceTracker initialization failed: " . $e->getMessage());
        }
    }

    public function getCategories($type = null) {
        try {
            error_log("Getting categories with type: " . ($type ?? 'null'));

            // Build the base query
            $sql = "SELECT c.*, 
                    COALESCE(t.transaction_count, 0) as transaction_count 
                    FROM categories c 
                    LEFT JOIN (
                        SELECT category_id, COUNT(*) as transaction_count 
                        FROM transactions 
                        GROUP BY category_id
                    ) t ON c.category_id = t.category_id 
                    WHERE 1=1";
            
            $params = [];
            $types = "";

            // Add type filter if specified
            if ($type !== null && $type !== '') {
                $sql .= " AND c.type = ?";
                $params[] = $type;
                $types .= "s";
            }

            $sql .= " ORDER BY c.is_default DESC, c.name ASC";

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $categories = [];
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }

            $stmt->close();
            error_log("Retrieved categories: " . print_r($categories, true));
            return $categories;

        } catch (Exception $e) {
            error_log("Error in getCategories: " . $e->getMessage());
            throw $e;
        }
    }

    public function getTransactions($filters = []) {
        try {
            $sql = "SELECT t.*, c.name as category_name 
                   FROM transactions t 
                   LEFT JOIN categories c ON t.category_id = c.category_id 
                   WHERE ";
            
            // Super admins can see all transactions, regular users only see their own
            if ($this->is_super_admin) {
                $sql .= "1=1"; // No user_id filter for super admins
                $params = [];
                $types = "";
            } else {
                $sql .= "t.user_id = ?";
                $params = [$this->user_id];
                $types = "i";
            }

            // Add date range filter
            if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                $startDate = date('Y-m-d', strtotime($filters['start_date']));
                $endDate = date('Y-m-d', strtotime($filters['end_date']));
                
                if ($startDate && $endDate) {
                    $sql .= " AND DATE(t.transaction_date) BETWEEN ? AND ?";
                    $params[] = $startDate;
                    $params[] = $endDate;
                    $types .= "ss";
                }
            }

            // Add category filter
            if (!empty($filters['category_id'])) {
                $sql .= " AND t.category_id = ?";
                $params[] = $filters['category_id'];
                $types .= "i";
            }

            // Add type filter
            if (!empty($filters['type'])) {
                $sql .= " AND t.type = ?";
                $params[] = $filters['type'];
                $types .= "s";
            }

            // Add payment method filter - updated to handle it properly
            if (!empty($filters['payment_method']) && in_array($filters['payment_method'], ['cash', 'bank'])) {
                $sql .= " AND t.payment_method = ?";
                $params[] = strtolower($filters['payment_method']);
                $types .= "s";
                error_log("Adding payment method filter: " . $filters['payment_method']);
            }

            // Add user filter for super admins if specified
            if ($this->is_super_admin && !empty($filters['user_id'])) {
                $sql .= " AND t.user_id = ?";
                $params[] = $filters['user_id'];
                $types .= "i";
            }

            $sql .= " ORDER BY t.transaction_date DESC";

            error_log("Transaction query: " . $sql);
            error_log("Parameters: " . print_r($params, true));
            error_log("Types: " . $types);

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $transactions = [];
            while ($row = $result->fetch_assoc()) {
                // For super admins, add user_id to the result for reference
                if ($this->is_super_admin) {
                    $row['user_id'] = $row['user_id'] ?? null;
                }
                $transactions[] = $row;
            }

            $stmt->close();
            return $transactions;

        } catch (Exception $e) {
            error_log("Error in getTransactions: " . $e->getMessage());
            throw $e;
        }
    }

    public function getTransaction($id) {
        try {
            error_log("Getting transaction ID: " . $id);

            $sql = "SELECT t.*, c.name as category_name, c.type as category_type 
                    FROM transactions t 
                    JOIN categories c ON t.category_id = c.category_id 
                    WHERE t.transaction_id = ?";
            
            // Regular users can only see their own transactions
            if (!$this->is_super_admin) {
                $sql .= " AND t.user_id = ?";
            }

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("Prepare failed: " . $this->conn->error);
                throw new Exception("Database error while getting transaction");
            }

            if ($this->is_super_admin) {
                $stmt->bind_param("i", $id);
            } else {
                $stmt->bind_param("ii", $id, $this->user_id);
            }
            
            if (!$stmt->execute()) {
                error_log("Execute failed: " . $stmt->error);
                throw new Exception("Database error while getting transaction");
            }

            $result = $stmt->get_result();
            $transaction = $result->fetch_assoc();
            
            if (!$transaction) {
                throw new Exception("Transaction not found");
            }

            $stmt->close();
            error_log("Retrieved transaction: " . print_r($transaction, true));
            return $transaction;

        } catch (Exception $e) {
            error_log("Error in getTransaction: " . $e->getMessage());
            throw $e;
        }
    }

    public function addTransaction($data) {
        try {
            // Debug log the incoming data
            error_log("Raw transaction data: " . print_r($data, true));

            // Validate payment_method
            $payment_method = isset($data['payment_method']) ? strtolower(trim($data['payment_method'])) : 'cash';
            if (!in_array($payment_method, ['cash', 'bank'])) {
                error_log("Invalid payment method received: " . $payment_method);
                throw new Exception("Invalid payment method. Must be 'cash' or 'bank'");
            }

            // Validate required fields
            $required_fields = ['type', 'category_id', 'amount', 'date'];
            foreach ($required_fields as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    error_log("Missing required field: {$field}");
                    throw new Exception("Missing required field: {$field}");
                }
            }

            // Validate amount is numeric and positive
            if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
                error_log("Invalid amount value: " . $data['amount']);
                throw new Exception("Amount must be a valid positive number");
            }

            // Validate date format
            $date = date('Y-m-d', strtotime($data['date']));
            if (!$date) {
                error_log("Invalid date format: " . $data['date']);
                throw new Exception("Invalid date format");
            }

            // Validate category exists and matches the transaction type
            $checkCatSql = "SELECT category_id FROM categories WHERE category_id = ? AND type = ?";
            $checkCatStmt = $this->conn->prepare($checkCatSql);
            if (!$checkCatStmt) {
                error_log("Category check prepare failed: " . $this->conn->error);
                throw new Exception("Database error while validating category");
            }

            $checkCatStmt->bind_param("is", $data['category_id'], $data['type']);
            $checkCatStmt->execute();
            $catResult = $checkCatStmt->get_result();
            
            if ($catResult->num_rows === 0) {
                error_log("Invalid category_id or type mismatch: " . $data['category_id'] . ", type: " . $data['type']);
                throw new Exception("Invalid category selected for this transaction type");
            }
            $checkCatStmt->close();

            // Prepare insert statement
            $sql = "INSERT INTO transactions (
                user_id, 
                category_id, 
                amount, 
                payment_method, 
                transaction_date, 
                description, 
                type
            ) VALUES (?, ?, ?, ?, ?, ?, ?)";

            error_log("Preparing SQL: " . $sql);
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("Prepare failed: " . $this->conn->error);
                throw new Exception("Database error while preparing transaction");
            }

            // Format and validate data
            $description = isset($data['description']) ? $data['description'] : '';
            $amount = floatval($data['amount']);

            error_log("Binding parameters: " . print_r([
                'user_id' => $this->user_id,
                'category_id' => $data['category_id'],
                'amount' => $amount,
                'payment_method' => $payment_method,
                'date' => $date,
                'description' => $description,
                'type' => $data['type']
            ], true));

            $stmt->bind_param("iidssss",
                $this->user_id,
                $data['category_id'],
                $amount,
                $payment_method,
                $date,
                $description,
                $data['type']
            );

            // Execute and check result
            if (!$stmt->execute()) {
                error_log("Execute failed: " . $stmt->error);
                throw new Exception("Failed to add transaction: " . $stmt->error);
            }

            $transaction_id = $stmt->insert_id;
            $stmt->close();
            
            error_log("Transaction added successfully - ID: " . $transaction_id);
            
            // Return detailed response
            $response = [
                'success' => true,
                'transaction_id' => $transaction_id,
                'message' => 'Transaction added successfully',
                'debug_info' => [
                    'payment_method' => $payment_method,
                    'amount' => $amount,
                    'date' => $date
                ]
            ];
            
            error_log("Returning response: " . print_r($response, true));
            return $response;

        } catch (Exception $e) {
            error_log("Error in addTransaction: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function deleteTransaction($id) {
        try {
            error_log("Starting deleteTransaction for ID: " . $id);

            // Validate transaction ID
            if (empty($id) || !is_numeric($id)) {
                error_log("Invalid transaction ID provided: " . print_r($id, true));
                throw new Exception("Invalid transaction ID");
            }

            // First verify the transaction exists and belongs to the user (if not super admin)
            $checkSql = "SELECT transaction_id, type, amount, user_id FROM transactions WHERE transaction_id = ?";
            
            // Regular users can only delete their own transactions
            if (!$this->is_super_admin) {
                $checkSql .= " AND user_id = ?";
            }
            
            error_log("Executing verification query: " . $checkSql);
            
            $checkStmt = $this->conn->prepare($checkSql);
            if (!$checkStmt) {
                error_log("Prepare check failed: " . $this->conn->error);
                throw new Exception("Database error while verifying transaction");
            }

            if ($this->is_super_admin) {
                $checkStmt->bind_param("i", $id);
            } else {
                $checkStmt->bind_param("ii", $id, $this->user_id);
            }
            
            if (!$checkStmt->execute()) {
                error_log("Execute check failed: " . $checkStmt->error);
                throw new Exception("Database error while verifying transaction");
            }

            $result = $checkStmt->get_result();
            $transaction = $result->fetch_assoc();
            
            if (!$transaction) {
                error_log("Transaction not found or unauthorized - ID: " . $id . ", User: " . $this->user_id);
                throw new Exception("Transaction not found or unauthorized");
            }

            error_log("Found transaction to delete: " . print_r($transaction, true));
            $checkStmt->close();

            // Proceed with deletion
            $sql = "DELETE FROM transactions WHERE transaction_id = ?";
            
            // Regular users can only delete their own transactions
            if (!$this->is_super_admin) {
                $sql .= " AND user_id = ?";
            }
            
            error_log("Executing delete query: " . $sql);

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("Prepare delete failed: " . $this->conn->error);
                throw new Exception("Database error while deleting transaction");
            }

            if ($this->is_super_admin) {
                $stmt->bind_param("i", $id);
            } else {
                $stmt->bind_param("ii", $id, $this->user_id);
            }
            
            if (!$stmt->execute()) {
                error_log("Execute delete failed: " . $stmt->error);
                throw new Exception("Failed to delete transaction: " . $stmt->error);
            }

            if ($stmt->affected_rows === 0) {
                error_log("No rows affected when deleting transaction: " . $id);
                throw new Exception("No transaction was deleted");
            }

            $stmt->close();
            error_log("Transaction deleted successfully - ID: " . $id);
            
            return [
                'success' => true, 
                'message' => 'Transaction deleted successfully',
                'debug_info' => [
                    'transaction_id' => $id,
                    'type' => $transaction['type'],
                    'amount' => $transaction['amount'],
                    'user_id' => $transaction['user_id']
                ]
            ];

        } catch (Exception $e) {
            error_log("Error in deleteTransaction: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function addCategory($data) {
        try {
            $sql = "INSERT INTO categories (user_id, name, type) VALUES (?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("iss", $this->user_id, $data['name'], $data['type']);

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $stmt->close();
            return true;

        } catch (Exception $e) {
            error_log("Error in addCategory: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateTransaction($data) {
        try {
            // Validate required fields
            if (!isset($data['transaction_id'])) {
                throw new Exception("Transaction ID is required");
            }

            // Validate and sanitize payment method
            $payment_method = isset($data['payment_method']) ? strtolower(trim($data['payment_method'])) : 'cash';
            if (!in_array($payment_method, ['cash', 'bank'])) {
                error_log("Invalid payment method in update: " . $payment_method);
                throw new Exception("Invalid payment method. Must be 'cash' or 'bank'");
            }

            // Prepare the SQL query
            $sql = "UPDATE transactions SET 
                    type = ?,
                    category_id = ?,
                    amount = ?,
                    payment_method = ?,
                    transaction_date = ?,
                    description = ?
                    WHERE transaction_id = ? AND user_id = ?";

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            // Format date
            $date = date('Y-m-d', strtotime($data['date']));
            
            // Bind parameters
            $stmt->bind_param(
                "sidsssis",
                $data['type'],
                $data['category_id'],
                $data['amount'],
                $payment_method,
                $date,
                $data['description'],
                $data['transaction_id'],
                $this->user_id
            );

            // Debug log
            error_log("Updating transaction with data: " . print_r([
                'type' => $data['type'],
                'category_id' => $data['category_id'],
                'amount' => $data['amount'],
                'payment_method' => $payment_method,
                'date' => $date,
                'description' => $data['description'],
                'transaction_id' => $data['transaction_id'],
                'user_id' => $this->user_id
            ], true));

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            return [
                'success' => true,
                'message' => 'Transaction updated successfully',
                'debug_info' => [
                    'payment_method' => $payment_method,
                    'affected_rows' => $stmt->affected_rows
                ]
            ];

        } catch (Exception $e) {
            error_log("Error in updateTransaction: " . $e->getMessage());
            throw $e;
        }
    }

    public function getSummary() {
        try {
            error_log("Starting summary calculation");
            
            $currentMonth = date('m');
            $currentYear = date('Y');
            $lastMonth = date('m', strtotime('-1 month'));
            $lastMonthYear = date('Y', strtotime('-1 month'));
            $startOfYear = date('Y-01-01'); // Start of current year

            // 1. Get cumulative income from cust_details with payment method
            $custSql = "SELECT 
                COALESCE(SUM(paidamount), 0) as cust_income,
                COALESCE(SUM(totalamount), 0) as total_amount,
                COALESCE(SUM(dueamount), 0) as total_due,
                COALESCE(SUM(CASE WHEN payment_method = 'cash' THEN paidamount ELSE 0 END), 0) as cash_income,
                COALESCE(SUM(CASE WHEN payment_method = 'bank' THEN paidamount ELSE 0 END), 0) as bank_income
                FROM cust_details 
                WHERE YEAR(date) = ? AND date <= CURDATE()";

            // Current year cumulative customer income
            $stmt = $this->conn->prepare($custSql);
            $stmt->bind_param("s", $currentYear);
            $stmt->execute();
            $cumulativeCustData = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // Current month customer income
            $custMonthSql = "SELECT 
                COALESCE(SUM(paidamount), 0) as cust_income,
                COALESCE(SUM(totalamount), 0) as total_amount,
                COALESCE(SUM(dueamount), 0) as total_due,
                COALESCE(SUM(CASE WHEN payment_method = 'cash' THEN paidamount ELSE 0 END), 0) as cash_income,
                COALESCE(SUM(CASE WHEN payment_method = 'bank' THEN paidamount ELSE 0 END), 0) as bank_income
                FROM cust_details 
                WHERE MONTH(date) = ? AND YEAR(date) = ?";

            $stmt = $this->conn->prepare($custMonthSql);
            $stmt->bind_param("ss", $currentMonth, $currentYear);
            $stmt->execute();
            $currentCustData = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // Last month customer income
            $stmt = $this->conn->prepare($custMonthSql);
            $stmt->bind_param("ss", $lastMonth, $lastMonthYear);
            $stmt->execute();
            $lastCustData = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // 2. Get cumulative income/expense from transactions
            $cumTransSql = "SELECT 
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as trans_income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as trans_expense,
                COALESCE(SUM(CASE WHEN type = 'income' AND payment_method = 'cash' THEN amount ELSE 0 END), 0) as cash_income,
                COALESCE(SUM(CASE WHEN type = 'income' AND payment_method = 'bank' THEN amount ELSE 0 END), 0) as bank_income,
                COALESCE(SUM(CASE WHEN type = 'expense' AND payment_method = 'cash' THEN amount ELSE 0 END), 0) as cash_expense,
                COALESCE(SUM(CASE WHEN type = 'expense' AND payment_method = 'bank' THEN amount ELSE 0 END), 0) as bank_expense
                FROM transactions 
                WHERE ";
                
            // Super admins can see all transactions, regular users only see their own
            if ($this->is_super_admin) {
                $cumTransSql .= "1=1";
                $cumTransParams = [$currentYear];
                $cumTransTypes = "s";
            } else {
                $cumTransSql .= "user_id = ?";
                $cumTransParams = [$this->user_id, $currentYear];
                $cumTransTypes = "is";
            }
            
            $cumTransSql .= " AND YEAR(transaction_date) = ?
                AND transaction_date <= CURDATE()";

            // Get cumulative transactions for current year
            $stmt = $this->conn->prepare($cumTransSql);
            $stmt->bind_param($cumTransTypes, ...$cumTransParams);
            $stmt->execute();
            $cumulativeTransData = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // Get current month transactions
            $transSql = "SELECT 
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as trans_income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as trans_expense,
                COALESCE(SUM(CASE WHEN type = 'income' AND payment_method = 'cash' THEN amount ELSE 0 END), 0) as cash_income,
                COALESCE(SUM(CASE WHEN type = 'income' AND payment_method = 'bank' THEN amount ELSE 0 END), 0) as bank_income,
                COALESCE(SUM(CASE WHEN type = 'expense' AND payment_method = 'cash' THEN amount ELSE 0 END), 0) as cash_expense,
                COALESCE(SUM(CASE WHEN type = 'expense' AND payment_method = 'bank' THEN amount ELSE 0 END), 0) as bank_expense
                FROM transactions 
                WHERE ";
                
            // Super admins can see all transactions, regular users only see their own
            if ($this->is_super_admin) {
                $transSql .= "1=1";
                $transParams = [$currentYear, $currentMonth];
                $transTypes = "ss";
            } else {
                $transSql .= "user_id = ?";
                $transParams = [$this->user_id, $currentYear, $currentMonth];
                $transTypes = "iss";
            }
            
            $transSql .= " AND YEAR(transaction_date) = ? 
                AND MONTH(transaction_date) = ?";

            // Current month transactions
            $stmt = $this->conn->prepare($transSql);
            $stmt->bind_param($transTypes, ...$transParams);
            $stmt->execute();
            $currentTransData = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // Last month transactions
            if ($this->is_super_admin) {
                $lastTransParams = [$lastMonthYear, $lastMonth];
            } else {
                $lastTransParams = [$this->user_id, $lastMonthYear, $lastMonth];
            }
            
            $stmt = $this->conn->prepare($transSql);
            $stmt->bind_param($transTypes, ...$lastTransParams);
            $stmt->execute();
            $lastTransData = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // Calculate totals
            $currentIncome = floatval($currentCustData['cust_income']) + floatval($currentTransData['trans_income']);
            $lastIncome = floatval($lastCustData['cust_income']) + floatval($lastTransData['trans_income']);
            $currentExpense = floatval($currentTransData['trans_expense']);
            $lastExpense = floatval($lastTransData['trans_expense']);

            // Calculate cumulative totals
            $cumulativeIncome = floatval($cumulativeCustData['cust_income']) + floatval($cumulativeTransData['trans_income']);
            $cumulativeExpense = floatval($cumulativeTransData['trans_expense']);
            $cumulativeProfit = $cumulativeIncome - $cumulativeExpense;

            // Calculate payment method totals for current month
            $totalCashIncome = floatval($currentCustData['cash_income']) + floatval($currentTransData['cash_income']);
            $totalBankIncome = floatval($currentCustData['bank_income']) + floatval($currentTransData['bank_income']);
            $totalCashExpense = floatval($currentTransData['cash_expense']);
            $totalBankExpense = floatval($currentTransData['bank_expense']);

            // Calculate cumulative payment method totals
            $cumulativeCashIncome = floatval($cumulativeCustData['cash_income']) + floatval($cumulativeTransData['cash_income']);
            $cumulativeBankIncome = floatval($cumulativeCustData['bank_income']) + floatval($cumulativeTransData['bank_income']);
            $cumulativeCashExpense = floatval($cumulativeTransData['cash_expense']);
            $cumulativeBankExpense = floatval($cumulativeTransData['bank_expense']);

            // Calculate percentage changes
            $incomeChange = $this->calculatePercentageChange($currentIncome, $lastIncome);
            $expenseChange = $this->calculatePercentageChange($currentExpense, $lastExpense);

            // Build summary response
            $summary = [
                'current_month' => [
                    'total_income' => $currentIncome,
                    'total_amount' => floatval($currentCustData['total_amount']),
                    'total_due' => floatval($currentCustData['total_due']),
                    'total_expense' => $currentExpense,
                    'net_balance' => $currentIncome - $currentExpense,
                    'income_change' => round($incomeChange, 1),
                    'expense_change' => round($expenseChange, 1),
                    'payment_methods' => [
                        'cash' => [
                            'income' => $totalCashIncome,
                            'expense' => $totalCashExpense,
                            'balance' => $totalCashIncome - $totalCashExpense
                        ],
                        'bank' => [
                            'income' => $totalBankIncome,
                            'expense' => $totalBankExpense,
                            'balance' => $totalBankIncome - $totalBankExpense
                        ]
                    ]
                ],
                'cumulative' => [
                    'total_income' => $cumulativeIncome,
                    'total_expense' => $cumulativeExpense,
                    'net_profit' => $cumulativeProfit,
                    'payment_methods' => [
                        'cash' => [
                            'income' => $cumulativeCashIncome,
                            'expense' => $cumulativeCashExpense,
                            'balance' => $cumulativeCashIncome - $cumulativeCashExpense
                        ],
                        'bank' => [
                            'income' => $cumulativeBankIncome,
                            'expense' => $cumulativeBankExpense,
                            'balance' => $cumulativeBankIncome - $cumulativeBankExpense
                        ]
                    ],
                    'from_date' => $startOfYear,
                    'to_date' => date('Y-m-d')
                ],
                'current_month_name' => date('F Y'),
                'income_breakdown' => [
                    'customer_payments' => floatval($currentCustData['cust_income']),
                    'other_income' => floatval($currentTransData['trans_income'])
                ]
            ];

            // Add super admin flag to the response
            if ($this->is_super_admin) {
                $summary['is_super_admin'] = true;
            }

            error_log("Final summary: " . print_r($summary, true));
            return $summary;

        } catch (Exception $e) {
            error_log("Error in getSummary: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    // Add this helper method for percentage calculation
    private function calculatePercentageChange($current, $previous) {
        if ($previous > 0) {
            return (($current - $previous) / $previous) * 100;
        } elseif ($current > 0) {
            return 100;
        }
        return 0;
    }

    public function deleteCategory($id) {
        try {
            error_log("Attempting to delete category ID: " . $id);

            // First check if the category exists and belongs to the user
            $checkSql = "SELECT * FROM categories WHERE category_id = ?";
            $checkStmt = $this->conn->prepare($checkSql);
            if (!$checkStmt) {
                error_log("Prepare check failed: " . $this->conn->error);
                throw new Exception("Database error while checking category");
            }

            $checkStmt->bind_param("i", $id);
            if (!$checkStmt->execute()) {
                error_log("Execute check failed: " . $checkStmt->error);
                throw new Exception("Database error while checking category");
            }

            $category = $checkStmt->get_result()->fetch_assoc();
            $checkStmt->close();

            if (!$category) {
                error_log("Category not found: " . $id);
                throw new Exception("Category not found");
            }

            if ($category['is_default'] == 1) {
                error_log("Attempted to delete default category: " . $id);
                throw new Exception("Cannot delete default category");
            }

            // Check if category is in use
            $usageCheckSql = "SELECT COUNT(*) as count FROM transactions WHERE category_id = ?";
            $usageCheckStmt = $this->conn->prepare($usageCheckSql);
            if (!$usageCheckStmt) {
                error_log("Prepare usage check failed: " . $this->conn->error);
                throw new Exception("Database error while checking category usage");
            }

            $usageCheckStmt->bind_param("i", $id);
            if (!$usageCheckStmt->execute()) {
                error_log("Execute usage check failed: " . $usageCheckStmt->error);
                throw new Exception("Database error while checking category usage");
            }

            $usageResult = $usageCheckStmt->get_result()->fetch_assoc();
            $usageCheckStmt->close();

            if ($usageResult['count'] > 0) {
                error_log("Category in use: " . $id . ", usage count: " . $usageResult['count']);
                throw new Exception("Cannot delete category that is in use. Please reassign or delete related transactions first.");
            }

            // Proceed with deletion
            $deleteSql = "DELETE FROM categories WHERE category_id = ? AND is_default = 0";
            $deleteStmt = $this->conn->prepare($deleteSql);
            if (!$deleteStmt) {
                error_log("Prepare delete failed: " . $this->conn->error);
                throw new Exception("Database error while deleting category");
            }

            $deleteStmt->bind_param("i", $id);
            if (!$deleteStmt->execute()) {
                error_log("Execute delete failed: " . $deleteStmt->error);
                throw new Exception("Database error while deleting category");
            }

            if ($deleteStmt->affected_rows === 0) {
                error_log("No rows affected when deleting category: " . $id);
                throw new Exception("Failed to delete category");
            }

            $deleteStmt->close();
            error_log("Category deleted successfully: " . $id);
            return true;

        } catch (Exception $e) {
            error_log("Error in deleteCategory: " . $e->getMessage());
            throw $e;
        }
    }

    public function getChartData($filters) {
        try {
            error_log("getChartData called with filters: " . print_r($filters, true));
            
            // Define dates first
            $period = $filters['period'] ?? 'monthly';
            $dataType = $filters['data_type'] ?? 'all';
            $startDate = $filters['start_date'] ?? date('Y-m-d', strtotime('-1 month'));
            $endDate = $filters['end_date'] ?? date('Y-m-d');
            $userId = null;

            // Check if user_id filter is provided for super admins
            if ($this->is_super_admin && !empty($filters['user_id'])) {
                $userId = intval($filters['user_id']);
                error_log("Super admin filtering chart data for user ID: " . $userId);
            }

            error_log("Processing chart data with period: $period, type: $dataType, dates: $startDate to $endDate");

            // Now debug queries will work with defined dates
            $debugSql = "SELECT 
                SUM(paidamount) as total_paid,
                COUNT(*) as record_count,
                MIN(date) as min_date,
                MAX(date) as max_date
            FROM cust_details 
            WHERE date BETWEEN ? AND ?";
            
            $debugStmt = $this->conn->prepare($debugSql);
            $debugStmt->bind_param("ss", $startDate, $endDate);
            $debugStmt->execute();
            $custDebug = $debugStmt->get_result()->fetch_assoc();
            error_log("Customer Details Debug Data: " . print_r($custDebug, true));

            $debugTransSql = "SELECT 
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense,
                COUNT(*) as record_count
            FROM transactions 
            WHERE ";
            
            // Add user filter
            if ($this->is_super_admin) {
                if ($userId) {
                    $debugTransSql .= "user_id = ? AND ";
                    $debugTransParams = [$userId, $startDate, $endDate];
                    $debugTransTypes = "iss";
                } else {
                    $debugTransSql .= "1=1 AND ";
                    $debugTransParams = [$startDate, $endDate];
                    $debugTransTypes = "ss";
                }
            } else {
                $debugTransSql .= "user_id = ? AND ";
                $debugTransParams = [$this->user_id, $startDate, $endDate];
                $debugTransTypes = "iss";
            }
            
            $debugTransSql .= "transaction_date BETWEEN ? AND ?";
            
            $debugTransStmt = $this->conn->prepare($debugTransSql);
            $debugTransStmt->bind_param($debugTransTypes, ...$debugTransParams);
            $debugTransStmt->execute();
            $transDebug = $debugTransStmt->get_result()->fetch_assoc();
            error_log("Transactions Debug Data: " . print_r($transDebug, true));

            // Define date format based on period
            switch ($period) {
                case 'daily':
                    $dateFormat = "DATE";
                    break;
                case 'weekly':
                    $dateFormat = "YEARWEEK";
                    break;
                case 'monthly':
                    $dateFormat = "DATE_FORMAT";
                    $datePattern = "'%Y-%m'";
                    break;
                case 'yearly':
                    $dateFormat = "DATE_FORMAT";
                    $datePattern = "'%Y'";
                    break;
                default:
                    $dateFormat = "DATE";
            }

            if ($dataType === 'category') {
                $sql = "SELECT 
                        date_group,
                        category,
                        SUM(amount) as total_amount
                    FROM (
                        -- Transactions data
                        SELECT 
                            " . ($dateFormat === "DATE_FORMAT" ? 
                                "$dateFormat(transaction_date, $datePattern)" : 
                                "$dateFormat(transaction_date)") . " as date_group,
                            c.name as category,
                            t.amount
                        FROM transactions t
                        JOIN categories c ON t.category_id = c.category_id
                        WHERE ";
                
                // Add user filter
                if ($this->is_super_admin) {
                    if ($userId) {
                        $sql .= "t.user_id = ? AND ";
                        $params = [$userId, $startDate, $endDate];
                        $types = "iss";
                    } else {
                        $sql .= "1=1 AND ";
                        $params = [$startDate, $endDate];
                        $types = "ss";
                    }
                } else {
                    $sql .= "t.user_id = ? AND ";
                    $params = [$this->user_id, $startDate, $endDate];
                    $types = "iss";
                }
                
                $sql .= "t.transaction_date BETWEEN ? AND ?
                        
                        UNION ALL
                        
                        -- Customer payments data
                        SELECT 
                            " . ($dateFormat === "DATE_FORMAT" ? 
                                "$dateFormat(date, $datePattern)" : 
                                "$dateFormat(date)") . " as date_group,
                            'Customer Payments' as category,
                            COALESCE(paidamount, 0) as amount  -- Handle NULL values
                        FROM cust_details cd
                        WHERE cd.date BETWEEN ? AND ?
                        AND cd.paidamount > 0
                    ) combined_data
                    GROUP BY date_group, category
                    ORDER BY date_group, category";

                // Add customer details params
                $params[] = $startDate;
                $params[] = $endDate;
                $types .= "ss";
                
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param($types, ...$params);
            } else {
                $sql = "SELECT 
                        date_group,
                        SUM(income) as income,
                        SUM(expense) as expense
                    FROM (
                        -- Transactions data
                        SELECT 
                            " . ($dateFormat === "DATE_FORMAT" ? 
                                "$dateFormat(transaction_date, $datePattern)" : 
                                "$dateFormat(transaction_date)") . " as date_group,
                        CASE WHEN type = 'income' THEN amount ELSE 0 END as income,
                        CASE WHEN type = 'expense' THEN amount ELSE 0 END as expense
                    FROM transactions
                    WHERE ";
                
                // Add user filter
                if ($this->is_super_admin) {
                    if ($userId) {
                        $sql .= "user_id = ? AND ";
                        $params = [$userId, $startDate, $endDate];
                        $types = "iss";
                    } else {
                        $sql .= "1=1 AND ";
                        $params = [$startDate, $endDate];
                        $types = "ss";
                    }
                } else {
                    $sql .= "user_id = ? AND ";
                    $params = [$this->user_id, $startDate, $endDate];
                    $types = "iss";
                }
                
                $sql .= "transaction_date BETWEEN ? AND ?
                    
                    UNION ALL
                    
                    -- Customer payments data
                    SELECT 
                        " . ($dateFormat === "DATE_FORMAT" ? 
                            "$dateFormat(date, $datePattern)" : 
                            "$dateFormat(date)") . " as date_group,
                        COALESCE(paidamount, 0) as income,  -- Handle NULL values
                        0 as expense
                    FROM cust_details cd
                    WHERE cd.date BETWEEN ? AND ?
                    AND cd.paidamount > 0
                ) combined_data
                GROUP BY date_group
                ORDER BY date_group";

                // Add customer details params
                $params[] = $startDate;
                $params[] = $endDate;
                $types .= "ss";
                
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param($types, ...$params);
            }

            // Add debug logging for yearly data
            if ($period === 'yearly') {
                error_log("Yearly data SQL: " . $sql);
                error_log("Date range: " . $startDate . " to " . $endDate);
            }

            if (!$stmt->execute()) {
                throw new Exception("Failed to execute statement: " . $stmt->error);
            }

            $result = $stmt->get_result();
            
            $data = [
                'labels' => [],
                'income' => [],
                'expenses' => [],
                'values' => [],
                'categories' => []
            ];

            $stats = [
                'total_income' => 0,
                'total_expenses' => 0,
                'net_balance' => 0,
                'daily_average' => 0
            ];

            error_log("Processing result rows...");
            
            while ($row = $result->fetch_assoc()) {
                error_log("Processing row: " . print_r($row, true));
                
                if ($dataType === 'category') {
                    $data['labels'][] = $row['category'];
                    $data['values'][] = floatval($row['total_amount']);
                    $stats['total_income'] += floatval($row['total_amount']);
                } else {
                    $data['labels'][] = $this->formatDateGroup($row['date_group'], $period);
                    $data['income'][] = floatval($row['income']);
                    $data['expenses'][] = floatval($row['expense']);
                    
                    $stats['total_income'] += floatval($row['income']);
                    $stats['total_expenses'] += floatval($row['expense']);
                }
            }

            $stats['net_balance'] = $stats['total_income'] - $stats['total_expenses'];
            $days = max(1, (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24));
            $stats['daily_average'] = $stats['net_balance'] / $days;

            error_log("Final SQL Query: " . $sql);
            error_log("Query Parameters: startDate=" . $startDate . ", endDate=" . $endDate);
            
            // Debug log the final data before returning
            error_log("Final Chart Data: " . print_r([
                'labels' => $data['labels'],
                'income_count' => count($data['income'] ?? []),
                'expenses_count' => count($data['expenses'] ?? []),
                'values_count' => count($data['values'] ?? []),
                'stats' => $stats
            ], true));

            return [
                'data' => $data,
                'stats' => $stats,
                'is_super_admin' => $this->is_super_admin
            ];

        } catch (Exception $e) {
            error_log("Error in getChartData: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    private function formatDateGroup($dateGroup, $period) {
        try {
            switch ($period) {
                case 'daily':
                    return date('M d, Y', strtotime($dateGroup));
                case 'weekly':
                    $year = substr($dateGroup, 0, 4);
                    $week = substr($dateGroup, 4);
                    return "Week {$week}, {$year}";
                case 'monthly':
                    return date('M Y', strtotime($dateGroup . '-01'));
                case 'yearly':
                    return $dateGroup;
                default:
                    return $dateGroup;
            }
        } catch (Exception $e) {
            error_log("Error formatting date group: " . $e->getMessage());
            return $dateGroup;
        }
    }

    public function exportData($params) {
        try {
            error_log("Starting export with params: " . print_r($params, true));
            
            $type = $params['type'] ?? 'all';
            $format = $params['format'] ?? 'excel';
            $startDate = $params['start_date'] ?? null;
            $endDate = $params['end_date'] ?? null;

            // Initialize query components
            $sql = "";
            $queryParams = [$this->user_id];
            $paramTypes = "i";

            // Build query based on type
            switch($type) {
                case 'all':
                    $sql = "SELECT 
                        DATE_FORMAT(t.transaction_date, '%Y-%m-%d') as transaction_date,
                        t.type,
                        c.name as category,
                        t.amount,
                        t.description 
                    FROM transactions t
                    LEFT JOIN categories c ON t.category_id = c.category_id
                    WHERE t.user_id = ?";
                    $headers = ['Date', 'Type', 'Category', 'Amount', 'Description'];
                    break;
                
                case 'income':
                case 'expense':
                    $sql = "SELECT 
                        DATE_FORMAT(t.transaction_date, '%Y-%m-%d') as transaction_date,
                        c.name as category,
                        t.amount,
                        t.description 
                    FROM transactions t
                    LEFT JOIN categories c ON t.category_id = c.category_id
                    WHERE t.user_id = ? AND t.type = ?";
                    $headers = ['Date', 'Category', 'Amount', 'Description'];
                    $queryParams[] = $type;
                    $paramTypes .= "s";
                    break;
                
                case 'summary':
                    $sql = "SELECT 
                        DATE_FORMAT(transaction_date, '%Y-%m') as month,
                        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
                        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expenses,
                        SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END) as net_balance
                    FROM transactions 
                    WHERE user_id = ?";
                    $headers = ['Month', 'Total Income', 'Total Expenses', 'Net Balance'];
                    break;
                
                case 'category':
                    $sql = "SELECT 
                        c.name as category,
                        t.type,
                        COUNT(*) as transaction_count,
                        SUM(t.amount) as total_amount,
                        AVG(t.amount) as average_amount
                    FROM transactions t
                    LEFT JOIN categories c ON t.category_id = c.category_id
                    WHERE t.user_id = ?";
                    $headers = ['Category', 'Type', 'Transaction Count', 'Total Amount', 'Average Amount'];
                    break;
                
                default:
                    throw new Exception("Invalid export type: " . $type);
            }

            // Add date range if provided
            if ($startDate && $endDate) {
                $sql .= " AND t.transaction_date BETWEEN ? AND ?";
                $queryParams[] = $startDate;
                $queryParams[] = $endDate;
                $paramTypes .= "ss";
            }

            // Add grouping for summary and category types
            if ($type === 'summary') {
                $sql .= " GROUP BY DATE_FORMAT(transaction_date, '%Y-%m') ORDER BY month";
            } elseif ($type === 'category') {
                $sql .= " GROUP BY c.category_id, t.type ORDER BY c.name, t.type";
            } else {
                $sql .= " ORDER BY t.transaction_date DESC";
            }

            error_log("Export SQL: " . $sql);
            error_log("Export params: " . print_r($queryParams, true));
            error_log("Param types: " . $paramTypes);

            // Prepare and execute query
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $this->conn->error);
            }

            if (!empty($queryParams)) {
                $stmt->bind_param($paramTypes, ...$queryParams);
            }

            if (!$stmt->execute()) {
                throw new Exception("Failed to execute statement: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);

            error_log("Retrieved " . count($data) . " rows for export");
            
            if (empty($data)) {
                throw new Exception("No data found for the selected criteria");
            }

            // Generate export based on format
            try {
                switch($format) {
                    case 'excel':
                        $output = $this->generateExcel($data, $headers);
                        break;
                    case 'csv':
                        $output = $this->generateCSV($data, $headers);
                        break;
                    case 'pdf':
                        $output = $this->generatePDF($data, $headers);
                        break;
                    default:
                        throw new Exception("Unsupported export format: " . $format);
                }
                
                error_log("Successfully generated " . $format . " export");
                return $output;

            } catch (Exception $e) {
                error_log("Error generating " . $format . " export: " . $e->getMessage());
                throw new Exception("Failed to generate " . $format . " export: " . $e->getMessage());
            }

        } catch (Exception $e) {
            error_log("Export error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    private function generateExcel($data, $headers) {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers
        foreach($headers as $col => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
            $sheet->setCellValue($colLetter . '1', $header);
            
            // Style headers
            $sheet->getStyle($colLetter . '1')
                ->getFont()
                ->setBold(true);
            
            $sheet->getStyle($colLetter . '1')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CCCCCC');
        }

        // Add data
        foreach($data as $rowIndex => $rowData) {
            $row = $rowIndex + 2; // Start from row 2 (after headers)
            
            foreach($headers as $colIndex => $header) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $cellAddress = $colLetter . $row;
                
                // Get the value from the row data using the original database column name
                $dbColumn = $this->getDbColumnName($header);
                $value = $rowData[$dbColumn] ?? '';

                // Format amount columns
                if ($this->isAmountColumn($header)) {
                    $sheet->setCellValue($cellAddress, floatval($value));
                    $sheet->getStyle($cellAddress)
                        ->getNumberFormat()
                        ->setFormatCode('₹#,##0.00');
                } else if ($this->isDateColumn($header)) {
                    // Format date columns
                    if ($value) {
                        $dateValue = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($value);
                        $sheet->setCellValue($cellAddress, $dateValue);
                        $sheet->getStyle($cellAddress)
                            ->getNumberFormat()
                            ->setFormatCode('dd-mm-yyyy');
                    }
                } else {
                    $sheet->setCellValue($cellAddress, $value);
                }
            }
        }

        // Auto-size columns
        foreach($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Create Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }

    // Helper functions for Excel generation
    private function getDbColumnName($header) {
        // Map display headers to database column names
        $columnMap = [
            'Date' => 'transaction_date',
            'Type' => 'type',
            'Category' => 'category',
            'Amount' => 'amount',
            'Description' => 'description',
            'Month' => 'month',
            'Total Income' => 'total_income',
            'Total Expenses' => 'total_expenses',
            'Net Balance' => 'net_balance',
            'Transaction Count' => 'transaction_count',
            'Total Amount' => 'total_amount',
            'Average Amount' => 'average_amount'
        ];
        
        return $columnMap[$header] ?? strtolower(str_replace(' ', '_', $header));
    }

    private function isAmountColumn($header) {
        $amountColumns = [
            'Amount',
            'Total Income',
            'Total Expenses',
            'Net Balance',
            'Total Amount',
            'Average Amount'
        ];
        return in_array($header, $amountColumns);
    }

    private function isDateColumn($header) {
        return in_array($header, ['Date']);
    }

    private function generateCSV($data, $headers) {
        $output = fopen('php://temp', 'r+');
        
        // Add headers
        fputcsv($output, $headers);
        
        // Add data
        foreach($data as $row) {
            $rowData = [];
            foreach($headers as $header) {
                $rowData[] = $row[$header] ?? '';
            }
            fputcsv($output, $rowData);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }

    private function generatePDF($data, $headers) {
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Financial Report');

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', 'B', 16);

        // Title
        $pdf->Cell(0, 15, 'Financial Report', 0, 1, 'C');
        $pdf->Ln(10);

        // Calculate column widths
        $pageWidth = $pdf->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT;
        $columnWidth = $pageWidth / count($headers);

        // Headers
        $pdf->SetFont('helvetica', 'B', 11);
        foreach($headers as $header) {
            $pdf->Cell($columnWidth, 7, $header, 1);
        }
        $pdf->Ln();

        // Data
        $pdf->SetFont('helvetica', '', 10);
        foreach($data as $row) {
            foreach($headers as $header) {
                $value = $row[$header] ?? '';
                // Format amounts
                if (strpos($header, 'Amount') !== false || strpos($header, 'Income') !== false || strpos($header, 'Expenses') !== false || strpos($header, 'Balance') !== false) {
                    $value = '₹' . number_format($value, 2);
                }
                $pdf->Cell($columnWidth, 7, $value, 1);
            }
            $pdf->Ln();
        }

        return $pdf->Output('', 'S');
    }

    public function getRevenueBreakdown($params = []) {
        try {
            // First, get available years
            $yearsQuery = "
                SELECT DISTINCT YEAR(transaction_date) as year 
                FROM transactions ";
                
            if (!$this->is_super_admin) {
                $yearsQuery .= "WHERE user_id = ? ";
                $yearsParams = [$this->user_id];
                $yearsTypes = "i";
            } else {
                $yearsQuery .= "WHERE 1=1 ";
                $yearsParams = [];
                $yearsTypes = "";
            }
            
            $yearsQuery .= "
                UNION
                SELECT DISTINCT YEAR(date) as year 
                FROM cust_details
                ORDER BY year DESC";

            $yearsStmt = $this->conn->prepare($yearsQuery);
            if (!empty($yearsParams)) {
                $yearsStmt->bind_param($yearsTypes, ...$yearsParams);
            }
            $yearsStmt->execute();
            $yearsResult = $yearsStmt->get_result();
            $availableYears = [];
            
            while ($yearRow = $yearsResult->fetch_assoc()) {
                $availableYears[] = $yearRow['year'];
            }

            // If no years available, return empty data
            if (empty($availableYears)) {
                return [
                    'data' => [],
                    'available_years' => [],
                    'current_filters' => [
                        'year' => date('Y'),
                        'month' => null,
                        'week' => null,
                        'day' => null
                    ]
                ];
            }

            // Use provided year or latest available year
            $year = $params['year'] ?? reset($availableYears);
            $month = $params['month'] ?? null;
            $week = isset($params['week']) && $params['week'] > 0 ? $params['week'] : null;
            $day = $params['day'] ?? null;

            // Validate year exists in available years
            if (!in_array($year, $availableYears)) {
                $year = reset($availableYears); // Use the most recent year if invalid
            }

            // Validate month (1-12)
            if ($month !== null && (!is_numeric($month) || $month < 1 || $month > 12)) {
                $month = null;
            }

            // Validate week (1-53)
            if ($week !== null && (!is_numeric($week) || $week < 1 || $week > 53)) {
                $week = null;
            }

            // Define the order by clause based on the drill-down level
            if ($day) {
                $orderBy = "ORDER BY date DESC";
                $groupBy = "GROUP BY date, source_id";
            } elseif ($week && $month) {
                $orderBy = "ORDER BY date DESC";
                $groupBy = "GROUP BY date";
            } elseif ($month) {
                $orderBy = "ORDER BY week_number";
                $groupBy = "GROUP BY week_number";
            } else {
                $orderBy = "ORDER BY month_number";
                $groupBy = "GROUP BY month_number";
            }

            // Build the combined query using a derived table
            $sql = "
                SELECT 
                    date,
                    week_number,
                    month_number,
                    year,
                    SUM(transaction_count) as transaction_count,
                    SUM(revenue) as revenue,
                    SUM(expenses) as expenses
                FROM (
                    -- Transactions data
                    SELECT 
                        DATE(transaction_date) as date,
                        WEEK(transaction_date, 1) as week_number,
                        MONTH(transaction_date) as month_number,
                        YEAR(transaction_date) as year,
                        COUNT(*) as transaction_count,
                        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as revenue,
                        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expenses,
                        transaction_id as source_id
                    FROM transactions 
                    WHERE ";
                    
            // Prepare parameters array
            $params = [];
            $types = "";
            
            // Super admins can see all transactions, regular users only see their own
            if ($this->is_super_admin) {
                $sql .= "1=1";
            } else {
                $sql .= "user_id = ?";
                $params[] = $this->user_id;
                $types .= "i";
            }
            
            $sql .= " AND YEAR(transaction_date) = ?";
            $params[] = $year;
            $types .= "i";
            
            if ($month) {
                $sql .= " AND MONTH(transaction_date) = ?";
                $params[] = $month;
                $types .= "i";
            }
            
            if ($week && $month) {
                $sql .= " AND WEEK(transaction_date, 1) = ?";
                $params[] = $week;
                $types .= "i";
            }
            
            if ($day) {
                $sql .= " AND DATE(transaction_date) = ?";
                $params[] = $day;
                $types .= "s";
            }
            
            $sql .= " GROUP BY " . ($day ? "transaction_id, " : "") . "date

                    UNION ALL

                    -- Customer details data
                    SELECT 
                        DATE(date) as date,
                        WEEK(date, 1) as week_number,
                        MONTH(date) as month_number,
                        YEAR(date) as year,
                        COUNT(*) as transaction_count,
                        SUM(paidamount) as revenue,
                        0 as expenses,
                        id as source_id
                    FROM cust_details 
                    WHERE YEAR(date) = ?";
                    
            // Add year parameter for customer details
            $custParams = [$year];
            $custTypes = "i";
            
            if ($month) {
                $sql .= " AND MONTH(date) = ?";
                $custParams[] = $month;
                $custTypes .= "i";
            }
            
            if ($week && $month) {
                $sql .= " AND WEEK(date, 1) = ?";
                $custParams[] = $week;
                $custTypes .= "i";
            }
            
            if ($day) {
                $sql .= " AND DATE(date) = ?";
                $custParams[] = $day;
                $custTypes .= "s";
            }
            
            $sql .= " GROUP BY " . ($day ? "id, " : "") . "date
                ) combined_data
                {$groupBy}
                {$orderBy}";

            // Combine all parameters
            $allParams = array_merge($params, $custParams);
            $allTypes = $types . $custTypes;

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $this->conn->error);
            }

            // Bind parameters
            if (!empty($allParams)) {
                $stmt->bind_param($allTypes, ...$allParams);
            }

            if (!$stmt->execute()) {
                throw new Exception("Failed to execute statement: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $data = [];
            
            while ($row = $result->fetch_assoc()) {
                $key = $day ? $row['date'] : ($week ? $row['date'] : ($month ? $row['week_number'] : $row['month_number']));
                
                if (!isset($data[$key])) {
                    $data[$key] = [
                        'date' => $row['date'],
                        'week' => $row['week_number'],
                        'month' => $row['month_number'],
                        'year' => $row['year'],
                        'transaction_count' => 0,
                        'revenue' => 0,
                        'expenses' => 0,
                        'profit' => 0
                    ];
                }
                
                $data[$key]['transaction_count'] += $row['transaction_count'];
                $data[$key]['revenue'] += $row['revenue'];
                $data[$key]['expenses'] += $row['expenses'];
                $data[$key]['profit'] = $data[$key]['revenue'] - $data[$key]['expenses'];
            }

            // Add filters based on drill-down level
            if ($day) {
                // Modify the SQL to get detailed data for the specified day
                $sql = "
                    SELECT 
                        'transaction' as source,
                        t.transaction_id,
                        c.name as category_name,
                        t.amount,
                        t.transaction_date,
                        t.type,
                        t.description,
                        " . ($this->is_super_admin ? "t.user_id," : "") . "
                        NULL as customer_name,
                        NULL as customer_phone,
                        NULL as vehicle,
                        TIME(t.created_at) as transaction_time
                    FROM transactions t
                    JOIN categories c ON t.category_id = c.category_id
                    WHERE ";
                    
                // Prepare parameters
                $detailParams = [];
                $detailTypes = "";
                
                // Super admins can see all transactions, regular users only see their own
                if ($this->is_super_admin) {
                    $sql .= "1=1";
                } else {
                    $sql .= "t.user_id = ?";
                    $detailParams[] = $this->user_id;
                    $detailTypes .= "i";
                }
                
                $sql .= " AND DATE(t.transaction_date) = ?
                    UNION ALL
                    SELECT 
                        'customer' as source,
                        cd.id as transaction_id,
                        NULL as category_name,
                        cd.paidamount as amount,
                        cd.date as transaction_date,
                        NULL as type,
                        NULL as description,
                        " . ($this->is_super_admin ? "NULL as user_id," : "") . "
                        cd.name,
                        cd.phone,
                        cd.vehicle,
                        cd.time as transaction_time
                    FROM cust_details cd
                    WHERE DATE(cd.date) = ?
                ";

                $stmt = $this->conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Failed to prepare statement: " . $this->conn->error);
                }

                // Add day parameter
                $detailParams[] = $day;
                $detailTypes .= "s";
                $detailParams[] = $day;
                $detailTypes .= "s";
                
                $stmt->bind_param($detailTypes, ...$detailParams);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to execute statement: " . $stmt->error);
                }

                $result = $stmt->get_result();
                $data = [];

                while ($row = $result->fetch_assoc()) {
                    $dataRow = [
                        'source' => $row['source'],
                        'transaction_id' => $row['transaction_id'],
                        'category_name' => $row['category_name'],
                        'amount' => $row['amount'],
                        'transaction_date' => $row['transaction_date'],
                        'type' => $row['type'],
                        'description' => $row['description'],
                        'customer_name' => $row['customer_name'],
                        'customer_phone' => $row['customer_phone'],
                        'vehicle' => $row['vehicle'],
                        'transaction_time' => $row['transaction_time']
                    ];
                    
                    // Add user_id for super admins
                    if ($this->is_super_admin && isset($row['user_id'])) {
                        $dataRow['user_id'] = $row['user_id'];
                    }
                    
                    $data[] = $dataRow;
                }

                return [
                    'data' => $data,
                    'available_years' => $availableYears,
                    'current_filters' => [
                        'year' => $year,
                        'month' => $month,
                        'week' => $week,
                        'day' => $day
                    ],
                    'is_super_admin' => $this->is_super_admin
                ];
            }

            return [
                'data' => array_values($data),
                'available_years' => $availableYears,
                'current_filters' => [
                    'year' => $year,
                    'month' => $month,
                    'week' => $week,
                    'day' => $day
                ],
                'is_super_admin' => $this->is_super_admin
            ];

        } catch (Exception $e) {
            error_log("Error in getRevenueBreakdown: " . $e->getMessage());
            throw $e;
        }
    }
}