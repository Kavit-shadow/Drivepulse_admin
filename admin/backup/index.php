<?php
include('../../includes/authentication.php');
authenticationAdmin('../../');

date_default_timezone_set('Asia/Kolkata');
include('../../config.php');

// Create backup directory if it doesn't exist
$backupDir = '../../backups/';
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// Get current timestamp for filename
$timestamp = date('Y-m-d_H-i-s');

// Backup MySQL Database to SQL
function backupDatabase($conn, $backupDir, $timestamp) {
    $tables = array();
    $result = mysqli_query($conn, "SHOW TABLES");
    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }

    $sqlFile = $backupDir . 'db_backup_' . $timestamp . '.sql';
    $handle = fopen($sqlFile, 'w');

    // Get create table statements and data for each table
    foreach ($tables as $table) {
        $result = mysqli_query($conn, "SHOW CREATE TABLE $table");
        $row = mysqli_fetch_row($result);
        fwrite($handle, "\n\n" . $row[1] . ";\n\n");

        $result = mysqli_query($conn, "SELECT * FROM $table");
        while ($row = mysqli_fetch_row($result)) {
            $values = array_map(function($value) use ($conn) {
                if ($value === null) return 'NULL';
                return "'" . mysqli_real_escape_string($conn, $value) . "'";
            }, $row);
            fwrite($handle, "INSERT INTO $table VALUES(" . implode(',', $values) . ");\n");
        }
    }
    fclose($handle);
}

// Backup MySQL Database to Excel
function backupToExcel($conn, $backupDir, $timestamp) {
    require '../../vendor/autoload.php'; // Make sure PHPSpreadsheet is installed
    
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $tables = array();
    $result = mysqli_query($conn, "SHOW TABLES");
    
    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }
    
    foreach($tables as $index => $table) {
        if($index > 0) {
            $spreadsheet->createSheet();
        }
        $spreadsheet->setActiveSheetIndex($index);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($table, 0, 31)); // Excel sheet title length limit
        
        // Get headers
        $result = mysqli_query($conn, "SHOW COLUMNS FROM $table");
        $headers = array();
        $col = 'A';
        while($row = mysqli_fetch_assoc($result)) {
            $sheet->setCellValue($col.'1', $row['Field']);
            $headers[] = $row['Field'];
            $col++;
        }
        
        // Get data
        $result = mysqli_query($conn, "SELECT * FROM $table");
        $row_count = 2;
        while($row = mysqli_fetch_assoc($result)) {
            $col = 'A';
            foreach($headers as $header) {
                $sheet->setCellValue($col.$row_count, $row[$header]);
                $col++;
            }
            $row_count++;
        }
    }
    
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save($backupDir . 'db_backup_' . $timestamp . '.xlsx');
}

// Backup JSON logs
function backupLogs($backupDir, $timestamp) {
    $logTypes = ['admin_logs', 'staff_logs', 'system_logs'];
    $logsDir = '../../logs/';
    
    foreach ($logTypes as $type) {
        $sourceFile = $logsDir . $type . '/logs.json';
        if (file_exists($sourceFile)) {
            $destFile = $backupDir . $type . '_' . $timestamp . '.json';
            copy($sourceFile, $destFile);
        }
    }
}

// Create ZIP archive
function createZipBackup($backupDir, $timestamp) {
    $zip = new ZipArchive();
    $zipFile = $backupDir . 'full_backup_' . $timestamp . '.zip';
    
    if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
        $files = scandir($backupDir);
        foreach ($files as $file) {
            if (in_array($file, array('.', '..', basename($zipFile)))) continue;
            if (strpos($file, $timestamp) !== false) {
                $zip->addFile($backupDir . $file, $file);
            }
        }
        $zip->close();
        
        // Clean up individual backup files
        foreach ($files as $file) {
            if (strpos($file, $timestamp) !== false && $file !== basename($zipFile)) {
                unlink($backupDir . $file);
            }
        }
        
        return true;
    }
    return false;
}

// CSS Styles
echo '<style>
.backup-container {
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.backup-header {
    text-align: center;
    margin-bottom: 30px;
}

.backup-header h1 {
    color: #2c3e50;
    font-size: 2.5em;
    margin-bottom: 10px;
}

.backup-status {
    text-align: center;
    padding: 20px;
    margin: 20px 0;
    border-radius: 8px;
}

.success {
    background: #d4edda;
    color: #155724;
}

.error {
    background: #f8d7da;
    color: #721c24;
}

.btn-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
    margin-top: 20px;
}

.btn-download {
    transition: transform 0.2s;
}

.btn-download:hover {
    transform: translateY(-2px);
}

@media (max-width: 600px) {
    .backup-container {
        margin: 20px;
        padding: 15px;
    }
    
    .backup-header h1 {
        font-size: 2em;
    }
}
</style>';

// Perform backups
try {
    backupDatabase($conn, $backupDir, $timestamp);
    backupToExcel($conn, $backupDir, $timestamp);
    backupLogs($backupDir, $timestamp);
    if (createZipBackup($backupDir, $timestamp)) {
        echo "<div class='backup-container'>";
        echo "<div class='backup-header'>";
        echo "<h1>Backup System</h1>";
        echo "</div>";
        echo "<div class='backup-status success'>";
        echo "<h2><i class='fa-solid fa-check-circle'></i> Backup Created Successfully!</h2>";
        echo "<p>Backup file: full_backup_" . $timestamp . ".zip</p>";
        echo "</div>";
        echo "<div class='btn-container'>";
        echo "<a href='" . $backupDir . "full_backup_" . $timestamp . ".zip' class='btn-download' style='display: inline-block; padding: 12px 25px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>";
        echo "<i class='fa-solid fa-download'></i> Download Backup";
        echo "</a>";
        echo "<a href='../' class='btn-download' style='display: inline-block; padding: 12px 25px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;'>";
        echo "<i class='fa-solid fa-arrow-left'></i> Back to Dashboard";
        echo "</a>";
        echo "</div></div>";
    } else {
        throw new Exception("Failed to create ZIP archive");
    }
} catch (Exception $e) {
    echo "<div class='backup-container'>";
    echo "<div class='backup-header'>";
    echo "<h1>Backup System</h1>";
    echo "</div>";
    echo "<div class='backup-status error'>";
    echo "<h2><i class='fa-solid fa-exclamation-circle'></i> Backup Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "</div>";
    echo "<div class='btn-container'>";
    echo "<a href='../' class='btn-download' style='display: inline-block; padding: 12px 25px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;'>";
    echo "<i class='fa-solid fa-arrow-left'></i> Back to Dashboard";
    echo "</a>";
    echo "</div></div>";
}
?>
