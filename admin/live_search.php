<?php
require_once('../includes/authentication.php');
authenticationAdmin('../');

/**
 * Search customers and display results
 * Added prepared statements for security
 * Added customer UID support
 * Improved error handling
 * Added input sanitization
 */

if (isset($_POST['input']) && !empty(trim($_POST['input']))) {
    try {
        $input = trim($_POST['input']);
        
        // Prepare the search query with customer UID support
        $query = "SELECT id, cust_uid, name, phone, email, totalamount, vehicle, date 
                 FROM cust_details 
                 WHERE name LIKE ? 
                 OR phone LIKE ? 
                 OR date LIKE ?
                 OR cust_uid LIKE ?
                 ORDER BY date DESC";
                 
        if ($stmt = $conn->prepare($query)) {
            $searchParam = $input . '%';
            $stmt->bind_param('ssss', $searchParam, $searchParam, $searchParam, $searchParam);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                displayResults($result);
            } else {
                echo '<div class="alert alert-info">No matching records found</div>';
            }
            
            $stmt->close();
        } else {
            throw new Exception("Failed to prepare the search query");
        }
        
    } catch (Exception $e) {
        error_log("Search error: " . $e->getMessage());
        echo '<div class="alert alert-danger">An error occurred while processing your request</div>';
    }
}

/**
 * Display search results in a table format
 * @param mysqli_result $result
 */
function displayResults($result) {
    ?>
    <div class="search-table">
        <table>
            <thead>
                <tr>
                    <th>Customer ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Total Amount</th>
                    <th>Vehicle</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                    $customerId = htmlspecialchars($row['cust_uid'] ?? 'N/A');
                    $name = htmlspecialchars($row['name']);
                    $phone = htmlspecialchars($row['phone']);
                    $email = htmlspecialchars($row['email']);
                    $amount = htmlspecialchars($row['totalamount']);
                    $vehicle = htmlspecialchars($row['vehicle']);
                    $date = htmlspecialchars($row['date']);
                    
                    $viewUrl = sprintf(
                        'view/?id=%s&phone=%s&date=%s&route=../search?q=%s',
                        urlencode($row['id']),
                        urlencode($phone),
                        urlencode($date),
                        urlencode($_POST['input'])
                    );
                    ?>
                    <tr>
                        <td><?php echo $customerId; ?></td>
                        <td><?php echo $name; ?></td>
                        <td><a href="tel:<?php echo $phone; ?>"><?php echo $phone; ?></a></td>
                        <td><?php echo $email; ?></td>
                        <td><?php echo $amount; ?></td>
                        <td><?php echo $vehicle; ?></td>
                        <td><?php echo $date; ?></td>
                        <td><a class="view" href="<?php echo $viewUrl; ?>">View Details</a></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>