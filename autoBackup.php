<?php
function autoBackup($path)
{

    date_default_timezone_set('Asia/Kolkata');
    include($path . 'config.php');


    // Create backup directory if it doesn't exist
    $backupDir = $path . 'backups/';
    if (!file_exists($backupDir)) {
        mkdir($backupDir, 0755, true);
    }

    // Get current timestamp for filename
    $timestamp = date('Y-m-d_H-i-s');

    // Backup MySQL Database to SQL
    function backupDatabase($conn, $backupDir, $timestamp, $path)
    {
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
                $values = array_map(function ($value) use ($conn) {
                    if ($value === null) return 'NULL';
                    return "'" . mysqli_real_escape_string($conn, $value) . "'";
                }, $row);
                fwrite($handle, "INSERT INTO $table VALUES(" . implode(',', $values) . ");\n");
            }
        }
        fclose($handle);
    }

    // Backup MySQL Database to Excel
    function backupToExcel($conn, $backupDir, $timestamp, $path)
    {
        require $path . 'vendor/autoload.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $tables = array();
        $result = mysqli_query($conn, "SHOW TABLES");

        while ($row = mysqli_fetch_row($result)) {
            $tables[] = $row[0];
        }

        foreach ($tables as $index => $table) {
            if ($index > 0) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndex($index);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle(substr($table, 0, 31));

            // Get headers
            $result = mysqli_query($conn, "SHOW COLUMNS FROM $table");
            $headers = array();
            $col = 'A';
            while ($row = mysqli_fetch_assoc($result)) {
                $sheet->setCellValue($col . '1', $row['Field']);
                $headers[] = $row['Field'];
                $col++;
            }

            // Get data
            $result = mysqli_query($conn, "SELECT * FROM $table");
            $row_count = 2;
            while ($row = mysqli_fetch_assoc($result)) {
                $col = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue($col . $row_count, $row[$header]);
                    $col++;
                }
                $row_count++;
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($backupDir . 'db_backup_' . $timestamp . '.xlsx');
    }

    // Backup JSON logs
    function backupLogs($backupDir, $timestamp, $path)
    {
        $logTypes = ['admin_logs', 'staff_logs', 'system_logs'];
        $logsDir = $path . 'logs/';

        foreach ($logTypes as $type) {
            $sourceFile = $logsDir . $type . '/logs.json';
            if (file_exists($sourceFile)) {
                $destFile = $backupDir . $type . '_' . $timestamp . '.json';
                copy($sourceFile, $destFile);
            }
        }
    }

    // Create ZIP archive
    function createZipBackup($backupDir, $timestamp)
    {
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

    // Check if backup is needed based on days elapsed
    $lastBackupFile = $backupDir . 'last_backup.txt';
    $backupInterval = 7; // Number of days between backups

    $shouldBackup = true;
    if (file_exists($lastBackupFile)) {
        $lastBackupTime = file_get_contents($lastBackupFile);
        $daysSinceLastBackup = (time() - strtotime($lastBackupTime)) / (60 * 60 * 24);
        if ($daysSinceLastBackup < $backupInterval) {
            $shouldBackup = false;
        }
    }

    // echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11' ></script>";
    // echo "<link href='https://cdn.jsdelivr.net/npm/@sweetalert2/theme-default/default.css' rel='stylesheet'>";
    // echo "<script>";

    if ($shouldBackup) {
        try {
            backupDatabase($conn, $backupDir, $timestamp, $path);
            backupToExcel($conn, $backupDir, $timestamp, $path);
            backupLogs($backupDir, $timestamp, $path);

            if (createZipBackup($backupDir, $timestamp)) {
                // Update last backup time
                file_put_contents($lastBackupFile, date('Y-m-d H:i:s'));
            //     echo "
            //     Swal.fire({
            //         icon: 'success',
            //         title: 'Backup Created Successfully',
            //         text: 'Backup has been saved to server at: " . $backupDir . "full_backup_" . $timestamp . ".zip',
            //         confirmButtonColor: '#28a745'
            //     });
            // ";
             echo "console.log('Backup Created Successfully')";
            } else {
                // throw new Exception("Failed to create ZIP archive");
                echo "console.log('Failed to create ZIP archive')";
            }
        } catch (Exception $e) {
        //     echo "
        //     Swal.fire({
        //         icon: 'error',
        //         title: 'Backup Failed',
        //         text: 'Error: " . $e->getMessage() . "',
        //         confirmButtonColor: '#dc3545'
        //     });
        // ";
        
          echo "console.log('Backup Failed')";
        }
    } else {
        $nextBackupDate = date('Y-m-d', strtotime($lastBackupTime . ' + ' . $backupInterval . ' days'));
//         echo "
//         Swal.fire({
//             icon: 'info',
//             title: 'Backup Not Required',
//             html: 'Last backup was performed on " . date('Y-m-d', strtotime($lastBackupTime)) . "<br>Next backup scheduled for " . $nextBackupDate . "',
//             confirmButtonColor: '#17a2b8'
//         });
//   ";

 echo "console.log('Backup Not Required')";
    }
    // echo "</script>";
}
