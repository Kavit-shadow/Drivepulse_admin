<?php
class AdmissionLogger {
    private $timezone = 'Asia/Kolkata';
    
    public function __construct() {
        date_default_timezone_set($this->timezone);
    }
    
    public function formatDateTime($timeString) {
        $timestamp = strtotime($timeString);
        return $timestamp ? date('d M Y g:ia', $timestamp) : "Invalid Date";
    }
    
    public function formatDate($dateString) {
        $timestamp = strtotime($dateString);
        return $timestamp ? date('d M Y', $timestamp) : "Invalid Date";
    }
    
    public function calculateDaysRemaining($endDate) {
        $end = new DateTime($endDate);
        $now = new DateTime();
        $interval = $now->diff($end);
        return $interval->invert ? 0 : $interval->days;
    }
    
    public function calculateProgress($startDate, $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $now = new DateTime();
        
        $totalDays = $start->diff($end)->days;
        $daysElapsed = $start->diff($now)->days;
        
        if ($daysElapsed >= $totalDays) return 100;
        if ($daysElapsed <= 0) return 0;
        
        return round(($daysElapsed / $totalDays) * 100);
    }

    public function getPriorityBadgeClass($activity) {
        if (strpos($activity['What'], 'Priority: 1') !== false) {
            return 'priority-high';
        }
        return 'priority-normal';
    }
}

$logger = new AdmissionLogger();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Logs Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --success-color: #059669;
            --warning-color: #d97706;
            --background-color: #f8fafc;
            --card-background: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admission-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .priority-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .priority-high {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .priority-normal {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .card-content {
            padding: 1.5rem;
        }

        .student-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-group {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 6px;
        }

        .info-label {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .info-value {
            color: var(--text-primary);
            font-weight: 500;
        }

        .progress-section {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 6px;
            margin-top: 1rem;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background-color: #e2e8f0;
            border-radius: 4px;
            margin: 0.5rem 0;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background-color: var(--primary-color);
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .progress-stats {
            display: flex;
            justify-content: space-between;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        @media (max-width: 768px) {
            .student-info {
                grid-template-columns: 1fr;
            }
            
            .card-header {
                flex-direction: column;
                gap: 0.5rem;
                align-items: flex-start;
            }
            
            .meta-info {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <h1>
                <i class="fas fa-clipboard-list"></i>
                Admission Logs
            </h1>
            <div class="timestamp">
                Last updated: <?= $logger->formatDateTime(date('Y-m-d H:i:s')) ?>
            </div>
        </div>

        <?php
        $jsonFilePath = '../../../../logs/admin_logs/admission_logs.json';
        
        if (file_exists($jsonFilePath)) {
            $logEntries = json_decode(file_get_contents($jsonFilePath), true);
            
            if ($logEntries) {
                foreach ($logEntries as $entry) {
                    if (is_array($entry['activity'])) {
                        $customerDetails = $entry['activity']['0']['customer_details'];
                        $progress = $logger->calculateProgress($customerDetails['started_at'], $customerDetails['ended_at']);
                        $daysRemaining = $logger->calculateDaysRemaining($customerDetails['ended_at']);
                        ?>
                        <div class="admission-card">
                            <div class="card-header">
                                <div class="header-title">
                                    <h2><?= htmlspecialchars($entry['activity']['What']) ?></h2>
                                </div>
                                <div class="<?= $logger->getPriorityBadgeClass($entry['activity']) ?> priority-badge">
                                    <?= strpos($entry['activity']['What'], 'Priority: 1') !== false ? 'High Priority' : 'Standard' ?>
                                </div>
                            </div>
                            
                            <div class="card-content">
                                <div class="student-info">
                                    <div class="info-group">
                                        <div class="info-label">Student Details</div>
                                        <div class="info-value">
                                            <?= htmlspecialchars($customerDetails['name']) ?>
                                            <div class="info-secondary">
                                                <i class="fas fa-phone"></i> <?= htmlspecialchars($customerDetails['phone']) ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="info-group">
                                        <div class="info-label">Vehicle & Time Slot</div>
                                        <div class="info-value">
                                            <?= htmlspecialchars($customerDetails['vehicle']) ?>
                                            <div class="info-secondary">
                                                <i class="fas fa-clock"></i> <?= htmlspecialchars($customerDetails['timeSlot']) ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="info-group">
                                        <div class="info-label">Training Period</div>
                                        <div class="info-value">
                                            <?= $customerDetails['days'] ?> Days
                                            <div class="info-secondary">
                                                <?= $logger->formatDate($customerDetails['started_at']) ?> - 
                                                <?= $logger->formatDate($customerDetails['ended_at']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- <div class="progress-section">
                                    <div class="info-label">Training Progress</div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?= $progress ?>%"></div>
                                    </div>
                                    <div class="progress-stats">
                                        <span><?= $progress ?>% Complete</span>
                                        <span><?= $daysRemaining ?> Days Remaining</span>
                                    </div>
                                </div> -->
                                
                                <div class="meta-info">
                                    <div>
                                        <i class="fas fa-user"></i> Form filled by: <?= htmlspecialchars($customerDetails['formfiller']) ?>
                                    </div>
                                    <div>
                                        <i class="fas fa-calendar"></i> Admission Date: <?= $logger->formatDate($customerDetails['addmission_date']) ?>
                                    </div>
                                    <div>
                                        <i class="fas fa-clock"></i> Logged: <?= $logger->formatDateTime($entry['timestamp']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
            } else {
                echo '<div class="error-message">No admission records found.</div>';
            }
        } else {
            echo '<div class="error-message">Admission logs file not found.</div>';
        }
        ?>
    </div>
</body>
</html>