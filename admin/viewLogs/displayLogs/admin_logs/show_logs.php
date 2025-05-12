<?php
function customTimeFormat($timeString) {
    date_default_timezone_set('Asia/Kolkata');
    return date('d M Y g:ia', strtotime($timeString)) ?: "Invalid Date";
}

function renderCustomerChangeTable($activity) {
    if (!isset($activity['0'])) return '';
    
    $customer = $activity['0']['customer_details'];
    $changes = $activity['0']['changed_things'];
    
    return "
        <div class='changes-table'>
            <div class='customer-info'>
                <div class='info-item'>
                    <span class='label'>Name:</span>
                    <span class='value'>" . htmlspecialchars($customer['name']) . "</span>
                </div>
                <div class='info-item'>
                    <span class='label'>Phone:</span>
                    <span class='value'>" . htmlspecialchars($customer['phone']) . "</span>
                </div>
            </div>
            <div class='changes-grid'>
                <div class='change-item'>
                    <h4>Vehicle Changes</h4>
                    " . formatChange($changes['car']) . "
                </div>
                " . (isset($changes['date']) ? formatDateChange($changes['date']) : '') . "
                " . (isset($changes['timeSlot']) ? formatTimeSlotChange($changes['timeSlot']) : '') . "
            </div>
        </div>";
}

function formatChange($change) {
    if (is_array($change)) {
        return "<div class='change-values'>
                  <div class='old'>Old: " . htmlspecialchars($change['old']) . "</div>
                  <div class='new'>New: " . htmlspecialchars($change['new']) . "</div>
               </div>";
    }
    return "<div class='single-value'>" . htmlspecialchars($change) . "</div>";
}

function formatDateChange($date) {
    return "<div class='change-item'>
              <h4>Date Changes</h4>
              <div class='change-values'>
                  <div class='old'>Old: " . htmlspecialchars($date['0ld']) . "</div>
                  <div class='new'>New: " . htmlspecialchars($date['new']) . "</div>
              </div>
           </div>";
}

function formatTimeSlotChange($timeSlot) {
    return "<div class='change-item'>
              <h4>Time Slot Changes</h4>
              <div class='change-values'>
                  <div class='old'>Old: " . htmlspecialchars($timeSlot['0ld']) . "</div>
                  <div class='new'>New: " . htmlspecialchars($timeSlot['new']) . "</div>
              </div>
           </div>";
}

?>




<?php
function getPaginatedLogs($jsonFilePath, $currentPage, $perPage) {
    if (!file_exists($jsonFilePath)) return ['logs' => [], 'total' => 0];

    $jsonData = file_get_contents($jsonFilePath);
    $logEntries = json_decode($jsonData, true);

    if ($logEntries === null) return ['logs' => [], 'total' => 0];

    $totalLogs = count($logEntries);
    $startIndex = ($currentPage - 1) * $perPage;
    $logs = array_slice($logEntries, $startIndex, $perPage);

    return ['logs' => $logs, 'total' => $totalLogs];
}

function getCompactPagination($currentPage, $totalPages) {
    $pagination = [];
    $maxVisible = 5; // Number of visible page links

    if ($totalPages <= $maxVisible + 2) {
        for ($i = 1; $i <= $totalPages; $i++) $pagination[] = $i;
    } else {
        $start = max(2, $currentPage - 2);
        $end = min($totalPages - 1, $currentPage + 2);

        $pagination[] = 1;
        if ($start > 2) $pagination[] = '...';

        for ($i = $start; $i <= $end; $i++) $pagination[] = $i;

        if ($end < $totalPages - 1) $pagination[] = '...';
        $pagination[] = $totalPages;
    }

    return $pagination;
}

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$jsonFilePath = '../../../../logs/admin_logs/logs.json';
$perPage = 30;

$paginationData = getPaginatedLogs($jsonFilePath, $page, $perPage);
$logs = $paginationData['logs'];
$totalLogs = $paginationData['total'];
$totalPages = ceil($totalLogs / $perPage);

