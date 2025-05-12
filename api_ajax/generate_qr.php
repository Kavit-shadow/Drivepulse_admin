<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include Composer autoloader
require_once '../vendor/autoload.php';

// Import required classes
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Color\Color;

// Set content type to PNG image
header('Content-Type: image/png');

try {
    // Get parameters from URL with validation
    $data = isset($_GET['data']) ? $_GET['data'] : '';
    $size = isset($_GET['size']) ? intval($_GET['size']) : 300;
    $margin = isset($_GET['margin']) ? intval($_GET['margin']) : 10;

    // Validate size (100-1000)
    if ($size < 100 || $size > 1000) {
        $size = 300;
    }

    // Validate margin (0-100)
    if ($margin < 0 || $margin > 100) {
        $margin = 10;
    }

    if (!empty($data)) {
        // Create QR code
        $qrCode = QrCode::create($data)
            ->setSize($size)
            ->setMargin($margin)
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        // Create writer
        $writer = new PngWriter();
        
        // Write QR code
        $result = $writer->write($qrCode);
        
        // Output the image directly
        echo $result->getString();
    } else {
        // If no data provided, generate an error image
        $img = imagecreate(300, 100);
        $bgColor = imagecolorallocate($img, 255, 255, 255);
        $textColor = imagecolorallocate($img, 255, 0, 0);
        imagestring($img, 3, 10, 40, "Error: No data provided for QR code", $textColor);
        imagepng($img);
        imagedestroy($img);
    }
} catch (Exception $e) {
    // Handle any exceptions that occur during QR code generation
    $img = imagecreate(300, 100);
    $bgColor = imagecolorallocate($img, 255, 255, 255);
    $textColor = imagecolorallocate($img, 255, 0, 0);
    imagestring($img, 3, 10, 40, "Error generating QR code: " . $e->getMessage(), $textColor);
    imagepng($img);
    imagedestroy($img);
} 