$paginationLinks = getCompactPagination($page, $totalPages);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Logs Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --background-color: #f8fafc;
            --card-background: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --success-color: #10b981;
            --warning-color: #f59e0b;

            --primary-color: #2563eb;
            --alert-color: #dc2626;
            --alert-bg: #fee2e2;
            --warning-color: #d97706;
            --warning-bg: #fef3c7;
            --success-color: #059669;
            --success-bg: #d1fae5;
            --neutral-color: #6b7280;
            --border-color: #e5e7eb;
            --background: #f9fafb;
        }


        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            /*background-color: var(--background-color);*/
             background-color: #eee;
            color: var(--text-primary);
            line-height: 1.5;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .dashboard-header {
            background-color: var(--card-background);
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .dashboard-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logs-table {
            background-color: var(--card-background);
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 1rem;
            text-align: left;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        tr:hover {
            background-color: #f8fafc;
        }

        .user-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            background-color: #e2e8f0;
            border-radius: 1rem;
            font-size: 0.875rem;
        }

        .timestamp {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .changes-table {
            background-color: #f8fafc;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-top: 0.5rem;
        }

        .customer-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-item .label {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .info-item .value {
            font-weight: 500;
        }

        .changes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .change-item {
            background-color: white;
            padding: 1rem;
            border-radius: 0.375rem;
            border: 1px solid var(--border-color);
        }

        .change-item h4 {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .change-values {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .old, .new {
            padding: 0.375rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
        }

        .old {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .new {
            background-color: #dcfce7;
            color: #166534;
        }

        .activity-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            gap: .5rem;
        }

        .activity-login {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .activity-modify {
            background-color: #fef3c7;
            color: #92400e;
        }

        @media (max-width: 768px) {
            .container {
                margin: 1rem auto;
            }

            .changes-grid {
                grid-template-columns: 1fr;
            }

            td {
                padding: 0.75rem;
            }
        }
        
        
        
        
        
        





        .pagination {
    display: flex;
    justify-content: center;
    margin: 1rem 0;
    gap: 0.5rem;
    flex-wrap: wrap;  /* Allow wrapping on smaller screens */
}

.pagination a, .pagination span {
    padding: 0.5rem 1rem;
    text-decoration: none;
    border: 1px solid #ccc;
    border-radius: 5px;
    color: #333;
    font-size: 1rem;  /* Default font size for desktop */
}

.pagination a:hover {
    background-color: #007bff;
    color: white;
}

.pagination .active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

.pagination .disabled {
    color: #ccc;
    cursor: not-allowed;
}

.pagination .dots {
    padding: 0.5rem;
    color: #999;
}

/* Mobile screens */
@media (max-width: 480px) {
    .pagination a, .pagination span {
        padding: 0.4rem 0.8rem;  /* Reduce padding on small screens */
        font-size: 0.9rem;  /* Smaller font size on mobile */
    }

    .pagination {
        gap: 0.3rem;  /* Reduce gap for better fitting */
    }
}

/* Tablets and medium screens */
@media (max-width: 768px) {
    .pagination a, .pagination span {
        padding: 0.5rem 1rem;
        font-size: 0.95rem;  /* Slightly smaller font size for tablets */
    }

    .pagination {
        gap: 0.4rem;  /* Adjust gap for medium screens */
    }
}

/* Larger screens (desktops) */
@media (min-width: 769px) {
    .pagination a, .pagination span {
        padding: 0.6rem 1.2rem;  /* Increase padding for larger screens */
        font-size: 1.1rem;  /* Increase font size for desktop */
    }

    .pagination {
        gap: 0.6rem;  /* Increased gap on large screens */
    }
}






/* Alert Styles */

.alert-badge {
    background-color: #ffcccc;
    color: #cc0000;
    padding: 0.5rem;
    border-radius: 5px;

}

.warning-badge {
    background-color: #fff3cd;
    color: #856404;
    padding: 0.5rem;
    border-radius: 5px;

}

.added-badge {
    background-color: #d4edda;
    color: #155724;
    padding: 0.5rem;
    border-radius: 5px;

}

.ended-badge {
    background-color: #cce5ff;
    color: #004085;
    padding: 0.5rem;
    border-radius: 5px;

}

.login-badge {
    background-color: #e6f7ff;
    color: #004085;
    padding: 0.5rem;
    border-radius: 5px;

}

.logout-badge {
    background-color: #fff3cd;
    color: #856404;
    padding: 0.5rem;
    border-radius: 5px;

}


.removed-badge {
    background-color: #f8d7da;
    color: #721c24;
    padding: 0.5rem;
    border-radius: 5px;

}

.ended-badge {
    background-color: #d4edda;
    color: #155724;
    padding: 0.5rem;
    border-radius: 5px;

}

    </style>
</head>
<body>

<div class="container">

    <div class="dashboard-header">
        <h1 class="dashboard-title">
            <i class="fas fa-shield-alt"></i>
            Admin Activity Logs
        </h1>
    </div>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>">Previous</a>
        <?php else: ?>
            <span class="disabled">Previous</span>
        <?php endif; ?>

        <?php foreach ($paginationLinks as $link): ?>
            <?php if ($link === '...'): ?>
                <span class="dots">...</span>
            <?php elseif ($link == $page): ?>
                <a href="?page=<?= $link ?>" class="active"><?= $link ?></a>
            <?php else: ?>
                <a href="?page=<?= $link ?>"><?= $link ?></a>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>">Next</a>
        <?php else: ?>
            <span class="disabled">Next</span>
        <?php endif; ?>
    </div>

        <div class="logs-table">
    <?php if (!empty($logs)): ?>
        <table>
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Activity</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $entry): ?>
                    <tr>
                        <td class="timestamp"><?= htmlspecialchars(customTimeFormat($entry['timestamp'])) ?></td>
                        <td>
                            <div class="user-badge">
                                <i class="fas fa-user"></i> <?= htmlspecialchars($entry['who']) ?>
                            </div>
                        </td>
                        <td>
                            <?php if (is_array($entry['activity'])): ?>
                                <div class="activity-badge activity-modify">
                                    <i class="fas fa-edit mr-2"></i> <?= htmlspecialchars($entry['activity']['What']) ?>
                                </div>
                                <?= renderCustomerChangeTable($entry['activity']) ?>
                            <?php else: ?>
                                <?php 
                                    $activityText = htmlspecialchars($entry['activity']);
                                    $badgeClass = '';
                                    $iconClass = '';
                                    
                                    if (stripos($activityText, 'Alert:') === 0) {
                                        $badgeClass = 'alert-badge';
                                        $iconClass = 'fas fa-exclamation-triangle';  // Icon for alerts
                                    } elseif (stripos($activityText, 'Warning:') === 0) {
                                        $badgeClass = 'warning-badge';
                                        $iconClass = 'fas fa-exclamation-circle';  // Icon for warnings
                                    } elseif (stripos($activityText, 'added to') !== false) {
                                        $badgeClass = 'added-badge';
                                        $iconClass = 'fas fa-plus-circle';  // Icon for adding
                                    } elseif (stripos($activityText, 'ended') !== false && stripos($activityText, 'Training') !== false) {
                                        $badgeClass = 'ended-badge';
                                        $iconClass = 'fas fa-calendar-check';  // Icon for training ended
                                    } elseif (stripos($activityText, 'Removed') !== false && stripos($activityText, 'from timetable') !== false) {
                                        $badgeClass = 'removed-badge';
                                        $iconClass = 'fas fa-times-circle';  // Icon for removal
                                    } elseif (stripos($activityText, 'logged in') !== false) {
                                        $badgeClass = 'login-badge';
                                        $iconClass = 'fas fa-sign-in-alt';  // Icon for login
                                    } elseif (stripos($activityText, 'logged out') !== false) {
                                        $badgeClass = 'logout-badge';
                                        $iconClass = 'fas fa-sign-out-alt';  // Icon for logout
                                    }
                                ?>
                                <div class="activity-badge <?= $badgeClass ?>">
                                    <i class="<?= $iconClass ?> mr-2"></i> <?= $activityText ?>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="error-message">No logs available.</div>
    <?php endif; ?>
</div>

<div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>">Previous</a>
        <?php else: ?>
            <span class="disabled">Previous</span>
        <?php endif; ?>

        <?php foreach ($paginationLinks as $link): ?>
            <?php if ($link === '...'): ?>
                <span class="dots">...</span>
            <?php elseif ($link == $page): ?>
                <a href="?page=<?= $link ?>" class="active"><?= $link ?></a>
            <?php else: ?>
                <a href="?page=<?= $link ?>"><?= $link ?></a>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>">Next</a>
        <?php else: ?>
            <span class="disabled">Next</span>
        <?php endif; ?>
    </div>

</div>

</body>
</html>